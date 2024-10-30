<?php
/**
 * @package BinTrackerOnline
 */

//Some security protocols
if(!defined('WP_UNINSTALL_PLUGIN')) { die; }

//now delete the options.
 delete_option('b1nT_page_title');
 delete_option('b1nT_username');
 delete_option('b1nT_password');
 delete_option('b1nT_zipcode_label');
 delete_option('b1nT_admin_country');
 delete_option('b1nT_google_api_key');
 delete_option('b1nT_payment_info');
 delete_option('b1nT_mode');
 delete_option('b1nT_api_mode');
 delete_option('b1nT_admin_debug');
 delete_option('b1nT_google_validation_bypass');