<?php
/**
 * NRDS Business Theme functions.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NRDS_VERSION', '3.3.0' );
define( 'NRDS_DIR', get_template_directory() );
define( 'NRDS_URI', get_template_directory_uri() );

require NRDS_DIR . '/inc/icons.php';
require NRDS_DIR . '/inc/settings.php';
require NRDS_DIR . '/inc/layout.php';
require NRDS_DIR . '/inc/template-tags.php';
require NRDS_DIR . '/inc/navigation.php';

// WooCommerce store support; the file returns early when WooCommerce isn't active.
require NRDS_DIR . '/inc/woocommerce/woocommerce.php';

if ( is_admin() ) {
	require NRDS_DIR . '/inc/admin-settings.php';
}

/**
 * Theme setup.
 */
function nrds_theme_setup() {
	load_theme_textdomain( 'nrds-theme', NRDS_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-background' );
	add_theme_support( 'custom-header' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 100,
			'width'       => 400,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'site-title', 'site-description' ),
		)
	);

	add_image_size( 'nrds-card', 720, 450, true );

	register_nav_menus(
		array(
			'primary'         => __( 'Primary Menu', 'nrds-theme' ),
			'header'          => __( 'Header Menu', 'nrds-theme' ),
			'footer'          => __( 'Footer Bottom Bar Menu', 'nrds-theme' ),
			'footer-column-1' => __( 'Footer Column 1', 'nrds-theme' ),
			'footer-column-2' => __( 'Footer Column 2', 'nrds-theme' ),
			'footer-column-3' => __( 'Footer Column 3', 'nrds-theme' ),
			'footer-column-4' => __( 'Footer Column 4', 'nrds-theme' ),
		)
	);

	add_editor_style( array( nrds_fonts_url(), 'assets/css/tokens.css', 'assets/css/button.css' ) );
}
add_action( 'after_setup_theme', 'nrds_theme_setup' );

/**
 * Content width for embeds and images.
 */
function nrds_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'nrds_content_width', 840 );
}
add_action( 'after_setup_theme', 'nrds_content_width', 0 );

/**
 * Google Fonts URL (Oswald for headings and navigation, Roboto for text).
 *
 * @return string
 */
function nrds_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Roboto:ital,wght@0,400;0,500;0,700;1,400&display=swap';
}

/**
 * Widget areas.
 */
function nrds_theme_sidebars() {
	$shared = array(
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Left Sidebar', 'nrds-theme' ),
				'id'          => 'left-sidebar',
				'description' => __( 'Shown on the left where it is turned on in Appearance → Theme Settings.', 'nrds-theme' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Right Sidebar', 'nrds-theme' ),
				'id'          => 'right-sidebar',
				'description' => __( 'Shown on the right where it is turned on in Appearance → Theme Settings.', 'nrds-theme' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Header Widget Area', 'nrds-theme' ),
				'id'          => 'header-widget-area',
				'description' => __( 'Shown in the header next to the menu. Useful for a search form or social links.', 'nrds-theme' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Footer Newsletter Band', 'nrds-theme' ),
				'id'          => 'footer-top',
				'description' => __( 'A full-width band at the top of the footer, above the columns. Add a sign-up form or a call to action.', 'nrds-theme' ),
			)
		)
	);

	// The IDs stay footer-widget-1..3 from earlier versions so widgets already placed are kept.
	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar(
			array_merge(
				$shared,
				array(
					/* translators: %d: footer column number. */
					'name'        => sprintf( __( 'Footer Column %d', 'nrds-theme' ), $i ),
					'id'          => 'footer-widget-' . $i,
					/* translators: %1$d: footer column number. */
					'description' => sprintf( __( 'Widgets in footer column %1$d, below the Footer Column %1$d menu.', 'nrds-theme' ), $i ),
				)
			)
		);
	}

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Footer Bottom Bar', 'nrds-theme' ),
				'id'          => 'footer-secondary',
				'description' => __( 'Shown in the bar at the very bottom, next to the copyright line and the bottom bar menu.', 'nrds-theme' ),
			)
		)
	);
}
add_action( 'widgets_init', 'nrds_theme_sidebars' );

/**
 * Stylesheet parts in assets/css/, in load order. style.css loads after
 * "tokens" and screens.css loads last so its breakpoints win.
 *
 * @return string[]
 */
function nrds_style_parts() {
	return apply_filters(
		'nrds_style_parts',
		array( 'header', 'navigation', 'button', 'content', 'comments', 'social-icons', 'footer', 'screens' )
	);
}

/**
 * Scripts and styles.
 */
function nrds_theme_scripts() {
	wp_enqueue_style( 'nrds-fonts', nrds_fonts_url(), array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts.
	wp_enqueue_style( 'nrds-tokens', NRDS_URI . '/assets/css/tokens.css', array( 'nrds-fonts' ), NRDS_VERSION );
	wp_enqueue_style( 'nrds-theme-style', NRDS_URI . '/style.css', array( 'nrds-tokens' ), NRDS_VERSION );

	// Each part depends on the previous one so they print in order.
	$previous = 'nrds-theme-style';
	foreach ( nrds_style_parts() as $part ) {
		$handle = 'nrds-' . $part;
		wp_enqueue_style( $handle, NRDS_URI . '/assets/css/' . $part . '.css', array( $previous ), NRDS_VERSION );
		$previous = $handle;
	}

	// Colors and content width from Theme Settings override the token defaults.
	wp_add_inline_style( 'nrds-tokens', nrds_settings_css() );

	// A child theme's style.css loads after all of the parent's styles.
	if ( is_child_theme() ) {
		wp_enqueue_style( 'nrds-child', get_stylesheet_uri(), array( $previous ), wp_get_theme()->get( 'Version' ) );
	}

	wp_enqueue_script( 'nrds-theme-script', NRDS_URI . '/assets/js/script.js', array( 'jquery' ), NRDS_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'nrds_theme_scripts' );

/**
 * Apply the Theme Settings colors inside the block editor.
 */
function nrds_editor_colors() {
	wp_add_inline_style( 'wp-block-library', str_replace( ':root{', '.editor-styles-wrapper{', nrds_settings_css() ) );
}
add_action( 'enqueue_block_editor_assets', 'nrds_editor_colors' );

/**
 * Give the block editor's color palette the four Theme Settings colors, so
 * blocks colored with them follow later palette changes.
 *
 * @param WP_Theme_JSON_Data $theme_json Theme data from theme.json.
 * @return WP_Theme_JSON_Data
 */
function nrds_theme_json_palette( $theme_json ) {
	$palette = array();
	foreach ( nrds_color_fields() as $key => $field ) {
		$value     = sanitize_hex_color( nrds_setting( $key ) );
		$palette[] = array(
			'slug'  => substr( $key, strlen( 'color_' ) ),
			'name'  => $field[0],
			'color' => $value ? $value : $field[1],
		);
	}
	return $theme_json->update_with(
		array(
			'version'  => 2,
			'settings' => array( 'color' => array( 'palette' => $palette ) ),
		)
	);
}
add_filter( 'wp_theme_json_data_theme', 'nrds_theme_json_palette' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function nrds_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && wp_style_is( 'nrds-fonts', 'queue' ) ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'nrds_resource_hints', 10, 2 );

// Shortcodes in text widgets and excerpts.
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_excerpt', 'do_shortcode' );
