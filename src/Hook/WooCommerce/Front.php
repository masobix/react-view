<?php

namespace FRZR\Hook\WooCommerce;

if (!defined('WPTEST')) {
	defined('ABSPATH') or die("Direct access to files is prohibited");
}

class Front
{
	use \FRZR\SingletonTrait;

	public function __construct()
	{
		add_action('wp_enqueue_scripts', [$this, 'add_to_cart_script']);
	}

	public function add_to_cart_script()
	{
		$inline_script = "
        document.addEventListener('DOMContentLoaded', function() {
            var buttons = document.querySelectorAll('.fundrizer-button');

            buttons.forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();

                    var amountBox = document.getElementById('amount-box');
                    var productId = amountBox.getAttribute('data-product-id');

                    // Input Validation
                    if (!amountBox.value) {
                        amountBox.style.border = '1px solid red';
                        amountBox.value = '';
                        return false;
                    } else {
                        amountBox.style.border = '';
                    }

                    // Minimum JS Validation
                    var enteredValue = parseFloat(amountBox.value.replace(/,/g, ''));
                    var minValue = parseFloat(amountBox.getAttribute('min'));

                    if (enteredValue < minValue) {
                        amountBox.style.border = '1px solid red';
                        amountBox.value = minValue;
                        return false;
                    } else {
                        amountBox.style.border = '';
                    }

                    // Start Processing
                    var anchorElement = this.querySelector('a');
                    if (anchorElement) {
                        anchorElement.innerText = '...';
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '" . esc_url(admin_url('admin-ajax.php')) . "', true);

                    // Set up FormData
                    var formData = new FormData();
                    formData.append('action', 'frzr_add_to_cart');
                    formData.append('amount', amountBox.value);
                    formData.append('pid', productId);
                    formData.append('nonce', '" . esc_js(wp_create_nonce('frzr-add-to-cart')) . "');

                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var responseText = xhr.responseText;
                            if (responseText.trim() === 'success') {
                                window.location.href = '" . esc_url(\wc_get_checkout_url()) . "';
                            }
                        }
                    };

                    xhr.send(formData);

                });
            });
        });";
		wp_add_inline_script('create-block-amount-box-view-script', $inline_script);
	}
}
