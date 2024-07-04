<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class RWMB_Currency_Field extends RWMB_Field
{
	public static function html($meta, $field)
	{
		$meta = is_numeric($meta) ? $meta : '';

		$formatted_currency = number_format((float) $meta, 0, '.', ',');

		$html = sprintf(
			'<input type="text" name="%s" id="%s" class="rwmb-number rwmb-currency-field" value="%s" placeholder="%s" />',
			$field['field_name'],
			$field['id'],
			$formatted_currency,
			isset($field['placeholder']) ? $field['placeholder'] : '0'
		);

		return $html;
	}
}

add_filter('rwmb_currency_value', function ($value, $field) {
	if ('currency' === $field['type']) {
		$value = floatval(preg_replace("/[,.]/", "", $value));
	}
	return $value;
}, 10, 2);

add_action('rwmb_enqueue_scripts', function ($meta_box) {
	add_action('admin_print_scripts', function () {
		echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                var currencyInputs = document.querySelectorAll(".rwmb-currency-field");
                currencyInputs.forEach(function(input) {
                    input.addEventListener("input", function(event) {
                        var value = event.target.value.replace(/[^\d.]/g, "");
                        var formattedValue = new Intl.NumberFormat("en-US", {
                            currency: "USD",
                            minimumFractionDigits: 0
                        }).format(value);
                        event.target.value = formattedValue;
                    });
                });
            });
        </script>';
	});
});
