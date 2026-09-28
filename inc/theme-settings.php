<?php
// Theme settings page in admin dashboard
function nrds_theme_settings_page_menu() {
    // Add a new menu item under "Appearance" for theme settings page

    add_theme_page(
        __('Theme Settings', 'nrds-theme'),
        __('Theme Settings', 'nrds-theme'),
        'manage_options',
        'nrds-theme-settings',
        'nrds_theme_settings_page',
    );
}
add_action('admin_menu', 'nrds_theme_settings_page_menu');

// Callback for the "Enable Fullscreen Layout" checkbox
function nrds_theme_setting_fullscreen_callback() {
    $options = get_option('nrds_theme_settings_options');
    $value = isset($options['fullscreen']) ? (bool) $options['fullscreen'] : false;
    ?>
    <input type="checkbox" id="nrds_theme_setting_fullscreen" name="nrds_theme_settings_options[fullscreen]" value="1" <?php checked( $value, true ); ?> onchange="document.getElementById('nrds_theme_setting_content_width').disabled = this.checked; document.getElementById('nrds_theme_setting_content_width_output').style.opacity = this.checked ? 0.5 : 1;" />
    <p class="description"><?php _e('When checked, the site will use a fullscreen layout (no max-width or side padding on content areas).', 'nrds-theme'); ?></p>
    <?php
}

// Callback for the content width slider
function nrds_theme_setting_content_width_callback() {
    $options = get_option('nrds_theme_settings_options');
    $value = isset($options['content_width']) ? intval($options['content_width']) : 1200;
    ?>
        <?php $fullscreen = isset($options['fullscreen']) ? (bool)$options['fullscreen'] : false; ?>
        <input type="range" min="800" max="1920" step="10" id="nrds_theme_setting_content_width" name="nrds_theme_settings_options[content_width]" value="<?php echo esc_attr($value); ?>" oninput="this.nextElementSibling.value = this.value" <?php if ($fullscreen) echo 'disabled'; ?> >
        <output id="nrds_theme_setting_content_width_output" style="margin-left:8px;<?php if ($fullscreen) echo 'opacity:0.5;'; ?>"><?php echo esc_html($value); ?></output> px
        <p class="description"><?php _e('Set the maximum width of the site content area (in pixels).', 'nrds-theme'); ?></p>
        <script>
        // Ensure slider disables/enables live if toggled after page load
        document.addEventListener('DOMContentLoaded', function() {
            var fs = document.getElementById('nrds_theme_setting_fullscreen');
            var slider = document.getElementById('nrds_theme_setting_content_width');
            var out = document.getElementById('nrds_theme_setting_content_width_output');
            if (fs && slider && out) {
                fs.addEventListener('change', function() {
                    slider.disabled = fs.checked;
                    out.style.opacity = fs.checked ? 0.5 : 1;
                });
            }
        });
        </script>
    <?php
}

function nrds_theme_settings_init() {
    // Add a slider for content width (in px)
    add_settings_field(
        'nrds_theme_setting_content_width',
        __('Content Width', 'nrds-theme'),
        'nrds_theme_setting_content_width_callback',
        'nrds-theme-settings',
        'nrds_theme_settings_display_section'
    );
    // Add a checkbox for fullscreen mode
    add_settings_field(
        'nrds_theme_setting_fullscreen',
        __('Enable Fullscreen Layout', 'nrds-theme'),
        'nrds_theme_setting_fullscreen_callback',
        'nrds-theme-settings',
        'nrds_theme_settings_display_section'
    );

    // Register a new setting for theme settings page
    // Register setting with sanitize callback to ensure valid values
    register_setting('nrds_theme_settings', 'nrds_theme_settings_options', 'nrds_theme_settings_sanitize');

    // Add a new section in the theme settings page
    add_settings_section(
        'nrds_theme_settings_general_section',
        __('General Settings', 'nrds-theme'),
        'nrds_theme_settings_general_section',
        'nrds-theme-settings'
    );

    add_settings_section(
        'nrds_theme_settings_display_section',
        __('Display Settings', 'nrds-theme'),
        'nrds_theme_settings_display_section',
        'nrds-theme-settings'
    ); 

    // Add a new field in the general settings section
    add_settings_field(
        'nrds_theme_setting_example',
        __('Example Setting', 'nrds-theme'),
        'nrds_theme_setting_example_callback',
        'nrds-theme-settings',
        'nrds_theme_settings_general_section'
    );

    // You can add more fields and sections as needed
    // For example, you can add a field for custom colors, fonts, etc.
    add_settings_field(
        'nrds_theme_setting_color',
        __('Primary Color', 'nrds-theme'),
        'nrds_theme_setting_color_callback',
        'nrds-theme-settings',
        'nrds_theme_settings_display_section'
    );

    // Add a checkbox to disable post header images
    add_settings_field(
        'nrds_theme_setting_disable_post_header_images',
        __('Disable post header images', 'nrds-theme'),
        'nrds_theme_setting_disable_post_header_images_callback',
        'nrds-theme-settings',
        'nrds_theme_settings_display_section'
    );
}
add_action('admin_init', 'nrds_theme_settings_init');

function nrds_theme_settings_display_section() {
    echo '<p>' . __('Manage display settings for the NRDS theme.', 'nrds-theme') . '</p>';
}

function nrds_theme_settings_general_section() {
    echo '<p>' . __('Manage general settings for the NRDS theme.', 'nrds-theme') . '</p>';
}

function nrds_theme_setting_color_callback() {
    $options = get_option('nrds_theme_settings_options');
    ?>
    <input type="text" name="nrds_theme_settings_options[nrds_theme_setting_color]" value="<?php echo isset($options['nrds_theme_setting_color']) ? esc_attr($options['nrds_theme_setting_color']) : ''; ?>" class="nrds-color-field" />
    <p class="description"><?php _e('Primary color field.', 'nrds-theme'); ?></p>
    <?php
}

function nrds_theme_setting_example_callback() {
    $options = get_option('nrds_theme_settings_options');
    ?>
    <input type="text" name="nrds_theme_settings_options[nrds_theme_setting_example]" value="<?php echo isset($options['nrds_theme_setting_example']) ? esc_attr($options['nrds_theme_setting_example']) : ''; ?>" />
    <p class="description"><?php _e('This is an example setting field.', 'nrds-theme'); ?></p>
    <?php
}

// Callback for the "Disable post header images" checkbox
function nrds_theme_setting_disable_post_header_images_callback() {
    $options = get_option('nrds_theme_settings_options');
    $value = isset($options['disable_post_header_images']) ? (bool) $options['disable_post_header_images'] : false;
    ?>
    <input type="checkbox" name="nrds_theme_settings_options[disable_post_header_images]" value="1" <?php checked( $value, true ); ?> />
    <p class="description"><?php _e('When checked, post header images (featured image or custom header) will not be shown on single posts.', 'nrds-theme'); ?></p>
    <?php
}

// Sanitize callback for theme settings
function nrds_theme_settings_sanitize( $input ) {
    $output = array();

    // Content width slider (sanitize and clamp)
    if ( isset( $input['content_width'] ) ) {
        $width = intval($input['content_width']);
        if ($width < 800) $width = 800;
        if ($width > 1920) $width = 1920;
        $output['content_width'] = $width;
    }

    if ( isset( $input['nrds_theme_setting_color'] ) ) {
        $output['nrds_theme_setting_color'] = sanitize_text_field( $input['nrds_theme_setting_color'] );
    }

    if ( isset( $input['nrds_theme_setting_example'] ) ) {
        $output['nrds_theme_setting_example'] = sanitize_text_field( $input['nrds_theme_setting_example'] );
    }

    // Ensure checkbox is stored as 1 or 0
    $output['disable_post_header_images'] = ! empty( $input['disable_post_header_images'] ) ? 1 : 0;
    $output['fullscreen'] = ! empty( $input['fullscreen'] ) ? 1 : 0;
    return $output;
}

function nrds_theme_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <p>Manage your theme's general settings here.</p>
    <form method="post" action="options.php">
    <?php
        settings_fields('nrds_theme_settings');
        do_settings_sections('nrds-theme-settings');
        submit_button();
    ?>
    </form>

    </div>
<?php } ?>