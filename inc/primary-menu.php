<nav class="nrd-nav" aria-label="Primary">
    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
        <span class="menu-icon">☰</span>
        <span class="screen-reader-text"><?php esc_html_e( 'Primary Menu', 'nrds-theme' ); ?></span>
    </button>
    <?php
        /* wp_nav_menu( array(
            'theme_location' => 'primary', // Name of your menu location
            'container'      => false,     // Don't wrap the ul in a div
            'menu_class'     => 'main-menu', // Custom class for the ul element
            'menu_id'        => 'primary-menu' // Custom ID for the ul element
        ));*/
        wp_nav_menu( array(
            'theme_location' => 'primary', // Name of your menu location
            'container'      => false,     // Don't wrap the ul in a div
            'menu_id'        => 'primary-menu' // Custom ID for the ul element
        ));
    ?>
</nav>