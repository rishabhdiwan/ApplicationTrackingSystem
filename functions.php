<?php
// Start the session if not already started
add_action('init', function() {
    if (!session_id()) {
        session_start();
    }
});
// Theme Enqueue
function ats_enqueue() {
    wp_enqueue_script('custom-scipt', get_template_directory_uri() . 'assets/build/js/index.min.js', array('jquery'), 1.0, true);
    wp_enqueue_style( 'custom-style', get_template_directory_uri() . '/assets/build/css/style.min.css');
} 
add_action('wp_enqueue_scripts', 'ats_enqueue');
// Thumbnail Support //
add_theme_support("post-thumbnails");
// Menu Activation //
function theme_register_menus() {
    register_nav_menus(array(
        'primary_menu' => __('Primary Menu'),
    ));
}
add_action('init', 'theme_register_menus');

// Form Submission //
function handle_application_submission() {
    // Check if form data is valid
    if (isset($_POST['fullname']) && isset($_POST['email']) && isset($_FILES['application_document'])) {

        // Sanitize user input
        $fullname = sanitize_text_field($_POST['fullname']);
        $email = sanitize_email($_POST['email']);
        // Handle the applicant image upload
        $featured_image_id = null; // Default to null
        if (!empty($_FILES['applicant_image']['name'])) {
            $image_file = $_FILES['applicant_image'];
            $image_upload = wp_handle_upload($image_file, ['test_form' => false]);

            if ($image_upload && !isset($image_upload['error'])) {
                $image_path = $image_upload['file'];
                $image_url = $image_upload['url'];

                // Create an attachment for the image
                $image_attachment = [
                    'post_mime_type' => $image_file['type'],
                    'post_title'     => sanitize_file_name($image_file['name']),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ];

                $featured_image_id = wp_insert_attachment($image_attachment, $image_path);
                require_once ABSPATH . 'wp-admin/includes/image.php';
                wp_update_attachment_metadata($featured_image_id, wp_generate_attachment_metadata($featured_image_id, $image_path));
            } else {
                wp_die('Image upload failed: ' . $image_upload['error']);
            }
        }
        // Handle the file upload
        if (!empty($_FILES['application_document']['name'])) {
            // Use WordPress's file upload handler
            $uploaded_file = $_FILES['application_document'];
            $upload = wp_handle_upload($uploaded_file, ['test_form' => false]);

            if ($upload && !isset($upload['error'])) {
                // File uploaded successfully
                $file_url = $upload['url']; // URL of the uploaded file
                $file_path = $upload['file']; // Path of the uploaded file

                // Create an attachment post for the file
                $attachment = [
                    'post_mime_type' => $uploaded_file['type'],
                    'post_title'     => sanitize_file_name($uploaded_file['name']),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ];

                $file_id = wp_insert_attachment($attachment, $file_path);
                require_once ABSPATH . 'wp-admin/includes/image.php';
                wp_update_attachment_metadata($file_id, wp_generate_attachment_metadata($file_id, $file_path));

                // Insert new post into the custom post type 'application'
                $post_id = wp_insert_post([
                    'post_title' => $fullname,
                    'post_type' => 'application', // Custom post type
                    'post_status' => 'publish', // Set the post status as 'publish'
                ]);

                if ($post_id) {
                    // Set the featured image for the post
                    if ($featured_image_id) {
                        set_post_thumbnail($post_id, $featured_image_id);
                    }
                    // Update the ACF email field and attach the document
                    update_field('email', $email, $post_id);
                    update_field('application_document', $file_id, $post_id);

                    // Assign default taxonomy term ('Submitted') to the post
                    wp_set_object_terms($post_id, 'Submitted', 'application_status');

                    // **Send Email on Submission**
                    $subject = 'Application Received';
                    $message = "Dear $fullname,\n\nYour application has been successfully submitted.\n\nStatus: Submitted\n\nThank you for your application.\n\nRegards,\nThe Hiring Team";
                    $headers = ['Content-Type: text/plain; charset=UTF-8'];

                    wp_mail($email, $subject, $message, $headers);
                } else {
                    echo 'Not True';
                }
                // Store message in session
                $_SESSION['submission_status'] = 'success';
                // Redirect back to the same page
                $redirect_url = !empty($_POST['redirect_to']) ? esc_url_raw($_POST['redirect_to']) : home_url();
                wp_safe_redirect($redirect_url);
                exit;
            } else {
                // Handle file upload error
                wp_die('File upload failed: ' . $upload['error']);
            }
        } else {
            wp_die('No file was uploaded.');
        }
    } else {
        wp_die('Please fill in all required fields.');
    }
}
// Hook into the form submission and process the data.
add_action('admin_post_nopriv_submit_application', 'handle_application_submission');
add_action('admin_post_submit_application', 'handle_application_submission');

// Send Emails on Status Updation //
function send_email_on_status_change($post_id, $post, $update) {
    // Check if this is the correct post type
    if ($post->post_type !== 'application') {
        return;
    }
    // Avoid infinite loop
    remove_action('save_post', 'send_email_on_status_change', 10);
    // Get the current taxonomy term
    $new_terms = wp_get_object_terms($post_id, 'application_status', ['fields' => 'names']);
    $new_status = !empty($new_terms) ? $new_terms[0] : '';

    // Get the previous taxonomy term from the database
    $old_status = get_post_meta($post_id, '_previous_status', true);
    // If this is a new post, set the old status to an empty string
    if (!$update && empty($new_status)) {
        $new_status = 'Submitted';
        wp_set_object_terms($post_id, 'Submitted', 'application_status'); // Set taxonomy
    }
    // Update the stored status for future comparisons
    update_post_meta($post_id, '_previous_status', $new_status);
    // Get the applicant's email from ACF
    $applicant_email = get_field('email', $post_id);
    $applicant_name = get_the_title($post_id);
    // If no email, do nothing
    if (empty($applicant_email) || !is_email($applicant_email)) {
        error_log("send_email_on_status_change: Invalid email for post ID $post_id"); // Log error for debugging
        return; // Stop execution if the email is invalid
    }
    // Check if the status has actually changed
    if ($old_status !== $new_status || !$update) {
        // Prepare the email
        $subject = 'Application Status Update';
        $message = "Dear $applicant_name,\n\nThe status of your application has been updated by the hiring team.\n\nNew Status: $new_status\n\nRegards,\nThe Hiring Team";
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        // Send the email using wp_mail() (SMTP will be used automatically)
        wp_mail($applicant_email, $subject, $message, $headers);
    }
    // Re-add the action to avoid breaking other save_post processes
    add_action('save_post', 'send_email_on_status_change', 10, 3);
}
add_action('save_post', 'send_email_on_status_change', 10, 3);
