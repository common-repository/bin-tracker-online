<?php
/**
 * This class will create an admin
 * setting page on the dashboard.
 * 
 * @package BinTrackerOnline
 */

namespace b1nT_includes\b1nT_gui;

use b1nT_includes\b1nT_base\B1nT_Global_Variables;
use b1nT_includes\b1nT_callbacks\B1nT_Admin_Options_Callbacks;

class B1nT_Admin_Options extends B1nT_Global_Variables {
    public $b1nT_admin_options_callbacks;

    function b1nT_init() {
        if(class_exists('b1nT_includes\b1nT_callbacks\B1nT_Admin_Options_Callbacks')) {
            $this->b1nT_admin_options_callbacks = new B1nT_Admin_Options_Callbacks();
            add_action('admin_menu', array($this, 'b1nT_admin_menu_page'));
            add_action('admin_init', array($this, 'b1nT_admin_options_settings'));
        }
    }

    function b1nT_admin_menu_page() {
        $b1nT_imgage_icon = '';
        if($this->b1nT_plugin_url && wp_http_validate_url($this->b1nT_plugin_url)) {
            $b1nT_imgage_icon = $this->b1nT_plugin_url.'images/icon.png';
        }

        add_menu_page('Bin Tracker Online', 'Bin Tracker', 'administrator', 'bin-tracker-online', method_exists($this->b1nT_admin_options_callbacks, 'b1nT_admin_options_template') ? array($this->b1nT_admin_options_callbacks, 'b1nT_admin_options_template') : '', $b1nT_imgage_icon, 101);
    }

    function b1nT_admin_options_settings() {
        register_setting('b1nT_setting_group', 'b1nT_username', method_exists($this->b1nT_admin_options_callbacks, 'b1nT_validate_username') ? array($this->b1nT_admin_options_callbacks, 'b1nT_validate_username') : '');
        register_setting('b1nT_setting_group', 'b1nT_password', method_exists($this->b1nT_admin_options_callbacks, 'b1nT_validate_password') ? array($this->b1nT_admin_options_callbacks, 'b1nT_validate_password') : '');
        register_setting('b1nT_setting_group', 'b1nT_admin_country', method_exists($this->b1nT_admin_options_callbacks, 'b1nT_validate_admin_country') ? array($this->b1nT_admin_options_callbacks, 'b1nT_validate_admin_country') : '');
        register_setting('b1nT_setting_group', 'b1nT_google_api_key', method_exists($this->b1nT_admin_options_callbacks, 'b1nT_validate_google_api_key') ? array($this->b1nT_admin_options_callbacks, 'b1nT_validate_google_api_key') : '');
        register_setting('b1nT_setting_group', 'b1nT_payment_info', method_exists($this->b1nT_admin_options_callbacks, 'b1nT_validate_payment_info') ? array($this->b1nT_admin_options_callbacks, 'b1nT_validate_payment_info') : '');
        register_setting('b1nT_setting_group', 'b1nT_mode', method_exists($this->b1nT_admin_options_callbacks, 'b1nT_validate_mode') ? array($this->b1nT_admin_options_callbacks, 'b1nT_validate_mode') : '');
        register_setting('b1nT_setting_group', 'b1nT_admin_debug', method_exists($this->b1nT_admin_options_callbacks, 'b1nT_validate_admin_debug') ? array($this->b1nT_admin_options_callbacks, 'b1nT_validate_admin_debug') : '');
        register_setting('b1nT_setting_group', 'b1nT_zipcode_label', '');
        register_setting('b1nT_setting_group', 'b1nT_page_title', '');
        register_setting('b1nT_setting_group', 'b1nT_google_validation_bypass', method_exists($this->b1nT_admin_options_callbacks, 'b1nT_validate_bypass_google_validation') ? array($this->b1nT_admin_options_callbacks, 'b1nT_validate_bypass_google_validation') : '');
    }
}