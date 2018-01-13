<?php
/**
 * Real Estate
 *
 * @package   doorzz-real-estate
 * @author    Evgeny Kolyakov <thenetfreaker@gmail.com>
 * @license   GPL-3.0+
 * @link      https://doorzz.com
 * @copyright 2018 Evgeny Kolyakov
 *
 * @wordpress-plugin
 * Plugin Name:       Doorzz Real Estate
 * Plugin URI:        https://doorzz.com
 * Description:       Get real estate worldwide.
 * Version:           1.0.0
 * Author:            Evgeny Kolyakov
 * Author URI:        https://doorzz.com
 * Text Domain:       real-estate
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl.html
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/freaker2k7/doorzz-real-estate
 * GitHub Branch:     master
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

define('REAL_ESTATE_PLUGIN_FILE', __FILE__);
define('REAL_ESTATE_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once(REAL_ESTATE_PLUGIN_DIR . 'includes/core.php');

add_action('init', array('REAL_ESTATE', 'init'));

if (is_admin()) {
	add_action('admin_menu', array('REAL_ESTATE', 'menu'));
}