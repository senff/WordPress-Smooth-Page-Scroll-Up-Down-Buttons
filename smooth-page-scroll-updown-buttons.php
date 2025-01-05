<?php
/*
Plugin Name: Smooth Page Scroll Up/Down Buttons
Plugin URI: https://wordpress.org/plugins/smooth-page-scroll-updown-buttons
Description: Adds buttons to your page that will enable the user to easily (and smoothly) scroll one screen up/down.
Author: Senff
Author URI: http://www.senff.com
Version: 1.4.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: smooth-page-scroll-updown-buttons
*/

defined('ABSPATH') or die('READY PLAYER ONE');


// === FUNCTIONS =========================================================================================================

if (!function_exists('page_scroll_buttons_default_options')) {
	function page_scroll_buttons_default_options() {
		$versionNum = '1.4';
		if (get_option('page_scroll_buttons_options') === false) {
			$new_options['psb_version'] = $versionNum;
			$new_options['psb_positioning'] = '0';
			$new_options['psb_topbutton'] = '';
			$new_options['psb_buttonsize'] = '45';
			$new_options['psb_distance'] = '100';
			$new_options['psb_speed'] = '1200';
			add_option('page_scroll_buttons_options',$new_options);
		} 
	}
}


if (!function_exists('page_scroll_buttons_update')) {
	function page_scroll_buttons_update() {
		$versionNum = '1.4';
		$existing_options = get_option('page_scroll_buttons_options');

		if(!isset($existing_options['psb_distance'])) {
			// Introduced in version 1.2
			$existing_options['psb_distance'] = '100';
		} 

		if(!isset($existing_options['psb_buttonsize'])) {
			// Introduced in version 1.3
			$existing_options['psb_buttonsize'] = '45';
		} 

		$existing_options['psb_version'] = $versionNum;
		update_option('page_scroll_buttons_options',$existing_options);
	}
}




if (!function_exists('load_page_scroll_buttons')) {
    function load_page_scroll_buttons() {

		// Main jQuery plugin file 
	    wp_register_script('pageScrollButtonsLib', plugins_url('/assets/js/smooth-page-scroll-updown-buttons.min.js', __FILE__), array( 'jquery' ), '1.4.2', array( 'in_footer' => true ));
	    wp_enqueue_script('pageScrollButtonsLib');

		wp_register_style('pageScrollButtonsStyle', plugins_url('/assets/css/smooth-page-scroll-updown-buttons.css', __FILE__),'', '1.4.2' );
	    wp_enqueue_style('pageScrollButtonsStyle');

		$options = get_option('page_scroll_buttons_options');

		// Set defaults when empty (because '' does not seem to work with the JQ plugin) 
		if (!$options['psb_positioning']) {
			$options['psb_positioning'] = '0';
		}

		if (!$options['psb_topbutton']) {
			$options['psb_topbutton'] = '';
		}

		if (!$options['psb_buttonsize']) {
			$options['psb_buttonsize'] = '45';
		}

		if (!$options['psb_distance']) {
			$options['psb_distance'] = '100';
		}

		if (!$options['psb_speed']) {
			$options['psb_speed'] = '1200';
		}

		$script_vars = array(
		      'positioning' => $options['psb_positioning'],
		      'topbutton' => $options['psb_topbutton'],
		      'buttonsize' => $options['psb_buttonsize'],
		      'distance' => $options['psb_distance'],
		      'speed' => $options['psb_speed']
		);

		wp_enqueue_script('addButtons', plugins_url('/assets/js/addButtons.js', __FILE__), array( 'jquery' ), '1.4.2', array( 'in_footer' => true ));
		wp_localize_script( 'addButtons', 'add_buttons_engage', $script_vars );

    }
}

if (!function_exists('page_scroll_buttons_menu')) {
    function page_scroll_buttons_menu() {
		add_options_page( 'Smooth Scroll Page Up/Down Buttons Configuration', 'Smooth Scroll Page Up/Down Buttons', 'manage_options', 'pagescrollupdownmenu', 'page_scroll_up_down_buttons_config_page' );
    }
}

if (!function_exists('page_scroll_buttons_settings_link')) {
	function page_scroll_buttons_settings_link($links) { 
  		$settings_link = '<a href="options-general.php?page=pagescrollupdownmenu">Settings</a>'; 
  		array_unshift($links, $settings_link); 
  		return $links; 
	}
}


if (!function_exists('page_scroll_up_down_buttons_config_page')) {
	function page_scroll_up_down_buttons_config_page() {
	// Retrieve plugin configuration options from database
	$page_scroll_buttons_options = get_option( 'page_scroll_buttons_options' );
	?>

	<div id="page-scroll-up-down-buttons-settings-general" class="wrap">
		<h2><?php esc_html_e('Smooth Scroll Page Up/Down Buttons Settings', 'smooth-page-scroll-updown-buttons'); ?></h2>

		<p><?php esc_html_e('Adding UP/DOWN buttons will enable visitors of your site to scroll smoothly, scrolling one page at a time. Handy for pages with a lot of text/content, or wherever a browser\'s scrollbar is just not good enough (or not present at all, like on tablets) to go up or down exactly one page/screen.', 'smooth-page-scroll-updown-buttons'); ?></p>


		<?php

			$warnings = false;

			if ( isset( $_GET['message'] )) { 
				if ($_GET['message'] == '1') {
					echo '<div id="message" class="fade updated"><p><strong>'.esc_html__('Settings updated.', 'smooth-page-scroll-updown-buttons').'</strong></p></div>';
				}
			} 

			if ( (!is_numeric($page_scroll_buttons_options['psb_distance'])) && ($page_scroll_buttons_options['psb_distance'] != '' ) || ($page_scroll_buttons_options['psb_distance'] == '')) {
				// Distance is not empty and has bad value
				$warnings = true;
			}

			if ( (!is_numeric($page_scroll_buttons_options['psb_buttonsize'])) && ($page_scroll_buttons_options['psb_buttonsize'] != '')) {
				// Button size is not empty and has bad value
				$warnings = true;
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
				echo '<div id="message" class="error"><p><strong>'.esc_html__('Error! Please review the current settings:', 'smooth-page-scroll-updown-buttons').'</strong></p>';
				echo '<ul style="list-style-type:disc; margin:0 0 20px 24px;">';

				if ( (!is_numeric($page_scroll_buttons_options['psb_distance'])) && ($page_scroll_buttons_options['psb_distance'] != '')) {
					echo '<li><strong>'.esc_html__('SCROLLING DISTANCE', 'smooth-page-scroll-updown-buttons').'</strong> '.esc_html__('has to be a number (do not include "%" or "px", or any other characters).', 'smooth-page-scroll-updown-buttons').'</li>';
				}

				if ($page_scroll_buttons_options['psb_distance'] == '') {
					echo '<li><strong>'.esc_html__('SCROLLING DISTANCE', 'smooth-page-scroll-updown-buttons').'</strong> '.esc_html__('is required.', 'smooth-page-scroll-updown-buttons').'</li>';
				}				

				if ( (!is_numeric($page_scroll_buttons_options['psb_buttonsize'])) && ($page_scroll_buttons_options['psb_buttonsize'] != '')) {
					echo '<li><strong>'.esc_html__('BUTTON SIZE', 'smooth-page-scroll-updown-buttons').'</strong> '.esc_html__('has to be a number (do not include "%" or "px", or any other characters).', 'smooth-page-scroll-updown-buttons').'</li>';
				} 

				if ($page_scroll_buttons_options['psb_speed'] == '') {
					echo '<li><strong>'.esc_html__('SCROLLING SPEED', 'smooth-page-scroll-updown-buttons').'</strong> '.esc_html__('is required.', 'smooth-page-scroll-updown-buttons').'</li>';
				} else {

					if ( (!is_numeric($page_scroll_buttons_options['psb_speed'])) && ($page_scroll_buttons_options['psb_speed'] != '')) {
						echo '<li><strong>'.esc_html__('SCROLLING SPEED', 'smooth-page-scroll-updown-buttons').'</strong> '.esc_html__('has to be a number (do not include "ms" or "seconds", or any other characters).', 'smooth-page-scroll-updown-buttons').'</li>';
					} elseif ($page_scroll_buttons_options['psb_speed'] < 1) {
						echo '<li><strong>'.esc_html__('SCROLLING SPEED', 'smooth-page-scroll-updown-buttons').'</strong> '.esc_html__('has to be larger than 0.', 'smooth-page-scroll-updown-buttons').'</li>';
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
								<th scope="row"><?php esc_html_e('Include \'back to top\' button:', 'smooth-page-scroll-updown-buttons'); ?> <a href="#" title="<?php esc_attr_e('If you want an additional button that scrolls all the way to the top of the page, check this box.', 'smooth-page-scroll-updown-buttons'); ?>" class="help">?</a></th>
								<td>
									<input type="checkbox" id="psb_topbutton" name="psb_topbutton" <?php if ($page_scroll_buttons_options['psb_topbutton'] ) echo ' checked="checked" ';?> />
									<label for="psb_topbutton"></label>
								</td>
							</tr>

							<tr>
								<th scope="row"><?php esc_html_e('Positioning', 'smooth-page-scroll-updown-buttons'); ?> <a href="#" title="<?php esc_attr_e('Choose where you want your up/down buttons to be positioned.', 'smooth-page-scroll-updown-buttons'); ?>" class="help">?</a></th>
								<td class="positioning-buttons">
									<?php $psb_positioning = ( isset( $page_scroll_buttons_options['psb_positioning'] ) ) ? $page_scroll_buttons_options['psb_positioning'] : ''; ?>
									<div class="positioning-option"><input type="radio" id="psb_positioning_0" name="psb_positioning" value="0" <?php if (esc_attr( $psb_positioning ) == 0) {echo 'checked';} ?>><label id="pos-0" for="psb_positioning_0"></label></div>
									<div class="positioning-option"><input type="radio" id="psb_positioning_1" name="psb_positioning" value="1" <?php if (esc_attr( $psb_positioning ) == 1) {echo 'checked';} ?>><label id="pos-1" for="psb_positioning_1"></label></div>
									<div class="positioning-option"><input type="radio" id="psb_positioning_2" name="psb_positioning" value="2" <?php if (esc_attr( $psb_positioning ) == 2) {echo 'checked';} ?>><label id="pos-2" for="psb_positioning_2"></label></div>
									<div class="positioning-option"><input type="radio" id="psb_positioning_3" name="psb_positioning" value="3" <?php if (esc_attr( $psb_positioning ) == 3) {echo 'checked';} ?>><label id="pos-3" for="psb_positioning_3"></label></div>
							</td>
							</tr>

							<tr>
								<th scope="row"><?php esc_html_e('Scrolling Distance', 'smooth-page-scroll-updown-buttons'); ?> <a href="#" title="<?php esc_attr_e('How far the page scrolls when you click on a button.', 'smooth-page-scroll-updown-buttons'); ?>" class="help">?</a></th>
								<td>
									<?php $psb_distance = ( isset( $page_scroll_buttons_options['psb_distance'] ) ) ? $page_scroll_buttons_options['psb_distance'] : ''; ?>
									<input type="number" min="1" id="psb_distance" name="psb_distance" value="<?php echo esc_attr( $psb_distance ); ?>" style="width:80px;" /> % &nbsp;&nbsp;&nbsp;(<em>100% = full page/screen, 50% = half screen, etc.</em>)
								</td>
							</tr>

							<tr>
								<th scope="row"><?php esc_html_e('Button Size', 'smooth-page-scroll-updown-buttons'); ?> <a href="#" title="<?php esc_attr_e('How large the arrow buttons should be.', 'smooth-page-scroll-updown-buttons'); ?>" class="help">?</a></th>
								<td>
									<?php $psb_buttonsize = ( isset( $page_scroll_buttons_options['psb_buttonsize'] ) ) ? $page_scroll_buttons_options['psb_buttonsize'] : ''; ?>
									<input type="number" min="1" id="psb_buttonsize" name="psb_buttonsize" value="<?php echo esc_attr( $psb_buttonsize ); ?>" style="width:80px;" /> pixels square<br />
									<div class="button-example-preview">Preview:</div><div class="button-example-wrapper"><div class="button-example" style="width:<?php echo esc_attr( $psb_buttonsize ); ?>px; height:<?php echo esc_attr( $psb_buttonsize ); ?>px;"></div></div>
								</td>
							</tr>

							<tr>
								<th scope="row"><?php esc_html_e('Scrolling Speed', 'smooth-page-scroll-updown-buttons'); ?> <a href="#" title="<?php esc_attr_e('The speed at which the page scrolls when you click on a button (set to 1 for no visible scrolling).', 'smooth-page-scroll-updown-buttons'); ?>" class="help">?</a></th>
								<td>
									<?php $psb_speed = ( isset( $page_scroll_buttons_options['psb_speed'] ) ) ? $page_scroll_buttons_options['psb_speed'] : ''; ?>
									<input type="number" min="1" id="psb_speed" name="psb_speed" value="<?php echo esc_attr( $psb_speed ); ?>" style="width:80px;" /> milliseconds &nbsp;&nbsp;&nbsp;(<em>1 second = 1000 milliseconds</em>)
								</td>
							</tr>

						</table>

						<input type="submit" value="<?php esc_attr_e('Save Settings', 'smooth-page-scroll-updown-buttons'); ?>" class="button-primary"/>

						<p>&nbsp;</p>
					</form>

				</td>
			</tr>
		</table>

		<hr />

		<p><strong><?php esc_html_e('Smooth Page Scroll Up/Down Buttons', 'smooth-page-scroll-updown-buttons'); ?></strong> version 1.4 by <a href="http://www.senff.com" target="_blank">Senff</a> &nbsp;/&nbsp; <a href="https://wordpress.org/support/plugin/smooth-page-scroll-updown-buttons" target="_blank"><?php esc_html_e('Please report bugs', 'smooth-page-scroll-updown-buttons'); ?></a> &nbsp;/&nbsp; <?php esc_html_e('Follow on Bluesky', 'smooth-page-scroll-updown-buttons'); ?> <a href="https://bsky.app/profile/senff.com" target="_blank">@senff.com</a> &nbsp;/&nbsp; <a href="https://github.com/senff/WordPress-Smooth-Page-Scroll-Up-Down-Buttons" target="_blank"><?php esc_html_e('Detailed documentation', 'smooth-page-scroll-updown-buttons'); ?></a> &nbsp;/&nbsp; <a href="http://www.senff.com/donate" target="_blank"><?php esc_html_e('Donate', 'smooth-page-scroll-updown-buttons'); ?></a></p>

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
				$options[$option_name] = sanitize_text_field(wp_unslash($_POST[$option_name] ));
			} 
		}

		foreach ( array('psb_topbutton') as $option_name ) {
			if ( isset( $_POST[$option_name] ) ) {
				$options[$option_name] = true;
			} else {
				$options[$option_name] = false;
			}
		}

		foreach ( array('psb_distance') as $option_name ) {
			if ( isset( $_POST[$option_name] ) ) {
				$options[$option_name] = sanitize_text_field( wp_unslash($_POST[$option_name] ));
			} 
		}

		foreach ( array('psb_buttonsize') as $option_name ) {
			if ( isset( $_POST[$option_name] ) ) {
				$options[$option_name] = sanitize_text_field( wp_unslash($_POST[$option_name] ));
			} 
		}

		foreach ( array('psb_speed') as $option_name ) {
			if ( isset( $_POST[$option_name] ) ) {
				$options[$option_name] = sanitize_text_field( wp_unslash($_POST[$option_name] ));
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

		wp_register_script('pageScrollButtonsAdmin', plugins_url('/assets/js/smooth-page-scroll-updown-admin.js', __FILE__), array( 'jquery' ), '1.4.2', array( 'in_footer' => true ));
		wp_enqueue_script('pageScrollButtonsAdmin');

		wp_register_style('pageScrollButtonsAdminStyle', plugins_url('/assets/css/smooth-page-scroll-updown-admin.css', __FILE__),'', '1.4.2' );
	    wp_enqueue_style('pageScrollButtonsAdminStyle');		
	}
}

// === HOOKS AND ACTIONS ==================================================================================

$plugin = plugin_basename(__FILE__); 

register_activation_hook( __FILE__, 'page_scroll_buttons_default_options' );
add_action('init','page_scroll_buttons_update',1);
add_action('wp_enqueue_scripts', 'load_page_scroll_buttons');
add_action('admin_menu', 'page_scroll_buttons_menu');
add_action('admin_init', 'page_scroll_buttons_admin_init' );
add_action('admin_enqueue_scripts', 'page_scroll_script' );
add_filter("plugin_action_links_$plugin", 'page_scroll_buttons_settings_link' );



