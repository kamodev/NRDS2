<?php
/**
 * Right sidebar.
 *
 * Whether it shows is decided by nrds_get_sidebars() from Appearance →
 * Theme Settings → Layout and the per-post "Sidebars" box.
 *
 * @package NRDS
 */

$nrds_sidebars = nrds_get_sidebars();
if ( empty( $nrds_sidebars['right'] ) ) {
	return;
}
?>
<aside id="right-sidebar" class="sidebar sidebar--right" aria-label="<?php esc_attr_e( 'Right sidebar', 'nrds-theme' ); ?>">
	<?php dynamic_sidebar( 'right-sidebar' ); ?>
</aside>
