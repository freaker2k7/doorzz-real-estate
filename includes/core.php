<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

require_once(REAL_ESTATE_PLUGIN_DIR . '/includes/apc.php');

/**
 * Class REAL_ESTATE
 * 
 * @since 1.0.0
 */
final class REAL_ESTATE {
	private const CACHE_PERIOD = 300; // Seconds
	private const ROOT_URL = 'https://doorzz.com/';
	private const CDN_URL = 'https://cdn.doorzz.com/';
	private const LINK_TO_HID = 'https://dorz.me/id/';
	private static $items = array();
	
	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_shortcode('listings', array('REAL_ESTATE', 'shortcode'));
	}
	
	/**
	 * Make the menu.
	 *
	 * @since 1.0.0
	 */
	public static function menu() {
		add_options_page('Real-Estate (Options)', 'Doorzz Real Estate', 'manage_options', 'REAL_ESTATE', array('REAL_ESTATE', 'plugin_options'));
	}
	
	/**
	 * Render the menu.
	 *
	 * @since 1.0.0
	 */
	public static function plugin_options() {
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$error = '';
		$success = false;
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['REAL_ESTATE_CARD_TEMPLATE'])) {
			self::_set_template(stripslashes($_POST['REAL_ESTATE_CARD_TEMPLATE']));
			$success = true;
		}
		
		require_once(REAL_ESTATE_PLUGIN_DIR . 'views/admin.php');
	}
	
	/**
	 * Main shortcode.
	 *
	 * @since 1.0.0
	 */
	private static function _set_template($data, $rewrite = true) {
		if ($rewrite) {
			file_put_contents(REAL_ESTATE_PLUGIN_DIR . 'views/card.php', $data);
		}
		apc_store('real_estate_template', $data);
	}
	
	/**
	 * Main shortcode.
	 *
	 * @since 1.0.0
	 */
	private static function _get_template() {
		$data = apc_fetch('real_estate_template');
		
		if ($data) {
			return $data;
		}
		
		$data = file_get_contents(REAL_ESTATE_PLUGIN_DIR . 'views/card.php');
		self::_set_template($data, false);
		
		return $data;
	}
	
	/**
	 * Main shortcode.
	 *
	 * @since 1.0.0
	 */
	public static function shortcode($atts = array(), $content = null, $tag = '') {
		if (isset($atts['hid'])) {
			$params = explode(',', $atts['hid']);
			$key = 'real_estate_output_' . $atts['hid'];
		} else {
			$params = array(
				'lat' => isset($atts['lat']) ? $atts['lat'] : 32, 
				'lng' => isset($atts['lng']) ? $atts['lng'] : 34
			);
			$key = 'real_estate_output_' . $params['lat'] . '_' . $params['lng'];
		}
		
		$out = apc_fetch($key);
		$time = time();
		
		var_dump($out);
		
		if ($out) {
			if ($out->timestamp > $time - self::CACHE_PERIOD) {
				
				echo '<pre>';
				var_dump($out);
				echo '</pre>';
				
				return $out->data;
			}
		}
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, self::ROOT_URL . 'wp/list');
		// curl_setopt($ch,CURLOPT_URL, 'http://localhost:3000/wp/list');
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$items = curl_exec($ch);
		curl_close($ch);
		
		try {
			$items = json_decode($items, true);
		} catch (Exception $e) {
			$items = [];
			error_log($e->getMessage());
		}
		
		$out = '<div style="width: 100%; height: 250px; overflow-x: scroll; overflow-y: hidden;">'.
			'<div style="width: ' . (270 * count($items)) . 'px; height: 100%;">';
		
		if ($items) {
			$template = self::linerize(self::_get_template());
			
			$periods = array(
				'' => 'Month',
				2592000 => 'Month',
				604800 => 'Week',
				86400 => 'Day',
				3600 => 'Hour',
				'2592000' => 'Month',
				'604800' => 'Week',
				'86400' => 'Day',
				'3600' => 'Hour'
			);
			
			foreach ($items as $idx => $item) {
				$item['pics'] = json_decode($item['pics'], true);
				$item['free_text'] = htmlspecialchars(json_decode($item['free_text']), true);
				$item['formatted_location'] = json_decode($item['formatted_location'], true);
				
				if (!count($item['pics'])) {
					$img = self::CDN_URL . 'none';
				} else if (isset($item['pics'][0]['url'])) {
					$img = $item['pics'][0]['url'];
				} else {
					$img = self::CDN_URL . 'uploads/' . $item['pics'][0]['_id'] . '.jpg';
				}
				
				$t = str_replace('{hid}', $item['hid'], $template);
				$t = str_replace('{img}', $img, $t);
				$t = str_replace('{name}', $item['name'], $t);
				$t = str_replace('{price}', $item['param_price'], $t);
				$t = str_replace('{period}', $periods[$item['period']], $t);
				$t = str_replace('{size}', $item['param_size'], $t);
				$t = str_replace('{title}', $item['name'], $t);
				$t = str_replace('{type}', $item['filter_sell'] ? 'sell' : 'rent', $t);
				$t = str_replace('{subtype}', $item['filter_house'] ? 'house' : ($item['filter_apartment'] ? 'apartment' : 'commercial'), $t);
				$t = str_replace('{free_text}', $item['free_text'], $t);
				$t = str_replace('{location}', ($item['formatted_location']['en']['street'] ? $item['formatted_location']['en']['street'] . ', ' : '') . 
					($item['formatted_location']['en']['city'] ? $item['formatted_location']['en']['city'] . ', ' : '') . 
					$item['formatted_location']['en']['country'], $t);
				
				$t = str_replace('{{LINK_TO_HID}}', self::LINK_TO_HID, $t);
				
				$out .= $t;
			}
		}
		
		$out .= '</div></div>';
		
		apc_store($key, array('data' => $out, 'timestamp' => $time));
		
		return $out;
	}
	
	/**
	 * Make the text a big string.
	 *
	 * @since 1.0.0
	 */
	private static function linerize($content) {
		return preg_replace('/\n|\t|\r|(\s\s+)/', ' ', $content);
	}
}
