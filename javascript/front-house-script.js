jQuery(document).ready(function($){
    var b1nT_first_step=$('#b1nT-first-step');
    var b1nT_second_step=$('#b1nT-second-step');
    var b1nT_third_step=$('#b1nT-third-step');
    var b1nT_fourth_step=$('#b1nT-fourth-step');
    var b1nT_job_address_vldtr;

    $('form').each(function() { this.reset() });

    $("#b1nT_address_submit").click(function(e) {
        $('.b1nT-error-msg').remove();
        if($('span[name="b1nT_google_status"]').attr('class') == "b1nT_verify_error") {
            $('#b1nT_step_one_error').after('<p class="b1nT-error-msg">Please provide a google validated address.</p>');
            return false;
        }

        e.preventDefault();       
        var formData = {
            'action': 'b1nT_get_job',
            'b1nT_job_address': $('input[name=b1nT_job_address]').val(),
            'b1nT_job_city': $('input[name=b1nT_job_city]').val(),
            'b1nT_job_state': $('select[name=b1nT_job_state]').val(),
            'b1nT_user_zipcode': $('input[name=b1nT_user_zipcode]').val(),
            'b1nT_nonce': b1nT_config.ajax_nonce, 
            'b1nT_step'    : 1
        };

        $(".b1nT_loader").show();
        
        $.ajax({
            url : b1nT_config.ajax_url,
            type : 'post',
            data : formData,
            success : function( data ) {
                $(".b1nT_loader").hide();
                var response= JSON.parse(data);
                if(response.status=='validation_error'){
                    $.each(response.message, function (field, field_message) {
                        $('#'+field).after('<p class="b1nT-error-msg">'+field_message+'</p>');
                    });
                } else if(response.status=='success'){
                    $('#b1nT_service_type').html(response.data.b1nT_service_types);
                    $('#b1nT_container_size').html(response.data.b1nT_container_sizes);
                    $('#b1nT_date_requested').val(response.data.b1nT_date_requested);
                    $('#b1nT_date_diff').val(response.data.b1nT_date_diff);
                    $('#b1nT_availability_table').html(response.data.b1nT_availability_table_show_view);
                    $('#b1nT_pricingquery_response_show').html(JSON.stringify(response.data.b1nT_pricingquery_response));
                    $("#b1nT_date_requested").datepicker({ dateFormat: 'M dd,yy', minDate: response.data.b1nT_date_diff, maxDate : response.data.b1nT_date_last});

                    b1nT_first_step.hide();
                    b1nT_second_step.show();
                    b1nT_third_step.hide();
                    b1nT_fourth_step.hide();

                    b1nT_set_terms_behaviors(response.data.b1nT_termsURL, response.data.b1nT_apiUseTerms);
                } else{
                    $('#b1nT_step_one_error').after('<p class="b1nT-error-msg">'+response.message+'</p>');
                }
            }, error: function (error) {
                $('#b1nT_step_one_error').after('<p class="b1nT-error-msg">Oops. Something went wrong. Please try again later.</p>');
            }
        });
    });

    function b1nT_set_terms_behaviors(b1nT_terms_url, b1nT_api_use_terms) {
        if((b1nT_api_use_terms * 1) == 0 || b1nT_terms_url == "") { 
            $('#b1nT_place_order_submit').attr("class",    "b1nT_form-button");
            $('#b1nT_place_order_submit').attr("disabled", false);
            return; 
        }

        $('#b1nT_terms_cond_check_box_link').attr("href", b1nT_terms_url);

        //we are going to use the check box to 
        //enable or disable the place order button
        $('#b1nT_terms_cond_check_box').change(function(e){
            if($('#b1nT_terms_cond_check_box').is(':checked')) {
                $('#b1nT_place_order_submit').attr("class",    "b1nT_form-button");
                $('#b1nT_place_order_submit').attr("disabled", false);
                return;
            }

            $('#b1nT_place_order_submit').attr("class",    "b1nT_form-button b1nT_form-disabled");
            $('#b1nT_place_order_submit').attr("disabled", true);
        });

        $('#b1nT_terms_cond_wrapper').css("display", "block");
    }

    $("#b1nT_about_job_submit" ).click(function(e) {
        $('.b1nT-error-msg').remove();
        if($('span[name="b1nT_google_status"]').attr('class') == "b1nT_verify_error") {
            $('#b1nT_step_one_error').after('<p class="b1nT-error-msg">Please provide a google validated address.</p>');
            return false;
        }

        e.preventDefault();
        var formData = {
            'action': 'b1nT_about_job',
            'b1nT_service_type': $('select[name=b1nT_service_type]').val(),
            'b1nT_container_size': $('select[name=b1nT_container_size]').val(),
            'b1nT_date_requested': $('input[name=b1nT_date_requested]').val(),
            'b1nT_nonce': b1nT_config.ajax_nonce,
            'b1nT_step'    :2
        };

        $(".b1nT_loader").show();

        $.ajax({
            url : b1nT_config.ajax_url,
            type : 'post',
            data : formData,
            success : function( data ) {
                $(".b1nT_loader").hide();
                var response= JSON.parse(data);
                if(response.status=='validation_error'){
                    $.each(response.message, function (field, field_message) {
                        $('#'+field).after('<p class="b1nT-error-msg">'+field_message+'</p>');
                    });
                } else if(response.status=='success'){
                    $('#b1nT_billing_state').html(response.data.b1nT_billing_state_list);
                    $('#b1nT_payment_state').html(response.data.b1nT_payment_state_list);
                    $("b1nT_total_amount").text(response.data.b1nT_total_amount);
                    $("b1nT_price_sheets_sub_total").text(response.data.b1nT_price_sheets_sub_total);
                    $("b1nT_price_sheets_taxes").text(response.data.b1nT_price_sheets_taxes);
                    $("b1nT_price_sheets_days").text(response.data.b1nT_price_sheets_days);
                    $("b1nT_price_sheets_days_price").text(response.data.b1nT_price_sheets_days_price);
                    $("b1nT_price_sheets_units_included").text(response.data.b1nT_price_sheets_units_included);
                    $("b1nT_price_sheets_unit").text(response.data.b1nT_price_sheets_unit);
                    $("b1nT_price_sheets_excess_units").text(response.data.b1nT_price_sheets_excess_units);

                    b1nT_first_step.hide();
                    b1nT_second_step.hide();
                    b1nT_third_step.show();
                    b1nT_fourth_step.hide();
                } else {
                    $('#b1nT_step_two_error').after('<p class="b1nT-error-msg">'+response.message+'</p>');
                }
            }, error: function (error) {
               $('#b1nT_step_two_error').after('<p class="b1nT-error-msg">Oops. Something went wrong. Please try again later.</p>');
            }
        });
    });

    $( "#b1nT_place_order_submit" ).click(function(e) {
        $('.b1nT-error-msg').remove();
        if($('span[name="b1nT_google_status"]').attr('class') == "b1nT_verify_error") {
            $('#b1nT_step_one_error').after('<p class="b1nT-error-msg">Please provide a google validated address.</p>');
            return false;
        }

        e.preventDefault();
        var formData = {
            'action': 'b1nT_place_order',
            'b1nT_user_name': $('input[name=b1nT_user_name]').val(),
            'b1nT_billing_address': $('input[name=b1nT_billing_address]').val(),
            'b1nT_billing_address_2': $('input[name=b1nT_billing_address_2]').val(),
            'b1nT_billing_city': $('input[name=b1nT_billing_city]').val(),
            'b1nT_billing_state': $('select[name=b1nT_billing_state]').val(),
            'b1nT_billing_zipcode': $('input[name=b1nT_billing_zipcode]').val(),                    
            'b1nT_billing_phone': $('input[name=b1nT_billing_phone]').val(),
            'b1nT_billing_email': $('input[name=b1nT_billing_email]').val(),
            'b1nT_payment_first_name': $('input[name=b1nT_payment_first_name]').val(),
            'b1nT_payment_last_name': $('input[name=b1nT_payment_last_name]').val(),
            'b1nT_payment_address': $('input[name=b1nT_payment_address]').val(),
            'b1nT_payment_address_2': $('input[name=b1nT_payment_address_2]').val(),
            'b1nT_payment_city': $('input[name=b1nT_payment_city]').val(),
            'b1nT_payment_state': $('select[name=b1nT_payment_state]').val(),
            'b1nT_payment_zipcode': $('input[name=b1nT_payment_zipcode]').val(),
            'b1nT_order_note': $('textarea[name=b1nT_order_note]').val(),
            'b1nT_card_number': $('input[name=b1nT_card_number]').val(),
            'b1nT_card_expiry_month': $('select[name=b1nT_card_expiry_month]').val(),
            'b1nT_card_expiry_year': $('select[name=b1nT_card_expiry_year]').val(),
            'b1nT_card_cvv': $('input[name=b1nT_card_cvv]').val(),
            'b1nT_nonce': b1nT_config.ajax_nonce,
            'b1nT_step' :3
        };

        $(".b1nT_loader").show();

        $.ajax({
            url : b1nT_config.ajax_url,
            type : 'post',
            data : formData,
            success : function( data ) {
                $(".b1nT_loader").hide();
                var response= JSON.parse(data);
                if(response.status=='validation_error'){
                    $.each(response.message, function (field, field_message) {
                        $('#'+field).after('<p class="b1nT-error-msg">'+field_message+'</p>');
                    });
                } else if(response.status=='success'){
                     b1nT_first_step.hide();
                     b1nT_second_step.hide();
                     b1nT_third_step.hide();
                     b1nT_fourth_step.show();

                     $('#b1nT_response_message').html(response.data.b1nT_response_message);
                     $('#b1nT_success_thank_you').html(response.data.b1nT_success_thank_you);

                     //display order details if valid
                     if(response.data.b1nT_order_fields.OrderID){
                        $('#b1nT_response_order_id').html("Order# "+response.data.b1nT_order_fields.OrderID);
                     }

                     if(response.data.b1nT_order_fields.OrderDate) {
                        $('#b1nT_response_order_date').html(response.data.b1nT_order_fields.OrderDate);
                     }

                     if(response.data.b1nT_order_fields.OrderAddress) {
                        var b1nT_full_address = response.data.b1nT_order_fields.OrderAddress;

                        //include other details if they are present.
                        if(response.data.b1nT_order_fields.OrderCity) {
                            b1nT_full_address += (" "+response.data.b1nT_order_fields.OrderCity);
                        }

                        if(response.data.b1nT_order_fields.OrderState) {
                            b1nT_full_address += (", "+response.data.b1nT_order_fields.OrderState);
                        }

                        if(response.data.b1nT_order_fields.OrderZip) {
                           b1nT_full_address += (" "+response.data.b1nT_order_fields.OrderZip); 
                        }

                        $('#b1nT_response_order_address').html(b1nT_full_address);
                     }

                     $('#b1nT_error_message').html(response.data.b1nT_error_message);
                     $('#b1nT_test_url').html(response.data.b1nT_test_url);
                } else{
                   $('#b1nT_step_three_error').after('<p class="b1nT-error-msg">'+response.message+'</p>');
                }
            }, error: function (error) {
               $('#b1nT_step_three_error').after('<p class="b1nT-error-msg">Oops. Something went wrong. Please try again later.</p>');
            }
        });
    });

    $( "#b1nT_about_job_back" ).click(function(e) {
        $('.b1nT-error-msg').remove();
        e.preventDefault();       
        var formData = {
            'action': 'b1nT_get_job',
            'b1nT_job_address': $('input[name=b1nT_job_address]').val(),
            'b1nT_job_city': $('input[name=b1nT_job_city]').val(),
            'b1nT_job_state': $('select[name=b1nT_job_state]').val(),
            'b1nT_user_zipcode': $('input[name=b1nT_user_zipcode]').val(),
            'b1nT_nonce': b1nT_config.ajax_nonce,
            'b1nT_step'    : 2
        };

        $(".b1nT_loader").show();

        $.ajax({
            url : b1nT_config.ajax_url,
            type : 'post',
            data : formData,
            success : function( data ) {
                $(".b1nT_loader").hide();
                var response= JSON.parse(data);
                if(response.status=='validation_error'){
                    $('#b1nT_step_two_error').after('<p class="b1nT-error-msg">Something went wrong, validation error.</p>');
                } else if(response.status=='success'){
                    $('#b1nT_service_type').html(response.data.b1nT_service_types);
                    $('#b1nT_container_size').html(response.data.b1nT_container_sizes);
                    $('#b1nT_date_requested').val(response.data.b1nT_date_requested);

                    b1nT_first_step.show();
                    b1nT_second_step.hide();
                    b1nT_third_step.hide();
                    b1nT_fourth_step.hide();

                    b1nT_set_terms_behaviors(response.data.b1nT_termsURL, response.data.b1nT_apiUseTerms);
                } else{
                    $('#b1nT_step_two_error').after('<p class="b1nT-error-msg">Something went wrong.</p>');
                }
            }, error: function (error) {
                $('#b1nT_step_two_error').after('<p class="b1nT-error-msg">Oops. Something went wrong. Please try again later.</p>');
            }
        });
    });

    $( "#b1nT_place_order_back" ).click(function(e) {
        $('.b1nT-error-msg').remove();
        e.preventDefault();       
        var formData = {
            'action': 'b1nT_get_job',
            'b1nT_job_address': $('input[name=b1nT_job_address]').val(),
            'b1nT_job_city': $('input[name=b1nT_job_city]').val(),
            'b1nT_job_state': $('select[name=b1nT_job_state]').val(),
            'b1nT_user_zipcode': $('input[name=b1nT_user_zipcode]').val(),
            'b1nT_nonce': b1nT_config.ajax_nonce,
            'b1nT_step'    : 3
        };

        $(".b1nT_loader").show();

        $.ajax({
            url : b1nT_config.ajax_url,
            type : 'post',
            data : formData,
            success : function( data ) {
                $(".b1nT_loader").hide();
                var response= JSON.parse(data);
                if(response.status=='validation_error'){
                    $('#b1nT_step_three_error').after('<p class="b1nT-error-msg">Something went wrong, validation error.</p>');
                } else if(response.status=='success'){
                    $('#b1nT_service_type').html(response.data.b1nT_service_types);
                    $('#b1nT_container_size').html(response.data.b1nT_container_sizes);
                    $('#b1nT_date_requested').val(response.data.b1nT_date_requested);

                    b1nT_first_step.hide();
                    b1nT_second_step.show();
                    b1nT_third_step.hide();
                    b1nT_fourth_step.hide();

                    b1nT_set_terms_behaviors(response.data.b1nT_termsURL, response.data.b1nT_apiUseTerms);
                } else{
                    $('#b1nT_step_three_error').after('<p class="b1nT-error-msg">Something went wrong.</p>');
                }
            }, error: function (error) {
                $('#b1nT_step_three_error').after('<p class="b1nT-error-msg">Oops. Something went wrong. Please try again later.</p>');
            }
        });
    });

    $( "#b1nT_date_requested" ).change(function(e) { 
        $('.b1nT-error-msg').remove();
        e.preventDefault();
        var formData = {
            'action': 'b1nT_availability_search',
            'b1nT_date_requested': $('input[name=b1nT_date_requested]').val(),
            'b1nT_nonce': b1nT_config.ajax_nonce
        };

        $.ajax({
            url : b1nT_config.ajax_url,
            type : 'post',
            data : formData,
            success : function( data ) {
                var response= JSON.parse(data);
                if(response.status=='validation_error'){
                    $('#b1nT_container_availability').after('<p class="b1nT-error-msg">'+response.message.b1nT_container_availability+'</p>');
                    $('#b1nT_container_size').html(response.data.b1nT_container_size);
                } else if(response.status=='success'){
                    $('#b1nT_container_size').html(response.data.b1nT_container_size);
                }
            }, error: function (error) {
               $('#b1nT_step_two_error').after('<p class="b1nT-error-msg">Oops. Something went wrong. Please try again later.</p>');
            }
        });
    });

    $("input#b1nT_sameadd").bind("click",function(o){
        if($("input#b1nT_sameadd:checked").length){
            var result = $("#b1nT_user_name").val().toString();
            var array = result.split(' ');
            b1nT_user_first_name = array[0], b1nT_user_last_name = array[1];
            $("#b1nT_payment_first_name").val(b1nT_user_first_name);
            $("#b1nT_payment_last_name").val(b1nT_user_last_name);
            $("#b1nT_payment_address").val($("#b1nT_billing_address").val());
            $("#b1nT_payment_address_2").val($("#b1nT_billing_address_2").val());
            $("#b1nT_payment_city").val($("#b1nT_billing_city").val());
            $("#b1nT_payment_state").val($("#b1nT_billing_state").val());
            $("#b1nT_payment_zipcode").val($("#b1nT_billing_zipcode").val());
        } else {
            $("#b1nT_payment_first_name").val("");
            $("#b1nT_payment_last_name").val("");
            $("#b1nT_payment_address").val("");
            $("#b1nT_payment_address_2").val("");
            $("#b1nT_payment_city").val("");
            $("#b1nT_payment_state").val("");
            $("#b1nT_payment_zipcode").val("");
        }
    });

    $("input#b1nT_billing_details_sameadd").bind("click",function(o){
        if($("input#b1nT_billing_details_sameadd:checked").length){
            $('.b1nT-error-msg').remove();
            var formData = {
                'action': 'b1nT_job_details',
                'b1nT_nonce': b1nT_config.ajax_nonce
            };

            $.ajax({
                url : b1nT_config.ajax_url,
                type : 'post',
                data : formData,
                success : function( data ) {
                    var response= JSON.parse(data);
                    if(response.status=='success'){
                        $("#b1nT_billing_address").val(response.data.b1nT_billing_address);
                        $("#b1nT_billing_city").val(response.data.b1nT_billing_city);
                        $("#b1nT_billing_state").val(response.data.b1nT_billing_state);
                        $("#b1nT_billing_zipcode").val(response.data.b1nT_billing_zipcode);
                    } else {
                        $('#b1nT_step_three_billing_details_error').after('<p class="b1nT-error-msg">'+response.message+'</p>');
                    }
                }, error: function (error) {
                   $('#b1nT_step_three_billing_details_error').after('<p class="b1nT-error-msg">Oops. Something went wrong. Please try again later.</p>');
                }
            });
        } else {
            $("#b1nT_billing_address").val("");
            $("#b1nT_billing_city").val("");
            $("#b1nT_billing_state").val("");
            $("#b1nT_billing_zipcode").val("");
        }
    });

    $(function () { 
        if(typeof(b1nT_cls_address_vldtr) == 'function') {
            b1nT_job_address_vldtr = new b1nT_cls_address_vldtr({
                "searchCtrl" : $('input[name=b1nT_job_address]')[0],
                "addressCtrl" : $('input[name=b1nT_job_address]')[0],
                "cityCtrl" : $('input[name=b1nT_job_city]')[0],
                "stateCtrl" : $('select[name=b1nT_job_state]')[0],
                "postalCtrl" : $('input[name=b1nT_user_zipcode]')[0],
                "validateCtrl" : $('span[name=b1nT_google_status]')[0],
                "errorShield" : $('#b1nT_google_search_error_shield')[0],
                "errorDialog" : $('#b1nT_google_search_error_dialog')[0]
            });
        }        
    });

    $(function () {
        var formData = {
            'action': 'b1nT_state_list',
            'b1nT_nonce': b1nT_config.ajax_nonce
        };

        $.ajax({
            url : b1nT_config.ajax_url,
            type : 'post',
            data : formData,
            success : function( data ) {
                var response= JSON.parse(data);
                if(response.status=='success'){
                    $('#b1nT_job_state').html(response.data.b1nT_state_list);
                } else {
                    $('#b1nT_step_one_error').after('<p class="b1nT-error-msg">'+response.message+'</p>');
                }
            }, error: function (error) {
               $('#b1nT_step_one_error').after('<p class="b1nT-error-msg">Oops. Something went wrong. Please try again later.</p>');
            }
        });
    });
});

jQuery(function($){
    $("[b1nT_data-pop]").click(function(){
        var b1nT_data_pop_value = $(this).attr("b1nT_data-pop");
        var $b1nT_popup = b1nT_data_pop_value ? $("[b1nT_data-popup='"+b1nT_data_pop_value+"']") : $(this).closest("[b1nT_data-popup]");
        $b1nT_popup.slideToggle(240);
    });
});
