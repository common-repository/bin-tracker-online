<?php
/**
 * This class will create a session
 * when the plug in launches.
 * 
 * @package BinTrackerOnline
 */

namespace b1nT_includes\b1nT_base;

class B1nT_Session {
    function b1nT_init() {
        add_action('init', array($this, 'b1nT_session_start'));
    }

    //start session
    function b1nT_session_start() {
        if(!session_id()) {
            session_start();
        }
    }
}