<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
	<div id="b1nT_setting_logo_div">
		<?php echo '<img src="'.esc_url($this->b1nT_plugin_url).'images/logo.png'.'">'; ?>
	</div>

	<?php settings_errors(); ?>

	<form id="b1nT_setting_form" method="post" action="options.php">
		<?php
			settings_fields('b1nT_setting_group');
			do_settings_sections('bin-tracker-online');
		?>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Page Title</th>
					<td><input type="text" name="b1nT_page_title" value="<?php echo esc_attr(get_option('b1nT_page_title')); ?>"></td>
				</tr>
				<tr valign="top">
					<th scope="row">Username</th>
					<td><input type="text" name="b1nT_username" value="<?php echo  esc_attr(get_option('b1nT_username')); ?>"></td>
				</tr>
				<tr valign="top">
					<th scope="row">Password</th>
					<td><input type="password" name="b1nT_password" value="<?php echo esc_attr(get_option('b1nT_password')); ?>"></td>
				</tr>
				<tr valign="top">
					<th scope="row">Postalcode/Zipcode Label </th>
					<td><input type="text" name="b1nT_zipcode_label" value="<?php echo esc_attr(get_option('b1nT_zipcode_label')); ?>"></td>
				</tr>
				<tr valign="top">
					<th scope="row">Country</th>
					<td>
						<select name="b1nT_admin_country">
							<option value="United States" <?php selected(esc_attr(get_option('b1nT_admin_country')), 'United States'); ?>>United States</option>
							<option value="Canada" <?php selected(esc_attr(get_option('b1nT_admin_country')), 'Canada'); ?>>Canada</option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Google API Key</th>
					<td>
						<input type="text" name="b1nT_google_api_key" value="<?php echo esc_attr(get_option('b1nT_google_api_key')); ?>">
						<a href="https://developers.google.com/places/web-service/get-api-key" target="_blank">How to get api key?</a>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Bypass Google Validation</th>
					<td>
						<select name="b1nT_google_validation_bypass">
							<option value="On"  <?php selected(esc_attr(get_option('b1nT_google_validation_bypass')), 'On');  ?>>On</option>
							<option value="Off" <?php if(esc_attr(get_option('b1nT_google_validation_bypass'))) { 
								selected(esc_attr(get_option('b1nT_google_validation_bypass')), 'Off'); 
							} else { 
								echo 'selected="selected"'; 
							} ?>>Off</option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Payment Info</th>
					<td>
						<select name="b1nT_payment_info">
							<option value="Show" <?php selected(esc_attr(get_option('b1nT_payment_info')), 'Show'); ?>>Show</option>
							<option value="Hide" <?php selected(esc_attr(get_option('b1nT_payment_info')) ,'Hide'); ?>>Hide</option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Mode</th>
					<td>
						<select name="b1nT_mode">
							<option value="TEST" <?php selected(esc_attr(get_option('b1nT_mode')), 'TEST'); ?>>Test</option>
							<option value="LIVE" <?php selected(esc_attr(get_option('b1nT_mode')), 'LIVE'); ?>>Live</option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Debug</th>
					<td>
						<select name="b1nT_admin_debug">
							<option value="On" <?php selected(esc_attr(get_option('b1nT_admin_debug')), 'On'); ?>>On</option>
							<option value="Off" <?php selected(esc_attr(get_option('b1nT_admin_debug')), 'Off'); ?>>Off</option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">IP Address</th>
					<td>
						<input type="text" name="b1nT_ip_address" id="b1nT_ip_address" value="" disabled>
						<input type="button" id="b1nT_check_details_ip" class="button-primary" value="Check Details" disabled/>
					</td>
				</tr>
			</tbody>
		</table>

		<?php submit_button(); ?>
	</form>
</div>