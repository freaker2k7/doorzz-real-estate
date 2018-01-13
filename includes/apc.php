<?php

define('APC_CACHE_FILE', '.apc.cache');

if (!function_exists('apc_fetch')) {
	function apc_fetch($key = '', $full = false) {
		$cache = @file_get_contents(APC_CACHE_FILE);
		
		if (empty($cache)) {
			if ($full) {
				return new stdClass();
			}
			
			return null;
		}
		
		$cache = json_decode($cache);
		
		if ($full) {
			return $cache;
		}
		
		if (isset($cache->$key)) {
			return json_decode($cache->$key);
		}
		
		return null;
	}
}

if (!function_exists('apc_store')) {
	function apc_store($key = '', $val = null) {
		$cache = apc_fetch(null, true);
		$cache->$key = json_encode($val);
		file_put_contents(APC_CACHE_FILE, json_encode($cache));
	}
}