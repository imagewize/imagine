<?php
/**
 * Comparison between Imagine Blocks and Gutenberg Blocks
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * This file is for educational purposes only - it explains the differences
 * between Imagine blocks and Gutenberg blocks
 */

class Imagine_Editor_Comparison {
    
    /**
     * How Imagine blocks are structured (Vue.js based)
     */
    public static function imagine_block_example() {
        // Imagine blocks are stored as JSON in post meta
        $imagine_block = [
            'id' => 'block-12345',
            'type' => 'header',
            'content' => 'This is a header',
            'settings' => [
                'level' => 'h2',
                // Other block-specific settings
            ]
        ];
        
        // Imagine renders blocks using custom PHP functions
        $output = Imagine_Blocks::render_block($imagine_block);
        
        return $output; // Returns: <h2>This is a header</h2>
    }
    
    /**
     * How Gutenberg blocks are structured (React based)
     */
    public static function gutenberg_block_example() {
        // Gutenberg blocks are stored directly in post_content
        $gutenberg_block = '<!-- wp:heading --><h2>This is a header</h2><!-- /wp:heading -->';
        
        // Gutenberg blocks are parsed and rendered by WordPress core
        $blocks = parse_blocks($gutenberg_block);
        $output = '';
        
        foreach ($blocks as $block) {
            $output .= render_block($block);
        }
        
        return $output; // Returns: <h2>This is a header</h2>
    }
    
    /**
     * Main differences between Imagine and Gutenberg
     */
    public static function key_differences() {
        return [
            'Storage' => [
                'Imagine' => 'Blocks stored as JSON in post meta with rendered HTML in post_content',
                'Gutenberg' => 'Blocks stored directly in post_content with HTML and block comments'
            ],
            'Frontend' => [
                'Imagine' => 'Custom rendering system using PHP functions',
                'Gutenberg' => 'WordPress core rendering or server-side registered render callbacks'
            ],
            'Editor' => [
                'Imagine' => 'Custom Vue.js based editor',
                'Gutenberg' => 'React-based editor integrated with WordPress'
            ],
            'Block Registration' => [
                'Imagine' => 'PHP array in the Imagine_Blocks class',
                'Gutenberg' => 'JavaScript registration via registerBlockType'
            ],
            'Compatibility' => [
                'Imagine' => 'Self-contained system separate from Gutenberg',
                'Gutenberg' => 'Native WordPress system'
            ]
        ];
    }
    
    /**
     * When you might choose one over the other
     */
    public static function use_cases() {
        return [
            'Use Imagine Blocks when' => [
                'You want a completely custom UI separate from Gutenberg',
                'You prefer Vue.js over React',
                'You need a simpler drag-and-drop experience',
                'You want to maintain full control over the editing experience'
            ],
            'Use Gutenberg Blocks when' => [
                'You want to leverage WordPress core functionality',
                'You need compatibility with other Gutenberg plugins',
                'You want to benefit from WordPress improvements to the editor',
                'Your users are already familiar with the Gutenberg interface'
            ]
        ];
    }
}
