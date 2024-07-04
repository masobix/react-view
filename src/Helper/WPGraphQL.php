<?php

namespace FRZR\Helper;

class WPGraphQL
{
	public static function addTypeJSON()
	{
		add_action('graphql_register_types', function () {
			register_graphql_scalar('JSON', [
				'description' => __('JSON according to the JSON spec', 'wp-graphql'),
				'serialize' => function ($value) {
					// Serialize the internal value for response
					return $value;
				},
				'parseValue' => function ($value) {
					// Parse an externally provided value to use as an input
					$decodedValue = json_decode($value, true);

					if (json_last_error() !== JSON_ERROR_NONE) {
						throw new \Error('Cannot represent following value as JSON: ' . esc_html(\GraphQLUtilsUtils::printSafeJson($value)));
					}

					return $decodedValue;
				},
				'parseLiteral' => function ($valueNode, array $variables = null) {
					// Parse an externally provided literal value to use as an input
					if (!$valueNode instanceof \GraphQLLanguageASTStringValueNode) {
						throw new \Error('Query error: Can only parse strings got: ' . esc_html($valueNode->kind), esc_html([$valueNode]));
					}

					$decodedValue = json_decode($valueNode->value, true);

					if (json_last_error() !== JSON_ERROR_NONE) {
						throw new \Error('Not a valid JSON', esc_html([$valueNode]));
					}

					return $decodedValue;
				},
			]);
		});
	}
}
