<?php

namespace FRZR\Admin\Setting;

use GraphQL\Type\Definition\ResolveInfo;

class Mutation
{
	public static function register()
	{
		add_action('graphql_register_types', [self::class, 'register_graphql_mutations']);
	}

	public static function register_graphql_mutations()
	{
		register_graphql_mutation('fundrizerSettings', [
			'inputFields' => [
				'general' => ['type' => 'GeneralSettingsInput'],
				'profile' => ['type' => 'ProfileSettingsInput'],
			],
			'outputFields' => [
				'success' => ['type' => 'Boolean'],
			],
			'mutateAndGetPayload' => [self::class, 'resolve'],
		]);

		register_graphql_input_type('GeneralSettingsInput', [
			'fields' => [
				'currency' => ['type' => 'String'],
				'country' => ['type' => 'String'],
			],
		]);

		register_graphql_input_type('ProfileSettingsInput', [
			'fields' => [
				'organization' => ['type' => 'String'],
				'description' => ['type' => 'String'],
				'email' => ['type' => 'String'],
				'whatsapp' => ['type' => 'String'],
			],
		]);
	}

	public static function resolve($input, $context, $info)
	{
		if (isset($input['general'])) {
			update_option('frzr_currency', sanitize_text_field($input['general']['currency']));
			update_option('frzr_country', sanitize_text_field($input['general']['country']));
		}

		if (isset($input['profile'])) {
			update_option('frzr_organization', sanitize_text_field($input['profile']['organization']));
			update_option('frzr_description', sanitize_textarea_field($input['profile']['description']));
			update_option('frzr_email', sanitize_email($input['profile']['email']));
			update_option('frzr_whatsapp', sanitize_text_field($input['profile']['whatsapp']));
		}

		return ['success' => true];
	}
}
