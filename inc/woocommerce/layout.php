<?php
/**
 * WooCommerce layout: content wrappers, sidebars, breadcrumbs and grid sizes.
 *
 * Only WooCommerce's public actions and filters are used here; nothing
 * replaces a WooCommerce template file.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * The theme's header.php and footer.php already place the sidebars around
 * <main>, so WooCommerce's own wrapper and sidebar are swapped for <main>.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Open the theme's main content area on shop, category and product pages.
 */
function nrds_wc_wrapper_start() {
	echo '<main id="main" class="site-main nrds-store-main">';
}
add_action( 'woocommerce_before_main_content', 'nrds_wc_wrapper_start', 10 );

/**
 * Close the main content area.
 */
function nrds_wc_wrapper_end() {
	echo '</main>';
}
add_action( 'woocommerce_after_main_content', 'nrds_wc_wrapper_end', 10 );

/**
 * Sidebar settings for the shop and for product pages.
 *
 * @param array $contexts context => label.
 * @return array
 */
function nrds_wc_sidebar_contexts( $contexts ) {
	$contexts['shop']    = __( 'Shop & product categories', 'nrds-theme' );
	$contexts['product'] = __( 'Products', 'nrds-theme' );
	return $contexts;
}
add_filter( 'nrds_sidebar_contexts', 'nrds_wc_sidebar_contexts' );

/**
 * The shop starts with the Shop Sidebar on the left; products start full width.
 *
 * @param array $defaults context => [left, right].
 * @return array
 */
function nrds_wc_sidebar_defaults( $defaults ) {
	$defaults['shop']    = array( 'show', 'hide' );
	$defaults['product'] = array( 'hide', 'hide' );
	return $defaults;
}
add_filter( 'nrds_sidebar_defaults', 'nrds_wc_sidebar_defaults' );

/**
 * Store views get their own sidebar context. Cart, checkout and account
 * pages never show sidebars, so nothing distracts from buying.
 *
 * @param string|null $context Sidebar context.
 * @return string|null
 */
function nrds_wc_sidebar_context( $context ) {
	if ( is_cart() || is_checkout() || is_account_page() ) {
		return null;
	}
	if ( is_product() ) {
		return 'product';
	}
	if ( is_shop() || is_product_taxonomy() ) {
		return 'shop';
	}
	return $context;
}
add_filter( 'nrds_sidebar_context', 'nrds_wc_sidebar_context' );

/**
 * On store views the left sidebar shows the Shop Sidebar widgets.
 *
 * @param array       $areas   side => sidebar ID.
 * @param string|null $context Sidebar context.
 * @return array
 */
function nrds_wc_sidebar_areas( $areas, $context ) {
	if ( in_array( $context, array( 'shop', 'product' ), true ) ) {
		$areas['left'] = 'shop-sidebar';
	}
	return $areas;
}
add_filter( 'nrds_sidebar_areas', 'nrds_wc_sidebar_areas', 10, 2 );

/**
 * Breadcrumbs with a plain slash separator.
 *
 * @param array $args Breadcrumb arguments.
 * @return array
 */
function nrds_wc_breadcrumb_args( $args ) {
	$args['delimiter'] = '<span class="nrds-crumb-sep" aria-hidden="true"> / </span>';
	return $args;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'nrds_wc_breadcrumb_args' );

/**
 * Three related products in one row.
 *
 * @param array $args Related products arguments.
 * @return array
 */
function nrds_wc_related_args( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'nrds_wc_related_args' );

/**
 * Upsells in one row of three.
 *
 * @return int
 */
function nrds_wc_upsell_columns() {
	return 3;
}
add_filter( 'woocommerce_upsells_columns', 'nrds_wc_upsell_columns' );
