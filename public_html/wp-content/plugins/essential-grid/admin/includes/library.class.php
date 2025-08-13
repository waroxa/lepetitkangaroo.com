<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Library
{
	/**
	 * option to keep templates list retrieved from TP server
	 */
	CONST OPTION_TEMPLATES = 'tp_eg-templates';
	/**
	 * option to keep new templates counter
	 */
	CONST OPTION_TEMPLATES_COUNTER = 'tp_eg-templates-counter';
	/**
	 * option to keep templates hash
	 */
	CONST OPTION_TEMPLATES_HASH = 'tp_eg-templates-hash';
	/**
	 * option to keep last templates check timestamp
	 */
	CONST OPTION_TEMPLATES_CHECK = 'tp_eg-templates-check';
	/**
	 * shop version
	 */
	const SHOP_VERSION = '1.0.0';

	/**
	 * @var string 
	 */
	private $library_list = 'essential-grid/get-list.php';

	/**
	 * @var string 
	 */
	private $library_dl = 'essential-grid/download.php';

	/**
	 * @var string 
	 */
	private $library_server_path = '/essential-grid/images/';

	/**
	 * @var string 
	 */
	private $library_path = '/essential-grid/templates/';

	/**
	 * @var string 
	 */
	private $library_path_plugin = 'admin/assets/imports/';

	/**
	 * @var bool 
	 */
	private $curl_check = false;

	/**
	 * @param mixed $default
	 * @return false|mixed|void
	 */
	public function get_templates($default = [])
	{
		return get_option(self::OPTION_TEMPLATES, $default);
	}

	/**
	 * @param array $data
	 * @return void
	 */
	public function set_templates($data)
	{
		update_option(self::OPTION_TEMPLATES, $data, false);
	}

	/**
	 * @param mixed $default
	 * @return false|mixed|void
	 */
	public function get_templates_counter($default = 0)
	{
		return get_option(self::OPTION_TEMPLATES_COUNTER, $default);
	}

	/**
	 * @param int $count
	 * @return void
	 */
	public function set_templates_counter($count)
	{
		update_option(self::OPTION_TEMPLATES_COUNTER, $count);
	}

	/**
	 * Download template by UID (also validates if download is legal)
	 * @since: 2.3
	 */
	public function _download_template($uid)
	{
		global $esg_loadbalancer;
		
		$wp_filesystem = Essential_Grid_Base::get_filesystem();

		$return = false;
		$uid = esc_attr($uid);

		$code = Essential_Grid_Base::isValid() ? Essential_Grid_Base::getCode() : '';

		$upload_dir = wp_upload_dir(); // Set upload folder
		// Check folder permission and define file location
		if (wp_mkdir_p($upload_dir['basedir'] . $this->library_path)) {
			//check here to not flood the server

			$data = [
				'code' => urlencode($code),
				'shop_version' => urlencode(self::SHOP_VERSION),
				'version' => urlencode(ESG_REVISION),
				'product' => urlencode(ESG_PLUGIN_SLUG),
				'uid' => urlencode($uid),
			];
			$request = $esg_loadbalancer->call_url($this->library_dl, $data, 'templates');
			
			if (!is_wp_error($request)) {
				$response = wp_remote_retrieve_body($request);
				if ($response !== 'invalid') {
					//add stream as a zip file
					$dir = $upload_dir['basedir'] . $this->library_path;
					$file = $dir . '/' . $uid . '.zip';

					wp_mkdir_p($dir);
					$ret = $wp_filesystem->put_contents($file, $response);
					if ($ret !== false) {
						//return $file so it can be processed. We have now downloaded it into a zip file
						$return = $file;
					} else {
						//else, print that file could not be written
						$return = ['error' => esc_attr__('Can\'t write the file into the uploads folder of WordPress, please change permissions and try again!', 'essential-grid')];
					}
				} else {
					$return = ['error' => esc_attr__('Purchase Code is invalid', 'essential-grid')];
				}
			}
			//else, check for error and print it to customer
		} else {
			$return = ['error' => esc_attr__('Can\'t write into the uploads folder of WordPress, please change permissions and try again!', 'essential-grid')];
		}
		return $return;
	}


	/**
	 * Delete the Template file
	 * 
	 * @since: 2.3
	 * @param string $uid
	 */
	public function _delete_template($uid)
	{
		$uid = sanitize_file_name($uid);

		// Set upload folder
		$upload_dir = wp_upload_dir();

		// Check folder permission and define file location
		if (wp_mkdir_p($upload_dir['basedir'] . $this->library_path)) {
			wp_delete_file($upload_dir['basedir'] . $this->library_path . '/' . $uid . '.zip');
		}
	}

	/**
	 * Get the Templatelist from servers
	 * @since: 5.0.5
	 */
	public function _get_template_list($force = false)
	{
		global $esg_loadbalancer;

		// If there is no option in the database, boolean `false` is returned
		$last_check = get_option(self::OPTION_TEMPLATES_CHECK);

		// Check for updates
		// if no last check date in db or last check was more than 4 days or check forced
		if ($last_check === false || time() - $last_check > 345600 || $force) {
			update_option(self::OPTION_TEMPLATES_CHECK, time());

			$code = Essential_Grid_Base::isValid() ? Essential_Grid_Base::getCode() : '';
			$hash = $force ? '' : get_option(self::OPTION_TEMPLATES_HASH, '');
			$data = [
				'code' => urlencode($code),
				'hash' => urlencode($hash),
				'shop_version' => urlencode(self::SHOP_VERSION),
				'version' => urlencode(ESG_REVISION),
				'product' => urlencode(ESG_PLUGIN_SLUG),
			];
			$request = $esg_loadbalancer->call_url($this->library_list, $data, 'templates');

			if (!is_wp_error($request)) {
				if ($response = maybe_unserialize($request['body'])) {
					if('actual' != $response) {
						$templates = json_decode($response, true);
						if (is_array($templates)) {
							$current = $this->get_templates();
							$current_count = isset($current['grids']) ? count($current['grids']) : 0;
							$new_count = isset($templates['grids']) ? count($templates['grids']) : 0;
							if ($current_count < $new_count) {
								$this->set_templates_counter($new_count - $current_count);
							}

							if (isset($templates['hash'])) update_option(self::OPTION_TEMPLATES_HASH, $templates['hash']);
							$this->set_templates($templates);
							$this->_update_images();
						}
					}
				}
			}
			
			return !is_wp_error($request);
		}
		
		return true;
	}

	/**
	 * Remove the is_new attribute which shows the "update available" button
	 * @since: 2.3
	 */
	public function remove_is_new($uid)
	{
		$cur = $this->get_templates();
		if (isset($cur['grids']) && is_array($cur['grids'])) {
			foreach ($cur['grids'] as $ck => $c) {
				if ($c['uid'] == $uid) {
					unset($cur['grids'][$ck]['is_new']);
					break;
				}
			}
		}

		$this->set_templates($cur);
	}

	/**
	 * Update the Images get them from Server and check for existance on each image
	 * @since: 2.3
	 */
	private function _update_images()
	{
		global $wp_version, $esg_loadbalancer;

		$wp_filesystem = Essential_Grid_Base::get_filesystem();

		$templates = $this->get_templates();
		$url = $esg_loadbalancer->get_url('templates');
		
		$connection = 0;

		if (!empty($templates) && is_array($templates)) {
			$upload_dir = wp_upload_dir(); // Set upload folder
			if (!empty($templates['grids']) && is_array($templates['grids'])) {
				foreach ($templates['grids'] as $key => $temp) {

					if ($connection > 3) continue; //cant connect to server

					// Check folder permission and define file location
					if (wp_mkdir_p($upload_dir['basedir'] . $this->library_path)) {
						$file = $upload_dir['basedir'] . $this->library_path . '/' . $temp['img'];
						$file_plugin = ESG_PLUGIN_PATH . $this->library_path_plugin . '/' . $temp['img'];
						$image_data = false;
						
						if ((!file_exists($file) && !file_exists($file_plugin)) || isset($temp['push_image'])) {
							$count = 0;
							do {
								// Get image data
								$image_request = wp_safe_remote_get($url . '/' . $this->library_server_path . $temp['img']);
								if (is_wp_error($image_request)) {
									$esg_loadbalancer->move_server_list();
									$url = $esg_loadbalancer->get_url('templates');
								} else {
									$image_data = wp_remote_retrieve_body($image_request);
								}
								$count++;
							} while (is_wp_error($image_request) && $count < 5);
							
							if ($image_data !== false) {
								unset($templates['grids'][$key]['push_image']);
								wp_mkdir_p(dirname($file));
								$wp_filesystem->put_contents($file, $image_data);
							} else {
								//could not connect to server
								$connection++;
							}
						}
					}
				}
			}
		}

		//remove the push_image
		$this->set_templates($templates);
	}

	/**
	 * get default ThemePunch default Grids
	 * @since: 2.3
	 */
	public function get_tp_template_grids()
	{
		$tmpl_option = $this->get_templates();
		$grids = Essential_Grid_Base::getVar($tmpl_option, 'grids', []);
		krsort($grids);

		return $grids;
	}
	
	/**
	 * get Grid filters
	 * @since: 3.0.16
	 */
	public function get_tp_template_filters()
	{
		$tmpl_option = $this->get_templates();
		return Essential_Grid_Base::getVar($tmpl_option, 'filters', []);
	}

	/**
	 * Get the HTML for all Library Grids
	 * 
	 * @param array $tp_grids
	 * @return string
	 */
	public function get_library_grids_html($tp_grids)
	{
		ob_start();
		include(ESG_PLUGIN_ADMIN_PATH . '/views/elements/grid-library-templates-wrapper.php');
		return ob_get_clean();
	}

	/**
	 * output markup for the import grid, the zip was not yet improted
	 * @since: 2.3
	 */
	public function write_import_template_markup($grid)
	{
		$allow_install = true;

		$grid['img'] = $this->_check_file_path($grid['img'], true);

		//check for version and compare, only allow download if version is high enough
		$deny = '';
		if (isset($grid['required'])) {
			if (version_compare(ESG_REVISION, $grid['required'], '<')) {
				$deny = ' deny_download';
			}
		}

		ob_start();
		include(ESG_PLUGIN_ADMIN_PATH . '/views/elements/grid-library-template.php');
		return ob_get_clean();
	}


	/**
	 * check if image was uploaded, if yes, return path or url
	 * @since: 2.3
	 */
	public function _check_file_path($image, $url = false)
	{
		$upload_dir = wp_upload_dir(); // Set upload folder
		$file = $upload_dir['basedir'] . $this->library_path . $image;
		$file_plugin = ESG_PLUGIN_PATH . $this->library_path_plugin . $image;

		if (file_exists($file)) {
			//downloaded image first, for update reasons
			if ($url) {
				$image = $upload_dir['baseurl'] . $this->library_path . $image;
			} else {
				$image = $upload_dir['basedir'] . $this->library_path . $image; //server path
			}
		} elseif (file_exists($file_plugin)) {
			if ($url) {
				$image = ESG_PLUGIN_URL . $this->library_path_plugin . $image;
			} else {
				$image = ESG_PLUGIN_PATH . $this->library_path_plugin . $image;
			}
		} else {
			//redownload image from server and store it
			$this->_update_images();
			if (file_exists($file)) {
				//downloaded image first, for update reasons
				if ($url) {
					$image = $upload_dir['baseurl'] . $this->library_path . $image;
				} else {
					$image = $upload_dir['basedir'] . $this->library_path . $image; //server path
				}
			} else {
				$image = false;
			}
		}

		return $image;
	}


	/**
	 * import Grid from TP servers
	 * @since: 2.3
	 */
	public function import_grid($uid, $zip, $ignore_exists = false)
	{
		$wp_filesystem = Essential_Grid_Base::get_filesystem();
		
		$return = [];

		if ($uid == '') {
			return esc_attr__("ID missing, something went wrong. Please try again!", 'essential-grid');
		} else {
			$uids = (array)$uid;

			if (!empty($uids)) {
				foreach ($uids as $uid) {

					// can be single or multiple
					$filepath = $this->_download_template($uid); 
					// send request to TP server and download file
					if (is_array($filepath) && isset($filepath['error'])) {
						return $filepath['error'];
					}

					if ($filepath !== false) {
						// pull the content from the filepath
						$upload_dir = wp_upload_dir();
						$rem_path = $upload_dir['basedir'] . '/esgtemp/';
						$d_path = $rem_path;
						$content = '';

						$unzipfile = unzip_file($filepath, $d_path);

						if (!is_wp_error($unzipfile)) {
							$content = ($wp_filesystem->exists($d_path . 'ess_grid.json')) ? $wp_filesystem->get_contents($d_path . 'ess_grid.json') : '';
							$content = json_decode($content, true);
							// import custom images
							$content = $this->import_custom_images($content, $d_path);
						}

						$wp_filesystem->delete($rem_path, true);
						$this->_delete_template($uid);

						if (is_array($content)) {
							$return = apply_filters('essgrid_library_import_grid_template_before', $return, $content);

							$grids = (isset($content['grids'])) ? $content['grids'] : [];
							$grids_ids = [];
							if (!empty($grids)) {
								foreach ($grids as $k => $grid) {
									$grids_ids[] = $grid['id'];
									
									$params = json_decode($grid['params'], true);
									$params['pg'] = 'true';
									$grids[$k]['params'] = wp_json_encode($params);
								}
							}
							
							$im = new Essential_Grid_Import();
							$skins = (isset($content['skins'])) ? $content['skins'] : [];
							$skins_ids = [];
							if (!empty($skins)) {
								foreach ($skins as $skin) {
									$skins_ids[] = $skin['id'];
								}
								$im->import_skins($skins, $skins_ids, true, $ignore_exists);
							}

							$navigation_skins = (isset($content['navigation-skins'])) ? $content['navigation-skins'] : [];
							$navigation_skins_ids = [];
							if (!empty($navigation_skins)) {
								foreach ($navigation_skins as $skin) {
									$navigation_skins_ids[] = $skin['id'];
								}
								$im->import_navigation_skins($navigation_skins, $navigation_skins_ids, true, $ignore_exists);
							}

							if (!empty($grids)) {
								$return['grids_imported'] = $im->import_grids($grids, $grids_ids);
							}

							$elements = (isset($content['elements'])) ? $content['elements'] : [];
							$elements_ids = [];
							if (!empty($elements)) {
								foreach ($elements as $element) {
									$elements_ids[] = $element['id'];
								}
								$im->import_elements(@$elements, $elements_ids);
							}

							$custom_metas = (isset($content['custom-meta'])) ? $content['custom-meta'] : [];
							if (!empty($custom_metas) && is_array($custom_metas)) {
								foreach ($custom_metas as $key => $custom_meta) {
									$custom_metas[$key] = $custom_meta;
								}
								if (!empty($custom_metas)) {
									$im->import_custom_meta($custom_metas);
								}
							}

							$global_css = (isset($content['global-css'])) ? $content['global-css'] : '';
							$im->import_global_styles($global_css);

							$return = apply_filters('essgrid_library_import_grid_template_after', $return, $content);
							
						} else {
							return esc_attr__("Could not download Grid. Please try again later!", 'essential-grid');
						}
					} else {
						return esc_attr__("Could not download from server. Please try again later!", 'essential-grid');
					}
				}
			} else {
				return esc_attr__("Could not download Grid. Please try again later!", 'essential-grid');
			}
		}

		return $return;
	}

	// read the json import file
	public function import_custom_images($json, $path)
	{
		//search for the layers part
		$grids = $json["grids"];
		$new_grids = [];
		if (is_array($grids)) {
			foreach ($grids as $grid) {
				$layers = json_decode($grid["layers"]);
				//find the image ids
				$new_layers = [];
				if (is_array($layers)) {
					foreach ($layers as $layer) {
						$layer = json_decode($layer);
						if (isset($layer->{'custom-type'}) && $layer->{'custom-type'} == "image") {
							$custom_image = $path . $layer->{'custom-image'} . ".jpg";
							//import the image and replace the id
							$layer->{'custom-image'} = "" . $this->create_image($custom_image);
							if (!empty($layer->{'eg-alternate-image'})) {
								$alternate_image = $path . $layer->{'eg-alternate-image'} . ".jpg";
								//import the image and replace the id
								$layer->{'eg-alternate-image'} = "" . $this->create_image($alternate_image);
							}
						}
						$new_layers[] = wp_json_encode($layer);
					}
				}
				$grid["layers"] = wp_json_encode($new_layers);
				$new_grids[] = $grid;
			}
		}
		$json["grids"] = $new_grids;

		return $json;
	}

	public function create_image($file)
	{
		$wp_filesystem = Essential_Grid_Base::get_filesystem();
		
		if (empty($file)) return false;

		$upload_dir = wp_upload_dir();
		$image_url = $file;
		$image_data = $wp_filesystem->get_contents($image_url);
		$filename = basename($image_url);
		if (wp_mkdir_p($upload_dir['path']))
			$file = $upload_dir['path'] . '/' . $filename;
		else
			$file = $upload_dir['basedir'] . '/' . $filename;
		$wp_filesystem->put_contents($file, $image_data);

		$wp_filetype = wp_check_filetype($filename);
		$attachment = [
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name($filename),
				'post_content' => '',
				'post_status' => 'inherit'
		];

		$attach_id = wp_insert_attachment($attachment, $file);
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata($attach_id, $file);
		wp_update_attachment_metadata($attach_id, $attach_data);

		return $attach_id;
	}
}
