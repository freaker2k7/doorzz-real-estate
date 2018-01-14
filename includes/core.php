<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Class DOORZZ_REAL_ESTATE
 * 
 * @since 1.0.0
 */
final class DOORZZ_REAL_ESTATE {
	private const SLAG = 'doorzz-real-estate';
	private const DBSLAG = 'doorzz_real_estate';
	private const CACHE_PERIOD = 300; // Seconds
	private const ROOT_URL = 'https://doorzz.com/';
	private const CDN_URL = 'https://cdn.doorzz.com/';
	private const LINK_TO_HID = 'https://dorz.me/id/';
	
	private static $PERIODS = array(
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
	
	
	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_shortcode('listings', array('DOORZZ_REAL_ESTATE', 'shortcode'));
	}
	
	
	/**
	 * Make the menu.
	 *
	 * @since 1.0.0
	 */
	public static function menu() {
		add_options_page('Real-Estate (Options)', 'Doorzz Real Estate', 'manage_options', self::SLAG, array('DOORZZ_REAL_ESTATE', 'plugin_options'));
	}
	
	
	/**
	 * Register the menu.
	 *
	 * @since 1.0.0
	 */
	public static function plugin_options() {
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['DOORZZ_REAL_ESTATE_CARD_TEMPLATE'])) {
			self::_set_template(stripslashes_deep($_POST['DOORZZ_REAL_ESTATE_CARD_TEMPLATE']));
			$success = true;
		}
		
		require_once(DOORZZ_REAL_ESTATE_PLUGIN_DIR . 'views/admin.php');
	}
	
	
	/**
	 * Activation.
	 *
	 * @since 1.0.0
	 */
	public static function plugin_activation() {
		global $wpdb;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE " . self::_get_table_name($wpdb) . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			name varchar(128) NOT NULL,
			data text NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY title (name)
		) $charset_collate;";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		self::_set_template();
	}
	
	
	/**
	 * Main shortcode.
	 *
	 * @since 1.0.0
	 */
	public static function shortcode($atts = array(), $content = null, $tag = '') {
		$key = 'real_estate_output';
		$params = array();
		
		self::_populate_params($atts, $key, $params);
		
		$out = wp_cache_get($key);
		
		// if ($out) {
		// 	return $out->data;
		// }
		
		try {
			$items = json_decode(self::_get_items($params), true);
		} catch (Exception $e) {
			$items = [];
			error_log($e->getMessage());
			return '';
		}
		
		$out = self::_render($items);
		
		// wp_cache_set($key, $out, null, 300);
		
		return $out;
	}
	
	
	/**
	 * Get the DB table name.
	 *
	 * @since 1.0.0
	 */
	private static function _get_table_name(&$wpdb = null) {
		return $wpdb->prefix . self::DBSLAG;
	}
	
	
	/**
	 * Set a single card's template.
	 *
	 * @since 1.0.0
	 */
	private static function _set_template($data = null, $rewrite = true) {
		global $wpdb;
		
		if (!$data) {
			require_once(DOORZZ_REAL_ESTATE_PLUGIN_DIR . 'views/cards/default.php');
			global $DOORZZ_REAL_ESTATE_DEFAULT_TEMPLATE;
			
			$data = $DOORZZ_REAL_ESTATE_DEFAULT_TEMPLATE;
		}
		
		$wpdb->replace( 
			self::_get_table_name($wpdb), 
			array( 
				'name' => 'default', 
				'time' => current_time( 'mysql' ), 
				'data' => $data
			), 
			array( 
				'%s',
				'%s', 
				'%s' 
			) 
		);
		
		// wp_cache_set('real_estate_template', $data);
	}
	
	
	/**
	 * Get a single card's template.
	 *
	 * @since 1.0.0
	 */
	private static function _get_template($use_cache = false, $name = 'default') {
		if ($use_cache) {
			$data = wp_cache_get('real_estate_template');
			
			// if ($data) {
			// 	return $data;
			// }
		}
		
		global $wpdb;
		
		$template = $wpdb->get_row( "SELECT data FROM " . self::_get_table_name($wpdb) . " WHERE name = 'default'", ARRAY_A );
		
		// wp_cache_set('real_estate_template', $template['data']);
		
		return $template['data'];
	}
	
	
	/**
	 * Creates the cache $key according to the arguments that are parsed and populated into $params.
	 *
	 * @since 1.0.0
	 */
	private static function _populate_params($atts, &$key=null, &$params=null) {
		if (isset($atts['hid'])) {
			$params['hid'] = explode(',', $atts['hid']);
			$key .= '_' . $atts['hid'];
		}
		
		if (isset($atts['uid'])) {
			$params['uid'] = $atts['uid'];
			$key .= '_' . $atts['uid'];
		}
		
		if (isset($atts['qid'])) {
			$params['qid'] = $atts['qid'];
			$key .= '_' . $atts['qid'];
		}
		
		if (isset($atts['params'])) {
			$params['params'] = explode(',', $atts['params']);
			foreach ($params['params'] as $key => &$value) {
				$value = explode('=', $value);
				$value = array($value[0] => array('min' => $value[1], 'max' => $value[1]));
			}
			$key .= '_' . $atts['params'];
		}
		
		if (isset($atts['filters'])) {
			$params['filters'] = explode(',', $atts['filters']);
			foreach ($params['filters'] as $key => &$value) {
				$value = explode('=', $value);
				$value = array($value[0] => intval($value[1] != 0 && $value[1] !== 'false'));
			}
			$key .= '_' . $atts['filters'];
		}
		
		if (empty($params)) {
			$params = array(
				'lat' => isset($atts['lat']) ? $atts['lat'] : 32, 
				'lng' => isset($atts['lng']) ? $atts['lng'] : 34
			);
			$key = 'real_estate_output_' . $params['lat'] . '_' . $params['lng'];
		}
		
		if (isset($atts['limit'])) {
			$params['limit'] = intval($atts['limit']);
			$key .= '_' . $params['limit'];
		}
	}
	
	
	/**
	 * Gets items from resource.
	 *
	 * @since 1.0.0
	 */
	private static function _get_items($params = []) {
		$response = wp_remote_post(
			'http://localhost:3000/wp/list',
			// self::ROOT_URL . 'wp/list',
			 array(
			 	'method'		=> 'POST',
				'headers'		=> array('Content-Type' => 'application/json'),
				'timeout'		=> 3,
				'redirection'	=> 3,
				'httpversion'	=> '2.0',
				'body'			=> json_encode($params)
			)
		);
		
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			return '[]'; // The result will be json_decode(d)
		}
		
		return wp_remote_retrieve_body($response);
	}
	
	
	/**
	 * Render the slideshow.
	 *
	 * @since 1.0.0
	 */
	private static function _render($items) {
		$out = array(
			'<div style="width: 100%; height: 250px; overflow-x: scroll; overflow-y: hidden;">',
			'<div style="width: ' . (270 * count($items)) . 'px; height: 100%;">'
		);
		// 270 = 10 + 250 + 10 (margin of 10px left&right of every card)
		
		if ($items) {
			$template = self::_get_template(true);
			if ($template) {
				$template = self::_linerize($template);
				
				foreach ($items as $idx => $item) {
					$item['pics'] = json_decode($item['pics'], true);
					$item['free_text'] = esc_html(json_decode($item['free_text']), true);
					$item['formatted_location'] = json_decode($item['formatted_location'], true);
					
					if (!count($item['pics'])) {
						$img = self::CDN_URL . 'none';
					} else if (isset($item['pics'][0]['url'])) {
						$img = $item['pics'][0]['url'];
					} else {
						$img = self::CDN_URL . 'uploads/' . $item['pics'][0]['_id'] . '.jpg';
					}
					
					$t = str_replace('{{LINK_TO_HID}}', esc_url(self::LINK_TO_HID . $item['hid']), $template);
					$t = str_replace('{img}', esc_url($img), $t);
					$t = str_replace('{name}', $item['name'], $t);
					$t = str_replace('{price}', $item['param_price'], $t);
					$t = str_replace('{period}', self::$PERIODS[$item['period']], $t);
					$t = str_replace('{size}', $item['param_size'], $t);
					$t = str_replace('{title}', $item['name'], $t);
					$t = str_replace('{type}', $item['filter_sell'] ? 'sale' : 'rent', $t);
					$t = str_replace('{subtype}', $item['filter_house'] ? 'house' : ($item['filter_apartment'] ? 'apartment' : 'commercial'), $t);
					
					$t = str_replace('{location}', ($item['formatted_location']['en']['street'] ? $item['formatted_location']['en']['street'] . ', ' : '') . 
						($item['formatted_location']['en']['city'] ? $item['formatted_location']['en']['city'] . ', ' : '') . 
						$item['formatted_location']['en']['country'], $t);
					
					$t = str_replace('{free_text}', $item['free_text'], $t); // This might add the most text, so let's add it last.
					
					$out[] = $t;
				}
			}
		}
		
		$out[] = '</div></div>';
		
		return implode('', $out);
	}
	
	
	/**
	 * Make the text a big string to serve less data.
	 *
	 * @since 1.0.0
	 */
	private static function _linerize($content) {
		return preg_replace('/\n|\t|\r|(\s\s+)/', ' ', $content);
	}
}
