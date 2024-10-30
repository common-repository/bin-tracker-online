<?php
/**
 * Dynamiclly handle callbacks for 
 * the amdmin settings api class.
 * 
 * @package BinTrackerOnline
 */

namespace b1nT_includes\b1nT_callbacks;

use b1nT_includes\b1nT_base\B1nT_Global_Variables;

class B1nT_Admin_Options_Callbacks extends B1nT_Global_Variables {
    function b1nT_admin_options_template() { 
        if($this->b1nT_plugin_path && file_exists($this->b1nT_plugin_path.'templates/admin-options-template.php')) {      
            return require_once $this->b1nT_plugin_path.'templates/admin-options-template.php';
        }
    }

    /**
     * Validates the the input and
     * throws an error if its blank
     * 
     * @param string $b1nT_input
     * @return string $b1nT_input
     */
    function b1nT_validate_username($b1nT_input) {
        //sanitize
        $b1nT_input = sanitize_text_field($b1nT_input);

        //validate
        if($b1nT_input == ""){
            add_settings_error('b1nT_username', 'b1nT_username', __('Please enter a username', 'bin-tracker-online'), 'error');
            $b1nT_input = sanitize_text_field(get_option('b1nT_username'));
        }  

        return $b1nT_input;
    }

    /**
     * Validates the the input and
     * throws an error if its blank
     * 
     * @param string $b1nT_input
     * @return string $b1nT_input
     */
    function b1nT_validate_password($b1nT_input) {
        //sanitize
        $b1nT_input = sanitize_text_field($b1nT_input);

        //validate
        if($b1nT_input == ""){
            add_settings_error('b1nT_password', 'b1nT_password', __('Please enter a password', 'bin-tracker-online'), 'error');
            $b1nT_input = sanitize_text_field(get_option('b1nT_password'));
        }

        return $b1nT_input;
    }

    /**
     * Validates the the input and
     * throws an error if its blank
     * 
     * @param string $b1nT_input
     * @return string $b1nT_input
     */
    function b1nT_validate_admin_country($b1nT_input) {
        //sanitize
        $b1nT_input = sanitize_text_field($b1nT_input);

        //validate
        if(!($b1nT_input == "United States" || $b1nT_input == "Canada")) {
            add_settings_error('b1nT_admin_country', 'b1nT_admin_country', __('Please select a country', 'bin-tracker-online'), 'error');
            $b1nT_input = sanitize_text_field(get_option('b1nT_admin_country'));
        }   

        return $b1nT_input;
    }

    /**
     * Validates the the input and
     * throws an error if its blank
     * 
     * @param string $b1nT_input
     * @return string $b1nT_input
     */
    function b1nT_validate_google_api_key($b1nT_input) {
        //sanitize
        $b1nT_input = sanitize_text_field($b1nT_input);

        //validate
        if($b1nT_input == ""){
            add_settings_error('b1nT_google_api_key', 'b1nT_google_api_key', __('Please enter a Google API Key', 'bin-tracker-online'), 'error');
            $b1nT_input = sanitize_text_field(get_option('b1nT_google_api_key'));
        }   

        return $b1nT_input;
    }

    /**
     * Validates the the input and
     * throws an error if its blank
     * 
     * @param string $b1nT_input
     * @return string $b1nT_input
     */
    function b1nT_validate_payment_info($b1nT_input) {
        //sanitize
        $b1nT_input = sanitize_text_field($b1nT_input);

        //validate
        if(!($b1nT_input == "Show" || $b1nT_input == "Hide")){
            add_settings_error('b1nT_payment_info', 'b1nT_payment_info', __('Please select to show/hide payment info.', 'bin-tracker-online'), 'error');
            $b1nT_input = sanitize_text_field(get_option('b1nT_payment_info'));
        } 

        return $b1nT_input;
    }

    /**
     * Validates the the input and
     * throws an error if its blank
     * 
     * @param string $b1nT_input
     * @return string $b1nT_input
     */
    function b1nT_validate_mode($b1nT_input) {
        //sanitize
        $b1nT_input = sanitize_text_field($b1nT_input);

        //validate
        if(!($b1nT_input == "TEST" || $b1nT_input == "LIVE")){
            add_settings_error('b1nT_mode', 'b1nT_mode', __('Please select a Test/Live mode', 'bin-tracker-online'), 'error');
            $b1nT_input = sanitize_text_field(get_option('b1nT_mode'));
        }  

        return $b1nT_input;
    }

    /**
     * Validates the the input and
     * throws an error if its blank
     * 
     * @param string $b1nT_input
     * @return string $b1nT_input
     */
    function b1nT_validate_admin_debug($b1nT_input) {
        //sanitize
        $b1nT_input = sanitize_text_field($b1nT_input);

        if(!($b1nT_input == "On" || $b1nT_input == "Off")){
            add_settings_error('b1nT_admin_debug', 'b1nT_admin_debug', __('Please select a Debug', 'bin-tracker-online'), 'error');
            $b1nT_input = sanitize_text_field(get_option('b1nT_admin_debug'));
        }  

        return $b1nT_input;
    }

    /**
     * Validates the the input and
     * throws an error if its blank
     * 
     * @param string $b1nT_input
     * @return string $b1nT_input
     */
    function b1nT_validate_bypass_google_validation($b1nT_input) {
        //sanitize
        $b1nT_input = sanitize_text_field($b1nT_input);

        if(!($b1nT_input == "On" || $b1nT_input == "Off")){
            add_settings_error('b1nT_google_validation_bypass', 'b1nT_google_validation_bypass', __('Bypass Google Validation must be equal to On/Off', 'bin-tracker-online'), 'error');
            $b1nT_input = sanitize_text_field(get_option('b1nT_google_validation_bypass'));
        }  

        return $b1nT_input;
    }
}