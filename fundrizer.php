<?php

/**
 * Plugin Name: Fundrizer
 * Plugin URI: https://fundrizer.com
 * Description: Fundraising with Gutenberg Block Based
 * Author: LokusWP
 * Author URI: https://lokuswp.com
 * Version: 0.3.0
 * Text Domain: fundrizer
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires Plugins: wp-graphql
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('FRZR_VERSION', '0.3.0');
define('FRZR_PATH', plugin_dir_path(__FILE__));
define('FRZR_URI', plugin_dir_url(__FILE__));
define('FRZR_BASENAME', plugin_basename(__FILE__));
define('FRZR_PAYMENT', "woocommerce");
define('FRZR_DEVMODE', file_exists(FRZR_PATH . '/.dev'));

// PSR4 Load
require_once __DIR__ . '/vendor/autoload.php';
FRZR\Init::init();

// Integration with Appsero
if (!class_exists('Appsero\Client')) {
	require_once __DIR__ . '/appsero/src/Client.php';
}
$client = new \Appsero\Client('5fa1cca7-a8f9-47d7-9e22-ed02bb530f81', 'Fundrizer', __FILE__);
$client->insights()->init();
