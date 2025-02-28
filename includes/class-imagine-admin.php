<?php

if (!defined('ABSPATH')) {
    exit;
}

class Imagine_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }
    
    public function register_admin_menu() {
        add_menu_page(
            'Imagine Page Builder',
            'Imagine',
            'edit_posts',
            'imagine-settings',
            [$this, 'render_settings_page'],
            'dashicons-layout'
        );
        
        add_submenu_page(
            null, // No parent
            'Imagine Editor',
            'Imagine Editor',
            'edit_posts',
            'imagine-editor',
            [$this, 'render_editor_page']
        );
    }
    
    public function render_settings_page() {
        echo '<div class="wrap">';
        echo '<h1>Imagine Page Builder Settings</h1>';
        echo '<p>Configure your page builder settings here.</p>';
        echo '</div>';
    }
    
    public function render_editor_page() {
        $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
        
        if (!$post_id) {
            wp_die('No post selected');
        }
        
        $post = get_post($post_id);
        
        if (!$post) {
            wp_die('Post not found');
        }
        
        // Enqueue editor assets
        wp_enqueue_script('imagine-editor');
        wp_enqueue_style('imagine-editor-style');
        
        // Pass data to the editor
        wp_localize_script('imagine-editor', 'imagineData', [
            'post_id' => $post_id,
            'post_title' => $post->post_title,
            'post_content' => $post->post_content,
            'nonce' => wp_create_nonce('imagine_save'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'blocks_data' => get_post_meta($post_id, '_imagine_blocks', true),
        ]);
        
        echo '<div id="imagine-editor-app"></div>';
    }
    
    public function enqueue_admin_assets($hook) {
        // Only load on our settings page
        if ('toplevel_page_imagine-settings' === $hook) {
            wp_enqueue_style(
                'imagine-admin-style',
                IMAGINE_ASSETS_URL . 'imagine-admin.css',
                [],
                IMAGINE_VERSION
            );
            
            wp_enqueue_script(
                'imagine-admin-script',
                IMAGINE_ASSETS_URL . 'imagine-admin.js',
                ['jquery'],
                IMAGINE_VERSION,
                true
            );
        }
    }
}
