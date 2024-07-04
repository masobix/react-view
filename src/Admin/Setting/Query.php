<?php

namespace FRZR\Admin\Setting;

use GraphQL\Type\Definition\ResolveInfo;

class Query
{
	public static function register()
	{
		add_action('graphql_register_types', [self::class, 'register_graphql_types']);
	}

	public static function register_graphql_types()
	{
		// Register SettingsType
		register_graphql_object_type('SettingsType', [
			'fields' => [
				'general' => ['type' => 'SettingsGeneralType'],
				'profile' => ['type' => 'SettingsProfileType'],
			],
		]);

		// Register SettingsGeneralType
		register_graphql_object_type('SettingsGeneralType', [
			'fields' => [
				'currency' => ['type' => 'String'],
				'country' => ['type' => 'String'],
			],
		]);

		// Register SettingsProfileType
		register_graphql_object_type('SettingsProfileType', [
			'fields' => [
				'organization' => ['type' => 'String'],
				'description' => ['type' => 'String'],
				'email' => ['type' => 'String'],
				'whatsapp' => ['type' => 'String'],
			],
		]);

		// Register RootQuery
		register_graphql_field('RootQuery', 'fundrizerSettings', [
			'type' => 'SettingsType',
			'description' => 'Get settings',
			'resolve' => [self::class, 'resolveSettings'],
		]);
	}

	public static function resolveSettings($root, $args)
	{
		// Fetch general settings
		$generalSettings = [
			'currency' => get_option('frzr_currency'),
			'country' => get_option('frzr_country'),
		];

		// Fetch profile settings
		$profileSettings = [
			'organization' => get_option('frzr_organization'),
			'description' => get_option('frzr_description'),
			'email' => get_option('frzr_email'),
			'whatsapp' => get_option('frzr_whatsapp'),
		];

		// Return settings data
		return [
			'general' => $generalSettings,
			'profile' => $profileSettings,
		];
	}
}
