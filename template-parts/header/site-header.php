<?php
/**
 * Site header: top bar, logo, primary menu and header widgets.
 *
 * @package NRDS
 */

?>
<div class="nrd-topbar"></div>
<header id="masthead" class="nrd-header" role="banner">
	<div class="nrd-container nrd-header__inner">
		<div class="site-branding">
			<?php nrds_site_brand( is_front_page() ? 'h1' : 'p' ); ?>
		</div>

		<nav class="nrd-nav" aria-label="<?php esc_attr_e( 'Primary', 'nrds-theme' ); ?>">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
				<span class="menu-icon" aria-hidden="true">☰</span>
				<span class="screen-reader-text"><?php esc_html_e( 'Primary Menu', 'nrds-theme' ); ?></span>
			</button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_id'        => 'primary-menu',
				)
			);
			?>
		</nav>

		<?php if ( has_action( 'nrds_header_actions' ) ) : ?>
			<div class="nrd-header__actions">
				<?php
				/**
				 * Header icons after the menu (the WooCommerce integration adds account and cart).
				 */
				do_action( 'nrds_header_actions' );
				?>
			</div>
		<?php endif; ?>

		<?php if ( is_active_sidebar( 'header-widget-area' ) ) : ?>
			<div class="nrd-header__widgets">
				<?php dynamic_sidebar( 'header-widget-area' ); ?>
			</div>
		<?php endif; ?>
	</div>
</header>
