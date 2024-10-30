<?php
/**
 * This class will handle server calls
 * 
 * @package BinTrackerOnline
 */

namespace b1nT_includes\b1nT_base;

use b1nT_includes\b1nT_base\B1nT_Global_Variables;

class B1nT_Server_Calls extends B1nT_Global_Variables {

    /**
     * Validate user information
     * 
     * @return array
     */
    function b1nT_handshake(){
        $b1nT_username = sanitize_text_field(get_option('b1nT_username'));
        $b1nT_password = sanitize_text_field(get_option('b1nT_password'));

        $b1nT_user_credentials = array( 
            'command'   => 'cmdBinTWebAPIHandShake',
            'username'  => $b1nT_username,
            'password'  => $b1nT_password 
        );

        $b1nT_user_credentials_query_string = http_build_query($b1nT_user_credentials);
        $b1nT_response                      = wp_remote_post($this->b1nT_boxT_url, array('body' => $b1nT_user_credentials_query_string));
        return $this->b1nT_sanitize_reponse(json_decode($b1nT_response['body'])); //b1nT_sanitize_reponse() sanitizes values
    }

    /**
     * Get price sheet data
     * 
     * @param string $b1nT_key
     * @param string $user_zipcode
     * @return array
     */
    function b1nT_pricing_query($b1nT_key, $b1nT_user_zipcode) {
        $b1nT_username     = sanitize_text_field(get_option('b1nT_username'));
        $b1nT_key          = sanitize_text_field($b1nT_key);
        $b1nT_user_zipcode = sanitize_text_field($b1nT_user_zipcode);

        $b1nT_user_pricingquery  = array(
            'command'       => 'cmdBinTWebAPIPricing',
            'username'      => $b1nT_username,
            'key'           => $b1nT_key,
            'zipPostalCode' => $b1nT_user_zipcode
        ); 

        $b1nT_user_pricingquery_query_string = http_build_query($b1nT_user_pricingquery);
        $b1nT_response                       = wp_remote_post($this->b1nT_boxT_url, array('body' => $b1nT_user_pricingquery_query_string));
        return $this->b1nT_sanitize_reponse(json_decode($b1nT_response['body'])); //b1nT_sanitize_reponse() sanitizes values
    }

    /**
     * Request service
     * 
     * @param string $b1nT_data
     * @return array
     */
    function b1nT_place_order($b1nT_data){
        if(!is_array($b1nT_data)) { return null; }
        $b1nT_data     = $this->b1nT_sanitize_array_values($b1nT_data); //b1nT_sanitize_array_values() sanitizes values
        $b1nT_username = sanitize_text_field(get_option('b1nT_username'));

        $b1nT_user_placeoder = array( 
            'command'   => 'cmdBinTWebAPIDirectBooking',
            'username'  => $b1nT_username
        );

        $b1nT_user_placeoder              = array_merge($b1nT_user_placeoder, $b1nT_data);
        $b1nT_user_placeoder_query_string = http_build_query($b1nT_user_placeoder);
        $b1nT_response                    = wp_remote_post($this->b1nT_boxT_url, array('body' => $b1nT_user_placeoder_query_string));
        return $this->b1nT_sanitize_reponse(json_decode($b1nT_response['body'])); //b1nT_sanitize_reponse() sanitizes values
    }

    /**
     * Validate the credit card number
     * 
     * @param string $b1nT_number
     * @return true or false
     */
    function b1nT_credit_card_number_valid($b1nT_number) {
        //Remove non-digits from the number
        $b1nT_number = preg_replace('/[^0-9]/', '', $b1nT_number);
 
        //Get the string length and parity
        $b1nT_number_length = strlen($b1nT_number);
        if($b1nT_number_length == 0){
            return false;
        }

        $b1nT_parity = $b1nT_number_length % 2;
        
        //Split up the number into sin-
        //gle digits and get the total
        $b1nT_total = 0;
        for ($i = 0; $i < $b1nT_number_length; $i++) { 
            $b1nT_digit = $b1nT_number[$i];

            //Multiply alterna-
            //te digits by two
            if ($i % 2 == $b1nT_parity) {
                $b1nT_digit *= 2;

                //If the sum is two dig- 
                //its,  add them together
                if ($b1nT_digit > 9) {
                    $b1nT_digit -= 9;
                }       
            }       
            //Sum up the digits
            $b1nT_total += $b1nT_digit;
        }

        //If the total mod 10 equ-
        //als 0, the number is valid
        return ($b1nT_total % 10 == 0) ? TRUE : FALSE;
    }

    /**
     * Request driving distance from google
     * 
     * @param string $b1nT_lat1
     * @param string $b1nT_long1
     * @param string $b1nT_lat2
     * @param string $b1nT_long2
     * @return array
     */
    function b1nT_get_driving_distance($b1nT_lat1, $b1nT_long1, $b1nT_lat2, $b1nT_long2) {
        $b1nT_lat1           = sanitize_text_field($b1nT_lat1);
        $b1nT_long1          = sanitize_text_field($b1nT_long1);
        $b1nT_lat2           = sanitize_text_field($b1nT_lat2);
        $b1nT_long2          = sanitize_text_field($b1nT_long2);
        $b1nT_country_id     = sanitize_text_field(get_option('b1nT_admin_country'));
        $b1nT_google_api_key = sanitize_text_field(get_option('b1nT_google_api_key'));
        $b1nT_units = ($b1nT_country_id == "Canada" ? "metric" : "imperial");

        $b1nT_url = "https://maps.googleapis.com/maps/api/distancematrix/json";

        $b1nT_url_arg = array( 
            'units'        => $b1nT_units,
            'origins'      => $b1nT_lat1.','.$b1nT_long1,
            'destinations' => $b1nT_lat2.','.$b1nT_long2,
            'mode'         => 'driving',
            'key'          => $b1nT_google_api_key
        );

        $b1nT_url_arg_query_string = http_build_query($b1nT_url_arg);
        $b1nT_url_new              = $b1nT_url."?".$b1nT_url_arg_query_string;     
        $b1nT_response             = wp_remote_post($b1nT_url_new, array('body' => $b1nT_url_arg_query_string));
        $b1nT_response             = $this->b1nT_sanitize_reponse(json_decode($b1nT_response['body'])); //b1nT_sanitize_reponse() sanitizes values

        $b1nT_error_message = isset($b1nT_response->error_message) ? $b1nT_response->error_message : "";
                    
        if($b1nT_error_message != ""){
            return array('b1nT_error_message' => $b1nT_error_message);
        }
        
        $b1nT_dist = $b1nT_response->rows[0]->elements[0]->distance->text;
        $b1nT_time = $b1nT_response->rows[0]->elements[0]->duration->text;
                  
        if($b1nT_dist == "" || $b1nT_time == ""){
            return array('b1nT_error_message' => 'Latitude and longitude invalid.');
        }

        return array('b1nT_distance' => $b1nT_dist, 'b1nT_time' => $b1nT_time, 'b1nT_response' => $b1nT_response); 
    }

    /**
     * Request lat lng from google
     * 
     * @param string $b1nT_address
     * @return array
     */
    function b1nT_get_latitude_longitude($b1nT_address) {
        $b1nT_address        = sanitize_text_field($b1nT_address);
        $b1nT_google_api_key = sanitize_text_field(get_option('b1nT_google_api_key'));

        $b1nT_url = "https://maps.google.com/maps/api/geocode/json";

        $b1nT_url_arg = array( 
            'address' => $b1nT_address,
            'sensor'  => 'false',
            'key'     => $b1nT_google_api_key
        );

        $b1nT_url_arg_query_string = http_build_query($b1nT_url_arg);
        $b1nT_url_arg_query_string = str_replace("%2","+",$b1nT_url_arg_query_string);
        $b1nT_url_new              = $b1nT_url."?".$b1nT_url_arg_query_string;
        $b1nT_response             = wp_remote_post($b1nT_url_new, array('body' => $b1nT_url_arg_query_string));
        $b1nT_response             = $this->b1nT_sanitize_reponse(json_decode($b1nT_response['body'])); //b1nT_sanitize_reponse() sanitizes values

        $b1nT_error_message = isset($b1nT_response->error_message) ? $b1nT_response->error_message : "";
                    
        if($b1nT_error_message != ""){
            return array('b1nT_error_message' => $b1nT_error_message);
        }
                    
        if(empty($b1nT_response->results)) {
            return array('b1nT_error_message' =>'Please enter valid address.');
        }
        
        $b1nT_formatted_address = $b1nT_response->results[0]->formatted_address;
        $b1nT_lat               = $b1nT_response->results[0]->geometry->location->lat;
        $b1nT_long              = $b1nT_response->results[0]->geometry->location->lng; 
        return array('b1nT_lat' => $b1nT_lat, 'b1nT_long' => $b1nT_long, 'b1nT_formatted_address' => $b1nT_formatted_address);
    }

    /**
     * Request lat, lng from google based state
     * 
     * @param string $b1nT_lat
     * @param string $b1nT_long
     * @return array
     */
    function b1nT_get_state_by_latitude_longitude($b1nT_lat, $b1nT_long) {
        $b1nT_lat            = sanitize_text_field($b1nT_lat);
        $b1nT_long           = sanitize_text_field($b1nT_long);
        $b1nT_google_api_key = sanitize_text_field(get_option('b1nT_google_api_key'));

        $b1nT_url = "https://maps.googleapis.com/maps/api/geocode/json";

        $b1nT_url_arg = array(
            'latlng'  => $b1nT_lat.','.$b1nT_long,
            'sensor'  => 'false',  
            'key'     => $b1nT_google_api_key
        );

        $b1nT_url_arg_query_string = http_build_query($b1nT_url_arg);
        $b1nT_url_new              = $b1nT_url."?".$b1nT_url_arg_query_string;
        $b1nT_response             = wp_remote_post($b1nT_url_new, array('body' => $b1nT_url_arg_query_string));
        $b1nT_response             = $this->b1nT_sanitize_reponse(json_decode($b1nT_response['body'])); //b1nT_sanitize_reponse() sanitizes values

        $b1nT_error_message = isset($b1nT_response->error_message) ? $b1nT_response->error_message : "";
            
        if($b1nT_error_message != ""){
            return array('b1nT_error_message' => $b1nT_error_message);
        }

        $b1nT_long_name  = $b1nT_response->results[0]->address_components[4]->long_name;
        $b1nT_short_name = $b1nT_response->results[0]->address_components[4]->short_name;
        return array('b1nT_long_name' => $b1nT_long_name, 'b1nT_short_name' => $b1nT_short_name);
    }

    /**
     * Find date requested in availability table
     * 
     * @param array $b1nT_availability_rows
     * @param string $b1nT_date_requested
     * @var string $b1nT_v
     * @return array
     */
    function b1nT_search_availability_table($b1nT_availability_rows, $b1nT_date_requested) {
        if(!is_array($b1nT_availability_rows)) { return null; }
        $b1nT_availability_rows = $this->b1nT_sanitize_array_values($b1nT_availability_rows); //b1nT_sanitize_array_values() sanitizes values
        $b1nT_date_requested    = sanitize_text_field($b1nT_date_requested);

        $b1nT_result = array();
        foreach ($b1nT_availability_rows as $b1nT_row_value) {
            if($b1nT_row_value[0] == $b1nT_date_requested) {
                $b1nT_result = $b1nT_row_value;
                break;  
            }
        }

        return $b1nT_result;
    }

    /**
     * Get a list of states
     * 
     * @param string $b1nT_country_id
     * @return string
     */
    function b1nT_get_states($b1nT_country_id) {
        $b1nT_country_id = sanitize_text_field($b1nT_country_id);

        global $wpdb;
        if($b1nT_country_id == "Canada"){
             $b1nT_country_id = 'CAN';
        } else {
             $b1nT_country_id = 'USA';
        }

        $b1nT_table_name = $wpdb->prefix."b1nT_states";
        $b1nT_output = $wpdb->get_results($wpdb->prepare("SELECT * FROM $b1nT_table_name WHERE country_id = %s", $b1nT_country_id));

        if(!is_array($b1nT_output)) { return null; }
        return $this->b1nT_sanitize_array_values($b1nT_output); //b1nT_sanitize_array_values() sanitizes values
    }

    /**
     * Get state name
     * 
     * @param string $b1nT_state_code
     * @return string
     */
    function b1nT_get_state_name($b1nT_state_code) {
        $b1nT_state_code = sanitize_text_field($b1nT_state_code);

        global $wpdb;
        $b1nT_table_name = $wpdb->prefix."b1nT_states";
        $b1nT_output = $wpdb->get_row($wpdb->prepare("SELECT * FROM $b1nT_table_name WHERE state_short = %s", $b1nT_state_code));

        if(!is_object($b1nT_output)) { return null; }
        return $this->b1nT_sanitize_array_values($b1nT_output); //b1nT_sanitize_array_values() sanitizes values
    }

   /**
    * Sanitize array
    * 
    * @param  array $b1nT_array
    * @return sanitized array
    */
    function b1nT_sanitize_array_values($b1nT_array) {
        //if not an array dont proceed.
        if(!is_array($b1nT_array)) { return $b1nT_array; }

        foreach ($b1nT_array as $b1nT_key => &$b1nT_value) {
            if(is_array($b1nT_value)) {
                $b1nT_value = $this->b1nT_sanitize_array_values($b1nT_value);
            } else {
                if(is_object($b1nT_value)) {
                    $b1nT_value = $this->b1nT_sanitize_object_values($b1nT_value);
                } else {
                    $b1nT_value = $this->b1nT_sanitize_string_values($b1nT_value);
                }
            }
        }

        return $b1nT_array;
    }

   /**
    * Sanitize objects
    * 
    * @param  object $b1nT_object
    * @return sanitized object
    */
    function b1nT_sanitize_object_values($b1nT_object) {
        //if not an object dont proceed.
        if(!is_object($b1nT_object)) { return $b1nT_object; }

        foreach ($b1nT_object as $b1nT_key => &$b1nT_value) {
            if(is_object($b1nT_value)) {
                $b1nT_value = $this->b1nT_sanitize_object_values($b1nT_value);
            } else {
                if(is_array($b1nT_value)) {
                    $b1nT_value = $this->b1nT_sanitize_array_values($b1nT_value);
                } else {
                    $b1nT_value = $this->b1nT_sanitize_string_values($b1nT_value);
                }
            }
        }

        return $b1nT_object;
    }

   /**
    * Sanitize json
    * 
    * @param  object $b1nT_data
    * @return sanitized $b1nT_data
    */
    function b1nT_sanitize_string_values($b1nT_data) {
        $b1nT_data_temp = json_decode($b1nT_data);
        if($b1nT_data_temp && is_array($b1nT_data_temp)) {
            return $this->b1nT_sanitize_array_values($b1nT_data_temp);
        } 

        if($b1nT_data_temp && is_object($b1nT_data_temp)) {
            return $this->b1nT_sanitize_object_values($b1nT_data_temp);
        }

        if($b1nT_data == null) {
            return "";
        }

        return sanitize_text_field($b1nT_data);
    }

    /**
     * Server calls sanitize
     * 
     * @param array/object $b1nT_data
     * @return array/object $b1nT_data
     */
    function b1nT_sanitize_reponse($b1nT_data) {
        if(is_array($b1nT_data)) {
            return $this->b1nT_sanitize_array_values($b1nT_data); 
        }

        if(is_object($b1nT_data)) {
            return $this->b1nT_sanitize_object_values($b1nT_data);
        }

        return (object) array(
            'status'      => '511',
            'errorString' => 'Failed to sanitize data'
        );
    }
}