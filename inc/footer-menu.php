<nav id="footer-navigation">
    <?php
        wp_nav_menu( array(
            'theme_location' => 'footer', // Name of your menu location
            'container'      => false,     // Don't wrap the ul in a div
            'menu_class'     => 'footer-menu', // Custom class for the ul element
            'menu_id'        => 'footer-menu' // Custom ID for the ul element
        ));
    ?>
</nav>