<?php
/**
 * WooCommerce integration loader. Loads only when WooCommerce is active.
 *
 * Update-safe by design: the theme ships no WooCommerce template overrides
 * (there is no /woocommerce/ folder). Everything is done with WooCommerce's
 * hooks, filters and CSS, so WooCommerce updates never leave the theme with
 * outdated templates.
 *
 * Files:
 *   setup.php   Theme support, shop sidebar, stylesheet, body class.
 *   layout.php  Content wrappers, sidebar contexts, breadcrumbs, grid sizes.
 *   header.php  Header account and cart icons, cart count fragment.
 *   pages.php   The pages a store needs, their status and the "create missing pages" action.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

require __DIR__ . '/setup.php';
require __DIR__ . '/layout.php';
require __DIR__ . '/header.php';
require __DIR__ . '/pages.php';
