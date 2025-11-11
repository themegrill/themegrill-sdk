<?php
/**
 * Loader manager test.
 *
 * @package ThemeGrillSDK
 */

/**
 * Test loader manager.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class Loader_Test extends WP_UnitTestCase {

	/**
	 * Test loading of invalid file.
	 */
	public function test_products_invalid_subscribe() {
		$file = __DIR__ . '/invalid/sample_products/sample_plugin/plugin-file.php';
		\ThemeGrillSDK\Loader::add_product( $file );
		$this->assertEmpty( ThemeGrillSDK\Loader::get_products() );
	}

	/**
	 * Test loading of plugin file.
	 */
	public function test_products_valid_subscribe_plugin() {
		$file = __DIR__ . '/sample_products/sample_plugin/plugin_file.php';

		\ThemeGrillSDK\Loader::add_product( $file );

		$this->assertEquals( count( ThemeGrillSDK\Loader::get_products() ), 1 );
	}

	/**
	 * Test loading of theme file.
	 */
	public function test_products_valid_subscribe_theme() {
		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		\ThemeGrillSDK\Loader::add_product( $file );

		$this->assertEquals( count( ThemeGrillSDK\Loader::get_products() ), 1 );
	}
}
