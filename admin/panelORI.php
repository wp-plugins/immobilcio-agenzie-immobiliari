<?php
// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'immob_add_defaults');
register_uninstall_hook(__FILE__, 'immob_delete_plugin_options');
add_action('admin_init', 'immob_init' );
add_action('admin_menu', 'immob_add_options_page');
add_filter( 'plugin_action_links', 'immob_plugin_action_links', 10, 2 );

// --------------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_uninstall_hook(__FILE__, 'immob_delete_plugin_options')
// --------------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE USER DEACTIVATES AND DELETES THE PLUGIN. IT SIMPLY DELETES
// THE PLUGIN OPTIONS DB ENTRY (WHICH IS AN ARRAY STORING ALL THE PLUGIN OPTIONS).
// --------------------------------------------------------------------------------------

// Delete options table entries ONLY when plugin deactivated AND deleted
function immob_delete_plugin_options() {
	delete_option('immob_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'immob_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function immob_add_defaults() {
	$tmp = get_option('immob_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('immob_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"chk_button1" => "1",
						"chk_button3" => "1",
						"textarea_one" => "This type of control allows a large amount of information to be entered all at once. Set the 'rows' and 'cols' attributes to set the width and height.",
						"textarea_two" => "This text area control uses the TinyMCE editor to make it super easy to add formatted content.",
						"textarea_three" => "Another TinyMCE editor! It is really easy now in WordPress 3.3 to add one or more instances of the built-in WP editor.",
						"chk_maps" => "Enter key for Google Maps",
						"drp_select_box" => "four",
						"chk_default_options_db" => "",
						"rdo_group_one" => "one",
						"rdo_group_two" => "two",
						"chk_mapw" => "500",
						"chk_maph" => "250"
		);
		update_option('immob_options', $arr);
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'immob_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function immob_init(){
	register_setting( 'immob_plugin_options', 'immob_options', 'immob_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'immob_add_options_page');
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function immob_add_options_page() {
	add_menu_page('ImmobilCIO Options Admin Page', 'ImmobilCIO Options', 'manage_options', __FILE__, 'immob_render_form');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------

// Render the Plugin options form
function immob_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php echo __('ImmobilCIO Options Admin Page'); ?></h2>
		<p><?php echo __('Below is a collection of sample controls you can use in your own Plugins. Or, you can analyse the code and learn how all the most common controls can be added to a Plugin options form. See the code for more details, it is fully commented.'); ?></p>

		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('immob_plugin_options'); ?>
			<?php $options = get_option('immob_options'); ?>

			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<table class="form-table">

				<!-- Checkbox Buttons -->
				<tr valign="top">
					<th scope="row"><?php echo __("Widget or post/page content"); ?></th>
					<td>
						<label><input name="immob_options[chk_dethome]" type="checkbox" value="1" <?php if (isset($options['chk_dethome'])) { checked('1', $options['chk_dethome']); } ?> /><br />
						<span style="color:#666666;margin-left:2px;">
							<?php echo __("If you want add real estate's details on content post check this option otherwise you can use theme widget"); ?>
						</span>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __("Facebook share button"); ?></th>
					<td>
						<label><input name="immob_options[chk_facebook]" type="checkbox" value="1" <?php if (isset($options['chk_facebook'])) { checked('1', $options['chk_facebook']); } ?> /><br />
						<span style="color:#666666;margin-left:2px;">
							<?php echo __("If you want displayed facebook share button on single real estate page you can check this option."); ?>
						</span>
						</label>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo __("Activate Google Maps"); ?></th>
					<td>
						<label><input name="immob_options[act_maps]" type="checkbox" value="1" <?php if (isset($options['act_maps'])) { checked('1', $options['act_maps']); } ?> /><br />
						<span style="color:#666666;margin-left:2px;">
							<?php echo __("If you can activate maps of real estate in single post, you can check this option.<br /><span style=\"color:#666666;margin-left:2px;\">You can use shortcode for inserting map on your theme</span>"); ?></label>
						</span>
						</label>
					</td>
				</tr>
				<!-- tr valign="top">
					<th scope="row">API Key Google Maps</th>
					<td>
						<label><input type="text" size="57" name="immob_options[chk_maps]" value="<?php echo $options['chk_maps']; ?>" />
						<br /><?php echo __("Insert API Key<br /><span style=\"color:#666666;margin-left:2px;\">You can get API key from this URL: <a href=\"https://developers.google.com/maps/documentation/javascript/tutorial#api_key\">https://developers.google.com/maps/documentation/javascript/tutorial#api_key</a></span>"); ?></label>
					</td>
				</tr -->
				<tr valign="top">
					<th scope="row"><?php echo __("Width - Google Maps"); ?></th>
					<td>
						<label><input type="text" size="5" name="immob_options[chk_mapw]" value="<?php echo $options['chk_mapw']; ?>" />
						<br /></label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __("Height - Google Maps"); ?></th>
					<td>
						<label><input type="text" size="5" name="immob_options[chk_maph]" value="<?php echo $options['chk_maph']; ?>" />
						<br /></label>
					</td>
				</tr>

				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="immob_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon Plugin reactivation</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

		<p style="margin-top:15px;">
			<p style="font-style: italic;font-weight: bold;color: #26779a;">If you have found ImmobilCIO plugin at all useful, please consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XKZXD2BHQ5UB2" target="_blank" style="color:#72a1c6;">donation</a>. Thanks.
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="ZYDK3C2X89Z4E">
<input type="image" src="https://www.paypalobjects.com/it_IT/IT/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - Il metodo rapido, affidabile e innovativo per pagare e farsi pagare.">
<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
</form>
			</p>
			<span><a href="http://www.facebook.com/PressCoders" title="Our Facebook page" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url('/images/facebook-icon.png' , __FILE__ ); ?>" /></a></span>
			&nbsp;&nbsp;<span><a href="http://www.twitter.com/dgwyer" title="Follow on Twitter" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url('/images/twitter-icon.png' , __FILE__ ); ?>" /></a></span>
			&nbsp;&nbsp;<span><a href="http://www.presscoders.com" title="PressCoders.com" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url('/images/pc-icon.png' , __FILE__ ); ?>" /></a></span>
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
		$immob_links = '<a href="'.get_admin_url().'options-general.php?page=/index.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $immob_links );
	}

	return $links;
}

?>