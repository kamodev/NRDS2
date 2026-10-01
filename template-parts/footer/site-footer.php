<?php
/**
 * Site footer: newsletter band, four columns, disclaimer and bottom bar.
 *
 * Each of the four columns can hold, in this order:
 *   1. The brand block (column 1 only): logo, about text and social icons,
 *      when turned on in Appearance → Theme Settings → Footer
 *   2. A menu assigned to the "Footer Column N" menu location
 *   3. Widgets in the "Footer Column N" widget area (text, custom HTML,
 *      blocks, navigation menus, etc.)
 *   4. Anything hooked to the `nrds_footer_column_N` action from a plugin or
 *      child theme
 *
 * Empty columns are skipped and the rest share the width.
 *
 * @package NRDS
 */

$nrds_columns    = nrds_footer_columns();
$nrds_has_brand  = isset( $nrds_columns[1] ) && $nrds_columns[1]['brand'];
$nrds_disclaimer = nrds_setting( 'footer_disclaimer' );
?>
<footer id="colophon" class="site-footer nrds-footer" role="contentinfo">
	<?php if ( is_active_sidebar( 'footer-top' ) ) : ?>
		<div class="nrds-footer__band">
			<div class="nrds-footer__container">
				<?php dynamic_sidebar( 'footer-top' ); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $nrds_columns ) : ?>
		<div class="nrds-footer__main">
			<div class="nrds-footer__container nrds-footer__grid nrds-footer__grid--<?php echo count( $nrds_columns ); ?><?php echo $nrds_has_brand ? ' has-brand' : ''; ?>">
				<?php foreach ( $nrds_columns as $nrds_i => $nrds_column ) : ?>
					<div class="nrds-footer__col nrds-footer__col--<?php echo (int) $nrds_i; ?>">
						<?php if ( $nrds_column['brand'] ) : ?>
							<div class="nrds-footer__brand">
								<?php nrds_site_brand( 'p' ); ?>
								<?php if ( nrds_setting( 'footer_about' ) ) : ?>
									<p class="nrds-footer__about"><?php echo nl2br( esc_html( nrds_setting( 'footer_about' ) ) ); ?></p>
								<?php endif; ?>
								<?php nrds_social_links(); ?>
							</div>
						<?php endif; ?>

						<?php if ( has_nav_menu( $nrds_column['menu_location'] ) ) : ?>
							<?php $nrds_menu_name = wp_get_nav_menu_name( $nrds_column['menu_location'] ); ?>
							<nav class="nrds-footer__nav" aria-label="<?php echo esc_attr( $nrds_menu_name ); ?>">
								<h2 class="widget-title"><?php echo esc_html( $nrds_menu_name ); ?></h2>
								<?php
								wp_nav_menu(
									array(
										'theme_location' => $nrds_column['menu_location'],
										'container'      => false,
										'menu_class'     => 'nrds-footer__menu',
										'depth'          => 1,
									)
								);
								?>
							</nav>
						<?php endif; ?>

						<?php if ( is_active_sidebar( $nrds_column['sidebar_id'] ) ) : ?>
							<?php dynamic_sidebar( $nrds_column['sidebar_id'] ); ?>
						<?php endif; ?>

						<?php do_action( $nrds_column['action'] ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $nrds_disclaimer ) : ?>
		<div class="nrds-footer__legal">
			<div class="nrds-footer__container">
				<p><?php echo nl2br( esc_html( $nrds_disclaimer ) ); ?></p>
			</div>
		</div>
	<?php endif; ?>

	<div class="nrds-footer__bottom">
		<div class="nrds-footer__container">
			<p class="nrds-footer__copyright"><?php echo esc_html( nrds_copyright_text() ); ?></p>
			<?php
			wp_nav_menu(
				array(
					'theme_location'       => 'footer',
					'container'            => 'nav',
					'container_class'      => 'nrds-footer__legal-nav',
					'container_aria_label' => __( 'Footer', 'nrds-theme' ),
					'menu_class'           => 'nrds-footer__bottom-menu',
					'depth'                => 1,
					'fallback_cb'          => false,
				)
			);
			?>
			<?php if ( is_active_sidebar( 'footer-secondary' ) ) : ?>
				<div class="nrds-footer__bottom-widgets">
					<?php dynamic_sidebar( 'footer-secondary' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</footer>
