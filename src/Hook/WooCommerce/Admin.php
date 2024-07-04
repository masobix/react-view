<?php

namespace FRZR\Hook\WooCommerce;

if (!defined('WPTEST')) {
	defined('ABSPATH') or die("Direct access to files is prohibited");
}

class Admin
{
	use \FRZR\SingletonTrait;

	private function __construct()
	{
		$this->add_fundraising_product_type();
		$this->add_campaign_product();
		$this->add_campaign_metabox_on_product();
	}

	public function add_fundraising_product_type()
	{
		add_filter('product_type_selector', function ($types) {
			$types['fundraising'] = __('Fundraising', 'woocommerce');
			return $types;
		});
	}

	public function add_campaign_metabox_on_product()
	{
		// Add metabox to product edit page
		add_action('add_meta_boxes', function () {
			add_meta_box(
				'campaign_metabox',
				'Select Campaign',
				function ($post) {
					// Add nonce for security
					wp_nonce_field('save-campaign-data', 'campaign_nonce');

					$selected_campaign = get_post_meta($post->ID, 'frzr_bind_campaign_id', true);
					$campaigns = get_posts(array(
						'post_type' => 'frzr_campaign',
						'posts_per_page' => -1,
					));

					if ($campaigns) {
						echo '<label for="selected_campaign">' . esc_html__('Select a campaign', 'fundrizer') . ':</label>';
						echo '<select id="selected_campaign" name="selected_campaign">';
						echo '<option value="">' . esc_html__('Select...', 'fundrizer') . '</option>';
						foreach ($campaigns as $campaign) {
							echo '<option value="' . esc_attr($campaign->ID) . '"' . selected($selected_campaign, $campaign->ID, false) . '>' . esc_html($campaign->post_title) . '</option>';
						}
						echo '</select>';
					} else {
						echo esc_html__('No campaigns found.', 'fundrizer');
					}
				},
				'product',
				'side',
				'default'
			);
		});

		// Save the selected campaign with the product
		add_action('save_post_product', function ($post_id) {
			if (!isset($_POST['campaign_nonce'])) {
				return;
			}

			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['campaign_nonce'])), 'save-campaign-data')) {
				return;
			}

			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}

			if (!current_user_can('edit_post', $post_id)) {
				return;
			}

			if (isset($_POST['selected_campaign'])) {
				update_post_meta($post_id, 'frzr_bind_campaign_id', sanitize_text_field($_POST['selected_campaign']));
			} else {
				delete_post_meta($post_id, 'frzr_bind_campaign_id');
			}
		});
	}

	public function add_campaign_product()
	{
		add_filter('manage_frzr_campaign_posts_columns', function ($columns) {
			$columns['create_woocommerce_product'] = esc_html('Product', 'fundrizer');
			return $columns;
		});

		add_action('manage_frzr_campaign_posts_custom_column', function ($column, $post_id) {
			if ($column == 'create_woocommerce_product') {
				$campaign_title = html_entity_decode(get_the_title($post_id), ENT_QUOTES, 'UTF-8');
				$existing_product = get_posts(array(
					'post_type'      => 'product',
					'posts_per_page' => 1,
					'title'     => $campaign_title
				));

				if ($existing_product && is_array($existing_product) && !empty($existing_product)) {
					$product_id = $existing_product[0]->ID;
					wp_set_object_terms($product_id, 'fundraising', 'product_type');
					echo '<a href="' . esc_url(get_edit_post_link($product_id)) . '" target="_blank">' . esc_html__('See Product', 'fundrizer') . '</a>';
				} else {
					echo '<a href="#" class="button create-woocommerce-product-button" data-post-id="' . esc_attr($post_id) . '" data-campaign-title="' . esc_attr($campaign_title) . '">' . esc_html__('Create Product', 'fundrizer') . '</a>';
				}
			}
		}, 10, 2);


		add_action('admin_print_scripts', function () {
			echo '<script type="text/javascript">
				document.addEventListener("DOMContentLoaded", function() {
					var createButtons = document.querySelectorAll(".create-woocommerce-product-button");

					createButtons.forEach(function(button) {
						button.addEventListener("click", function(event) {
							event.preventDefault();

							var postId = this.getAttribute("data-post-id");
							var campaignTitle = this.getAttribute("data-campaign-title");
							this.textContent = "Processing...";

							var xhr = new XMLHttpRequest();
							var url = "' . esc_url(admin_url('admin-ajax.php')) . '";
							var params = "action=frzr_create_product&post_id=" + postId + "&campaign_title=" + campaignTitle + "&nonce=" + "' . esc_js(wp_create_nonce('frzr-create-woo-product')) . '";

							xhr.open("POST", url);
							xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

							xhr.onload = function() {
								if (xhr.status === 200) {
									alert("WooCommerce product created successfully!");
									window.location.href = window.location.href;
								} else {
									console.error("Error creating WooCommerce product:", xhr.statusText);
									window.location.href = window.location.href;
								}
							};

							xhr.onerror = function() {
								console.error("Request failed");
							};

							xhr.send(params);
						});
					});
				});
			</script>';
		});

		add_action('wp_ajax_frzr_create_product', function () {
			if (!isset($_POST['nonce'])) {
				wp_send_json_error('Nonce value not found.');
			}

			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'frzr-create-woo-product')) {
				wp_send_json_error('Invalid nonce value.');
			}

			if (isset($_POST['post_id']) && isset($_POST['campaign_title'])) {
				$post_id = intval($_POST['post_id']);
				$campaign_title = sanitize_text_field($_POST['campaign_title']);

				$existing_product_query = new \WP_Query(array(
					'post_type' => 'product',
					'post_status' => 'any',
					'posts_per_page' => 1,
					'title' => $campaign_title
				));

				if ($existing_product_query->have_posts()) {
					wp_send_json_error('A product with the same name already exists.');
				} else {
					$product_data = array(
						'post_title' => $campaign_title,
						'post_type' => 'product',
						'post_status' => 'publish'
					);
					$product_id = wp_insert_post($product_data);
					update_post_meta($product_id, 'frzr_bind_campaign_id', $post_id);
					update_post_meta($post_id, 'frzr_bind_product_id', $product_id);
					wp_set_post_terms($product_id, 'fundraising', 'product_type');
					wp_send_json_success('WooCommerce product created successfully!');
				}
				wp_reset_postdata();
			} else {
				wp_send_json_error('Invalid data received.');
			}
		});
	}
}
