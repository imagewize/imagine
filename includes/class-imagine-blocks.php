<?php

if (!defined('ABSPATH')) {
    exit;
}

class Imagine_Blocks {
    public static function get_available_blocks() {
        return [
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
            ]
            // More blocks can be added here
        ];
    }
    
    public static function register_blocks() {
        // This method will be used when registering blocks with WordPress Block API if needed
    }
}
