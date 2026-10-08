<?php
/**
 * WooCommerce theme support, shop sidebar, stylesheet and body class.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Declare WooCommerce support.
 *
 * Image widths and grid defaults are declared here instead of hard-coded in
 * templates, so store owners can still change them under Customize →
 * WooCommerce → Product Images / Product Catalog.
 */
function nrds_wc_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_rows'    => 4,
				'min_rows'        => 1,
				'default_columns' => 3,
				'min_columns'     => 1,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'nrds_wc_setup' );

/**
 * Shop sidebar widget area (product filters, categories, cart widget).
 */
function nrds_wc_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Shop Sidebar', 'nrds-theme' ),
			'id'            => 'shop-sidebar',
			'description'   => __( 'Shown on the left of the shop, product categories and products where turned on in Appearance → Theme Settings. Good for product filters and categories.', 'nrds-theme' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'nrds_wc_widgets_init' );

/**
 * Load woocommerce.css after the theme's other parts, before the breakpoints.
 *
 * @param string[] $parts Stylesheet parts.
 * @return string[]
 */
function nrds_wc_style_part( $parts ) {
	$at = array_search( 'screens', $parts, true );
	array_splice( $parts, false === $at ? count( $parts ) : $at, 0, array( 'woocommerce' ) );
	return $parts;
}
add_filter( 'nrds_style_parts', 'nrds_wc_style_part' );

/**
 * Body class for store pages.
 *
 * @param array $classes Body classes.
 * @return array
 */
function nrds_wc_body_class( $classes ) {
	if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		$classes[] = 'nrds-store';
	}
	return $classes;
}
add_filter( 'body_class', 'nrds_wc_body_class' );
