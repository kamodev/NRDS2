<?php
/**
 * Site header. Opens the content area and the left sidebar; footer.php
 * closes them.
 *
 * @package NRDS
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php if ( get_bloginfo( 'description' ) ) : ?>
		<meta name="description" content="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'nrds-theme' ); ?></a>

<div class="site-container">
	<?php get_template_part( 'template-parts/header/site-header' ); ?>

	<div id="site-content" class="site-content">
		<?php get_sidebar( 'left' ); ?>
