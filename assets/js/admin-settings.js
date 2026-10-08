/**
 * Theme Settings screen: content width slider, color pickers, color themes,
 * live preview and contrast checks.
 */
( function ( $ ) {
	'use strict';

	var i18n = window.nrdsSettings || {};

	function hexToRgb( hex ) {
		hex = String( hex || '' ).replace( '#', '' );
		if ( 3 === hex.length ) {
			hex = hex.replace( /(.)/g, '$1$1' );
		}
		if ( ! /^[0-9a-f]{6}$/i.test( hex ) ) {
			return null;
		}
		var n = parseInt( hex, 16 );
		return [ ( n >> 16 ) & 255, ( n >> 8 ) & 255, n & 255 ];
	}

	function luminance( rgb ) {
		var c = rgb.map( function ( v ) {
			v /= 255;
			return v <= 0.03928 ? v / 12.92 : Math.pow( ( v + 0.055 ) / 1.055, 2.4 );
		} );
		return 0.2126 * c[ 0 ] + 0.7152 * c[ 1 ] + 0.0722 * c[ 2 ];
	}

	function contrast( a, b ) {
		var x = hexToRgb( a );
		var y = hexToRgb( b );
		if ( ! x || ! y ) {
			return null;
		}
		var l1 = luminance( x );
		var l2 = luminance( y );
		return ( Math.max( l1, l2 ) + 0.05 ) / ( Math.min( l1, l2 ) + 0.05 );
	}

	// Same mix as nrds_mix_colors() in inc/settings.php.
	function mix( from, to, amount ) {
		var a = hexToRgb( from );
		var b = hexToRgb( to );
		if ( ! a || ! b ) {
			return from;
		}
		return '#' + a.map( function ( v, i ) {
			return ( '0' + Math.round( v + ( b[ i ] - v ) * amount ).toString( 16 ) ).slice( -2 );
		} ).join( '' );
	}

	function initWidth() {
		var $slider = $( '#nrds-content-width' );
		var $output = $( '#nrds-content-width-output' );
		var $radios = $( 'input[name="nrds_theme_settings_options[site_width]"]' );

		$slider.on( 'input change', function () {
			$output.text( this.value );
		} );
		$radios.on( 'change', function () {
			var full = 'full' === $radios.filter( ':checked' ).val();
			$slider.prop( 'disabled', full );
			$output.css( 'opacity', full ? 0.5 : 1 );
		} ).filter( ':checked' ).trigger( 'change' );
	}

	function initColors( $wrap ) {
		var $inputs = $wrap.find( '.nrds-color-field' );
		var $preview = $wrap.find( '.nrds-preview' );
		var $contrast = $wrap.find( '.nrds-contrast' );
		var pairs = $contrast.data( 'pairs' ) || [];
		var pending = null;

		// Effective color for a key: typed value, else the default.
		function resolved( key ) {
			var $input = $inputs.filter( '[data-key="' + key + '"]' );
			return $.trim( $input.val() ) || $input.data( 'default' );
		}

		function update() {
			pending = null;
			var preview = $preview[ 0 ].style;
			var dark = resolved( 'color_dark' );
			var light = resolved( 'color_light' );
			var primary = resolved( 'color_primary' );

			$inputs.each( function () {
				var $input = $( this );
				preview.setProperty( $input.data( 'var' ), resolved( $input.data( 'key' ) ) );
			} );
			preview.setProperty( '--nrd-primary-hover', mix( primary, '#000000', 0.15 ) );
			preview.setProperty( '--nrd-text-muted', mix( dark, light, 0.35 ) );
			preview.setProperty( '--nrd-surface-alt', mix( dark, light, 0.95 ) );
			preview.setProperty( '--nrd-border', mix( dark, light, 0.85 ) );
			preview.setProperty( '--nrd-on-dark-muted', mix( light, dark, 0.25 ) );

			$contrast.empty();
			pairs.forEach( function ( pair ) {
				var fg = resolved( pair[ 1 ] );
				var bg = resolved( pair[ 2 ] );
				var ratio = contrast( fg, bg );
				if ( null === ratio ) {
					return;
				}
				var level = ratio >= 4.5 ? 'ok' : ( ratio >= 3 ? 'large' : 'low' );
				var $li = $( '<li/>' ).addClass( 'is-' + level );
				$( '<span class="nrds-contrast__sample" aria-hidden="true">Aa</span>' ).css( { color: fg, background: bg } ).appendTo( $li );
				$( '<span class="nrds-contrast__label"/>' ).text( pair[ 0 ] ).appendTo( $li );
				$( '<span class="nrds-contrast__ratio"/>' ).text( ratio.toFixed( 1 ) + ':1' ).appendTo( $li );
				$( '<span class="nrds-contrast__status"/>' ).text( i18n[ level ] || '' ).appendTo( $li );
				$contrast.append( $li );
			} );
		}

		function queue() {
			if ( ! pending ) {
				pending = window.setTimeout( update, 30 );
			}
		}

		$inputs.wpColorPicker( {
			change: queue,
			clear: queue,
		} );
		$inputs.on( 'input change', queue );

		$wrap.on( 'click', '.nrds-preset', function () {
			var colors = $( this ).data( 'colors' ) || {};
			$inputs.each( function () {
				var key = $( this ).data( 'key' );
				if ( colors[ key ] ) {
					$( this ).wpColorPicker( 'color', colors[ key ] );
				}
			} );
			queue();
		} );

		$wrap.on( 'click', '.nrds-clear-colors', function () {
			$inputs.each( function () {
				var $input = $( this );
				if ( $input.val() ) {
					$input.closest( '.wp-picker-container' ).find( '.wp-picker-clear' ).trigger( 'click' );
				}
			} );
			queue();
		} );

		update();
	}

	// Media Library pickers for the footer logo versions.
	function initImageFields() {
		$( '.nrds-image-field' ).each( function () {
			var $field = $( this );
			var $input = $field.find( '.nrds-image-field__id' );
			var $img = $field.find( '.nrds-image-field__preview img' );
			var $empty = $field.find( '.nrds-image-field__empty' );
			var $select = $field.find( '.nrds-image-field__select' );
			var $remove = $field.find( '.nrds-image-field__remove' );
			var frame = null;

			function show( id, url ) {
				$input.val( id || '' );
				$img.attr( 'src', url || '' ).prop( 'hidden', ! url );
				$empty.prop( 'hidden', !! url );
				$remove.prop( 'hidden', ! id );
				$select.text( id ? ( i18n.replace || 'Replace' ) : ( i18n.pick || 'Choose logo' ) );
			}

			$select.on( 'click', function () {
				if ( ! frame ) {
					frame = wp.media( {
						title: i18n.pick,
						button: { text: i18n.use },
						library: { type: 'image' },
						multiple: false,
					} );
					frame.on( 'select', function () {
						var image = frame.state().get( 'selection' ).first().toJSON();
						var sized = image.sizes && image.sizes.medium ? image.sizes.medium.url : image.url;
						show( image.id, sized );
					} );
				}
				frame.open();
			} );

			$remove.on( 'click', function () {
				show( '', '' );
				$select.trigger( 'focus' );
			} );
		} );
	}

	$( function () {
		initWidth();
		initImageFields();
		$( '.nrds-colors' ).each( function () {
			initColors( $( this ) );
		} );
	} );
} )( jQuery );
