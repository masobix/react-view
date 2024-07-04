<?php

if (!defined('ABSPATH')) {
	exit;
}

use \FRZR\Modules\Campaign\MetaUpdate;

add_action('init', function () {
	if (!class_exists('WC_Product_Fundraising')) {
		class WC_Product_Fundraising extends \WC_Product
		{
			public $product_type;

			public function __construct($product)
			{
				parent::__construct($product);
				$this->product_type = 'fundraising';
				$this->set_virtual(true);
			}
		}
	}
});

add_filter('woocommerce_is_purchasable', function ($purchasable, $product) {
	if ($product->get_type() === 'fundraising') {
		$purchasable = true;
	}
	return $purchasable;
}, 10, 2);

add_action('woocommerce_order_status_completed', 'frzr_woo_email_order_completed', 10, 1);
function frzr_woo_email_order_completed($order_id)
{
	if (frzr_is_fundraising_product($order_id)) {
		add_filter('woocommerce_email_enabled_customer_completed_order', function ($yesno, $object) {
			return false;
		}, 10, 2);
		$email_new_order = WC()->mailer()->get_emails()['FRZR_Contribution_Completed_Email'];
		$email_new_order->trigger($order_id);

		// Update Campaign Data
		$binding_ids = frzr_get_binding_ids($order_id);
		if (is_array($binding_ids)) {
			foreach ($binding_ids as $product_id => $campaign_id) {
				$campaign_data = new MetaUpdate();
				$campaign_data->set_id($campaign_id)->funding(frzr_get_amount_order($order_id))->refresh();
			}
		}
	}
}

add_action('woocommerce_order_status_pending', 'frzr_woo_email_new_order', 10, 1);
add_action('woocommerce_order_status_processing', 'frzr_woo_email_new_order', 10, 1);
function frzr_woo_email_new_order($order_id)
{
	if (frzr_is_fundraising_product($order_id)) {
		add_filter('woocommerce_email_enabled_new_order', function ($yesno, $object) {
			return false;
		}, 10, 2);
		add_filter('woocommerce_email_enabled_customer_processing_order', function ($yesno, $object) {
			return false;
		}, 10, 2);
		add_filter('woocommerce_email_enabled_customer_on_hold_order', function ($yesno, $object) {
			return false;
		}, 10, 2);

		$email_new_order = WC()->mailer()->get_emails()['FRZR_New_Contribution_Email'];
		$email_new_order->trigger($order_id);
	}
}

function frzr_is_fundraising_product($order_id)
{
	$order = wc_get_order($order_id);
	if (!$order || !$order->get_items()) {
		return;
	}

	$has_fundraising_product = false;
	foreach ($order->get_items() as $item) {
		$product = $item->get_product();
		if ($product && $product->is_type('fundraising')) {
			$has_fundraising_product = true;
			break;
		}
	}

	return $has_fundraising_product;
}

function frzr_get_binding_ids($order_id)
{
	$order = wc_get_order($order_id);
	if (!$order || !$order->get_items()) {
		return array();
	}

	$binding_ids = array();
	foreach ($order->get_items() as $item_id => $item) {
		$product = $item->get_product();
		if ($product && $product->get_id() && $product->is_type('fundraising')) {
			$campaign_id = get_post_meta($product->get_id(), 'frzr_bind_campaign_id', true);
			if (!empty($campaign_id)) {
				$binding_ids[$product->get_id()] = $campaign_id;
			}
		}
	}

	return $binding_ids;
}

function frzr_get_amount_order($order_id)
{
	$order = wc_get_order($order_id);

	foreach ($order->get_items() as $item_key => $item) :
		$subtotal = $item->get_subtotal();
		return $subtotal;
	endforeach;
}

add_filter('woocommerce_email_classes', function ($email_classes) {
	require_once FRZR_PATH . 'src/Hook/WooCommerce/Notification/class-new-contribution-email.php';
	require_once FRZR_PATH . 'src/Hook/WooCommerce/Notification/class-contribution-completed-email.php';

	$email_classes['FRZR_New_Contribution_Email'] = new FRZR_New_Contribution_Email();
	$email_classes['FRZR_Contribution_Completed_Email'] = new FRZR_Contribution_Completed_Email();

	return $email_classes;
});

add_action('woocommerce_checkout_update_order_meta', function ($order_id, $posted) {
	if (frzr_is_fundraising_product($order_id)) {
		$order = wc_get_order($order_id);
		$order->update_meta_data('fundraising', 'true');
		$order->save();

		if (!get_post_meta($order->get_id(), 'fundraising', true)) {
			update_post_meta($order->get_id(), 'fundraising', 'true');
		}
	}
}, 10, 2);
