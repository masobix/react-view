<?php

namespace FRZR\Modules\Campaign;

use \FRZR\Modules\Campaign\MetaUpdate;

class Module
{
	use \FRZR\SingletonTrait;

	public function __construct()
	{
		add_action('save_post', function ($post_id, $post) {
			if ($post->post_type === 'frzr_campaign') {
				$present = new MetaUpdate();
				$present->set_id($post_id)->refresh();
			}
		}, 10, 2);

		$this->admin_campaign_columns();
		$this->register_gutenberg_pattern();
	}

	public function register_gutenberg_pattern()
	{
		register_block_pattern_category(
			'campaign',
			array('label' => __('Campaign', 'fundrizer'))
		);

		register_block_pattern(
			'fundrizer/campaign-roll',
			array(
				'title'       => __('Campaign Grid', 'fundrizer'),
				'description' => _x('Displaying a grid of campaign cards.',  'Block pattern description', 'fundrizer'),
				'content'     => '<!-- wp:columns {"metadata":{"name":"Campaign Grid"},"align":"wide"} --><div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"blockGap":"0"}}} --><div class="wp-block-column"><!-- wp:query {"queryId":3,"query":{"perPage":10,"pages":0,"offset":0,"postType":"frzr_campaign","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"parents":[]},"layout":{"type":"default"}} --><div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"18px"}},"layout":{"type":"grid","columnCount":3}} --><!-- wp:post-featured-image {"isLink":true,"style":{"spacing":{"padding":{"top":"0","bottom":"0"}},"border":{"radius":"8px"}}} /--><!-- wp:post-title {"level":4,"isLink":true,"style":{"typography":{"fontSize":"1.24em","fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0"},"margin":{"top":"8px","bottom":"4px"}}}} /--><!-- wp:post-excerpt {"showMoreOnNewLine":false,"excerptLength":47,"style":{"elements":{"link":{"color":{"text":"#434343"}}},"color":{"text":"#434343"},"spacing":{"margin":{"top":"0","bottom":"4px"}},"typography":{"fontSize":"0.88rem"}}} /--><!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"margin":{"top":"10px","bottom":"10px"}}}} --><div class="wp-block-columns is-not-stacked-on-mobile" style="margin-top:10px;margin-bottom:10px"><!-- wp:column {"verticalAlignment":"stretch","style":{"spacing":{"blockGap":"0"}}} --><div class="wp-block-column is-vertically-aligned-stretch"><!-- wp:paragraph {"fontSize":"small"} --><p class="has-small-font-size">Goal</p><!-- /wp:paragraph --><!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"echo_goal"}}}}} --><p></p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column {"style":{"spacing":{"blockGap":"0"}}} --><div class="wp-block-column"><!-- wp:paragraph {"align":"right","fontSize":"small"} --><p class="has-text-align-right has-small-font-size">Due date</p><!-- /wp:paragraph --><!-- wp:paragraph {"align":"right","metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"echo_deadline"}}}}} --><p class="has-text-align-right">Today</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --><!-- /wp:post-template --><!-- wp:query-pagination --><!-- wp:query-pagination-previous /--><!-- wp:query-pagination-numbers /--><!-- wp:query-pagination-next /--><!-- /wp:query-pagination --><!-- wp:query-no-results --><!-- wp:paragraph {"placeholder":"Tambahkan teks atau blok yang akan ditampilkan jika tidak ada hasil dari kueri. "} --><p></p><!-- /wp:paragraph --><!-- /wp:query-no-results --></div><!-- /wp:query --></div><!-- /wp:column --></div><!-- /wp:columns -->',
				'categories'  => array('campaign'),
			)
		);

		register_block_pattern(
			'fundrizer/campaign-single',
			array(
				'title'       => __('Campaign Single', 'fundrizer'),
				'description' => _x('Displaying a single of campaign.',  'Block pattern description', 'fundrizer'),
				'content'     => '<!-- wp:columns --><div class="wp-block-columns"><!-- wp:column {"width":"","layout":{"type":"constrained","wideSize":"1216px"}} --><div class="wp-block-column"><!-- wp:spacer {"height":"58px"} --><div style="height:58px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column {"width":"66.66%"} --><div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:post-featured-image /--><!-- wp:post-content {"layout":{"type":"default"}} /--></div><!-- /wp:column --><!-- wp:column {"width":"33.33%"} --><div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:post-title {"level":1,"style":{"spacing":{"padding":{"bottom":"6px","top":"8px"},"margin":{"bottom":"0"}},"typography":{"fontSize":"24px","fontStyle":"normal","fontWeight":"600"}}} /--><!-- wp:post-excerpt {"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"0","bottom":"0"}}}} /--><!-- wp:create-block/campaign-progress /--><!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"margin":{"top":"10px","bottom":"10px"}}}} --><div class="wp-block-columns is-not-stacked-on-mobile" style="margin-top:10px;margin-bottom:10px"><!-- wp:column {"verticalAlignment":"stretch","style":{"spacing":{"blockGap":"0"}}} --><div class="wp-block-column is-vertically-aligned-stretch"><!-- wp:paragraph {"fontSize":"small"} --><p class="has-small-font-size">Goal</p><!-- /wp:paragraph --><!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"echo_goal"}}}}} --><p></p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column {"style":{"spacing":{"blockGap":"0"}}} --><div class="wp-block-column"><!-- wp:paragraph {"align":"right","fontSize":"small"} --><p class="has-text-align-right has-small-font-size">Due date</p><!-- /wp:paragraph --><!-- wp:paragraph {"align":"right","metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"echo_deadline"}}}}} --><p class="has-text-align-right">Today</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:create-block/amount-box /--><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"contrast","width":100,"className":"fundrizer-button","fontSize":"medium"} --><div class="wp-block-button has-custom-width wp-block-button__width-100 has-custom-font-size fundrizer-button has-medium-font-size"><a class="wp-block-button__link has-contrast-background-color has-background wp-element-button">Contribute Now</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:column --></div><!-- /wp:columns -->',
				'categories'  => array('campaign'),
			)
		);
	}


	public function admin_campaign_columns()
	{
		add_filter('manage_campaign-update_posts_columns', function ($columns) {
			$new_columns = array();
			foreach ($columns as $key => $value) {
				$new_columns[$key] = $value;
				if ($key === 'cb') {
					$new_columns['campaign'] = 'Campaign';
				}
			}

			return $new_columns;
		});

		add_action('manage_campaign-update_posts_custom_column', function ($column, $post_id) {
			if ('campaign' === $column) {
				wp_add_inline_style('frzr-posts-column', '.column-campaign { width: 200px; }');
				if ($camapaign_id = get_post_meta($post_id, 'campaign_id', true)) {
					echo esc_html(get_the_title($camapaign_id));
				} else {
					echo esc_html__('Not linked', 'fundrizer');
				}
			}
		}, 10, 2);
	}
}
