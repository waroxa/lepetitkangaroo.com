<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

if (!class_exists('ThemePunch_Newsletter')) {
	 
	class ThemePunch_Newsletter
	{
		/**
		 * @var string 
		 */
		protected static $remote_url = 'http://newsletter.themepunch.com/';
		/**
		 * @var string 
		 */
		protected static $subscribe = 'subscribe.php';
		/**
		 * @var string 
		 */
		protected static $unsubscribe = 'unsubscribe.php';

		public function __construct()
		{
		}

		/**
		 * @param string $url
		 * @param mixed $data
		 * @return array|false
		 */
		private static function _call_newsletter_server($url, $data)
		{
			global $esg_loadbalancer;
			
			$request = $esg_loadbalancer->call_url($url, $data);
			if (is_wp_error($request)) return false;

			$response = wp_remote_retrieve_body($request);
			if ($response = json_decode($response, true)) {
				return is_array($response) ? $response :  false;
			}

			return false;
		}

		/**
		 * Subscribe to the ThemePunch Newsletter
		 * @since: 1.0.0
		 * 
		 * @param string $email
		 * @return array|false
		 **/
		public static function subscribe($email)
		{
			$data = [
				'email' => urlencode($email),
			];
			return self::_call_newsletter_server(self::$remote_url . self::$subscribe, $data);
		}

		/**
		 * Unsubscribe to the ThemePunch Newsletter
		 * @since: 1.0.0
		 * 
		 * @param string $email
		 * @return array|false
		 **/
		public static function unsubscribe($email)
		{
			$data = [
				'email' => urlencode($email),
			];
			return self::_call_newsletter_server(self::$remote_url . self::$unsubscribe, $data);
		}

	}
}
