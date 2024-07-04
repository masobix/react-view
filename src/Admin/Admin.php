<?php

namespace FRZR\Admin;

if (!defined('WPTEST')) {
	defined('ABSPATH') or exit('Direct access to files is prohibited');
}

class Admin
{
	use \FRZR\SingletonTrait;

	private function __construct()
	{
		add_action('init', [$this, 'flush_permalink']);
		add_action('admin_menu', [$this, 'add_menus'], 10);
		add_action('admin_notices', [$this, 'add_payment_system'], 10);


		if (!FRZR_DEVMODE) {
			add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
		}

		if (self::is_admin_page()) {
			add_action(
				'admin_print_scripts',
				function () {
					global $wp_filter;
					unset($wp_filter['admin_notices']);
					unset($wp_filter['all_admin_notices']);
				}
			);
		}

		if (FRZR_DEVMODE && class_exists('FRZR\Admin\DevMode')) {
			DevMode::init();
		// } else {
		// 	// add_filter('graphql_show_admin', function () {
		// 	// 	return false;
		// 	// });
		}

		add_action(
			'admin_head',
			function () {
				wp_add_inline_style('frzr-admin-menu', "li#toplevel_page_fundrizer { margin-top: 14px !important; }
				li#toplevel_page_campaign{ margin-bottom: 4px !important; };
				th#create_woocommerce_product { width: 120px; }
				#toplevel_page_fundrizer .wp-menu-image.svg { margin-left: 6px !important; width: 28px; }';");
			}
		);
	}

	public function add_payment_system()
	{
		if (!class_exists('WooCommerce')) {
			echo '<div class="notice notice-info is-dismissible"><p>' . esc_html__('Please consider installing WooCommerce to accept funds with Fundrizer. ', 'fundrizer') . '<a href="' . esc_url(admin_url('plugin-install.php?s=WooCommerce&tab=search&type=term')) . '">' . esc_html__('Install WooCommerce Now', 'fundrizer') .  '</a>' . '</p></div>';
		}
	}

	public function add_menus()
	{
		$has_pro = is_plugin_active('fundrizer-pro/fundrizer-pro.php');
		$menu_title = $has_pro ? 'Fundrizer Pro' : 'Fundrizer';

		add_menu_page(
			$menu_title,
			$menu_title,
			'manage_options',
			'fundrizer',
			[$this, 'admin_page'],
			'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgd2lkdGg9IjEwODBweCIgaGVpZ2h0PSIxMDgwcHgiIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgo8Zz48cGF0aCBzdHlsZT0ib3BhY2l0eTowLjk5NSIgZmlsbD0iI2ZlZmZmZSIgZD0iTSA3My41LDY1LjUgQyAzODQuMTY3LDY1LjMzMzMgNjk0LjgzNCw2NS41IDEwMDUuNSw2NkMgOTEzLjg2MSwxNTguNjM5IDgyMS44NjEsMjUwLjk3MyA3MjkuNSwzNDNDIDUxMC44MzQsMzQzLjUgMjkyLjE2NywzNDMuNjY3IDczLjUsMzQzLjVDIDczLjUsMjUwLjgzMyA3My41LDE1OC4xNjcgNzMuNSw2NS41IFoiLz48L2c+CjxnPjxwYXRoIHN0eWxlPSJvcGFjaXR5OjAuOTk2IiBmaWxsPSIjZmVmZmZlIiBkPSJNIDM1MC41LDQ3Mi41IEMgNDc2LjgzMyw0NzIuNSA2MDMuMTY3LDQ3Mi41IDcyOS41LDQ3Mi41QyA3MjkuNjY3LDU2NS4xNjcgNzI5LjUsNjU3LjgzNCA3MjksNzUwLjVDIDY0Mi4xOTQsODM4LjMwNiA1NTUuMDI3LDkyNS44MDYgNDY3LjUsMTAxM0MgMzM2LjE2NywxMDEzLjY3IDIwNC44MzMsMTAxMy42NyA3My41LDEwMTNDIDE2MC44MzMsOTI1LjY2NyAyNDguMTY3LDgzOC4zMzMgMzM1LjUsNzUxQyAyNDguMTY3LDc1MC42NjcgMTYwLjgzMyw3NTAuMzMzIDczLjUsNzUwQyAxNjYuMDM1LDY1Ny42MzIgMjU4LjM2OCw1NjUuMTMyIDM1MC41LDQ3Mi41IFoiLz48L2c+Cjwvc3ZnPgo=',
			40
		);

		if (!$has_pro) {
			add_submenu_page('fundrizer', '', '', 'manage_options', 'fundrizer', '__return_null');
			remove_submenu_page('fundrizer', 'fundrizer');
		}

		$campaigns = esc_html('Campaigns', 'fundrizer');
		$campaigns_update = esc_html('Updates', 'fundrizer');
		// $campaigns_transaction = esc_html('Transactions', 'fundrizer');

		add_menu_page(
			'Campaigns',
			'Campaigns',
			'manage_options',
			'campaign',
			[$this, ''],
			'dashicons-megaphone',
			42
		);

		add_submenu_page('campaign', '', '', 'manage_options', 'campaign', '__return_null');
		remove_submenu_page('campaign', 'campaign');

		add_submenu_page(
			'campaign',
			$campaigns,
			$campaigns,
			'manage_options',
			'edit.php?post_type=frzr_campaign'
		);

		// add_submenu_page(
		// 	'campaign',
		// 	'├ ' . $campaigns_transaction,
		// 	'├ ' . $campaigns_transaction,
		// 	'manage_options',
		// 	'edit.php?post_type=frzr_campaign_update'
		// );

		add_submenu_page(
			'campaign',
			'├ ' . $campaigns_update,
			'├ ' . $campaigns_update,
			'manage_options',
			'edit.php?post_type=frzr_campaign_update'
		);
	}

	public function admin_page()
	{
		echo '<div id="root"></div>';
	}


	public function enqueue_scripts()
	{
		if (self::is_admin_page()) {

			$endpoint = esc_url(site_url() . '/graphql');
			if (get_option('graphql_general_settings')) {
				$graphql_options = get_option('graphql_general_settings');
				$endpoint = esc_url(site_url() . '/' . $graphql_options['graphql_endpoint']);
			}

			wp_enqueue_script('frzr-admin', FRZR_URI . 'src/Admin/assets/fundrizer-admin.js', [], FRZR_VERSION, true);
			wp_localize_script(
				'frzr-admin',
				'fundrizer_admin',
				[
					'endpoint' => $endpoint,
					'pro' => is_plugin_active('fundrizer-pro/fundrizer-pro.php') ? 'active' : '',
				]
			);
			wp_enqueue_style('frzr-admin', FRZR_URI . 'src/Admin/assets/fundrizer-admin.css', [], FRZR_VERSION);
		}

	}

	public static function is_admin_page()
	{
		$query_params = wp_parse_url(esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])), PHP_URL_QUERY);
		parse_str($query_params ?? '', $param);

		if (isset($param['page']) && $param['page'] === 'fundrizer') {
			return true;
		}

		return false;
	}

	public function flush_permalink()
	{
		if (!get_option('frzr_flush_permalink')) {
			flush_rewrite_rules();
			get_option('frzr_flush_permalink', true);
		}
	}
}
