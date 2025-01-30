<?php
/*
Template Name: home
*/
get_header();
?>
<div class="home">
    <div class="container">
        <div class="form-application">
            <h2>Application Form</h2>
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST" enctype="multipart/form-data">
                <div><input type="hidden" name="action" value="submit_application"></div>
                <div><input type="hidden" name="redirect_to" value="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>"></div>
                <div><input type="text" name="fullname" placeholder="Enter your Full Name" required></div>
                <div><input type="email" name="email" placeholder="Enter your Email" required></div>
                <div><input type="tel" name="applicant_phone" placeholder="Enter your Phone Number" id="applicant_phone" required></div>
                <div class = "applicant-img-or-doc">
                    <i class="fa-solid fa-image" style="color: #d558bc;"></i>
                    <label for="applicant_image" class="custom-file-upload" required>Add your image here</label>
                    <input type="file" id="applicant_image" name="applicant_image" style="display: none;" required>
                    <p id="image-upload-status" class="upload-status"></p>
                </div>
                <div class = "applicant-img-or-doc">
                    <i class="fa-solid fa-file" style="color: #d558bc;"></i>
                    <label for="application_document" class="custom-file-upload" required>Add your application below</label>
                    <input type="file" id="application_document" name="application_document" style="display: none;" required>
                    <p id="document-upload-status" class="upload-status"></p>
                </div>
                <input type="submit" value="Submit">
            </form>
            <?php if (!empty($_SESSION['submission_status']) && $_SESSION['submission_status'] === 'success') : ?>
            <p class="success-message">Your application has been submitted.</p>
            <?php unset($_SESSION['submission_status']); // Clear message after displaying ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>