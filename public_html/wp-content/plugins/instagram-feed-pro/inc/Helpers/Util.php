<?php

namespace InstagramFeed\Helpers;

use DateTime;
use InstagramFeed\Vendor\Brumann\Polyfill\Unserialize;

/**
 * Utilities class
 *
 * @since 6.0.5
 */
class Util
{
	/**
	 * Returns the enabled debugging flag state.
	 *
	 * @return bool
	 */
	public static function isDebugging()
	{
		return (defined('SBI_DEBUG') && SBI_DEBUG === true) || isset($_GET['sbi_debug']) || isset($_GET['sb_debug']);
	}

	/**
	 * Check if the current page is an Instagram Feed page.
	 *
	 * This method determines whether the current page being viewed is an Instagram Feed page.
	 *
	 * @return bool Returns true if the current page is an Instagram Feed page, false otherwise.
	 */
	public static function isIFPage()
	{
		return get_current_screen() !== null && !empty($_GET['page']) && strpos($_GET['page'], 'sbi-') !== false;
	}

	/**
	 * Get other active plugins of Smash Balloon
	 *
	 * @since 6.2.0
	 */
	public static function get_sb_active_plugins_info()
	{
		// Get the WordPress's core list of installed plugins.
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$installed_plugins = get_plugins();

		$plugins = array(
			'instagram' => array(
				'free' => 'instagram-feed/instagram-feed.php',
				'pro' => 'instagram-feed-pro/instagram-feed.php',
			),
			'facebook' => array(
				'free' => 'custom-facebook-feed/custom-facebook-feed.php',
				'pro' => 'custom-facebook-feed-pro/custom-facebook-feed.php',
			),
			'twitter' => array(
				'free' => 'custom-twitter-feeds/custom-twitter-feed.php',
				'pro' => 'custom-twitter-feeds-pro/custom-twitter-feed.php',
			),
			'youtube' => array(
				'free' => 'feeds-for-youtube/feeds-for-youtube.php',
				'pro' => 'youtube-feed-pro/youtube-feed-pro.php',
			),
			'tiktok' => array(
				'free' => 'feeds-for-tiktok/feeds-for-tiktok.php',
				'pro' => 'tiktok-feeds-pro/tiktok-feeds-pro.php',
			),
			'reviews' => array(
				'free' => 'reviews-feed/sb-reviews.php',
				'pro' => 'reviews-feed-pro/sb-reviews-pro.php',
			),
			'social_wall' => array(
				'free' => 'social-wall/social-wall.php',
			),
			'feed_analytics' => array(
				'free' => 'sb-analytics/sb-analytics-pro.php',
			),
			'clicksocial' => array(
				'free' => 'click-social/click-social.php'
			)
		);

		$active_plugins_info = array();

		foreach ($plugins as $key => $plugin_files) {
			$active_plugins_info[$key . '_plugin'] = $plugin_files['free'];
			$active_plugins_info['is_' . $key . '_installed'] = false;

			if (isset($plugin_files['pro']) && isset($installed_plugins[$plugin_files['pro']])) {
				$active_plugins_info[$key . '_plugin'] = $plugin_files['pro'];
				$active_plugins_info['is_' . $key . '_installed'] = true;
			} elseif (isset($installed_plugins[$plugin_files['free']])) {
				$active_plugins_info['is_' . $key . '_installed'] = true;
			}
		}

		$active_plugins_info['clicksocial_path'] = 'https://downloads.wordpress.org/plugin/click-social.zip';
		$active_plugins_info['installed_plugins'] = $installed_plugins;

		return $active_plugins_info;
	}

	/**
	 * Checks if sb_instagram_posts_manager errors and license errors exists
	 *
	 * @return bool
	 */
	public static function sbi_has_admin_errors()
	{
		global $sb_instagram_posts_manager;
		$are_critical_errors = $sb_instagram_posts_manager->are_critical_errors();

		if ($are_critical_errors) {
			return true;
		}

		$errors = $sb_instagram_posts_manager->get_errors();
		if (!empty($errors)) {
			foreach ($errors as $type => $error) {
				if (
					in_array($type, array( 'database_create', 'upload_dir', 'unused_feed', 'platform_data_deleted', 'database_error' ))
					&& ! empty($error)
				) {
					return true;
				}
			}
		}

		// Check if license error exists.
		return self::sbi_has_license_error();
	}

	/**
	 * Checks if license error exists
	 *
	 * @return bool
	 */
	public static function sbi_has_license_error()
	{
		$sbi_license_key = sbi_builder_pro()->license_service->get_license_key;
		if (empty($sbi_license_key) || !isset($sbi_license_key)) {
			return true;
		}
		$sbi_license_expired = sbi_builder_pro()->license_service->is_license_expired;
		$grace_period_ended = sbi_builder_pro()->license_service->is_license_grace_period_ended;
		if ($sbi_license_expired && !$grace_period_ended) {
			return true;
		}

		return false;
	}

	/** Returns the script debug state.
	 *
	 * @return bool
	 */
	public static function is_script_debug()
	{
		return defined('SCRIPT_DEBUG') && SCRIPT_DEBUG === true;
	}

	/**
	 * Get a valid timestamp to avoid Year 2038 problem.
	 *
	 * @param mixed $timestamp Timestamp to check.
	 * @return int
	 */
	public static function get_valid_timestamp($timestamp)
	{
		// check if timestamp is negative and set to maximum value.
		if ($timestamp < 0) {
			$timestamp = 2147483647;
		}

		if (is_numeric($timestamp)) {
			return (int)$timestamp;
		}

		$new_timestamp = new DateTime($timestamp);
		$year = $new_timestamp->format('Y');
		if ((int)$year >= 2038) {
			$new_timestamp->setDate(2037, 12, 30)->setTime(00, 00, 00);
			$timestamp = $new_timestamp->getTimestamp();
		} else {
			$timestamp = strtotime($timestamp);
		}

		return $timestamp;
	}

	/**
	 * Checks if the user has custom templates, CSS or JS added and if they have dismissed the notice
	 *
	 * @return bool
	 * @since 6.3
	 */
	public static function sbi_show_legacy_css_settings()
	{
		$show_legacy_css_settings = false;
		$sbi_statuses = get_option('sbi_statuses', array());

		if (
			(isset($sbi_statuses['custom_templates_notice'])
				&& self::sbi_has_custom_templates())
			|| self::sbi_legacy_css_enabled()
		) {
			$show_legacy_css_settings = true;
		}

		return apply_filters('sbi_show_legacy_css_settings', $show_legacy_css_settings);
	}

	/**
	 * Checks if the user has custom templates, CSS or JS added
	 *
	 * @return bool
	 * @since 6.3
	 */
	public static function sbi_has_custom_templates()
	{
		// Check if the user has sbi custom templates in their theme.
		$templates = array(
			'feed.php',
			'footer.php',
			'header-boxed.php',
			'header-generic.php',
			'header-text.php',
			'header.php',
			'item.php',
		);
		foreach ($templates as $template) {
			if (locate_template('sbi/' . $template)) {
				return true;
			}
		}

		// Check if the user has custom CSS and or JS added in the settings.
		$settings = get_option('sb_instagram_settings', array());
		if (!empty($settings['sb_instagram_custom_css'])) {
			return true;
		}
		if (!empty($settings['sb_instagram_custom_js'])) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if the user has legacy CSS enabled
	 *
	 * @return bool
	 * @since 6.3
	 */
	public static function sbi_legacy_css_enabled()
	{
		$legacy_css_enabled = false;
		$settings = get_option('sb_instagram_settings', array());
		if (
			isset($settings['enqueue_legacy_css'])
			&& $settings['enqueue_legacy_css']
		) {
			$legacy_css_enabled = true;
		}

		return apply_filters('sbi_legacy_css_enabled', $legacy_css_enabled);
	}

	/**
	 * Safely unserialize data
	 *
	 * @param $data
	 * @return mixed
	 */
	public static function safe_unserialize($data)
	{
		if (!is_string($data)) {
			return $data;
		}

		$data = Unserialize::unserialize($data, ['allowed_classes' => false]);
		return $data;
	}
}
