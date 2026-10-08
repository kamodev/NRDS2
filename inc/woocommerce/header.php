<?php
/**
 * Header account and cart icons, with a cart count that updates after
 * add-to-cart.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart count badge.
 */
function nrds_wc_cart_count() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	printf( '<span class="nrds-cart-count"%s>%d</span>', $count ? '' : ' hidden', (int) $count );
}

/**
 * Account and cart icons in the header.
 */
function nrds_wc_header_icons() {
	?>
	<a class="nrds-icon-link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
		<span class="screen-reader-text"><?php is_user_logged_in() ? esc_html_e( 'My account', 'nrds-theme' ) : esc_html_e( 'Log in', 'nrds-theme' ); ?></span>
		<?php nrds_icon( 'user' ); ?>
	</a>
	<a class="nrds-icon-link nrds-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'nrds-theme' ); ?></span>
		<?php nrds_icon( 'cart' ); ?>
		<?php nrds_wc_cart_count(); ?>
	</a>
	<?php
}
add_action( 'nrds_header_actions', 'nrds_wc_header_icons' );

/**
 * Keep the header cart count fresh after AJAX add-to-cart.
 *
 * @param array $fragments Cart fragments.
 * @return array
 */
function nrds_wc_cart_fragment( $fragments ) {
	ob_start();
	nrds_wc_cart_count();
	$fragments['.nrds-cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'nrds_wc_cart_fragment' );
