<?php
/**
 * Plugin Name: Imagine Page Builder
 * Description: A Vue.js-based page builder for WordPress similar to Elementor
 * Version: 1.0.0
 * Author: Jasper Frumau
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('IMAGINE_VERSION', '1.0.0');
define('IMAGINE_PATH', plugin_dir_path(__FILE__));
define('IMAGINE_URL', plugin_dir_url(__FILE__));
define('IMAGINE_ASSETS_URL', IMAGINE_URL . 'dist/assets/');

// Include necessary files
require_once IMAGINE_PATH . 'includes/class-imagine-editor.php';
require_once IMAGINE_PATH . 'includes/class-imagine-blocks.php';
require_once IMAGINE_PATH . 'includes/class-imagine-admin.php';

// Initialize the plugin
function imagine_init() {
    new Imagine_Admin();
}
add_action('plugins_loaded', 'imagine_init');

// Register editor scripts and styles
function imagine_register_editor_assets() {
    $asset_file = include(IMAGINE_PATH . 'dist/imagine-editor.asset.php');
    
    wp_register_script(
        'imagine-editor',
        IMAGINE_ASSETS_URL . 'imagine-editor.js',
        $asset_file['dependencies'],
        $asset_file['version'],
        true
    );
    
    wp_register_style(
        'imagine-editor-style',
        IMAGINE_ASSETS_URL . 'imagine-editor.css',
        [],
        $asset_file['version']
    );
}
add_action('admin_enqueue_scripts', 'imagine_register_editor_assets');

// Register AJAX endpoint for saving page data
function imagine_save_page_data() {
    check_ajax_referer('imagine_save', 'nonce');
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }
    
    $post_id = intval($_POST['post_id']);
    $content = wp_kses_post($_POST['content']);
    $blocks_data = sanitize_text_field($_POST['blocks_data']);
    
    update_post_meta($post_id, '_imagine_blocks', $blocks_data);
    
    $post_data = [
        'ID' => $post_id,
        'post_content' => $content
    ];
    
    wp_update_post($post_data);
    
    wp_send_json_success();
}
add_action('wp_ajax_imagine_save_page_data', 'imagine_save_page_data');

// Add the editor button to admin pages
function imagine_add_edit_button($actions, $post) {
    if (current_user_can('edit_post', $post->ID)) {
        $actions['imagine_edit'] = '<a href="' . admin_url('admin.php?page=imagine-editor&post_id=' . $post->ID) . '">Edit with Imagine</a>';
    }
    return $actions;
}
add_filter('post_row_actions', 'imagine_add_edit_button', 10, 2);
add_filter('page_row_actions', 'imagine_add_edit_button', 10, 2);
