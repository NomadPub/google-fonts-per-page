<?php
function gppp_register_settings_page() {
    add_options_page(
        'Google Font Per Page (Enhanced)',
        'Google Font Settings',
        'manage_options',
        'gppp-settings',
        'gppp_render_settings_page'
    );
}
add_action('admin_menu', 'gppp_register_settings_page');

function gppp_register_settings() {
    register_setting('gppp_settings_group', 'gppp_available_fonts');
    register_setting('gppp_settings_group', 'gppp_default_font');
    register_setting('gppp_settings_group', 'gppp_default_weights');
    register_setting('gppp_settings_group', 'gppp_default_size');
    register_setting('gppp_settings_group', 'gppp_enabled_post_types');
    register_setting('gppp_settings_group', 'gppp_css_selector');
    register_setting('gppp_settings_group', 'gppp_font_display');
}
add_action('admin_init', 'gppp_register_settings');

function gppp_render_settings_page() {
    $available_fonts = get_option('gppp_available_fonts');
    $default_font = get_option('gppp_default_font', '');
    $default_weights = get_option('gppp_default_weights', '400,700');
    $default_size = get_option('gppp_default_size', '16px');
    $enabled_post_types = get_option('gppp_enabled_post_types', ['page']);
    $css_selector = get_option('gppp_css_selector', 'body');
    $font_display = get_option('gppp_font_display', 'swap');

    if (!is_array($available_fonts)) {
        $available_fonts = explode(',', $available_fonts);
    }

    $all_post_types = get_post_types(['public' => true], 'objects');
    $display_options = [
        'auto' => 'Auto (Browser decides)',
        'block' => 'Block (FOIT)',
        'swap' => 'Swap (FOUT)',
        'fallback' => 'Fallback',
        'optional' => 'Optional'
    ];

    ?>
    <div class="wrap">
        <h1>Google Font Per Page (Enhanced)</h1>
        <form method="post" action="options.php">
            <?php settings_fields('gppp_settings_group'); ?>
            
            <h2>Font Configuration</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Available Fonts</th>
                    <td>
                        <p>Enter one font per line (e.g., "Roboto", "Open Sans")</p>
                        <textarea name="gppp_available_fonts" rows="10" cols="50"><?php
                            echo esc_textarea(implode("\n", $available_fonts ?: []));
                        ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Default Font Weights</th>
                    <td>
                        <input type="text" name="gppp_default_weights" value="<?php echo esc_attr($default_weights); ?>" />
                        <p class="description">Comma-separated weights (e.g., 300,400,500,700)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Default Font Size</th>
                    <td>
                        <input type="text" name="gppp_default_size" value="<?php echo esc_attr($default_size); ?>" />
                        <p class="description">Default size with unit (e.g., 16px, 1.2rem)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Default Font</th>
                    <td>
                        <select name="gppp_default_font">
                            <option value="">Use Theme Default</option>
                            <?php foreach ((array)$available_fonts as $font): ?>
                                <option value="<?php echo esc_attr($font); ?>" <?php selected($default_font, $font); ?>>
                                    <?php echo esc_html($font); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">CSS Selector</th>
                    <td>
                        <input type="text" name="gppp_css_selector" value="<?php echo esc_attr($css_selector); ?>" />
                        <p class="description">Where to apply fonts (e.g., "body", ".entry-content")</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Font Display</th>
                    <td>
                        <select name="gppp_font_display">
                            <?php foreach ($display_options as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($font_display, $value); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">Controls how fonts load (recommended: swap)</p>
                    </td>
                </tr>
            </table>

            <h2>Post Type Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Enable For Post Types</th>
                    <td>
                        <?php foreach ($all_post_types as $post_type): ?>
                            <label>
                                <input type="checkbox" name="gppp_enabled_post_types[]" value="<?php echo esc_attr($post_type->name); ?>"
                                    <?php checked(in_array($post_type->name, $enabled_post_types)); ?> />
                                <?php echo esc_html($post_type->labels->singular_name); ?>
                            </label><br/>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

add_filter('gppp_available_fonts', function () {
    $saved_fonts = get_option('gppp_available_fonts');
    if (!$saved_fonts) return [];

    if (!is_array($saved_fonts)) {
        $saved_fonts = array_filter(array_map('trim', explode("\n", $saved_fonts)));
    }

    return $saved_fonts ?: [];
});