<?php
// Enqueue block editor assets
function gppp_enqueue_block_editor_assets() {
    wp_enqueue_script(
        'gppp-enhanced-block-editor',
        plugins_url('../block-editor-enhanced.js', __FILE__),
        ['wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components'],
        filemtime(plugin_dir_path(__FILE__) . '/../block-editor-enhanced.js'),
        true
    );

    $available_fonts = gppp_get_available_fonts_for_js();
    $default_font = get_option('gppp_default_font', '');
    $default_weights = get_option('gppp_default_weights', '400,700');
    $default_size = get_option('gppp_default_size', '16px');
    $enabled_post_types = get_option('gppp_enabled_post_types', ['page']);
    $default_css = get_option('gppp_css_selector', 'body');
    $font_display = get_option('gppp_font_display', 'swap');

    wp_localize_script('gppp-enhanced-block-editor', 'gpppData', [
        'fonts' => $available_fonts,
        'defaultFont' => $default_font,
        'defaultWeights' => $default_weights,
        'defaultSize' => $default_size,
        'defaultCSS' => $default_css,
        'fontDisplay' => $font_display,
        'enabledPostTypes' => $enabled_post_types,
    ]);
}
add_action('enqueue_block_editor_assets', 'gppp_enqueue_block_editor_assets');

// Enqueue selected font on frontend with performance optimizations
function gppp_enqueue_frontend_styles() {
    if (!is_singular()) return;

    $post_id = get_the_ID();
    $post_type = get_post_type($post_id);

    if (!in_array($post_type, get_option('gppp_enabled_post_types', ['page']))) return;

    $font = get_post_meta($post_id, '_custom_google_font', true);
    if (!$font) {
        $font = get_option('gppp_default_font', '');
    }

    if (!$font) return;

    $weights = get_post_meta($post_id, '_custom_font_weights', true) ?: get_option('gppp_default_weights', '400,700');
    $size = get_post_meta($post_id, '_custom_font_size', true) ?: get_option('gppp_default_size', '16px');
    $css_selector = get_post_meta($post_id, '_custom_font_css', true) ?: get_option('gppp_css_selector', 'body');
    $font_display = get_option('gppp_font_display', 'swap');

    // Clean up weights string
    $clean_weights = preg_replace('/\s+/', '', $weights);
    
    // Build font URL
    $font_url = add_query_arg([
        'family' => urlencode("{$font}:wght@{$clean_weights}"),
        'display' => $font_display
    ], 'https://fonts.googleapis.com/css2');

    // Enqueue font
    wp_enqueue_style("gppp-font-{$post_id}", $font_url, [], null);

    // Add inline CSS for the selector and size
    $custom_css = "{$css_selector} { 
        font-family: '{$font}', sans-serif;
        font-size: {$size};
    }";
    
    wp_add_inline_style("gppp-font-{$post_id}", $custom_css);
}
add_action('wp_enqueue_scripts', 'gppp_enqueue_frontend_styles', 20);

// Prepare available fonts for JavaScript use
function gppp_get_available_fonts_for_js() {
    $fonts = apply_filters('gppp_available_fonts', []);
    $result = [];

    foreach ($fonts as $font) {
        $result[] = [
            'label' => $font,
            'value' => $font,
        ];
    }

    return $result;
}

// Add preconnect for Google Fonts for better performance
function gppp_add_resource_hints($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = [
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        ];
        $urls[] = [
            'href' => 'https://fonts.googleapis.com',
            'crossorigin',
        ];
    }
    return $urls;
}
add_filter('wp_resource_hints', 'gppp_add_resource_hints', 10, 2);