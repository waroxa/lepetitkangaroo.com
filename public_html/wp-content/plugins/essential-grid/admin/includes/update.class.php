<?php
/**
 * Update Class For Essential Grid
 * Enables automatic updates on the Plugin
 *
 * @package Essential_Grid_Admin
 * @author  ThemePunch <info@themepunch.com>
 * @link    https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Update
{

	/**
	 * @var string 
	 */
	private $plugin_url = 'https://www.essential-grid.com/';
	/**
	 * @var string 
	 */
	private $remote_url = 'check_for_updates.php';
	/**
	 * @var string 
	 */
	private $remote_url_info = 'essential-grid/essential-grid.php';
	/**
	 * @var string 
	 */
	private $plugin_path = 'essential-grid/essential-grid.php';
	/**
	 * @var string 
	 */
	private $version;
	/**
	 * @var string 
	 */
	private $option;
	/**
	 * @var stdClass 
	 */
	private $data;
	/**
	 * @var bool 
	 */
	public $force = false;

	/**
	 * @param string $version
	 */
	public function __construct( string $version)
	{
		$this->option = ESG_PLUGIN_SLUG . '_update_info';
		$this->data = new stdClass;
		$this->_retrieve_version_info();
		$this->version = (empty($version)) ? ESG_REVISION : $version;
	}

	public function add_update_checks()
	{
		if ($this->force) {
			$transient = get_site_transient('update_plugins');
			$esg_t = $this->set_update_transient($transient);
			
			if(!empty($esg_t)){
				set_site_transient('update_plugins', $esg_t);
			}
		}

		add_filter('pre_set_site_transient_update_plugins', [&$this, 'set_update_transient']);
		add_filter('plugins_api', [&$this, 'set_updates_api_results'], 10, 3);
	}

	/**
	 * @param stdClass $transient
	 * @return stdClass
	 */
	public function set_update_transient($transient)
	{
		$this->_check_updates();
		
		if (!is_object($transient)) $transient = new stdClass();
		if (!isset($transient->response)) $transient->response = [];
		if (!isset($transient->no_update)) $transient->no_update = [];

		if (!empty($this->data->basic) && is_object($this->data->basic)) {
			$version = (isset($this->data->basic->version)) ? $this->data->basic->version : $this->data->basic->new_version;
			if (version_compare($this->version, $version, '<')) {
				$this->data->basic->new_version = $version;
				if(isset($this->data->basic->version)){
					unset($this->data->basic->version);
				}
				$transient->response[$this->plugin_path] = $this->data->basic;
			} else {
				$transient->no_update[$this->plugin_path] = $this->data->basic;
			}
		}

		return $transient;
	}

	/**
	 * @param mixed $result
	 * @param string $action
	 * @param stdClass $args
	 * @return mixed
	 */
	public function set_updates_api_results($result, $action, $args)
	{
		$this->_check_updates();

		if (isset($args->slug) && $args->slug == ESG_PLUGIN_SLUG && $action == 'plugin_information') {
			if (!empty($this->data->full) && is_object($this->data->full)) {
				$result = $this->data->full;
			}
		}

		return $result;
	}
	
	protected function _check_updates()
	{
		// Get data
		if (empty((array)$this->data)) {
			$data = get_option($this->option, false);
			$data = $data ? $data : new stdClass;
			$this->data = is_object($data) ? $data : maybe_unserialize($data);
		}

		// If there is no option in the database, boolean `false` is returned
		$last_check = get_option('tp_eg_update-check');
		if (!$last_check) {
			$last_check = time() - 172802;
			update_option('tp_eg_update-check', $last_check);
		}

		// Check for updates
		if (time() - $last_check > 172800 || $this->force) {
			$data = $this->_retrieve_update_info();
			if (isset($data->basic)) {
				update_option('tp_eg_update-check', time());
				$this->data->checked = time();
				$this->data->basic = $data->basic;
				$this->data->full = $data->full;
				Essential_Grid_Base::setLatestVersion( $data->full->version );
			}
		}

		// Save results
		update_option($this->option, $this->data);
	}

	public function _retrieve_update_info()
	{
		global $esg_loadbalancer;
		
		// Build request
		$code = Essential_Grid_Base::getCode();
		$data = [
			'code' => urlencode($code),
			'product' => urlencode(ESG_PLUGIN_SLUG),
			'version' => urlencode(ESG_REVISION)
		];
		$request = $esg_loadbalancer->call_url($this->remote_url_info, $data);

		$data = new stdClass;
		if (!is_wp_error($request) && ($response = maybe_unserialize($request['body'])) && is_object($response)) {
			$data = $response;
			$data->basic->url = $this->plugin_url;
			$data->full->url = $this->plugin_url;
			$data->full->external = 1;
		}
		
		return $data;
	}

	/**
	 * @return bool|string
	 */
	public function _retrieve_version_info()
	{
		global $esg_loadbalancer;

		// If there is no option in the database, boolean `false` is returned
		$last_check = get_option('tp_eg_update-check-short');

		// Check for updates
		if (!$last_check || time() - $last_check > 172800 || $this->force) {
			update_option('tp_eg_update-check-short', time());

			$hash = $this->force ? '' : get_option('tp_eg-update-hash', '');
			$data = [
				'item' => urlencode(ESG_PLUGIN_SLUG),
				'version' => urlencode(ESG_REVISION),
				'hash' => urlencode($hash),
			];
			$request = $esg_loadbalancer->call_url($this->remote_url, $data);
			if (is_wp_error($request)) {
				update_option('essential-connection', false);
				return false;
			} 
			update_option('essential-connection', true);

			$response = wp_remote_retrieve_body($request);
			if ('actual' != $response) {
				$version_info = json_decode($response);

				if (isset($version_info->hash)) update_option('tp_eg-update-hash', $version_info->hash);
				if (isset($version_info->stable)) update_option('essential-stable-version', $version_info->stable);
				if (isset($version_info->notices)) update_option('essential-notices', $version_info->notices);
				if (isset($version_info->dashboard)) update_option('essential-dashboard', $version_info->dashboard);
				if (isset($version_info->version)) Essential_Grid_Base::setLatestVersion( $version_info->version );
				if (isset($version_info->addons)) {
					$esg_addons = Essential_Grid_Addons::instance();
					$esg_addons->update_addons((array)$version_info->addons);
				}
			}
		}

		if($this->force) update_option('tp_eg_update-check', '');

		return Essential_Grid_Base::getLatestVersion();
	}

}
