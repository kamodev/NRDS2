<?php
// All functions related to the theme should be added here
// This file is automatically loaded by WordPress when the theme is activated
require_once('inc/theme-settings.php');

// Add a body class if fullscreen mode is enabled
// Output a dynamic CSS variable for content width based on theme setting
add_action('wp_head', function() {
    $options = get_option('nrds_theme_settings_options');
    if (!empty($options['content_width'])) {
        $width = intval($options['content_width']);
        echo '<style>:root { --content-max-width: ' . esc_attr($width) . 'px; }</style>';
    }
});

add_filter('body_class', function($classes) {
    $options = get_option('nrds_theme_settings_options');
    if (!empty($options['fullscreen'])) {
        $classes[] = 'nrds-fullscreen';
    }
    return $classes;
});

function nrds_theme_setup() {

    // Add support for various WordPress features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-background');
    add_theme_support('custom-header');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'nrds-theme'),
        'header' => __('Header Menu', 'nrds-theme'),
        'footer' => __('Footer Menu', 'nrds-theme'),
    ));
}

function nrds_theme_scripts() {
    // Enqueue styles
    wp_enqueue_style('nrds-theme-style', get_stylesheet_uri());

    // Enqueue scripts
    wp_enqueue_script('nrds-theme-script', get_template_directory_uri() . '/assets/js/script.js', array('jquery'), null, true);
}

function nrds_theme_sidebars() {
    // Register a sidebar
    // This is useful for themes that want to display widgets in the sidebar area.
    // It can be used for displaying recent posts, categories, archives, etc.
    // The sidebar can be styled separately from the main content.
    register_sidebar(array(
        'name'          => __('Right Sidebar', 'nrds-theme'),
        'id'            => 'right-sidebar',
        'description'   => __('Widgets in this area will be shown on the right side.', 'nrds-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // Register a footer widget area 1
    // This is useful for themes that want to display widgets in the footer area.
    // It can be used for displaying additional information, links, or advertisements.
    // The footer widget area can be styled separately from the main content.
    // It is a common practice to have multiple footer widget areas.
    register_sidebar(array(
        'name'          => __('Footer Widget Area 1', 'nrds-theme'),
        'id'            => 'footer-widget-1',
        'description'   => __('Widgets in this area will be shown in the footer.', 'nrds-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // Register a footer widget area 1
    // This is useful for themes that want to display widgets in the footer area.
    // It can be used for displaying additional information, links, or advertisements.
    // The footer widget area can be styled separately from the main content.
    // It is a common practice to have multiple footer widget areas.
    register_sidebar(array(
        'name'          => __('Footer Widget Area 2', 'nrds-theme'),
        'id'            => 'footer-widget-2',
        'description'   => __('Widgets in this area will be shown in the footer.', 'nrds-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
    
    // Register a footer widget area 3
    // This is useful for themes that want to display widgets in the footer area.
    // It can be used for displaying additional information, links, or advertisements.
    // The footer widget area can be styled separately from the main content.
    // It is a common practice to have multiple footer widget areas.
    register_sidebar(array(
        'name'          => __('Footer Widget Area 3', 'nrds-theme'),
        'id'            => 'footer-widget-3',
        'description'   => __('Widgets in this area will be shown in the footer.', 'nrds-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    )); 

    // Register a header widget area
    // This is useful for themes that want to display widgets in the header area.
    // It can be used for displaying social media links, search forms, etc.
    register_sidebar(array(
        'name'          => __('Header Widget Area', 'nrds-theme'),
        'id'            => 'header-widget-area',
        'description'   => __('Widgets in this area will be shown in the header.', 'nrds-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
    
    // Register a left sidebar
    // This is useful for themes that have a left sidebar layout
    // or for themes that want to provide more widget areas.
    register_sidebar(array(
        'name'          => __('Left Sidebar', 'nrds-theme'),
        'id'            => 'left-sidebar',
        'description'   => __('Widgets in this area will be shown on the left side.', 'nrds-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}

function nrds_custom_logo_setup() {
    // Add support for custom logo in the theme
    $defaults = array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array('site-title', 'site-description'),
    );
    add_theme_support('custom-logo', $defaults);
}

// Add action section to the theme
add_action('after_setup_theme', 'nrds_custom_logo_setup');
add_action('after_setup_theme', 'nrds_theme_setup');
add_action('widgets_init', 'nrds_theme_sidebars');
add_action('wp_enqueue_scripts', 'nrds_theme_scripts');

// Add filter(s) to the theme
add_filter('widget_text', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');

// End of functions.php file
?>