<?php
/**
 * Header template.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php flatsome_html_classes(); ?>">
<head>

    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php
$domain = function_exists('p2c_get_domain_name') ? p2c_get_domain_name() : '';

if ($domain === 'plus2clothing.com') {
    get_template_part('template-parts/header/analytics-module', 'page');
}
get_template_part('template-parts/header/custom-header', 'page');
?>

    <?php wp_head(); ?>


</head>

<body <?php body_class(); ?>>
<?php
if ($domain === 'plus2clothing.com') {
    get_template_part('template-parts/header/analytics-module-2', 'page');
}
?>

    <?php do_action('flatsome_after_body_open'); ?>
    <?php wp_body_open(); ?>

    <a class="skip-link screen-reader-text" href="#main">
        <?php esc_html_e('Skip to content', 'flatsome'); ?>
    </a>

    <div id="wrapper">

        <?php do_action('flatsome_before_header'); ?>

        <header id="header" class="header <?php flatsome_header_classes(); ?>">
            <div class="header-wrapper">
                <?php get_template_part('template-parts/header/header', 'wrapper'); ?>
            </div>
        </header>

        <?php do_action('flatsome_after_header'); ?>


        <?php
        get_template_part('template-parts/header/shipping-notice-module', 'page');
        ?>

        <main id="main" class="<?php flatsome_main_classes(); ?>">
