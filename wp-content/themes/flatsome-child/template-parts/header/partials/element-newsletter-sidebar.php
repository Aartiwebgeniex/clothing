<?php
/**
 * Newsletter sidebar element.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

$label = get_theme_mod('header_newsletter_label', 'Newsletter');
$title = get_theme_mod('header_newsletter_title', 'Sign up for Newsletter');
?>
<li class="header-newsletter-item has-icon" style="display:none;">
  <a href="#ninja-popup-98848" class="tooltip" title="<?php echo $title; ?>">

    <i class="icon-envelop"></i>
    <span class="header-newsletter-title">
      <?php echo $label; ?>
    </span>
  </a>

</li>
<li class="menu-item menu-item-type-post_type menu-item-object-page">
  <a href="<?php echo home_url(); ?>/contact/" class="tooltip" title="<?php echo "Contact Us"; ?>">Contact Us</a>
</li>