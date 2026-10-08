<?php
/**
 * Page layout: sized or full-screen site, and the left and right sidebars.
 *
 * The front page, posts, pages and archives each have their own left and
 * right sidebar switches (Appearance → Theme Settings → Layout). A single
 * post or page can override them in the "Sidebars" box on its edit screen.
 * A sidebar only shows when its widget area has widgets.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget area for each side.
 *
 * @param string|null $context Sidebar context (the WooCommerce integration
 *                             gives shop pages their own left area).
 * @return array side => sidebar ID
 */
function nrds_sidebar_areas( $context = null ) {
	return apply_filters(
		'nrds_sidebar_areas',
		array(
			'left'  => 'left-sidebar',
			'right' => 'right-sidebar',
		),
		$context
	);
}

/**
 * Sidebar context of the current view, or null where the theme shows no
 * sidebars (the Full Width template and 404 pages).
 *
 * @return string|null 'front', 'post', 'page', 'archive' or null.
 */
function nrds_sidebar_context() {
	$context = null;

	if ( is_front_page() ) {
		$context = 'front';
	} elseif ( is_page_template( 'page-full-width.php' ) ) {
		$context = null;
	} elseif ( is_page() ) {
		$context = 'page';
	} elseif ( is_singular() ) {
		$context = 'post';
	} elseif ( is_home() || is_archive() || is_search() ) {
		$context = 'archive';
	}

	/**
	 * Filter the sidebar context. Return null for no sidebars.
	 *
	 * @param string|null $context 'front', 'post', 'page', 'archive' or null.
	 */
	return apply_filters( 'nrds_sidebar_context', $context );
}

/**
 * Per-post sidebar choices.
 *
 * @return array value => label
 */
function nrds_sidebar_overrides() {
	return array(
		'both'  => __( 'Left and right', 'nrds-theme' ),
		'left'  => __( 'Left only', 'nrds-theme' ),
		'right' => __( 'Right only', 'nrds-theme' ),
		'none'  => __( 'No sidebars (full width)', 'nrds-theme' ),
	);
}

/**
 * Which sidebars show on the current view.
 *
 * @return array { left: string|false, right: string|false } Widget area ID
 *               for each side that shows, false for each side that doesn't.
 */
function nrds_get_sidebars() {
	$show    = array(
		'left'  => false,
		'right' => false,
	);
	$context = nrds_sidebar_context();

	if ( $context ) {
		$show['left']  = 'show' === nrds_setting( $context . '_left_sidebar' );
		$show['right'] = 'show' === nrds_setting( $context . '_right_sidebar' );

		if ( is_singular() ) {
			$override = get_post_meta( get_queried_object_id(), '_nrds_sidebars', true );
			if ( array_key_exists( $override, nrds_sidebar_overrides() ) ) {
				$show['left']  = in_array( $override, array( 'both', 'left' ), true );
				$show['right'] = in_array( $override, array( 'both', 'right' ), true );
			}
		}

		foreach ( nrds_sidebar_areas( $context ) as $side => $area ) {
			$show[ $side ] = ( $show[ $side ] && is_active_sidebar( $area ) ) ? $area : false;
		}
	}

	/**
	 * Filter which sidebars show.
	 *
	 * @param array       $show    { left: string|false, right: string|false }.
	 * @param string|null $context Sidebar context.
	 */
	return apply_filters( 'nrds_sidebars', $show, $context );
}

/**
 * Body classes for the site width and sidebars.
 *
 * @param array $classes Body classes.
 * @return array
 */
function nrds_layout_body_class( $classes ) {
	$classes[] = 'full' === nrds_setting( 'site_width' ) ? 'nrds-fullscreen' : 'nrds-sized';

	$sidebars = nrds_get_sidebars();
	foreach ( $sidebars as $side => $area ) {
		if ( $area ) {
			$classes[] = 'has-' . $side . '-sidebar';
		}
	}
	if ( ! array_filter( $sidebars ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'nrds_layout_body_class' );

/**
 * "Sidebars" box on posts and pages.
 */
function nrds_add_sidebar_meta_box() {
	add_meta_box( 'nrds-sidebars', __( 'Sidebars', 'nrds-theme' ), 'nrds_render_sidebar_meta_box', array( 'post', 'page' ), 'side', 'default' );
}
add_action( 'add_meta_boxes', 'nrds_add_sidebar_meta_box' );

/**
 * Render the "Sidebars" box.
 *
 * @param WP_Post $post Post being edited.
 */
function nrds_render_sidebar_meta_box( $post ) {
	$value   = get_post_meta( $post->ID, '_nrds_sidebars', true );
	$context = 'page' === $post->post_type ? 'page' : 'post';
	if ( (int) get_option( 'page_on_front' ) === $post->ID && 'page' === get_option( 'show_on_front' ) ) {
		$context = 'front';
	}
	$left    = 'show' === nrds_setting( $context . '_left_sidebar' );
	$right   = 'show' === nrds_setting( $context . '_right_sidebar' );
	$default = $left ? ( $right ? 'both' : 'left' ) : ( $right ? 'right' : 'none' );
	$choices = nrds_sidebar_overrides();

	wp_nonce_field( 'nrds_sidebar_meta', 'nrds_sidebar_nonce' );
	?>
	<p>
		<label class="screen-reader-text" for="nrds-sidebars-choice"><?php esc_html_e( 'Sidebars', 'nrds-theme' ); ?></label>
		<select id="nrds-sidebars-choice" name="nrds_sidebars" style="width:100%">
			<option value="" <?php selected( $value, '' ); ?>>
				<?php
				/* translators: %s: the default sidebar choice, for example "Right only". */
				echo esc_html( sprintf( __( 'Theme default (%s)', 'nrds-theme' ), $choices[ $default ] ) );
				?>
			</option>
			<?php foreach ( $choices as $choice => $label ) : ?>
				<option value="<?php echo esc_attr( $choice ); ?>" <?php selected( $value, $choice ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
		<p class="description">
			<?php
			printf(
				/* translators: 1: settings page link, 2: widgets page link. */
				wp_kses_post( __( 'Change the default in <a href="%1$s">Theme Settings</a>. Add sidebar content in <a href="%2$s">Widgets</a>.', 'nrds-theme' ) ),
				esc_url( admin_url( 'themes.php?page=nrds-theme-settings&tab=layout' ) ),
				esc_url( admin_url( 'widgets.php' ) )
			);
			?>
		</p>
	<?php endif; ?>
	<?php
}

/**
 * Save the "Sidebars" box.
 *
 * @param int $post_id Post ID.
 */
function nrds_save_sidebar_meta( $post_id ) {
	if ( ! isset( $_POST['nrds_sidebar_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nrds_sidebar_nonce'] ) ), 'nrds_sidebar_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$value = isset( $_POST['nrds_sidebars'] ) ? sanitize_key( wp_unslash( $_POST['nrds_sidebars'] ) ) : '';
	if ( array_key_exists( $value, nrds_sidebar_overrides() ) ) {
		update_post_meta( $post_id, '_nrds_sidebars', $value );
	} else {
		delete_post_meta( $post_id, '_nrds_sidebars' );
	}
}
add_action( 'save_post', 'nrds_save_sidebar_meta' );
