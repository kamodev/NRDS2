<?php
/**
 * Theme settings: colors, layout (site width and sidebars) and footer content.
 *
 * Everything is stored in one option, nrds_theme_settings_options, which is
 * edited under Appearance → Theme Settings. A setting that is missing or left
 * blank uses the theme default.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Editable palette colors: key => label, default, CSS custom property, help text.
 *
 * The defaults come from the NRDS logo: orange, black and white, plus a
 * charcoal for secondary elements.
 *
 * @return array
 */
function nrds_color_fields() {
	return array(
		'color_primary'   => array( __( 'Primary', 'nrds-theme' ), '#ff4c00', '--nrd-primary', __( 'Buttons, links, the top bar, active menu items and highlights.', 'nrds-theme' ) ),
		'color_dark'      => array( __( 'Dark', 'nrds-theme' ), '#000000', '--nrd-dark', __( 'Text, headings and the footer background.', 'nrds-theme' ) ),
		'color_secondary' => array( __( 'Secondary', 'nrds-theme' ), '#333333', '--nrd-secondary', __( 'Secondary buttons and the footer bottom bar.', 'nrds-theme' ) ),
		'color_light'     => array( __( 'Light', 'nrds-theme' ), '#ffffff', '--nrd-light', __( 'Page background, and text on dark and primary areas.', 'nrds-theme' ) ),
	);
}

/**
 * Color themes offered on the Colors tab. Each one sets all four palette
 * colors and keeps white as the light color.
 *
 * @return array slug => [label, colors]
 */
function nrds_color_presets() {
	$presets = array(
		'nrds'     => array( __( 'NRDS Blaze (logo colors)', 'nrds-theme' ), array() ),
		'navy'     => array(
			__( 'Navy & Orange', 'nrds-theme' ),
			array(
				'color_primary'   => '#e8641c',
				'color_dark'      => '#1d3547',
				'color_secondary' => '#5b6b78',
				'color_light'     => '#ffffff',
			),
		),
		'signal'   => array(
			__( 'Signal Red', 'nrds-theme' ),
			array(
				'color_primary'   => '#d62828',
				'color_dark'      => '#111111',
				'color_secondary' => '#4a4a4a',
				'color_light'     => '#ffffff',
			),
		),
		'olive'    => array(
			__( 'Field Olive', 'nrds-theme' ),
			array(
				'color_primary'   => '#e0621b',
				'color_dark'      => '#16181a',
				'color_secondary' => '#4b5320',
				'color_light'     => '#ffffff',
			),
		),
		'woodland' => array(
			__( 'Woodland', 'nrds-theme' ),
			array(
				'color_primary'   => '#a85a14',
				'color_dark'      => '#141a13',
				'color_secondary' => '#3b5a2e',
				'color_light'     => '#ffffff',
			),
		),
		'desert'   => array(
			__( 'Desert Tan', 'nrds-theme' ),
			array(
				'color_primary'   => '#b5541c',
				'color_dark'      => '#2b2219',
				'color_secondary' => '#6f6034',
				'color_light'     => '#ffffff',
			),
		),
	);

	// The first preset is the theme defaults.
	foreach ( nrds_color_fields() as $key => $field ) {
		$presets['nrds'][1][ $key ] = $field[1];
	}

	return apply_filters( 'nrds_color_presets', $presets );
}

/**
 * Where sidebars can be switched on or off: context => label.
 *
 * @return array
 */
function nrds_sidebar_contexts() {
	return apply_filters(
		'nrds_sidebar_contexts',
		array(
			'front'   => __( 'Front page', 'nrds-theme' ),
			'post'    => __( 'Posts', 'nrds-theme' ),
			'page'    => __( 'Pages', 'nrds-theme' ),
			'archive' => __( 'Blog, archives & search', 'nrds-theme' ),
		)
	);
}

/**
 * Default sidebar switches for each context: context => [left, right].
 * Contexts not listed show both sidebars.
 *
 * @return array
 */
function nrds_sidebar_defaults() {
	return apply_filters(
		'nrds_sidebar_defaults',
		array(
			// The front page starts without sidebars so full-width layouts aren't squeezed.
			'front' => array( 'hide', 'hide' ),
		)
	);
}

/**
 * Select-style settings: key => choices, default.
 *
 * @return array
 */
function nrds_choice_fields() {
	$show_hide = array(
		'show' => __( 'Show', 'nrds-theme' ),
		'hide' => __( 'Hide', 'nrds-theme' ),
	);

	$fields = array(
		'site_width'        => array(
			array(
				'sized' => __( 'Sized (centered, with a maximum width)', 'nrds-theme' ),
				'full'  => __( 'Full screen (edge to edge)', 'nrds-theme' ),
			),
			'sized',
		),
		'post_header_image' => array( $show_hide, 'show' ),
		'footer_brand'      => array( $show_hide, 'show' ),
		'footer_logo'       => array(
			array(
				'auto'  => __( 'Automatic (the version that suits the footer color)', 'nrds-theme' ),
				'light' => __( 'Light version', 'nrds-theme' ),
				'dark'  => __( 'Dark version', 'nrds-theme' ),
				'site'  => __( 'Site logo (same as the header)', 'nrds-theme' ),
				'title' => __( 'Site name as text', 'nrds-theme' ),
				'none'  => __( 'Off (no logo)', 'nrds-theme' ),
			),
			'auto',
		),
	);

	$defaults = nrds_sidebar_defaults();
	foreach ( nrds_sidebar_contexts() as $context => $label ) {
		$default                               = isset( $defaults[ $context ] ) ? $defaults[ $context ] : array( 'show', 'show' );
		$fields[ $context . '_left_sidebar' ]  = array( $show_hide, $default[0] );
		$fields[ $context . '_right_sidebar' ] = array( $show_hide, $default[1] );
	}

	return $fields;
}

/**
 * Free-text settings: key => label, default, type (text, textarea, url or email).
 *
 * @return array
 */
function nrds_text_fields() {
	return array(
		'footer_about'      => array( __( 'About text', 'nrds-theme' ), '', 'textarea' ),
		'social_facebook'   => array( 'Facebook', '', 'url' ),
		'social_instagram'  => array( 'Instagram', '', 'url' ),
		'social_x'          => array( 'X (Twitter)', '', 'url' ),
		'social_youtube'    => array( 'YouTube', '', 'url' ),
		'social_linkedin'   => array( 'LinkedIn', '', 'url' ),
		'social_email'      => array( __( 'Email address', 'nrds-theme' ), '', 'email' ),
		'footer_disclaimer' => array( __( 'Disclaimer', 'nrds-theme' ), '', 'textarea' ),
		'footer_copyright'  => array( __( 'Copyright line', 'nrds-theme' ), '', 'text' ),
	);
}

/**
 * Image settings, stored as Media Library attachment IDs: key => label, help text.
 *
 * @return array
 */
function nrds_image_fields() {
	return array(
		'footer_logo_light' => array( __( 'Light version', 'nrds-theme' ), __( 'For dark backgrounds, such as the default black footer: a white or light-colored logo.', 'nrds-theme' ) ),
		'footer_logo_dark'  => array( __( 'Dark version', 'nrds-theme' ), __( 'For light backgrounds, if the footer color is changed to a light one: a black or dark-colored logo.', 'nrds-theme' ) ),
	);
}

/**
 * Content width limits for the sized layout, in pixels.
 *
 * @return array min, max, default
 */
function nrds_content_width_range() {
	return array( 800, 1920, 1440 );
}

/**
 * Theme defaults for every setting.
 *
 * @return array
 */
function nrds_setting_defaults() {
	$defaults = array();
	foreach ( nrds_color_fields() as $key => $field ) {
		$defaults[ $key ] = $field[1];
	}
	foreach ( nrds_choice_fields() as $key => $field ) {
		$defaults[ $key ] = $field[1];
	}
	foreach ( nrds_text_fields() as $key => $field ) {
		$defaults[ $key ] = $field[1];
	}
	foreach ( nrds_image_fields() as $key => $field ) {
		$defaults[ $key ] = 0;
	}
	$range                     = nrds_content_width_range();
	$defaults['content_width'] = $range[2];

	return apply_filters( 'nrds_setting_defaults', $defaults );
}

/**
 * Map settings saved by theme version 2 to the current keys.
 *
 * Version 2 stored a "fullscreen" checkbox and a "disable post header images"
 * checkbox; they become the site width and post header image choices.
 *
 * @param array $values Saved values.
 * @return array
 */
function nrds_upgrade_settings( $values ) {
	$values = is_array( $values ) ? $values : array();

	if ( ! isset( $values['site_width'] ) && isset( $values['fullscreen'] ) ) {
		$values['site_width'] = $values['fullscreen'] ? 'full' : 'sized';
	}
	if ( ! isset( $values['post_header_image'] ) && isset( $values['disable_post_header_images'] ) ) {
		$values['post_header_image'] = $values['disable_post_header_images'] ? 'hide' : 'show';
	}
	unset( $values['fullscreen'], $values['disable_post_header_images'], $values['nrds_theme_setting_color'], $values['nrds_theme_setting_example'] );

	return $values;
}

/**
 * This site's saved settings.
 *
 * @return array
 */
function nrds_site_settings() {
	return nrds_upgrade_settings( get_option( 'nrds_theme_settings_options', array() ) );
}

/**
 * Resolved value of a setting: the saved value, or the theme default.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function nrds_setting( $key ) {
	$site = nrds_site_settings();
	if ( isset( $site[ $key ] ) && '' !== $site[ $key ] ) {
		return $site[ $key ];
	}
	$defaults = nrds_setting_defaults();
	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Sanitize settings, keeping existing values for keys not in the input.
 *
 * @param array $input    Raw input.
 * @param array $existing Current saved values.
 * @return array
 */
function nrds_sanitize_settings( $input, $existing ) {
	$input    = is_array( $input ) ? $input : array();
	$existing = nrds_upgrade_settings( $existing );
	$out      = array();
	$pick     = function ( $key ) use ( $input, $existing ) {
		if ( array_key_exists( $key, $input ) ) {
			return $input[ $key ];
		}
		return isset( $existing[ $key ] ) ? $existing[ $key ] : '';
	};

	foreach ( nrds_color_fields() as $key => $field ) {
		$value = sanitize_hex_color( trim( (string) $pick( $key ) ) );
		if ( $value ) {
			$out[ $key ] = strtolower( $value );
		}
	}

	foreach ( nrds_choice_fields() as $key => $field ) {
		$value = (string) $pick( $key );
		if ( array_key_exists( $value, $field[0] ) ) {
			$out[ $key ] = $value;
		}
	}

	foreach ( nrds_text_fields() as $key => $field ) {
		$value = (string) $pick( $key );
		switch ( $field[2] ) {
			case 'textarea':
				$value = sanitize_textarea_field( $value );
				break;
			case 'url':
				$value = esc_url_raw( trim( $value ) );
				break;
			case 'email':
				$value = sanitize_email( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		}
		if ( '' !== $value ) {
			$out[ $key ] = $value;
		}
	}

	foreach ( nrds_image_fields() as $key => $field ) {
		$value = absint( $pick( $key ) );
		if ( $value && wp_attachment_is_image( $value ) ) {
			$out[ $key ] = $value;
		}
	}

	$width = $pick( 'content_width' );
	if ( '' !== $width ) {
		list( $min, $max ) = nrds_content_width_range();
		$out['content_width'] = min( $max, max( $min, absint( $width ) ) );
	}

	return $out;
}

/**
 * Sanitize callback for the option.
 *
 * A Theme Settings tab posts only its own fields plus a "_tab" marker, so
 * that save is merged into the stored values. Any other write (WP-CLI, code)
 * is taken as the complete value.
 *
 * @param array $input Raw input.
 * @return array
 */
function nrds_sanitize_site_settings( $input ) {
	if ( is_array( $input ) && isset( $input['_tab'] ) ) {
		unset( $input['_tab'] );
		return nrds_sanitize_settings( $input, get_option( 'nrds_theme_settings_options', array() ) );
	}
	return nrds_sanitize_settings( $input, array() );
}

/**
 * Register the option.
 */
function nrds_register_settings() {
	register_setting(
		'nrds_theme_settings',
		'nrds_theme_settings_options',
		array(
			'type'              => 'object',
			'sanitize_callback' => 'nrds_sanitize_site_settings',
			'default'           => array(),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'nrds_register_settings' );

/**
 * Hex color to [r, g, b].
 *
 * @param string $hex Hex color.
 * @return int[]
 */
function nrds_hex_to_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$n = hexdec( $hex );
	return array( ( $n >> 16 ) & 255, ( $n >> 8 ) & 255, $n & 255 );
}

/**
 * Mix two hex colors.
 *
 * @param string $from   Hex color.
 * @param string $to     Hex color.
 * @param float  $amount How far to move from $from toward $to (0-1).
 * @return string Hex color.
 */
function nrds_mix_colors( $from, $to, $amount ) {
	$a   = nrds_hex_to_rgb( $from );
	$b   = nrds_hex_to_rgb( $to );
	$out = '#';
	for ( $i = 0; $i < 3; $i++ ) {
		$out .= sprintf( '%02x', (int) round( $a[ $i ] + ( $b[ $i ] - $a[ $i ] ) * $amount ) );
	}
	return $out;
}

/**
 * Whether a color is dark, meaning white text or a light logo reads better on
 * it than black (WCAG relative luminance below 0.179, where the contrast with
 * black and with white is equal).
 *
 * @param string $hex Hex color.
 * @return bool
 */
function nrds_is_dark_color( $hex ) {
	$channels = array_map(
		function ( $v ) {
			$v /= 255;
			return $v <= 0.03928 ? $v / 12.92 : pow( ( $v + 0.055 ) / 1.055, 2.4 );
		},
		nrds_hex_to_rgb( $hex )
	);
	return ( 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2] ) < 0.179;
}

/**
 * The four palette colors plus the shades derived from them.
 *
 * @return array CSS custom property => hex color
 */
function nrds_palette() {
	$colors = array();
	foreach ( nrds_color_fields() as $key => $field ) {
		$value = sanitize_hex_color( nrds_setting( $key ) );
		$colors[ $key ] = $value ? $value : $field[1];
	}

	$dark  = $colors['color_dark'];
	$light = $colors['color_light'];

	return array(
		'--nrd-primary'       => $colors['color_primary'],
		'--nrd-primary-hover' => nrds_mix_colors( $colors['color_primary'], '#000000', 0.15 ),
		'--nrd-on-primary'    => $light,
		'--nrd-dark'          => $dark,
		'--nrd-secondary'     => $colors['color_secondary'],
		'--nrd-light'         => $light,
		'--nrd-text-muted'    => nrds_mix_colors( $dark, $light, 0.35 ),
		'--nrd-border'        => nrds_mix_colors( $dark, $light, 0.85 ),
		'--nrd-surface-alt'   => nrds_mix_colors( $dark, $light, 0.95 ),
		'--nrd-on-dark-muted' => nrds_mix_colors( $light, $dark, 0.25 ),
	);
}

/**
 * CSS for the palette and the content width.
 *
 * @return string
 */
function nrds_settings_css() {
	$css = ':root{';
	foreach ( nrds_palette() as $property => $value ) {
		$css .= $property . ':' . $value . ';';
	}
	$css .= '--content-max-width:' . absint( nrds_setting( 'content_width' ) ) . 'px;';
	return $css . '}';
}
