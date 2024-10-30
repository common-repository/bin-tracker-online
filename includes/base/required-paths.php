<?php
/**
 * generates a list of paths that we
 * can use to make all our includes
 * 
 * @package BinTrackerOnline
 */

namespace b1nT_includes\b1nT_base;

class B1nT_Required_Paths {
    public $b1nT_plugin_path;

    /**
     * @param string $b1nT_path
     */
    function __construct(string $b1nT_path) {
        $b1nT_path = sanitize_text_field($b1nT_path);
        $this->b1nT_plugin_path = $b1nT_path;
    }

    /**
     * Get a list of paths
     * 
     * @return an array of paths
     */
    function b1nT_get_paths() {
        $b1nT_list_of_paths = array();
        if($this->b1nT_plugin_path) {
            if(file_exists($this->b1nT_plugin_path.'includes/base/session.php')) {
                array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/base/session.php');
            }
            if(file_exists($this->b1nT_plugin_path.'includes/init.php')) {
                array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/init.php');
            }
            if(file_exists($this->b1nT_plugin_path.'includes/base/global_variables.php')) {
                array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/base/global_variables.php');
            }
            if(file_exists($this->b1nT_plugin_path.'includes/base/enqueue.php')) {
                array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/base/enqueue.php');
            }
            if(file_exists($this->b1nT_plugin_path.'includes/base/ajax-control.php')) {
                array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/base/ajax-control.php');
            }
            if(file_exists($this->b1nT_plugin_path.'includes/base/server-calls.php')) {
                array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/base/server-calls.php');
            }

            if(is_admin()) {
                if(file_exists($this->b1nT_plugin_path.'includes/base/activate.php')) {
                    array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/base/activate.php');
                }
                if(file_exists($this->b1nT_plugin_path.'includes/base/deactivate.php')) {
                    array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/base/deactivate.php');
                }
                if(file_exists($this->b1nT_plugin_path.'includes/gui/admin-options.php')) {
                array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/gui/admin-options.php');
                }
                if(file_exists($this->b1nT_plugin_path.'includes/callbacks/admin-options-callbacks.php')) {
                    array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/callbacks/admin-options-callbacks.php');
                }   
                if(file_exists($this->b1nT_plugin_path.'includes/base/plugin-links.php')) {       
                    array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/base/plugin-links.php');
                }
            } else {
                if(file_exists($this->b1nT_plugin_path.'includes/gui/front-house.php')) {
                    array_push($b1nT_list_of_paths, $this->b1nT_plugin_path.'includes/gui/front-house.php');
                }
            }
        }
        return $b1nT_list_of_paths;
    }
}