<?php
/**
 * Real Estate
 *
 * @package   doorzz-real-estate
 * @author    Evgeny Kolyakov <thenetfreaker@gmail.com>
 * @license   GPLv2 or later
 * @link      https://doorzz.com
 * @copyright 2018 Doorzz, Inc.
 *
 * @wordpress-plugin
 * Plugin Name:       Doorzz Real Estate
 * Plugin URI:        https://doorzz.com
 * Description:       Get real estate worldwide.
 * Version:           1.0
 * Author:            Evgeny Kolyakov
 * Author URI:        https://doorzz.com
 * Text Domain:       real-estate
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/freaker2k7/doorzz-real-estate
 * GitHub Branch:     master
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

define('DOORZZ_REAL_ESTATE_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once(DOORZZ_REAL_ESTATE_PLUGIN_DIR . 'includes/core.php');

add_action('init', array('DOORZZ_REAL_ESTATE', 'init'));

if (is_admin()) {
	add_action('admin_menu', array('DOORZZ_REAL_ESTATE', 'menu'));
	
	register_activation_hook(__FILE__, array('DOORZZ_REAL_ESTATE', 'plugin_activation'));
}