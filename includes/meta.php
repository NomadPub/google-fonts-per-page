<?php
function gppp_register_meta() {
    $supported_types = get_option('gppp_enabled_post_types', ['page']);
    foreach ($supported_types as $post_type) {
        register_post_meta($post_type, '_custom_google_font', array(
            'show_in_rest' => true,
            'type' => 'string',
            'single' => true,
            'auth_callback' => '__return_true',
        ));
        
        register_post_meta($post_type, '_custom_font_weights', array(
            'show_in_rest' => true,
            'type' => 'string',
            'single' => true,
            'auth_callback' => '__return_true',
            'default' => get_option('gppp_default_weights', '400,700'),
        ));
        
        register_post_meta($post_type, '_custom_font_size', array(
            'show_in_rest' => true,
            'type' => 'string',
            'single' => true,
            'auth_callback' => '__return_true',
            'default' => get_option('gppp_default_size', '16px'),
        ));
        
        register_post_meta($post_type, '_custom_font_css', array(
            'show_in_rest' => true,
            'type' => 'string',
            'single' => true,
            'auth_callback' => '__return_true',
            'default' => get_option('gppp_css_selector', 'body'),
        ));
    }
}
add_action('init', 'gppp_register_meta');