<?php
/**
 * This class will create an instance
 * of one or more classes, and then call
 * the b1nT_init method if it exist.
 * 
 * @package BinTrackerOnline
 */

namespace b1nT_includes;

use b1nT_includes\b1nT_base\B1nT_Enqueue;
use b1nT_includes\b1nT_gui\B1nT_Admin_Options;
use b1nT_includes\b1nT_base\B1nT_Plugin_Links;
use b1nT_includes\b1nT_gui\B1nT_Front_House;
use b1nT_includes\b1nT_base\B1nT_Session;
use b1nT_includes\b1nT_base\B1nT_Ajax_Control;

final class B1nT_Init {
    /**
     * Get the desired classes to get the
     * plugin working.
     * 
     * @return an array of classes 
     */
    public static function b1nT_get_classes () {
        $b1nT_array = array();

        array_push($b1nT_array, B1nT_Session::class);
        array_push($b1nT_array, B1nT_Enqueue::class);
        array_push($b1nT_array, B1nT_Ajax_Control::class);

        if(is_admin()) {
            array_push($b1nT_array, B1nT_Admin_Options::class);
            array_push($b1nT_array, B1nT_Plugin_Links::class);
        } else {
            array_push($b1nT_array, B1nT_Front_House::class);
        }

        return $b1nT_array;
    }

    /**
     * Iterate through an array of classes
     * instantiate them, and call b1nT_init.
     * 
     */
    public static function b1nT_init () {
        foreach (self::b1nT_get_classes() as $b1nT_class) {
            $b1nT_instance = self::b1nT_instantiate_class($b1nT_class);
            if(method_exists($b1nT_instance, 'b1nT_init')) {
                $b1nT_instance->b1nT_init();
            }
        }
    }

    /**
     * Instatiate the class
     * 
     * @return an instance of a class
     */
    private static function b1nT_instantiate_class($b1nT_class) {
        $b1nT_instance = new $b1nT_class(); 
        return $b1nT_instance;
    }
}