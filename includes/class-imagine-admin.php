<?php

if (!defined('ABSPATH')) {
    exit;
}

class Imagine_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_enqueue_scripts', [$this, 'maybe_redirect_to_imagine']);
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
        
        // Add visible submenu for pages
        add_submenu_page(
            'imagine-settings',
            'Pages',
            'Pages',
            'edit_posts',
            'edit.php?post_type=page',
            null
        );
        
        // Add visible submenu for posts
        add_submenu_page(
            'imagine-settings',
            'Posts',
            'Posts',
            'edit_posts',
            'edit.php',
            null
        );
        
        // Editor page (hidden from menu)
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
        
        // Add a quick start guide section
        echo '<div class="card" style="max-width: 800px; margin-top: 20px;">';
        echo '<h2>Quick Start Guide</h2>';
        echo '<p>Imagine Page Builder allows you to create beautiful layouts for your content. Here\'s how to get started:</p>';
        
        echo '<h3>Method 1: Edit existing content</h3>';
        echo '<p>To edit an existing page or post:</p>';
        echo '<ol>';
        echo '<li>Go to <a href="' . admin_url('edit.php?post_type=page') . '">Pages</a> or <a href="' . admin_url('edit.php') . '">Posts</a></li>';
        echo '<li>Hover over a page/post and click "Edit with Imagine"</li>';
        echo '</ol>';
        
        echo '<h3>Method 2: Create new content with Imagine</h3>';
        echo '<div style="margin-bottom: 20px;">';
        echo '<p>Start creating new content with Imagine:</p>';
        
        // Get post types that support the editor
        $post_types = get_post_types(['public' => true, 'show_ui' => true], 'objects');
        foreach ($post_types as $post_type) {
            if (!in_array($post_type->name, ['attachment', 'revision', 'nav_menu_item'])) {
                echo '<a href="' . admin_url('post-new.php?post_type=' . $post_type->name . '&use_imagine=1') . '" class="button" style="margin-right: 10px; margin-bottom: 10px;">New ' . $post_type->labels->singular_name . '</a>';
            }
        }
        echo '</div>';
        
        echo '</div>';
        echo '</div>';
    }
    
    public function maybe_redirect_to_imagine() {
        // Check if we're creating a new post and should use Imagine
        if (isset($_GET['post_type']) && isset($_GET['use_imagine']) && $_GET['use_imagine'] == 1) {
            if (!isset($_GET['imagine_redirect_done'])) {
                // Create a draft post first
                $post_id = wp_insert_post([
                    'post_title' => 'Draft ' . ucfirst($_GET['post_type']),
                    'post_type' => sanitize_text_field($_GET['post_type']),
                    'post_status' => 'draft'
                ]);
                
                if ($post_id && !is_wp_error($post_id)) {
                    // Redirect to Imagine editor with the new post
                    wp_redirect(admin_url('admin.php?page=imagine-editor&post_id=' . $post_id));
                    exit;
                }
            }
        }
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
        
        // Check if editor script is available
        if (wp_script_is('imagine-editor', 'registered')) {
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
                'blocks_data' => Imagine_Editor::get_blocks_data($post_id),
                'available_blocks' => Imagine_Blocks::get_available_blocks()
            ]);
            
            echo '<div class="wrap">';
            echo '<h1>Editing: ' . esc_html($post->post_title) . '</h1>';
            echo '<div id="imagine-editor-app" data-post-id="' . esc_attr($post_id) . '"></div>';
            echo '</div>';
        } else {
            echo '<div class="wrap">';
            echo '<h1>Imagine Editor</h1>';
            echo '<div class="notice notice-error">';
            echo '<p>The Imagine Editor assets are not available. This usually happens because the build files have not been generated yet.</p>';
            echo '<p>If you are developing the plugin, please run the build process to generate the required assets.</p>';
            echo '</div>';
            echo '<p><a href="' . esc_url(get_edit_post_link($post_id)) . '" class="button">Edit with Standard WordPress Editor</a></p>';
            echo '</div>';
        }
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
