<?php
/**
 * This class will ad links that will
 * direct us to the setting page.
 * 
 * @package BinTrackerOnline
 */

namespace b1nT_includes\b1nT_base;

use b1nT_includes\b1nT_base\B1nT_Global_Variables;

class B1nT_Plugin_Links extends B1nT_Global_Variables {
    function b1nT_init() {
        if($this->b1nT_plugin) {
            add_filter('plugin_action_links_'.$this->b1nT_plugin, array($this, 'b1nT_add_links'));
        }
    }

    /**
     * This function will generate a set
     * of links that we can display.
     * 
     * @param array $b1nT_links
     * @return an array of links
     */
    function b1nT_add_links($b1nT_links) {
        if($this->b1nT_admin_url && wp_http_validate_url($this->b1nT_admin_url)) {
            $b1nT_link = '<a href="'.$this->b1nT_admin_url.'admin.php?page=bin-tracker-online">Settings</a>';
            array_push($b1nT_links, $b1nT_link);
        }
        return $b1nT_links;
    }
}