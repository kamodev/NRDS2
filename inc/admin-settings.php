<?php
/**
 * Appearance → Theme Settings: Layout, Colors and Footer tabs.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the settings page.
 */
function nrds_add_settings_page() {
	$GLOBALS['nrds_settings_hook'] = add_theme_page(
		__( 'Theme Settings', 'nrds-theme' ),
		__( 'Theme Settings', 'nrds-theme' ),
		'edit_theme_options',
		'nrds-theme-settings',
		'nrds_render_settings_page'
	);
}
add_action( 'admin_menu', 'nrds_add_settings_page' );

/**
 * Link to the settings page from the Themes screen.
 *
 * @param array $links Action links.
 * @return array
 */
function nrds_settings_action_link( $links ) {
	$links[] = '<a href="' . esc_url( admin_url( 'themes.php?page=nrds-theme-settings' ) ) . '">' . esc_html__( 'Theme Settings', 'nrds-theme' ) . '</a>';
	return $links;
}
add_filter( 'theme_action_links_' . get_template(), 'nrds_settings_action_link' );

/**
 * Assets for the settings page.
 *
 * @param string $hook Current admin page hook.
 */
function nrds_settings_assets( $hook ) {
	if ( empty( $GLOBALS['nrds_settings_hook'] ) || $hook !== $GLOBALS['nrds_settings_hook'] ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'nrds-admin-settings', NRDS_URI . '/assets/css/admin/settings.css', array( 'wp-color-picker' ), NRDS_VERSION );
	wp_enqueue_script( 'nrds-admin-settings', NRDS_URI . '/assets/js/admin-settings.js', array( 'jquery', 'wp-color-picker' ), NRDS_VERSION, true );
	wp_localize_script(
		'nrds-admin-settings',
		'nrdsSettings',
		array(
			'ok'    => __( 'Readable', 'nrds-theme' ),
			'large' => __( 'OK for large or bold text such as buttons and headings; too low for body text.', 'nrds-theme' ),
			'low'   => __( 'Low contrast: hard to read for many people.', 'nrds-theme' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'nrds_settings_assets' );

/**
 * A select for one choice setting.
 *
 * @param string $key    Setting key.
 * @param array  $values Saved values.
 * @param string $label  Field label (for screen readers when $inline is true).
 * @param bool   $inline Whether to show the label above the select.
 */
function nrds_render_choice_field( $key, $values, $label, $inline = true ) {
	$fields  = nrds_choice_fields();
	$id      = 'nrds-' . str_replace( '_', '-', $key );
	$current = isset( $values[ $key ] ) ? $values[ $key ] : $fields[ $key ][1];
	?>
	<label class="nrds-inline-field" for="<?php echo esc_attr( $id ); ?>">
		<span<?php echo $inline ? '' : ' class="screen-reader-text"'; ?>><?php echo esc_html( $label ); ?></span>
		<select id="<?php echo esc_attr( $id ); ?>" name="nrds_theme_settings_options[<?php echo esc_attr( $key ); ?>]">
			<?php foreach ( $fields[ $key ][0] as $value => $choice ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $choice ); ?></option>
			<?php endforeach; ?>
		</select>
	</label>
	<?php
}

/**
 * Layout tab: site width, sidebars and post header images.
 *
 * @param array $values Saved values.
 */
function nrds_render_layout_tab( $values ) {
	list( $min, $max, $default ) = nrds_content_width_range();

	$site_width = isset( $values['site_width'] ) ? $values['site_width'] : 'sized';
	$width      = isset( $values['content_width'] ) ? (int) $values['content_width'] : $default;
	$choices    = nrds_choice_fields();
	?>
	<h2><?php esc_html_e( 'Site width', 'nrds-theme' ); ?></h2>
	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><?php esc_html_e( 'Layout', 'nrds-theme' ); ?></th>
				<td>
					<fieldset class="nrds-site-width">
						<legend class="screen-reader-text"><?php esc_html_e( 'Layout', 'nrds-theme' ); ?></legend>
						<?php foreach ( $choices['site_width'][0] as $value => $label ) : ?>
							<label>
								<input type="radio" name="nrds_theme_settings_options[site_width]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $site_width, $value ); ?>>
								<?php echo esc_html( $label ); ?>
							</label><br>
						<?php endforeach; ?>
					</fieldset>
					<p class="description"><?php esc_html_e( 'Sized keeps the header, content and footer within the content width below. Full screen runs them across the whole window.', 'nrds-theme' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="nrds-content-width"><?php esc_html_e( 'Content width', 'nrds-theme' ); ?></label></th>
				<td>
					<input type="range" id="nrds-content-width" name="nrds_theme_settings_options[content_width]"
						min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" step="10"
						value="<?php echo esc_attr( $width ); ?>" <?php disabled( 'full', $site_width ); ?>>
					<output for="nrds-content-width" id="nrds-content-width-output"><?php echo esc_html( $width ); ?></output> px
					<p class="description"><?php esc_html_e( 'Maximum width of the site when the layout is Sized.', 'nrds-theme' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<h2><?php esc_html_e( 'Sidebars', 'nrds-theme' ); ?></h2>
	<p><?php esc_html_e( 'Turn the left and right sidebars on or off for each kind of page.', 'nrds-theme' ); ?></p>
	<table class="form-table nrds-sidebar-table" role="presentation">
		<tbody>
		<?php foreach ( nrds_sidebar_contexts() as $context => $label ) : ?>
			<tr>
				<th scope="row"><?php echo esc_html( $label ); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><?php echo esc_html( $label ); ?></legend>
						<?php
						nrds_render_choice_field( $context . '_left_sidebar', $values, __( 'Left sidebar', 'nrds-theme' ) );
						nrds_render_choice_field( $context . '_right_sidebar', $values, __( 'Right sidebar', 'nrds-theme' ) );
						?>
					</fieldset>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<p class="description">
		<?php
		$empty = array();
		foreach ( nrds_sidebar_areas() as $area ) {
			if ( ! is_active_sidebar( $area ) && isset( $GLOBALS['wp_registered_sidebars'][ $area ] ) ) {
				$empty[] = $GLOBALS['wp_registered_sidebars'][ $area ]['name'];
			}
		}
		esc_html_e( 'A sidebar only appears when its widget area has widgets.', 'nrds-theme' );
		if ( $empty ) {
			/* translators: %s: widget area names. */
			echo ' <strong>' . esc_html( sprintf( __( 'No widgets yet in: %s.', 'nrds-theme' ), implode( ', ', $empty ) ) ) . '</strong>';
		}
		echo ' <a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">' . esc_html__( 'Manage widgets', 'nrds-theme' ) . '</a>.';
		echo ' ' . esc_html__( 'Single posts and pages can override this in the "Sidebars" box on their edit screen. The Full Width page template never shows sidebars.', 'nrds-theme' );
		?>
	</p>

	<h2><?php esc_html_e( 'Posts', 'nrds-theme' ); ?></h2>
	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><?php esc_html_e( 'Post header image', 'nrds-theme' ); ?></th>
				<td>
					<?php nrds_render_choice_field( 'post_header_image', $values, __( 'Post header image', 'nrds-theme' ), false ); ?>
					<p class="description"><?php esc_html_e( 'Shows the featured image (or the custom header image) as a banner behind the title on single posts.', 'nrds-theme' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Text/background pairs checked for contrast on the Colors tab.
 *
 * @return array
 */
function nrds_contrast_pairs() {
	return array(
		array( __( 'Button text on primary', 'nrds-theme' ), 'color_light', 'color_primary' ),
		array( __( 'Text on page background', 'nrds-theme' ), 'color_dark', 'color_light' ),
		array( __( 'Links on page background', 'nrds-theme' ), 'color_primary', 'color_light' ),
		array( __( 'Footer text', 'nrds-theme' ), 'color_light', 'color_dark' ),
		array( __( 'Footer links on hover', 'nrds-theme' ), 'color_primary', 'color_dark' ),
		array( __( 'Text on secondary', 'nrds-theme' ), 'color_light', 'color_secondary' ),
	);
}

/**
 * Colors tab: color themes, the four palette colors, a live preview and
 * contrast checks.
 *
 * @param array $values Saved values.
 */
function nrds_render_colors_tab( $values ) {
	?>
	<p><?php esc_html_e( 'The palette has four colors. Pick a color theme to fill them in, then fine-tune any color. Leave a color blank to use the default. Nothing changes on the site until you save.', 'nrds-theme' ); ?></p>
	<div class="nrds-colors">
		<div class="nrds-colors__main">
			<div class="nrds-presets">
				<h2><?php esc_html_e( 'Color themes', 'nrds-theme' ); ?></h2>
				<div class="nrds-presets__list">
					<?php foreach ( nrds_color_presets() as $slug => $preset ) : ?>
						<button type="button" class="button nrds-preset" data-colors="<?php echo esc_attr( wp_json_encode( $preset[1] ) ); ?>">
							<span class="nrds-preset__swatches" aria-hidden="true">
								<?php foreach ( array_keys( nrds_color_fields() ) as $k ) : ?>
									<span style="background:<?php echo esc_attr( $preset[1][ $k ] ); ?>"></span>
								<?php endforeach; ?>
							</span>
							<?php echo esc_html( $preset[0] ); ?>
						</button>
					<?php endforeach; ?>
					<button type="button" class="button-link nrds-clear-colors"><?php esc_html_e( 'Clear all (use defaults)', 'nrds-theme' ); ?></button>
				</div>
			</div>

			<table class="form-table nrds-color-table" role="presentation">
				<tbody>
				<?php
				foreach ( nrds_color_fields() as $key => $field ) :
					$id      = 'nrds-' . str_replace( '_', '-', $key );
					$current = isset( $values[ $key ] ) ? $values[ $key ] : '';
					?>
					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field[0] ); ?></label>
							<p class="description"><?php echo esc_html( $field[3] ); ?></p>
						</th>
						<td>
							<input type="text" class="nrds-color-field" id="<?php echo esc_attr( $id ); ?>"
								name="nrds_theme_settings_options[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( $current ); ?>"
								data-key="<?php echo esc_attr( $key ); ?>"
								data-var="<?php echo esc_attr( $field[2] ); ?>"
								data-default="<?php echo esc_attr( $field[1] ); ?>">
							<p class="nrds-inherit">
								<span class="nrds-swatch" style="background:<?php echo esc_attr( $field[1] ); ?>" aria-hidden="true"></span>
								<?php
								/* translators: %s: color hex. */
								echo esc_html( sprintf( __( 'Blank uses %s', 'nrds-theme' ), $field[1] ) );
								?>
							</p>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<aside class="nrds-colors__side" aria-label="<?php esc_attr_e( 'Preview', 'nrds-theme' ); ?>">
			<h2><?php esc_html_e( 'Preview', 'nrds-theme' ); ?></h2>
			<div class="nrds-preview">
				<div class="nrds-preview__bar"></div>
				<div class="nrds-preview__header">
					<span class="nrds-preview__logo"><b>N</b>ational <b>R</b>eadiness <b>D</b>efense</span>
					<span class="nrds-preview__nav"><span class="is-active"><?php esc_html_e( 'Home', 'nrds-theme' ); ?></span> <?php esc_html_e( 'Courses', 'nrds-theme' ); ?></span>
				</div>
				<div class="nrds-preview__body">
					<strong class="nrds-preview__title"><?php esc_html_e( 'Ready for anything', 'nrds-theme' ); ?></strong>
					<p><?php esc_html_e( 'Body text with a', 'nrds-theme' ); ?> <a href="#" onclick="return false;"><?php esc_html_e( 'link', 'nrds-theme' ); ?></a>.</p>
					<span class="nrds-preview__btns">
						<span class="nrds-preview__btn"><?php esc_html_e( 'Book a class', 'nrds-theme' ); ?></span>
						<span class="nrds-preview__btn nrds-preview__btn--secondary"><?php esc_html_e( 'Details', 'nrds-theme' ); ?></span>
					</span>
					<div class="nrds-preview__alt"><?php esc_html_e( 'Sidebar and alternate sections', 'nrds-theme' ); ?></div>
				</div>
				<div class="nrds-preview__footer">
					<span class="nrds-preview__heading"><?php esc_html_e( 'Explore', 'nrds-theme' ); ?></span>
					<span><?php esc_html_e( 'Footer link', 'nrds-theme' ); ?> · <span class="nrds-preview__hover"><?php esc_html_e( 'Hovered link', 'nrds-theme' ); ?></span></span>
				</div>
				<div class="nrds-preview__bottom">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?></div>
			</div>
			<h3><?php esc_html_e( 'Readability', 'nrds-theme' ); ?></h3>
			<ul class="nrds-contrast" data-pairs="<?php echo esc_attr( wp_json_encode( nrds_contrast_pairs() ) ); ?>"></ul>
		</aside>
	</div>
	<?php
}

/**
 * Footer tab: brand column, social links, disclaimer and copyright.
 *
 * @param array $values Saved values.
 */
function nrds_render_footer_tab( $values ) {
	$text = nrds_text_fields();
	$get  = function ( $key ) use ( $values ) {
		return isset( $values[ $key ] ) ? $values[ $key ] : '';
	};
	?>
	<p>
		<?php
		printf(
			/* translators: 1: menus screen link, 2: widgets screen link. */
			wp_kses_post( __( 'The footer has four columns. Fill each one with a menu assigned to "Footer Column 1–4" in <a href="%1$s">Menus</a>, and/or widgets in the "Footer Column 1–4" areas in <a href="%2$s">Widgets</a> (text, images, buttons, contact details, any block). Empty columns are skipped. A "Footer Newsletter Band" widget area spans the top of the footer for a sign-up form, and the "Footer Bottom Bar" area sits next to the copyright line.', 'nrds-theme' ) ),
			esc_url( admin_url( 'nav-menus.php?action=locations' ) ),
			esc_url( admin_url( 'widgets.php' ) )
		);
		?>
	</p>

	<h2><?php esc_html_e( 'Brand column', 'nrds-theme' ); ?></h2>
	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><?php esc_html_e( 'Logo, about text and social icons', 'nrds-theme' ); ?></th>
				<td>
					<?php nrds_render_choice_field( 'footer_brand', $values, __( 'Brand column', 'nrds-theme' ), false ); ?>
					<p class="description"><?php esc_html_e( 'Shown at the top of footer column 1. Uses the site logo (Appearance → Customize → Site Identity), or the site title when there is no logo.', 'nrds-theme' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="nrds-footer-about"><?php echo esc_html( $text['footer_about'][0] ); ?></label></th>
				<td><textarea id="nrds-footer-about" class="large-text" rows="3" name="nrds_theme_settings_options[footer_about]"><?php echo esc_textarea( $get( 'footer_about' ) ); ?></textarea></td>
			</tr>
		</tbody>
	</table>

	<h2><?php esc_html_e( 'Social links', 'nrds-theme' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Icons appear in the brand column for every link you fill in.', 'nrds-theme' ); ?></p>
	<table class="form-table" role="presentation">
		<tbody>
		<?php
		foreach ( $text as $key => $field ) :
			if ( 0 !== strpos( $key, 'social_' ) ) {
				continue;
			}
			$id = 'nrds-' . str_replace( '_', '-', $key );
			?>
			<tr>
				<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
				<td>
					<input type="<?php echo esc_attr( $field[2] ); ?>" id="<?php echo esc_attr( $id ); ?>" class="regular-text"
						name="nrds_theme_settings_options[<?php echo esc_attr( $key ); ?>]"
						value="<?php echo esc_attr( $get( $key ) ); ?>"
						placeholder="<?php echo 'email' === $field[2] ? 'info@example.com' : 'https://'; ?>">
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<h2><?php esc_html_e( 'Below the columns', 'nrds-theme' ); ?></h2>
	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><label for="nrds-footer-disclaimer"><?php echo esc_html( $text['footer_disclaimer'][0] ); ?></label></th>
				<td>
					<textarea id="nrds-footer-disclaimer" class="large-text" rows="3" name="nrds_theme_settings_options[footer_disclaimer]"><?php echo esc_textarea( $get( 'footer_disclaimer' ) ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Optional small print shown in its own band, for example a legal notice.', 'nrds-theme' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="nrds-footer-copyright"><?php echo esc_html( $text['footer_copyright'][0] ); ?></label></th>
				<td>
					<input type="text" id="nrds-footer-copyright" class="large-text" name="nrds_theme_settings_options[footer_copyright]" value="<?php echo esc_attr( $get( 'footer_copyright' ) ); ?>" placeholder="<?php echo esc_attr( nrds_default_copyright() ); ?>">
					<p class="description"><?php esc_html_e( 'Leave blank for the line shown in the box. {year} is replaced with the current year.', 'nrds-theme' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Appearance → Theme Settings.
 */
function nrds_render_settings_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$tabs = array(
		'layout' => __( 'Layout & Sidebars', 'nrds-theme' ),
		'colors' => __( 'Colors', 'nrds-theme' ),
		'footer' => __( 'Footer', 'nrds-theme' ),
	);
	$tab    = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'layout'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tab    = array_key_exists( $tab, $tabs ) ? $tab : 'layout';
	$values = nrds_site_settings();
	?>
	<div class="wrap nrds-settings">
		<h1><?php esc_html_e( 'Theme Settings', 'nrds-theme' ); ?></h1>
		<?php settings_errors(); ?>

		<nav class="nav-tab-wrapper" aria-label="<?php esc_attr_e( 'Settings sections', 'nrds-theme' ); ?>">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=nrds-theme-settings&tab=' . $slug ) ); ?>" class="nav-tab<?php echo $tab === $slug ? ' nav-tab-active' : ''; ?>"<?php echo $tab === $slug ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'nrds_theme_settings' );
			// Marks this save as one tab's fields, to merge with the other tabs' stored values.
			echo '<input type="hidden" name="nrds_theme_settings_options[_tab]" value="' . esc_attr( $tab ) . '">';

			if ( 'colors' === $tab ) {
				nrds_render_colors_tab( $values );
			} elseif ( 'footer' === $tab ) {
				nrds_render_footer_tab( $values );
			} else {
				nrds_render_layout_tab( $values );
			}
			submit_button();
			?>
		</form>
	</div>
	<?php
}
