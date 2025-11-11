<?php
/**
 * Uninstall feature test.
 *
 * @package ThemeGrillSDK
 */

/**
 * Test Uninstall feedback feature.
 */
class UninstallFeedback_Test extends WP_UnitTestCase {


	/**
	 * Test product from partner loading.
	 */
	public function test_product_partner_module_loading() {

		$file = __DIR__ . '/sample_products/sample_theme_external/style.css';

		\ThemeGrillSDK\Loader::add_product( $file );

		$modules = \ThemeGrillSDK\Common\ModuleFactory::get_modules_map();

		$this->assertArrayHasKey( 'sample_theme_external', $modules );
		$modules['sample_theme_external'] = array_filter(
			$modules['sample_theme_external'],
			[ $this, 'filter_value' ]
		);
		$this->assertCount( 0, $modules['sample_theme_external'] );
	}

	private function filter_value( $value ) {
		if ( ! is_object( $value ) ) {
			return false;
		}

		return ( get_class( $value ) === 'ThemeGrillSDK\\Modules\\UninstallFeedback' );
	}
	/**
	 * Test product from partner loading.
	 */
	public function test_un_feedback_product_loading() {

		$file = __DIR__ . '/sample_products/sample_theme/style.css';
		global $pagenow;
		$pagenow = 'theme-install.php';
		\ThemeGrillSDK\Loader::add_product( $file );

		$modules = \ThemeGrillSDK\Common\ModuleFactory::get_modules_map();

		$this->assertArrayHasKey( 'sample_theme', $modules );
		$modules['sample_theme'] = array_filter(
			$modules['sample_theme'],
			[ $this, 'filter_value' ]
		);
		$this->assertCount( 1, $modules['sample_theme'] );
	}

	/**
	 * Test if uninstall feedback is disabled on partners.
	 */
	public function test_un_feedback_can_load_partner() {

		$file    = __DIR__ . '/sample_products/sample_theme_external/style.css';
		$product = new \ThemeGrillSDK\Product( $file );

		$this->assertFalse( ( new \ThemeGrillSDK\Modules\UninstallFeedback() )->can_load( $product ) );
	}


	/**
	 * Test if  uninstall feedback  should load for non whitelisted pages.
	 */
	public function test_un_feedback_load_non_pages() {

		$file    = __DIR__ . '/sample_products/sample_theme/style.css';
		$product = new \ThemeGrillSDK\Product( $file );
		global $pagenow;
		$pagenow = 'index.php';
		$this->assertFalse( ( new \ThemeGrillSDK\Modules\UninstallFeedback() )->can_load( $product ) );
	}

	/**
	 * Test if  uninstall feedback  should load for plugins listing.
	 */
	public function test_un_feedback_load_plugins_pages() {

		$file    = __DIR__ . '/sample_products/sample_plugin/plugin-file.php';
		$product = new \ThemeGrillSDK\Product( $file );
		global $pagenow;
		$pagenow = 'plugins.php';

		$this->assertTrue( ( new \ThemeGrillSDK\Modules\UninstallFeedback() )->can_load( $product ) );
		$this->assertInstanceOf( 'ThemeGrillSDK\\Modules\\UninstallFeedback', ( new \ThemeGrillSDK\Modules\UninstallFeedback() )->load( $product ) );
	}

	/**
	 * Test if  uninstall feedback  should load for themes install.
	 */
	public function test_un_feedback_load_themes_pages() {

		$file    = __DIR__ . '/sample_products/sample_theme/style.css';
		$product = new \ThemeGrillSDK\Product( $file );
		global $pagenow;
		$pagenow = 'theme-install.php';

		$this->assertTrue( ( new \ThemeGrillSDK\Modules\UninstallFeedback() )->can_load( $product ) );
		$this->assertInstanceOf( 'ThemeGrillSDK\\Modules\\UninstallFeedback', ( new \ThemeGrillSDK\Modules\UninstallFeedback() )->load( $product ) );
	}

	/**
	 * Test if  uninstall feedback  loads on ajax requests.
	 */
	public function test_un_feedback_load_ajax() {

		$file    = __DIR__ . '/sample_products/sample_theme/style.css';
		$product = new \ThemeGrillSDK\Product( $file );

		define( 'DOING_AJAX', true );

		$this->assertTrue( ( new \ThemeGrillSDK\Modules\UninstallFeedback() )->can_load( $product ) );
		$this->assertInstanceOf( 'ThemeGrillSDK\\Modules\\UninstallFeedback', ( new \ThemeGrillSDK\Modules\UninstallFeedback() )->load( $product ) );
	}
}
