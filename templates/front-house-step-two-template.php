<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div  id="b1nT-second-step" style="display:none;" class="b1nT_panel-block">
    <div class="b1nT_label-field">Tell us about your Job:</div>
    <form  method="post"  action="<?php echo esc_url(get_permalink()); ?>" id="b1nT_about_job_form">
        <input type="hidden" name="b1nT_step" value="2"/>
        <div class="b1nT_row-column">
            <div class="b1nT_column-33">
                <div class="b1nT_field-row">
                    <label>Date Requested:<span style="color: red">*</span></label>
                    <input type="text" id="b1nT_date_requested" class="b1nT_form-input " name="b1nT_date_requested" value=""/>
                </div>
            </div>
            <div class="b1nT_column-33">
                <div class="b1nT_field-row">
                    <label>Service Type:<span style="color: red">*</span> </label>
                    <select name="b1nT_service_type"  id="b1nT_service_type" class="b1nT_form-input ">
                    </select>
                </div>
            </div>
            <div class="b1nT_column-33">
                <div class="b1nT_field-row">
                    <label>Size:<span style="color: red">*</span> </label>
                    <select name="b1nT_container_size"  id="b1nT_container_size" class="b1nT_form-input b1nT_container_size"></select>
                </div>
            </div>
        </div>
        <div class="b1nT_row-column">
            <div class="b1nT_column-33">
                <div class="b1nT_field-row">
                    <span id="b1nT_driving_distance" name="b1nT_driving_distance"></span>
                    <span id="b1nT_container_availability" name="b1nT_container_availability"></span>
                    <span id="b1nT_street_address" name="b1nT_street_address"></span>
                    <span id="b1nT_step_two_error" name="b1nT_step_two_error"></span>
                    <label  style="margin-top: 14px;"><b> Click "Next" for Pricing. </b></label>
                </div>
            </div>
        </div>
        <button type="submit" disabled style="display: none" aria-hidden="true"></button>
        <button name="b1nT_about_job_back" id="b1nT_about_job_back" class="b1nT_form-button b1nT_back-btn" value="Back">Back</button>
        <button name="b1nT_about_job_submit" id="b1nT_about_job_submit" class="b1nT_form-button" value="Next">Next</button>
        <label><img id="b1nT_loader" class="b1nT_loader" src="<?php echo esc_url($this->b1nT_plugin_url); ?>images/fading_squares.gif" style=" padding-top: 5px; " /></label>
        <div class="b1nT_row-column">
           <div class="column-11">
                <div class="b1nT_availability_table" id="b1nT_availability_table" style="padding-top: 50px; overflow-x: auto;">
                </div>
           </div>
        </div>
        <?php if (esc_attr(get_option('b1nT_admin_debug')) == 'On') { ?> 
            <div class="b1nT_row-column">
                <div class="column-11">
                    <h3>Pricing Query JSON Response</h3>
                    <pre>
                        <div class="b1nT_pricingquery_response_show" id="b1nT_pricingquery_response_show"></div>
                    </pre>
                </div>
            </div>
        <?php } ?>
    </form>
</div>
      