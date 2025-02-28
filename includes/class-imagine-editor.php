<?php

if (!defined('ABSPATH')) {
    exit;
}

class Imagine_Editor {
    public function __construct() {
        // Handle editor-specific functionality
    }
    
    public static function get_blocks_data($post_id) {
        $blocks_data = get_post_meta($post_id, '_imagine_blocks', true);
        
        if (empty($blocks_data)) {
            return '[]'; // Default to empty array
        }
        
        return $blocks_data;
    }
    
    public static function render_blocks($blocks_data) {
        $blocks = json_decode($blocks_data, true);
        $output = '';
        
        if (!is_array($blocks)) {
            return $output;
        }
        
        foreach ($blocks as $block) {
            $output .= self::render_block($block);
        }
        
        return $output;
    }
    
    private static function render_block($block) {
        $output = '';
        
        switch ($block['type']) {
            case 'header':
                $tag = $block['settings']['level'] ?? 'h2';
                $output = "<{$tag}>{$block['content']}</{$tag}>";
                break;
                
            case 'paragraph':
                $output = "<p>{$block['content']}</p>";
                break;
                
            // Add more block types here
        }
        
        return $output;
    }
}
