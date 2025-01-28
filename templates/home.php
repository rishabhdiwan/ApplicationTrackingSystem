<?php
/*
Template Name: home
*/
get_header();
?>
<form action="<?php echo admin_url('admin-post.php'); ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="submit_application">
    <input type="text" name="fullname" placeholder="Enter your Full Name" required>
    <input type="email" name="email" placeholder="Enter your Email" required>
    <span>Image</span>
    <input type="file" name="applicant_image" id="applicant_image" required>
    <span>Application</span>
    <input type="file" name="application_document" id="application_document" required>
    <input type="submit" value="Submit">
</form>

<?php get_footer(); ?>