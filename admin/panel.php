<?php
// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'immob_add_defaults');
register_uninstall_hook(__FILE__, 'immob_delete_plugin_options');
add_action('admin_init', 'immob_init' );
add_action('admin_menu', 'immob_add_options_page');
add_filter( 'plugin_action_links', 'immob_plugin_action_links', 10, 2 );

// Delete options table entries ONLY when plugin deactivated AND deleted
function immob_delete_plugin_options() {
	delete_option('immob_options');
}

// Define default option settings
function immob_add_defaults() {
	$tmp = get_option('immob_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('immob_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"chk_button1" => "1",
						"chk_button3" => "1",
						"chk_maps" => "Enter key for Google Maps",
						"chk_default_options_db" => "",
						"chk_mapw" => "500",
						"chk_maph" => "250"
		);
		update_option('immob_options', $arr);
	}
}

// Init plugin options to white list our options
function immob_init(){
	register_setting( 'immob_plugin_options', 'immob_options', 'immob_validate_options' );
}

// Add menu page
function immob_add_options_page() {
	add_menu_page('ImmobilCIO Options Admin Page', 'ImmobilCIO Options', 'manage_options', __FILE__, 'immob_render_form');
}

// Render the Plugin options form
function immob_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php echo __('ImmobilCIO Options Admin Page','immobilcio'); ?></h2>
		<p><?php echo __('These are the options and instructions to configure and use ImmobilCIO.', 'immobilcio'); ?></p>

		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('immob_plugin_options'); ?>
			<?php $options = get_option('immob_options'); ?>

			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<table class="form-table">

				<tr>
					<th scope="row"><?php echo __("Currency",'immobilcio'); ?></th>
					<td>
						<select name='immob_options[chk_currency]'>
							<option value='$' <?php selected('usa', isset($options['chk_currency'])); ?>>$</option>
							<option value='&euro;' <?php selected('eu', isset($options['chk_currency'])); ?>>&euro;</option>
						</select><br />
						<span style="color:#666666;margin-left:2px;">Select default currency.</span>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo __("Activate Google Maps", 'immobilcio'); ?></th>
					<td>
						<label><input name="immob_options[act_maps]" type="checkbox" value="1" <?php if (isset($options['act_maps'])) { checked('1', $options['act_maps']); } ?> /><br />
						<span style="color:#666666;margin-left:2px;">
							<?php echo __("If you can activate maps of real estate in single post, you can check this option.<br /><span style=\"color:#666666;margin-left:2px;\">You can use shortcode for inserting map on your theme</span>",'immobilcio'); ?></label>
						</span>
						</label>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo __("Width - Google Maps", 'immobilcio'); ?></th>
					<td>
						<label><input type="text" size="5" name="immob_options[chk_mapw]" value="<?php echo $options['chk_mapw']; ?>" /><br />
						<span style="color:#666666;margin-left:2px;">
							<?php echo __("Insert width in pixel", 'immobilcio'); ?></label>
						</span>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __("Height - Google Maps", 'immobilcio'); ?></th>
					<td>
						<label><input type="text" size="5" name="immob_options[chk_maph]" value="<?php echo $options['chk_maph']; ?>" /><br />
						<span style="color:#666666;margin-left:2px;">
							<?php echo __("Insert height in pixel", 'immobilcio'); ?></label>
						</span>
						</label>
					</td>
				</tr>
				
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

		<p style="margin-top:15px;">
			<p style="font-style: italic;font-weight: bold;color: #26779a;">If you want to support the development of ImmobilCIO can think about <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TCSXDWVY8VC5W" target="_blank" style="color:#72a1c6;">making a donation</a>. The developers will thank you :-)
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="ZYDK3C2X89Z4E">
<input type="image" src="https://www.paypalobjects.com/it_IT/IT/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - Il metodo rapido, affidabile e innovativo per pagare e farsi pagare.">
<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
</form>
			</p>
		<p>
			<span><a href="www.facebook.com/pages/CMS-Italia-BETA/151738647367" title="Our Facebook page" target="_blank"><img src="<?php echo plugins_url('/images/facebook.png' , __FILE__ ); ?>" /></a></span>
			&nbsp;&nbsp;<span><a href="http://twitter.com/KING_Hack/" title="Follow on Twitter" target="_blank"><img src="<?php echo plugins_url('/images/twitter.png' , __FILE__ ); ?>" /></a></span>
			&nbsp;&nbsp;<span><a href="https://plus.google.com/114145114721414733114" title="CMS-Italia.org" target="_blank"><img src="<?php echo plugins_url('/images/googleplus.png' , __FILE__ ); ?>" /></a></span>
			&nbsp;&nbsp;<span><a href="http://www.CMS-Italia.org" title="CMS-Italia.org" target="_blank"><img src="<?php echo plugins_url('/images/rss.png' , __FILE__ ); ?>" /></a></span>
		</p>

	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function immob_validate_options($input) {
	 // strip html from textboxes
	$input['textarea_one'] =  wp_filter_nohtml_kses(isset($input['textarea_one'])); // Sanitize textarea input (strip html tags, and escape characters)
	$input['chk_maps'] =  wp_filter_nohtml_kses(isset($input['chk_maps'])); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
}

// Display a Settings link on the main Plugins page
function immob_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$immob_links = '<a href="'.get_admin_url().'options-general.php?page=/index.php">'.__('Settings','immobilcio').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $immob_links );
	}

	return $links;
}

?>