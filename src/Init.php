<?php

namespace FRZR;

trait SingletonTrait
{
	private static $instance;

	public static function init()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

class Init
{
	use SingletonTrait;

	private function __construct()
	{
		if (is_admin()) {
			$this->init_admin();
		}

		$this->init_file();

		add_action('init', [$this, 'init_wp']);
	}

	public function init_admin()
	{
		\FRZR\Admin\Admin::init();
	}

	public function init_file()
	{
		\FRZR\Helper\WPGraphQL::addTypeJSON();

		\FRZR\Admin\State::register();
		\FRZR\Admin\Setting\Query::register();
		\FRZR\Admin\Setting\Mutation::register();

		if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			require_once FRZR_PATH . '/src/Hook/WooCommerce/Module.php';

			\FRZR\Hook\WooCommerce\Admin::init();
			\FRZR\Hook\WooCommerce\Front::init();
		}
	}

	public function init_wp()
	{
		add_image_size('campaign-small', 224, 124, true);
		add_theme_support('script');

		$this->init_blocks();

		\FRZR\Modules\Campaign\Posttypes::register();
		\FRZR\Modules\Campaign\Module::init();

		require_once FRZR_PATH . '/src/Hook/MetaBox/Currency_Field.php';
		require_once FRZR_PATH . '/src/Hook/GraphQL/Insight/Query.php';


		if (class_exists('WooCommerce')) {
			\FRZR\Hook\WooCommerce\Services::init();
		}

		// Data Source
		// \FRZR\Modules\Record\Query::register();
		// \FRZR\Modules\Record\Mutation::register();
	}

	public function init_blocks()
	{
		register_meta(
			'post',
			'echo_deadline',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'wp_strip_all_tags'
			)
		);

		register_meta(
			'post',
			'echo_raised',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'wp_strip_all_tags'
			)
		);

		register_meta(
			'post',
			'echo_goal',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'wp_strip_all_tags'
			)
		);

		add_filter('block_categories_all', function ($categories) {
			$categories[] = array(
				'slug' => 'fundrizer',
				'title' => 'Fundrizer'
			);
			return $categories;
		});

		$path = FRZR_DEVMODE ? '/wp-blocks/src/' : '/src/Blocks/';
		register_block_type(FRZR_PATH . $path . 'amount-box/build');
		register_block_type(FRZR_PATH . $path . 'campaign-progress/build');
	}
}
