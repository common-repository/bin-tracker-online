<?php
/**
 * This class will load my
 * scripts and my css files.
 * 
 * @package BinTrackerOnline
 */

namespace b1nT_includes\b1nT_base;

use b1nT_includes\b1nT_base\B1nT_Global_Variables;

class B1nT_Enqueue extends B1nT_Global_Variables {
    function b1nT_init() {
        if($this->b1nT_plugin_url && wp_http_validate_url($this->b1nT_plugin_url)) {
            add_action('admin_enqueue_scripts', array($this, 'b1nT_admin_enqueue'));
            add_action('wp_enqueue_scripts',    array($this, 'b1nT_wp_enqueue'));
        }
    }

    //Admin page
    function b1nT_admin_enqueue() {
        wp_enqueue_style('b1nT-admin-options-style', $this->b1nT_plugin_url.'styles/admin-options-styles.css');
    }

    //Front end page
    function b1nT_wp_enqueue() {
        global $post; //we need to know if the short code exist before we load scripts/css
        if(is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'b1nT_bin-tracker-online')) {
            wp_enqueue_style('b1nT-dialog-box-style',  $this->b1nT_plugin_url.'styles/dialog-box-styles.css', '', '', 'all');
            wp_enqueue_style('b1nT-front-house-style', $this->b1nT_plugin_url.'styles/front-house-styles.css', '', '', 'all');

            //inclided css locally; word press does not seem to load the jquery date picker supporting css
            wp_enqueue_style('b1nT-jquery-ui-style', $this->b1nT_plugin_url.'styles/"jquery-ui.css', '', '', 'all');

            $b1nT_google_api_key = esc_attr(get_option('b1nT_google_api_key'));
            wp_enqueue_script('b1nT-front-house-script',        $this->b1nT_plugin_url.'javascript/front-house-script.js', array('jquery'), '', 'all');
            wp_enqueue_script('b1nT-address-validation-script', $this->b1nT_plugin_url.'javascript/address-validation-script.js', '', '', 'all');
            wp_enqueue_script('b1nT-dialog-box-script',         $this->b1nT_plugin_url.'javascript/dialog-box-script.js', '', '', 'all');
            wp_enqueue_script('jquery-ui-datepicker');

            //google places api, provides a service.
            wp_enqueue_script('b1nT-google-maps', "https://maps.googleapis.com/maps/api/js?key=$b1nT_google_api_key&libraries=places", '', '', 'all');

            //localize
            $this->b1nT_front_house_script_localize();
        }
    }

    /**
     * Create the nonce and the url needed for the server calls, and
     * localize the information to make it accessible on the script
     * 
     */
    private function b1nT_front_house_script_localize() {
        if($this->b1nT_admin_url && wp_http_validate_url($this->b1nT_admin_url)) {
            $b1nT_config = array(
                 'ajax_url'   => $this->b1nT_admin_url."admin-ajax.php",
                 'ajax_nonce' => wp_create_nonce('_check__ajax_100'));

            wp_localize_script('b1nT-front-house-script', 'b1nT_config', $b1nT_config);
        }
    }
}