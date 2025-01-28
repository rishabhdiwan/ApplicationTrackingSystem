<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
    <title><?php echo str_replace('>>', '', wp_title('rd', false)); ?></title>
    <?php wp_head(); ?>
</head>
<body>
    <header>
    </header>