<?php

namespace FRZR\Modules\Campaign;

use Error;
use JsonException;

class Posttypes
{

	private static $registered = false;

	public static function register()
	{
		if (self::$registered) {
			return;
		}

		// Register campaign post type
		register_post_type(
			'frzr_campaign',
			array(
				'labels' => array(
					'name'          => __('Campaigns', 'fundrizer'),
					'singular_name' => __('Campaign', 'fundrizer'),
					'menu_name'     => __('Campaigns', 'fundrizer'),
					'add_new'       => __('New Campaign', 'fundrizer'),
					'add_new_item'  => __('New Campaign', 'fundrizer'),
					'new_item'      => __('New Campaign', 'fundrizer'),
					'edit_item'     => __('Edit Campaign', 'fundrizer'),
					'view_item'     => __('View Campaign', 'fundrizer'),
					'all_items'     => __('All Campaigns', 'fundrizer'),
				),
				'public' => true,
				'menu_icon' => 'dashicons-megaphone',
				'supports' => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'comments'),
				'has_archive' => true,
				'rewrite' => array('slug' => 'campaign'),
				'show_in_rest' => true,
				'show_in_menu' => false,
				'show_in_admin_bar' => true,
				'template' => array(
					array('core/heading', array(
						'level' => 4,
						'content' => '<em>Overview</em>',
					)),
					array('core/paragraph', array(
						'placeholder' => 'Explain briefly why the cause is important and why supporting it matters.',
					)),

					array('core/heading', array(
						'level' => 4,
						'content' => '<em>The Challenge</em>',
					)),
					array('core/paragraph', array(
						'placeholder' => 'Explain the problem or challenge that the campaign aims to address. Use data or statistics to strengthen your argument and demonstrate the urgency of the issue.',
					)),

					array('core/heading', array(
						'level' => 4,
						'content' => '<em>The Solution</em>',
					)),
					array('core/paragraph', array(
						'placeholder' => 'Explain how the proposed solutions will help to address the identified problem.',
					)),

					array('core/heading', array(
						'level' => 4,
						'content' => '<em>The How</em>',
					)),
					array('core/paragraph', array(
						'placeholder' => 'Explain how you will execute the plan to solve the aforementioned problem.<br>Provide a detailed explanation of how the funds raised will be utilized, including a budget plan outlining the expenditure.',
					)),

					array('core/heading', array(
						'level' => 4,
						'content' => '<em>The Proof</em>',
					)),
					array('core/paragraph', array(
						'placeholder' => 'Explain the steps you have taken to provide an execution track record and increase trust.',
					)),

					array('core/heading', array(
						'level' => 4,
						'content' => '<em>The Impact</em>',
					)),
					array('core/paragraph', array(
						'placeholder' => 'Explain how the success of the campaign will be measured and how its impact will be assessed. Show potential supporters that each of their contributions will make a tangible difference.',
					)),

					array('core/heading', array(
						'level' => 4,
						'content' => '<em>Call for Action</em>',
					)),
					array('core/paragraph', array(
						'placeholder' => 'Explain to the audience how to become a supporter and ask them for contributions.',
					)),

					array(
						'core/buttons',
						array(
							'contentJustification' => 'left',
						),
						array(
							array(
								'core/button',
								array(
									'text'      => 'Become Volunteer',
									'url'       => 'https://fundrizer.com',
									'className' => 'is-style-fill',
								),
							)
						)
					),
				),
			)
		);


		// Register campaign-update post type
		register_post_type(
			'frzr_campaign_update',
			array(
				'labels' => array(
					'name' => __('Campaign Update', 'fundrizer'),
					'singular_name' => __('Campaign Update', 'fundrizer'),
					'menu_name' => __('Campaigns Update', 'fundrizer'),
				),
				'public' => true,
				'hierarchical' => true,
				'menu_icon' => 'dashicons-megaphone',
				'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
				'has_archive' => true,
				'rewrite' => array('slug' => 'campagin/update', 'with_front' => false),
				'show_in_rest' => true,
				'show_in_menu' => false,
			)
		);

		add_filter('rwmb_meta_boxes', function ($meta_boxes) {
			$meta_boxes[] = [
				'id'         => 'fundraising-metric',
				'title'      => 'Fundraising Metric',
				'post_types' => 'frzr_campaign',
				'fields'     => [
					[
						'id'   => 'goal',
						'name' => __('Goal', 'fundrizer'),
						'type' => 'currency',
					],
					[
						'id'   => 'deadline',
						'name' => __('Deadline', 'fundrizer'),
						'type' => 'datetime',
					]
				],
			];
			return  $meta_boxes;
		});

		self::$registered = true;
	}
}
