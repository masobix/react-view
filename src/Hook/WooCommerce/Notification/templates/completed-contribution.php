<?php

/**
 * Completed Contribution Email HTML Template
 */

if (!defined('ABSPATH')) {
	exit;
}

echo '<p>' . sprintf(
	/* translators: %s: User's first name */
	esc_html__('Hello %s,', 'fundrizer'),
	esc_html($order->get_billing_first_name())
) . '</p>';

echo '<p>' . esc_html__('Thank You for Completing Your Contribution!', 'fundrizer') . '</p>';
echo '<p>' . esc_html__('Details of your contribution:', 'fundrizer') . '</p>';
echo '<ul>';
foreach ($order->get_items() as $item_id => $item) {
	$product = $item->get_product();
	if ($product && $product->is_type('fundraising')) {
		echo '<li>' . esc_html($product->get_name()) . ': ' . esc_html(wc_price($item->get_total())) . '</li>';
	}
}
echo '</ul>';
