<?php
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit;
	if ( get_option( 'page_scroll_buttons_options' ) != false ) {
		delete_option( 'page_scroll_buttons_options' );
	}
?>
