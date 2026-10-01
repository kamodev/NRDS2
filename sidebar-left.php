<?php
/**
 * Left sidebar.
 *
 * Whether it shows is decided by nrds_get_sidebars() from Appearance →
 * Theme Settings → Layout and the per-post "Sidebars" box.
 *
 * @package NRDS
 */

$nrds_sidebars = nrds_get_sidebars();
if ( empty( $nrds_sidebars['left'] ) ) {
	return;
}
?>
<aside id="left-sidebar" class="sidebar sidebar--left" aria-label="<?php esc_attr_e( 'Left sidebar', 'nrds-theme' ); ?>">
	<?php dynamic_sidebar( 'left-sidebar' ); ?>
</aside>
