<?php
/*
Plugin Name: RMM Custom Functionality
Description: Catch-all for miscellaneous bits of RMM-specific code.
Version: 0.1
Author: James
Author URI: https://www.redmountainmakers.org/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Text Domain: rmm
Domain Path: /languages

RMM Custom Functionality
Copyright (C) 2016, Red Mountain Makers

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RMM_CUSTOM_PLUGIN_PATH', __FILE__ );


/**
 * Add RMM custom stylesheet for stuff outside of the theme
 */

function rmm_add_custom_styles() {
	wp_register_style(
		'rmm-custom',
		plugins_url( 'css/rmm-custom.css', RMM_CUSTOM_PLUGIN_PATH )
	);
	wp_enqueue_style( 'rmm-custom' );
}

add_action( 'wp_enqueue_scripts', 'rmm_add_custom_styles' );


/**
 * Add [widget-location-text] shortcode
 *
 * This displays either "on the right" or "at the bottom of the page" depending
 * on the screen width.  It is used to point readers to content in the widget
 * sidebar.
 */

function rmm_widget_location_shortcode() {
	$html = <<<HTML
		<span class="widget-location-text">
			<span class="right">on the right</span>
			<span class="bottom">at the bottom of the page</span>
		</span>
HTML;
	// Remove whitespace in between tags
	$html = trim( $html );
	$html = preg_replace( '@>\s+<@m', '><', $html );
	return $html;
}

add_shortcode( 'widget-location-text', 'rmm_widget_location_shortcode' );


/**
 * Make sending emails work again (we use a DNS alias for the mail server name)
 * http://serverfault.com/a/761973/34249
 */

add_filter( 'wp_mail_smtp_custom_options', function( $phpmailer ) {
	$phpmailer->SMTPOptions = array(
		'ssl' => array(
			'verify_peer'       => true,
			'verify_peer_name'  => false,
			'allow_self_signed' => false,
		),
	);

	return $phpmailer;
} );


/**
 * Allow administrators to upload SVG files
 */

add_filter( 'upload_mimes', function( $mime_types ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mime_types['svg'] = 'image/svg+xml';
	}
	return $mime_types;
} );
