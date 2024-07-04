<?php

namespace FRZR\Admin;

class State
{
	public static function register()
	{
		add_action('graphql_register_types', function () {
			register_graphql_object_type('StateType', [
				'fields' => [
					'onboarding' => ['type' => 'Boolean'],
					'currency' => ['type' => 'String'],
					'plan' => ['type' => 'JSON'],
				],
			]);

			register_graphql_field('RootQuery', 'fundrizerState', [
				'type' => 'StateType',
				'resolve' => [self::class, 'resolveState'],
			]);
		});
	}

	public static function resolveState($root, $args)
	{
		$hasOnbord = true; //FixMe: Make it dynamic
		$plan = [
			'tier' => 'free',
			'email' => '',
			'key' => '',
			'expired' => ''
		];

		$payment = "woocommerce";
		$frzr_payment = get_option("frzr_payment");
		if ($frzr_payment) {
			$payment = $frzr_payment;
		}

		$currency = get_option($payment . "_currency");
		$currency = !empty($currency) ? $currency : 'IDR';

		return [
			'onboarding' => $hasOnbord,
			'currency' => $currency,
			'plan' => $plan,
		];
	}
}
