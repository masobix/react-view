<?php
if (!defined('ABSPATH')) {
	exit;
}

echo '<p>' . sprintf(
	/* translators: %s: First name */
	esc_html__('Hello %s,', 'fundrizer'),
	esc_html($order->get_billing_first_name())
) . '</p>';

echo '<p>' . esc_html__('Thank you for your contribution.', 'fundrizer') . '</p>';
echo '<p>' . esc_html__('Details of your contribution:', 'fundrizer') . '</p>';
echo '<ul>';
foreach ($order->get_items() as $item_id => $item) {
	$product = $item->get_product();
	if ($product && $product->is_type('fundraising')) {
		echo '<li>' . esc_html($product->get_name()) . ': ' . esc_html(wc_price($item->get_total())) . '</li>';
	}
}
echo '</ul>';

// Payment Information
echo '<p>' . esc_html__('Payment Information:', 'fundrizer') . '</p>';
echo '<p>' . esc_html__('Payment Method: ', 'fundrizer') . esc_html($order->get_payment_method()) . '</p>';
echo '<p>' . esc_html__('Total Amount: ', 'fundrizer') . esc_html(wc_price($order->get_total())) . '</p>';

// Payment Instructions
if ($order->get_payment_method() === 'bacs') { // Check if payment method is bank transfer
	$bacs_settings = get_option('woocommerce_bacs_accounts');

	if (!empty($bacs_settings)) {
		foreach ($bacs_settings as $bacs_account) {
			echo '<p>' . esc_html__('Bank Account Information:', 'fundrizer') . '</p>';
			echo '<p>' . esc_html__('Account Name: ', 'fundrizer') . esc_html($bacs_account['account_name']) . '</p>';
			echo '<p>' . esc_html__('Account Number: ', 'fundrizer') . esc_html($bacs_account['account_number']) . '</p>';
			echo '<p>' . esc_html__('Sort Code: ', 'fundrizer') . esc_html($bacs_account['sort_code']) . '</p>';
		}
	} else {
		echo '<p>' . esc_html__('No bank account information found.', 'fundrizer') . '</p>';
	}
} else {
	// Display default payment instructions from WooCommerce settings
	$payment_instructions = get_option('woocommerce_bacs_description');
	if (!empty($payment_instructions)) {
		echo '<p>' . esc_html__('Please complete your payment using the following instructions:', 'fundrizer') . '</p>';
		echo esc_html($payment_instructions); // Escaping applied here
	} else {
		echo '<p>' . esc_html__('Payment instructions are not available for this method', 'fundrizer') . '</p>';
	}
}
