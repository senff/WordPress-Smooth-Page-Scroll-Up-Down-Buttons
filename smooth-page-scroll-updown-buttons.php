<?php
/*
Plugin Name: Smooth Page Scroll Up/Down Buttons
Plugin URI: http://www.senff.com/plugins/smooth-page-scroll-up-down-buttons
Description: Adds buttons to your page that will enable the user to easily (and smoothly) scroll one screen up/down.
Author: Mark Senff
Author URI: http://www.senff.com
Version: 1.0
*/

defined('ABSPATH') or die('F*ck cancer.');


// === FUNCTIONS =========================================================================================================

if (!function_exists('page_scroll_buttons_default_options')) {
	function page_scroll_buttons_default_options() {
		$versionNum = '1.0';
		if (get_option('page_scroll_buttons_options') === false) {
			$new_options['psb_version'] = $versionNum;
			$new_options['psb_positioning'] = '0';
			$new_options['psb_topbutton'] = '';
			$new_options['psb_speed'] = '1200';
			add_option('page_scroll_buttons_options',$new_options);
		} 
	}
}


if (!function_exists('page_scroll_buttons_update')) {
	function page_scroll_buttons_update() {
		// No updates yet.
	}
}


if (!function_exists('load_page_scroll_buttons')) {
    function load_page_scroll_buttons() {

		// Main jQuery plugin file 
	    wp_register_script('pageScrollButtonsLib', plugins_url('/assets/js/smooth-page-scroll-updown-buttons.js', __FILE__), array( 'jquery' ), '1.0');
	    wp_enqueue_script('pageScrollButtonsLib');

		wp_register_style('pageScrollButtonsStyle', plugins_url('/assets/css/smooth-page-scroll-updown-buttons.css', __FILE__) );
	    wp_enqueue_style('pageScrollButtonsStyle');

		$options = get_option('page_scroll_buttons_options');

		// Set defaults when empty (because '' does not seem to work with the JQ plugin) 
		if (!$options['psb_positioning']) {
			$options['psb_positioning'] = '0';
		}

		if (!$options['psb_topbutton']) {
			$options['psb_topbutton'] = '';
		}

		if (!$options['psb_speed']) {
			$options['psb_speed'] = '1200';
		}

		$script_vars = array(
		      'positioning' => $options['psb_positioning'],
		      'topbutton' => $options['psb_topbutton'],
		      'speed' => $options['psb_speed']
		);

		wp_enqueue_script('addButtons', plugins_url('/assets/js/addButtons.js', __FILE__), array( 'jquery' ), '1.1', true);
		wp_localize_script( 'addButtons', 'add_buttons_engage', $script_vars );

    }
}

if (!function_exists('page_scroll_buttons_menu')) {
    function page_scroll_buttons_menu() {
		add_options_page( 'Smooth Page Scroll Up/Down Buttons Configuration', 'Smooth Page Scroll Up/Down Buttons', 'manage_options', 'pagescrollupdownmenu', 'page_scroll_up_down_buttons_config_page' );
    }
}

if (!function_exists('page_scroll_up_down_buttons_config_page')) {
	function page_scroll_up_down_buttons_config_page() {
	// Retrieve plugin configuration options from database
	$page_scroll_buttons_options = get_option( 'page_scroll_buttons_options' );
	?>

	<div id="page-scroll-up-down-buttons-settings-general" class="wrap">
		<h2>Smooth Page Scroll Up/Down Buttons Settings</h2>

		<p>Adding UP/DOWN buttons will enable visitors of your site to scroll smoothly, scrolling one page at a time. Handy for pages with a lot of text/content, or wherever a browser's scrollbar is just not good enough (or not present at all, like on tablets) to go up or down exactly one page/screen.</p>


		<?php

			$warnings = false;

			if ( isset( $_GET['message'] )) { 
				if ($_GET['message'] == '1') {
					echo '<div id="message" class="fade updated"><p><strong>Settings Updated</strong></p></div>';
				}
			} 
			
			if (($page_scroll_buttons_options['psb_speed'] < 1) || ($page_scroll_buttons_options['psb_speed'] == '')) {
				// Speed is empty or is smaller than 1
				$warnings = true;
			}

			if ( (!is_numeric($page_scroll_buttons_options['psb_speed'])) && ($page_scroll_buttons_options['psb_speed'] != '')) {
				// Speed is not empty and has bad value
				$warnings = true;
			}

			// IF THERE ARE ERRORS, SHOW THEM
			if ( $warnings == true ) { 
				echo '<div id="message" class="error"><p><strong>Error! Please review the current settings:</strong></p>';
				echo '<ul style="list-style-type:disc; margin:0 0 20px 24px;">';

				if ($page_scroll_buttons_options['psb_speed'] == '') {
					echo '<li><strong>SCROLLING SPEED</strong> is required.</li>';
				} else {

					if ( (!is_numeric($page_scroll_buttons_options['psb_speed'])) && ($page_scroll_buttons_options['psb_speed'] != '')) {
						echo '<li><strong>SCROLLING SPEED</strong> has to be a number (do not include "ms" or "seconds", or any other characters).</li>';
					} elseif ($page_scroll_buttons_options['psb_speed'] < 1) {
						echo '<li><strong>SCROLLING SPEED</strong> has to be larger than 0.</li>';
					}
 
				}

				echo '</ul></div>';
			} 			

		?>

		<table class="widefat">
			<tr>
				<td>

					<form method="post" action="admin-post.php">

						<input type="hidden" name="action" value="save_page_scroll_buttons_options" />
						<!-- Adding security through hidden referrer field -->
						<?php wp_nonce_field( 'page_scroll_buttons_ref' ); ?>

						<table class="form-table">

							<tr>
								<th scope="row">Include 'back to top' button: <a href="#" title="If you want an additional button that scrolls all the way to the top of the page, check this box." class="help">?</a></th>
								<td>
									<input type="checkbox" id="psb_topbutton" name="psb_topbutton" <?php if ($page_scroll_buttons_options['psb_topbutton'] ) echo ' checked="checked" ';?> />
									<label for="psb_topbutton"></label>
								</td>
							</tr>

							<tr>
								<th scope="row">Positioning <a href="#" title="Choose where you want your up/down buttons to be positioned." class="help">?</a></th>
								<td class="positioning-buttons">
									<div class="positioning-option"><input type="radio" id="psb_positioning_0" name="psb_positioning" value="0" <?php if ($page_scroll_buttons_options['psb_positioning'] == 0) {echo 'checked';} ?>><label id="pos-0" for="psb_positioning_0"></label></div>
									<div class="positioning-option"><input type="radio" id="psb_positioning_1" name="psb_positioning" value="1" <?php if ($page_scroll_buttons_options['psb_positioning'] == 1) {echo 'checked';} ?>><label id="pos-1" for="psb_positioning_1"></label></div>
									<div class="positioning-option"><input type="radio" id="psb_positioning_2" name="psb_positioning" value="2" <?php if ($page_scroll_buttons_options['psb_positioning'] == 2) {echo 'checked';} ?>><label id="pos-2" for="psb_positioning_2"></label></div>
							</td>
							</tr>

							<tr>
								<th scope="row">Scrolling speed <a href="#" title="The speed at which the page scrolls when you click on a button (set to 1 for no visible scrolling)." class="help">?</a></th>
								<td>
									<input type="number" id="psb_speed" name="psb_speed" value="<?php echo ( $page_scroll_buttons_options['psb_speed'] ); ?>" style="width:80px;" /> milliseconds &nbsp;&nbsp;&nbsp;(<em>1 second = 1000 milliseconds</em>)
								</td>
							</tr>

						</table>

						<input type="submit" value="SAVE SETTINGS" class="button-primary"/>

						<p>&nbsp;</p>
					</form>

				</td>
			</tr>
		</table>

		<hr />

		<p><a href="http://www.senff.com/plugins/smooth-page-scroll-up-down-buttons" target="_blank">Smooth Page Scroll Up/Down Buttons</a> version 1.0 by <a href="http://www.senff.com" target="_blank">Senff</a> &nbsp;/&nbsp; <a href="https://wordpress.org/support/plugin/smooth-page-scroll-updown-buttons" target="_blank">Please Report Bugs</a> &nbsp;/&nbsp; Follow on Twitter: <a href="http://www.twitter.com/senff" target="_blank">@Senff</a> &nbsp;/&nbsp; <a href="http://www.senff.com/plugins/smooth-page-scroll-up-down-buttons" target="_blank">Detailed documentation</a> &nbsp;/&nbsp; <a href="http://www.cancer.ca" target="_blank">Donate</a></p>

	</div>

	<?php
	}
}


if (!function_exists('page_scroll_buttons_admin_init')) {
	function page_scroll_buttons_admin_init() {
		add_action( 'admin_post_save_page_scroll_buttons_options', 'process_page_scroll_buttons_options' );
	}
}


if (!function_exists('process_page_scroll_buttons_options')) {
	function process_page_scroll_buttons_options() {

		if ( !current_user_can( 'manage_options' ))
			wp_die( 'Not allowed');

		check_admin_referer('page_scroll_buttons_ref');
		$options = get_option('page_scroll_buttons_options');

		foreach ( array('psb_positioning') as $option_name ) {
			if ( isset( $_POST[$option_name] ) ) {
				$options[$option_name] = sanitize_text_field( $_POST[$option_name] );
			} 
		}

		foreach ( array('psb_topbutton') as $option_name ) {
			if ( isset( $_POST[$option_name] ) ) {
				$options[$option_name] = true;
			} else {
				$options[$option_name] = false;
			}
		}

		foreach ( array('psb_speed') as $option_name ) {
			if ( isset( $_POST[$option_name] ) ) {
				$options[$option_name] = sanitize_text_field( $_POST[$option_name] );
			} 
		}


		update_option( 'page_scroll_buttons_options', $options );	
 		wp_redirect( add_query_arg(
 			array('page' => 'pagescrollupdownmenu', 'message' => '1'),
 			admin_url( 'options-general.php' ) 
 			)
 		);	

		exit;
	}
}



if (!function_exists('page_scroll_script')) {
	function page_scroll_script($hook) {
		if ($hook != 'settings_page_pagescrollupdownmenu') {
			return;
		}

		wp_register_script('pageScrollButtonsAdmin', plugins_url('/assets/js/smooth-page-scroll-updown-admin.js', __FILE__), array( 'jquery' ), '1.0');
		wp_enqueue_script('pageScrollButtonsAdmin');

		wp_register_style('pageScrollButtonsAdminStyle', plugins_url('/assets/css/smooth-page-scroll-updown-admin.css', __FILE__) );
	    wp_enqueue_style('pageScrollButtonsAdminStyle');		
	}
}

// === HOOKS AND ACTIONS ==================================================================================

register_activation_hook( __FILE__, 'page_scroll_buttons_default_options' );
add_action('init','page_scroll_buttons_update',1);
add_action('wp_enqueue_scripts', 'load_page_scroll_buttons');
add_action('admin_menu', 'page_scroll_buttons_menu');
add_action('admin_init', 'page_scroll_buttons_admin_init' );
add_action('admin_enqueue_scripts', 'page_scroll_script' );


