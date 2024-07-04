<?php

namespace FRZR\Helper;

class Utils
{
	public static function get_currency()
	{
		$woo_currency = get_option("woocommerce_currency");
		return !empty($woo_currency) ? $woo_currency : "USD";
	}

	public static function currency_format(float $amount, $options = array(), string $currency_args = ""): string
	{
		$currency = empty($currency_args) ? self::get_currency() : $currency_args;

		// Ensure currency exists to prevent undefined array key errors
		$currencies = [
			"IDR" => [
				"symbol" => "Rp",
				"thousand" => ".",
				"decimal" => ",",
				"decimals" => 0,
				"min" => 10000,
			],
			"MYR" => [
				"symbol" => "RM",
				"thousand" => ",",
				"decimal" => ".",
				"decimals" => 0,
				"min" => 1,
			],
			"AUD" => [
				"symbol" => "$",
				"thousand" => ",",
				"decimal" => ".",
				"decimals" => 2,
				"min" => 1,
			],
			"USD" => [
				"symbol" => "$",
				"thousand" => ",",
				"decimal" => ".",
				"decimals" => 0,
				"min" => 1,
			],
		];

		$selectedCurrency = array_key_exists($currency, $currencies) ? $currencies[$currency] : null;

		if (!$selectedCurrency) {
			return "Currency is not supported";
		}

		$symbol = $selectedCurrency['symbol'];
		$thousand = $selectedCurrency['thousand'];
		$decimal = $selectedCurrency['decimal'];
		$decimals = $selectedCurrency['decimals'];


		if (isset($options) && in_array("no_symbol", $options)) {
			$symbol = null;
		}

		$display = $symbol . number_format($amount, $decimals, $decimal, $thousand);

		return apply_filters("fundrizer/currency/formatting", $display);
	}
}
