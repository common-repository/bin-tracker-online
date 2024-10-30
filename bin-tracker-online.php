<?php
/**
 * @package BinTrackerOnline
 */

/*
Plugin Name: Bin Tracker Online
Plugin URI: https://www.bintracker.software/api/word-press-plugin.html
Description: The plug in provides integration with a private web application for Bin their Dump That, a franchisor in the waste hauling industry 
Version: 1.0.1
Author: Cairn Applications Inc
Author URI: https://www.cloud-computing.rocks/
License: GPLv2 or later
Text Domain: bin-tracker-online
*/

//security protocols
if(!defined('ABSPATH')) { die; }
if(!function_exists('add_action')) { die; }

//include some classes, these classes will be used to implement namespaces
if(file_exists(plugin_dir_path(__FILE__).'includes/base/required-paths.php')) {
    require_once plugin_dir_path(__FILE__).'includes/base/required-paths.php';
}

if(class_exists('b1nT_includes\b1nT_base\B1nT_Required_Paths')) {
    $b1nT_required_paths = new b1nT_includes\b1nT_base\B1nT_Required_Paths(plugin_dir_path(__FILE__));
    foreach($b1nT_required_paths->b1nT_get_paths() as $b1nT_path) {
        require_once $b1nT_path;
    }
}

//flush and create tables
function b1nT_activate_flush() {
    if(class_exists('b1nT_includes\b1nT_base\B1nT_Activate')) {
        b1nT_includes\b1nT_base\B1nT_Activate::b1nT_activate();
    }
}

//flushes and drop tables
function b1nT_deactivate_flush() {
    if(class_exists('b1nT_includes\b1nT_base\B1nT_Deactivate')) {
        b1nT_includes\b1nT_base\B1nT_Deactivate::b1nT_deactivate();
    }
}

register_activation_hook(__FILE__, 'b1nT_activate_flush');
register_deactivation_hook(__FILE__, 'b1nT_deactivate_flush');

//initialize some classes 
if(class_exists('b1nT_includes\B1nT_Init')) {
    b1nT_includes\B1nT_Init::b1nT_init();
}
