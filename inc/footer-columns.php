<?php
/**
 * Four-column footer.
 *
 * Each column can hold, in this order:
 *   1. A menu assigned to the "Footer Column N" menu location
 *   2. Widgets placed in the "Footer Column N" widget area (text, custom HTML,
 *      blocks, navigation menu widgets, etc.)
 *   3. Anything hooked to the `nrds_footer_column_N` action from a plugin or
 *      child theme
 *
 * Empty columns are skipped and the remaining ones share the width.
 */

$nrds_footer_columns = array();

for ( $i = 1; $i <= 4; $i++ ) {
    $menu_location = 'footer-column-' . $i;
    $sidebar_id    = 'footer-widget-' . $i;
    $action        = 'nrds_footer_column_' . $i;

    if ( has_nav_menu( $menu_location ) || is_active_sidebar( $sidebar_id ) || has_action( $action ) ) {
        $nrds_footer_columns[ $i ] = array(
            'menu_location' => $menu_location,
            'sidebar_id'    => $sidebar_id,
            'action'        => $action,
        );
    }
}

if ( empty( $nrds_footer_columns ) ) {
    return;
}
?>
<section class="footer-widgets-area footer-columns-<?php echo count( $nrds_footer_columns ); ?>">
    <?php foreach ( $nrds_footer_columns as $i => $column ) : ?>
        <div class="footer-widget-column footer-column-<?php echo $i; ?>">
            <?php if ( has_nav_menu( $column['menu_location'] ) ) : ?>
                <?php $menu_name = wp_get_nav_menu_name( $column['menu_location'] ); ?>
                <nav class="footer-column-nav" aria-label="<?php echo esc_attr( $menu_name ); ?>">
                    <h2 class="widget-title"><?php echo esc_html( $menu_name ); ?></h2>
                    <?php
                        wp_nav_menu( array(
                            'theme_location' => $column['menu_location'],
                            'container'      => false,
                            'menu_class'     => 'footer-column-menu',
                            'depth'          => 1,
                        ));
                    ?>
                </nav>
            <?php endif; ?>

            <?php if ( is_active_sidebar( $column['sidebar_id'] ) ) : ?>
                <?php dynamic_sidebar( $column['sidebar_id'] ); ?>
            <?php endif; ?>

            <?php do_action( $column['action'] ); ?>
        </div>
    <?php endforeach; ?>
</section>
