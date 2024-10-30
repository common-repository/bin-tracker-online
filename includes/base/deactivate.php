<?php
/**
 * @package BinTrackerOnline
 */

namespace b1nT_includes\b1nT_base;

class B1nT_Deactivate {
    public static function b1nT_deactivate() {
        flush_rewrite_rules();
        self::b1nT_clean_database();
    }

    private static function b1nT_clean_database() {
        global $wpdb;
        $b1nT_table_name = $wpdb->prefix.'b1nT_states';

        $b1nT_table_exist = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($b1nT_table_name))); 
               
        if($b1nT_table_exist === $b1nT_table_name) {
            $wpdb->query($wpdb->prepare("DROP TABLE $b1nT_table_name"));
        }
    }
}