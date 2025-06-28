<?php
// Enqueue the enhanced block editor JS
function gppp_enqueue_enhanced_block_editor_assets() {
    wp_enqueue_script(
        'gppp-enhanced-block-editor',
        plugins_url('../block-editor-enhanced.js', __FILE__),
        ['wp-plugins', 'wp-editPost', 'wp-element', 'wp-components', 'wp-data'],
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
add_action('enqueue_block_editor_assets', 'gppp_enqueue_enhanced_block_editor_assets');