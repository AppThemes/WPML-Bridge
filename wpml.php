<?php
/*
Plugin Name: AppThemes WPML Bridge
Plugin URI: https://docs.appthemes.com/wpml-bridge-plugin/
Description: Creates bridge between AppThemes Products and WPML plugin.

AppThemes ID: appthemes-wpml

Version: 1.3
Author: AppThemes
Author URI: http://appthemes.com
Text Domain: appthemes-wpml
Domain Path: /languages
*/


/**
 * Plugin version and textdomain constants.
 */
define( 'APP_WPML_VERSION', '1.3' );
define( 'APP_WPML_TD', 'appthemes-wpml' );


/**
 * Load Text-Domain.
 *
 * @return void
 */
function app_wpml_load_textdomain() {
	load_plugin_textdomain( APP_WPML_TD, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'app_wpml_load_textdomain' );


/**
 * Setup the WPML Bridge.
 *
 * @return void
 */
function app_wpml_setup() {

	// Check for existence of WPML plugin
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) || ! defined( 'WPML_ST_VERSION' ) ) {
		add_action( 'admin_notices', 'app_wpml_display_warning' );
		return;
	}

	require_once( dirname( __FILE__ ) . '/wpml-actions.php' );
}
add_action( 'plugins_loaded', 'app_wpml_setup' );


/**
 * Check for existence of AppThemes Products.
 *
 * @return void
 */
function app_wpml_check_appthemes() {

	if ( ! function_exists( 'appthemes_init' ) ) {
		add_action( 'admin_notices', 'app_wpml_display_warning' );
		return;
	}
}
add_action( 'init', 'app_wpml_check_appthemes' );


/**
 * Displays warning when AppThemes Theme or WPML plugin is not installed.
 *
 * @return void
 */
function app_wpml_display_warning() {

	$message = __( 'AppThemes WPML Bridge could not run.', APP_WPML_TD );

	if ( ! function_exists( 'appthemes_init' ) ) {
		$message = __( 'AppThemes WPML Bridge does not support the current theme.', APP_WPML_TD );
	}

	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
		$message = __( 'AppThemes WPML Bridge require WPML Multilingual CMS plugin to work.', APP_WPML_TD );
	}

	if ( ! defined( 'WPML_ST_VERSION' ) ) {
		$message = __( 'AppThemes WPML Bridge require WPML String Translation plugin to work.', APP_WPML_TD );
	}

	echo '<div class="error fade"><p>' . $message . '</p></div>';
	deactivate_plugins( plugin_basename( __FILE__ ) );
}


