<?php
function gppp_add_font_meta_box() {
    $post_types = get_option('gppp_enabled_post_types', ['page']);
    if (empty($post_types)) return;
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'gppp_font_selector',
            'Google Font Settings',
            'gppp_render_font_meta_box',
            $post_type,
            'side',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'gppp_add_font_meta_box');

function gppp_render_font_meta_box($post) {
    wp_nonce_field('gppp_save_font', 'gppp_font_nonce');

    $selected_font = get_post_meta($post->ID, '_custom_google_font', true);
    $selected_weights = get_post_meta($post->ID, '_custom_font_weights', true) ?: get_option('gppp_default_weights', '400,700');
    $selected_size = get_post_meta($post->ID, '_custom_font_size', true) ?: get_option('gppp_default_size', '16px');
    $selected_css = get_post_meta($post->ID, '_custom_font_css', true) ?: get_option('gppp_css_selector', 'body');
    $available_fonts = apply_filters('gppp_available_fonts', []);
    $default_font = get_option('gppp_default_font', '');
    $font_display = get_option('gppp_font_display', 'swap');

    echo '<div class="gppp-meta-box">';
    
    // Font Selector
    echo '<p><strong>Select Font:</strong></p>';
    echo '<select name="gppp_custom_font" style="width:100%; margin-bottom:1em;">';
    echo '<option value="">Default (' . esc_html($default_font ?: 'Theme Default') . ')</option>';
    foreach ($available_fonts as $font) {
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr($font),
            selected($selected_font, $font, false),
            esc_html($font)
        );
    }
    echo '</select>';

    // Font Weights (only shown when a font is selected)
    if ($selected_font || $default_font) {
        echo '<p><strong>Font Weights:</strong></p>';
        echo '<input type="text" name="gppp_custom_weights" value="' . esc_attr($selected_weights) . '" style="width:100%; margin-bottom:1em;" />';
        echo '<p class="description" style="margin-top:-0.5em; margin-bottom:1em;">Comma-separated (e.g., 300,400,700)</p>';
    }

    // Font Size (always shown)
    echo '<p><strong>Font Size:</strong></p>';
    echo '<input type="text" name="gppp_custom_size" value="' . esc_attr($selected_size) . '" style="width:100%; margin-bottom:1em;" />';
    echo '<p class="description" style="margin-top:-0.5em; margin-bottom:1em;">With unit (e.g., 16px, 1.2rem)</p>';

    // CSS Selector (always shown)
    echo '<p><strong>CSS Selector:</strong></p>';
    echo '<input type="text" name="gppp_custom_css" value="' . esc_attr($selected_css) . '" style="width:100%; margin-bottom:1em;" />';
    echo '<p class="description" style="margin-top:-0.5em; margin-bottom:1em;">Where to apply (e.g., "body", ".content")</p>';

    // Preview (only shown when a font is selected)
    if ($selected_font || $default_font) {
        $preview_font = $selected_font ?: $default_font;
        $preview_size = $selected_size ?: '16px';
        echo '<p><strong>Preview:</strong></p>';
        echo '<div style="font-family:' . esc_attr($preview_font) . ', sans-serif; font-size:' . esc_attr($preview_size) . '; border:1px solid #ccc; padding:8px; margin-bottom:1em; border-radius:4px;">';
        echo 'The quick brown fox jumps over the lazy dog.';
        echo '</div>';
        
        echo '<div style="font-size:12px; color:#666; padding:8px; background:#f6f7f7; border-radius:4px;">';
        echo '<strong>Settings Summary:</strong><br>';
        echo 'Applied to: ' . esc_html($selected_css) . '<br>';
        echo 'Font family: ' . esc_html($preview_font) . '<br>';
        echo 'Font weights: ' . esc_html(str_replace(' ', '', $selected_weights)) . '<br>';
        echo 'Font size: ' . esc_html($preview_size);
        echo '</div>';
    }

    echo '</div>';
}

function gppp_save_font($post_id) {
    if (!isset($_POST['gppp_font_nonce']) || !wp_verify_nonce($_POST['gppp_font_nonce'], 'gppp_save_font')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Always save these fields regardless of font selection
    $size = isset($_POST['gppp_custom_size']) ? sanitize_text_field($_POST['gppp_custom_size']) : '';
    update_post_meta($post_id, '_custom_font_size', $size);

    $css = isset($_POST['gppp_custom_css']) ? sanitize_text_field($_POST['gppp_custom_css']) : '';
    update_post_meta($post_id, '_custom_font_css', $css);

    // Save font and weights only if font is selected
    $font = isset($_POST['gppp_custom_font']) ? sanitize_text_field($_POST['gppp_custom_font']) : '';
    update_post_meta($post_id, '_custom_google_font', $font);

    if ($font) {
        $weights = isset($_POST['gppp_custom_weights']) ? sanitize_text_field($_POST['gppp_custom_weights']) : '';
        update_post_meta($post_id, '_custom_font_weights', $weights);
    }
}
add_action('save_post', 'gppp_save_font');