<?php
/**
 * Plugin Name:     MRKWP Social Photo Feed For Divi
 * Plugin URI:      https://www.mrkwp.com
 * Description:     Divi module for displaying your Instagram feed
 * Author:          M R K Development Pty Ltd
 * Text Domain:     mrkwp-instagram-feed-divi
 * Domain Path:     /languages
 * Version:         4.0.0
 *
 * @package
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('MRKWP_INSTAGRAM_FEED_MODULE_VERSION', '4.0.0');
define('MRKWP_INSTAGRAM_FEED_MODULE_DIR', __DIR__);
define('MRKWP_INSTAGRAM_FEED_MODULE_URL', plugins_url('/' . basename(__DIR__)));

require_once MRKWP_INSTAGRAM_FEED_MODULE_DIR . '/vendor/autoload.php';

$container = new \MRKWP\InstagramFeed\Container;
$container['plugin_name'] = 'MRKWP Social Photo Feed For Divi';
$container['plugin_version'] = MRKWP_INSTAGRAM_FEED_MODULE_VERSION;
$container['plugin_file'] = __FILE__;
$container['plugin_dir'] = MRKWP_INSTAGRAM_FEED_MODULE_DIR;
$container['plugin_url'] = MRKWP_INSTAGRAM_FEED_MODULE_URL;
$container['plugin_slug'] = 'mrkwp-instagram-feed-divi';

// activation hook.
register_activation_hook(__FILE__, array($container['activation'], 'install'));

$container->run();
