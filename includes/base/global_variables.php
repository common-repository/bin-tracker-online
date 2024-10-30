<?php
/**
 * This class will set global variables
 * 
 * @package BinTrackerOnline
 */

namespace b1nT_includes\b1nT_base;

class B1nT_Global_Variables {
    public $b1nT_plugin;
    public $b1nT_plugin_path;
    public $b1nT_plugin_url;
    public $b1nT_admin_url;
    public $b1nT_boxT_url;

    /**
     * Set variables
     * 
     */
    function __construct() {
        $this->b1nT_plugin      = plugin_basename(dirname(__FILE__, 3)).'/bin-tracker-online.php';
        $this->b1nT_plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->b1nT_plugin_url  = plugin_dir_url(dirname(__FILE__, 2));
        $this->b1nT_admin_url   = admin_url();
        //$this->b1nT_boxT_url  = 'https://btdt.dev2.rocks/controller.html';
        $this->b1nT_boxT_url    = 'https://www.bintracker.software/controller.html';
    }
}