<?php
/**
 * Primary asset declarations for Imagine Editor
 * 
 * This file defines the script dependencies and versioning for the Imagine editor.
 * It is the definitive source for asset dependencies in the plugin and is always
 * loaded regardless of build status.
 */

if (!defined('ABSPATH')) {
    exit;
}

return array(
    'dependencies' => array(
        'wp-element',
        'wp-components',
        'wp-blocks',
        'wp-i18n',
        'wp-api-fetch',
        'wp-editor',
        'wp-hooks',
        'jquery'
    ),
    'version' => defined('IMAGINE_VERSION') ? IMAGINE_VERSION : '1.0.0'
);
