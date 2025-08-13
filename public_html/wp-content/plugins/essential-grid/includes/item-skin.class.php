<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Item_Skin
{

	public $grid_id = 0;
	public $grid_params = [];

	/**
	 * @var string|int 
	 */
	private $id = '';
	private $name = '';
	private $handle = '';
	private $grid_type = 'even';
	private $params = [];
	private $layers = [];
	
	/**
	 * values to fill inside skin if we have custom selected. First false, becomes an array
	 * 
	 * @var bool|array
	 */
	private $layer_values = false;
	
	private $settings = [];
	private $filter = [];
	private $sorting = [];
	private $layers_css = [];
	private $layers_meta_css = [];
	private $cover_css = [];
	private $media_css = [];
	private $wrapper_css = [];
	private $content_css = [];
	private $media_poster_css = [];
	private $google_fonts = [];
	private $cover_image = '';
	private $cover_shadow = [];
	private $wrapper_shadow = [];
	private $media_shadow = [];
	private $content_shadow = [];

	/* 2.1.6.2 */
	private $grid_item_animation = 'none';
	private $grid_item_animation_other = 'none';
	private $grid_item_animation_zoomin = '125';
	private $grid_item_other_zoomin = '125';
	private $grid_item_animation_zoomout = '75';
	private $grid_item_other_zoomout = '75';
	private $grid_item_animation_fade = '75';
	private $grid_item_other_fade = '75';
	private $grid_item_animation_blur = '5';
	private $grid_item_other_blur = '5';
	private $grid_item_animation_shift = 'top';
	private $grid_item_other_shift = 'top';
	private $grid_item_animation_shift_amount = '10';
	private $grid_item_other_shift_amount = '10';
	private $grid_item_animation_rotate = '30';
	private $grid_item_other_rotate = '30';

	private $default_image = '';
	private $default_image_attr = [];
	private $default_youtube_image = '';
	private $default_vimeo_image = '';
	private $default_html_image = '';
	private $media_sources = [];
	private $video_sizes = ['0' => ['height' => '480', 'width' => '640'], '1' => ['height' => '576', 'width' => '1024']];
	private $video_ratios = ['vimeo' => '1', 'youtube' => '1', 'wistia' => '1', 'html5' => '1'];
	private $media_sources_type = 'full';
	private $item_media_type = ''; //gets the media type for later usage in advanced rules
	private $default_media_source_order = [];
	private $default_video_poster_order = [];
	private $default_lightbox_source_order = [];
	private $default_ajax_source_order = [];
	private $do_poster_cropping = false;
	private $lightbox_additions = ['items' => [], 'base' => 'off']; //lightbox addition off
	private $lightbox_thumbnail = '';
	/**
	 * @var string|bool 
	 */
	private $lb_rel = false;
	private $loaded_skins = []; //holds all loaded skins that can be switchted to. Make sure that default skin is also present on init
	private $item_counter = 0; //for custom grids, holds the ID that is needed for search results and to give each Item a unique class

	private $add_css_tags = []; //example usage: $this->add_css_tags[$unique_class]['a'] = true; //this will give the inner a tags styling information
	private $add_css_wrap = []; //example usage: $this->add_css_wrap[$unique_class]['wrap'] = true; //this will give the wrapping div element position and other stylings

	private $post = [];
	private $post_meta = [];

	private $load_more_element = false;
	private $lazy_load = false;
	private $lazy_load_blur = false;

	private $load_lightbox = false;

	public $ajax_loading = false;
	
	private $item_title = '';

	/**
	 * @return string
	 */
	public function get_item_title() {
		return $this->item_title;
	}

	/**
	 * @param string $item_title
	 */
	public function set_item_title($item_title) {
		$this->item_title = $item_title;
	}

	/**
	 * @return int
	 */
	public function get_item_counter() {
		return $this->item_counter;
	}

	/**
	 * @param int $item_counter
	 */
	public function set_item_counter( $item_counter ) {
		$this->item_counter = $item_counter;
	}

	/**
	 * init item skin by json data
	 */
	public function init_by_data($data) {
		$data = apply_filters('essgrid_init_by_data_item_skin', $data);
		$this->add_to_skin_list($data);

		$this->id = $data['id'];
		$this->name = $data['name'];
		$this->handle = $data['handle'];
		$this->params = $data['params'];
		$this->layers = $data['layers'];
		$this->settings = $data['settings'];

		$this->sort_item_skins();
	}

	/**
	 * init item skin by id
	 */
	public function init_by_id($id) {
		$id = intval($id);
		if ($id == 0) return false;

		$skin = apply_filters('essgrid_init_by_id', Essential_Grid_Db::get_entity('skins')->get( $id ), $id);
		if (!empty($skin)) {
			$this->id = $skin['id'];
			$this->name = $skin['name'];
			$this->handle = $skin['handle'];
			$params = Essential_Grid_Base::stripslashes_deep(@json_decode($skin['params'], true));
			$this->params = $params;
			$layers = @json_decode($skin['layers'], true);
			if (!empty($layers) && is_array($layers)) { 
				//prevent overhead
				foreach ($layers as $lkey => $layer) {
					$layers[$lkey] = Essential_Grid_Base::stripslashes_deep($layer);
				}
			}
			$this->layers = $layers;
			$this->settings = [];
			if (!empty($skin['settings'])) {
				$this->settings = Essential_Grid_Base::stripslashes_deep(@json_decode($skin['settings'], true));
			}

			//add to skin list
			$skin['params'] = $params;
			$skin['layers'] = $layers;
			$skin['settings'] = $this->settings;
			$this->add_to_skin_list($skin);
		}

		$this->sort_item_skins();
		
		return $this->id;
	}

	/**
	 * Return all item skins
	 */
	public static function get_essential_item_skins($type = 'all', $do_decode = true) {
		$item_skins = [];

		switch ($type) {
			case 'even':
			case 'masonry':
				break;
			case 'all':
			default:
				$item_skins = Essential_Grid_Db::get_entity('skins')->get_all();
		}

		if (!empty($item_skins) && $do_decode) {
			foreach ($item_skins as $key => $skin) {
				$item_skins[$key]['params'] = Essential_Grid_Base::stripslashes_deep(@json_decode($skin['params'], true));
				$layers = @json_decode($skin['layers'], true);

				if (!empty($layers) && is_array($layers)) { 
					//prevent overhead
					foreach ($layers as $lkey => $layer) {
						$layers[$lkey] = Essential_Grid_Base::stripslashes_deep($layer);
					}
				}
				$item_skins[$key]['layers'] = $layers;
				if (!empty($item_skins[$key]['settings'])) {
					$item_skins[$key]['settings'] = Essential_Grid_Base::stripslashes_deep(@json_decode($item_skins[$key]['settings'], true));
				} else {
					$item_skins[$key]['settings'] = [];
				}
				
			}
		}

		return apply_filters('essgrid_get_essential_item_skins', $item_skins, $type, $do_decode);
	}

	/**
	 * Get Item Skin handle by ID from database
	 * @since: 1.5.0
	 */
	public static function get_handle_by_id($id = 0) {
		return Essential_Grid_Db::get_entity('skins')->get($id);
	}

	/**
	 * Get Item Skin ID by handle from database
	 * @since: 1.5.0
	 */
	public static function get_id_by_handle($handle = '') {
		return Essential_Grid_Db::get_entity('skins')->get_by_handle($handle);
	}

	/**
	 * Get Item Skin by ID from Database
	 */
	public static function get_essential_item_skin_by_id($id = 0) {
		$id = intval($id);
		if ($id == 0) return false;

		$skin = Essential_Grid_Db::get_entity('skins')->get($id);
		if (!empty($skin)) {
			$skin['params'] = Essential_Grid_Base::stripslashes_deep(@json_decode($skin['params'], true));
			$layers = @json_decode($skin['layers'], true);
			if (!empty($layers) && is_array($layers)) { 
				//prevent overhead
				foreach ($layers as $lkey => $layer) {
					$layers[$lkey] = Essential_Grid_Base::stripslashes_deep($layer);
				}
			}
			$skin['layers'] = $layers;
			if (!empty($skin['settings'])) {
				$skin['settings'] = Essential_Grid_Base::stripslashes_deep(@json_decode($skin['settings'], true));
			} else {
				$skin['settings'] = [];
			}
		}

		return apply_filters('essgrid_get_essential_item_skin_by_id', $skin, $id);
	}
	
	/**
	 * Switch between Item Skins to allow more than one Skin in a Grid
	 * @since: 2.0
	 */
	public function switch_item_skin($skin_id) {
		$skin_id = apply_filters('essgrid_switch_item_skin', $skin_id, $this->loaded_skins);
		$to_default = false;

		if ($skin_id == -1) {
			$to_default = true;
		} else {
			//1. Check if Skin is already loaded (new variable to check if ID is initialized)
			if (!isset($this->loaded_skins[$skin_id])) {
				//2. If not, get it and set it with: get_essential_item_skin_by_id()
				$skin = $this->get_essential_item_skin_by_id($skin_id);
				if (empty($skin)) {
					$to_default = true;
				} else {
					//3. Switch the current things to the new one with: init_by_data()
					$this->init_by_data($skin);
				}
			} elseif ($this->id !== $skin_id) {
				//3. Switch the current things to the new one with: init_by_data()
				$this->init_by_data($this->loaded_skins[$skin_id]);
			}
		}

		//switch to default skin, which means first in loaded_skins
		if ($to_default) {
			$data = reset($this->loaded_skins);
			//3. Switch the current things to the new one with: init_by_data()
			if ($data !== false)
				$this->init_by_data($data);
		}
	}

	/**
	 * Add Skin to the loaded_skins if not already existing
	 * @since: 2.0
	 */
	public function add_to_skin_list($data) {
		$data = apply_filters('essgrid_add_to_skin_list', $data);
		if (!isset($this->loaded_skins[$data['id']])) $this->loaded_skins[$data['id']] = $data;
	}

	/**
	 * Update / Save Item Skins
	 */
	public static function update_save_item_skin($data) {
		$data = apply_filters('essgrid_update_save_item_skin', $data);
		if (isset($data['name'])) {
			if (strlen($data['name']) < 2) return esc_attr__('Invalid name. Name has to be at least 2 characters long.', 'essential-grid');
			if (strlen(sanitize_title($data['name'])) < 2) return esc_attr__('Invalid name. Name has to be at least 2 characters long.', 'essential-grid');
		} else {
			return esc_attr__('Invalid name. Name has to be at least 2 characters long.', 'essential-grid');
		}

		if (isset($data['id'])) {
			if (intval($data['id']) == 0) return esc_attr__('Invalid Item Skin. Wrong ID given.', 'essential-grid');
		}

		if (isset($data['layers'])) { 
			//set back to array for testing and stripping
			$layers = json_decode(stripslashes($data['layers']));
			if (empty($layers)) $layers = json_decode($data['layers']);
			$data['layers'] = $layers;
		}

		if (empty($data['params'])) return esc_attr__('No parameters found.', 'essential-grid');
		if (empty($data['layers'])) $data['layers'] = []; //allow empty layers

		if (isset($data['id']) && intval($data['id']) > 0) { 
			// check if entry with id exists, because this is unique
			$skin = Essential_Grid_Db::get_entity('skins')->get( $data['id'] );
			if (empty($skin)) {
				return esc_attr__('Invalid Item skin ID.', 'essential-grid');
			}
			
			$skin_by_handle = Essential_Grid_Db::get_entity('skins')->get_by_handle( sanitize_title( $data['name'] ) );
			if ( !empty($skin_by_handle) && $skin_by_handle['id'] != $data['id'] ) {
				return esc_attr__('Item skin with chosen name already exist. Please use a different name.', 'essential-grid');
			}

			$response = Essential_Grid_Db::get_entity('skins')->update(
				[
					'name' => $data['name'],
					'handle' => sanitize_title($data['name']),
					'params' => wp_json_encode($data['params']),
					'layers' => wp_json_encode($data['layers'])
				],
				$data['id']
			);

			if ($response === false) return esc_attr__('Item skin could not be changed.', 'essential-grid');

			return true;
			
		} else {
			// check if exists, if no, create
			$skin = Essential_Grid_Db::get_entity('skins')->get_by_handle( sanitize_title( $data['name'] ) );
			if (!empty($skin)) {
				return esc_attr__('Item skin with chosen name already exist. Please use a different name.', 'essential-grid');
			}

			$response = Essential_Grid_Db::get_entity('skins')->insert(
				[
					'name' => $data['name'],
					'handle' => sanitize_title($data['name']),
					'params' => wp_json_encode($data['params']),
					'layers' => wp_json_encode($data['layers'])
				]
			);
			if ($response === false) return false;

			return true;
		}
		
		return true;
	}

	/**
	 * Delete Item Skin
	 *
	 * @param array $data
	 * 
	 * @return string|bool
	 */
	public static function delete_item_skin_by_id($data) {
		$data = apply_filters('essgrid_delete_item_skin_by_id', $data);
		if (!isset($data['id']) || intval($data['id']) == 0) return esc_attr__('Invalid ID', 'essential-grid');

		$response = Essential_Grid_Db::get_entity('skins')->delete( $data['id'] );
		if ($response === false) return esc_attr__('Item Skin could not be deleted', 'essential-grid');

		return true;
	}

	/**
	 * Sort Item Skin and delete empty layers
	 * @return void
	 */
	public function sort_item_skins() {
		if (!empty($this->layers)) {
			//clean empty layers
			foreach ($this->layers as $id => $layer) {
				if (empty($layer)) unset($this->layers[$id]);
			}
			//order layers by order
			if (count($this->layers) >= 2)
				usort($this->layers, ['Essential_Grid_Base', 'sort_by_order']);
		}
		$this->layers = apply_filters('essgrid_sort_item_skins', $this->layers);
	}
	
	/**
	 * Set Lazy Loading Variable
	 */
	public function set_lazy_load($set_to) {
		$this->lazy_load = apply_filters('essgrid_set_lazy_load', $set_to);
	}
	
	/**
	 * Set Lazy Loading Blur Variable
	 */
	public function set_lazy_load_blur($set_to) {
		$this->lazy_load_blur = apply_filters('essgrid_set_lazy_load_blur', $set_to);
	}

	/**
	 * Set Lazy Loading Variable
	 */
	public function set_grid_type($grid_type) {
		$this->grid_type = apply_filters('essgrid_set_grid_type', $grid_type);
	}

	/**
	 * Set Lazy Loading Variable
	 */
	public function set_lightbox_rel($rel) {
		$this->lb_rel = apply_filters('essgrid_set_lightbox_rel', $rel);
	}

	/**
	 * Set default lightbox source order
	 */
	public function set_default_lightbox_source_order($order) {
		$this->default_lightbox_source_order = apply_filters('essgrid_set_default_lightbox_source_order', $order);
	}

	/**
	 * Set default ajax source order
	 * @since: 1.5.0
	 */
	public function set_default_ajax_source_order($order) {
		$this->default_ajax_source_order = apply_filters('essgrid_set_default_ajax_source_order', $order);
	}

	/**
	 * Set default media source order
	 */
	public function set_default_media_source_order($order) {
		$this->default_media_source_order = apply_filters('essgrid_set_default_media_source_order', $order);
	}

	/**
	 * Set default media source order
	 */
	public function set_default_video_poster_order($order) {
		$this->default_video_poster_order = apply_filters('essgrid_set_default_video_poster_order', $order);
	}

	/**
	 * Set default media source order
	 */
	public function set_poster_cropping($set_to) {
		$this->do_poster_cropping = apply_filters('essgrid_set_poster_cropping', $set_to);
	}

	/**
	 * Set LightBox mode
	 * @since: 1.5.4
	 */
	public function set_lightbox_addition($addition) {
		$this->lightbox_additions = apply_filters('essgrid_set_lightbox_addition', $addition);
	}

	/**
	 * Set video ratios
	 */
	public function set_video_ratios($video_ratios) {
		$video_ratios = apply_filters('essgrid_set_video_ratios', $video_ratios);

		if (isset($video_ratios['vimeo']))
			$this->video_ratios['vimeo'] = intval($video_ratios['vimeo']);

		if (isset($video_ratios['youtube']))
			$this->video_ratios['youtube'] = intval($video_ratios['youtube']);

		if (isset($video_ratios['wistia']))
			$this->video_ratios['wistia'] = intval($video_ratios['wistia']);

		if (isset($video_ratios['html5']))
			$this->video_ratios['html5'] = intval($video_ratios['html5']);

		if (isset($video_ratios['soundcloud']))
			$this->video_ratios['soundcloud'] = intval($video_ratios['soundcloud']);

		$this->video_ratios = apply_filters('essgrid_set_video_ratios_set', $this->video_ratios, $video_ratios);
	}

	/**
	 * Set Sorting Values
	 */
	public function set_sorting($data) {
		$this->sorting = $data + $this->sorting; //merges the array and preserves the key
		arsort($this->sorting);
		$this->sorting = apply_filters('essgrid_set_sorting', $this->sorting, $data);
	}

	/**
	 * Star Item Skin
	 * 
	 * @param array $data
	 * 
	 * @return boolean
	 */
	public static function star_item_skin_by_id($data) {
		$data = apply_filters('essgrid_star_item_skin_by_id', $data);
		if (!isset($data['id']) || intval($data['id']) == 0) return esc_attr__('Invalid ID', 'essential-grid');

		$item_skin = Essential_Grid_Db::get_entity('skins')->get( $data['id'] );
		if (empty($item_skin)) return esc_attr__('Invalid Skin', 'essential-grid');

		$settings = json_decode($item_skin['settings'], true);
		if (!isset($settings['favorite']) || !$settings['favorite'])
			$settings['favorite'] = true;
		else
			$settings['favorite'] = false;

		$response = Essential_Grid_Db::get_entity('skins')->update( [ 'settings' => wp_json_encode($settings) ], $data['id'] );
		if ($response === false) return esc_attr__('Could not change Favorite', 'essential-grid');

		return true;
	}

	/**
	 * Duplicate Item Skin
	 * 
	 * @param array $data
	 * 
	 * @return boolean
	 * @throws Exception
	 */
	public static function duplicate_item_skin_by_id($data) {
		if (!isset($data['id']) || intval($data['id']) == 0) return esc_attr__('Invalid ID', 'essential-grid');
		
		//check if ID exists
		$duplicate = Essential_Grid_Db::get_entity('skins')->get( $data['id'] );
		if (empty($duplicate)) return esc_attr__('Item Skin could not be duplicated', 'essential-grid');

		$i = Essential_Grid_Db::get_entity('skins')->get_max_id();
		do {
			$i++;
			$handle_check = Essential_Grid_Db::get_entity('skins')->get_by_handle( 'item-skin-' . $i );
		} while ( !empty($handle_check) );

		//save handle
		$original_handle = $duplicate['handle'];
		 
		//now add new Entry
		unset($duplicate['id']);
		$duplicate['name'] = 'Item Skin ' . $i;
		$duplicate['handle'] = 'item-skin-' . $i;
		$duplicate = apply_filters('essgrid_duplicate_item_skin_by_id', $duplicate);
		$response = Essential_Grid_Db::get_entity('skins')->insert( $duplicate );
		if ($response === false) return esc_attr__('Item Skin could not be duplicated', 'essential-grid');

		//check for global CSS
		$css = Essential_Grid_Global_Css::get_global_css_styles();
		if (strpos($css, 'eg-' . $original_handle) !== false ) {
			$matches = [];
			$pattern = "/\." . 'eg-' . $original_handle . ".*\{.*\}/i";
			preg_match_all($pattern, $css, $matches);
			if (!empty($matches[0])) {
				$css .= "\r\n\r\n";
				foreach ($matches[0] as $line) {
					$css .= str_replace('.eg-' . $original_handle, '.eg-' . $duplicate['handle'], $line) . "\r\n";
				}
				Essential_Grid_Global_Css::set_global_css_styles($css);
			}
		}

		return true;
	}

	/**
	 * insert default Item Skins
	 */
	public static function propagate_default_item_skins($networkwide = false) {
		$skins = self::get_default_item_skins();
		if (function_exists('is_multisite') && is_multisite() && $networkwide) { 
			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$skins = apply_filters('essgrid_propagate_default_item_skins_multisite', $skins, $site->blog_id);
				self::insert_default_item_skins($skins);
				// 2.2.5
				restore_current_blog();
			}
		} else {
			$skins = apply_filters('essgrid_propagate_default_item_skins', $skins);
			self::insert_default_item_skins($skins);
		}
	}

	/**
	 * All default Item Skins
	 */
	public static function get_default_item_skins() {
		$default = [];
		include('assets/default-item-skins.php');
		$default = apply_filters('essgrid_add_default_item_skins', $default); //backwards compatibility
		return apply_filters('essgrid_get_default_item_skins', $default);
	}

	/**
	 * Insert Default Skin if they are not already installed
	 */
	public static function insert_default_item_skins($data) {
		$data = apply_filters('essgrid_insert_default_item_skins', $data);
		if ( empty( $data ) ) return;
		
		foreach ($data as $skin) {
			// check if exists, if no, create
			$check = Essential_Grid_Db::get_entity('skins')->get_by_handle( $skin['handle'] );
			if (!empty($check)) continue;
			
			Essential_Grid_Db::get_entity('skins')->insert( [
				'name' => $skin['name'],
				'handle' => $skin['handle'],
				'params' => $skin['params'],
				'layers' => $skin['layers']
			] );
		}
	}
	
	/**
	 * Set Skin to Load More, giving for example the LI a new class
	 */
	public function set_load_more() {
		$this->load_more_element = apply_filters('essgrid_set_load_more', true);
	}

	/**
	 * Returns all Layers that the Skin has
	 * @since: 1.2.0
	 */
	public function get_skin_layer() {
		return apply_filters('essgrid_get_skin_layer', $this->layers);
	}
	
	/**
	 * Get the Skin ID
	 * @since: 1.2.0
	 */
	public function get_skin_id() {
		return apply_filters('essgrid_get_skin_id', $this->id);
	}

	/**
	 * @param string $media_type
	 *
	 * @return string
	 */
	public function get_video_poster_src($media_type) {
		$video_poster_src = '';
		if (empty($this->default_video_poster_order)) return $video_poster_src;

		foreach ($this->default_video_poster_order as $order) {
			
			$found = true;
			
			switch ($order) {
				case 'no-image':
					$video_poster_src = '';
					break;
					
				case 'featured-image':
				case 'alternate-image':
				case 'content-image':
					if (empty($this->media_sources[$order])) { 
						$found = false; 
						break; 
					}
					$video_poster_src = $this->media_sources[$order];
					break;
					
				case 'default-youtube-image':
					$video_poster_src = $this->default_youtube_image;
					break;
					
				case 'youtube-image':
					if (!in_array($media_type, ['youtube', 'content-youtube']) || empty($this->media_sources[$media_type])) {
						$found = false;
						break;
					}
					
					if (isset($this->layer_values['custom-image-url'][0])) {
						//yt image loaded into layer
						$video_poster_src = $this->layer_values['custom-image-url'][0];
						break;
					}
					
					$video_poster_src = $this->_get_youtube_thumb($this->media_sources[$media_type]);
					if (empty($video_poster_src)) {
						$found = false;
						break;
					}
					break;
					
				case 'default-vimeo-image':
					$video_poster_src = $this->default_vimeo_image;
					break;
					
				case 'vimeo-image':
					if (!in_array($media_type, ['vimeo', 'content-vimeo']) || empty($this->media_sources[$media_type])) {
						$found = false;
						break;
					}
					$video = $this->get_remote_json('https://vimeo.com/api/v2/video/' . esc_attr($this->media_sources[$media_type]) . '.json');
					if (is_null($video)) {
						$found = false;
						break;
					}
					if (isset($video[0]->thumbnail_large)) {
						$video_poster_src = str_replace('http://', 'https://', $video[0]->thumbnail_large);
					}
					break;
					
				case 'default-html-image':
					$video_poster_src = $this->default_html_image;
					break;
					
				default:
					$found = false;
			}

			if ($found) break;
		}

		//if we are masonry, we need to crop the image
		if ($this->do_poster_cropping) {
			$video_poster_src = esg_aq_resize(
				$video_poster_src, 
				$this->video_sizes[$this->video_ratios[$media_type]]['width'], 
				$this->video_sizes[$this->video_ratios[$media_type]]['height'], 
				true, 
				true, 
				true
			);
		}
		
		return $video_poster_src;
	}

	/**
	 * @param array $media_source_order
	 *
	 * @return array
	 */
	public function get_media_html($media_source_order) {
		$main_img = '';
		$is_video = false;
		$is_iframe = false;
		$lightbox_thumb = false;
		$echo_media = '';
		$order = '';
		
		if ( !empty($media_source_order) ) {
			foreach ($media_source_order as $order) {

				if (empty($this->media_sources[$order]) ) continue;

				$found = true;

				switch ($order) {
					case 'featured-image':
					case 'alternate-image':
					case 'content-image':
						$img_dim = $this->get_media_attributes($order);

						if ($this->lazy_load) {
							$small_thumb = '';
							if ($this->lazy_load_blur) {
								if (isset($this->media_sources['alternate-image-preload-url']) && !empty($this->media_sources['alternate-image-preload-url'])) {
									$small_thumb = ' data-lazythumb="' . esc_attr($this->media_sources['alternate-image-preload-url']) . '"';
								} else {
									$small_thumb = esg_aq_resize($this->media_sources[$order], 25, 25, true, true, true);
									if ($small_thumb !== $this->media_sources[$order]) {
										$small_thumb = ' data-lazythumb="' . esc_attr($small_thumb) . '"';
									} else {
										$small_thumb = '';
									}
								}
							}
							$lightbox_thumb = $this->media_sources[$order];
							// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
							$echo_media = '<img src="' . esc_url( ESG_PLUGIN_URL . 'public/assets/images/300x200transparent.png' ) . '"' . $small_thumb . ' data-no-lazy="1" data-lazysrc="' . esc_attr($this->media_sources[$order]) . '" alt="' . esc_attr($this->media_sources[$order . '-alt']) . '" title="' . esc_attr($this->media_sources[$order . '-title']) . '"' . $img_dim . '>';
						} else {
							// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage 
							$echo_media = '<img src="' . esc_url($this->media_sources[$order]) . '" data-no-lazy="1" alt="' . esc_attr($this->media_sources[$order . '-alt']) . '" title="' . esc_attr($this->media_sources[$order . '-title']) . '"' . $img_dim . '>';
							$lightbox_thumb = esc_attr($this->media_sources[$order]);
						}
						$main_img = $this->media_sources[$order];
						break;

					case 'youtube':
					case 'content-youtube':
						$video_poster_src = $this->get_video_poster_src($order);
						$lightbox_thumb = $video_poster_src;

						$echo_media = '<div class="esg-media-video" data-youtube="' . esc_attr($this->media_sources[$order]) . '" width="' . esc_attr($this->video_sizes[$this->video_ratios['youtube']]['width']) . '" height="' . esc_attr($this->video_sizes[$this->video_ratios['youtube']]['height']) . '" data-poster="' . esc_attr($video_poster_src) . '"></div>';
						$is_video = true;
						$main_img = $video_poster_src;
						break;

					case 'vimeo':
					case 'content-vimeo':
						$video_poster_src = $this->get_video_poster_src($order);
						$lightbox_thumb = $video_poster_src;

						$echo_media = '<div class="esg-media-video" data-vimeo="' . esc_attr($this->media_sources[$order]) . '" width="' . esc_attr($this->video_sizes[$this->video_ratios['vimeo']]['width']) . '" height="' . esc_attr($this->video_sizes[$this->video_ratios['vimeo']]['height']) . '" data-poster="' . esc_attr($video_poster_src) . '"></div>';
						$is_video = true;
						$main_img = $video_poster_src;
						break;

					case 'wistia':
					case 'content-wistia':
						$video_poster_src = $this->get_video_poster_src($order);
						$lightbox_thumb = $video_poster_src;

						$echo_media = '<div class="esg-media-video" data-wistia="' . esc_attr($this->media_sources[$order]) . '" width="' . esc_attr($this->video_sizes[$this->video_ratios['wistia']]['width']) . '" height="' . esc_attr($this->video_sizes[$this->video_ratios['wistia']]['height']) . '" data-poster="' . esc_attr($video_poster_src) . '"></div>';
						$is_video = true;
						$main_img = $video_poster_src;
						break;

					case 'html5':
					case 'content-html5':
						if ((!isset($this->media_sources[$order]['mp4']) || $this->media_sources[$order]['mp4'] == '')
							&& (!isset($this->media_sources[$order]['webm']) || $this->media_sources[$order]['webm'] == '')
							&& (!isset($this->media_sources[$order]['ogv']) || $this->media_sources[$order]['ogv'] == '')
						) {
							//not a single video is set, go to the next instead of the break
							$found = false;
							break;
						}

						$video_poster_src = $this->get_video_poster_src($order);
						$lightbox_thumb = $video_poster_src;

						$echo_media = '<div class="esg-media-video" data-mp4="' . esc_attr(@$this->media_sources[$order]['mp4']) . '" data-webm="' . esc_attr(@$this->media_sources[$order]['webm']) . '" data-ogv="' . esc_attr(@$this->media_sources[$order]['ogv']) . '" width="' . esc_attr($this->video_sizes[$this->video_ratios['html5']]['width']) . '" height="' . esc_attr($this->video_sizes[$this->video_ratios['html5']]['height']) . '" data-poster="' . esc_attr($video_poster_src) . '"></div>';
						$is_video = true;
						$main_img = $video_poster_src;
						break;

					case 'soundcloud':
					case 'content-soundcloud':
						$video_poster_src = $this->get_video_poster_src($order);
						$lightbox_thumb = $video_poster_src;

						$echo_media = '<div class="esg-media-video" data-soundcloud="' . esc_attr($this->media_sources[$order]) . '" width="' . esc_attr($this->video_sizes[$this->video_ratios['soundcloud']]['width']) . '" height="' . esc_attr($this->video_sizes[$this->video_ratios['soundcloud']]['height']) . '" data-poster="' . esc_attr($video_poster_src) . '"></div>';
						$is_video = true;
						$main_img = $video_poster_src;
						break;

					case 'iframe':
					case 'content-iframe':
						$echo_media = html_entity_decode($this->media_sources[$order]);
						$is_iframe = true;
						break;

					default:
						$found = false;
				}

				if ($found) break;
			}
		}
		
		do_action('essgrid_get_media_html', $main_img, $this->grid_id);

		return [
			'echo_media' => $echo_media,
			'lightbox_thumb' => $lightbox_thumb,
			'is_iframe' => $is_iframe,
			'is_video' => $is_video,
			'order' => $order,
		];
	}
	
	protected function _echo_cover($cover_classes, $cover_data, $meta_cover_bg_color, $link_set_to, $link_type_link, $link_inserted, $link_wrapper) {
		$cover_tmpl = '                <div'
		              . ' class="' . implode(' ', array_map('esc_attr', $cover_classes)) . '"'
		              . ( !empty($meta_cover_bg_color) ? ' style="background: ' . esc_attr($meta_cover_bg_color) . ';"' : '' )
		              // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sniffer seems to raise false positive here
		              . ' ' . implode(' ', array_map(function($n, $m) { return 'data-' . sanitize_key($n) . '="'.esc_js($m).'"'; }, array_keys($cover_data), $cover_data))
		              . '></div>' . "\n\n";

		if ($link_set_to == 'cover' && $link_type_link !== 'none' && $link_inserted === false) {
			//set link on whole cover
			$cover_tmpl = str_replace('%REPLACE%', $cover_tmpl, $link_wrapper);
		}
		
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $cover_tmpl;
	}

	/**
	 * Output a full Skin with items by data
	 * @return void
	 */
	public function output_item_skin($demo = false, $choosen_skin = 0) {
		$base = new Essential_Grid_Base();
		$grid = new Essential_Grid();
		$m = new Essential_Grid_Meta();
		
		$is_post = empty($this->layer_values);

		$this->import_google_fonts();
		$this->register_google_fonts();

		$li_classes = ['filterall', 'eg-' . $this->handle . '-wrapper'];
		$li_data = ['skin' => $this->handle];
		$media_wrapper_data = [];
		$cover_group_data = [];

		$cover_classes = ['esg-overlay', 'eg-' . $this->handle . '-container'];
		$cover_data = [];

		if (!empty($this->filter)) {
			foreach ($this->filter as $filter) {
				$li_classes[] = 'filter-' . $filter['slug'];
			}
		}
		if ($demo !== false && $demo !== 'preview') {
			// add favorite filter if we are in a demo
			if (isset($this->settings['favorite']) && $this->settings['favorite'])
				$li_classes[] = 'filter-favorite';
			if ($demo == 'skinchoose' && $choosen_skin == $this->id || $choosen_skin == '-1')
				$li_classes[] = 'filter-selectedskin';
		}

		if ($demo === false || $demo === 'preview' || $demo === 'custom') {
			if (!empty($this->sorting)) {
				foreach ($this->sorting as $handle => $value) {
					if (empty($handle) || empty($value)) continue;
					$sorting_content = is_numeric($value) ? $value : sanitize_title($value);
					$li_data[$handle] = $sorting_content;
				}
			}
		}

		$post_id = $base->getVar($this->post, 'ID');

		$li_classes[] = ($is_post) ? 'eg-post-id-' . $post_id : 'eg-post-id-' . $this->item_counter;

		if ($is_post) {
			$li_id = 'eg-' . $this->grid_id . '-post-id-' . $post_id . '_' . wp_rand(0, 10000);
		} else {
			$li_id = 'eg-' . $this->grid_id . '-post-id-' . $this->item_counter . '_' . wp_rand(0, 10000);
		}

		$grid_ids = $this->grid_id;
		$post_ids = $is_post ? $post_id : $this->item_counter;

		$this->item_counter++;

		//check for custom meta layout settings
		$meta_cover_bg_color = $this->get_meta_layout_change('cover-bg-color');
		$meta_item_bg_color = $this->get_meta_layout_change('item-bg-color');
		$meta_content_bg_color = $this->get_meta_layout_change('content-bg-color');

		/* 2.1.6 */
		if ($demo === false || $demo === 'preview') {

			if ($is_post) {

				$meta216 = $m->get_meta_value_by_handle($post_id, 'eg_custom_meta_216');

				// if post has not been modified since 2.1.6 update, legacy values still exist and need to be converted
				if ($meta216 != 'true') {

					$container_background_color = $base->getVar($this->params, 'container-background-color', '#000000');
					$meta_cover_bg_opacity = $this->get_meta_layout_change('cover-bg-opacity');
					$color_processed = ESGColorpicker::process($container_background_color);

					if (!empty($color_processed) && is_array($color_processed) && count($color_processed) > 1) {

						$color_type = $color_processed[1];
						if ($meta_cover_bg_color === false && $meta_cover_bg_opacity !== false) {

							if ($color_type === 'rgb' || $color_type === 'rgba') {
								$rgb_values = ESGColorpicker::rgbValues($container_background_color, 3);
								if (!empty($rgb_values) && is_array($rgb_values) && count($rgb_values) > 2) {
									$meta_cover_bg_opacity = intval($meta_cover_bg_opacity) / 100;
									$meta_cover_bg_color = 'rgba(' . $rgb_values[0] . ', ' . $rgb_values[1] . ', ' . $rgb_values[2] . ', ' . $meta_cover_bg_opacity . ')';
								}
							} else if ($color_type === 'hex') {
								$meta_cover_bg_color = ESGColorpicker::convert($container_background_color, $meta_cover_bg_opacity);
							}
						} else if ($meta_cover_bg_color !== false && $meta_cover_bg_opacity === false) {

							if ($color_type === 'rgb' || $color_type === 'rgba') {
								$rgb_values = ESGColorpicker::rgbValues($container_background_color, 4);
								if (!empty($rgb_values) && is_array($rgb_values) && count($rgb_values) > 3) {
									$meta_cover_bg_color = ESGColorpicker::processRgba($meta_cover_bg_color, $rgb_values[3]);
								}
							}
						} else if ($meta_cover_bg_color !== false && $meta_cover_bg_opacity !== false) {
							$meta_cover_bg_color = ESGColorpicker::convert($meta_cover_bg_color, $meta_cover_bg_opacity);
						}
					}
				}
			}
		}

		if ($meta_cover_bg_color !== false) $meta_cover_bg_color = ESGColorpicker::get($meta_cover_bg_color);
		if ($meta_content_bg_color !== false) $meta_content_bg_color = ESGColorpicker::get($meta_content_bg_color);
		if ($meta_item_bg_color !== false) $meta_item_bg_color = ESGColorpicker::get($meta_item_bg_color);

		$meta_content_style = '';
		if ($meta_content_bg_color !== false) {
			$meta_content_style = ' style="background: ' . esc_attr($meta_content_bg_color) . ';"';
		}

		$meta_item_style = '';
		if ($meta_item_bg_color !== false) {
			$meta_item_style = ' style="background: ' . esc_attr($meta_item_bg_color) . ';"';
		}

		$cover_type = $base->getVar($this->params, 'cover-type', 'full');

		$cover_animation_top = '';
		$cover_animation_delay_top = '';
		$cover_animation_center = '';
		$cover_animation_delay_center = '';
		$cover_animation_bottom = '';
		$cover_animation_delay_bottom = '';

		$cover_animation_duration_top = '';
		$cover_animation_duration_center = '';
		$cover_animation_duration_bottom = '';

		$cover_animation_color_top = '';
		$cover_animation_color_center = '';
		$cover_animation_color_bottom = '';

		$cover_wrapper_overflow = '';

		/* 2.1.6 */
		$force_key = wp_is_mobile() ? 'cover-always-visible-mobile' : 'cover-always-visible-desktop';
		$force_show_cover = $base->getVar($this->params, $force_key);

		if ($cover_type == 'full') { 
			//cover is for overlay container

			/* 2.1.6 */
			if (empty($force_show_cover) || $force_show_cover === 'false') {
				$cover_animation_center = 'esg-' . esc_attr($base->getVar($this->params, 'cover-animation-center', 'fade') . $base->getVar($this->params, 'cover-animation-center-type'));

				/* 2.2.6 */
				if (preg_match('/spiral|circle/', $cover_animation_center)) $cover_wrapper_overflow = ' esg-cover-overflow';
				if (preg_match('/line|spiral|circle/', $cover_animation_center)) $cover_animation_color_center = $base->getVar($this->params, 'cover-animation-color-center', '#FFFFFF');
			} else {
				$cover_animation_center = 'esg-none';
			}

			if ($cover_animation_center != 'esg-none' && $cover_animation_center != ' esg-noneout') {
				$cover_animation_delay_center = round($base->getVar($this->params, 'cover-animation-delay-center', 0, 'i') / 100, 2);
				$cover_animation_duration_center = $base->getVar($this->params, 'cover-animation-duration-center', 'default');
			}

		} else {

			/* 2.1.6 */
			if (empty($force_show_cover) || $force_show_cover === 'false') {
				$cover_animation_top = 'esg-' . esc_attr($base->getVar($this->params, 'cover-animation-top', 'fade') . $base->getVar($this->params, 'cover-animation-top-type'));
				$cover_animation_center = 'esg-' . esc_attr($base->getVar($this->params, 'cover-animation-center', 'fade') . $base->getVar($this->params, 'cover-animation-center-type'));
				$cover_animation_bottom = 'esg-' . esc_attr($base->getVar($this->params, 'cover-animation-bottom', 'fade') . $base->getVar($this->params, 'cover-animation-bottom-type'));

				/* 2.2.6 */
				if (preg_match('/spiral|circle/', $cover_animation_top) || preg_match('/spiral|circle/', $cover_animation_center) || preg_match('/spiral|circle/', $cover_animation_bottom)) {
					$cover_wrapper_overflow = ' esg-cover-overflow';
				}

				if (preg_match('/line|spiral|circle/', $cover_animation_top)) $cover_animation_color_top = $base->getVar($this->params, 'cover-animation-color-top', '#FFFFFF');
				if (preg_match('/line|spiral|circle/', $cover_animation_center)) $cover_animation_color_center = $base->getVar($this->params, 'cover-animation-color-center', '#FFFFFF');
				if (preg_match('/line|spiral|circle/', $cover_animation_bottom)) $cover_animation_color_bottom = $base->getVar($this->params, 'cover-animation-color-bottom', '#FFFFFF');

			} else {
				$cover_animation_top = 'esg-none';
				$cover_animation_center = 'esg-none';
				$cover_animation_bottom = 'esg-none';
			}

			if ($cover_animation_top != 'esg-none' && $cover_animation_top != ' esg-noneout') {
				$cover_animation_delay_top = round($base->getVar($this->params, 'cover-animation-delay-top', 0, 'i') / 100, 2);
				$cover_animation_duration_top = $base->getVar($this->params, 'cover-animation-duration-top', 'default');
			}

			if ($cover_animation_center != 'esg-none' && $cover_animation_center != ' esg-noneout') {
				$cover_animation_delay_center = round($base->getVar($this->params, 'cover-animation-delay-center', 0, 'i') / 100, 2);
				$cover_animation_duration_center = $base->getVar($this->params, 'cover-animation-duration-center', 'default');
			}

			if ($cover_animation_bottom != 'esg-none' && $cover_animation_bottom != ' esg-noneout') {
				$cover_animation_delay_bottom = round($base->getVar($this->params, 'cover-animation-delay-bottom', 0, 'i') / 100, 2);
				$cover_animation_duration_bottom = $base->getVar($this->params, 'cover-animation-duration-bottom', 'default');
			}

		}

		// 2.2.5
		if ($cover_animation_top) {
			$data_transition_top = $cover_animation_top;
			$cover_animation_top = ' esg-transition';
		} else {
			$data_transition_top = '';
		}

		if ($cover_animation_center) {
			$data_transition_center = $cover_animation_center;
			$cover_animation_center = ' esg-transition';
		} else {
			$data_transition_center = '';
		}

		if ($cover_animation_bottom) {
			$data_transition_bottom = $cover_animation_bottom;
			$cover_animation_bottom = ' esg-transition';
		} else {
			$data_transition_bottom = '';
		}

		//  2.1.6
		// the following moved up a bit in the function, so we can do more things in the foreach loop
		$c_layer = 0;
		$t_layer = 0;
		$b_layer = 0;
		$m_layer = 0;

		/* 2.1.6 */
		$visible_prop = wp_is_mobile() ? 'always-visible-mobile' : 'always-visible-desktop';
		$disable_group_animation = false;

		if (!empty($this->layers)) {
			foreach ($this->layers as $key => $layer) {
				if (isset($layer['container'])) {
					if (!isset($layer['settings']['position']) || $layer['settings']['position'] !== 'absolute') {
						switch ($layer['container']) {
							case 'c':
								$c_layer++;
								break;
							case 'tl':
								$t_layer++;
								break;
							case 'br':
								$b_layer++;
								break;
							case 'm':
								$m_layer++;
								break;
						}
					}
				}

				/* 2.1.6 */
				if (!empty($layer['settings']) && !empty($layer['settings'][$visible_prop]) && $layer['settings'][$visible_prop] == 'true') {
					$layer['settings']['transition'] = 'none';
					$this->layers[$key] = $layer;
					$disable_group_animation = true;
				}

			}
		}

		//group is for cover container

		/* 2.1.6 */
		if (empty($disable_group_animation)) {
			$cover_group_animation = 'esg-' . esc_attr($base->getVar($this->params, 'cover-group-animation', 'fade'));
		} else {
			$cover_group_animation = 'esg-none';
		}

		if ($cover_group_animation != 'esg-none') {
			$cover_group_data['delay'] = round($base->getVar($this->params, 'cover-group-animation-delay', 0, 'i') / 100, 2);
			$cover_group_data['duration'] = $base->getVar($this->params, 'cover-group-animation-duration', 'default');
		} else {
			$cover_group_animation = '';
		}

		// 2.2.5
		if ($cover_group_animation) {
			$cover_group_data['transition'] = $cover_group_animation;
			$cover_group_animation = ' esg-transition';
		}

		//media is for media container
		$media_animation = 'esg-' . esc_attr($base->getVar($this->params, 'media-animation', 'fade'));
		
		if (strpos($media_animation, 'blur') !== false) {
			$media_wrapper_data['bluramount'] = $base->getVar($this->params, 'media-animation-blur', '5');
		}

		if ($media_animation != 'esg-none') {
			$media_wrapper_data['delay'] = round($base->getVar($this->params, 'media-animation-delay', 0, 'i') / 100, 2);
			$media_wrapper_data['duration'] = $base->getVar($this->params, 'media-animation-duration', 'default');
		} else {
			$media_animation = '';
		}

		if ($this->load_more_element) $li_classes[] = 'eg-newli';

		/* 2.1.6 Split Item Option */
		$splitItem = $base->getVar($this->params, 'splitted-item', 'none');
		if (!empty($splitItem) && $splitItem !== 'none') {
			$li_classes[] = 'esg-split-content';
			$li_classes[] = 'esg-split-' . $splitItem;
		}

		//check if we are on cobble, if yes, get the data of entry for cobbles
		if ($this->grid_type == 'cobbles') {

			if ($this->layer_values === false) { 
				//we are on post
				$cobbles = json_decode(get_post_meta($this->post['ID'], 'eg_cobbles', true), true);
				if (isset($cobbles[$this->grid_id]['cobbles']) && strpos($cobbles[$this->grid_id]['cobbles'], ':') !== false)
					$use_cobbles = $cobbles[$this->grid_id]['cobbles'];
				else
					$use_cobbles = '1:1';
			} else {
				//get the info from $this->layer_values
				$use_cobbles = $base->getVar($this->layer_values, 'cobbles-size', '1:1');
			}
			$use_cobbles = explode(':', $use_cobbles);
			
			$li_data['cobblesw'] = $use_cobbles[0];
			$li_data['cobblesh'] = $use_cobbles[1];
		}

		// 2.1.6.2 itm hover animation
		$itm_anime = $this->grid_item_animation;
		if ($itm_anime !== 'none') {
			$li_data['anime'] = 'esg-item-' . $itm_anime;
			switch ($itm_anime) {
				case 'zoomin':
					$li_data['anime-zoomin'] = $this->grid_item_animation_zoomin;
					break;
				case 'zoomout':
					$li_data['anime-zoomout'] = $this->grid_item_animation_zoomout;
					break;
				case 'fade':
					$li_data['anime-fade'] = $this->grid_item_animation_fade;
					break;
				case 'blur':
					$li_data['anime-blur'] = $this->grid_item_animation_blur;
					break;
				case 'shift':
					$li_data['anime-shift'] = $this->grid_item_animation_shift;
					$li_data['anime-shift-amount'] = $this->grid_item_animation_shift_amount;
					break;
				case 'rotate':
					$li_data['anime-rotate'] = $this->grid_item_animation_rotate;
					break;
			}
		}

		// 2.1.6.2 itm hover animation
		$itm_anime_other = $this->grid_item_animation_other;
		if ($itm_anime_other !== 'none') {
			$li_data['anime-other'] = 'esg-item-' . $itm_anime_other;
			switch ($itm_anime_other) {
				case 'zoomin':
					$li_data['anime-other-zoomin'] = $this->grid_item_other_zoomin;
					break;
				case 'zoomout':
					$li_data['anime-other-zoomout'] = $this->grid_item_other_zoomout;
					break;
				case 'fade':
					$li_data['anime-other-fade'] = $this->grid_item_other_fade;
					break;
				case 'blur':
					$li_data['anime-other-blur'] = $this->grid_item_other_blur;
					break;
				case 'shift':
					$li_data['anime-other-shift'] = $this->grid_item_other_shift;
					$li_data['anime-other-shift-amount'] = $this->grid_item_other_shift_amount;
					break;
				case 'rotate':
					$li_data['anime-other-rotate'] = $this->grid_item_other_rotate;
					break;
			}
		}

		if ($demo === 'skinchoose') $li_classes[] = 'eg-tooltip-wrap';
		if ($demo === 'custom') $li_classes[] = 'eg-newli';

		echo '<li'
		     . ' id="' . esc_attr($li_id) . '"'
		     . ( $demo === 'skinchoose' ? ' title="' . esc_attr__('Select Skin', 'essential-grid') . '"' : '' )
		     . ' class="' . implode(' ', array_map('esc_attr', $li_classes)) . '"'
		     // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sniffer seems to raise false positive here
		     . ' ' . implode(' ', array_map(function($n, $m) { return 'data-' . sanitize_key($n) . '="'.esc_js($m).'"'; }, array_keys($li_data), $li_data))
		     . ' ' . $meta_item_style
		     . ">\n";
		
		if ($demo == 'overview' || $demo == 'skinchoose') {
			//check if fav or not

			$cl = ($demo == 'skinchoose') ? 'esg-screenselect-toolbar' : 'esg-skineditor-toolbar'; //show only in grid editor at skin chooser

			echo '<div class="' . esc_attr($cl) . '">' . "\n";
			echo '          <div class="btn-wrap-item-skin-overview-' . esc_attr($this->id) . '">' . "\n";
			echo '<div class="eg-item-skin-overview-name">' . esc_html($this->name) . "</div>\n";

			if ($demo == 'overview') {
				$fav_class = (!isset($this->settings['favorite']) || !$this->settings['favorite']) ? 'eg-icon-star-empty' : 'eg-icon-star';

				echo '<a href="javascript:void(0);" title="' . esc_attr__('Mark as Favorite', 'essential-grid') . '" class="eg-ov-1 eg-overview-button eg-btn-star-item-skin esg-purple eg-tooltip-wrap" id="eg-star-' . esc_attr($this->id) . '"><i class="' . esc_attr($fav_class) . '"></i></a>';
				echo '<a href="' . esc_url(Essential_Grid_Base::getViewUrl(Essential_Grid_Admin::VIEW_ITEM_SKIN_EDITOR, 'create=' . $this->id)) . '" title="' . esc_attr__('Edit Skin', 'essential-grid') . '" class="eg-tooltip-wrap eg-ov-2 eg-overview-button esg-green "><i class="eg-icon-droplet"></i></a>';
				echo '<a href="javascript:void(0);" title="' . esc_attr__('Duplicate Skin', 'essential-grid') . '" class="eg-ov-3 eg-overview-button eg-btn-duplicate-item-skin esg-blue eg-tooltip-wrap " id="eg-duplicate-' . esc_attr($this->id) . '"><i class="eg-icon-picture"></i></a>';
				echo '<a href="javascript:void(0);" title="' . esc_attr__('Delete Skin', 'essential-grid') . '" class="eg-ov-4 eg-overview-button eg-btn-delete-item-skin esg-red eg-tooltip-wrap " id="eg-delete-' . esc_attr($this->id) . '"><i class="eg-icon-trash"></i></a>';
			} elseif ($demo == 'skinchoose') {
				echo '<a href="' . esc_url('admin.php?page=' . ESG_PLUGIN_SLUG . '&view=grid-item-skin-editor&create=' . $this->id) . '" class="eg-edit-skin-button eg-overview-button eg-tooltip-wrap" target="_esg_edit_skin" title="' . esc_attr__('Edit Skin', 'essential-grid') . '"><i class="eg-icon-tint"></i></a>';
				echo '<input class="eg-tooltip-wrap eg-tooltip-wrap-position " type="radio" value="' . esc_attr($this->id) . '" title="' . esc_attr__('Choose Skin', 'essential-grid') . '" name="entry-skin"';
				if ($choosen_skin == '-1')
					echo ' checked="checked"';
				else
					checked($choosen_skin, $this->id); //echo checked if it is current ID
				echo ' />';
			}
			echo '          </div>' . "\n";
			echo '          <div class="clear"></div>' . "\n\n";
			echo '       </div>' . "\n\n";
		} elseif ($demo == 'preview') {
			$this->post['ID'] = $this->post['ID'] ?? "";
			$is_visible = $grid->check_if_visible($this->post['ID'], $this->grid_id);
			$vis_icon = ($is_visible) ? 'eg-icon-eye' : 'eg-icon-eye-off';
			$vis_icon_color = ($is_visible) ? 'esg-blue' : 'esg-red';

			echo '<div class="esg-atoolbar">' . "\n";
			echo '          <div class="btn-wrap-item-skin-overview-' . esc_attr($this->post['ID']) . '">' . "\n";
			echo '<div class="eg-item-skin-overview-name">';
			echo '<a href="javascript:void(0);" class="eg-ov-2 eg-overview-button eg-btn-activate-post-item ' . esc_attr($vis_icon_color) . ' eg-tooltip-wrap" title="' . esc_attr__('Show/Hide from Grid', 'essential-grid') . '" id="eg-act-post-item-' . esc_attr($this->post['ID']) . '"><i class="' . esc_attr($vis_icon) . '"></i></a>';
			echo '<a href="' . esc_url(get_edit_post_link($this->post['ID'])). '" class="eg-ov-3 eg-overview-button esg-purple eg-tooltip-wrap" title="' . esc_attr__('Edit Post', 'essential-grid') . '" target="_blank"><i class="eg-icon-pencil-1"></i></a>';
			echo '<a href="javascript:void(0);" class="eg-ov-4 eg-overview-button eg-btn-edit-post-item esg-green eg-tooltip-wrap" title="' . esc_attr__('Edit Post Meta', 'essential-grid') . '" id="eg-edit-post-item-' . esc_attr($this->post['ID']) . '"><i class="eg-icon-cog"></i></a>';
			echo '</div>' . "\n";
			echo '          </div>' . "\n";
			echo '          <div class="clear"></div>' . "\n\n";
			echo '       </div>' . "\n\n";
		} elseif ($demo == 'custom') { 
			//add info of what items do exist in the layer that can be edited
			$custom_layer_elements = [];
			$custom_layer_data = [];
			if (!empty($this->layers)) {
				foreach ($this->layers as $layer) {
					if (isset($layer['settings']['source'])) {
						switch ($layer['settings']['source']) {
							case 'post':
								$custom_layer_elements[$layer['settings']['source-post']] = '';
								break;
							case 'woocommerce':
								$custom_layer_elements[$layer['settings']['source-woocommerce']] = '';
								break;
						}
					}
				}
			}

			if (!empty($this->layer_values))
				$custom_layer_data = $this->layer_values;

			$custom_layer_elements = wp_json_encode($custom_layer_elements);
			$custom_layer_data = wp_json_encode($custom_layer_data);

			echo '<input class="esg-items-datas" type="hidden" name="layers[]" value="' . esc_attr($custom_layer_data) . '" />'; //has the values for this entry
			echo '<div class="esg-data-handler esg-display-none" data-exists="' . esc_attr($custom_layer_elements) . '" ></div>'; //has the information on what exists as layers in the skin #3498DB

			echo '<div class="esg-atoolbar esg-atoolbar-transparent">' . "\n";
			echo '          <div class="btn-wrap-item-skin-overview-0">' . "\n";
			echo '<div class="eg-item-skin-overview-name">';

			echo '<div title="' . esc_attr__('Move', 'essential-grid') . '" class="eg-ov-10 eg-overview-button esg-purple eg-tooltip-wrap esg-cursor-move"><i class="eg-icon-menu"></i></div>';
			echo '<div title="' . esc_attr__('Move one before', 'essential-grid') . '" class="eg-ov-11 eg-overview-button eg-btn-move-before-custom-element esg-purple eg-tooltip-wrap "><i class="eg-icon-angle-left"></i></div>';
			echo '<div title="' . esc_attr__('Move one after', 'essential-grid') . '" class="eg-ov-12 eg-overview-button eg-btn-move-after-custom-element esg-purple eg-tooltip-wrap "><i class="eg-icon-angle-right"></i></div>';
			echo '<div title="' . esc_attr__('Move after #x', 'essential-grid') . '" class="eg-ov-13 eg-overview-button eg-btn-switch-custom-element esg-purple eg-tooltip-wrap "><i class="eg-icon-angle-double-right"></i></div>';

			echo '<div title="' . esc_attr__('Delete Element', 'essential-grid') . '" class="eg-ov-4 eg-overview-button eg-btn-delete-custom-element esg-red eg-tooltip-wrap "><i class="eg-icon-trash"></i></div>';
			echo '<div title="' . esc_attr__('Duplicate Element', 'essential-grid') . '" class="eg-ov-3 eg-overview-button eg-btn-duplicate-custom-element esg-blue eg-tooltip-wrap "><i class="eg-icon-picture"></i></div>';
			echo '<div title="' . esc_attr__('Edit Element', 'essential-grid') . '" class="eg-ov-2 eg-overview-button eg-btn-edit-custom-element esg-green eg-tooltip-wrap "><i class="eg-icon-cog"></i></div>';

			echo '</div>' . "\n";
			echo '          </div>' . "\n";
			echo '          <div class="clear"></div>' . "\n\n";
			echo '       </div>' . "\n\n";
		}

		$is_video = false;
		$is_iframe = false;
		$lightbox_thumb = false;
		$echo_media = '';
		$order = '';
		
		if ($demo === false || $demo == 'preview' || $demo == 'custom') {

			//only show if something is checked
			if (!empty($this->default_media_source_order)) {
				$result = $this->get_media_html($this->default_media_source_order);
				$is_video = $result['is_video'];
				$is_iframe = $result['is_iframe'];
				$lightbox_thumb = $result['lightbox_thumb'];
				$echo_media = $result['echo_media'];
				$order = $result['order'];
			}

			if ($echo_media == '') { 
				//set default image if one is set
				if ($this->default_image !== '') {
					$def_img_attr = $this->get_media_attributes(false);
					// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage 
					$echo_media = '<img src="' . esc_url($this->default_image) . '"' . $def_img_attr . ' data-no-lazy="1" />';
					$lightbox_thumb = esc_attr($this->default_image);
					$this->item_media_type = 'default-image';
				}
			} else {
				$this->item_media_type = $order;
			}
			
			//disable avada lazy load feature
			if (class_exists('Avada') && strpos($echo_media, '<img') !== false) {
				if (strpos($echo_media, 'class="') !== false) {
					$echo_media = str_replace('class="', 'class="disable-lazyload ', $echo_media);
				} else {
					$echo_media = str_replace('<img', '<img class="disable-lazyload" ', $echo_media);
				}
			}

			/* 2.1.6 new hover image */
			/* hover images just get lazy-loaded immediately after the main image loads (whenever that happens) */
			$hover_image = $base->getVar($this->params, 'element-hover-image');
			if (!empty($hover_image) && $hover_image !== 'false' && !empty($this->media_sources) && isset($this->media_sources['alternate-image']) && !empty($this->media_sources['alternate-image'])) {

				$hover_image = $this->media_sources['alternate-image'];
				$hover_image_animation = 'esg-' . esc_attr($base->getVar($this->params, 'hover-image-animation', 'fade'));

				if ($hover_image_animation != 'esg-none') {
					$hover_image_animation_delay = ' data-delay="' . esc_attr(round($base->getVar($this->params, 'hover-image-animation-delay', 0, 'i') / 100, 2)) . '"';
					$hover_image_animation_duration = ' data-duration="' . esc_attr($base->getVar($this->params, 'hover-image-animation-duration', 'default')) . '"';
				} else {
					$hover_image_animation = '';
					$hover_image_animation_delay = '';
					$hover_image_animation_duration = '';
				}

				// 2.2.5
				if ($hover_image_animation) {

					$data_transition_hover = ' data-transition="' . esc_attr($hover_image_animation) . '"';
					$hover_image_animation = ' esg-transition';

				} else {
					$data_transition_hover = '';
				}

				$echo_media .= '<div class="esg-hover-image' . $hover_image_animation . '" data-src="' . esc_attr($hover_image) . '"' . $hover_image_animation_delay . $hover_image_animation_duration . $data_transition_hover . '></div>';
			}

		}
		//set the lightbox thumbnail for the current item!
		$this->lightbox_thumbnail = $lightbox_thumb;

		//check if we have a full link
		$link_set_to = $base->getVar($this->params, 'link-set-to', 'none');

		$link_type_link = $base->getVar($this->params, 'link-link-type', 'none');
		
		$link_target = $base->getVar($this->params, 'link-target', '_self');
		if ($link_target !== 'disabled')
			$link_target = ' target="' . esc_attr($link_target) . '"';
		else
			$link_target = '';
		
		$link_rel_nofollow = $base->getVar($this->params, 'rel-nofollow', 'false');
		if ($link_rel_nofollow === 'true')
			$link_rel_nofollow = ' rel="nofollow"';
		else
			$link_rel_nofollow = '';

		$link_wrapper = '';

		if ($link_set_to !== 'none') {

			switch ($link_type_link) {
				case 'post':
					if ($demo === false) {
						if ($is_post) {
							$link_wrapper = '<a href="' . esc_url(get_permalink($this->post['ID'])) . '"' . $link_target . $link_rel_nofollow . '>%REPLACE%</a>';
						} else {
							//get the post link
							$get_link = $this->get_custom_element_value('post-link', ''); 
							if ($get_link == '') {
								$link_wrapper = '<a href="javascript:void(0);"' . $link_target . $link_rel_nofollow . '>%REPLACE%</a>';
							} else {
								$link_wrapper = '<a href="' . esc_url($this->normalize_link($get_link)) . '"' . $link_target . $link_rel_nofollow . '>%REPLACE%</a>';
							}
						}
					} else {
						$link_wrapper = '<a href="javascript:void(0);"' . $link_target . $link_rel_nofollow . '>%REPLACE%</a>';
					}
					break;
				case 'url':
					$lurl = $base->getVar($this->params, 'link-url-link', 'javascript:void(0);');
					$link_wrapper = '<a href="' . esc_url($this->normalize_link($lurl)) . '"' . $link_target . $link_rel_nofollow . '>%REPLACE%</a>';
					break;
				case 'meta':
					if ($demo === false) {
						$meta_key = $base->getVar($this->params, 'link-meta-link', 'javascript:void(0);');
						if ($is_post) {
							$meta_link = $m->get_meta_value_by_handle($this->post['ID'], $meta_key);
							if ($meta_link == '') {
								// if empty, link to nothing
								$link_wrapper = '<a href="javascript:void(0);"' . $link_target . $link_rel_nofollow . '>%REPLACE%</a>';
							} else {
								$link_wrapper = '<a href="' . esc_attr($this->normalize_link($meta_link)) . '"' . $link_target . $link_rel_nofollow . '>%REPLACE%</a>';
							}
						} else {
							$get_link = $this->get_custom_element_value($meta_key, ''); //get the post link
							if ($get_link == '') {
								$link_wrapper = '<a href="javascript:void(0);"' . $link_target . $link_rel_nofollow . '>%REPLACE%</a>';
							} else {
								$link_wrapper = '<a href="' . esc_attr($this->normalize_link($get_link)) . '"' . $link_target . $link_rel_nofollow . '>%REPLACE%</a>';
							}
						}
					} else {
						$link_wrapper = '<a href="javascript:void(0);"' . $link_target . $link_rel_nofollow . '>%REPLACE%</a>';
					}
					break;
				case 'javascript':
					$js_link = esc_attr($base->getVar($this->params, 'link-javascript-link', 'void(0);'));
					$link_wrapper = '<a href="javascript:' . esc_attr($js_link) . '"' . $link_target . $link_rel_nofollow . '>%REPLACE%</a>';
					break;
				case 'lightbox':
					$opt = get_option('tp_eg_use_lightbox', 'false');
					if ($opt !== 'disabled') { 
						//enqueue only if default LightBox is selected
						wp_enqueue_script('esg-tp-boxext');
						wp_enqueue_style('esg-tp-boxextcss');
					}

					$lb_get_lightbox = ''; // media url to pass to get_lightbox_* functions if img from content
					$lb_source = 'javascript:void(0);';
					$lb_owidth = '';
					$lb_oheight = '';
					$lb_class = '';
					$lb_addition = '';
					$lb_content = '';
					$lb_data = '';
					$lb_featured = '';
					$lb_post_title = '';
					$lb_rel = ($this->lb_rel !== false) ? ' data-esgbox="' . esc_attr($this->lb_rel) . '"' : '';

					if (!empty($this->default_lightbox_source_order)) { 
						//only show if something is checked
						foreach ($this->default_lightbox_source_order as $order) {
							//go through the order and set media as wished

							$val = isset($this->media_sources[$order]) && $this->media_sources[$order] !== '' && $this->media_sources[$order] !== false;
							if ($order === 'post-content' || !empty($val)) {
								//found entry

								$do_continue = false;
								if (!empty($this->lightbox_additions['items']) && $this->lightbox_additions['base'] == 'on') {
									$lb_get_lightbox = $lb_source = $this->lightbox_additions['items'][0];
									$lb_class = ' esgbox';
								} else {

									switch ($order) {

										case 'featured-image':
										case 'alternate-image':
										case 'content-image':

											// 2.2.5
											$imgsource = explode('-', $order);
											$imgsource = $imgsource[0];

											if ($order == 'content-image') $lb_source = $this->media_sources[$order];
											else $lb_source = $this->media_sources[$order . '-full'];

											//update lightbox thumb
											$this->lightbox_thumbnail = $lb_source;
											
											$lb_class = ' esgbox';

											if (isset($this->media_sources[$imgsource . '-image-full-width'])) $lb_owidth = ' data-width="' . esc_attr($this->media_sources[$imgsource . '-image-full-width']) . '" ';
											if (isset($this->media_sources[$imgsource . '-image-full-height'])) $lb_oheight = ' data-height="' . esc_attr($this->media_sources[$imgsource . '-image-full-height']) . '" ';

											break;

										case 'youtube':
											$http = (is_ssl()) ? 'https' : 'http';
											$enable_youtube_nocookie = get_option('tp_eg_enable_youtube_nocookie', 'false');
											$lb_source = $enable_youtube_nocookie != 'false' ? $http . '://www.youtube-nocookie.com/embed/' . $this->media_sources[$order] : $http . '://www.youtube.com/watch?v=' . $this->media_sources[$order];
											$lb_addition = ($this->video_ratios['youtube'] == '1') ? '' : ' data-ratio="4:3"';
											$lb_class = ' esgbox';
											break;
										case 'vimeo':
											$http = (is_ssl()) ? 'https' : 'http';
											$lb_source = $http . '://vimeo.com/' . $this->media_sources[$order];
											$lb_addition = ($this->video_ratios['vimeo'] == '1') ? '' : ' data-ratio="4:3"';
											$lb_class = ' esgbox';
											break;
										case 'wistia':
											$lb_source = '//fast.wistia.net/embed/iframe/' . $this->media_sources[$order];
											$lb_class = ' esgbox';
											$lb_data .= ' data-type="iframe"';
											$lb_addition = ($this->video_ratios['wistia'] == '1') ? '' : ' data-ratio="4:3"';
											break;
										case 'soundcloud':
											$lb_source = '//w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/' . $this->media_sources[$order] . '&amp;color=%23ff5500&amp;auto_play=true&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;show_teaser=true&amp;visual=true';
											$lb_class = ' esgbox';
											$lb_data .= ' data-type="iframe"';
											break;
										case 'iframe':
											$lb_source = addslashes($this->media_sources[$order]);
											$lb_class = ' esgbox';
											$lb_data .= ' data-type="iframe"';
											break;
										case 'html5':
											if (trim($this->media_sources[$order]['mp4']) === '' && trim($this->media_sources[$order]['ogv']) === '' && trim($this->media_sources[$order]['webm'] === '')) {
												$do_continue = true;
											} else {
												$lb_mp4 = $this->media_sources[$order]['mp4'];
												$lb_ogv = $this->media_sources[$order]['ogv'];
												$lb_webm = $this->media_sources[$order]['webm'];
												$lb_source = "";
												if (!empty($lb_mp4)) {
													$lb_source = $lb_mp4;
												} elseif (!empty($lb_ogv)) {
													$lb_source = $lb_ogv;
												} elseif (!empty($lb_webm)) {
													$lb_source = $lb_webm;
												}
												$lb_class = ' esgbox esgboxhtml5';
												$vid_ratio = ($this->video_ratios['html5'] == '1') ? '' : ' data-ratio="4:3"';

												$lb_addition = ' data-mp4="' . esc_attr($lb_mp4) . '" data-ogv="' . esc_attr($lb_ogv) . '" data-webm="' . esc_attr($lb_webm) . '"' . $vid_ratio;
												if ($lightbox_thumb !== false) {
													// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage 
													$lb_content = '<img class="esg-display-none" src="' . esc_url($lightbox_thumb) . '" />';
												}
											}
											break;

										case 'post-content':
											$lb_source = 'javascript:void(0);';
											$lb_class = ' esgbox esgbox-post';
											$lb_data = ' data-post="' . esc_attr($post_ids) . '"';
											$lb_data .= ' data-gridid="' . esc_attr($grid_ids) . '" data-ispost="' . esc_attr($is_post) . '"';
											$separator = '';

											$lb_post_title = $is_post ? $base->getVar($this->post, 'post_title') : $this->get_custom_element_value('title', $separator);
											$lb_post_title = ' data-posttitle="' . esc_attr($lb_post_title) . '"';

											// if featured full is available
											if (isset($this->media_sources['featured-image-full']) && !empty($this->media_sources['featured-image-full'])) {
												$lb_featured = ' data-featured="' . esc_attr($this->media_sources['featured-image-full']) . '"';
											} // if featured regular size is available
											else if (isset($this->media_sources['featured-image']) && !empty($this->media_sources['featured-image'])) {
												$lb_featured = ' data-featured="' . esc_attr($this->media_sources['featured-image']) . '"';
											} // if global image is available
											else if (!empty($this->default_image)) {
												$lb_featured = ' data-featured="' . esc_attr($this->default_image) . '"';
											}
											break;

										case 'revslider':
											$lb_source = admin_url('admin-ajax.php');
											$lb_class = ' esgbox esgbox-post';
											$lb_data = ' data-post="' . esc_attr($post_ids) . '" data-revslider="' . esc_attr($this->media_sources[$order]) . '"';
											$lb_data .= ' data-gridid="' . esc_attr($grid_ids) . '" data-ispost="' . esc_attr($is_post) . '"';
											break;

										case 'essgrid':
											$lb_source = admin_url('admin-ajax.php');
											$lb_class = ' esgbox esgbox-post';
											$lb_data = ' data-post="' . esc_attr($post_ids) . '" data-lbesg="' . esc_attr($this->media_sources[$order]) . '"';
											$lb_data .= ' data-gridid="' . esc_attr($grid_ids) . '" data-ispost="' . esc_attr($is_post) . '"';
											break;
											
									}
								}
								if ($do_continue) {
									continue;
								}
								break;
							}

							/* 2.1.6 */
							if ($order === 'featured-image') {
								$default_img = $this->default_image;
								if (!empty($default_img)) {
									$lb_source = $default_img;
									$lb_class = ' esgbox';
									$lb_owidth = ' data-width="' . esc_attr($this->default_image_attr[0]) . '" ';
									$lb_oheight = ' data-height="' . esc_attr($this->default_image_attr[1]) . '" ';
									break;
								}
							}
						}
					}

					$lb_caption = $this->get_lightbox_caption($demo, $is_post, $lb_get_lightbox);
					$link_wrapper = '<a class="' . $lb_class . '"' . $lb_addition . ' href="' . esc_attr($lb_source) . '" data-elementor-open-lightbox="yes" data-thumb="' . esc_attr(esg_aq_resize($this->lightbox_thumbnail, 200)) . '" ' . $lb_caption . $lb_owidth . $lb_oheight . $lb_rel . $lb_data . $lb_featured . $lb_post_title . '>' . $lb_content . '%REPLACE%</a>';

					$this->load_lightbox = true; //set that jQuery is written
					break;
				case 'ajax':
					$ajax_class = '';
					if (!empty($this->default_ajax_source_order)) { 
						//only show if something is checked
						$ajax_class = ' eg-ajaxclicklistener';
						foreach ($this->default_ajax_source_order as $order) { 
							//go through the order and set media as wished
							$do_continue = false;
							if (isset($this->media_sources[$order]) && $this->media_sources[$order] !== '' && $this->media_sources[$order] !== false || $order == 'post-content') { //found entry
								switch ($order) {
									case 'youtube':
										$vid_ratio = ($this->video_ratios['youtube'] == '0') ? '4:3' : '16:9';
										$ajax_attr = ' data-ajaxtype="youtubeid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
										$ajax_attr .= ' data-ajaxsource="' . esc_attr($this->media_sources[$order]) . '"'; //depending on type
										$ajax_attr .= ' data-ajaxvideoaspect="' . $vid_ratio . '"'; //depending on type
										break;
									case 'vimeo':
										$vid_ratio = ($this->video_ratios['vimeo'] == '0') ? '4:3' : '16:9';
										$ajax_attr = ' data-ajaxtype="vimeoid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
										$ajax_attr .= ' data-ajaxsource="' . esc_attr($this->media_sources[$order]) . '"'; //depending on type
										$ajax_attr .= ' data-ajaxvideoaspect="' . $vid_ratio . '"'; //depending on type
										break;
									case 'wistia':
										$vid_ratio = ($this->video_ratios['wistia'] == '0') ? '4:3' : '16:9';
										$ajax_attr = ' data-ajaxtype="wistiaid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
										$ajax_attr .= ' data-ajaxsource="' . esc_attr($this->media_sources[$order]) . '"'; //depending on type
										$ajax_attr .= ' data-ajaxvideoaspect="' . $vid_ratio . '"'; //depending on type
										break;
									case 'html5':
										if ($this->media_sources[$order]['mp4'] == ''
											&& $this->media_sources[$order]['webm'] == ''
											&& $this->media_sources[$order]['ogv'] == '') {
											$do_continue = true;
										} else {
											//mp4/webm/ogv
											$vid_ratio = ($this->video_ratios['html5'] == '0') ? '4:3' : '16:9';
											$ajax_attr = ' data-ajaxtype="html5vid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
											$ajax_attr .= ' data-ajaxsource="';
											$ajax_attr .= esc_attr(@$this->media_sources[$order]['mp4']) . '|';
											$ajax_attr .= esc_attr(@$this->media_sources[$order]['webm']) . '|';
											$ajax_attr .= esc_attr(@$this->media_sources[$order]['ogv']);
											$ajax_attr .= '"';
											$ajax_attr .= ' data-ajaxvideoaspect="' . $vid_ratio . '"'; //depending on type
										}
										break;
									case 'soundcloud':
										$ajax_attr = ' data-ajaxtype="soundcloudid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
										$ajax_attr .= ' data-ajaxsource="' . esc_attr($this->media_sources[$order]) . '"'; //depending on type
										break;
									case 'post-content':
										if ($is_post) {
											$ajax_attr = ' data-ajaxtype="postid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
											$ajax_attr .= ' data-ajaxsource="' . esc_attr(@$this->post['ID']) . '"'; //depending on type
										} else {
											$do_continue = true;
											//$ajax_class = '';
										}
										break;
									case 'featured-image':
									case 'alternate-image':
									case 'content-image':
										if ($order == 'content-image')
											$img_url = $this->media_sources[$order];
										else
											$img_url = $this->media_sources[$order . '-full'];

										$ajax_attr = ' data-ajaxtype="imageurl"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
										$ajax_attr .= ' data-ajaxsource="' . esc_attr($img_url) . '"'; //depending on type
										break;
									default:
										$ajax_class = '';
										$do_continue = true;
										break;
								}
							} else {
								//some custom entry maybe
								$postobj = ($is_post) ? $this->post : false;

								$ajax_attr = apply_filters('essgrid_handle_ajax_content', $order, $this->media_sources, $postobj, $this->grid_id);
								if (empty($ajax_attr)) {
									$do_continue = true;
								}

							}
							if ($do_continue) {
								continue;
							}
							break;
						}
					}

					if ($ajax_class !== '') { 
						//set ajax loading to true so that the grid can decide to put ajax container in top/bottom
						$link_wrapper = '<a href="javascript:void(0);" ' . $ajax_attr . '>%REPLACE%</a>';
						$this->ajax_loading = true;
					}

					break;
			}
		}

		if ($m_layer > 0) {
			$show_content = $base->getVar($this->params, 'show-content', 'bottom');

			if ($show_content == 'top') {
				self::insert_masonry_layer($demo, $meta_content_style, $is_video, $grid_ids, $post_ids);
			}
		}

		//disable animation if we fill in iFrame
		if ($is_iframe) $media_animation = '';

		if (!empty($hover_image) && $hover_image !== 'false')
			$media_animation = '';

		// 2.2.5
		if ($media_animation) {
			$media_wrapper_data['transition'] = $media_animation;
			$media_animation = ' esg-transition';
		}

		echo '    <div class="esg-media-cover-wrapper' . esc_attr($cover_wrapper_overflow) . '">' . "\n";

		if (strpos($echo_media, '<img class="') !== false) {
			$echo_media = str_replace('<img class="', '<img class="esg-entry-media-img ', $echo_media);
		} else {
			$echo_media = str_replace('<img', '<img class="esg-entry-media-img" ', $echo_media);
		}

		$media_wrapper = '<div class="esg-entry-media' . esc_attr($media_animation) . '"'
		              // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sniffer seems to raise false positive here
		              . ' ' . implode(' ', array_map(function($n, $m) { return 'data-' . sanitize_key($n) . '="'.esc_js($m).'"'; }, array_keys($media_wrapper_data), $media_wrapper_data))
		              . '>%MEDIA%</div>' . "\n\n";
		
		if ($demo == 'overview' || $demo == 'skinchoose') {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage 
			echo str_replace('%MEDIA%', '<img class="esg-entry-media-img" src="' . esc_url(ESG_PLUGIN_URL . 'admin/assets/images/' . $this->cover_image) . '" data-no-lazy="1" alt="" />', $media_wrapper); 
		} else {
			$echo_media = str_replace('%MEDIA%', $echo_media, $media_wrapper);
			//echo media from top here
			if ($link_set_to == 'media' && $link_type_link !== 'none') { 
				//set link on whole media
				$echo_media = str_replace('%REPLACE%', $echo_media, $link_wrapper);
			}
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $echo_media;
		}

		//add absolute positioned elements here
		$link_inserted = false;

		if (!$is_iframe) {
			//if we are iFrame, no wrapper and no elements in media should be written
			if ($cover_type == 'full' && $c_layer > 0 || ($t_layer > 0 || $c_layer > 0 || $b_layer > 0)) {
				if ($link_set_to == 'cover' && $link_type_link !== 'none') $cover_group_data['clickable'] = 'on';

				echo '            <div class="esg-entry-cover' . esc_attr($cover_group_animation) . '"'
				     // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sniffer seems to raise false positive here
				     . ' ' . implode(' ', array_map(function($n, $m) { return 'data-' . sanitize_key($n) . '="'.esc_js($m).'"'; }, array_keys($cover_group_data), $cover_group_data))
				     . '>' . "\n\n";

				if ($link_set_to == 'cover' && $link_type_link !== 'none') {
					if (strpos($link_wrapper, 'class="') !== false) {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo str_replace(['%REPLACE%', 'class="'], [esc_html($this->get_item_title()), 'class="eg-invisiblebutton '], $link_wrapper);
					} else {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo str_replace(['%REPLACE%', '<a '], [esc_html($this->get_item_title()), '<a class="eg-invisiblebutton" '], $link_wrapper);
					}
					$link_inserted = true;
				}
			}

			if ( 'normal' !== $base->getVar($this->params, 'cover-blend-mode', 'normal') ) {
				$cover_classes[] = 'esg-cover-blend-' . $base->getVar($this->params, 'cover-blend-mode', 'normal');
			}
			
			if ($cover_type == 'full') {
				if (strpos($link_wrapper, 'class="') !== false) {
					$link_wrapper = str_replace('class="', 'class="eg-invisiblebutton ', $link_wrapper);
				} else {
					$link_wrapper = str_replace('<a ', '<a class="eg-invisiblebutton" ', $link_wrapper);
				}

				$cover_classes[] = $cover_animation_center;
				
				if (!empty($cover_animation_delay_center))    $cover_data['delay']      = $cover_animation_delay_center;
				if (!empty($cover_animation_duration_center)) $cover_data['duration']   = $cover_animation_duration_center;
				if (!empty($data_transition_center))          $cover_data['transition'] = $data_transition_center;
				if (!empty($cover_animation_color_center))    $cover_data['animcolor']  = $cover_animation_color_center;

				$this->_echo_cover($cover_classes, $cover_data, $meta_cover_bg_color, $link_set_to, $link_type_link, $link_inserted, $link_wrapper);
				
			} else {
				if ($t_layer > 0) {
					$cover_classes[] = 'esg-top';
					$cover_classes[] = $cover_animation_top;

					if (!empty($cover_animation_delay_top))    $cover_data['delay']      = $cover_animation_delay_top;
					if (!empty($cover_animation_duration_top)) $cover_data['duration']   = $cover_animation_duration_top;
					if (!empty($data_transition_top))          $cover_data['transition'] = $data_transition_top;
					if (!empty($cover_animation_color_top))    $cover_data['animcolor']  = $cover_animation_color_top;

					$this->_echo_cover($cover_classes, $cover_data, $meta_cover_bg_color, $link_set_to, $link_type_link, $link_inserted, $link_wrapper);
				}
				if ($c_layer > 0) {
					$cover_classes[] = 'esg-center';
					$cover_classes[] = $cover_animation_center;

					if (!empty($cover_animation_delay_center))    $cover_data['delay']      = $cover_animation_delay_center;
					if (!empty($cover_animation_duration_center)) $cover_data['duration']   = $cover_animation_duration_center;
					if (!empty($data_transition_center))          $cover_data['transition'] = $data_transition_center;
					if (!empty($cover_animation_color_center))    $cover_data['animcolor']  = $cover_animation_color_center;

					$this->_echo_cover($cover_classes, $cover_data, $meta_cover_bg_color, $link_set_to, $link_type_link, $link_inserted, $link_wrapper);
				}
				if ($b_layer > 0) {
					$cover_classes[] = 'esg-bottom';
					$cover_classes[] = $cover_animation_bottom;

					if (!empty($cover_animation_delay_bottom))    $cover_data['delay']      = $cover_animation_delay_bottom;
					if (!empty($cover_animation_duration_bottom)) $cover_data['duration']   = $cover_animation_duration_bottom;
					if (!empty($data_transition_bottom))          $cover_data['transition'] = $data_transition_bottom;
					if (!empty($cover_animation_color_bottom))    $cover_data['animcolor']  = $cover_animation_color_bottom;

					$this->_echo_cover($cover_classes, $cover_data, $meta_cover_bg_color, $link_set_to, $link_type_link, $link_inserted, $link_wrapper);
				}
			}

			/*
			<!-- #########################################################################
					THE CLASSES FOR THE ALIGNS OF ANY ELEMENT IS:

					 esg-top, esg-topleft, esg-topright,
					 esg-left, esg-right,  esg-center
					 esg-bottom, esg-bottomleft, esg-bottomright

					 IF YOU HAVE MORE THAN ONE ELEMENT IN THE SAME ALIGNED CONTAINER,
					 THEY WILL BE ADDED UNDER EACH OTHER IN THE SAME ALIGNED CONTAINER
			#########################################################################  -->
			*/

			if (!empty($this->layers)) {
				foreach ($this->layers as $layer) {  
					//add all but masonry elements
					if (!isset($layer['container']) || $layer['container'] == 'm') continue;
					$link_to = $base->getVar($layer, ['settings', 'link-type'], 'none');
					$hide_on_video = $base->getVar($layer, ['settings', 'hide-on-video'], 'false');

					if ($demo === false && $this->layer_values === false) { 
						//show element only if it is on sale or if featured
						if (Essential_Grid_Woocommerce::is_woo_exists()) {
							$show_on_sale = $base->getVar($layer, ['settings', 'show-on-sale'], 'false');
							if ($show_on_sale == 'true') {
								$sale = Essential_Grid_Woocommerce::check_if_on_sale($this->post['ID']);
								if (!$sale) continue;
							}
							$show_if_featured = $base->getVar($layer, ['settings', 'show-if-featured'], 'false');
							if ($show_if_featured == 'true') {
								$featured = Essential_Grid_Woocommerce::check_if_is_featured($this->post['ID']);
								if (!$featured) continue;
							}
						}
					}

					if ($link_to != 'embedded_video' && $hide_on_video == 'true' && $is_video) continue; //this element is hidden if media is video
					if ($link_to != 'embedded_video' && $hide_on_video == 'show' && !$is_video) continue; //this element is only shown if media is video

					if ($demo == 'overview' || $demo == 'skinchoose' || $demo == 'custom') {
						self::insert_layer($layer, $demo, false, $grid_ids, $post_ids);
					} else {
						self::insert_layer($layer, false, false, $grid_ids, $post_ids);
					}
				}

			}

			if ($this->load_lightbox === true) {
				if (!empty($this->lightbox_additions['items'])) {
					echo '<div class="esgbox-additional esg-display-none">';
					foreach ($this->lightbox_additions['items'] as $lb_key => $lb_img) {
						if ($this->lightbox_additions['base'] == 'on' && $lb_key == 0) continue; //if off, the first one is already written on the handle somewhere
						$thumg_src = (isset($this->lightbox_additions['thumbs'][$lb_key]) && !empty($this->lightbox_additions['thumbs'][$lb_key])) ? $this->lightbox_additions['thumbs'][$lb_key] : ESG_PLUGIN_URL . 'public/assets/images/300x200transparent.png';
						echo '<a href="' . esc_attr($lb_img) . '" class="esgbox" ' 
						     . 'data-thumb="' . esc_js($thumg_src) . '" ' 
						     . ($this->lb_rel !== false ? ' data-esgbox="' . esc_attr($this->lb_rel) . '"' : '')
						     . $this->get_lightbox_caption($demo, $is_post, $lb_img) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  -- esc_attr is applied in the function
						     . '>'
						     . '<img class="esg-lb-dummy" src="' . esc_url($thumg_src) . '" alt="" />' // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage 
						     . '</a>';
					}
					echo '</div>';
				}
			}

			if ($cover_type == 'full' && $c_layer > 0 || ($t_layer > 0 || $c_layer > 0 || $b_layer > 0)) {
				echo '           </div>' . "\n";
			}
		}

		if ($m_layer > 0) {
			if ($show_content == 'bottom') {
				self::insert_masonry_layer($demo, $meta_content_style, $is_video, $grid_ids, $post_ids);
			}
		}
		echo '   </div>' . "\n\n";
		echo '</li>' . "\n";
	}

	/**
	 * output the add more markup
	 */
	public function output_add_more() {
		// phpcs:disable 
		echo apply_filters('essgrid_output_add_more', '<li class="filterall eg-addnewitem-wrapper ui-state-disabled">
			<div class="esg-media-cover-wrapper">
				<div class="esg-entry-media"><img class="esg-entry-media-img" src="' . esc_url(ESG_PLUGIN_URL) . 'public/assets/images/300x200transparent.png" data-no-lazy="1" alt="" /></div>
				<div class="esg-entry-cover">
					<div class="esg-overlay esg-transition eg-addnewitem-container" data-transition="esg-fade" data-delay="0.18"></div>
					<div id="esg-add-new-custom-youtube" class="esg-open-edit-dialog esg-center eg-addnewitem-element-1 esg-transition" data-transition="esg-slideup" data-delay="0"><i class="eg-icon-youtube-squared"></i></div>
					<div class="esg-absolute eg-addnewitem-element-3 esg-transition" data-transition="esg-falldownout" data-delay="0.1"><i class="eg-icon-plus"></i></div>
					<div class="esg-bottom eg-addnewitem-element-2 esg-transition" data-transition="esg-flipup" data-delay="0.1">' . esc_attr__('CHOOSE YOUR ITEM', 'essential-grid') . '</div>
					<div id="esg-add-new-custom-vimeo" class="esg-open-edit-dialog esg-center eg-addnewitem-element-1 eg-addnewitem-element-space esg-transition" data-transition="esg-slideup" data-delay="0.1"><i class="eg-icon-vimeo-squared"></i></div>
					<div id="esg-add-new-custom-html5" class="esg-open-edit-dialog esg-center eg-addnewitem-element-1 esg-transition" data-transition="esg-slideup" data-delay="0.2"><i class="eg-icon-video"></i></div>
					<div class="esg-center eg-addnewitem-element-4 esg-none esg-clear"></div>
					<div id="esg-add-new-custom-image" class="esg-open-edit-dialog esg-center eg-addnewitem-element-1 esg-transition" data-transition="esg-slideup" data-delay="0.3"><i class="eg-icon-picture-1"></i></div>
					<div id="esg-add-new-custom-soundcloud" class="esg-open-edit-dialog esg-center eg-addnewitem-element-1 eg-addnewitem-element-5 esg-transition" data-transition="esg-slideup" data-delay="0.4"><i class="eg-icon-soundcloud"></i></div>
					<div id="esg-add-new-custom-text" class="esg-open-edit-dialog esg-center eg-addnewitem-element-1 eg-addnewitem-element-6 esg-transition" data-transition="esg-slideup" data-delay="0.5"><i class="eg-icon-font"></i></div>
					<div id="esg-add-new-custom-blank" class="esg-open-edit-dialog esg-center eg-addnewitem-element-1 esg-transition" data-transition="esg-slideup" data-delay="0.6"><i class="eg-icon-cancel"></i></div>
				</div>
			</div>
		</li>');
		// phpcs:enable
	}

	/**
	 * return all current set filter as array
	 */
	public function insert_masonry_layer($demo = false, $style = false, $is_video = false, $grid_ids = '', $post_ids = '') {
		$base = new Essential_Grid_Base();

		$content_class = ' eg-' . $this->handle . '-content';
		do_action('essgrid_insert_masonry_layer_pre', $demo, $style, $is_video);

		echo '<div class="esg-entry-content' . esc_attr($content_class) . '" ';
		if ($style !== false) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo apply_filters('essgrid_insert_masonry_layer_style', $style);
		}
		echo '>' . "\n";
		if (!empty($this->layers)) {
			foreach ($this->layers as $layer) {
				if (!isset($layer['container']) || $layer['container'] != 'm') continue;
				$link_to = $base->getVar($layer, ['settings', 'link-type'], 'none');
				$hide_on_video = $base->getVar($layer, ['settings', 'hide-on-video'], 'false');

				if ($link_to != 'embedded_video' && $hide_on_video == 'true' && $is_video) continue; //this element is hidden if media is video
				if ($link_to != 'embedded_video' && $hide_on_video == 'show' && !$is_video) continue; //this element is only shown if media is video

				if ($demo === false) {
					//show element only if it is on sale or if featured
					if (Essential_Grid_Woocommerce::is_woo_exists()) {
						$show_on_sale = $base->getVar($layer, ['settings', 'show-on-sale'], 'false');
						if ($show_on_sale == 'true') {
							$sale = Essential_Grid_Woocommerce::check_if_on_sale($this->post['ID']);
							if (!$sale) continue;
						}
						$show_if_featured = $base->getVar($layer, ['settings', 'show-if-featured'], 'false');
						if ($show_if_featured == 'true') {
							$featured = Essential_Grid_Woocommerce::check_if_is_featured($this->post['ID']);
							if (!$featured) continue;
						}
					}
				}

				if ($demo == 'overview' || $demo == 'skinchoose' || $demo == 'custom') {
					self::insert_layer($layer, $demo, true, $grid_ids, $post_ids);
				} else {
					self::insert_layer($layer, false, true, $grid_ids, $post_ids);
				}
			}
		}
		echo '</div>';
		do_action('essgrid_insert_masonry_layer_post', $demo, $style, $is_video);
	}

	/**
	 * return all current set filter as array
	 */
	public function get_filter_array() {
		return apply_filters('essgrid_get_filter_array', $this->filter);
	}

	/**
	 * set all post values for post output
	 */
	public function set_post_values($post) {
		$this->post = apply_filters('essgrid_set_post_values', $post);
		$this->set_post_meta_values();
	}

	/**
	 * set all post values for post output
	 */
	public function set_layer_values($values) {
		$this->layer_values = apply_filters('essgrid_set_layer_values', $values);
	}
	
	/**
	 * get all post values / layer values at custom grid
	 * @since: 2.1.0
	 */
	public function get_layer_values() {
		return apply_filters('essgrid_get_layer_values', $this->layer_values);
	}

	/**
	 * set custom post meta values for post output
	 * 
	 * @return bool
	 */
	public function set_post_meta_values() {
		if (empty($this->post)) return false;

		$values = isset($this->post['ID']) ? get_post_custom($this->post['ID']) : false;
		if (!empty($values)) {
			$eg_settings_custom_meta_skin = isset($values['eg_settings_custom_meta_skin']) ? unserialize($values['eg_settings_custom_meta_skin'][0]) : "";
			$eg_settings_custom_meta_element = isset($values['eg_settings_custom_meta_element']) ? unserialize($values['eg_settings_custom_meta_element'][0]) : "";
			$eg_settings_custom_meta_setting = isset($values['eg_settings_custom_meta_setting']) ? unserialize($values['eg_settings_custom_meta_setting'][0]) : "";
			$eg_settings_custom_meta_style = isset($values['eg_settings_custom_meta_style']) ? unserialize($values['eg_settings_custom_meta_style'][0]) : "";
		} /* 2.2.6 */
		else {
			$values = $this->post;
			$eg_settings_custom_meta_skin = $values['eg_settings_custom_meta_skin'] ?? "";
			$eg_settings_custom_meta_element = $values['eg_settings_custom_meta_element'] ?? "";
			$eg_settings_custom_meta_setting = $values['eg_settings_custom_meta_setting'] ?? "";
			$eg_settings_custom_meta_style = $values['eg_settings_custom_meta_style'] ?? "";
		}

		$eg_meta = [];

		if (!empty($eg_settings_custom_meta_skin)) {
			foreach ($eg_settings_custom_meta_skin as $key => $val) {
				$eg_meta[$key]['skin'] = @$val;
				$eg_meta[$key]['element'] = @$eg_settings_custom_meta_element[$key];
				$eg_meta[$key]['setting'] = @$eg_settings_custom_meta_setting[$key];
				$eg_meta[$key]['style'] = @$eg_settings_custom_meta_style[$key];
			}
		}

		unset($values['eg_settings_custom_meta_skin']);
		unset($values['eg_settings_custom_meta_element']);
		unset($values['eg_settings_custom_meta_setting']);
		unset($values['eg_settings_custom_meta_style']);

		$values['eg-meta-style'] = $eg_meta;
		$this->post_meta = apply_filters('essgrid_set_post_meta_values', $values);
		
		return true;
	}

	/**
	 * check if element has custom information set in post, if yes, return it
	 */
	public function set_meta_element_changes($layer_id, $class) {
		if (!empty($this->post_meta) && !empty($this->post_meta['eg-meta-style'])) {
			//get all allowed meta keys
			$item_ele = new Essential_Grid_Item_Element();
			$metas = $item_ele->get_allowed_meta();

			foreach ($this->post_meta['eg-meta-style'] as $entry) {
				if ($entry['skin'] == $this->id && $entry['element'] == $layer_id) {
					$found = false;
					foreach ($metas as $meta) {
						if ($meta['name']['handle'] == $entry['setting']) { 
							//found, check if style, anim or layout, we only need style here
							if ($meta['container'] == 'style') $found = true;
							break;
						}
					}

					if ($found) { 
						//only add if it is a style
						if (strpos($entry['setting'], '-hover') !== false) { 
							//check if we are hover or not
							$style = 'hover';
							$entry['setting'] = str_replace('-hover', '', $entry['setting']);
						} else {
							$style = 'idle';
						}

						if ($entry['setting'] == 'box-shadow') {
							$this->layers_meta_css[$style][$class]['-moz-' . $entry['setting']] = $entry['style'];
							$this->layers_meta_css[$style][$class]['-webkit-' . $entry['setting']] = $entry['style'];
						}
						$this->layers_meta_css[$style][$class][$entry['setting']] = $entry['style'];
					}
				}
			}
		}
	}

	/**
	 * check if layout has custom information set in post, if yes, return it
	 */
	public function get_meta_layout_change($setting) {
		$found = false;
		if (!empty($this->post_meta) && !empty($this->post_meta['eg-meta-style'])) {
			//get all allowed meta keys
			$item_ele = new Essential_Grid_Item_Element();
			$metas = $item_ele->get_allowed_meta();
			foreach ($this->post_meta['eg-meta-style'] as $entry) {
				if ($entry['skin'] == $this->id) {
					foreach ($metas as $meta) {
						if ($meta['name']['handle'] == $entry['setting'] && $setting == $entry['setting']) { //found, check if layout
							if ($meta['container'] == 'layout') { //we only want layout here
								$found = $entry['style'];
							}
							break;
						}
					}
				}
			}
		}

		return $found;
	}
	
	/**
	 * check if layout has custom information set in post, if yes, return it
	 */
	public function get_meta_element_change($layer_id, $setting) {
		$found = false;
		if (!empty($this->post_meta) && !empty($this->post_meta['eg-meta-style'])) {
			//get all allowed meta keys
			$item_ele = new Essential_Grid_Item_Element();
			$metas = $item_ele->get_allowed_meta();
			foreach ($this->post_meta['eg-meta-style'] as $entry) {
				if ($entry['skin'] == $this->id && $entry['element'] == $layer_id) {
					foreach ($metas as $meta) {
						if ($meta['name']['handle'] == $entry['setting'] && $setting == $entry['setting']) { 
							$found = $entry['style'];
							break;
						}
					}
				}
			}
		}

		return $found;
	}

	/**
	 * @param bool|string $order
	 *
	 * @return string
	 */
	public function get_media_attributes($order) {
		$base = new Essential_Grid_Base();
		$img_dim = '';
		$img_settings = [
			'image-fit' => $base->getVar($this->params, 'image-fit', 'cover'),
			'image-repeat' => $base->getVar($this->params, 'image-repeat', 'no-repeat'),
			'image-align-horizontal' => $base->getVar($this->params, 'image-align-horizontal', 'center'),
			'image-align-vertical' => $base->getVar($this->params, 'image-align-vertical', 'center')
		];

		if ($order !== false) {
			if (isset($this->media_sources[$order . '-width']) && intval($this->media_sources[$order . '-width']) > 0 && isset($this->media_sources[$order . '-height']) && intval($this->media_sources[$order . '-height']) > 0) {
				$img_dim = ' width="' . esc_attr($this->media_sources[$order . '-width']) . '" height="' . esc_attr($this->media_sources[$order . '-height']) . '"';
			}
		} else {
			$img_dim = (!empty($this->default_image_attr) && isset($this->default_image_attr[0]) && isset($this->default_image_attr[1])) ? ' width="' . esc_attr($this->default_image_attr[0]) . '" height="' . esc_attr($this->default_image_attr[1]) . '"' : '';
		}
		if (isset($this->media_sources['image-fit']) && $this->media_sources['image-fit'] !== '' && $this->media_sources['image-fit'] !== $img_settings['image-fit']) {
			$img_dim .= ' data-bgsize="' . esc_attr($this->media_sources['image-fit']) . '"';
		}

		if (isset($this->media_sources['image-repeat']) && $this->media_sources['image-repeat'] !== '' && $this->media_sources['image-repeat'] !== $img_settings['image-repeat']) {
			$img_dim .= ' data-bgrepeat="' . esc_attr($this->media_sources['image-repeat']) . '"';
		}

		$img_hor = (isset($this->media_sources['image-align-horizontal']) &&
			$this->media_sources['image-align-horizontal'] !== '' &&
			$this->media_sources['image-align-horizontal'] !== $img_settings['image-align-horizontal']
		) ? $this->media_sources['image-align-horizontal'] : '';
		$img_ver = (isset($this->media_sources['image-align-vertical']) &&
			$this->media_sources['image-align-vertical'] !== '' &&
			$this->media_sources['image-align-vertical'] !== $img_settings['image-align-vertical']
		) ? $this->media_sources['image-align-vertical'] : '';

		if ($img_hor !== '' || $img_ver !== '') {
			if ($img_hor == '') $img_hor = $img_settings['image-align-horizontal'];
			if ($img_ver == '') $img_ver = $img_settings['image-align-vertical'];
			$img_dim .= ' data-bgposition="' . esc_attr($img_hor) . ' ' . esc_attr($img_ver) . '"';
		}

		return $img_dim;
	}

	/**
	 * check if element is absolute positioned
	 * 
	 * @param string $ele_class
	 * 
	 * @return bool
	 */
	public function is_absolute_position($ele_class) {
		if (!empty($this->layers_css[$this->id]['idle'])) {
			foreach ($this->layers_css[$this->id]['idle'] as $class => $settings) {
				if ($class == $ele_class) {
					if (!empty($settings)) {
						foreach ($settings as $style => $value) {
							if ($style == 'position') {
								if ($value == 'absolute') {
									return true;
								}
								return false;
							}
						}
					}
					return false;
				}
			}
		}

		return false;
	}

	/**
	 * clean styles that are not needed
	 * @since: 1.5.4
	 */
	public function clean_up_styles($styles) {
		if (isset($styles['display'])) {
			if ($styles['display'] == 'block') {
				if (isset($styles['float'])) unset($styles['float']);
			}
			if ($styles['display'] == 'inline-block') {
				if (isset($styles['text-align'])) unset($styles['text-align']);
			}
		}
		return $styles;
	}

	/**
	 * @param string $css
	 *
	 * @return void
	 */
	protected function _output_css( $css ) {
		if ( empty( $css ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- var contain escaped css
		echo '<style>' . Essential_Grid_Base::compress_assets( $css ) . '</style>' . "\n";
	}
	
	/**
	 * return all styles from all elements
	 */
	public function generate_element_css($demo = false) {
		$base = new Essential_Grid_Base();

		$allowed_wrap_styles = Essential_Grid_Item_Element::get_allowed_styles_for_wrap();
		$wait_for_styles = Essential_Grid_Item_Element::get_wait_until_output_styles();

		if (!empty($this->layers_css)) {
			foreach ($this->layers_css as $layers_css) {
				if (!empty($layers_css['idle'])) {
					
					$css = '';
					foreach ($layers_css['idle'] as $class => $settings) {
						$wait = [];
						$forbidden = [];
						if (!empty($this->add_css_wrap) && isset($this->add_css_wrap[$class])) $forbidden = $allowed_wrap_styles; //write hover only if no tag inside the text exists
						$d_i = $layers_css['settings'][$class]['important']; //add important or not
						$position_found = false;
						if (!empty($settings)) {
							$settings = $this->clean_up_styles($settings);
							$css .= '.' . $class . ' {' . "\n";
							foreach ($settings as $style => $value) {
								$jump_next = false;
								foreach ($wait_for_styles as $k => $wf) { 
									//check if we wait until end to write style, depending on what setting the other styles have
									if (in_array($style, $wf['wait'])) {
										$wait[$k][] = [$style, $value];
										$jump_next = true;
									}
								}
								if ($jump_next) continue;

								if (!in_array($style, $forbidden))
									$css .= '	' . $style . ': ' . stripslashes($value) . $d_i . ';' . "\n";

								if ($style == 'position') $position_found = true;
							}
							if (!$position_found) $css .= '	position: relative;' . "\n";
							$css .= '	z-index: 2 !important;' . "\n";

							if (isset($this->add_css_wrap[$class]['a']) && $this->add_css_wrap[$class]['a']['display']) {
								$css .= '	display: '. $settings['display'] .';' . "\n";
							}
							if (!empty($wait)) {
								foreach ($wait as $wait_for => $wait_styles) {
									if (isset($settings[$wait_for])) {
										if (is_array($wait_for_styles[$wait_for]['not-if'])) {
											$do_continue = false;
											foreach ($wait_for_styles[$wait_for]['not-if'] as $wf) {
												if (strpos($settings[$wait_for], $wf) !== false) {
													$do_continue = true;
													break;
												}
											}
											if ($do_continue) continue;
										} else {
											if ($settings[$wait_for] === $wait_for_styles[$wait_for]['not-if']) continue;
										}
										foreach ($wait_styles as $ww) {
											if (!in_array($ww[0], $forbidden))
												$css .= '	' . $ww[0] . ': ' . stripslashes($ww[1]) . $d_i . ';' . "\n";
										}
									}
								}
							}
							$css .= '}' . "\n";
						}
					}

					$this->_output_css($css);
				}
				
				if (!empty($layers_css['hover'])) {
					
					$css = '';
					foreach ($layers_css['hover'] as $class => $settings) {
						if (!empty($this->add_css_tags) && isset($this->add_css_tags[$class])) continue; //write hover only if no tag inside the text exists

						$wait = [];
						$d_i = $layers_css['settings'][$class]['important']; //add important or not
						if (!empty($settings)) {
							$settings = $this->clean_up_styles($settings);
							$css .= '.' . $class . ':hover {' . "\n";
							foreach ($settings as $style => $value) {
								$jump_next = false;
								foreach ($wait_for_styles as $k => $wf) { 
									//check if we wait until end to write style, depending on what setting the other styles have
									if (in_array($style, $wf['wait'])) {
										$wait[$k][] = [$style, $value];
										$jump_next = true;
									}
								}
								if ($jump_next) continue;
								$css .= '	' . $style . ': ' . stripslashes($value) . $d_i . ';' . "\n";
							}
							if (!empty($wait)) {
								foreach ($wait as $wait_for => $wait_styles) {
									if (isset($settings[$wait_for]) && $settings[$wait_for] !== $wait_for_styles[$wait_for]['not-if']) {
										if (is_array($wait_for_styles[$wait_for]['not-if'])) {
											$do_continue = false;
											foreach ($wait_for_styles[$wait_for]['not-if'] as $wf) {
												if (strpos($settings[$wait_for], $wf) !== false) {
													$do_continue = true;
													break;
												}
											}
											if ($do_continue) continue;
										}
										
										foreach ($wait_styles as $ww) {
											$css .= '	' . $ww[0] . ': ' . stripslashes($ww[1]) . $d_i . ';' . "\n";
										}
									}
								}
							}
							$css .= '}' . "\n";
						}
					}

					$this->_output_css($css);
				}

				//check for custom css on tags
				if (!empty($this->add_css_tags)) {
					$allowed_styles = Essential_Grid_Item_Element::get_allowed_styles_for_tags();
					foreach ($this->add_css_tags as $class => $tags) {
						if (!empty($layers_css['idle'][$class])) { 
							// we write the idle styles
							$d_i = $layers_css['settings'][$class]['important']; //add important or not

							foreach ($tags as $tag => $do) {
								$css = '.' . $class . ' ' . $tag . ' {' . "\n";
								$layers_css['idle'][$class] = $this->clean_up_styles($layers_css['idle'][$class]);
								foreach ($layers_css['idle'][$class] as $style => $value) {
									if (in_array($style, $allowed_styles))
										$css .= '	' . $style . ': ' . stripslashes($value) . $d_i . ';' . "\n";
								}
								$css .= '}' . "\n";

								$this->_output_css($css);
							}
						}

						if (!empty($layers_css['hover'][$class])) { 
							// we write the hover styles
							$d_i = $layers_css['settings'][$class]['important']; //add important or not
							foreach ($tags as $tag => $do) {
								$css = '.' . $class . ' ' . $tag . ':hover {' . "\n";
								$layers_css['hover'][$class] = $this->clean_up_styles($layers_css['hover'][$class]);
								foreach ($layers_css['hover'][$class] as $style => $value) {
									if (in_array($style, $allowed_styles))
										$css .= '	' . $style . ': ' . stripslashes($value) . $d_i . ';' . "\n";
								}
								$css .= '}' . "\n";

								$this->_output_css($css);
							}
						}
					}
				}

				//check for custom css on wrappers for example
				if (!empty($this->add_css_wrap)) {
					$allowed_cat_tag_styles = Essential_Grid_Item_Element::get_allowed_styles_for_cat_tag();
					foreach ($this->add_css_wrap as $class => $tags) {
						if (!empty($layers_css['idle'][$class])) { 
							// we write the idle styles
							$d_i = $layers_css['settings'][$class]['important']; //add important or not
							foreach ($tags as $tag => $do) {
								$css = '.' . $class . '-' . $tag . ' {' . "\n";

								$position_found = false;

								if (isset($this->add_css_wrap[$class]['a']) && $this->add_css_wrap[$class]['a']['full']) { 
									// set more styles (used for cat & tag list)
									$allowed_styles = array_merge($allowed_cat_tag_styles, $allowed_wrap_styles);
								} else {
									$allowed_styles = $allowed_wrap_styles;
								}

								$layers_css['idle'][$class] = $this->clean_up_styles($layers_css['idle'][$class]);

								foreach ($layers_css['idle'][$class] as $style => $value) {
									if (in_array($style, $allowed_styles)) {
										$css .= '	' . $style . ': ' . stripslashes($value) . $d_i . ';' . "\n";
										if ($style == 'position') $position_found = true;
									}
								}

								if (!$position_found) $css .= '	position: relative;' . "\n";

								$css .= '}' . "\n";

								$this->_output_css($css);
							}
						}
					}
				}
			}
		}

		if (!empty($this->media_css)) {
			foreach ($this->media_css as $skin_id => $media_css) {
				if (!empty($media_css)) {

					$css = '';
					$handle = $this->loaded_skins[$skin_id]['handle'];
					$css .= '.eg-' . esc_attr($handle) . '-wrapper .esg-entry-media-wrapper {' . "\n";

					$media_css = $this->clean_up_styles($media_css);
					foreach ($media_css as $style => $value) {
						$css .= '	' . $style . ': ' . stripslashes($value) . ';' . "\n"; // !important;
					}
					$css .= '}' . "\n";

					/* 2.2.6 */
					if (isset($this->media_shadow[$skin_id])) {
						$cover_duration = $base->getVar($this->params, 'cover-animation-duration-center', 300);
						if ($cover_duration === 'default') $cover_duration = 300;
						$cover_delay = $base->getVar($this->params, 'cover-animation-delay-center', '0');
						$cover_duration = intval($cover_duration) * 0.001;
						$cover_delay = floatval($cover_delay);

						if (!$cover_delay) {
							$cover_delay = '';
						} else {
							$cover_delay = $cover_delay * 0.01;
							$cover_delay = $cover_delay . 's';
						}

						$css .= '.eg-' . esc_attr($handle) . '-wrapper .esg-entry-media-wrapper {' . "\n";
						$css .= '	transition: box-shadow ' . $cover_duration . 's ease-out ' . $cover_delay . ';';
						$css .= '}' . "\n";
						$css .= '.eg-' . esc_attr($handle) . '-wrapper.esg-hovered .esg-entry-media-wrapper {' . "\n";
						$css .= '	box-shadow: ' . $this->media_shadow[$skin_id] . ';' . "\n";
						$css .= '}' . "\n";
					}

					$this->_output_css($css);
				}
			}
		}

		if (!empty($this->cover_css)) {
			foreach ($this->cover_css as $skin_id => $cover_css) {
				if (!empty($cover_css) && !empty($this->loaded_skins[$skin_id]['handle'])) {

					$css = '';
					$handle = $this->loaded_skins[$skin_id]['handle'];
					$css .= '.eg-' . esc_attr($handle) . '-container {' . "\n";
					
					$cover_css = $this->clean_up_styles($cover_css);
					foreach ($cover_css as $style => $value) {
						$css .= '	' . $style . ': ' . stripslashes($value) . ';' . "\n"; // !important;
					}
					$css .= '}' . "\n";

					/* 2.2.6 */
					if (isset($this->cover_shadow[$skin_id])) {

						$cover_duration = $base->getVar($this->params, 'cover-animation-duration-center', 300);
						if ($cover_duration === 'default') $cover_duration = 300;

						$cover_delay = $base->getVar($this->params, 'cover-animation-delay-center', '0');
						$cover_duration = intval($cover_duration) * 0.001;
						$cover_delay = floatval($cover_delay);

						if (!$cover_delay) {
							$cover_delay = '';
						} else {
							$cover_delay = $cover_delay * 0.01;
							$cover_delay = $cover_delay . 's';
						}

						$css .= '.eg-' . esc_attr($handle) . '-container {' . "\n";
						$css .= '	transition: box-shadow ' . $cover_duration . 's ease-out ' . $cover_delay . ';';
						$css .= '}' . "\n";
						$css .= '.esg-hovered .eg-' . esc_attr($handle) . '-container {' . "\n";
						$css .= '	box-shadow: ' . $this->cover_shadow[$skin_id] . ';' . "\n";
						$css .= '}' . "\n";
					}

					$this->_output_css($css);
				}
			}
		}

		if (!empty($this->content_css)) {
			foreach ($this->content_css as $skin_id => $content_css) {
				if (!empty($content_css) && !empty($this->loaded_skins[$skin_id]['handle'])) {

					$css = '';
					$handle = $this->loaded_skins[$skin_id]['handle'];
					$css .= '.eg-' . esc_attr($handle) . '-content {' . "\n";

					$content_css = $this->clean_up_styles($content_css);
					foreach ($content_css as $style => $value) {
						$css .= '	' . $style . ': ' . stripslashes($value) . ';' . "\n";
					}
					$css .= '}' . "\n";

					/* 2.2.6 */
					if (isset($this->content_shadow[$skin_id])) {

						$cover_duration = $base->getVar($this->params, 'cover-animation-duration-center', 300);
						if ($cover_duration === 'default') $cover_duration = 300;

						$cover_delay = $base->getVar($this->params, 'cover-animation-delay-center', '0');
						$cover_duration = intval($cover_duration) * 0.001;
						$cover_delay = floatval($cover_delay);

						if (!$cover_delay) {
							$cover_delay = '';
						} else {
							$cover_delay = $cover_delay * 0.01;
							$cover_delay = $cover_delay . 's';
						}

						$css .= '.eg-' . esc_attr($handle) . '-content {' . "\n";
						$css .= '	transition: box-shadow ' . $cover_duration . 's ease-out ' . $cover_delay . ';';
						$css .= '}' . "\n";
						$css .= '.esg-hovered .eg-' . esc_attr($handle) . '-content{' . "\n";
						$css .= '	box-shadow: ' . $this->content_shadow[$skin_id] . ';' . "\n";
						$css .= '}' . "\n";
					}

					$this->_output_css($css);
				}
			}
		}

		if (!empty($this->wrapper_css)) {
			foreach ($this->wrapper_css as $skin_id => $wrapper_css) {
				if (!empty($wrapper_css) && !empty($this->loaded_skins[$skin_id]['handle'])) {

					$css = '';
					$handle = $this->loaded_skins[$skin_id]['handle'];
					$css .= '.esg-grid .mainul li.eg-' . esc_attr($handle) . '-wrapper {' . "\n";

					$wrapper_css = $this->clean_up_styles($wrapper_css);
					foreach ($wrapper_css as $style => $value) {
						$css .= '	' . $style . ': ' . stripslashes($value) . ';' . "\n";
					}
					$css .= '}' . "\n";

					/* 2.2.6 */
					if (isset($this->wrapper_shadow[$skin_id])) {

						$cover_duration = $base->getVar($this->params, 'cover-animation-duration-center', 300);
						if ($cover_duration === 'default') $cover_duration = 300;

						$cover_delay = $base->getVar($this->params, 'cover-animation-delay-center', '0');
						$cover_duration = intval($cover_duration) * 0.001;
						$cover_delay = floatval($cover_delay);

						if (!$cover_delay) {
							$cover_delay = '';
						} else {
							$cover_delay = $cover_delay * 0.01;
							$cover_delay = $cover_delay . 's';
						}

						$css .= '.eg-' . esc_attr($handle) . '-wrapper {' . "\n";
						$css .= '	transition: box-shadow ' . $cover_duration . 's ease-out ' . $cover_delay . ';';
						$css .= '}' . "\n";
						$css .= '.eg-' . esc_attr($handle) . '-wrapper.esg-hovered {' . "\n";
						$css .= '	box-shadow: ' . $this->wrapper_shadow[$skin_id] . ' !important;' . "\n";
						$css .= '}' . "\n";
					}

					$this->_output_css($css);
				}
			}
		}

		if (!empty($this->media_poster_css)) {
			foreach ($this->media_poster_css as $skin_id => $media_poster_css) {
				if (!empty($media_poster_css) && !empty($this->loaded_skins[$skin_id]['handle'])) {

					$css = '';
					$handle = $this->loaded_skins[$skin_id]['handle'];
					$css .= '.esg-grid .mainul li.eg-' . esc_attr($handle) . '-wrapper .esg-media-poster {' . "\n";

					$media_poster_css = $this->clean_up_styles($media_poster_css);
					foreach ($media_poster_css as $style => $value) {
						$css .= '	' . $style . ': ' . stripslashes($value) . ';' . "\n"; // !important
					}
					$css .= '}' . "\n";

					$this->_output_css($css);
				}
			}
		}
	}

	public function output_element_css_by_meta($id = false, $grid_preview = false) {
		$base = new Essential_Grid_Base();
		$disallowed = ['transition', 'transition-delay'];
		$allowed_wrap_styles = Essential_Grid_Item_Element::get_allowed_styles_for_wrap();
		$p_class = ($id === false) ? '' : '.eg-post-' . $id;

		if (!empty($this->layers_meta_css['idle'])) {

			$css = '';
			foreach ($this->layers_meta_css['idle'] as $class => $settings) {
				$forbidden = [];
				if (!empty($this->add_css_wrap) && isset($this->add_css_wrap[$class])) $forbidden = $allowed_wrap_styles; //write hover only if no tag inside the text exists
				$d_i = $this->layers_css[$this->id]['settings'][$class]['important']; //add important or not
				if (!empty($settings)) {

					// 2.2.6
					if (!empty($grid_preview) && !empty($this->grid_id)) $css .= '[id^="esg-grid-' . $this->grid_id . '"] ';

					$css .= '.' . $class . $p_class . ' {' . "\n";
					foreach ($settings as $style => $value) {
						if (!in_array($style, $forbidden) && !in_array($style, $disallowed))
							$css .= '	' . $style . ': ' . stripslashes($value) . $d_i . ';' . "\n";
					}
					$css .= '}' . "\n";
				}
			}
			
			$this->_output_css($css);
		}

		if (!empty($this->layers_meta_css['hover'])) {

			$css = '';
			foreach ($this->layers_meta_css['hover'] as $class => $settings) {
				if (!empty($this->add_css_tags) && isset($this->add_css_tags[$class])) continue; //write hover only if no tag inside the text exists
				$d_i = $this->layers_css[$this->id]['settings'][$class]['important']; //add important or not
				if (!empty($settings)) {
					$css .= '.' . $class . $p_class . ':hover {' . "\n";
					foreach ($settings as $style => $value) {
						$css .= '	' . $style . ': ' . stripslashes($value) . $d_i . ';' . "\n";
					}
					$css .= '}' . "\n";
				}
			}
			
			$this->_output_css($css);
		}

		//check for custom css on tags
		if (!empty($this->add_css_tags)) {
			$allowed_styles = Essential_Grid_Item_Element::get_allowed_styles_for_tags();
			foreach ($this->add_css_tags as $class => $tags) {
				if (!empty($this->layers_meta_css['idle'][$class])) { 
					// we write the idle styles
					$d_i = $this->layers_css[$this->id]['settings'][$class]['important']; //add important or not
					foreach ($tags as $tag => $do) {
						
						$css = '.' . $class . $p_class . ' ' . $tag . ' {' . "\n";

						foreach ($this->layers_meta_css['idle'][$class] as $style => $value) {
							if (in_array($style, $allowed_styles))
								$css .= '	' . $style . ': ' . stripslashes($value) . $d_i . ';' . "\n";
						}

						$css .= '}' . "\n";

						$this->_output_css($css);
					}
				}

				if (!empty($this->layers_meta_css['hover'][$class])) { 
					// we write the hover styles
					$d_i = $this->layers_css[$this->id]['settings'][$class]['important']; //add important or not
					foreach ($tags as $tag => $do) {
						$css = '.' . $class . $p_class . ' ' . $tag . ':hover {' . "\n";

						foreach ($this->layers_meta_css['hover'][$class] as $style => $value) {
							if (in_array($style, $allowed_styles))
								$css .= '	' . $style . ': ' . stripslashes($value) . $d_i . ';' . "\n";
						}

						$css .= '}' . "\n";

						$this->_output_css($css);
					}
				}
			}
		}

		//check for custom css on wrappers for example
		if (!empty($this->add_css_wrap)) {
			$allowed_cat_tag_styles = Essential_Grid_Item_Element::get_allowed_styles_for_cat_tag();
			foreach ($this->add_css_wrap as $class => $tags) {
				if (!empty($this->layers_meta_css['idle'][$class])) { 
					// we write the idle styles
					$d_i = $this->layers_css[$this->id]['settings'][$class]['important']; //add important or not
					foreach ($tags as $tag => $do) {
						$css = '.' . $class . '-' . $tag . $p_class . ' {' . "\n";

						if (isset($this->add_css_wrap[$class]['a']) && $this->add_css_wrap[$class]['a']['full']) { 
							// set more styles (used for cat & tag list)
							$allowed_styles = array_merge($allowed_cat_tag_styles, $allowed_wrap_styles);
						} else {
							$allowed_styles = $allowed_wrap_styles;
						}

						foreach ($this->layers_meta_css['idle'][$class] as $style => $value) {
							if (in_array($style, $allowed_styles)) {
								$css .= '	' . $style . ': ' . stripslashes($value) . $d_i . ';' . "\n";
							}
						}
						$css .= '}' . "\n";

						$this->_output_css($css);
					}
				}
			}
		}
		$this->layers_meta_css = [];
	}

	/**
	 * register skin css styles, added for multiskin in one grid + load more
	 * @since: 2.0
	 */
	public function register_skin_css() {
		$base = new Essential_Grid_Base();

		/* 2.1.6 */
		$container_background_color = $base->getVar($this->params, 'container-background-color', '#000');
		$contentBgColor = $base->getVar($this->params, 'content-bg-color', '#FFF');
		$fullBgColor = $base->getVar($this->params, 'full-bg-color', '#FFF');
		if (class_exists('ESGColorpicker')) {
			$container_background_color = ESGColorpicker::get($container_background_color);
			$contentBgColor = ESGColorpicker::get($contentBgColor);
			$fullBgColor = ESGColorpicker::get($fullBgColor);
		}

		$this->cover_css[$this->id]['background'] = $container_background_color;

		$cover_background_image_id = $base->getVar($this->params, 'cover-background-image', 0, 'i');
		$cover_background_image_size = $base->getVar($this->params, 'cover-background-size', 'cover');
		$cover_background_image_repeat = $base->getVar($this->params, 'cover-background-repeat', 'no-repeat');

		if ($cover_background_image_id > 0) {
			$cover_background_image_url = wp_get_attachment_image_src($cover_background_image_id, $this->media_sources_type);
			if ($cover_background_image_url !== false) {
				$this->cover_css[$this->id]['background-image'] = 'url(' . $cover_background_image_url[0] . ')';
				$this->cover_css[$this->id]['background-size'] = $cover_background_image_size;
				$this->cover_css[$this->id]['background-repeat'] = $cover_background_image_repeat;
			}
		}

		$this->wrapper_css[$this->id]['background'] = $fullBgColor;
		$this->wrapper_css[$this->id]['padding'] = implode('px ', $base->getVar($this->params, 'full-padding', ['0'])) . 'px';
		$this->wrapper_css[$this->id]['border-width'] = implode('px ', $base->getVar($this->params, 'full-border', ['0'])) . 'px';

		$border_type = $base->getVar($this->params, 'full-border-radius-type', 'px');
		$this->wrapper_css[$this->id]['border-radius'] = implode($border_type . ' ', $base->getVar($this->params, 'full-border-radius', ['0'])) . $border_type;

		$this->wrapper_css[$this->id]['border-color'] = $base->getVar($this->params, 'full-border-color', '#FFF');
		$this->wrapper_css[$this->id]['border-style'] = $base->getVar($this->params, 'full-border-style', 'none');
		$overflow = $base->getVar($this->params, 'full-overflow-hidden', 'false');
		if ($overflow == 'true') $this->wrapper_css[$this->id]['overflow'] = 'hidden';

		$this->media_poster_css[$this->id]['background-size'] = $base->getVar($this->params, 'image-fit', 'cover');
		$this->media_poster_css[$this->id]['background-position'] = $base->getVar($this->params, 'image-align-vertical', 'center') . ' ' . $base->getVar($this->params, 'image-align-horizontal', 'center');
		$this->media_poster_css[$this->id]['background-repeat'] = $base->getVar($this->params, 'image-repeat', 'no-repeat');

		$this->content_css[$this->id]['background'] = $contentBgColor;
		$this->content_css[$this->id]['padding'] = implode('px ', $base->getVar($this->params, 'content-padding', ['0'])) . 'px';
		$this->content_css[$this->id]['border-width'] = implode('px ', $base->getVar($this->params, 'content-border', ['0'])) . 'px';

		$border_type = $base->getVar($this->params, 'content-border-radius-type', 'px');
		$this->content_css[$this->id]['border-radius'] = implode($border_type . ' ', $base->getVar($this->params, 'content-border-radius', ['0'])) . $border_type;

		$this->content_css[$this->id]['border-color'] = $base->getVar($this->params, 'content-border-color', '#FFF');
		$this->content_css[$this->id]['border-style'] = $base->getVar($this->params, 'content-border-style', 'none');
		$this->content_css[$this->id]['text-align'] = $base->getVar($this->params, 'content-align', 'left');

		$shadow_place = $base->getVar($this->params, 'all-shadow-used', 'none');
		$shadow_values = implode('px ', $base->getVar($this->params, 'content-box-shadow', ['0', '0', '0', '0'])) . 'px';

		/* 2.1.6 */
		$shadow_rgba = $base->getVar($this->params, 'content-shadow-color', '#000000');

		/* 2.2.6 */
		$shadow_anim = $base->getVar($this->params, 'content-box-shadow-hover', 'false') == 'true';
		$inset = $base->getVar($this->params, 'content-box-shadow-inset', 'false') == 'true' ? 'inset ' : '';

		if ($shadow_place == 'media') {

			if (!$shadow_anim) {
				$this->media_css[$this->id]['box-shadow'] = $inset . $shadow_values . ' ' . $shadow_rgba;
			} else {
				$this->media_css[$this->id]['box-shadow'] = 'none';
				$this->media_shadow[$this->id] = $inset . $shadow_values . ' ' . $shadow_rgba;
			}

		} else if ($shadow_place == 'content') {

			if (!$shadow_anim) {
				$this->content_css[$this->id]['box-shadow'] = $inset . $shadow_values . ' ' . $shadow_rgba;
			} else {
				$this->content_css[$this->id]['box-shadow'] = 'none';
				$this->content_shadow[$this->id] = $inset . $shadow_values . ' ' . $shadow_rgba;
			}

		} else if ($shadow_place == 'both') {

			if (!$shadow_anim) {
				$this->wrapper_css[$this->id]['box-shadow'] = $inset . $shadow_values . ' ' . $shadow_rgba;
			} else {
				$this->wrapper_css[$this->id]['box-shadow'] = 'none';
				$this->wrapper_shadow[$this->id] = $inset . $shadow_values . ' ' . $shadow_rgba;
			}

		} else if ($shadow_place == 'cover') {

			/* 2.2.6 */
			$cover_direction = $base->getVar($this->params, 'cover-animation-center-type');
			$cover_type = $base->getVar($this->params, 'cover-type', 'full');

			if ($cover_type === 'content') $shadow_anim = false;
			if (!$shadow_anim || $cover_direction === 'out') {
				$this->cover_css[$this->id]['box-shadow'] = 'inset ' . $shadow_values . ' ' . $shadow_rgba;
			}
			if ($shadow_anim) {
				if ($cover_direction !== 'out') {
					$this->cover_css[$this->id]['box-shadow'] = 'none';
					$this->cover_shadow[$this->id] = 'inset ' . $shadow_values . ' ' . $shadow_rgba;
				} else {
					$this->cover_shadow[$this->id] = 'none';
				}
			}
		}
	}
	
	/**
	 * register layer css styles, added for multiskin in one grid + load more
	 * @since: 2.0
	 */
	public function register_layer_css($layer = false, $demo = false) {
		$base = new Essential_Grid_Base();
		if ($layer === false) {
			if (!empty($this->layers)) {
				foreach ($this->layers as $layer) {
					$this->register_layer_css($layer, $demo);
				}
			}
		} else {
			$is_post = empty($this->layer_values);
			if (isset($layer['id'])) $unique_class = 'eg-' . esc_attr($this->handle) . '-element-' . esc_attr($layer['id']);
			else $unique_class = "";

			$special_item = $base->getVar($layer, ['settings', 'special'], 'false');
			if ($special_item != 'true') {
				$this->add_element_css(@$layer['settings'], $unique_class); //add css to queue
			}

			/**
			 * not that elegant since code already existed and is now split but currently there is no better implementation than this
			 * $text is now parsed two times for each layer because of this to get if there is tag in the text -> settings display styles correctly...
			 * NOTE: most of "if(isset($layer['settings']['source'])){}" could be removed if it is known, that there can not be ANY <a> tag in it. Then it can just be replaced with a placeholdertext
			 */
			$m = new Essential_Grid_Meta();
			$text = '';

			$do_limit = true;
			$do_display = true;
			$do_full = false;
			$do_ignore_styles = false;

			if (isset($layer['settings']['source'])) {
				$separator = $base->getVar($layer, ['settings', 'source-separate'], ',');
				$catmax = $base->getVar($layer, ['settings', 'source-catmax'], '-1');
				$meta = $base->getVar($layer, ['settings', 'source-meta']);
				$func = $base->getVar($layer, ['settings', 'source-function'], 'link');
				$taxonomy = $base->getVar($layer, ['settings', 'source-taxonomy']);

				switch ($layer['settings']['source']) {
					case 'post':

						if ($demo === false) {
							if ($is_post)
								$text = $this->get_post_value($layer['settings']['source-post'], $separator, $func, $meta, $catmax, $taxonomy);
							else
								$text = $this->get_custom_element_value($layer['settings']['source-post'], $separator, $meta);
						} elseif ($demo === 'custom') {
							$text = $this->get_custom_element_value($layer['settings']['source-post'], $separator, $meta);
						} else {
							$post_text = Essential_Grid_Item_Element::getPostElementsArray();
							if (array_key_exists(@$layer['settings']['source-post'], $post_text)) $text = $post_text[@$layer['settings']['source-post']]['name'];

							if ($layer['settings']['source-post'] == 'date') {
								$da = get_option('date_format');
								if ($da !== false)
									$text = gmdate(get_option('date_format'));
								else
									$text = gmdate('Y.m.d');
							}
						}

						if ($layer['settings']['source-post'] == 'cat_list' || $layer['settings']['source-post'] == 'tag_list') { 
							//no limiting if category or tag list
							$do_limit = true;
							$do_display = false;
							$do_full = true;
						}
						$post_id = (isset($this->post['ID'])) ? $this->post['ID'] : '';
						$text = apply_filters('essgrid_post_meta_content', $text, $layer['settings']['source-post'], $post_id, $this->post);
						break;
					case 'woocommerce':
						if (Essential_Grid_Woocommerce::is_woo_exists()) {
							if ($demo === false) {
								if ($is_post)
									$text = $this->get_woocommerce_value($layer['settings']['source-woocommerce'], $separator, $catmax);
								else
									$text = $this->get_custom_element_value($layer['settings']['source-woocommerce'], $separator);

								if ($layer['settings']['source-woocommerce'] == 'wc_categories') {
									$do_limit = false;
									$do_display = false;
									$do_full = true;
								} elseif ($layer['settings']['source-woocommerce'] == 'wc_add_to_cart_button') {
									$do_limit = false;
								}
							} elseif ($demo === 'custom') {
								$text = $this->get_custom_element_value($layer['settings']['source-woocommerce'], $separator);

								if ($layer['settings']['source-woocommerce'] == 'wc_categories') {
									$do_limit = false;
									$do_display = false;
									$do_full = true;
								} elseif ($layer['settings']['source-woocommerce'] == 'wc_add_to_cart_button') {
									$do_limit = false;
								}
							} else {
								$tmp_wc = Essential_Grid_Woocommerce::get_meta_array();
								$woocommerce = [];
								
								foreach ($tmp_wc as $handle => $name) {
									$woocommerce[$handle]['name'] = $name;
								}

								if (array_key_exists(@$layer['settings']['source-woocommerce'], $woocommerce)) $text = $woocommerce[@$layer['settings']['source-woocommerce']]['name'];
							}
						}
						break;
					case 'icon':
						$text = '<i class="' . esc_attr(@$layer['settings']['source-icon']) . '"></i>';
						break;
					case 'text':
						$text = @$layer['settings']['source-text'];
						//check for metas by %meta%
						if ($is_post) {
							if (!empty($this->post['ID'])) $text = $m->replace_all_meta_in_text($this->post['ID'], $text, $layer);
						} else {
							$_a = (!empty($this->layer_values)) ? $this->layer_values : [];
							$_b = (!empty($this->media_sources)) ? $this->media_sources : [];
							$_values = array_merge($_a, $_b);
							$text = $m->replace_all_custom_element_meta_in_text($_values, $text);
						}

						// remove any meta left in text after replace
						$text = $m->remove_meta_in_text($text);

						$do_display = false;

						if (isset($layer['settings']['source-text-style-disable']) && @$layer['settings']['source-text-style-disable'])
							$do_ignore_styles = true;

						break;
				}

				if ($do_limit) {
					$limit_by = $base->getVar($layer, ['settings', 'limit-type'], 'none');
					if ($limit_by !== 'none') {
						switch ($layer['settings']['source']) {
							case 'post':
							case 'event':
							case 'woocommerce':
								$text = $base->get_text_intro($text, $base->getVar($layer, ['settings', 'limit-num'], 10, 'i'), $limit_by);
								break;
						}
					}
				}

				// 2.2.6
				$min_height = $base->getVar($layer, ['settings', 'min-height'], '0');
				$max_height = $base->getVar($layer, ['settings', 'max-height'], 'none');

				if ($min_height != '0' || $max_height !== 'none') {

					$span = '<span style="display: block;';

					if ($min_height != '0') $span .= 'min-height: ' . esc_attr($min_height) . 'px;';
					if ($max_height != 'none') $span .= 'max-height: ' . esc_attr($max_height) . 'px';

					$text = $span . '">' . $text . '</span>';

				}

			}

			$link_to = $base->getVar($layer, ['settings', 'link-type'], 'none');
			if ($link_to !== 'none') $do_display = true;

			//very basic in relation to original, just add a tag around text so that it works (note: href is mandatory here! Not sure why though)
			switch ($link_to) {
				case 'post':
				case 'url':
				case 'meta':
				case 'javascript':
				case 'lightbox':
				case 'sharefacebook':
				case 'sharetwitter':
				case 'sharepinterest':
				case 'likepost':
					$text = '<a href="#">' . $text . '</a>';
					break;
				case 'embedded_video':
				case 'ajax':
					break;
			}
			
			if ($base->text_has_certain_tag($text, 'a') && !$do_ignore_styles) {
				$this->add_css_wrap[$unique_class]['a']['display'] = $do_display; //do_display defines if we should write display: block;
				$this->add_css_wrap[$unique_class]['a']['full'] = $do_full; //do full styles (for categories and tags separator)
			}
		}
	}

	/**
	 * add all styles from an element to queue
	 */
	public function add_element_css($settings, $element_class) {
		//check if element_class already set, only proceed if it is not set
		if (isset($this->layers_css[$this->id]['idle'][$element_class])) return true;

		$idle = [];
		$hover = [];

		$do_hover = false;
		$do_important = '';

		if (isset($settings['enable-hover']) && $settings['enable-hover'] == 'on') $do_hover = true;
		if (isset($settings['force-important']) && $settings['force-important'] == 'true') $do_important = ' !important';

		if (!empty($settings)) {
			$attributes = Essential_Grid_Item_Element::get_existing_elements(true);
			foreach ($attributes as $style => $attr) {
				if ($attr['style'] == 'hover' && !$do_hover) continue;
				if (empty($settings[$style])) continue;
				if ($attr['style'] != 'idle' && $attr['style'] != 'hover') continue;
				$set_style = ($attr['style'] == 'idle') ? $style : str_replace('-hover', '', $style);

				if ($attr['type'] == 'multi-text') {
					if (!isset($settings[$style . '-unit'])) $settings[$style . '-unit'] = 'px';
					$set_unit = $settings[$style . '-unit'];
					if ($set_style == 'box-shadow' || $set_style == 'background-color') {
						$multi_string = '';
						foreach ($settings[$style] as $val) {
							$multi_string .= $val . $set_unit . ' ';
						}

						//get box shadow color
						$shadow_color = ($attr['style'] == 'idle') ? $settings['shadow-color'] : $settings['shadow-color-hover'];

						$multi_string .= ' ' . $shadow_color;

						if ($attr['style'] == 'idle') {
							$idle['-moz-' . $set_style] = $multi_string;
							$idle['-webkit-' . $set_style] = $multi_string;
							$idle[$set_style] = $multi_string;
						} else {
							$hover['-moz-' . $set_style] = $multi_string;
							$hover['-webkit-' . $set_style] = $multi_string;
							$hover[$set_style] = $multi_string;
						}
					} elseif ($set_style == 'border') {

						if ($attr['style'] == 'idle') {
							$idle['border-top-width'] = (isset($settings[$style][0])) ? $settings[$style][0] . $set_unit : '0' . $set_unit;
							$idle['border-right-width'] = (isset($settings[$style][1])) ? $settings[$style][1] . $set_unit : '0' . $set_unit;
							$idle['border-bottom-width'] = (isset($settings[$style][2])) ? $settings[$style][2] . $set_unit : '0' . $set_unit;
							$idle['border-left-width'] = (isset($settings[$style][3])) ? $settings[$style][3] . $set_unit : '0' . $set_unit;
						} else {
							$hover['border-top-width'] = (isset($settings[$style][0])) ? $settings[$style][0] . $set_unit : '0' . $set_unit;
							$hover['border-right-width'] = (isset($settings[$style][1])) ? $settings[$style][1] . $set_unit : '0' . $set_unit;
							$hover['border-bottom-width'] = (isset($settings[$style][2])) ? $settings[$style][2] . $set_unit : '0' . $set_unit;
							$hover['border-left-width'] = (isset($settings[$style][3])) ? $settings[$style][3] . $set_unit : '0' . $set_unit;
						}

					} else {
						$multi_string = '';
						foreach ($settings[$style] as $val) {
							$multi_string .= $val . $set_unit . ' ';
						}

						if ($attr['style'] == 'idle') {
							$idle[$set_style] = $multi_string;
						} else {
							$hover[$set_style] = $multi_string;
						}
					}
				} else {
					if ($set_style == 'background-color' || $set_style == 'background') {
						$bg_color_rgba = $settings[$style];
						if (class_exists('ESGColorpicker')) $bg_color_rgba = ESGColorpicker::get($bg_color_rgba);

						if ($attr['style'] == 'idle') {
							$idle['background'] = $bg_color_rgba;
						} else {
							$hover['background'] = $bg_color_rgba;
						}

					} else {
						if ($set_style == 'border') {
							if ($attr['style'] == 'idle') {
								$idle['border-style'] = 'solid';
							} else {
								$hover['border-style'] = 'solid';
							}
						}
						if ($set_style == 'font-style' && $settings[$style] == 'true') $settings[$style] = 'italic';

						$set_unit = @$attr['unit'];

						if ($attr['style'] == 'idle') {
							$idle[$set_style] = $settings[$style] . $set_unit;

							if ($set_style == 'position' && $settings[$style] == 'absolute') {
								$idle['height'] = 'auto';
								$idle['width'] = 'auto';

								switch ($settings['align']) {
									case 't_l':
										$idle['top'] = $settings['top-bottom'] . $settings['absolute-unit'];
										$idle['left'] = $settings['left-right'] . $settings['absolute-unit'];
										break;
									case 't_r':
										$idle['top'] = $settings['top-bottom'] . $settings['absolute-unit'];
										$idle['right'] = $settings['left-right'] . $settings['absolute-unit'];
										break;
									case 'b_l':
										$idle['bottom'] = $settings['top-bottom'] . $settings['absolute-unit'];
										$idle['left'] = $settings['left-right'] . $settings['absolute-unit'];
										break;
									case 'b_r':
										$idle['bottom'] = $settings['top-bottom'] . $settings['absolute-unit'];
										$idle['right'] = $settings['left-right'] . $settings['absolute-unit'];
										break;
								}
							}

						} else {
							$hover[$set_style] = $settings[$style] . $set_unit;
						}
					}
				}
			}
		}

		$this->layers_css[$this->id]['idle'][$element_class] = $idle;
		$this->layers_css[$this->id]['hover'][$element_class] = $hover;
		$this->layers_css[$this->id]['settings'][$element_class]['important'] = $do_important;

		return true;
	}

	/**
	 * set all demo filter categories like Post Title, WooCommerce, Event Calendar and even/masonry
	 */
	public function set_filter($filter) {
		$this->filter = $filter;
	}

	/**
	 * set all demo filter categories like Post Title, WooCommerce, Event Calendar and even/masonry
	 */
	public function set_demo_filter() {
		$filter = [];
		if (isset($this->params['choose-layout'])) {
			$filter[] = ['slug' => $this->params['choose-layout']]; //even || masonry
		}
		if (!empty($this->layers)) {
			foreach ($this->layers as $layer) {
				if (!isset($layer['settings']) || !isset($layer['settings']['source'])) continue;
				switch ($layer['settings']['source']) {
					case 'post':
					case 'woocommerce':
					case 'event':
						if (!in_array($layer['settings']['source'], $filter)) $filter[] = ['slug' => $layer['settings']['source']];
						break;
				}
			}
		}
		$this->filter = $filter;
	}
	
	/**
	 * set all demo filter categories like Post Title, WooCommerce, Event Calendar and even/masonry
	 */
	public function set_skin_choose_filter() {
		$filter = [];
		if (isset($this->params['choose-layout'])) {
			$filter[] = ['slug' => $this->params['choose-layout']]; //even || masonry
		}
		$this->filter = $filter;
	}

	/**
	 * set demo image
	 */
	public function set_image($img) {
		$this->cover_image = $img;
	}

	/**
	 * set default image by id
	 * @since: 1.2.0
	 */
	public function set_default_image_by_id($img_id) {
		$img_id = apply_filters('essgrid_set_default_image_by_id', $img_id);
		$img = wp_get_attachment_image_src($img_id, 'full');

		/* 2.1.5 */
		if ($img === false) {
			$img = get_option('tp_eg_global_default_img', '');
			$img = !empty($img) ? wp_get_attachment_image_src($img, 'full') : false;
		}

		$img = apply_filters('essgrid_set_default_image_by_id_after_global_default', $img);

		if ($img !== false) {
			$this->default_image = $img[0];
			$this->default_image_attr = [$img[1], $img[2]];
		}
	}

	/**
	 * set grid item animation
	 * @since: 2.1.6.2
	 */
	public function set_grid_item_animation($base, $params) {
		$this->grid_item_animation = $base->getVar($params, 'grid-item-animation', 'none');
		$this->grid_item_animation_other = $base->getVar($params, 'grid-item-animation-other', 'none');

		$this->grid_item_animation_zoomin = $base->getVar($params, 'grid-item-animation-zoomin', '125');
		$this->grid_item_other_zoomin = $base->getVar($params, 'grid-item-other-zoomin', '125');

		$this->grid_item_animation_zoomout = $base->getVar($params, 'grid-item-animation-zoomout', '75');
		$this->grid_item_other_zoomout = $base->getVar($params, 'grid-item-other-zoomout', '75');

		$this->grid_item_animation_fade = $base->getVar($params, 'grid-item-animation-fade', '75');
		$this->grid_item_other_fade = $base->getVar($params, 'grid-item-other-fade', '75');

		$this->grid_item_animation_blur = $base->getVar($params, 'grid-item-animation-blur', '5');
		$this->grid_item_other_blur = $base->getVar($params, 'grid-item-other-blur', '5');

		$this->grid_item_animation_shift = $base->getVar($params, 'grid-item-animation-shift', 'top');
		$this->grid_item_other_shift = $base->getVar($params, 'grid-item-other-shift', 'top');

		$this->grid_item_animation_shift_amount = $base->getVar($params, 'grid-item-animation-shift-amount', '10');
		$this->grid_item_other_shift_amount = $base->getVar($params, 'grid-item-other-shift-amount', '10');

		$this->grid_item_animation_rotate = $base->getVar($params, 'grid-item-animation-rotate', '30');
		$this->grid_item_other_rotate = $base->getVar($params, 'grid-item-other-rotate', '30');
	}

	/**
	 * set default image
	 * @since: 1.2.0
	 */
	public function set_default_image($img) {
		$this->default_image = $img;
	}

	/**
	 * set YouTube default image by id
	 * @since: 2.1.0
	 */
	public function set_default_youtube_image_by_id($img_id) {
		$img = wp_get_attachment_image_src($img_id, 'full');
		if ($img !== false) {
			$this->default_youtube_image = $img[0];
		}
	}

	/**
	 * set YouTube default image by id
	 * @since: 2.1.0
	 */
	public function set_default_vimeo_image_by_id($img_id) {
		$img = wp_get_attachment_image_src($img_id, 'full');
		if ($img !== false) {
			$this->default_vimeo_image = $img[0];
		}
	}

	/**
	 * set YouTube default image by id
	 * @since: 2.1.0
	 */
	public function set_default_html_image_by_id($img_id) {
		$img = wp_get_attachment_image_src($img_id, 'full');
		if ($img !== false) {
			$this->default_html_image = $img[0];
		}
	}
	
	/**
	 * set demo image
	 */
	public function set_media_sources($sources) {
		$this->media_sources = apply_filters('essgrid_item_skin_set_media_sources', $sources, $this->grid_id, $this->grid_params);
	}
	
	/**
	 * set demo image
	 */
	public function set_media_sources_type($sources_type) {
		$this->media_sources_type = apply_filters('essgrid_item_skin_set_media_sources_type', $sources_type);
	}

	/**
	 * returns the Google fonts
	 */
	private function get_google_fonts() {
		return $this->google_fonts;
	}

	/**
	 * set google fonts
	 */
	private function import_google_fonts() {
		$base = new Essential_Grid_Base();
		$this->google_fonts = $base->getVar($this->params, 'google-fonts', []);
	}

	/**
	 * return if lightbox needs to be loaded
	 */
	public function do_lightbox_loading() {
		return $this->load_lightbox;
	}

	/**
	 * return if lightbox needs to be loaded
	 * 
	 * @param string $css_id
	 * @param array $params
	 *
	 * @return string
	 */
	public function output_lighbox_css($css_id, $params) {
		$base = new Essential_Grid_Base();
		$override_colors = $base->getVar($params, 'lightbox-override-ui-colors', 'off');
		if ('on' !== $override_colors) return '';
		
		$css = "
		.esgbox-container-%CSS_ID%.esgbox-is-open .esgbox-bg {opacity: 1}
		.esgbox-container-%CSS_ID% .esgbox-bg {background: %OVERLAY_COLOR% }
		.esgbox-container-%CSS_ID% .esgbox-button {background: %UI_BG_COLOR% !important}
		.esgbox-container-%CSS_ID% .esgbox-button svg path {fill: %UI_COLOR%}
		.esgbox-container-%CSS_ID% .esgbox-button:hover {background: %UI_HOVER_BG_COLOR% !important}
		.esgbox-container-%CSS_ID% .esgbox-button:hover svg path {fill: %UI_HOVER_COLOR%}
		.esgbox-container-%CSS_ID% .esgbox-caption {color: %UI_TEXT_COLOR%}
		";

		$overlay_color = $base->getVar($params, 'lightbox-overlay-bg-color', 'rgba(30,30,30,0.9)');
		$ui_bg_color = $base->getVar($params, 'lightbox-ui-bg-color', '#28303d');
		$ui_color = $base->getVar($params, 'lightbox-ui-color', '#ffffff');
		$ui_hover_bg_color = $base->getVar($params, 'lightbox-ui-hover-bg-color', '#000000');
		$ui_hover_color = $base->getVar($params, 'lightbox-ui-hover-color', '#ffffff');
		$ui_text_color = $base->getVar($params, 'lightbox-ui-text-color', '#eeeeee');
		
		$css = str_replace(
			['%CSS_ID%', '%OVERLAY_COLOR%', '%UI_BG_COLOR%', '%UI_COLOR%', '%UI_HOVER_BG_COLOR%', '%UI_HOVER_COLOR%', '%UI_TEXT_COLOR%'],
			[$css_id, $overlay_color, $ui_bg_color, $ui_color, $ui_hover_bg_color, $ui_hover_color, $ui_text_color],
			$css
		);

		$n = '<style>' . Essential_Grid_Base::compress_assets($css) . '</style>' . "\n";

		return apply_filters('essgrid_output_lighbox_css', $n, $params);
	}
	
	/**
	 * register google fonts to header
	 */
	public function register_google_fonts() {
		do_action('essgrid_item_skin_register_google_fonts', $this->google_fonts);
	}

	/**
	 * Check Advanced Rules of layer to see if it should be shown or not
	 * @since: 1.5.0
	 */
	public function check_advanced_rules($layer, $post) {
		$base = new Essential_Grid_Base();
		$link_meta = new Essential_Grid_Meta_Linking();
		$meta = new Essential_Grid_Meta();
		$m = $meta->get_all_meta(false);
		$lm = $link_meta->get_all_link_meta();

		$is_post = empty($this->layer_values);

		$rules = $base->getVar($layer, ['settings', 'adv-rules'], []);
		$show = $base->getVar($rules, 'ar-show', 'show');
		$logic = $base->getVar($rules, 'ar-logic', ['and', 'and', 'and', 'and', 'and', 'and']);
		$logic_glob = $base->getVar($rules, 'ar-logic-glob', ['and', 'and']);

		//define return values. They change depending on if we want to show or hide if values meet requirements
		$suc = $show == 'show';
		$fail = !$show;

		if (!empty($rules)) {

			foreach ($rules['ar-type'] as $key => $value) {
				$delete = false;
				switch ($value) {
					case 'meta':
						if (trim($rules['ar-meta'][$key]) == '')
							$delete = true;
						break;
					case 'off':
						$delete = true;
						break;
				}

				if ($delete === false) { 
					//check if operator between. If yes and value or value-2 empty, delete
					if ($rules['ar-operator'][$key] == 'between') {
						if (trim($rules['ar-value'][$key]) == '' || trim($rules['ar-value-2'][$key]) == '') $delete = true;
					}
				}

				if ($delete) {
					unset($rules['ar-value'][$key]);
					unset($rules['ar-operator'][$key]);
					unset($rules['ar-type'][$key]);
					unset($rules['ar-meta'][$key]);
					unset($rules['ar-value-2'][$key]);
				}
			}

			$results = [];
			if (!empty($rules['ar-type'])) {
				foreach ($rules['ar-type'] as $key => $value) {
					$my_val = '';
					switch ($value) {
						case 'meta':
							if ($is_post) {
								if (strpos($rules['ar-meta'][$key], 'eg-') === 0) {
									if (!empty($m)) {
										foreach ($m as $me) {
											if ('eg-' . $me['handle'] == $rules['ar-meta'][$key]) {
												$my_val = $meta->get_meta_value_by_handle($post['ID'], $rules['ar-meta'][$key]);
												break;
											}
										}
									}
								} elseif (strpos($rules['ar-meta'][$key], 'egl-') === 0) {
									if (!empty($lm)) {
										foreach ($lm as $me) {
											if ('egl-' . $me['handle'] == $rules['ar-meta'][$key]) {
												$my_val = $link_meta->get_link_meta_value_by_handle($post['ID'], $rules['ar-meta'][$key]);
												break;
											}
										}
									}
								} else {
									$my_val = get_post_meta($post['ID'], $rules['ar-meta'][$key], true);
								}
							} else {
								$my_val = @$this->layer_values[$rules['ar-meta'][$key]];
							}
							break;

						case 'featured-image':
						case 'alternate-image':
						case 'content-image':
						case 'youtube':
						case 'vimeo':
						case 'wistia':
						case 'soundcloud':
						case 'content-youtube':
						case 'content-vimeo':
						case 'content-wistia':
						case 'content-soundcloud':
						case 'iframe':
						case 'content-iframe':
							if ($this->item_media_type == $value) {
								$my_val = @$this->media_sources[$value];
							}
							break;
						case 'html5':
						case 'content-html5':
							if ($this->item_media_type == $value) {
								$my_val = @$this->media_sources[$value]['mp4'] . @$this->media_sources[$value]['webm'] . @$this->media_sources[$value]['ogv'];
							}
							break;

						default:
							if ($this->item_media_type == $value) {
								$my_val = apply_filters('essgrid_set_media_source', $my_val, $value, @$this->media_sources);
							}
							break;
					}

					switch ($rules['ar-operator'][$key]) {
						case 'lt':
							$results[$key] = $my_val < $rules['ar-value'][$key];
							break;
						case 'lte':
							$results[$key] = $my_val <= $rules['ar-value'][$key];
							break;
						case 'gt':
							$results[$key] = $my_val > $rules['ar-value'][$key];
							break;
						case 'gte':
							$results[$key] = $my_val >= $rules['ar-value'][$key];
							break;
						case 'equal':
							$results[$key] = $my_val === $rules['ar-value'][$key];
							break;
						case 'notequal':
							$results[$key] = $my_val !== $rules['ar-value'][$key];
							break;
						case 'between':
							$results[$key] = $my_val > $rules['ar-value'][$key] && $my_val < $rules['ar-value-2'][$key];
							break;
						case 'isset':
							$results[$key] = trim($my_val) !== '' || !empty($my_val);
							break;
						case 'empty':
							$results[$key] = trim($my_val) === '';
							break;
					}
				}
			}

			if (!empty($results)) {
				$part = [];
				$pnr = 0;
				$log = 0;

				for ($i = 0; $i < 9; $i = $i + 3) {
					$first = isset($results[$i]);
					$second = isset($results[$i + 1]);
					$third = isset($results[$i + 2]);

					if ($first && $second) {
						if ($third) { //all three exist
							if ($logic[$log] == 'and' && $logic[$log + 1] == 'and') {
								$part[$pnr] = $results[$i] === true && $results[$i + 1] === true && $results[$i + 2] === true;
							} elseif ($logic[$log] == 'and' && $logic[$log + 1] == 'or') {
								$part[$pnr] = $results[$i] === true && $results[$i + 1] === true || $results[$i + 2] === true;
							} elseif ($logic[$log] == 'or' && $logic[$log + 1] == 'and') {
								$part[$pnr] = $results[$i] === true || $results[$i + 1] === true && $results[$i + 2] === true;
							} elseif ($logic[$log] == 'or' && $logic[$log + 1] == 'or') {
								$part[$pnr] = $results[$i] === true || $results[$i + 1] === true || $results[$i + 2] === true;
							}
						} else { 
							//only first and second exist
							if ($logic[$log] == 'and') {
								$part[$pnr] = $results[$i] === true && $results[$i + 1] === true;
							} else {
								$part[$pnr] = $results[$i] === true || $results[$i + 1] === true;
							}
						}
					} else {
						if ($first) {
							if ($third) {
								if ($logic[$log + 1] == 'and') {
									$part[$pnr] = $results[$i] === true && $results[$i + 2] === true;
								} else {
									$part[$pnr] = $results[$i] === true || $results[$i + 2] === true;
								}
							} else { 
								//only first exist
								$part[$pnr] = $results[$i] === true;
							}
						} elseif ($second) {
							if ($third) {
								if ($logic[$log + 1] == 'and') {
									$part[$pnr] = $results[$i + 1] === true && $results[$i + 2] === true;
								} else {
									$part[$pnr] = $results[$i + 1] === true || $results[$i + 2] === true;
								}
							} else { 
								//only second exist
								$part[$pnr] = $results[$i + 1] === true;
							}
						} elseif ($third) { 
							//only third exists
							$part[$pnr] = $results[$i + 2] === true;
						}
					}

					$pnr++;
					$log += 2;

				}

				if (!empty($part)) {
					//start the && and || operations here
					if (isset($part[0]) && isset($part[1]) && isset($part[2])) { 
						//all three exist
						if ($logic_glob[0] == 'and' && $logic[1] == 'and') {
							return ($part[0] === true && $part[1] === true && $part[2] === true) ? $suc : $fail;
						} elseif ($logic[0] == 'and' && $logic[1] == 'or') {
							return ($part[0] === true && $part[1] === true || $part[2] === true) ? $suc : $fail;
						} elseif ($logic[0] == 'or' && $logic[1] == 'and') {
							return ($part[0] === true || $part[1] === true && $part[2] === true) ? $suc : $fail;
						} elseif ($logic[0] == 'or' && $logic[1] == 'or') {
							return ($part[0] === true || $part[1] === true || $part[2] === true) ? $suc : $fail;
						}
					} elseif (isset($part[0]) && isset($part[1])) { 
						//first two
						if ($logic_glob[0] == 'and') {
							return ($part[0] === true && $part[1] === true) ? $suc : $fail;
						} else {
							return ($part[0] === true || $part[1] === true) ? $suc : $fail;
						}
					} elseif (isset($part[0]) && isset($part[2])) { 
						//first and last
						if ($logic_glob[1] == 'and') {
							return ($part[0] === true && $part[2] === true) ? $suc : $fail;
						} else {
							return ($part[0] === true || $part[2] === true) ? $suc : $fail;
						}
					} elseif (isset($part[1]) && isset($part[2])) { 
						//second and last
						if ($logic_glob[1] == 'and') {
							return ($part[1] === true && $part[2] === true) ? $suc : $fail;
						} else {
							return ($part[1] === true || $part[2] === true) ? $suc : $fail;
						}
					} elseif (isset($part[0])) { 
						//only first
						return ($part[0] === true) ? $suc : $fail;
					} elseif (isset($part[1])) { 
						//only second
						return ($part[1] === true) ? $suc : $fail;
					} elseif (isset($part[2])) { 
						//only third
						return ($part[2] === true) ? $suc : $fail;
					}
				}
				return $fail;
			}
		}
		return $suc;
	}

	/**
	 * insert layer
	 */
	public function insert_layer($layer, $demo = false, $masonry = false, $grid_ids = '', $post_ids = '') {
		$base = new Essential_Grid_Base();
		$m = new Essential_Grid_Meta();

		$is_post = empty($this->layer_values);

		if ($demo === false) {
			$post = $this->post;
		} else {
			$post['ID'] = '0'; //set default if we are in demo mode
		}

		//check advanced rules
		$show = $this->check_advanced_rules($layer, $post);
		if ($show === false) return false;

		$position = $base->getVar($layer, 'container', 'tl');
		switch ($position) {
			case 'br' :
				$class = 'bottom';
				break;
			case 'c' :
				$class = 'center';
				break;
			case 'm' :
				$class = 'content';
				break;
			case 'tl' :
			default :
				$class = 'top';
		}

		if (!isset($layer['settings'])) return false;

		$this->register_layer_css($layer, $demo);

		if (isset($layer['id'])) $unique_class = 'eg-' . esc_attr($this->handle) . '-element-' . esc_attr($layer['id']);
		else $unique_class = '';

		$special_item = $base->getVar($layer, ['settings', 'special'], 'false');
		$special_item_type = $base->getVar($layer, ['settings', 'special-type'], 'line-break');

		//check if absolute positioned, remove class depending on it
		$absolute = $this->is_absolute_position($unique_class);
		if ($absolute) {
			$class = 'absolute';
		}

		$hideunderHTML = '';
		$hideunderClass = '';
		$hideunder = $base->getVar($layer, ['settings', 'hideunder'], 0, 'i');
		$hideunderheight = $base->getVar($layer, ['settings', 'hideunderheight'], 0, 'i');
		$hideundertype = $base->getVar($layer, ['settings', 'hidetype'], 'visibility');

		if ($hideunder > 0) {
			$hideunderHTML .= ' data-hideunder="' . esc_attr($hideunder) . '"';
			$hideunderClass = ' eg-handlehideunder ';
		}

		if ($hideunderheight > 0) {
			$hideunderHTML .= ' data-hideunderheight="' . esc_attr($hideunderheight) . '"';
			$hideunderClass = ' eg-handlehideunder ';
		}

		if ($hideunderHTML !== '') {
			$hideunderHTML .= ' data-hidetype="' . esc_attr($hideundertype) . '"';
		}

		$delay = '';
		$duration = '';
		$transition_split = '';
		
		if ($masonry) {
			$transition = '';
			$data_transition_transition = '';
		} else {
			$transition = 'esg-' . esc_attr($base->getVar($layer, ['settings', 'transition'], 'fade')) . esc_attr($base->getVar($layer, ['settings', 'transition-type']));

			if (isset($layer['id'])) $meta_tran = esc_attr($this->get_meta_element_change($layer['id'], 'transition')); //check if we have meta transition set
			else $meta_tran = false;
			if ($meta_tran !== false && trim($meta_tran) !== '') $transition = ' esg-' . $meta_tran;

			if ($transition == 'esg-none' || $transition == 'esg-noneout' || $base->getVar($layer, ['settings', 'transition-type']) == 'always') { 
				//no transition
				$transition = '';
			} else {
				$delay = ' data-delay="' . esc_attr(round($base->getVar($layer, ['settings', 'delay'], 0) / 100, 2)) . '"';
				$duration = ' data-duration="' . esc_attr($base->getVar($layer, ['settings', 'duration'], 'default')) . '"';

				if (isset($layer['id'])) $meta_tran_delay = $this->get_meta_element_change($layer['id'], 'transition-delay'); //check if we have meta transition-delay set
				else $meta_tran_delay = false;
				if ($meta_tran_delay !== false)
					$delay = ' data-delay="' . esc_attr(round($meta_tran_delay / 100, 2)) . '"';
			}

			// 2.2.5
			if ($transition) {
				$data_transition_transition = ' data-transition="' . esc_attr(trim($transition)) . '"';
				$transition = ' esg-transition';
			} else {
				$data_transition_transition = '';
			}
		}

		$text = '';

		$do_limit = true;
		$do_ignore_styles = false;
		$is_woo_cats = false;
		$is_woo_button = false;
		$is_html_source = false;
		$is_filter_cat = false;
		$demo_element_type = ' data-custom-type="%s"';
		$separator = ',';

		if (isset($layer['settings']['source'])) {
			$separator = $base->getVar($layer, ['settings', 'source-separate'], ',');
			$catmax = $base->getVar($layer, ['settings', 'source-catmax'], '-1');
			$meta = $base->getVar($layer, ['settings', 'source-meta']);
			$func = $base->getVar($layer, ['settings', 'source-function'], 'link');
			$taxonomy = $base->getVar($layer, ['settings', 'source-taxonomy']);

			switch ($layer['settings']['source']) {
				case 'post':
					if ($demo === false) {
						if ($is_post)
							$text = $this->get_post_value($layer['settings']['source-post'], $separator, $func, $meta, $catmax, $taxonomy);
						else
							$text = $this->get_custom_element_value($layer['settings']['source-post'], $separator, $meta);
						if ($func == 'filter') $is_filter_cat = true;
					} elseif ($demo === 'custom') {
						$text = $this->get_custom_element_value($layer['settings']['source-post'], $separator, $meta);
					} else {
						$post_text = Essential_Grid_Item_Element::getPostElementsArray();
						if (array_key_exists(@$layer['settings']['source-post'], $post_text)) $text = $post_text[@$layer['settings']['source-post']]['name'];

						if ($layer['settings']['source-post'] == 'date') {
							$da = get_option('date_format');
							if ($da !== false)
								$text = gmdate(get_option('date_format'));
							else
								$text = gmdate('Y.m.d');
						}
					}
					$demo_element_type = str_replace('%s', $layer['settings']['source-post'], $demo_element_type);

					if ($layer['settings']['source-post'] == 'cat_list' || $layer['settings']['source-post'] == 'tag_list') { //no limiting if category or tag list
						$do_limit = false;
					}
					$text = apply_filters('essgrid_post_meta_content', $text, $layer['settings']['source-post'], $base->getVar($this->post, 'ID'), $this->post);
					break;
					
				case 'event':
					if ($demo !== false) {
						$event = Essential_Grid_Item_Element::getEventElementsArray();
						if (array_key_exists(@$layer['settings']['source-event'], $event)) $text = $event[@$layer['settings']['source-event']]['name'];
					}
					$demo_element_type = str_replace('%s', $layer['settings']['source-event'], $demo_element_type);
					break;
					
				case 'woocommerce':
					//check if woocommerce is installed
					if ($demo === false) {
						if (Essential_Grid_Woocommerce::is_woo_exists()) {
							if ($is_post)
								$text = $this->get_woocommerce_value($layer['settings']['source-woocommerce'], $separator, $catmax);
							else
								$text = $this->get_custom_element_value($layer['settings']['source-woocommerce'], $separator);

							if ($layer['settings']['source-woocommerce'] == 'wc_categories') {
								$do_limit = false;
								$is_woo_cats = true;
							} elseif ($layer['settings']['source-woocommerce'] == 'wc_add_to_cart_button') {
								$do_limit = false;
								$is_woo_button = true;
							}
						}
					} elseif ($demo === 'custom') {
						if (Essential_Grid_Woocommerce::is_woo_exists()) {
							$text = $this->get_custom_element_value($layer['settings']['source-woocommerce'], $separator);

							if ($layer['settings']['source-woocommerce'] == 'wc_categories') {
								$do_limit = false;
								$is_woo_cats = true;
							} elseif ($layer['settings']['source-woocommerce'] == 'wc_add_to_cart_button') {
								$do_limit = false;
								$is_woo_button = true;
							}

						}
					} else {
						if (Essential_Grid_Woocommerce::is_woo_exists()) {
							$tmp_wc = Essential_Grid_Woocommerce::get_meta_array();
							$woocommerce = [];
							foreach ($tmp_wc as $handle => $name) {
								$woocommerce[$handle]['name'] = $name;
							}
							if (array_key_exists(@$layer['settings']['source-woocommerce'], $woocommerce)) $text = $woocommerce[@$layer['settings']['source-woocommerce']]['name'];
						}
					}
					$demo_element_type = str_replace('%s', $layer['settings']['source-woocommerce'], $demo_element_type);
					break;
					
				case 'icon':
					$text = '<i class="' . esc_attr(@$layer['settings']['source-icon']) . '"></i>';
					$demo_element_type = '';
					break;
					
				case 'text':
					$text = @$layer['settings']['source-text'];
					//check for metas by %meta%
					if ($is_post) {
						if (!empty($this->post['ID'])) $text = $m->replace_all_meta_in_text($this->post['ID'], $text, $layer);
					} else {
						$_a = (!empty($this->layer_values)) ? $this->layer_values : [];
						$_b = (!empty($this->media_sources)) ? $this->media_sources : [];
						$_values = array_merge($_a, $_b);
						$text = $m->replace_all_custom_element_meta_in_text($_values, $text);
					}

					// remove any meta left in text after replace
					$text = $m->remove_meta_in_text($text);
					
					//run shortcodes before apply libxml fix, because it breaks shortcodes in attributes
					$text = do_shortcode($text);

					// Fix html tags
					libxml_use_internal_errors(true);
					$text_xml = "";
					if (class_exists('DOMDocument') && defined('LIBXML_HTML_NOIMPLIED') && defined('LIBXML_HTML_NODEFDTD')) {
						$dom = new DOMDocument();
						$dom->loadHTML('<html>' . mb_encode_numericentity($text, array(0x80, 0xffff, 0, 0xffff), 'UTF-8') . '</html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
						$text_xml = substr($dom->saveHTML(), 6, -8);
					}
					if (!empty($text_xml)) $text = $text_xml;

					// added so that Item Skin Editor can be still used even if wrong formated HTML was added as a layer
					if ($demo == 'overview' || $demo == 'skinchoose' || $demo == 'skin-editor') $text = esc_attr($text);

					if (isset($layer['settings']['source-text-style-disable']) && @$layer['settings']['source-text-style-disable'])
						$do_ignore_styles = true;

					$demo_element_type = '';
					$is_html_source = true;
					break;
					
				default:
					$demo_element_type = '';
			}

			if ($do_limit) {
				$limit_by = $base->getVar($layer, ['settings', 'limit-type'], 'none');
				if ($limit_by !== 'none') {
					switch ($layer['settings']['source']) {
						case 'post':
						case 'event':
						case 'woocommerce':
							if (!in_array($base->getVar($layer, ['settings', 'source-post']), ['taxonomy', 'cat_list', 'tag_list']))
								$text = $base->get_text_intro($text, $base->getVar($layer, ['settings', 'limit-num'], 10, 'i'), $limit_by);
							break;
					}
				}
			}

			// 2.2.6
			$min_height = $base->getVar($layer, ['settings', 'min-height'], '0');
			$max_height = $base->getVar($layer, ['settings', 'max-height'], 'none');

			if ($min_height != '0' || $max_height !== 'none') {
				$span = '<span style="display: block;';
				if ($min_height != '0') {
					$min_height = intval($min_height);
					$span .= 'min-height: ' . $min_height . 'px;';
				}
				if ($max_height != 'none') {
					$max_height = intval($max_height);
					$span .= 'overflow: hidden; height: ' . $max_height . 'px; max-height: ' . $max_height . 'px';
				}
				$text = $span . '">' . $text . '</span>';
			}
		}

		$link_to = $base->getVar($layer, ['settings', 'link-type'], 'none');
		$link_target = $base->getVar($layer, ['settings', 'link-target'], '_self');
		if ($link_target !== 'disabled')
			$link_target = ' target="' . esc_attr($link_target) . '"';
		else
			$link_target = '';
		$link_rel_nofollow = $base->getVar($layer, ['settings', 'rel-nofollow'], 'false');
		if ($link_rel_nofollow === 'true')
			$link_rel_nofollow = ' rel="nofollow"';
		else
			$link_rel_nofollow = '';

		$video_play = '';
		$ajax_class = '';
		$ajax_attr = '';
		$lb_class = '';

		switch ($link_to) {
			case 'post':
				if ($demo === false) {
					if ($is_post) {
						$text = '<a href="' . get_permalink($post['ID']) . '"' . $link_target . $link_rel_nofollow . '>' . $text . '</a>';
					} else {
						$get_link = $this->get_custom_element_value('post-link', $separator); //get the post link

						/* 2.1.5 */
						// fix for grids populated with WP Media Galleries
						if ($get_link == '') {
							if (isset($this->layer_values['custom-image']) && !empty($this->layer_values['custom-image'])) {
								$text = '<a href="' . get_permalink($this->layer_values['custom-image']) . '"' . $link_target . $link_rel_nofollow . '>' . $text . '</a>';
							} else {
								$text = '<a href="javascript:void(0);"' . $link_target . $link_rel_nofollow . '>' . $text . '</a>';
							}
						} else {
							$text = '<a href="' . esc_attr($this->normalize_link($get_link)) . '"' . $link_target . $link_rel_nofollow . '>' . preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $text) . '</a>';
						}
					}
				} else {
					$text = '<a href="javascript:void(0);"' . $link_target . $link_rel_nofollow . '>' . $text . '</a>';
				}
				break;
				
			case 'url':
				$lurl = $base->getVar($layer, ['settings', 'link-type-url'], 'javascript:void(0);');
				$text = '<a href="' . esc_attr($this->normalize_link($lurl)) . '"' . $link_target . $link_rel_nofollow . '>' . $text . '</a>';
				break;
				
			case 'meta':
				if ($demo === false) {
					if ($is_post) {
						$meta_key = $base->getVar($layer, ['settings', 'link-type-meta'], 'javascript:void(0);');
						$meta_link = $m->get_meta_value_by_handle($post['ID'], $meta_key);
						if ($meta_link == '') {
							// if empty, link to nothing
							$text = '<a href="javascript:void(0);"' . $link_target . $link_rel_nofollow . '>' . $text . '</a>';
						} else {
							$text = '<a href="' . esc_attr($this->normalize_link($meta_link)) . '"' . $link_target . $link_rel_nofollow . '>' . $text . '</a>';
						}
					} else {
						$meta_key = $base->getVar($layer, ['settings', 'link-type-meta']);
						//get the post link
						$get_link = $this->get_custom_element_value('post-link', $separator, $meta_key);
						if ($get_link == '') {
							$text = '<a href="javascript:void(0);"' . $link_target . $link_rel_nofollow . '>' . $text . '</a>';
						} else {
							$text = '<a href="' . esc_attr($this->normalize_link($get_link)) . '"' . $link_target . $link_rel_nofollow . '>' . $text . '</a>';
						}
					}
				} else {
					$text = '<a href="javascript:void(0);"' . $link_target . $link_rel_nofollow . '>' . $text . '</a>';
				}
				break;
				
			case 'javascript':
				$text = '<a href="javascript:' . esc_attr($base->getVar($layer, ['settings', 'link-type-javascript'], 'void(0);')) . '"' . $link_target . $link_rel_nofollow . '>' . $text . '</a>'; //javascript-link
				break;
				
			case 'lightbox':
				$opt = get_option('tp_eg_use_lightbox', 'false');
				if ($opt !== 'disabled') { 
					//enqueue only if default LightBox is selected
					wp_enqueue_script('esg-tp-boxext');
					wp_enqueue_style('esg-tp-boxextcss');
				}

				$lb_get_lightbox = ''; // media url to pass to get_lightbox_* functions if img from content
				$lb_source = 'javascript:void(0);';
				$lb_addition = '';
				$lb_rel = ($this->lb_rel !== false) ? ' data-esgbox="' . esc_attr($this->lb_rel) . '"' : '';
				$lb_data = '';
				$lb_featured = '';
				$lb_post_title = '';
				$lb_owidth = '';
				$lb_oheight = '';

				if (!empty($this->default_lightbox_source_order)) { 
					//only show if something is checked
					foreach ($this->default_lightbox_source_order as $order) { 
						//go through the order and set media as wished
						$val = isset($this->media_sources[$order]) && $this->media_sources[$order] !== '' && $this->media_sources[$order] !== false;
						if ($order === 'post-content' || !empty($val)) { 
							//found entry

							$do_continue = false;
							$is_video = false;

							if (!empty($this->lightbox_additions['items']) && $this->lightbox_additions['base'] == 'on') {
								$lb_get_lightbox = $lb_source = $this->lightbox_additions['items'][0];
								$lb_class = ' esgbox';
							} else {

								switch ($order) {
									case 'featured-image':
									case 'alternate-image':
									case 'content-image':

										// 2.2.5
										$imgsource = explode('-', $order);
										$imgsource = $imgsource[0];

										if ($order == 'content-image') $lb_source = $this->media_sources[$order];
										else $lb_source = $this->media_sources[$order . '-full'];

										//update lightbox thumb
										$this->lightbox_thumbnail = $lb_source;
									
										$lb_class = ' esgbox';

										if (isset($this->media_sources[$imgsource . '-image-full-width'])) $lb_owidth = ' data-width="' . esc_attr($this->media_sources[$imgsource . '-image-full-width']) . '" ';
										if (isset($this->media_sources[$imgsource . '-image-full-height'])) $lb_oheight = ' data-height="' . esc_attr($this->media_sources[$imgsource . '-image-full-height']) . '" ';

										break;
									case 'youtube':
										$http = (is_ssl()) ? 'https' : 'http';
										$enable_youtube_nocookie = get_option('tp_eg_enable_youtube_nocookie', 'false');
										$lb_source = $enable_youtube_nocookie != 'false' ? $http . '://www.youtube-nocookie.com/embed/' . $this->media_sources[$order] : $http . '://www.youtube.com/watch?v=' . $this->media_sources[$order];

										$lb_class = ' esgbox';
										$is_video = true;
										$lb_addition = ($this->video_ratios['youtube'] == '1') ? '' : ' data-ratio="4:3"';
										break;
									case 'vimeo':
										$http = (is_ssl()) ? 'https' : 'http';
										$lb_source = $http . '://vimeo.com/' . $this->media_sources[$order];
										$lb_class = ' esgbox';
										$is_video = true;
										$lb_addition = ($this->video_ratios['vimeo'] == '1') ? '' : ' data-ratio="4:3"';
										break;
									case 'wistia':
										$lb_source = '//fast.wistia.net/embed/iframe/' . $this->media_sources[$order];
										$lb_class = ' esgbox';
										$lb_data .= ' data-type="iframe"';
										$lb_addition = ($this->video_ratios['wistia'] == '1') ? '' : ' data-ratio="4:3"';
										break;
									case 'soundcloud':
										$lb_source = '//w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/' . $this->media_sources[$order] . '&amp;color=%23ff5500&amp;auto_play=true&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;show_teaser=true&amp;visual=true';
										$lb_class = ' esgbox';
										$lb_data .= ' data-type="iframe"';
										break;
									case 'iframe':
										$lb_source = addslashes($this->media_sources[$order]);
										$lb_class = ' esgbox';
										$lb_data .= ' data-type="iframe"';

										break;
									case 'html5':
										if (trim($this->media_sources[$order]['mp4']) === '' && trim($this->media_sources[$order]['ogv']) === '' && trim($this->media_sources[$order]['webm'] === '')) {
											$do_continue = true;
										} else {
											//check for video poster image
											$video_poster_src = '';
											if (!empty($this->default_video_poster_order)) {
												foreach ($this->default_video_poster_order as $n_order) {
													if ($n_order == 'no-image') { 
														//do not show image so set image empty
														break;
													}
													if (isset($this->media_sources[$n_order]) && $this->media_sources[$n_order] !== '' && $this->media_sources[$n_order] !== false) { //found entry
														$video_poster_src = $this->media_sources[$n_order];
														break;
													}
												}
											}

											$lb_mp4 = $this->media_sources[$order]['mp4'];
											$lb_ogv = $this->media_sources[$order]['ogv'];
											$lb_webm = $this->media_sources[$order]['webm'];
											$vid_ratio = ($this->video_ratios['html5'] == '1') ? '' : ' data-ratio="4:3"';

											$lb_source = ""; //Leave it Empty, other way HTML5 Video will not work !!

											if (!empty($lb_mp4)) {
												$lb_source = $lb_mp4;
											} elseif (!empty($lb_ogv)) {
												$lb_source = $lb_ogv;
											} elseif (!empty($lb_webm)) {
												$lb_source = $lb_webm;
											}

											// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage 
											$text = '<img class="esg-display-none" src="' . esc_attr($video_poster_src) . '" />' . $text;
											$lb_class = ' esgbox esgboxhtml5';
											$lb_addition = ' data-mp4="' . esc_attr($lb_mp4) . '" data-ogv="' . esc_attr($lb_ogv) . '" data-webm="' . esc_attr($lb_webm) . '"' . $vid_ratio;
											$is_video = true;
										}
										break;

									case 'revslider':
										$lb_source = 'javascript:void(0);';
										$lb_class = ' esgbox esgbox-post';
										$lb_data = ' data-post="' . esc_attr($post_ids) . '"';
										$lb_data .= ' data-revslider="' . esc_attr($this->media_sources[$order]) . '"';
										$lb_data .= ' data-gridid="' . esc_attr($grid_ids) . '" data-ispost="' . esc_attr($is_post) . '"';
										break;

									case 'essgrid':
										$lb_source = 'javascript:void(0);';
										$lb_class = ' esgbox esgbox-post';
										$lb_data = ' data-post="' . esc_attr($post_ids) . '"';
										$lb_data .= ' data-lbesg="' . esc_attr($this->media_sources[$order]) . '"';
										$lb_data .= ' data-gridid="' . esc_attr($grid_ids) . '" data-ispost="' . esc_attr($is_post) . '"';
										break;

									case 'post-content':
										$lb_source = 'javascript:void(0);';
										$lb_class = ' esgbox esgbox-post';
										$lb_data = ' data-post="' . esc_attr($post_ids) . '"';
										$lb_data .= ' data-gridid="' . esc_attr($grid_ids) . '" data-ispost="' . esc_attr($is_post) . '"';
										$lb_post_title = $is_post ? $base->getVar($this->post, 'post_title') : $this->get_custom_element_value('title', $separator);
										$lb_post_title = ' data-posttitle="' . esc_attr($lb_post_title) . '"';

										// if featured full is available
										if (isset($this->media_sources['featured-image-full']) && !empty($this->media_sources['featured-image-full'])) {
											$lb_featured = ' data-featured="' . esc_attr($this->media_sources['featured-image-full']) . '"';
										} // if featured regular size is available
										else if (isset($this->media_sources['featured-image']) && !empty($this->media_sources['featured-image'])) {
											$lb_featured = ' data-featured="' . esc_attr($this->media_sources['featured-image']) . '"';
										} // if global image is available
										else if (!empty($this->default_image)) {
											$lb_featured = ' data-featured="' . esc_attr($this->default_image) . '"';
										}
										break;

									default:
										$do_continue = true;
										break;

								}
							}

							if ($do_continue) {
								continue;
							}

							if ($base->getVar($layer, ['settings', 'show-on-lightbox-video'], 'false') == 'true' && $is_video === false) {
								return false; //this element is hidden if media is video
							}
							if ($base->getVar($layer, ['settings', 'show-on-lightbox-video'], 'false') == 'hide' && $is_video === true) {
								return false; //this element is hidden if media is video
							}

							break;
						}

						/* 2.1.5 */
						if ($order === 'featured-image') {
							$default_img = $this->default_image;
							if (!empty($default_img)) {
								$lb_source = $default_img;
								$lb_class = ' esgbox';
								$lb_owidth = ' data-width="' . esc_attr($this->default_image_attr[0]) . '" ';
								$lb_oheight = ' data-height="' . esc_attr($this->default_image_attr[1]) . '" ';
								break;
							}
						}

					}
				}

				$lb_caption = $this->get_lightbox_caption($demo, $is_post, $lb_get_lightbox);
				$text = '<a data-elementor-open-lightbox="yes" data-thumb="' . esc_url(esg_aq_resize($this->lightbox_thumbnail, 200)) . '" href="' . esc_url($lb_source) . '"' . $lb_addition . $lb_caption . $lb_owidth . $lb_oheight . $lb_rel . $lb_data . $lb_featured . $lb_post_title . '>' . $text . '</a>';

				$this->load_lightbox = true; //set that jQuery is written
				break;
				
			case 'embedded_video':
				$video_play = ' esg-click-to-play-video';
				break;
				
			case 'ajax':
				if (!empty($this->default_ajax_source_order)) { 
					//only show if something is checked
					$ajax_class = ' eg-ajaxclicklistener';
					foreach ($this->default_ajax_source_order as $order) { 
						//go through the order and set media as wished
						$do_continue = false;
						if (isset($this->media_sources[$order]) && $this->media_sources[$order] !== '' && $this->media_sources[$order] !== false || $order == 'post-content') { 
							// found entry
							switch ($order) {
								case 'youtube':
									$vid_ratio = ($this->video_ratios['youtube'] == '0') ? '4:3' : '16:9';
									$ajax_attr = ' data-ajaxtype="youtubeid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
									$ajax_attr .= ' data-ajaxsource="' . esc_attr($this->media_sources[$order]) . '"'; //depending on type
									$ajax_attr .= ' data-ajaxvideoaspect="' . $vid_ratio . '"'; //depending on type
									break;
									
								case 'vimeo':
									$vid_ratio = ($this->video_ratios['vimeo'] == '0') ? '4:3' : '16:9';
									$ajax_attr = ' data-ajaxtype="vimeoid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
									$ajax_attr .= ' data-ajaxsource="' . esc_attr($this->media_sources[$order]) . '"'; //depending on type
									$ajax_attr .= ' data-ajaxvideoaspect="' . $vid_ratio . '"'; //depending on type
									break;
									
								case 'wistia':
									$vid_ratio = ($this->video_ratios['wistia'] == '0') ? '4:3' : '16:9';
									$ajax_attr = ' data-ajaxtype="wistiaid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
									$ajax_attr .= ' data-ajaxsource="' . esc_attr($this->media_sources[$order]) . '"'; //depending on type
									$ajax_attr .= ' data-ajaxvideoaspect="' . $vid_ratio . '"'; //depending on type
									break;
									
								case 'html5':
									if ($this->media_sources[$order]['mp4'] == ''
										&& $this->media_sources[$order]['webm'] == ''
										&& $this->media_sources[$order]['ogv'] == '') {
										$do_continue = true;
									} else {
										//mp4/webm/ogv
										$vid_ratio = ($this->video_ratios['html5'] == '0') ? '4:3' : '16:9';
										$ajax_attr = ' data-ajaxtype="html5vid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
										$ajax_attr .= ' data-ajaxsource="';
										$ajax_attr .= esc_attr(@$this->media_sources[$order]['mp4']) . '|';
										$ajax_attr .= esc_attr(@$this->media_sources[$order]['webm']) . '|';
										$ajax_attr .= esc_attr(@$this->media_sources[$order]['ogv']);
										$ajax_attr .= '"';
										$ajax_attr .= ' data-ajaxvideoaspect="' . $vid_ratio . '"'; //depending on type
									}
									break;
									
								case 'soundcloud':
									$ajax_attr = ' data-ajaxtype="soundcloudid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
									$ajax_attr .= ' data-ajaxsource="' . esc_attr($this->media_sources[$order]) . '"'; //depending on type
									break;
									
								case 'post-content':
									if ($is_post) {
										$ajax_attr = ' data-ajaxtype="postid"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
										$ajax_attr .= ' data-ajaxsource="' . esc_attr(@$this->post['ID']) . '"'; //depending on type
									} else {
										$do_continue = true;
										//$ajax_class = '';
									}
									break;
									
								case 'featured-image':
								case 'alternate-image':
								case 'content-image':
									if ($order == 'content-image')
										$img_url = $this->media_sources[$order];
									else
										$img_url = $this->media_sources[$order . '-full'];

									$ajax_attr = ' data-ajaxtype="imageurl"'; // postid, html5vid youtubeid vimeoid soundcloud revslider
									$ajax_attr .= ' data-ajaxsource="' . esc_attr($img_url) . '"'; // depending on type
									break;
									
								default:
									$ajax_class = '';
									$do_continue = true;
									break;
							}
						} else { 
							//some custom entry maybe
							$postobj = ($is_post) ? $this->post : false;
							$ajax_attr = apply_filters('essgrid_handle_ajax_content', $order, $this->media_sources, $postobj, $this->grid_id);
							if (empty($ajax_attr)) {
								$do_continue = true;
							}

						}
						if ($do_continue) {
							continue;
						}
						break;
					}
				}

				if ($ajax_class !== '') { 
					//set ajax loading to true so that the grid can decide to put ajax container in top/bottom
					$this->ajax_loading = true;
				}

				break;
			case 'sharefacebook':
				if (isset($layer['settings']['link-type-sharefacebook'])) {
					switch ($layer['settings']['link-type-sharefacebook']) {
						case 'custom':
							$facebook_share_url = $layer['settings']['link-type-sharefacebook-custom-url'];
							break;
							
						case 'site':
							$facebook_share_url = get_permalink();
							break;
							
						default:
							if ($is_post) {
								$facebook_share_url = get_permalink($post['ID']);
							} else {
								$get_link = $this->get_custom_element_value('post-link', $separator);
								$facebook_share_url = $get_link;
							}
							break;
					}
				} else {
					if ($is_post) {
						$facebook_share_url = get_permalink($post['ID']);
					} else {
						$get_link = $this->get_custom_element_value('post-link', $separator);
						$facebook_share_url = $get_link;
					}
				}
				$text = '<a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode($facebook_share_url) . '" target="_blank" rel=nofollow>' . $text . '</a>';
				break;
				
			case 'sharegplus':
				if (isset($layer['settings']['link-type-sharegplus'])) {
					switch ($layer['settings']['link-type-sharegplus']) {
						case 'custom':
							$gplus_share_url = $layer['settings']['link-type-sharegplus-custom-url'];
							break;
							
						case 'site':
							$gplus_share_url = get_permalink();
							break;
							
						default:
							if ($is_post) {
								$gplus_share_url = get_permalink($post['ID']);
							} else {
								$get_link = $this->get_custom_element_value('post-link', $separator);
								$gplus_share_url = $get_link;
							}
							break;
					}
				} else {
					if ($is_post) {
						$gplus_share_url = get_permalink($post['ID']);
					} else {
						$get_link = $this->get_custom_element_value('post-link', $separator);
						$gplus_share_url = $get_link;
					}
				}
				$text = '<a href="https://plus.google.com/share?url=' . urlencode($gplus_share_url) . '" target="_blank" rel=nofollow>' . $text . '</a>';
				break;
				
			case 'sharepinterest':
				$title = $excerpt = $img_url = "";
				if (isset($layer['settings']['link-type-sharepinterest'])) {
					switch ($layer['settings']['link-type-sharepinterest']) {
						case 'custom':
							$pinterest_share_url = $layer['settings']['link-type-sharepinterest-custom-url'];
							break;
							
						case 'site':
							$pinterest_share_url = get_permalink();
							$title = get_the_title();
							$excerpt = get_the_excerpt();
							break;
							
						default:
							if ($is_post) {
								$pinterest_share_url = get_permalink($post['ID']);
								$title = get_the_title($post['ID']);
								$excerpt = get_the_excerpt($post['ID']);
							} else {
								$get_link = $this->get_custom_element_value('post-link', $separator);
								$title = $this->get_custom_element_value('title', $separator);
								$excerpt = $this->get_custom_element_value('content', $separator);
								$pinterest_share_url = $get_link;
							}
							break;
					}
				} else {
					if ($is_post) {
						$pinterest_share_url = get_permalink($post['ID']);
						$title = get_the_title($post['ID']);
						$excerpt = get_the_excerpt($post['ID']);
					} else {
						$get_link = $this->get_custom_element_value('post-link', $separator);
						$title = $this->get_custom_element_value('title', $separator);
						$excerpt = $this->get_custom_element_value('content', $separator);
						$pinterest_share_url = $get_link;
					}
				}
				// if featured full is available
				if (isset($this->media_sources['featured-image-full']) && !empty($this->media_sources['featured-image-full'])) {
					$img_url = $this->media_sources['featured-image-full'];
				} // if featured regular size is available
				else if (isset($this->media_sources['featured-image']) && !empty($this->media_sources['featured-image'])) {
					$img_url = $this->media_sources['featured-image'];
				} // if global image is available
				else if (!empty($this->default_image)) {
					$img_url = esc_attr($this->default_image);
				}

				$description = '';
				if (!empty($layer['settings']['link-type-sharepinterest-description'])) {
					$description = str_replace(["%title%", "%excerpt%"], [$title, $excerpt], $layer['settings']['link-type-sharepinterest-description']);
				}
				$text = '<a href="https://pinterest.com/pin/create/button/?url=' . urlencode($pinterest_share_url) . '&media=' . urlencode($img_url) . '&description=' . urlencode($description) . '" target="_blank" rel=nofollow>' . $text . '</a>';
				break;
				
			case 'sharetwitter':
				$title = $excerpt = '';
				if (isset($layer['settings']['link-type-sharetwitter'])) {
					switch ($layer['settings']['link-type-sharetwitter']) {
						case 'custom':
							$twitter_share_url = $layer['settings']['link-type-sharetwitter-custom-url'];
							break;
							
						case 'site':
							$twitter_share_url = get_permalink();
							$title = get_the_title();
							$excerpt = get_the_excerpt();
							break;
							
						default:
							if ($is_post) {
								$twitter_share_url = get_permalink($post['ID']);
								$title = get_the_title($post['ID']);
								$excerpt = get_the_excerpt($post['ID']);
							} else {
								$get_link = $this->get_custom_element_value('post-link', $separator);
								$title = $this->get_custom_element_value('title', $separator);
								$excerpt = $this->get_custom_element_value('content', $separator);
								$twitter_share_url = $get_link;
							}
							break;
					}
				} else {
					if ($is_post) {
						$twitter_share_url = get_permalink($post['ID']);
						$title = get_the_title($post['ID']);
						$excerpt = get_the_excerpt($post['ID']);
					} else {
						$get_link = $this->get_custom_element_value('post-link', $separator);
						$title = $this->get_custom_element_value('title', $separator);
						$excerpt = $this->get_custom_element_value('content', $separator);
						$twitter_share_url = $get_link;
					}
				}

				if (!empty($layer['settings']['link-type-sharetwitter-text-before'])) {
					$twitter_share_text_before = str_replace(["%title%", "%excerpt%"], [$title, $excerpt], $layer['settings']['link-type-sharetwitter-text-before']);
				} else {
					$twitter_share_text_before = "";
				}
				$twitter_share_text = $twitter_share_text_before;
				$text = '<a href="https://twitter.com/intent/tweet?text=' . urlencode($twitter_share_text) . '&url=' . $twitter_share_url . '&related=" target="_blank" rel=nofollow>' . $text . '</a>';
				break;
				
			case 'likepost':
				if (!empty($this->post['ID']))
					$text = '<a data-post_id="' . @$this->post['ID'] . '" href="#"><span class="eg-post-like">' . $text . '</span></a>'; //javascript-link
				else $text = '';
				break;
		}

		$text = trim($text);

		//check for special styling coming from post option and set css to the queue
		if (isset($layer['id'])) $this->set_meta_element_changes($layer['id'], $unique_class);

		if ($is_post) {
			$post_class = (!isset($post['ID'])) ? '' : ' eg-post-' . esc_attr($post['ID']);
		} else {
			$post_class = isset($this->post['post_id']) && !empty($this->post['post_id']) ? ' eg-post-' . esc_attr($this->post['post_id']) : '';
		}

		if ($base->text_has_certain_tag($text, 'a') && !$do_ignore_styles) { 
			//check if a tag exists, if yes, class will be set to a tags and not the wrapping div, also the div will receive the position and other stylings // && @$layer['settings']['source'] !== 'text'
			if ($is_woo_cats && strpos($text, 'class="') !== false || $is_woo_button || $is_filter_cat && strpos($text, 'class="') !== false) { 
				//add to the classes instead of creating own class attribute if it is woocommerce cats AND a class can be found
				$text = str_replace('class="', 'class="' . $unique_class . $post_class . $lb_class . ' ', $text);
			} elseif ($is_html_source && strpos($text, 'class="') !== false) {
				$text = str_replace('<a', '<a class="' . $unique_class . $post_class . $lb_class . '"', $text);
			} else {
				$text = str_replace('<a', '<a class="' . $unique_class . $post_class . $lb_class . '"', $text);
			}

			$unique_class .= '-a';
		}

		if ($do_ignore_styles) $unique_class = 'eg-' . esc_attr($this->handle) . '-nostyle-element-' . esc_attr($layer['id']);

		//replace all the normal shortcodes
		if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) { 
			//use qTranslate
			$text = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($text);
		} elseif (function_exists('ppqtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) { 
			//use qTranslate plus
			$text = ppqtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($text);
		} elseif (function_exists('qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage')) { 
			//use qTranslate X
			$text = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage($text);
		}
		$text = do_shortcode($text);

		$classes = ['esg-' . $class, $unique_class];
		
		if ($special_item == 'true' && $special_item_type == 'line-break') {
			
			//line break element
			$classes = array_filter(array_map('trim', $classes));
			echo '              <div class="' . implode(' ', array_map('esc_attr', $classes)) . ' esg-none esg-clear esg-line-break"></div>' . "\n";
			
		} elseif (trim($text) !== '') { 
			
			$use_tag = $base->getVar($layer, ['settings', 'tag-type'], 'div');
			if (!in_array($use_tag, ['div', 'p', 'h2', 'h3', 'h4', 'h5', 'h6'])) $use_tag = 'div';

			$classes[] = $post_class;
			$classes[] = $video_play;
			$classes[] = $ajax_class;
			$classes[] = $hideunderClass;
			$classes[] = $unique_class;
			$classes[] = $transition;
			$classes = array_filter(array_map('trim', $classes));

			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- var contain escaped data-* attributes and layer text
			echo '				<' . tag_escape($use_tag) . ' class="' . implode(' ', array_map('esc_attr', $classes)) . '"'
			     . $ajax_attr . $transition_split . $delay . $duration . $hideunderHTML
			     . ($demo == 'custom' ? $demo_element_type : '') . $data_transition_transition . '>';
			echo $text;
			echo '</' . tag_escape($use_tag) . '>' . "\n";
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Retrieve the value of post elements
	 */
	public function get_post_value($handle, $separator, $function, $meta, $catmax = '-1', $taxonomy = "") {
		$base = new Essential_Grid_Base();
		$text = '';

		/* 2.1.5 category max option */
		$adjustMax = false;

		if (in_array($handle, ['cat_list', 'tag_list', 'taxonomy'])) {
			if (!empty($catmax) && $catmax !== '-1' && is_numeric($catmax) && intval($catmax) > 0) {
				$catmax = intval($catmax);
				$adjustMax = true;
			}
		}

		switch ($handle) {
			//Post elements
			case 'post_id':
				$text = $base->getVar($this->post, 'ID');
				break;
			case 'post_url':
				$post_id = $base->getVar($this->post, 'ID');
				$text = get_permalink($post_id);
				break;
			case 'title':
				$text = $base->getVar($this->post, 'post_title');
				break;
			case 'caption':
			case 'excerpt':
				$text = $base->getVar( $this->post, 'post_excerpt' );
				if ( empty( $text ) ) {
					$text = $base->getVar( $this->post, 'post_content' );
				}
				$text = trim( wp_strip_all_tags( do_shortcode( $base->strip_shortcode( $text ) ) ) );
				break;
			case 'meta':
				$m = new Essential_Grid_Meta();
				$text = $m->get_meta_value_by_handle($base->getVar($this->post, 'ID'), $meta);
				break;
			case 'likespost':
				$post_id = $base->getVar($this->post, 'ID');
				if (!empty($post_id)) {
					$count = get_post_meta($post_id, "eg_votes_count", 0);
					if (!$count) $count[0] = 0;
					if (is_array($count)) {
						$text = '<span class="eg-post-count">' . $count[0] . '</span>';
					}
				}
				break;
			case 'alias':
				$text = $base->getVar($this->post, 'post_name');
				break;
			case 'description':
			case 'content':
				$text = do_shortcode( $base->getVar($this->post, 'post_content') );
			break;
			case 'link':
				$text = get_permalink($base->getVar($this->post, 'ID'));
				break;
			case 'date':
				$text = $base->convert_post_date( Essential_Grid_Base::getPostDate( $this->post ) );
				break;
			case 'date_day':
				$text = Essential_Grid_Base::getPostDate($this->post, 'd');
				break;
			case 'date_month':
				$text = Essential_Grid_Base::getPostDate($this->post, 'm');
				break;
			case 'date_month_abbr':
				$text = Essential_Grid_Base::getPostDate($this->post, 'M');
				break;
			case 'date_month_name':
				$text = Essential_Grid_Base::getPostDate($this->post, 'F');
				break;
			case 'date_year':
				$text = Essential_Grid_Base::getPostDate($this->post, 'Y');
				break;
			case 'date_year_abbr':
				$text = Essential_Grid_Base::getPostDate($this->post, 'y');
				break;
			case 'date_modified':
				$dateModified = $base->getVar($this->post, "post_modified");
				$text = $base->convert_post_date($dateModified);
				break;
			case 'author_name':
				$authorID = $base->getVar($this->post, 'post_author');
				$text = get_the_author_meta('display_name', $authorID);
				break;
			case 'author_posts':
				$authorID = $base->getVar($this->post, 'post_author');
				$text = get_author_posts_url($authorID);
				break;
			case 'author_profile':
				$authorID = $base->getVar($this->post, 'post_author');
				$text = get_the_author_meta('url', $authorID);
				break;
			case 'author_avatar_32':
				$authorID = $base->getVar($this->post, 'post_author');
				$text = get_avatar($authorID, 32);
				break;
			case 'author_avatar_64':
				$authorID = $base->getVar($this->post, 'post_author');
				$text = get_avatar($authorID, 64);
				break;
			case 'author_avatar_96':
				$authorID = $base->getVar($this->post, 'post_author');
				$text = get_avatar($authorID);
				break;
			case 'author_avatar_512':
				$authorID = $base->getVar($this->post, 'post_author');
				$text = get_avatar($authorID, 512);
				break;
			case 'num_comments':
				$text = $base->getVar($this->post, 'comment_count');
				break;
			case 'cat_list':
				$use_taxonomies = [];
				$postCatsIDs = $base->getVar($this->post, 'post_category');
				if (empty($postCatsIDs) && isset($this->post['post_type'])) {
					$postCatsIDs = [];
					$obj = get_object_taxonomies($this->post['post_type']);
					if (!empty($obj) && is_array($obj)) {
						foreach ($obj as $tax) {
							if ($tax == 'post_tag') continue;
							
							$use_taxonomies[] = $tax;
							$new_terms = get_the_terms($base->getVar($this->post, 'ID'), $tax);
							if (is_array($new_terms) && !empty($new_terms)) {
								foreach ($new_terms as $term) {
									$postCatsIDs[$term->term_id] = $term->term_id;
								}
							}
						}
					}
				}

				/* 2.1.5 category max option */
				if ($adjustMax && is_array($postCatsIDs)) $postCatsIDs = array_slice($postCatsIDs, 0, $catmax, true);

				if (empty($use_taxonomies)) $use_taxonomies = false;
				$text = $base->get_categories_html_list($postCatsIDs, $function, $separator, $use_taxonomies);
				break;
			case 'tag_list':
				if (!$adjustMax) {
					$text = $base->get_tags_html_list($base->getVar($this->post, 'ID'), $separator, $function);
				} else {
					$text = $base->get_tags_html_list($base->getVar($this->post, 'ID'), $separator, $function, $catmax);
				}
				break;
			case 'taxonomy':
				if (!$adjustMax) {
					$text = $base->get_tax_html_list($base->getVar($this->post, 'ID'), $taxonomy, $separator, $function);
				} else {
					$text = $base->get_tax_html_list($base->getVar($this->post, 'ID'), $taxonomy, $separator, $function, $catmax);
				}
				if (is_array($text)) $text = implode($separator, $text);
				break;
			case 'alternate-image':
				$source = get_post_meta($base->getVar($this->post, 'ID'), 'eg_sources_image', true);
				$source = wp_get_attachment_image_src(esc_attr($source), 'full');
				$source = ($source !== false && isset($source['0'])) ? $source['0'] : '';
				// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage 
				$text = (!empty($source)) ? '<img src="' . $source . '" />' : '';
				break;
			default:
				$text = apply_filters('essgrid_post_meta_content', $text, $handle, $base->getVar($this->post, 'ID'), $this->post);
				break;
		}

		return $text;
	}

	/**
	 * Retrieve the value of post elements
	 */
	public function get_custom_element_value($handle, $separator, $meta = '') {
		$base = new Essential_Grid_Base();
		$m = new Essential_Grid_Meta();

		$text = $base->getVar($this->layer_values, $handle);
		if ($text == '' && $meta != '')
			$text = $base->getVar($this->layer_values, $meta);
		
		if ('post-link' == $handle && !preg_match("/^https?:\/\//i", $text)) {
			//just a path, lets build an url
			$text = get_home_url(null, $text);
		}

		if (intval($text) > 0) { 
			//we may be an image from the metas
			$custom_meta = $m->get_all_meta(false);
			if (!empty($custom_meta)) {
				foreach ($custom_meta as $cmeta) {
					if ($cmeta['handle'] == $handle) {
						if ($cmeta['type'] == 'image') {
							$img = wp_get_attachment_image_src($text, $this->media_sources_type);
							if ($img !== false) {
								$text = $img[0]; //replace with URL
							}
						}
						break;
					}
				}
			}
		}

		return $text;
	}

	/**
	 * Retrieve the value of woocommerce elements
	 */
	public function get_woocommerce_value($meta, $separator, $catmax = false) {
		$text = '';

		if (isset($this->post['ID'])) {
			if (Essential_Grid_Woocommerce::is_woo_exists()) {
				/* 2.1.5 category max option */
				$adjustMax = false;
				if ($meta === 'wc_categories') {
					if (!empty($catmax) && $catmax !== '-1' && is_numeric($catmax) && intval($catmax) > 0) {
						$catmax = intval($catmax);
						$adjustMax = true;
					}
				}

				if ($adjustMax) {
					$text = Essential_Grid_Woocommerce::get_value_by_meta($this->post['ID'], $meta, $separator, $catmax);
				} else {
					$text = Essential_Grid_Woocommerce::get_value_by_meta($this->post['ID'], $meta, $separator);
				}
			}
		}
		return $text;
	}

	/**
	 * get item skin handle 
	 * @since: 3.0.13
	 * @return string
	 */
	public function get_handle() {
		return $this->handle;
	}

	/**
	 * attempt to get media attribute ( title, alt, caption, description ) by media url 
	 * 
	 * @param array $sources media sources array
	 * @param string $type  media type
	 * @param string $attr  attribute to get
	 * @param string $lb_source  lightbox image source, can be passed directly if it is not in media sources
	 *
	 * @return string
	 * @since: 3.0.14
	 */
	protected function get_media_attr($sources, $type, $attr, $lb_source = '') {
		$result = '';

		if ( empty($lb_source) ) {
			// use full if exist, i.e. 'featured-image-full'
			if ( isset( $sources[ $type . '-full' ] ) ) {
				$type .= '-full';
			}

			$url = apply_filters( 'essgrid_get_media_attr', Essential_Grid_Base::getVar( $sources, $type ), $sources, $type );

			if ( ! is_string( $url ) ) {
				return $result;
			}
		} else {
			$url = $lb_source;
		}
		
		$media_id = attachment_url_to_postid($url);
		if ($media_id) {
			$media_info = Essential_Grid_Base::get_attachment_info($media_id);
			$result = Essential_Grid_Base::getVar($media_info, $attr);
		}
		
		return $result;
	}

	/**
	 * get caption for lightbox
	 *
	 * @param bool|string $demo
	 * @param bool $is_post
	 * @param string $lb_source  lightbox image source, can be passed directly if it is not in media sources
	 *
	 * @return string
	 */
	protected function get_lightbox_caption($demo, $is_post, $lb_get_lightbox = '') {
		$lb_title = $this->get_lightbox_title($demo, $is_post, $lb_get_lightbox);
		$lb_description = $this->get_lightbox_description($demo, $is_post, $lb_get_lightbox);

		$lb_caption = '';
		if (!empty($lb_title)) {
			$lb_caption .= '<div class="esgbox-caption-title">'.$lb_title.'</div>';
		}
		if (!empty($lb_description)) {
			$lb_caption .= '<div class="esgbox-caption-description">'.$lb_description.'</div>';
		}
		if (!empty($lb_caption)) {
			$lb_caption = ' data-caption="' . esc_attr($lb_caption) . '" ';
		}
		
		return $lb_caption;
	}
		
	
	/**
	 * get title for lightbox
	 *
	 * @param bool|string $demo
	 * @param bool $is_post
	 * @param string $lb_source  lightbox image source, can be passed directly if it is not in media sources
	 *
	 * @return string
	 * @since: 3.0.14
	 */
	protected function get_lightbox_title($demo, $is_post, $lb_source = '') {
		$base = new Essential_Grid_Base();
		$lb_title = '';
		
		$lightbox_title_strip = $base->getVar($this->grid_params, 'lightbox-title-strip', 'on');
		$lightbox_title = $base->getVar($this->grid_params, 'lightbox-title', 'off');
		if ('on' === $lightbox_title) {
			$lightbox_title_source_orig = $base->getVar($this->grid_params, 'lightbox-title-source', 'title');
			$lightbox_title_source = $demo !== false ? 'demo' : $lightbox_title_source_orig;
			switch ($lightbox_title_source) {
				case 'demo':
					$lb_title = sprintf(
						/* translators: %s: lightbox title/description source */
						esc_attr__('demo mode %s', 'essential-grid'),
						$lightbox_title_source_orig
					);
					break;
				case 'title':
					if ($is_post)
						$lb_title = $base->getVar($this->post, 'post_title');
					else
						$lb_title = $this->get_custom_element_value('title', '');
					break;
				case 'caption':
					$lb_title = $this->get_custom_element_value('caption', '');
					break;
				case 'media_alt':
				case 'media_title':
				case 'media_caption':
					$lb_title = $this->get_media_attr(
						$this->media_sources,
						$this->item_media_type,
						str_replace('media_', '', $lightbox_title_source),
						$lb_source
					);
					break;
			}

			if ('on' === $lightbox_title_strip) {
				$lb_title = wp_strip_all_tags($lb_title);
			}
		}
		
		return $lb_title;
	}

	/**
	 * get description for lightbox
	 *
	 * @param bool|string $demo
	 * @param bool $is_post
	 * @param string $lb_source  lightbox image source, can be passed directly if it is not in media sources
	 *
	 * @return string
	 * @since: 3.0.14
	 */
	protected function get_lightbox_description($demo, $is_post, $lb_source = '') {
		$base = new Essential_Grid_Base();
		$lb_description = '';
		
		$lightbox_title_strip = $base->getVar($this->grid_params, 'lightbox-title-strip', 'on');
		$lightbox_description = $base->getVar($this->grid_params, 'lightbox-description', 'off');
		if ('on' === $lightbox_description) {
			$lightbox_description_source_orig = $base->getVar($this->grid_params, 'lightbox-description-source', 'description');
			$lightbox_description_source = $demo !== false ? 'demo' : $lightbox_description_source_orig;
			switch ($lightbox_description_source) {
				case 'demo':
					$lb_description = sprintf(
						/* translators: %s: lightbox title/description source */
						esc_attr__('demo mode %s', 'essential-grid'),
						$lightbox_description_source_orig
					);
					break;
				case 'description':
					$lb_description = $this->get_custom_element_value('description', '');
					break;
				case 'post_excerpt':
					if ($is_post) {
						$lb_description = $base->getVar($this->post, 'post_excerpt');
					} else {
						$lb_description = $base->getVar($this->post, 'excerpt');
					}
					break;
				case 'post_content':
					if ($is_post) {
						$lb_description = wp_strip_all_tags($base->getVar($this->post, 'post_content'));
					} else {
						$lb_description = wp_strip_all_tags($base->getVar($this->post, 'content'));
					}
					if (strlen($lb_description) > 140)
						$lb_description = substr($lb_description, 0, 140) . '...';
					break;
				case 'media_description':
				case 'media_alt':
					$lb_description = $this->get_media_attr(
						$this->media_sources,
						$this->item_media_type,
						str_replace('media_', '', $lightbox_description_source),
						$lb_source
					);
					break;
			}

			if ('on' === $lightbox_title_strip) {
				$lb_description = wp_strip_all_tags($lb_description);
			}
		}
		
		return $lb_description;
	}

	/**
	 * check if link is email without mailto
	 * check if link missing protocol
	 * 
	 * @param string $link
	 *
	 * @return string
	 */
	protected function normalize_link($link) {
		$good = false;
		$link = trim($link);
		
		if (empty($link)
		    || $link == 'javascript:void(0);'
		    || strpos($link, 'mailto:') === 0
		    || strpos($link, '//') === 0
		    || strpos($link, 'http://') === 0
		    || strpos($link, 'https://') === 0
		) {
			$good = true;
		}

		if (!$good && strpos($link, '@')) {
			$link = 'mailto:' . $link;
			$good = true;
		}

		if (!$good) {
			$link = (is_ssl() ? 'https://' : 'http://') . $link;
		}

		return apply_filters('essgrid_item_skin_normalize_link', $link);
	}

	/**
	 * @param string $url
	 *
	 * @return mixed|null
	 */
	protected function get_remote_json($url) {
		$return = null;
		$response = wp_safe_remote_get( $url );

		if ( ! is_wp_error( $response ) && $response['response']['code'] === 200 ) {
			$return = json_decode( wp_remote_retrieve_body( $response ) );
		}

		return $return;
	}

	/**
	 * @param string $yt_id
	 *
	 * @return string
	 */
	protected function _get_youtube_thumb($yt_id) {
		$yt_thumbs = [
			'maxresdefault.jpg',
			'hqdefault.jpg',
			'mqdefault.jpg',
			'sddefault.jpg',
			'default.jpg',
		];
		
		switch ($this->media_sources_type) {

			case 'large':
				$yt_thumbs[] = array_shift( $yt_thumbs );
				break;

			case 'medium':
				$yt_thumbs[] = array_shift( $yt_thumbs );
				$yt_thumbs[] = array_shift( $yt_thumbs );
				break;

			case 'thumbnail':
				$yt_thumbs[] = array_shift( $yt_thumbs );
				$yt_thumbs[] = array_shift( $yt_thumbs );
				$yt_thumbs[] = array_shift( $yt_thumbs );
				break;

			case 'full':
			default:
		}
		
		foreach ($yt_thumbs as $img) {
			$video_poster_src = 'https://i.ytimg.com/vi/' . $yt_id . '/' . $img;
			$exist = $this->_is_url_exists($video_poster_src);
			if ($exist) {
				return $video_poster_src;
			}
		}
		
		return '';
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	protected function _is_url_exists($url) {
		$response = wp_safe_remote_get($url);
		return ( !is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response) );
	}

}
