<?php
/**
 * Loader for the ThemeGrillSDK
 *
 * Logic for loading always the latest SDK from the installed themes/plugins.
 *
 * @package     ThemeGrillSDK
 * @copyright   Copyright (c) 2025, ThemeGrill
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

// Current SDK version and path.
$themegrill_sdk_version = '1.0.0';
$themegrill_sdk_path    = __DIR__;

global $themegrill_sdk_max_version;
global $themegrill_sdk_max_path;

if (
	( is_null( $themegrill_sdk_max_path ) ||
	version_compare( $themegrill_sdk_version, $themegrill_sdk_max_version ) === 0 ) &&
	apply_filters( 'themegrill_sdk_should_overwrite_path', false, $themegrill_sdk_path, $themegrill_sdk_max_path )
	) {
	$themegrill_sdk_max_path = $themegrill_sdk_path;
}

if (
	is_null( $themegrill_sdk_max_version ) ||
	version_compare( $themegrill_sdk_version, $themegrill_sdk_max_version ) > 0
) {
	$themegrill_sdk_max_version = $themegrill_sdk_version;
	$themegrill_sdk_max_path    = $themegrill_sdk_path;
}

// Load the latest sdk version from the active ThemeGrill products.
if ( ! function_exists( 'themegrill_sdk_load_latest' ) ) :
	/**
	 * Always load the latest sdk version.
	 */
	function themegrill_sdk_load_latest() {
		/**
		 * Don't load the library if we are on < 7.2.24.
		 */
		if ( version_compare( PHP_VERSION, '7.2.24', '<' ) ) {
			return;
		}
		global $themegrill_sdk_max_path;
		require_once $themegrill_sdk_max_path . '/start.php';
	}
endif;

add_action( 'init', 'themegrill_sdk_load_latest' );
