<?php
/*
Plugin Name: Google Font Per Page (Enhanced)
Description: Select custom Google Fonts with per-page size, weight controls and CSS targeting. Optimized font loading.
Version: 2.2
Author: Damon Noisette
*/

if (!defined('ABSPATH')) {
    exit;
}

define('GPPP_DIR', plugin_dir_path(__FILE__));
define('GPPP_URL', plugin_dir_url(__FILE__));

// Shared includes
require_once GPPP_DIR . 'includes/enqueue.php';
require_once GPPP_DIR . 'includes/settings-page.php';
require_once GPPP_DIR . 'includes/meta.php';

// Load editor-specific UI
function gppp_load_editor_integration() {
    if (function_exists('register_block_type') && !class_exists('Classic_Editor')) {
        require_once GPPP_DIR . 'includes/gutenberg.php';
    } else {
        require_once GPPP_DIR . 'includes/classic-editor.php';
    }
}
add_action('plugins_loaded', 'gppp_load_editor_integration');