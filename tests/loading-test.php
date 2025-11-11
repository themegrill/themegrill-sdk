<?php
/**
 * `loading` test.
 *
 * @package ThemeGrillSDK
 */

/**
 * Test sdk loading.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class Sdk_Loading_Test extends WP_UnitTestCase {
	/**
	 * Test if the SDK is loading properly and version is exported.
	 */
	public function test_version_exists() {
		global $themegrill_sdk_max_version;
		$this->assertTrue( isset( $themegrill_sdk_max_version ) );
		$this->assertTrue( version_compare( '0.0.1', $themegrill_sdk_max_version, '<' ) );
	}

	/**
	 * Test that classes are properly loaded.
	 */
	public function test_class_loading() {
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Loader' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Product' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Modules\\Dashboard_Widget' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Modules\\Rollback' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Modules\\Uninstall_Feedback' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Modules\\Licenser' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Modules\\Notification' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Modules\\Logger' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Modules\\Translate' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Modules\\Review' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Modules\\Recommendation' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Common\\Abstract_Module' ) );
		$this->assertTrue( class_exists( 'ThemeGrillSDK\\Common\\Module_factory' ) );
	}

	/**
	 * Test the loaded products.
	 */
	public function test_loaded_defaults() {
		$this->assertEquals( count( \ThemeGrillSDK\Loader::get_products() ), 0 );
		$this->assertGreaterThan( 0, count( \ThemeGrillSDK\Loader::get_modules() ) );
	}
}
