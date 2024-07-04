<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action('graphql_register_types', function () {
	register_graphql_object_type('InsightType', [
		'fields' => [
			'raisedFunds' => ['type' => 'Float'],
			'unpaidFunds' => ['type' => 'Float'],
			'backers' => ['type' => 'Int'],
			'averageFunds' => ['type' => 'Float'],
		],
	]);

	register_graphql_field('RootQuery', 'fundrizerInsight', [
		'type' => 'InsightType',
		'args' => [
			'campaignId' => [
				'type' => 'String',
				'description' => 'ID of the campaign',
			],
			'date' => [
				'type' => 'String',
				'description' => 'Date in the format YYYY-MM-DD',
			],
		],
		'resolve' => function ($root, $args) {

			if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

				$woocommerceData = array();
				$query_args = array(
					'limit' => -1,
					'return' => 'ids',
					'meta_query' => array(
						array(
							'key' => 'fundraising',
							'value' => 'true',
							'compare' => '=',
						),
					)
				);

				$order_ids = wc_get_orders($query_args);

				if ($order_ids) {
					foreach ($order_ids as $order_id) {
						$order = wc_get_order($order_id);

						if ($order) {
							$trx_id = $order_id;
							$product_ids = frzr_get_product_ids($order->get_items());
							$product_id = intval($product_ids[0]);
							$campaign_id = get_post_meta($product_id, 'frzr_bind_campaign_id', true);
							$campaign_title = html_entity_decode(get_the_title($campaign_id), ENT_QUOTES, 'UTF-8');
							$backer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
							if (empty($backer_name)) {
								$backer_name = $order->get_meta('billing_fullname');
							}
							$payment_id = $order->get_payment_method();
							$payment_name = $order->get_payment_method_title();
							$amount =  $order->get_total();
							$currency = $order->get_currency();

							$date = get_date_from_gmt($order->get_date_created(), 'Y-m-d H:i:s');

							$status = $order->get_status();

							$status_peg = array(
								'on-hold' => 'hold',
								'wc-processing' => 'hold',
								'wc-completed' => 'done',
								'processing' => 'hold',
								'completed' => 'done',
							);
							$status = isset($status_peg[$status]) ? $status_peg[$status] : 'unknown';

							if ($status === "unknown") {
								continue;
							}

							$woocommerceData[] = array(
								'trx_id' => $trx_id,
								'campaign_id' => $campaign_id,
								'campaign_title' => $campaign_title,
								'backer_name' => $backer_name,
								'payment_id' => $payment_id,
								'payment_name' => $payment_name,
								'amount' => (int)$amount,
								'currency' => $currency,
								'date' => $date,
								'status' => $status
							);
						}
					}

					$records = $woocommerceData;
					$metrics = [];

					foreach ($records as $record) {
						$date = explode(' ', $record['date'])[0];
						$campaignId = $record['campaign_id'];
						$amount = $record['amount'];

						if (!isset($metrics[$campaignId][$date])) {
							$metrics[$campaignId][$date] = [
								'totalFunds' => 0,
								'fundsRaised' => 0,
								'bakers' => 0,
								'unpaidFunds' => 0,
								'averageFund' => 0
							];
						}

						$metrics[$campaignId][$date]['totalFunds'] += $amount;
						$metrics[$campaignId][$date]['bakers']++;
						$metrics[$campaignId][$date]['averageFund'] = $metrics[$campaignId][$date]['fundsRaised'] / $metrics[$campaignId][$date]['bakers'];

						if ($record['status'] === 'hold') {
							$metrics[$campaignId][$date]['unpaidFunds'] += $amount;
						}

						if ($record['status'] === 'done') {
							$metrics[$campaignId][$date]['fundsRaised'] += $amount;
						}
					}

					$data = ['metrics' => [], 'campaigns' => [], 'dates' => []];
					foreach ($metrics as $campaignId => $campaignMetrics) {
						foreach ($campaignMetrics as $date => $metricsData) {
							$metricsData['campaignId'] = $campaignId;
							$metricsData['date'] = $date;
							$data['metrics'][] = $metricsData;
						}
					}

					$filteredMetrics = array_filter($data["metrics"], function ($metric) use ($args) {
						$isCampaignWildcard = $args['campaignId'] === '*' || $args['campaignId'] === 'all';
						$isDateWildcard = $args['date'] === '*' || $args['date'] === 'all';

						if ($isCampaignWildcard && $isDateWildcard) {
							return true;
						} elseif ($isCampaignWildcard) {
							return $metric["date"] == $args['date'];
						} elseif ($isDateWildcard) {
							return $metric["campaignId"] == $args['campaignId'];
						} else {
							return $metric["campaignId"] == $args['campaignId'] && $metric["date"] == $args['date'];
						}
					});

					$raisedFunds = array_sum(array_column($filteredMetrics, 'fundsRaised'));
					$unpaidFunds = array_sum(array_column($filteredMetrics, 'unpaidFunds'));
					$backers = array_sum(array_column($filteredMetrics, 'bakers'));
					$averageFunds = round($backers > 0 ? ($raisedFunds / $backers) : 0, 2);
				}
			}

			return [
				'raisedFunds' => empty($raisedFunds) ? 0 : $raisedFunds,
				'unpaidFunds' => empty($unpaidFunds) ? 0 : $unpaidFunds,
				'backers' => empty($backers) ? 0 : $backers,
				'averageFunds' => empty($averageFunds) ? 0 : $averageFunds,
			];
		},
	]);
});


function frzr_get_product_ids($order_items)
{
	$product_ids = [];
	foreach ($order_items as $item_id => $item) {
		$product_ids[] = $item->get_product_id();
	}
	return $product_ids;
}
