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
	private static $SLAG = 'doorzz-real-estate';
	private static $DBSLAG = 'doorzz_real_estate';
	private static $CACHE_PERIOD = 300; // Seconds
	private static $ROOT_URL = 'https://doorzz.com/';
	private static $CDN_URL = 'https://cdn.doorzz.com/';
	private static $LINK_TO_HID = 'https://dorz.me/id/';
	
	private static $DOORZZ_REAL_ESTATE_DEFAULT_TEMPLATE = 
'<a style="display: inline-block; width: 250px; height: 100%; vertical-align: top; margin: 0 10px; text-decoration: none;" href="{{LINK_TO_HID}}" target="_blank">
	<h2 style="font-size: 1rem;">{title}</h2>
	<div style="width: 100%; height: 100px; overflow: hidden; background: #efefef; position: relative;">
		<img src="{img}" alt="{free_text}" title="{free_text}" style="width: 100%; position: absolute; margin: auto; top: 0; left: 0; right: 0; bottom: 0;" />
	</div>
	<h2 style="text-transform: capitalize; font-size: 1rem; padding-top: 0.5rem; margin-bottom: 0;">
		<i class="local-icons {type} {subtype}"></i> {subtype} for {type}<br>
		${price}/{period}<br>
		{location}
	</h4>
</a>';
	
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
		add_options_page('Real-Estate (Options)', 'Doorzz Real Estate', 'manage_options', self::$SLAG, array('DOORZZ_REAL_ESTATE', 'plugin_options'));
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
	 * Activation - Create table & populate it with default data.
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
		if (dbDelta($sql, true)) {
			self::_set_template();
		}
	}
	
	
	/**
	 * Main shortcode.
	 *
	 * @since 1.0.0
	 */
	public static function shortcode($atts = array(), $content = null, $tag = '') {
		$key = self::$DBSLAG . '_output';
		$params = array();
		
		self::_populate_params($atts, $key, $params);
		
		$out = wp_cache_get($key);
		
		if ($out) {
			return $out->data;
		}
		
		try {
			$items = json_decode(self::_get_items($params), true);
		} catch (Exception $e) {
			$items = [];
			error_log($e->getMessage());
		}
		
		if (!$items || array_key_exists('error', $items) && $items['error']) {
			return '';
		}
		
		$out = self::_render($items, isset($params['lang']) ? $params['lang'] : 'en');
		
		wp_cache_set($key, $out, null, 300);
		
		return $out;
	}
	
	
	/**
	 * Get the DB table name.
	 *
	 * @since 1.0.0
	 */
	private static function _get_table_name(&$wpdb = null) {
		return $wpdb->prefix . self::$DBSLAG;
	}
	
	
	/**
	 * Set a single card's template.
	 *
	 * @since 1.0.0
	 */
	private static function _set_template($data = null, $name = 'default') {
		global $wpdb;
		
		if (!$data) {
			$data = self::$DOORZZ_REAL_ESTATE_DEFAULT_TEMPLATE;
		}
		
		$wpdb->replace( 
			self::_get_table_name($wpdb),
			array('name' => 'default', 'time' => current_time( 'mysql' ), 'data' => $data),
			array('%s', '%s', '%s')
		);
		
		wp_cache_set('real_estate_template' . $name, $data);
	}
	
	
	/**
	 * Get a single card's template.
	 *
	 * @since 1.0.0
	 */
	private static function _get_template($use_cache = false, $name = 'default') {
		$key = 'real_estate_template' . $name;
		
		if ($use_cache) {
			$data = wp_cache_get($key);
			
			if ($data) {
				return $data;
			}
		}
		
		global $wpdb;
		
		$template = $wpdb->get_row("SELECT data FROM " . self::_get_table_name($wpdb) . " WHERE name = 'default'", ARRAY_A);
		
		wp_cache_set($key, $template['data']);
		
		return $template['data'];
	}
	
	
	/**
	 * Creates the cache $key according to the arguments that are parsed and populated into $params.
	 *
	 * @since 1.0.0
	 */
	private static function _populate_params($atts, &$key=null, &$params=null) {
		if (isset($atts['lang'])) {
			$params['lang'] = $atts['lang'];
			$key .= '_' . $atts['lang'];
		} else {
			$key .= '_auto';
		}
		
		if (isset($atts['zoom'])) {
			$params['zoom'] = $atts['zoom'];
			$key .= '_' . intval($atts['zoom']);
		} else {
			$key .= '_15';
		}
		
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
			$key = self::$DBSLAG . '_output_' . $params['lat'] . '_' . $params['lng'];
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
			WP_DEBUG ? 'http://localhost:3000/wp/list' : self::$ROOT_URL . 'wp/list',
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
	private static function _render($items, $lang = 'en') {
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
					if (is_array($item)) {
						try {
							if ($item['pics']) {
								$item['pics'] = json_decode($item['pics'], true);
							} else {
								$item['pics'] = array();
							}
						} catch (Exception $e) {
							$item['pics'] = array();
						}
						
						try {
							if ($item['formatted_location']) {
								$item['formatted_location'] = json_decode($item['formatted_location'], true);
							} else {
								$item['formatted_location'] = array();
							}
						} catch (Exception $e) {
							$item['formatted_location'] = array();
						}
						
						$formatted_location = array();
						if (array_key_exists($lang, $item['formatted_location'])) {
							$formatted_location = $item['formatted_location'][$lang];
						} elseif ($lang !== 'en' && array_key_exists('en', $item['formatted_location'])) {
							$formatted_location = $item['formatted_location']['en'];
						}
						
						try {
							if ($item['free_text']) {
								$item['free_text'] = json_decode($item['free_text'], true);
							} else {
								$item['free_text'] = 'Doorzz.com';
							}
						} catch (Exception $e) {
							if (empty($item['free_text'])) {
								$item['free_text'] = 'Doorzz.com';
							}
						}
						
						if (!count($item['pics'])) {
							$img = self::$CDN_URL . 'none';
						} else if (isset($item['pics'][0]['url'])) {
							$img = $item['pics'][0]['url'];
						} else {
							$img = self::$CDN_URL . 'uploads/' . $item['pics'][0]['_id'] . '.jpg';
						}
						
						$t = str_replace('{{LINK_TO_HID}}', esc_url(self::$LINK_TO_HID . self::_xss_cleanup($item['hid'])), $template);
						$t = str_replace('{img}', esc_url(self::_xss_cleanup($img)), $t);
						$t = str_replace('{name}', self::_xss_cleanup($item['name']), $t);
						$t = str_replace('{price}', self::_xss_cleanup($item['param_price']), $t);
						$t = str_replace('{period}', self::$PERIODS[$item['period']], $t);
						$t = str_replace('{size}', self::_xss_cleanup($item['param_size']), $t);
						$t = str_replace('{title}', self::_xss_cleanup($item['name']), $t);
						$t = str_replace('{type}', $item['filter_sell'] ? 'sale' : 'rent', $t);
						$t = str_replace('{subtype}', $item['filter_house'] ? 'house' : ($item['filter_apartment'] ? 'apartment' : 'commercial'), $t);
						
						$t = str_replace('{location}', self::_xss_cleanup(
							(!empty($formatted_location['street']) ? $formatted_location['street'] . ', ' : '') . 
							(!empty($formatted_location['city']) ? $formatted_location['city'] . ', ' : '') . 
							$formatted_location['country']
						), $t);
						
						// This might add the most text, so let's add it last.
						$t = str_replace('{free_text}', esc_html(self::_xss_cleanup($item['free_text'])), $t);
						
						$out[] = $t;
					}
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
	
	
	/**
	 * Sanitize data.
	 *
	 * @since 1.0.0
	 */
	private static function _xss_cleanup($content) {
		return filter_var($content, FILTER_SANITIZE_STRING);
	}
}
