<?php
/**
 * Script loader feature test.
 *
 * @package ThemeGrillSDK
 */

/**
 * Test script logger feature.
 */
class ScriptLoader_Test extends WP_UnitTestCase {


	public function test_scriptLoader_module_loading() {

		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		\ThemeGrillSDK\Loader::add_product( $file );

		$modules = \ThemeGrillSDK\Common\ModuleFactory::get_modules_map();

		$this->assertArrayHasKey( 'sample_theme', $modules );
		$modules['sample_theme'] = array_filter(
			$modules['sample_theme'],
			[ $this, 'filter_value' ]
		);
		$this->assertEquals( count( $modules['sample_theme'] ), 1 );
	}

	private function filter_value( $value ) {
		return ! empty( $value ) && ( get_class( $value ) === 'ThemeGrillSDK\\Modules\\ScriptLoader' );
	}

	public function test_scriptLoader_product_loading() {

		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		\ThemeGrillSDK\Loader::add_product( $file );

		$modules = \ThemeGrillSDK\Common\ModuleFactory::get_modules_map();

		$this->assertArrayHasKey( 'sample_theme', $modules );
		$this->assertGreaterThan( 0, count( $modules['sample_theme'] ) );
	}

	public function test_scriptLoader_can_not_load_partner() {

		$file    = __DIR__ . '/sample_products/sample_theme_external/style.css';
		$product = new \ThemeGrillSDK\Product( $file );

		$this->assertFalse( ( new \ThemeGrillSDK\Modules\ScriptLoader() )->can_load( $product ) );
	}

	public function test_scriptLoader_load_normal() {
		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		$product = new \ThemeGrillSDK\Product( $file );

		$this->assertTrue( ( new \ThemeGrillSDK\Modules\ScriptLoader() )->can_load( $product ) );
		$this->assertInstanceOf( 'ThemeGrillSDK\\Modules\\ScriptLoader', ( new \ThemeGrillSDK\Modules\ScriptLoader() )->load( $product ) );
	}

	public function test_scriptLoader_filters_check() {
		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		$product = new \ThemeGrillSDK\Product( $file );

		$module = ( new \ThemeGrillSDK\Modules\ScriptLoader() )->load( $product );

		// Check if the hooks are available.
		$this->assertEquals( has_filter( 'themegrill_sdk_dependency_script_handler', [ $module, 'get_script_handler' ] ), 10 );
		$this->assertEquals( has_action( 'themegrill_sdk_dependency_enqueue_script', [ $module, 'enqueue_script' ] ), 10 );
	}

	public function test_multiple_script_loading() {
		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		$product = new \ThemeGrillSDK\Product( $file );

		/**
		 * When multiple products are loaded, the script loader hooks registration should not be triggered multiple times.
		 */
		( new \ThemeGrillSDK\Modules\ScriptLoader() )->load( $product );
		( new \ThemeGrillSDK\Modules\ScriptLoader() )->load( $product );
		( new \ThemeGrillSDK\Modules\ScriptLoader() )->load( $product );

		// Load survey script.
		$handler = apply_filters( 'themegrill_sdk_dependency_script_handler', 'survey' );
		$this->assertNotEmpty( $handler );
		$this->assertTrue( 'themegrill_sdk_survey_script' === $handler );
		do_action( 'themegrill_sdk_dependency_enqueue_script', 'survey' );
		$this->assertTrue( wp_script_is( $handler, 'enqueued' ) );

		// Load tracking script.
		$handler = apply_filters( 'themegrill_sdk_dependency_script_handler', 'tracking' );
		$this->assertNotEmpty( $handler );
		$this->assertTrue( 'themegrill_sdk_tracking_script' === $handler );
		do_action( 'themegrill_sdk_dependency_enqueue_script', 'tracking' );
		$this->assertTrue( wp_script_is( $handler, 'enqueued' ) );

		$this->assertTrue( has_filter( 'themegrill_sdk_script_setup' ) );
	}

	public function test_scriptLoader_handler_check() {
		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		$product = new \ThemeGrillSDK\Product( $file );

		( new \ThemeGrillSDK\Modules\ScriptLoader() )->load( $product );

		// Existing dependencies should have a handler.
		$handler = apply_filters( 'themegrill_sdk_dependency_script_handler', 'survey' );
		$this->assertNotEmpty( $handler );

		$handler = apply_filters( 'themegrill_sdk_dependency_script_handler', 'tracking' );
		$this->assertNotEmpty( $handler );

		// Non-existing dependencies should not have a handler.
		$handler = apply_filters( 'themegrill_sdk_dependency_script_handler', 'test' );
		$this->assertEmpty( $handler );
	}

	public function test_scriptLoader_enqueue_script() {
		$file = __DIR__ . '/sample_products/sample_theme/style.css';

		$product = new \ThemeGrillSDK\Product( $file );

		( new \ThemeGrillSDK\Modules\ScriptLoader() )->load( $product );

		// Load survey script.
		$handler = apply_filters( 'themegrill_sdk_dependency_script_handler', 'survey' );
		$this->assertNotEmpty( $handler );
		do_action( 'themegrill_sdk_dependency_enqueue_script', 'survey' );
		$this->assertTrue( wp_script_is( $handler, 'enqueued' ) );

		// Load tracking script.
		$handler = apply_filters( 'themegrill_sdk_dependency_script_handler', 'tracking' );
		$this->assertNotEmpty( $handler );
		do_action( 'themegrill_sdk_dependency_enqueue_script', 'tracking' );
		$this->assertTrue( wp_script_is( $handler, 'enqueued' ) );

		// Load test script (it does not exist so it should not be enqueued).
		$handler = apply_filters( 'themegrill_sdk_dependency_script_handler', 'test' );
		$this->assertEmpty( $handler );
		do_action( 'themegrill_sdk_dependency_enqueue_script', 'test' );
		$this->assertFalse( wp_script_is( $handler, 'enqueued' ) );
	}

	/**
	 * Test the load_survey_for_product method.
	 *
	 * @return void
	 */
	public function test_load_survey_for_product() {
		$file         = __DIR__ . '/sample_products/sample_theme/style.css';
		$product      = new \ThemeGrillSDK\Product( $file );
		$scriptLoader = new \ThemeGrillSDK\Modules\ScriptLoader();

		$scriptLoader->load_survey_for_product( $product->get_slug(), [] );

		// Verify the survey script is loaded
		$handler = $scriptLoader->get_script_handler( 'survey' );
		$this->assertNotEmpty( $handler );
		$this->assertTrue( wp_script_is( $handler, 'enqueued' ) );
	}

	/**
	 * Test the get_survey_common_data method.
	 *
	 * @return void
	 */
	public function test_get_survey_common_data() {
		$scriptLoader = new \ThemeGrillSDK\Modules\ScriptLoader();

		// Set up test filters
		add_filter(
			'themegrill_sdk_current_lang',
			function () {
				return 'de_DE';
			}
		);

		add_filter(
			'themegrill_sdk_current_site_url',
			function () {
				return 'https://example.com/wordpress';
			}
		);

		$data = $scriptLoader->get_survey_common_data( [ 'attributes' => [ 'install_days_number' => 3 ] ] );

		// Assert the structure and content of returned data
		$this->assertIsArray( $data );
		$this->assertArrayHasKey( 'userId', $data );
		$this->assertArrayHasKey( 'attributes', $data );
		$this->assertArrayHasKey( 'language', $data['attributes'] );
		$this->assertArrayHasKey( 'days_since_install', $data['attributes'] );

		// Test install category.
		$this->assertEquals( 7, $data['attributes']['days_since_install'] );

		// Test German language mapping
		$this->assertEquals( 'de', $data['attributes']['language'] );

		// Test userId generation
		$expected_user_id = 'u_' . hash( 'crc32b', 'example.com/wordpress' );
		$this->assertEquals( $expected_user_id, $data['userId'] );

		// Test with different language that are not yet supported.
		add_filter(
			'themegrill_sdk_current_lang',
			function () {
				return 'fr_FR';
			}
		);
		$data = $scriptLoader->get_survey_common_data();
		$this->assertEquals( 'en', $data['attributes']['language'] );
	}

	/**
	 * Test the secret masking filter.
	 *
	 * @return void
	 */
	public function test_secret_masking() {
		$scriptLoader = new \ThemeGrillSDK\Modules\ScriptLoader();

		// Test normal string
		$this->assertEquals( '****test', $scriptLoader->secret_masking( 'testtest' ) );

		// Test odd length string
		$this->assertEquals( '***test', $scriptLoader->secret_masking( 'footest' ) );

		// Test empty string
		$this->assertEquals( '', $scriptLoader->secret_masking( '' ) );

		// Test non-string input
		$this->assertEquals( 123, $scriptLoader->secret_masking( 123 ) );
		$this->assertEquals( null, $scriptLoader->secret_masking( null ) );
		$this->assertEquals( [], $scriptLoader->secret_masking( [] ) );
	}

	/**
	 * Test the install_time_category method for all cases.
	 *
	 * @return void
	 */
	public function test_install_time_category() {
		$scriptLoader = new \ThemeGrillSDK\Modules\ScriptLoader();

		$test_data = [
			// Test default case (0-1 days)
			0   => 0,
			1   => 0,
			// Test 1-7 days category
			2   => 7,
			5   => 7,
			7   => 7,
			// Test 8-30 days category
			8   => 30,
			15  => 30,
			30  => 30,
			// Test 31-89 days category
			31  => 90,
			45  => 90,
			89  => 90,
			// Test 90+ days category
			90  => 91,
			100 => 91,
			365 => 91,
		];

		foreach ( $test_data as $days => $expected ) {
			$data = $scriptLoader->get_survey_common_data( [ 'attributes' => [ 'install_days_number' => $days ] ] );
			$this->assertEquals( $expected, $data['attributes']['days_since_install'], "Failed asserting that {$days} days maps to category {$expected}" );
		}
	}
}
