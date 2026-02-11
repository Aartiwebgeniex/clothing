<?php
/**
 * The template for displaying the footer.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

global $flatsome_opt;
?>

<?php if (is_product()) : ?>
	<div class="row container">
		<div class="klaviyo-form-XKY6iJ"></div>
		<div class="mcwidget-embed" style="margin-top: 20px;" data-widget-id="14744927"></div>
	</div>
<?php endif; ?>

</main>

<footer id="footer" class="footer-wrapper">
	<?php do_action('flatsome_footer'); ?>
</footer>

<?php
get_template_part('template-parts/footer/custom-footer', 'page');
?>

<?php wp_footer(); ?>

</body>

</html>
