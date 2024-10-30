<?php
/**
 * This class will mange the ajax calls
 * 
 * @package BinTrackerOnline
 */

namespace b1nT_includes\b1nT_base;

use b1nT_includes\b1nT_base\B1nT_Server_Calls;
use \Datetime;

class B1nT_Ajax_Control {
    public $b1nT_server_calls;

    function b1nT_init() {    
        $this->b1nT_server_calls = new B1nT_Server_Calls();
        add_action('wp_ajax_b1nT_state_list', array($this, 'b1nT_state_list'));
        add_action('wp_ajax_nopriv_b1nT_state_list', array($this, 'b1nT_state_list'));

        add_action('wp_ajax_b1nT_get_job', array($this, 'b1nT_get_job'));
        add_action('wp_ajax_nopriv_b1nT_get_job', array($this, 'b1nT_get_job'));

        add_action('wp_ajax_b1nT_availability_search', array($this, 'b1nT_availability_search'));
        add_action('wp_ajax_nopriv_b1nT_availability_search', array($this, 'b1nT_availability_search'));

        add_action('wp_ajax_b1nT_about_job', array($this, 'b1nT_about_job'));
        add_action('wp_ajax_nopriv_b1nT_about_job', array($this, 'b1nT_about_job'));

        add_action('wp_ajax_b1nT_job_details', array($this, 'b1nT_job_details'));
        add_action('wp_ajax_nopriv_b1nT_job_details', array($this, 'b1nT_job_details'));

        add_action('wp_ajax_b1nT_place_order', array($this, 'b1nT_place_order'));
        add_action('wp_ajax_nopriv_b1nT_place_order', array($this, 'b1nT_place_order'));
    }

    /**
     * Get the state list from the wordpress data base
     * 
     */ 
    function b1nT_state_list() {
        //security checks
        if(!$this->b1nT_security_checks()) {
            wp_send_json($this->b1nT_response('error', 'Something went wrong', ''));
        }

        //list of states that match the country and then create state list
        $b1nT_ss_billing_country = sanitize_text_field(get_option('b1nT_admin_country'));
        $b1nT_states             = $this->b1nT_server_calls->b1nT_get_states($b1nT_ss_billing_country); //b1nT_get_states() sanitizes values
        $b1nT_state_list         = '<option value="">--Select State--</option>';

        if(is_array($b1nT_states)) {
            foreach($b1nT_states as $b1nT_state){
                $b1nT_state_list .= '<option value="'.$b1nT_state->state_short.'">'.$b1nT_state->state_name.'</option>';
            }
        }

        //return array
        $job_state_list_data = array(
            'b1nT_state_list' => $b1nT_state_list
        );

        wp_send_json($this->b1nT_response('success', 'State date found.', $job_state_list_data));
    }

    /**
     * Get job details
     * 
     */
    function b1nT_job_details() {
        //security checks
        if(!$this->b1nT_security_checks()) {
            wp_send_json($this->b1nT_response('error', 'Something went wrong', ''));
        }

        $b1nT_job_address       = sanitize_text_field($_SESSION['b1nT_ssession']['job_address']);
        $b1nT_job_city          = sanitize_text_field($_SESSION['b1nT_ssession']['job_city']);
        $b1nT_job_state         = sanitize_text_field($_SESSION['b1nT_ssession']['job_state']);
        $b1nT_post_user_zipcode = sanitize_text_field($_SESSION['b1nT_ssession']['jobZipPostal']);

        $b1nT_job_details_data = array(
            "b1nT_billing_address" => $b1nT_job_address,
            "b1nT_billing_city"    => $b1nT_job_city,
            "b1nT_billing_state"   => $b1nT_job_state,
            "b1nT_billing_zipcode" => $b1nT_post_user_zipcode
        );

        wp_send_json($this->b1nT_response('success', 'Found job details', $b1nT_job_details_data));
    }

    /**
     * Get the pricing data needed to start gathering information
     * about the location where the user wants service.
     * 
     */
    function b1nT_get_job() {
        //security checks
        if(!$this->b1nT_security_checks()) {
            wp_send_json($this->b1nT_response('error', 'Something went wrong', ''));
        }

        //any errors
        $b1nT_field_errors      = array();
        $b1nT_post_user_zipcode = sanitize_text_field($_POST['b1nT_user_zipcode']);
        $b1nT_job_address       = sanitize_text_field($_POST['b1nT_job_address']);
        $b1nT_job_city          = sanitize_text_field($_POST['b1nT_job_city']);
        $b1nT_job_state         = sanitize_text_field($_POST['b1nT_job_state']);

        if($b1nT_post_user_zipcode == "") {
            $b1nT_zipcode_label                     = sanitize_text_field(get_option('b1nT_zipcode_label'));
            $b1nT_zipcode_label_message             = empty($b1nT_zipcode_label) ? 'Zipcode' : $b1nT_zipcode_label;
            $b1nT_field_errors['b1nT_user_zipcode'] = 'Please enter '.$b1nT_zipcode_label_message.'.';
        }

        if($b1nT_job_address == "") {
            $b1nT_field_errors['b1nT_job_address'] = 'Please enter job address';
        }
        
        if($b1nT_job_city == "") {
            $b1nT_field_errors['b1nT_job_city'] = 'Please enter job city';
        }

        if($b1nT_job_state == "") {
            $b1nT_field_errors['b1nT_job_state'] = 'Please select job state';
        } else if(strlen($b1nT_job_state) != 2) {
            $b1nT_field_errors['b1nT_job_state'] = 'Please select valid job state';
        }

        if(!empty($b1nT_field_errors)) {
            wp_send_json($this->b1nT_response('validation_error', $b1nT_field_errors, ''));
        }

        //gather some data, starting with the handshake.
        $b1nT_handshake = $this->b1nT_server_calls->b1nT_handshake(); //b1nT_handshake() sanitizes values

        if($b1nT_handshake && $b1nT_handshake->status == '200') {
            $b1nT_ss_billing_country = sanitize_text_field(get_option('b1nT_admin_country'));

            //gather price sheet data
            $b1nT_pricing_query = $this->b1nT_server_calls->b1nT_pricing_query($b1nT_handshake->key, $b1nT_post_user_zipcode); //b1nT_pricing_query() sanitizes values

            if($b1nT_pricing_query && $b1nT_pricing_query->status == '200'){
                $b1nT_price_sheets = $b1nT_pricing_query->priceSheets;

                $b1nT_assets_array = array();
                $b1nT_assets_class_array = array();  

                if($b1nT_price_sheets && is_array($b1nT_price_sheets)) {
                    //extracting and sanitizing the data i need
                    $b1nT_size_price_sheets = sizeof($b1nT_price_sheets);

                    for ($x = 0; $x < $b1nT_size_price_sheets; $x++) {
                        $b1nT_assets_class = strtolower($b1nT_price_sheets[$x]->Assets['0']->AssetClass);
                        array_push($b1nT_assets_class_array, $b1nT_assets_class);

                        $b1nT_min_distance = $b1nT_price_sheets[$x]->Distance1;
                        $b1nT_max_distance = $b1nT_price_sheets[$x]->Distance2;

                        //validate some values
                        if(!is_numeric($b1nT_min_distance) && !is_numeric($b1nT_max_distance)) { continue; }
                        if($b1nT_min_distance > $b1nT_max_distance) { continue; }

                        //grouping price sheets by asset, min distance and max distance
                        if(!isset($b1nT_assets_array[$b1nT_assets_class][$b1nT_min_distance][$b1nT_max_distance])) {
                            $b1nT_price_sheets_new                        = array();
                            $b1nT_price_sheets_new['b1nT_price_sheet_id'] = $b1nT_price_sheets[$x]->ID;
                            $b1nT_price_sheets_new['b1nT_total_amount']   = $b1nT_price_sheets[$x]->Total;
                            $b1nT_price_sheets_new['b1nT_sub_total']      = $b1nT_price_sheets[$x]->SubTotal;
                            $b1nT_price_sheets_new['b1nT_taxes']          = $b1nT_price_sheets[$x]->Taxes;
                            $b1nT_price_sheets_new['b1nT_days']           = $b1nT_price_sheets[$x]->Days;
                            $b1nT_price_sheets_new['b1nT_days_price']     = $b1nT_price_sheets[$x]->DaysPrice;
                            $b1nT_price_sheets_new['b1nT_units_included'] = $b1nT_price_sheets[$x]->UnitsIncluded;
                            $b1nT_price_sheets_new['b1nT_unit']           = $b1nT_price_sheets[$x]->Unit;
                            $b1nT_price_sheets_new['b1nT_excess_units']   = $b1nT_price_sheets[$x]->ExcessUnits;
                            $b1nT_price_sheets_new['b1nT_distance1']      = $b1nT_price_sheets[$x]->Distance1;
                            $b1nT_price_sheets_new['b1nT_distance2']      = $b1nT_price_sheets[$x]->Distance2;
                            $b1nT_assets_array[$b1nT_assets_class][$b1nT_min_distance][$b1nT_max_distance] = $b1nT_price_sheets_new;  
                        }
                    }
                }

                //sort the assets arrays
                sort($b1nT_assets_class_array);

                $b1nT_days_of_the_week = array();
                if(is_array($b1nT_pricing_query->daysOfTheWeek)) {
                    $b1nT_days_of_the_week = $b1nT_pricing_query->daysOfTheWeek;  
                }

                $b1nT_availability_table                              = array();
                $b1nT_availability_table['b1nT_col_headers']          = array();
                $b1nT_availability_table['b1nT_rows']                 = array();

                if(is_array($b1nT_pricing_query->availabilityTable->colHeaders)) {
                    $b1nT_availability_table['b1nT_col_headers'] = $b1nT_pricing_query->availabilityTable->colHeaders;
                }

                if(is_array($b1nT_pricing_query->availabilityTable->rows)) {
                    foreach($b1nT_pricing_query->availabilityTable->rows as $b1nT_row) {
                        $b1nT_row_day_int = gmdate('w', strtotime($b1nT_row[0]));
                        if($b1nT_days_of_the_week[$b1nT_row_day_int]) {
                            array_push($b1nT_availability_table['b1nT_rows'], $b1nT_row);
                        }
                    }
                }

                $b1nT_overall_max_distance = $b1nT_pricing_query->maxDistance;
                $b1nT_overall_max_distance = is_numeric($b1nT_overall_max_distance) ? $b1nT_overall_max_distance : "0";

                $b1nT_earliest_booking_date = $b1nT_pricing_query->earliestBookingDate;
                $b1nT_earliest_booking_date = DateTime::createFromFormat('Y-m-d', $b1nT_earliest_booking_date) !== false ? $b1nT_earliest_booking_date : "";

                //cuff time
                $b1nT_cut_off_time = '5:00 PM';
                if(DateTime::createFromFormat('h:i A', $b1nT_pricing_query->cutOffTime)) {
                    $b1nT_cut_off_time = $b1nT_pricing_query->cutOffTime;
                }

                $b1nT_cut_off_date = current_datetime()->format('Y-m-d')." ".$b1nT_cut_off_time;
                $b1nT_cut_off_now  = current_datetime()->format('Y-m-d h:i A');

                //make sure that cut off time is not exceeded
                if(strtotime($b1nT_cut_off_now) > strtotime($b1nT_cut_off_date)) {
                    $b1nT_new_earliest_booking_time_stamp = strtotime($b1nT_earliest_booking_date)+86400;
                    $b1nT_earliest_booking_date = date_i18n('Y-m-d', $b1nT_new_earliest_booking_time_stamp);
                }

                //get state information from google based on center lng and center lat
                $b1nT_mapit_center_lat = $b1nT_pricing_query->mapItCenterLat;
                $b1nT_mapit_center_lng = $b1nT_pricing_query->mapItCenterLng;

                //make sure its a lat/lng
                $b1nT_mapit_center_lat = is_numeric($b1nT_mapit_center_lat) && $b1nT_mapit_center_lat >= -90 && $b1nT_mapit_center_lat <= 90 ? $b1nT_mapit_center_lat : "0";
                $b1nT_mapit_center_lng = is_numeric($b1nT_mapit_center_lng) && $b1nT_mapit_center_lng >= -180 && $b1nT_mapit_center_lng <= 180 ? $b1nT_mapit_center_lng : "0";

                //multiple map centers
                $b1nT_multiple_map_centers = array();
                if(is_array($b1nT_pricing_query->multipleMapCenters)) {
                    $b1nT_multiple_map_centers  = $b1nT_pricing_query->multipleMapCenters;
                }

                //create the container size list
                $b1nT_assets_class_header_array = array_unique($b1nT_assets_class_array);
                $b1nT_container_sizes           = '<option value="">--Container Size--</option>';
                foreach($b1nT_assets_class_header_array as $b1nT_container_size){
                    $b1nT_container_sizes .='<option value="'.$b1nT_container_size.'" '.'>'.$b1nT_container_size.'</option>';
                } 

                //create the service type list
                $b1nT_service_types       = "";
                $b1nT_service_types_array = array();

                if(is_array($b1nT_pricing_query->serviceTypes)) {
                    $b1nT_service_types_array = $b1nT_pricing_query->serviceTypes;
                }

                foreach($b1nT_service_types_array as $b1nT_service_type) {
                    $b1nT_service_types .= '<option value="'.$b1nT_service_type.'" '.'>'.$b1nT_service_type.'</option>';
                } 

                $b1nT_lad_size_of_rows = sizeof($b1nT_availability_table['b1nT_rows']) - 1;
                $b1nT_last_availabile_date = $b1nT_availability_table['b1nT_rows'][$b1nT_lad_size_of_rows][0];

                $b1nT_today     = gmdate("Y-m-d");  
                $b1nT_date_diff = strtotime($b1nT_today) - strtotime($b1nT_earliest_booking_date);
                $b1nT_date_diff = round($b1nT_date_diff / (60 * 60 * 24));
                $b1nT_date_diff = trim($b1nT_date_diff,"-");

                $b1nT_date_last = strtotime($b1nT_last_availabile_date) - strtotime($b1nT_today);
                $b1nT_date_last = round($b1nT_date_last / (60 * 60 * 24));
                $b1nT_date_last = trim($b1nT_date_last, "-");

                //build availability html table
                $b1nT_availability_table_col_headers = $b1nT_availability_table['b1nT_col_headers'];
                $b1nT_availability_table_col_header_indexes = array();

                $b1nT_availability_table_show_view = "<h3>Container Availability Table</h3>";                
                $b1nT_availability_table_show_view .= "<div class='b1nT_availability_inner'><table id='b1nT_availability_table_str'><thead><tr>";

                //headers
                foreach($b1nT_availability_table_col_headers as $b1nT_availability_table_col_header){
                    if ($b1nT_availability_table_col_header == 'Date') {
                        $b1nT_availability_table_show_view .="<th><b>".$b1nT_availability_table_col_header."</b></th>";
                    } else {   
                        if (in_array($b1nT_availability_table_col_header, $b1nT_assets_class_header_array)) { 
                            //keep track of the index to reference it when display rows
                            $b1nT_availability_table_col_header_index = array_search($b1nT_availability_table_col_header, $b1nT_availability_table_col_headers);
                            array_push($b1nT_availability_table_col_header_indexes, $b1nT_availability_table_col_header_index);
                            $b1nT_availability_table_show_view .="<th style='text-align:center;'><b>".$b1nT_availability_table_col_header."</b></th>";
                        }
                    }
                } 

                $b1nT_availability_table_show_view .= "</tr></thead>";
                $b1nT_availability_table_show_view .= "<tbody>";

                //keep track of the next 7 days
                $b1nT_timestamp = strtotime('next Sunday');
                $b1nT_days = array();
                for ($i = 0; $i < 7; $i++) { 
                    $b1nT_days[] = strftime('%A', $b1nT_timestamp);
                    $b1nT_timestamp = strtotime('+1 day', $b1nT_timestamp);
                } 

                $b1nT_availability_table_rows      = $b1nT_availability_table['b1nT_rows'];
                $b1nT_availability_table_rows_size = sizeof($b1nT_availability_table_rows);
                $b1nT_earliest_booking_date_conv   = strtotime($b1nT_earliest_booking_date);

                $b1nT_asset_buffer = $b1nT_pricing_query->assetBuffer;
                $b1nT_asset_buffer = is_numeric($b1nT_asset_buffer) ? $b1nT_asset_buffer : "0";

                //rows
                for ($i = 0; $i < $b1nT_availability_table_rows_size; $i++) {
                    $b1nT_availability_table_rows_size_inner = sizeof($b1nT_availability_table_rows[$i]);
                    $b1nT_availability_table_show_view .= "<tr>";

                    //the dates
                    for ($j = 0; $j < $b1nT_availability_table_rows_size_inner; $j++) {
                        if (DateTime::createFromFormat('Y-m-d', $b1nT_availability_table_rows[$i][$j]) !== false) {
                            $b1nT_availability_table_date        = strtotime($b1nT_availability_table_rows[$i][$j]);
                            $b1nT_day_of_availability_table_date = gmdate("l", $b1nT_availability_table_date);
                            $b1nT_day_of_availability_table_day  = array_search($b1nT_day_of_availability_table_date, $b1nT_days);

                            if(!is_numeric($b1nT_earliest_booking_date_conv)) { continue; }
                            if(!is_numeric($b1nT_availability_table_date)) { continue; }
                            if(!is_numeric($b1nT_days_of_the_week[$b1nT_day_of_availability_table_day])) { continue; }

                            if ($b1nT_earliest_booking_date_conv <= $b1nT_availability_table_date) {
                                if ($b1nT_days_of_the_week[$b1nT_day_of_availability_table_day] > 0 ) {
                                    $b1nT_availability_table_show_view .= "<td><b>".gmdate("M d, Y", strtotime($b1nT_availability_table_rows[$i][$j]))."</b></td>";
                                }               
                            }               
                        }               
                    }       
                   
                    //the assets     
                    foreach($b1nT_availability_table_col_header_indexes as $b1nT_availability_table_col_header_index){
                        $b1nT_availability_table_date        = strtotime($b1nT_availability_table_rows[$i][0]);
                        $b1nT_day_of_availability_table_date = gmdate("l", $b1nT_availability_table_date);
                        $b1nT_day_of_availability_table_day  = array_search($b1nT_day_of_availability_table_date, $b1nT_days);

                        if(!is_numeric($b1nT_earliest_booking_date_conv)) { continue; }
                        if(!is_numeric($b1nT_availability_table_date)) { continue; }
                        if(!is_numeric($b1nT_days_of_the_week[$b1nT_day_of_availability_table_day])) { continue; }

                        if ($b1nT_earliest_booking_date_conv <= $b1nT_availability_table_date) {
                            if ($b1nT_days_of_the_week[$b1nT_day_of_availability_table_day] > 0 ){
                                $b1nT_available_asset = $b1nT_availability_table_rows[$i][$b1nT_availability_table_col_header_index];
                                if ( is_numeric($b1nT_available_asset) && ($b1nT_available_asset > $b1nT_asset_buffer)) {
                                    $b1nT_availability_table_show_view .= "<td style='color:green;text-align:center;'>&#10003;</td>";
                                } else {           
                                    $b1nT_availability_table_show_view .= "<td style='color:red;text-align:center;'>X</td>";
                                }               
                            }               
                        }               
                    } 

                    $b1nT_availability_table_show_view .= "</tr>";
                }  

                $b1nT_availability_table_show_view .= "</tbody>";
                $b1nT_availability_table_show_view .= "</table></div>";

                //terms and conditions
                $termsUrl    = wp_http_validate_url($b1nT_pricing_query->termsUrl) ? $b1nT_pricing_query->termsUrl : "";
                $apiUseTerms = is_numeric($b1nT_pricing_query->apiUseTerms) ? $b1nT_pricing_query->apiUseTerms : 0;

                //add some variables to sessions for other forms, all sanitized
                $_SESSION['b1nT_ssession']['job_address']            = $b1nT_job_address;
                $_SESSION['b1nT_ssession']['job_city']               = $b1nT_job_city;
                $_SESSION['b1nT_ssession']['job_state']              = $b1nT_job_state;
                $_SESSION['b1nT_ssession']['jobZipPostal']           = $b1nT_post_user_zipcode;
                $_SESSION['b1nT_ssession']['billing_country']        = $b1nT_ss_billing_country;
                $_SESSION['b1nT_ssession']['map_center_lat']         = $b1nT_mapit_center_lat;
                $_SESSION['b1nT_ssession']['map_center_lng']         = $b1nT_mapit_center_lng;
                $_SESSION['b1nT_ssession']['assetsClassArray']       = $b1nT_assets_array;
                $_SESSION['b1nT_ssession']['availability_table']     = $b1nT_availability_table;
                $_SESSION['b1nT_ssession']['assetsClassHeaderArray'] = $b1nT_assets_class_header_array;
                $_SESSION['b1nT_ssession']['assetBuffer']            = $b1nT_asset_buffer;
                $_SESSION['b1nT_ssession']['daysOfTheWeek']          = $b1nT_days_of_the_week;
                $_SESSION['b1nT_ssession']['days']                   = $b1nT_days;
                $_SESSION['b1nT_ssession']['multiple_map_centers']   = $b1nT_multiple_map_centers;
                $_SESSION['b1nT_ssession']['overall_max_distance']   = $b1nT_overall_max_distance;

                //return array
                $job_field_data = array(
                    'b1nT_service_types'                => $b1nT_service_types,
                    'b1nT_container_sizes'              => $b1nT_container_sizes,
                    'b1nT_date_requested'               => $b1nT_earliest_booking_date,
                    'b1nT_date_diff'                    => $b1nT_date_diff,
                    'b1nT_date_last'                    => $b1nT_date_last,
                    'b1nT_availability_table_show_view' => $b1nT_availability_table_show_view,
                    'b1nT_pricingquery_response'        => $b1nT_pricing_query,
                    'b1nT_termsURL'                     => $termsUrl,
                    'b1nT_apiUseTerms'                  => $apiUseTerms,
                    'debug'                             => ''
                );

                wp_send_json($this->b1nT_response('success', 'Pricing Query data found.', $job_field_data));
            } else {
                $b1nT_message = 'Status: '.$b1nT_pricing_query->status.'<br>Error: '.$b1nT_pricing_query->errorString;
                wp_send_json($this->b1nT_response('error', $b1nT_message, ''));
            }
        } else {
            $b1nT_message = 'Invalid configuration.<br>Status: '.$b1nT_handshake->status.'<br>Error: '.$b1nT_handshake->errorString;
            wp_send_json($this->b1nT_response('error', $b1nT_message, ''));
        }
    }

    /**
     * Verify that the new date request is available for booking
     * 
     */
    function b1nT_availability_search() {
        //security checks
        if(!$this->b1nT_security_checks()) {
            wp_send_json($this->b1nT_response('error', 'Something went wrong', ''));
        }

        //clean up the post date requested
        $b1nT_date_requested = sanitize_text_field($_POST['b1nT_date_requested']);
        $b1nT_date_requested_formated = date_format(date_create($b1nT_date_requested), "Y-m-d");

        //gather data
        $b1nT_availability_table = array();
        if(is_array($_SESSION['b1nT_ssession']['availability_table'])) {
            $b1nT_availability_table = $this->b1nT_sanitize_array_values($_SESSION['b1nT_ssession']['availability_table']); //b1nT_sanitize_array_values() sanitizes values
        }

        $b1nT_availability_table_result = $this->b1nT_server_calls->b1nT_search_availability_table($b1nT_availability_table['b1nT_rows'], $b1nT_date_requested_formated); //b1nT_search_availability_table() sanitizes values

        $b1nT_container_field_data  = array();
        $b1nT_container_sizes = '<option value="">--Container Size--</option>';

        //is the container available on the date requested
        if (is_array($b1nT_availability_table_result) && empty($b1nT_availability_table_result)) {
            $b1nT_container_field_data['b1nT_container_size'] = $b1nT_container_sizes;
            $b1nT_field_errors['b1nT_container_availability'] = 'No booking available on the date selected. Please refer to the availability table.';
            wp_send_json($this->b1nT_response('validation_error', $b1nT_field_errors, $b1nT_container_field_data));
        }

        //repopulate the container size field.
        $b1nT_assets_class_header_array = array();
        if(is_array($_SESSION['b1nT_ssession']['assetsClassHeaderArray'])) {
            $b1nT_assets_class_header_array = $this->b1nT_sanitize_array_values($_SESSION['b1nT_ssession']['assetsClassHeaderArray']); //b1nT_sanitize_array_values() sanitizes values
        }

        foreach($b1nT_assets_class_header_array as $b1nT_container_size){
            $b1nT_container_sizes .='<option value="'.$b1nT_container_size.'" '.'>'.$b1nT_container_size.'</option>';
        } 

        $b1nT_container_field_data['b1nT_container_size'] = $b1nT_container_sizes;
        wp_send_json($this->b1nT_response('success', 'Container data found.', $b1nT_container_field_data));
    }

    /**
     * Gather information about the job and select the most
     * optimum price sheet if any.
     * 
     */
    function b1nT_about_job() {
        //security checks
        if(!$this->b1nT_security_checks()) {
            $b1nT_message = 'Something went wrong';
            wp_send_json($this->b1nT_response('error', esc_html($b1nT_message), ''));
        }

        //any errors
        $b1nT_field_errors            = array();
        $b1nT_service_type            = sanitize_text_field($_POST['b1nT_service_type']);
        $b1nT_container_size          = sanitize_text_field($_POST['b1nT_container_size']);
        $b1nT_job_address             = sanitize_text_field($_SESSION['b1nT_ssession']['job_address']);
        $b1nT_job_city                = sanitize_text_field($_SESSION['b1nT_ssession']['job_city']);
        $b1nT_job_state               = sanitize_text_field($_SESSION['b1nT_ssession']['job_state']);
        $b1nT_date_requested          = sanitize_text_field($_POST['b1nT_date_requested']);
        $b1nT_date_requested_formated = date_format(date_create($b1nT_date_requested), "Y-m-d");

        if($b1nT_service_type == "") {
            $b1nT_field_errors['b1nT_service_type'] = 'Please select serivce type';
        }
        
        if($b1nT_container_size == "") {
            $b1nT_field_errors['b1nT_container_size'] = 'Please select container size';
        }
        
        if($b1nT_date_requested == "") {
            $b1nT_field_errors['b1nT_date_requested'] = 'Please enter job request date';
        }

        if(!empty($b1nT_field_errors)) {
            wp_send_json($this->b1nT_response('validation_error', $b1nT_field_errors, ''));
        }

        //gather data
        $b1nT_availability_table = array();
        if(is_array($_SESSION['b1nT_ssession']['availability_table'])) {
            $b1nT_availability_table = $this->b1nT_sanitize_array_values($_SESSION['b1nT_ssession']['availability_table']); //b1nT_sanitize_array_values() sanitizes values
        }

        $b1nT_availability_table_result = $this->b1nT_server_calls->b1nT_search_availability_table($b1nT_availability_table['b1nT_rows'], $b1nT_date_requested_formated); //b1nT_search_availability_table() sanitizes values

        $b1nT_container_size_index = array_search($b1nT_container_size, $b1nT_availability_table['b1nT_col_headers']);
        $b1nT_containers_available = $b1nT_availability_table_result[$b1nT_container_size_index];
        $b1nT_containers_available = is_numeric($b1nT_containers_available) ? $b1nT_containers_available : "0";

        $b1nT_asset_buffer = sanitize_text_field($_SESSION['b1nT_ssession']['assetBuffer']);
        $b1nT_asset_buffer = is_numeric($b1nT_asset_buffer) ? $b1nT_asset_buffer : "0";

        //is the container available on the date requested
        if (is_numeric($b1nT_containers_available) && ( $b1nT_containers_available <= $b1nT_asset_buffer)) {
            $b1nT_field_errors['b1nT_container_availability'] = 'This dumpster size is not available on the date selected. Please refer to the availability table.';
            wp_send_json($this->b1nT_response('validation_error', $b1nT_field_errors, ''));
        }

        $b1nT_overall_max_distance = sanitize_text_field($_SESSION['b1nT_ssession']['overall_max_distance']);
        $b1nT_ss_billing_country   = sanitize_text_field($_SESSION['b1nT_ssession']['billing_country']);
        $b1nT_billing_state_list   = '<option value="">--Select Billing State--</option>';
        $b1nT_payment_state_list   = '<option value="">--Select Payment State--</option>';

        $b1nT_states = $this->b1nT_server_calls->b1nT_get_states($b1nT_ss_billing_country); //b1nT_get_states() sanitizes values

        if(is_array($b1nT_states)) {
            foreach($b1nT_states as $b1nT_state){
                $b1nT_billing_state_list .= '<option value="'.$b1nT_state->state_short.'" '.'>'.$b1nT_state->state_name.'</option>';
                $b1nT_payment_state_list .= '<option value="'.$b1nT_state->state_short.'" '.'>'.$b1nT_state->state_name.'</option>';
            }
        }

        $b1nT_state_full     = $this->b1nT_server_calls->b1nT_get_state_name($b1nT_job_state); //b1nT_get_state_name() sanitizes values
        $b1nT_address        = $b1nT_state_full ? $b1nT_job_address.",".$b1nT_job_city.",".sanitize_text_field($b1nT_state_full->state_name).",".sanitize_text_field($b1nT_ss_billing_country) : "";
        $b1nT_lat_long_query = $this->b1nT_server_calls->b1nT_get_latitude_longitude($b1nT_address); //b1nT_get_latitude_longitude() sanitizes values

        //any errors
        if($b1nT_lat_long_query['b1nT_error_message']){
            $b1nT_field_errors['b1nT_street_address'] = $b1nT_lat_long_query['b1nT_error_message'];
            wp_send_json($this->b1nT_response('validation_error', $b1nT_field_errors, ''));
        }

        $b1nT_multiple_map_centers = array();
        if(is_array($_SESSION['b1nT_ssession']['multiple_map_centers'])) {
            $b1nT_multiple_map_centers = $this->b1nT_sanitize_array_values($_SESSION['b1nT_ssession']['multiple_map_centers']); //b1nT_sanitize_array_values() sanitizes values
        }

        if(empty($b1nT_multiple_map_centers)) {
            wp_send_json($this->b1nT_response('error', 'No valid map center', ''));
        }

        $b1nT_assets_array = array();
        if(is_array($_SESSION['b1nT_ssession']['assetsClassArray'])) {
            $b1nT_assets_array = $this->b1nT_sanitize_array_values($_SESSION['b1nT_ssession']['assetsClassArray']); //b1nT_sanitize_array_values() sanitizes values
        }

        $b1nT_lat  = $b1nT_lat_long_query['b1nT_lat'];
        $b1nT_long = $b1nT_lat_long_query['b1nT_long'];
        $b1nT_lat  = is_numeric($b1nT_lat) && $b1nT_lat >= -90 && $b1nT_lat <= 90 ? $b1nT_lat : "0";
        $b1nT_lng  = is_numeric($b1nT_lng) && $b1nT_lng >= -180 && $b1nT_lng <= 180 ? $b1nT_lng : "0";

        $b1nT_ps_failed_messages = array();
        $b1nT_multiple_map_centers_price_sheet;

        //add errors to the error messages array
        $b1nT_push_errors = function($b1nT_arg_code) use(&$b1nT_ps_failed_messages) {
            if(is_array($b1nT_ps_failed_messages) && !in_array($b1nT_arg_code, $b1nT_ps_failed_messages)) {
                array_push($b1nT_ps_failed_messages, $b1nT_arg_code);
            }
        };

        foreach($b1nT_multiple_map_centers as $b1nT_multiple_map_centers_object) {
            $b1nT_ss_map_center_lat = $b1nT_multiple_map_centers_object->Latitude;
            $b1nT_ss_map_center_lng = $b1nT_multiple_map_centers_object->Longitude;
            $b1nT_ss_map_center_lat = is_numeric($b1nT_ss_map_center_lat) && $b1nT_ss_map_center_lat >= -90 && $b1nT_ss_map_center_lat <= 90 ? $b1nT_ss_map_center_lat : "0";
            $b1nT_ss_map_center_lng = is_numeric($b1nT_ss_map_center_lng) && $b1nT_ss_map_center_lng >= -180 && $b1nT_ss_map_center_lng <= 180 ? $b1nT_ss_map_center_lng : "0";

            $b1nT_driving_distance_query = $this->b1nT_server_calls->b1nT_get_driving_distance($b1nT_lat, $b1nT_long, $b1nT_ss_map_center_lat, $b1nT_ss_map_center_lng); //b1nT_get_driving_distance() sanitizes values

            //error = skip this irritation
            if($b1nT_driving_distance_query['b1nT_error_message']) {
                $b1nT_push_errors('400');
                continue;
            } 

            $b1nT_drv_distance_clean = sanitize_text_field($b1nT_driving_distance_query['b1nT_distance']);
            $b1nT_drv_distance       = sanitize_text_field(str_replace(array("km", "mi", ","), "", $b1nT_driving_distance_query['b1nT_distance']));

            //we need a numeric values
            if(!is_numeric($b1nT_drv_distance)) {
                $b1nT_push_errors('401');
                continue;
            }

            //max distance; has to be bigger than 0 to be evaluated
            if(is_numeric($b1nT_overall_max_distance) && $b1nT_overall_max_distance > 0 && $b1nT_overall_max_distance < $b1nT_drv_distance) {
                $b1nT_push_errors('402');
                continue;
            }

            if($b1nT_multiple_map_centers_price_sheet && is_numeric($b1nT_multiple_map_centers_price_sheet['b1nT_drv_distance'])) {
                //dont settle for a bigger distance
                if($b1nT_drv_distance > $b1nT_multiple_map_centers_price_sheet['b1nT_drv_distance']) {
                    continue;
                }
            }
 
            $b1nT_single_price_sheet; //reset

            foreach($b1nT_assets_array as $b1nT_assets => $b1nT_assets_value) {
                if(!is_array($b1nT_assets_value)) { continue; }
                if($b1nT_container_size != $b1nT_assets) { continue; };

                foreach($b1nT_assets_value as $b1nT_min_distance => $b1nT_min_distance_value) {
                    if(!is_numeric($b1nT_min_distance)) { 
                        $b1nT_push_errors('403');
                        continue; 
                    }
                    if(!is_array($b1nT_min_distance_value)) { continue; }        

                    foreach($b1nT_min_distance_value as $b1nT_max_distance => $b1nT_max_distance_value) {
                        if(!is_numeric($b1nT_max_distance)) {
                            $b1nT_push_errors('403');
                            continue; 
                        }

                        //no distance price sheet ? use it and call it a day.
                        if($b1nT_min_distance == 0 && $b1nT_max_distance == 0) {
                            if(is_numeric($b1nT_max_distance_value['b1nT_distance1']) && is_numeric($b1nT_max_distance_value['b1nT_distance2'])) {
                                $b1nT_single_price_sheet = $b1nT_max_distance_value;
                                $b1nT_single_price_sheet['b1nT_drv_distance'] = $b1nT_drv_distance;
                                $b1nT_single_price_sheet['b1nT_drv_distance_clean'] = $b1nT_drv_distance_clean;
                                $b1nT_single_price_sheet['b1nT_drv_distance_mc'] = $b1nT_multiple_map_centers_object->Name;
                                break 3;
                            }
                            $b1nT_push_errors('403');
                        }

                        //is the distance within the price sheet specs
                        if($b1nT_drv_distance < $b1nT_min_distance) { 
                            $b1nT_push_errors('404');
                            continue 2; 
                        }
                        if($b1nT_drv_distance > $b1nT_max_distance) {
                            $b1nT_push_errors('404');
                            continue; 
                        }

                        if(!$b1nT_single_price_sheet) {
                            //these values have to be numeric, we need them for calculations
                            if(is_numeric($b1nT_max_distance_value['b1nT_distance1']) && is_numeric($b1nT_max_distance_value['b1nT_distance2'])) {
                                $b1nT_single_price_sheet = $b1nT_max_distance_value;
                                $b1nT_single_price_sheet['b1nT_drv_distance'] = $b1nT_drv_distance;
                                $b1nT_single_price_sheet['b1nT_drv_distance_clean'] = $b1nT_drv_distance_clean;
                                $b1nT_single_price_sheet['b1nT_drv_distance_mc'] = $b1nT_multiple_map_centers_object->ID;
                                continue;
                            }

                            $b1nT_push_errors('403');
                            continue;     
                        }

                        //at this point we have multiple price sheets that fit all,
                        //requirements. lets refine our current price sheet selection.     
                        if($b1nT_single_price_sheet['b1nT_distance1'] > $b1nT_min_distance) {
                            if(is_numeric($b1nT_max_distance_value['b1nT_distance1']) && is_numeric($b1nT_max_distance_value['b1nT_distance2'])) {
                                $b1nT_single_price_sheet = $b1nT_max_distance_value;
                                $b1nT_single_price_sheet['b1nT_drv_distance'] = $b1nT_drv_distance;
                                $b1nT_single_price_sheet['b1nT_drv_distance_clean'] = $b1nT_drv_distance_clean;
                                $b1nT_single_price_sheet['b1nT_drv_distance_mc'] = $b1nT_multiple_map_centers_object->ID;
                                continue;
                            }

                            $b1nT_push_errors('403');
                            continue;
                        }
                        
                        //now that we have selected the smallest min distance that we could
                        //find, lets select the smallest maximum distance that we could find.
                        if($b1nT_single_price_sheet['b1nT_distance1'] == $b1nT_min_distance && $b1nT_single_price_sheet['b1nT_distance2'] > $b1nT_max_distance) {
                            if(is_numeric($b1nT_max_distance_value['b1nT_distance1']) && is_numeric($b1nT_max_distance_value['b1nT_distance2'])) {
                                $b1nT_single_price_sheet = $b1nT_max_distance_value;
                                $b1nT_single_price_sheet['b1nT_drv_distance'] = $b1nT_drv_distance;
                                $b1nT_single_price_sheet['b1nT_drv_distance_clean'] = $b1nT_drv_distance_clean;
                                $b1nT_single_price_sheet['b1nT_drv_distance_mc'] = $b1nT_multiple_map_centers_object->ID;
                                continue;
                            }

                            $b1nT_push_errors('403');
                            continue;
                        }
                    }
                }
            }

            //we are going to filter the
            //price sheet seperately per
            //center and set here if any
            if($b1nT_single_price_sheet) {
                $b1nT_multiple_map_centers_price_sheet = $b1nT_single_price_sheet; 
            }
        }

        //any price sheet.
        if(!$b1nT_multiple_map_centers_price_sheet) {
            $b1nT_failed_message_string = 'No pricing information available please contact our office.';

            if(!empty($b1nT_ps_failed_messages)) {
                $b1nT_failed_message_string .= ' Error code: ';
                $b1nT_failed_message_string .= implode(', ', $b1nT_ps_failed_messages);
            } 

            wp_send_json($this->b1nT_response('error', $b1nT_failed_message_string, ''));
        }

        //gather price sheet data, all the values are sanitized at the top.
        $b1nT_total_amount                  = $b1nT_multiple_map_centers_price_sheet['b1nT_total_amount'];
        $b1nT_price_sheets_sub_total        = $b1nT_multiple_map_centers_price_sheet['b1nT_sub_total'];
        $b1nT_price_sheets_taxes            = $b1nT_multiple_map_centers_price_sheet['b1nT_taxes'];
        $b1nT_price_sheets_days             = $b1nT_multiple_map_centers_price_sheet['b1nT_days'];
        $b1nT_price_sheets_days_price       = $b1nT_multiple_map_centers_price_sheet['b1nT_days_price'];
        $b1nT_price_sheets_units_included   = $b1nT_multiple_map_centers_price_sheet['b1nT_units_included'];
        $b1nT_price_sheets_unit             = $b1nT_multiple_map_centers_price_sheet['b1nT_unit'];
        $b1nT_price_sheets_excess_units     = $b1nT_multiple_map_centers_price_sheet['b1nT_excess_units'];
        $b1nT_price_sheets_id               = $b1nT_multiple_map_centers_price_sheet['b1nT_price_sheet_id'];
        $b1nT_drv_distance_from_price_sheet = $b1nT_multiple_map_centers_price_sheet['b1nT_drv_distance_clean'];
        $b1nT_drv_distance_from_mc          = $b1nT_single_price_sheet['b1nT_drv_distance_mc'];

        //add some variables to sessions for other forms, all sanitized
        $_SESSION['b1nT_ssession']['lat']            = $b1nT_lat;
        $_SESSION['b1nT_ssession']['long']           = $b1nT_long;
        $_SESSION['b1nT_ssession']['job_distance']   = $b1nT_drv_distance_from_price_sheet;
        $_SESSION['b1nT_ssession']['date_requested'] = $b1nT_date_requested_formated;
        $_SESSION['b1nT_ssession']['service_type']   = $b1nT_service_type;
        $_SESSION['b1nT_ssession']['container_size'] = $b1nT_container_size;
        $_SESSION['b1nT_ssession']['priceSheetID']   = $b1nT_price_sheets_id;
        $_SESSION['b1nT_ssession']['map_center']     = $b1nT_drv_distance_from_mc;

        $b1nT_billing_field_data = array(
            'b1nT_billing_state_list'          => $b1nT_billing_state_list,
            'b1nT_payment_state_list'          => $b1nT_payment_state_list,
            'b1nT_total_amount'                => $b1nT_total_amount,
            'b1nT_price_sheets_sub_total'      => $b1nT_price_sheets_sub_total,
            'b1nT_price_sheets_taxes'          => $b1nT_price_sheets_taxes,
            'b1nT_price_sheets_days'           => $b1nT_price_sheets_days,
            'b1nT_price_sheets_days_price'     => $b1nT_price_sheets_days_price,
            'b1nT_price_sheets_units_included' => $b1nT_price_sheets_units_included,
            'b1nT_price_sheets_unit'           => $b1nT_price_sheets_unit,
            'b1nT_price_sheets_excess_units'   => $b1nT_price_sheets_excess_units
        );

        wp_send_json($this->b1nT_response('success', 'Price sheet data found.', $b1nT_billing_field_data));
    }

    /**
     * Gather billing data, submit
     * the request for service.
     * 
     */
    function b1nT_place_order() {
        //security checks
        if(!$this->b1nT_security_checks()) {
            wp_send_json($this->b1nT_response('error', 'Something went wrong', ''));
        }

        $b1nT_field_errors           = array();
        $b1nT_zipcode_label          = sanitize_text_field(get_option('b1nT_zipcode_label'));
        $b1nT_zipcode_label_message  = empty($b1nT_zipcode_label) ? 'Zipcode' : $b1nT_zipcode_label;

        $b1nT_mode                   = sanitize_text_field(get_option('b1nT_mode'));
        $b1nT_user_name              = sanitize_text_field($_POST['b1nT_user_name']);
        $b1nT_billing_address        = sanitize_text_field($_POST['b1nT_billing_address']);
        $b1nT_billing_address_2      = sanitize_text_field($_POST['b1nT_billing_address_2']);
        $b1nT_billing_city           = sanitize_text_field($_POST['b1nT_billing_city']);
        $b1nT_billing_state          = sanitize_text_field($_POST['b1nT_billing_state']);
        $b1nT_billing_zipcode        = sanitize_text_field($_POST['b1nT_billing_zipcode']);
        $b1nT_billing_phone          = sanitize_text_field($_POST['b1nT_billing_phone']);
        $b1nT_filtered_billing_phone = preg_replace("/[^\d]/", "", $b1nT_billing_phone);
        $b1nT_billing_email          = sanitize_email($_POST['b1nT_billing_email']);
 
        $b1nT_payment_info           = esc_attr(get_option('b1nT_payment_info'));
        $b1nT_payment_first_name     = sanitize_text_field($_POST['b1nT_payment_first_name']);
        $b1nT_payment_last_name      = sanitize_text_field($_POST['b1nT_payment_last_name']);
        $b1nT_payment_address        = sanitize_text_field($_POST['b1nT_payment_address']);
        $b1nT_payment_address_2      = sanitize_text_field($_POST['b1nT_payment_address_2']);
        $b1nT_payment_city           = sanitize_text_field($_POST['b1nT_payment_city']);
        $b1nT_payment_state          = sanitize_text_field($_POST['b1nT_payment_state']);
        $b1nT_payment_zipcode        = sanitize_text_field($_POST['b1nT_payment_zipcode']);
        $b1nT_card_number            = sanitize_text_field($_POST['b1nT_card_number']);
        $b1nT_card_expiry_month      = sanitize_text_field($_POST['b1nT_card_expiry_month']);
        $b1nT_card_expiry_year       = sanitize_text_field($_POST['b1nT_card_expiry_year']);
        $b1nT_card_cvv               = sanitize_text_field($_POST['b1nT_card_cvv']);
        $b1nT_order_note             = sanitize_text_field($_POST['b1nT_order_note']);

        //billing
        if($b1nT_user_name == "") {
            $b1nT_field_errors['b1nT_user_name'] = 'Please enter name';
        }

        if($b1nT_billing_address == "") {
            $b1nT_field_errors['b1nT_billing_address'] = 'Please enter address';
        }

        if($b1nT_billing_city == "") {
            $b1nT_field_errors['b1nT_billing_city'] = 'Please enter city';
        }

        if($b1nT_billing_state == "") {
            $b1nT_field_errors['b1nT_billing_state'] = 'Please enter state';
        } else if(strlen($b1nT_billing_state) != 2) {
            $b1nT_field_errors['b1nT_billing_state'] = 'Please enter valid state';
        }

        if($b1nT_billing_zipcode == "") {
            $b1nT_field_errors['b1nT_billing_zipcode'] = 'Please enter '.$b1nT_zipcode_label_message;
        }

        if($b1nT_billing_phone == "") {
            $b1nT_field_errors['b1nT_billing_phone'] = 'Please enter phone number';
        } else if(strlen($b1nT_filtered_billing_phone) <= 9) {
            $b1nT_field_errors['b1nT_billing_phone'] = 'Please enter valid phone number';
        }

        if($b1nT_billing_email == "") {
            $b1nT_field_errors['b1nT_billing_email'] = 'Please enter email address';
        } else if(!filter_var($b1nT_billing_email, FILTER_VALIDATE_EMAIL)) {
            $b1nT_field_errors['b1nT_billing_email'] = 'Please enter valid email address';
        }

        //payment
        if($b1nT_payment_info === "Show") {
            if($b1nT_payment_first_name == "") {
                $b1nT_field_errors['b1nT_payment_first_name'] = 'Please enter first name';
            }            

            if($b1nT_payment_last_name == "") {
                $b1nT_field_errors['b1nT_payment_last_name'] = 'Please enter last name';
            } 

            if($b1nT_payment_address == "") {
                $b1nT_field_errors['b1nT_payment_address'] = 'Please enter address';
            } 

            if($b1nT_payment_city == "") {
                $b1nT_field_errors['b1nT_payment_city'] = 'Please enter city';
            }

            if($b1nT_payment_state == "") {
                $b1nT_field_errors['b1nT_payment_state'] = 'Please enter state';
            } else if(strlen($b1nT_payment_state) != 2) {
                $b1nT_field_errors['b1nT_payment_state'] = 'Please enter valid state';
            }

            if($b1nT_payment_zipcode == "") {
                $b1nT_field_errors['b1nT_payment_zipcode'] = 'Please enter '.$b1nT_zipcode_label_message;
            }

            if($b1nT_card_number == "") {
                $b1nT_field_errors['b1nT_card_number'] = 'Please enter card number';
            } else if($this->b1nT_server_calls->b1nT_credit_card_number_valid($b1nT_card_number) == false) {
                $b1nT_field_errors['b1nT_card_number'] = 'Please enter valid card number';    
            }

            if($b1nT_card_expiry_month == "") {
                $b1nT_field_errors['b1nT_card_expiry_month'] = 'Please select month';
            }  

            if($b1nT_card_expiry_year == "") {
                $b1nT_field_errors['b1nT_card_expiry_year'] = 'Please select year';
            } 

            if($b1nT_card_cvv == "") {
                $b1nT_field_errors['b1nT_card_cvv'] = 'Please enter cvv';
            } else if(preg_match('/^[0-9]{3,4}$/', $b1nT_card_cvv) == 0) {
                $b1nT_field_errors['b1nT_card_cvv'] = 'Please enter valid cvv';
            }
        }

        if(!empty($b1nT_field_errors)) {
            wp_send_json($this->b1nT_response('validation_error', $b1nT_field_errors, ''));
        }

        //make a service request
        $b1nT_handshake = $this->b1nT_server_calls->b1nT_handshake(); //b1nT_handshake() sanitizes values
 
        if($b1nT_handshake && $b1nT_handshake->status == '200') {
            $b1nT_job_address        = sanitize_text_field($_SESSION['b1nT_ssession']['job_address']);
            $b1nT_job_city           = sanitize_text_field($_SESSION['b1nT_ssession']['job_city']);
            $b1nT_job_state          = sanitize_text_field($_SESSION['b1nT_ssession']['job_state']);
            $b1nT_job_zipcode        = sanitize_text_field($_SESSION['b1nT_ssession']['jobZipPostal']);
            $b1nT_ss_billing_country = sanitize_text_field($_SESSION['b1nT_ssession']['billing_country']);
            $b1nT_lat                = sanitize_text_field($_SESSION['b1nT_ssession']['lat']);
            $b1nT_long               = sanitize_text_field($_SESSION['b1nT_ssession']['long']);
            $b1nT_drv_distance       = sanitize_text_field($_SESSION['b1nT_ssession']['job_distance']);
            $b1nT_map_center         = sanitize_text_field($_SESSION['b1nT_ssession']['map_center']);
            $b1nT_date_requested     = sanitize_text_field($_SESSION['b1nT_ssession']['date_requested']);
            $b1nT_service_type       = sanitize_text_field($_SESSION['b1nT_ssession']['service_type']);
            $b1nT_container_size     = sanitize_text_field($_SESSION['b1nT_ssession']['container_size']);
            $b1nT_price_sheet_id     = sanitize_text_field($_SESSION['b1nT_ssession']['priceSheetID']);

            $b1nT_user_order_data = array(
                'key'              => $b1nT_handshake->key,
                'name'             => $b1nT_user_name,
                'billingAddress1'  => $b1nT_billing_address,
                'billingAddress2'  => $b1nT_billing_address_2,
                'billingCity'      => $b1nT_billing_city,
                'billingStateProv' => $b1nT_billing_state,
                'billingZipPostal' => $b1nT_billing_zipcode,
                'billingContry'    => $b1nT_ss_billing_country,
                'billingPhone'     => $b1nT_billing_phone,
                'billingEmail'     => $b1nT_billing_email,
                'jobAddress'       => $b1nT_job_address,
                'jobCity'          => $b1nT_job_city,
                'jobStateProv'     => $b1nT_job_state,
                'jobZipPostal'     => $b1nT_job_zipcode,
                'jobLatitude'      => $b1nT_lat,
                'jobLongitude'     => $b1nT_long,
                'distanceByRoad'   => $b1nT_drv_distance,
                'mapCenter'        => $b1nT_map_center,
                'dateRequested'    => $b1nT_date_requested,
                'serviceType'      => $b1nT_service_type,
                'assetClass'       => $b1nT_container_size,
                'priceSheetID'     => $b1nT_price_sheet_id,
                'note'             => $b1nT_order_note,
                'mode'             => $b1nT_mode
            );

            $b1nT_user_card_data = array();

            if($b1nT_payment_info === "Show") {
                $b1nT_user_card_data['ccardFName']            = $b1nT_payment_first_name;
                $b1nT_user_card_data['ccardLName']            = $b1nT_payment_last_name;
                $b1nT_user_card_data['ccardBillingAddress1']  = $b1nT_payment_address;
                $b1nT_user_card_data['ccardBillingAddress2']  = $b1nT_payment_address_2;
                $b1nT_user_card_data['ccardBillingCity']      = $b1nT_payment_city;
                $b1nT_user_card_data['ccardBillingStateProv'] = $b1nT_payment_state;
                $b1nT_user_card_data['ccardBillingZipPostal'] = $b1nT_payment_zipcode;
                $b1nT_user_card_data['ccardNumber']           = $b1nT_card_number;
                $b1nT_user_card_data['ccardExp']              = $b1nT_card_expiry_month.$b1nT_card_expiry_year;
                $b1nT_user_card_data['ccardCVV']              = $b1nT_card_cvv;
            }

            if(!empty($b1nT_user_card_data)) {
                $b1nT_user_order_data = array_merge($b1nT_user_order_data, $b1nT_user_card_data); 
            } 

            //final step
            $b1nT_place_order_query = $this->b1nT_server_calls->b1nT_place_order($b1nT_user_order_data); //b1nT_place_order() sanitizes values

            $b1nT_message           = "";
            $b1nT_response_message  = "";
            $b1nT_error_message     = "";
            $b1nT_success_thank_you = "";
            $b1nT_test_url          = "";

            $b1nT_order_fields = array();

            if($b1nT_place_order_query->status == '200') {
                $b1nT_response_message  = "Order Placed Successfully, we will contact you soon.";
                $b1nT_success_thank_you = "Thank You";
                $b1nT_message           = 'Place order success.';

                $b1nT_order_fields["OrderID"]      =  $b1nT_place_order_query->OrderID;
                $b1nT_order_fields["OrderDate"]    =  $b1nT_place_order_query->OrderDate;
                $b1nT_order_fields["OrderAddress"] =  $b1nT_place_order_query->OrderAddress;
                $b1nT_order_fields["OrderCity"]    =  $b1nT_place_order_query->OrderCity;
                $b1nT_order_fields["OrderState"]   =  $b1nT_place_order_query->OrderState;
                $b1nT_order_fields["OrderZip"]     =  $b1nT_place_order_query->OrderZip;
            } else {
                $b1nT_error_message = "Sorry..! Something went wrong please try again later.<br>Status: ".$b1nT_place_order_query->status.'<br>Error: '.$b1nT_place_order_query->errorString;
                $b1nT_message       = 'Place order failed.';
            }

            if($b1nT_mode === "TEST") {
                $b1nT_test_url = $b1nT_place_order_query->TestURL;
            }

            $b1nT_thank_you_data = array(
                'b1nT_response_message'  => $b1nT_response_message,
                'b1nT_error_message'     => $b1nT_error_message,
                'b1nT_success_thank_you' => $b1nT_success_thank_you,
                'b1nT_order_fields'      => $b1nT_order_fields,
                'b1nT_test_url'          => $b1nT_test_url
            );           

            $_SESSION = array();
            session_destroy();
            
            wp_send_json($this->b1nT_response('success', $b1nT_message, $b1nT_thank_you_data));
        } else {
            $b1nT_message = 'Invalid configuration.<br>Status: '.$b1nT_handshake->status.'<br>Error: '.$b1nT_handshake->errorString;
            wp_send_json($this->b1nT_response('error', $b1nT_message, ''));
        }
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
     * Validate the ajax call 
     * 
     * @return 0 or 1
     */
    function b1nT_security_checks() {
        if(!DOING_AJAX) { return 0; } 
        if(!check_ajax_referer('_check__ajax_100', 'b1nT_nonce')) { return 0; }
        return 1;
    }

    /**
     * customize error
     * 
     * @return json
     */
    function b1nT_response($b1nT_status, $b1nT_message, $b1nT_data) {
        $b1nT_json = wp_json_encode(array(
            'status'  => $b1nT_status,
            'message' => $b1nT_message,
            'data'    => $b1nT_data
        ));
        return $b1nT_json;
    }
}