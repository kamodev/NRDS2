<?php
/**
 * Primary menu dropdowns.
 *
 * Every primary-menu item with a submenu gets a toggle button after its link.
 * On wide screens the submenu opens as a dropdown on hover, keyboard focus or
 * a click on the toggle; on phones it opens as a collapsible section inside
 * the mobile menu. assets/js/script.js handles the toggles and
 * assets/css/navigation.css (with screens.css) the look.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a submenu toggle after primary-menu links that have a submenu.
 *
 * @param string   $item_output The menu item's HTML.
 * @param WP_Post  $item        Menu item.
 * @param int      $depth       Depth of the item.
 * @param stdClass $args        wp_nav_menu() arguments.
 * @return string
 */
function nrds_submenu_toggle( $item_output, $item, $depth, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $item_output;
	}
	if ( ! in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
		return $item_output;
	}
	// A submenu below the menu's depth limit isn't printed, so it gets no toggle.
	if ( ! empty( $args->depth ) && $depth + 1 >= $args->depth ) {
		return $item_output;
	}

	/* translators: %s: menu item title. */
	$label = sprintf( __( 'Show submenu for %s', 'nrds-theme' ), wp_strip_all_tags( $item->title ) );

	return $item_output . sprintf(
		'<button type="button" class="submenu-toggle" aria-expanded="false"><span class="screen-reader-text">%1$s</span>%2$s</button>',
		esc_html( $label ),
		nrds_get_icon( 'chevron' )
	);
}
add_filter( 'walker_nav_menu_start_el', 'nrds_submenu_toggle', 10, 4 );
