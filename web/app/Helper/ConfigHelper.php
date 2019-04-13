<?php

namespace App\Helpers;

use App\Repositories\ConfigRepository;
use Illuminate\Support\Facades\Cache;

class ConfigHelper
{
	private $config;

	public function __construct() {
		$this->config = new ConfigRepository();
	}

	public function saveList($list) {
		$this->config->updateConfig($list);
		$this->refreshCache();
	}

	public function refreshCache() {
		Cache::forget('settings');
		Cache::remember('settings', 24*60, function() {
			$settings = $this->config->getConfig()->toArray();
			for ($i = 0; $i < count($settings); $i++) {
				$settings[$i]['value'] = isset($settings[$i]['numeric_value']) ? floatval($settings[$i]['numeric_value']) : $settings[$i]['text_value'];
			}
			return array_pluck($settings, 'value', 'name');
		});
	}

	private static function getSettings() {
		$settings = Cache::get('settings');
		if (!$settings) {
			$configHelper = new ConfigHelper();
			$configHelper->refreshCache();
			$settings = Cache::get('settings');
		}
		return $settings;
	}

	public static function bulkAdd($list) {
		$configHelper = new ConfigHelper();
		$configHelper->saveList($list);
	}

	/**
	 * @param $key
	 * @param null $default
	 * @return array|float|string|null
	 */
	public static function get($key, $default = null) {
		$settings = ConfigHelper::getSettings();
		return (is_array($key)) ? array_only($settings, $key) : (isset($settings[$key]) ? $settings[$key] : $default);
	}

	public static function clear() {
		Cache::forget('settings');
	}
}