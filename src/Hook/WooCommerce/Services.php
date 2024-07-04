<?php

namespace FRZR\Hook\WooCommerce;

if (!defined('WPTEST')) {
	defined('ABSPATH') or die("Direct access to files is prohibited");
}

class Services
{
	use \FRZR\SingletonTrait;

	private function __construct()
	{
		add_action('wp_ajax_frzr_add_to_cart', array($this, 'frzr_add_to_cart'));
		add_action('wp_ajax_nopriv_frzr_add_to_cart', array($this, 'frzr_add_to_cart'));

		add_action('woocommerce_before_calculate_totals', array($this, 'update_cart_item_price'), 10, 1);
	}

	public function frzr_add_to_cart()
	{
		if (!isset($_POST['nonce'])) {
			wp_send_json_error('Nonce value not found.');
		}

		if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'frzr-add-to-cart')) {
			wp_send_json_error('Invalid nonce value.');
		}

		if (isset($_POST['amount']) && isset($_POST['pid'])) {
			$amount = intval($_POST['amount']);
			$product_id = intval($_POST['pid']);

			// Validate Minimum Amount
			$campaign_id = get_post_meta($product_id, 'frzr_bind_campaign_id', true);
			$minimum = intval(get_post_meta($campaign_id, 'minimum', true));
			$minimum = empty($minimum) ? 1 : $minimum;
			if ($amount <= $minimum) {
				$amount = $minimum;
			}

			\WC()->cart->empty_cart();
			$add_to_cart = \WC()->cart->add_to_cart($product_id, 1, '', array(), ['amount' =>  $amount]);
			if ($add_to_cart) {
				echo 'success';
			} else {
				echo 'failed';
			}
		}
		wp_die();
	}

	public function update_cart_item_price($cart)
	{
		foreach ($cart->get_cart() as $hash => $cart_item) {
			// $product_id = $cart_item['product_id'];
			if (isset($cart_item['amount'])) {
				$cart_item_price = floatval($cart_item['amount']);
				$cart_item['data']->set_price($cart_item_price);
			}
		}
	}
}
