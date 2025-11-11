<?php
/**
 * Notification feature test.
 *
 * @package ThemeGrillSDK
 */

/**
 * Test notification feature.
 */
class Notification_Test extends WP_UnitTestCase {

	protected static $editor_id;
	protected static $admin_id;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$editor_id = $factory->user->create(
			array(
				'role' => 'editor',
			)
		);
		self::$admin_id  = $factory->user->create(
			array(
				'role' => 'administrator',
			)
		);

		wp_set_current_user( self::$editor_id );
	}

	public static function wpTearDownAfterClass() {
		self::delete_user( self::$editor_id );
		self::delete_user( self::$admin_id );
	}

	public function test_product_partner_module_loading() {

		$file = __DIR__ . '/sample_products/sample_theme_external/style.css';

		\ThemeGrillSDK\Loader::add_product( $file );

		$modules = \ThemeGrillSDK\Common\Module_Factory::get_modules_map();

		$this->assertArrayHasKey( 'sample_theme_external', $modules );
		$modules['sample_theme_external'] = array_filter(
			$modules['sample_theme_external'],
			[ $this, 'filter_value' ]
		);
		$this->assertCount( 0, $modules['sample_theme_external'] );
	}

	private function filter_value( $value ) {
		return ( get_class( $value ) === 'ThemeGrillSDK\\Modules\\Notification' );
	}

	public function test_notification_product_loading() {

		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		\ThemeGrillSDK\Loader::add_product( $file );

		$modules = \ThemeGrillSDK\Common\ModuleFactory::get_modules_map();

		$this->assertArrayHasKey( 'sample_theme', $modules );
		$this->assertGreaterThan( 0, count( $modules['sample_theme'] ) );
	}

	public function test_notification_can_load_partner() {

		$file    = __DIR__ . '/sample_products/sample_theme_external/style.css';
		$product = new \ThemeGrillSDK\Product( $file );

		$this->assertFalse( ( new \ThemeGrillSDK\Modules\Notification() )->can_load( $product ) );
	}

	public function test_notification_load_non_admins() {
		wp_set_current_user( self::$editor_id );
		$file    = __DIR__ . '/sample_products/sample_theme/style.css';
		$product = new \ThemeGrillSDK\Product( $file );

		$this->assertFalse( ( new \ThemeGrillSDK\Modules\Notification() )->can_load( $product ) );
	}

	public function test_notification_not_load_for_new() {
		wp_set_current_user( self::$admin_id );
		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		$product = new \ThemeGrillSDK\Product( $file );

		$this->assertFalse( ( new \ThemeGrillSDK\Modules\Notification() )->can_load( $product ) );
	}

	public function test_notification_load_old() {
		wp_set_current_user( self::$admin_id );

		update_option( 'sample_theme_install', ( time() - MONTH_IN_SECONDS ) );

		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		$product = new \ThemeGrillSDK\Product( $file );

		$this->assertTrue( ( new \ThemeGrillSDK\Modules\Notification() )->can_load( $product ) );
		$this->assertInstanceOf( 'ThemeGrillSDK\\Modules\\Notification', ( new \ThemeGrillSDK\Modules\Notification() )->load( $product ) );
	}
}
