<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */
 
if( !defined( 'ABSPATH') ) exit();

class Essential_Grid_LoadBalancer
{

	/**
	 * @var string[] 
	 */
	public $servers = [];
	/**
	 * @var string[] 
	 */
	public $defaults = ['themepunch.tools', 'themepunch-ext-a.tools', 'themepunch-ext-b.tools', 'themepunch-ext-c.tools'];
	 	
	/**
	 * set the server list on construct
	 **/
	public function __construct()
	{
		
		$this->servers = get_option('essgrid_servers', []);
		if(empty($this->servers)){
			$this->servers = $this->defaults;
			shuffle($this->servers);
			update_option('essgrid_servers', $this->servers);
		}
	}

	/**
	 * @return string[]
	 */
	public function get_defaults()
	{
		return $this->defaults;
	}

	/**
	 * get the url depending on the purpose, here with key, you can switch do a different server
	 *
	 * @param string $purpose
	 * @param int $key
	 * @param bool $force_http
	 *
	 * @return string
	 * @throws Exception
	 */
	public function get_url($purpose, $key = 0, $force_http = false)
	{
		$url     = ($force_http) ? 'http://' : 'https://';
		$use_url = (!isset($this->servers[$key])) ? reset($this->servers) : $this->servers[$key];

		switch($purpose){
			case 'updates':
			case 'templates':
			case 'library':
				$url .= $purpose;
				break;
			default:
				throw new Exception(esc_html__('Wrong Url subdomain.', 'essential-grid'));
		}

		$url .= '.';
		
		return $url . $use_url;
	}
	
	/**
	 * refresh the server list to be used, will be done once in a month
	 * 
	 * @param bool $force
	 * @return void
	 **/
	public function refresh_server_list($force = false)
	{
		$last_check = get_option('essgrid_server_refresh');
		if(empty($last_check)) {
			$last_check = time();
			update_option('essgrid_server_refresh', $last_check);
		}
		
		if($force || time() - $last_check > 60 * 60 * 24 * 30){
			$data = [
				'item' => urlencode(ESG_PLUGIN_SLUG),
				'version' => urlencode(ESG_REVISION)
			];
			$url = apply_filters('essgrid_loadbalancer_get_server_list_url', 'https://updates.themepunch.tools/get_server_list.php');
			$request = $this->call_url($url, $data);
			if(!is_wp_error($request)){
				if($response = maybe_unserialize($request['body'])){
					$this->servers = json_decode($response, true);
					update_option('essgrid_servers', $this->servers);
					update_option('essgrid_server_refresh', time());
				}
			}
		}
	}
	
	/**
	 * move the server list, to take the next server as the one currently seems unavailable
	 **/
	public function move_server_list()
	{
		$this->servers[] = array_shift($this->servers);
		update_option('essgrid_servers', $this->servers);
	}
	
	/**
	 * call themepunch URL and retrieve data
	 * 
	 * @param string $url can be full URL or just a file name. In this case url is built using $subdomain param
	 * @param array $data
	 * @param string $subdomain
	 * @param bool $force_http
	 * @return array|WP_Error The response or WP_Error on failure.
	 **/
	public function call_url($url, $data = [], $subdomain = 'updates', $force_http = false)
	{
		global $wp_version;
		
		//add version if not passed
		$data['version'] = (!isset($data['version'])) ? urlencode(ESG_REVISION) : $data['version'];
		
		$done	= false;
		$count	= 0;
		
		do {
			if (!preg_match("/^https?:\/\//i", $url)) {
				// url is filename
				$server = $this->get_url($subdomain, 0, $force_http);
				$url = $server . '/' . ltrim($url, '/');
			} else {
				//full URL 
				if ($force_http) {
					$url = preg_replace("/^https:\/\//i", "http://", $url);
				}
			}
			
			$request = wp_safe_remote_post($url, [
				'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
				'body'		 => $data,
				'timeout'	 => 45
			]);
			
			$response_code = wp_remote_retrieve_response_code($request);
			if($response_code == 200){
				$done = true;
			}else{
				$this->move_server_list();
			}
			
			$count++;
		}while($done == false && $count < 3);
		
		return $request;
	}
}
