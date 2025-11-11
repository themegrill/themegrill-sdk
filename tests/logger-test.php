<?php
/**
 * Logger feature test.
 *
 * @package ThemeGrillSDK
 */

/**
 * Test logger feature.
 */
class Logger_Test extends WP_UnitTestCase {


	public function test_product_partner_module_loading() {

		$file = __DIR__ . '/sample_products/sample_theme_external/style.css';

		\ThemeGrillSDK\Loader::add_product( $file );

		$modules = \ThemeGrillSDK\Common\Module_Factory::get_modules_map();

		$this->assertArrayHasKey( 'sample_theme_external', $modules );
		$modules['sample_theme_external'] = array_filter(
			$modules['sample_theme_external'],
			[ $this, 'filter_value' ]
		);
		$this->assertEquals( count( $modules['sample_theme_external'] ), 1 );
	}

	private function filter_value( $value ) {
		return ( get_class( $value ) === 'ThemeGrillSDK\\Modules\\Logger' );
	}

	public function test_product_loading() {

		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		\ThemeGrillSDK\Loader::add_product( $file );

		$modules = \ThemeGrillSDK\Common\Module_Factory::get_modules_map();

		$this->assertArrayHasKey( 'sample_theme', $modules );
		$this->assertGreaterThan( 0, count( $modules['sample_theme'] ) );
	}

	public function test_can_load_partner() {

		$file    = __DIR__ . '/sample_products/sample_theme_external/style.css';
		$product = new \ThemeGrillSDK\Product( $file );

		$this->assertTrue( ( new \ThemeGrillSDK\Modules\Logger() )->can_load( $product ) );
	}

	public function test_load_normal() {

		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		$product = new \ThemeGrillSDK\Product( $file );

		$this->assertTrue( ( new \ThemeGrillSDK\Modules\Logger() )->can_load( $product ) );
		$this->assertInstanceOf( 'ThemeGrillSDK\\Modules\\Logger', ( new \ThemeGrillSDK\Modules\Logger() )->load( $product ) );
	}
}
