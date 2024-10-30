<?php
/**
 * This class will load up the
 * form for the front house page.
 * 
 * @package BinTrackerOnline
 */

namespace b1nT_includes\b1nT_gui;
use b1nT_includes\b1nT_base\B1nT_Global_Variables;

class B1nT_Front_House extends B1nT_Global_Variables {
    function b1nT_init() {
        add_shortcode('b1nT_bin-tracker-online', array($this, 'b1nT_load_plugin_form'));
    }

     /**
     * @var string $b1nT_password
     * @var string $b1nT_username
     * @return template or an error
     */
    function b1nT_load_plugin_form() {
        //lets make sure we have some credentials on file.
        $b1nT_username = sanitize_text_field(get_option('b1nT_username'));
        $b1nT_password = sanitize_text_field(get_option('b1nT_password'));
        
        if($b1nT_username == "" || $b1nT_password == "") {
            return __('Please contact to administrator, Invalid configuration.', 'bin-tracker-online');
        }

        if($this->b1nT_plugin_path) {
            if(file_exists($this->b1nT_plugin_path.'templates/front-house-step-one-template.php')) {
                require_once $this->b1nT_plugin_path.'templates/front-house-step-one-template.php'; 
            }

            if(file_exists($this->b1nT_plugin_path.'templates/front-house-step-two-template.php')) {
                require_once $this->b1nT_plugin_path.'templates/front-house-step-two-template.php';
            }

            if(file_exists($this->b1nT_plugin_path.'templates/front-house-step-three-template.php')) {
                require_once $this->b1nT_plugin_path.'templates/front-house-step-three-template.php';
            }
            
            if(file_exists($this->b1nT_plugin_path.'templates/front-house-step-four-template.php')) {
                require_once $this->b1nT_plugin_path.'templates/front-house-step-four-template.php';
            }
        }
    }
}