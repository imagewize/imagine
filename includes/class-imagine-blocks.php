<?php

if (!defined('ABSPATH')) {
    exit;
}

class Imagine_Blocks {
    private static $registered_blocks = [];
    
    /**
     * Get all available blocks for the editor
     */
    public static function get_available_blocks() {
        $default_blocks = [
            'header' => [
                'name' => 'Header',
                'icon' => 'heading',
                'defaults' => [
                    'level' => 'h2',
                    'content' => 'Header Text',
                ]
            ],
            'paragraph' => [
                'name' => 'Paragraph',
                'icon' => 'text',
                'defaults' => [
                    'content' => 'Enter your text here...',
                ]
            ],
            'image' => [
                'name' => 'Image',
                'icon' => 'format-image',
                'defaults' => [
                    'url' => '',
                    'alt' => '',
                    'caption' => '',
                    'align' => 'center',
                    'size' => 'medium'
                ]
            ],
            'button' => [
                'name' => 'Button',
                'icon' => 'button',
                'defaults' => [
                    'text' => 'Click Here',
                    'url' => '#',
                    'style' => 'primary',
                    'align' => 'left',
                    'newTab' => false
                ]
            ],
            'spacer' => [
                'name' => 'Spacer',
                'icon' => 'minus',
                'defaults' => [
                    'height' => '30px'
                ]
            ],
            'columns' => [
                'name' => 'Columns',
                'icon' => 'columns',
                'defaults' => [
                    'count' => 2,
                    'ratio' => '1:1',
                    'blocks' => []
                ]
            ],
            'list' => [
                'name' => 'List',
                'icon' => 'editor-ul',
                'defaults' => [
                    'type' => 'unordered',
                    'items' => ['List item 1', 'List item 2', 'List item 3']
                ]
            ],
            'quote' => [
                'name' => 'Quote',
                'icon' => 'format-quote',
                'defaults' => [
                    'content' => 'Enter your quote text here...',
                    'citation' => 'Citation',
                    'style' => 'default'
                ]
            ],
            'divider' => [
                'name' => 'Divider',
                'icon' => 'minus',
                'defaults' => [
                    'style' => 'solid',
                    'width' => '100%',
                    'height' => '1px',
                    'color' => '#DDDDDD'
                ]
            ],
            'video' => [
                'name' => 'Video',
                'icon' => 'format-video',
                'defaults' => [
                    'url' => '',
                    'embed_type' => 'youtube', // youtube, vimeo, etc.
                    'autoplay' => false,
                    'muted' => false,
                    'controls' => true
                ]
            ]
        ];

        // Allow plugins/themes to modify the available blocks
        $blocks = apply_filters('imagine_available_blocks', $default_blocks);
        
        // Cache the registered blocks
        self::$registered_blocks = $blocks;
        
        return $blocks;
    }
    
    /**
     * Register a custom block type
     */
    public static function register_block($type, $config) {
        // Make sure get_available_blocks has been called at least once
        if (empty(self::$registered_blocks)) {
            self::get_available_blocks();
        }
        
        // Add or override block
        self::$registered_blocks[$type] = wp_parse_args($config, [
            'name' => ucfirst($type),
            'icon' => 'admin-generic',
            'defaults' => []
        ]);
    }
    
    /**
     * Render blocks from saved data
     */
    public static function render_blocks($blocks_data) {
        if (empty($blocks_data)) {
            return '';
        }
        
        $blocks = json_decode($blocks_data, true);
        if (!is_array($blocks)) {
            return '';
        }
        
        $output = '';
        foreach ($blocks as $block) {
            $output .= self::render_block($block);
        }
        
        return $output;
    }
    
    /**
     * Render a single block
     */
    public static function render_block($block) {
        if (empty($block['type'])) {
            return '';
        }
        
        // Allow custom block rendering through filters
        $output = apply_filters('imagine_render_block_' . $block['type'], '', $block);
        if (!empty($output)) {
            return $output;
        }
        
        // Default rendering based on block type
        $output = '';
        switch ($block['type']) {
            case 'header':
                $level = isset($block['settings']['level']) ? $block['settings']['level'] : 'h2';
                $content = isset($block['content']) ? $block['content'] : '';
                $output = sprintf('<%1$s>%2$s</%1$s>', esc_attr($level), wp_kses_post($content));
                break;
            
            case 'paragraph':
                $content = isset($block['content']) ? $block['content'] : '';
                $output = sprintf('<p>%s</p>', wp_kses_post($content));
                break;
                
            case 'image':
                $url = isset($block['settings']['url']) ? $block['settings']['url'] : '';
                $alt = isset($block['settings']['alt']) ? $block['settings']['alt'] : '';
                $caption = isset($block['settings']['caption']) ? $block['settings']['caption'] : '';
                $align = isset($block['settings']['align']) ? $block['settings']['align'] : 'center';
                
                if (!empty($url)) {
                    $output = '<figure class="imagine-image imagine-align-' . esc_attr($align) . '">';
                    $output .= sprintf(
                        '<img src="%1$s" alt="%2$s" />',
                        esc_url($url),
                        esc_attr($alt)
                    );
                    
                    if (!empty($caption)) {
                        $output .= sprintf('<figcaption>%s</figcaption>', wp_kses_post($caption));
                    }
                    
                    $output .= '</figure>';
                }
                break;
                
            case 'button':
                $text = isset($block['settings']['text']) ? $block['settings']['text'] : 'Click Here';
                $url = isset($block['settings']['url']) ? $block['settings']['url'] : '#';
                $style = isset($block['settings']['style']) ? $block['settings']['style'] : 'primary';
                $align = isset($block['settings']['align']) ? $block['settings']['align'] : 'left';
                $newTab = isset($block['settings']['newTab']) && $block['settings']['newTab'] ? true : false;
                
                $target = $newTab ? ' target="_blank" rel="noopener noreferrer"' : '';
                
                $output = sprintf(
                    '<div class="imagine-button-wrap imagine-align-%3$s"><a href="%1$s" class="imagine-button imagine-button-%2$s"%4$s>%5$s</a></div>',
                    esc_url($url),
                    esc_attr($style),
                    esc_attr($align),
                    $target,
                    esc_html($text)
                );
                break;
                
            case 'spacer':
                $height = isset($block['settings']['height']) ? $block['settings']['height'] : '30px';
                $output = sprintf('<div class="imagine-spacer" style="height: %s;"></div>', esc_attr($height));
                break;
                
            case 'columns':
                $count = isset($block['settings']['count']) ? intval($block['settings']['count']) : 2;
                $blocks = isset($block['settings']['blocks']) ? $block['settings']['blocks'] : [];
                
                $output = '<div class="imagine-columns imagine-columns-' . esc_attr($count) . '">';
                
                // Loop through columns
                for ($i = 0; $i < $count; $i++) {
                    $output .= '<div class="imagine-column">';
                    
                    // Render blocks in this column
                    if (isset($blocks[$i]) && is_array($blocks[$i])) {
                        foreach ($blocks[$i] as $column_block) {
                            $output .= self::render_block($column_block);
                        }
                    }
                    
                    $output .= '</div>';
                }
                
                $output .= '</div>';
                break;
                
            case 'list':
                $type = isset($block['settings']['type']) ? $block['settings']['type'] : 'unordered';
                $items = isset($block['settings']['items']) ? $block['settings']['items'] : [];
                
                $tag = $type === 'ordered' ? 'ol' : 'ul';
                
                if (!empty($items)) {
                    $output = "<{$tag} class=\"imagine-list imagine-list-{$type}\">";
                    
                    foreach ($items as $item) {
                        $output .= '<li>' . wp_kses_post($item) . '</li>';
                    }
                    
                    $output .= "</{$tag}>";
                }
                break;
                
            case 'quote':
                $content = isset($block['content']) ? $block['content'] : '';
                $citation = isset($block['settings']['citation']) ? $block['settings']['citation'] : '';
                $style = isset($block['settings']['style']) ? $block['settings']['style'] : 'default';
                
                $output = '<blockquote class="imagine-quote imagine-quote-' . esc_attr($style) . '">';
                $output .= '<p>' . wp_kses_post($content) . '</p>';
                
                if (!empty($citation)) {
                    $output .= '<cite>' . esc_html($citation) . '</cite>';
                }
                
                $output .= '</blockquote>';
                break;
                
            case 'divider':
                $style = isset($block['settings']['style']) ? $block['settings']['style'] : 'solid';
                $width = isset($block['settings']['width']) ? $block['settings']['width'] : '100%';
                $height = isset($block['settings']['height']) ? $block['settings']['height'] : '1px';
                $color = isset($block['settings']['color']) ? $block['settings']['color'] : '#DDDDDD';
                
                $output = sprintf(
                    '<hr class="imagine-divider imagine-divider-%1$s" style="width: %2$s; height: %3$s; border: none; background-color: %4$s;" />',
                    esc_attr($style),
                    esc_attr($width),
                    esc_attr($height),
                    esc_attr($color)
                );
                break;
                
            case 'video':
                $url = isset($block['settings']['url']) ? $block['settings']['url'] : '';
                $embed_type = isset($block['settings']['embed_type']) ? $block['settings']['embed_type'] : 'youtube';
                $autoplay = isset($block['settings']['autoplay']) && $block['settings']['autoplay'] ? 'autoplay' : '';
                $muted = isset($block['settings']['muted']) && $block['settings']['muted'] ? 'muted' : '';
                $controls = !isset($block['settings']['controls']) || $block['settings']['controls'] ? 'controls' : '';
                
                if (!empty($url)) {
                    $output = '<div class="imagine-video imagine-video-' . esc_attr($embed_type) . '">';
                    
                    // Handle different video types
                    if ($embed_type === 'youtube' || $embed_type === 'vimeo') {
                        $output .= wp_oembed_get($url);
                    } else {
                        $output .= sprintf(
                            '<video %1$s %2$s %3$s width="100%%"><source src="%4$s" type="video/mp4"></video>',
                            $autoplay,
                            $muted,
                            $controls,
                            esc_url($url)
                        );
                    }
                    
                    $output .= '</div>';
                }
                break;
                
            default:
                // Try to handle custom block types
                $output = apply_filters('imagine_render_custom_block', '', $block);
                break;
        }
        
        return $output;
    }
    
    /**
     * Get block CSS classes for styling
     */
    public static function get_block_css_class($block) {
        if (empty($block['type'])) {
            return '';
        }
        
        $classes = ['imagine-block', 'imagine-block-' . $block['type']];
        
        // Add alignment if present
        if (!empty($block['settings']['align'])) {
            $classes[] = 'imagine-align-' . $block['settings']['align'];
        }
        
        // Add styling if present
        if (!empty($block['settings']['style'])) {
            $classes[] = 'imagine-style-' . $block['settings']['style'];
        }
        
        return implode(' ', $classes);
    }
    
    /**
     * Register frontend styles for blocks
     */
    public static function register_frontend_styles() {
        wp_register_style(
            'imagine-blocks',
            IMAGINE_URL . 'dist/assets/imagine-blocks.css',
            [],
            IMAGINE_VERSION
        );
        
        wp_enqueue_style('imagine-blocks');
    }
}

// Register frontend styles
add_action('wp_enqueue_scripts', array('Imagine_Blocks', 'register_frontend_styles'));
