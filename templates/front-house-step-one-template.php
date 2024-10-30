<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<h1 class="entry-title"><?php echo esc_attr(get_option('b1nT_page_title')); ?></h1>

<div id="b1nT-first-step" class="b1nT_panel-block">
    <input type="hidden" id="b1nT_google_status_bypass" value="<?php if(esc_attr(get_option('b1nT_google_validation_bypass'))) {
        echo esc_attr(get_option('b1nT_google_validation_bypass')); 
    } else {
        echo "Off"; 
    } ?>"/>

	<div class="b1nT_label-field">Enter address to begin:</div>
	<form  method="post"  action="<?php echo esc_url(get_permalink()); ?>" id="b1nT_address_form">
	    <input type="hidden" class="b1nT_form-input" name="b1nT_step" value="1"/>
		<div class="b1nT_row-column">
            <div class="b1nT_column-33">
                <div class="b1nT_field-row">
                    <label>Job Address:<span style="color: red">*</span></label>
                    <input type="text" id="b1nT_job_address" class="b1nT_form-input " name="b1nT_job_address" value=""/>
                    <div style="padding-top: 5px; font-size: 90%;">
                        <strong>
                            <em>GOOGLE STATUS: <span name="b1nT_google_status" class="b1nT_verify_error">NOT VERIFIED</span></em>
                        </strong>
                    </div>
                    <div id="b1nT_google_search_error_shield" class="b1nT_dialog_box_shield"></div>
                    <div id="b1nT_google_search_error_dialog" class="b1nT_dialog_box_div"></div>
                </div>
            </div>
            <div class="b1nT_column-22">
                <div class="b1nT_field-row">
                    <label>Job City:<span style="color: red">*</span></label>
                    <input type="text" id="b1nT_job_city" class="b1nT_form-input " name="b1nT_job_city" value=""/>
                </div>
            </div>
            <div class="b1nT_column-22">
                <div class="b1nT_field-row">
                    <label>Job State:<span style="color: red">*</span></label>
                    <select name="b1nT_job_state"  id="b1nT_job_state" class="b1nT_form-input "></select>
                </div>
            </div>
	        <div class="b1nT_column-22">
	            <div class="b1nT_field-row">
	                <label><?php echo(esc_attr(get_option('b1nT_zipcode_label')) == "") ? 'Zipcode' : esc_attr(get_option('b1nT_zipcode_label')); ?>:<span style="color: red">*</span></label>
	                <input type="text" id="b1nT_user_zipcode" class="b1nT_form-input" name="b1nT_user_zipcode" value=""/>
	            </div>  
	        </div>
	 	</div>
		<div class="b1nT_row-column">
            <div class="b1nT_column-33">
                <div class="b1nT_field-row">
                    <span id="b1nT_step_one_error" name="b1nT_step_one_error"></span>
                </div>
            </div>
        </div>
        <button type="submit" disabled style="display: none" aria-hidden="true"></button>
        <button name="b1nT_address_submit" id="b1nT_address_submit" class="b1nT_form-button" value="Next">Next</button>
       	<label><img id="b1nT_loader" class="b1nT_loader" src="<?php echo esc_url($this->b1nT_plugin_url); ?>images/fading_squares.gif" style="z-index: 9999; padding-top: 25px; " /></label> 
	</form>
</div>