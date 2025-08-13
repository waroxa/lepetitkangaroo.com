<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Import
{
	/**
	 * @var array
	 */
	private $_import_keys = [
		'grids',
		'skins',
		'elements',
		'navigation-skins',
		'custom-meta',
		'global-css',
	];

	/**
	 * @var bool 
	 */
	private $overwrite_data = false;

	/**
	 * @var Essential_Grid_Base
	 */
	private $base;
	
	public function __construct()
	{
		$this->base = new Essential_Grid_Base();
	}

	public function set_overwrite_data($data)
	{
		$this->overwrite_data = $data;
	}
	
	public function get_import_keys()
	{
		return apply_filters('essgrid_get_import_keys', $this->_import_keys);
	}

	/**
	 * @param array $import_grids
	 * @param array $import_ids
	 * @param bool $check_append
	 * @param array $imported_data
	 * @param string $create_taxonomies
	 * @return array
	 * @throws Exception
	 */
	public function import_grids($import_grids, $import_ids, $check_append = true, $imported_data = [], $create_taxonomies = 'off')
	{
		global $wpdb;
		
		$d = apply_filters('essgrid_import_grids', ['import_grids' => $import_grids, 'import_ids' => $import_ids, 'check_append' => $check_append]);
		$import_grids = $d['import_grids'];
		$import_ids = $d['import_ids'];
		$check_append = $d['check_append'];
		
		$ids = [];

		if (empty($import_grids)) return $ids;
		if (empty($import_ids) || !is_array($import_ids)) return $ids;

		$item_skin = new Essential_Grid_Item_Skin();

		$grids = Essential_Grid_Db::get_entity('grids')->get_grids();

		foreach ($import_grids as $i_grid) {
			if (!in_array($i_grid['id'], $import_ids)) continue;

			$processed_id = 0;
			$i_grid_original = $i_grid;

			if (!$this->base::isValid()) {
				$params = json_decode($i_grid['params'], true);
				$pg = $this->base->getVar($params, 'pg', 'false');
				if ($pg != 'false') throw new Exception(esc_html__('Please register the Essential Grid plugin to use premium templates.', 'essential-grid'));
			}

			// create/get category and tags if there are some selected
			$check = json_decode($i_grid['postparams'], true);
			if (!empty($check['post_category'])) {
				$slug_cats = [];
				$the_cats = explode(',', $check['post_category']);
				foreach ($the_cats as $cat) {
					list($cat, $catSlug) = Essential_Grid_Base::splitString($cat);
					$category = $this->base->get_create_category_by_slug($catSlug, $cat, $create_taxonomies);
					if ($category !== false) 
						$slug_cats[] = $cat . '_' . $category;
				}
				$check['post_category'] = implode(',', $slug_cats);

				$i_grid['postparams'] = wp_json_encode($check);

			}

			$check = json_decode($i_grid['params'], true);

			// 3.0.14
			// update grid params to add desktop xl dimension to columns
			$check = Essential_Grid_Plugin_Update::update_grid_desktop_xl($check);
			
			// update nav skin if needed
			if (!empty($check['navigation-skin']) && !empty($imported_data['nav_skins'][$check['navigation-skin']])) {
				$check['navigation-skin'] = $imported_data['nav_skins'][$check['navigation-skin']];
			}
			
			if (!empty($check['entry-skin'])) {
				$handle = $check['entry-skin'];
				if (!empty($imported_data['skins'][$check['entry-skin']])) $handle = $imported_data['skins'][$check['entry-skin']];
				$skin = $item_skin->get_id_by_handle($handle);
				if (!empty($skin)) {
					$check['entry-skin'] = $skin['id'];

					// 2.3
					// convert all skin modifications to the current skin ID
					// ... and also convert blank skin item IDs
					if (!empty($i_grid['layers'])) {

						$mods = json_decode($i_grid['layers'], true);
						$blankSkinId = false;

						foreach ($mods as $key => $val) {

							// convert skin modifications
							$mod = json_decode($val, true);
							if (!empty($mod['eg_settings_custom_meta_skin'])) {
								$skin_mods = $mod['eg_settings_custom_meta_skin'];
								$mod_ids = [];
								foreach ($skin_mods as $i) {
									$mod_ids[] = $skin['id'];
								}
								$mod['eg_settings_custom_meta_skin'] = $mod_ids;
							}

							// convert blank skin IDs
							if (!empty($mod['custom-type'])) {
								$alternateSkin = $mod['custom-type'];
								if ($alternateSkin === 'blank') {
									// only get the ID once and store it outside the loop
									if (empty($blankSkinId)) {
										$blankSkin = $item_skin->get_id_by_handle('esgblankskin');
										if (!empty($blankSkin) && isset($blankSkin['id'])) $blankSkinId = $blankSkin['id'];
									}
									if (!empty($blankSkinId)) $mod['use-skin'] = $blankSkinId;
								}
							}
							
							$mods[$key] = wp_json_encode($mod);
						}
						
						$i_grid['layers'] = wp_json_encode($mods);
					}
				}
			}

			$i_grid['params'] = wp_json_encode($check);

			$exist = false;
			if (!empty($grids)) {
				foreach ($grids as $grid) {
					if ($grid->handle == $i_grid['handle']) {
						// this will force an update
						$i_grid['id'] = $grid->id; 
						$exist = true;
						break;
					}
				}
			}

			$append = true;
			if ($exist && $check_append) {
				// grid exists - append or overwrite
				$do = $this->base->getVar($this->overwrite_data, 'grid-overwrite-' . $i_grid_original['id'], 'append');
				$append = $do == 'append';
			}

			$i_grid = apply_filters('essgrid_import_grids_insert_before', $i_grid, $import_ids, $exist, $append);
			$data = [
				'name' => $i_grid['name'],
				'handle' => $i_grid['handle'],
				'postparams' => $i_grid['postparams'],
				'params' => $i_grid['params'],
				'layers' => $i_grid['layers'],
				'last_modified' => gmdate('Y-m-d H:i:s'),
			];

			if ($append) {
				// append
				if ($exist) {
					$data['handle'] .= '-' . gmdate('His');
					$data['name'] .= '-' . gmdate('His');
				}
				$response = Essential_Grid_Db::get_entity('grids')->insert( $data );
				if ($response !== false) {
					$processed_id = $ids[] = $wpdb->insert_id;
				}
				
			} else {
				// overwrite
				$response = Essential_Grid_Db::get_entity('grids')->update( $data, $i_grid['id'] );
				if ($response !== false) {
					$processed_id = $ids[] = $i_grid['id'];
				}
			}

			do_action('essgrid_import_grids_insert_after', $i_grid, $processed_id);
		}

		return $ids;
	}

	/**
	 * @param array $import_skins
	 * @param array $import_ids
	 * @param bool $check_append
	 * @param bool $ignore_exists
	 * @return array  [original_handle] => imported handle
	 * @throws Exception
	 */
	public function import_skins($import_skins, $import_ids, $check_append = true, $ignore_exists = false)
	{
		global $wpdb;
		
		$d = apply_filters('essgrid_import_skins', ['import_skins' => $import_skins, 'import_ids' => $import_ids, 'check_append' => $check_append]);
		$import_skins = $d['import_skins'];
		$import_ids = $d['import_ids'];
		$check_append = $d['check_append'];
		
		$return = [];

		if (empty($import_skins)) return $return;
		if (empty($import_ids) || !is_array($import_ids)) return $return;

		$item_skin = new Essential_Grid_Item_Skin();

		// false = do not decode params
		$skins = $item_skin->get_essential_item_skins('all', false);

		foreach ($import_skins as $i_skin) {
			if (!in_array($i_skin['id'], $import_ids)) continue;

			$i_skin_original = $i_skin;

			$exist = false;
			if (!empty($skins)) {
				foreach ($skins as $skin) {
					if ($skin['handle'] == $i_skin['handle']) {
						// this will force an update
						$i_skin['id'] = $skin['id']; 
						$exist = true;
						break;
					}
				}
			}
			if ($ignore_exists && $exist) {
				continue;
			}

			$append = true;
			if ($exist) {
				// skin exists - append or overwrite
				if ($check_append) {
					//check in data if append or overwrite
					$do = $this->base->getVar($this->overwrite_data, 'skin-overwrite-' . $i_skin_original['id'], 'append');
					$append = $do == 'append';
				}
			}

			if ($append) {
				// append
				if ($exist) {
					$i_skin['handle'] = $i_skin['handle'] . '-' . gmdate('His');
					$i_skin['name'] = $i_skin['name'] . '-' . gmdate('His');
				}
				Essential_Grid_Db::get_entity('skins')->insert(
					[
						'name' => $i_skin['name'],
						'handle' => $i_skin['handle'],
						'params' => $i_skin['params'],
						'layers' => $i_skin['layers']
					]
				);
			} else {
				// overwrite
				Essential_Grid_Db::get_entity('skins')->update(
					[
						'name' => $i_skin['name'],
						'handle' => $i_skin['handle'],
						'params' => $i_skin['params'],
						'layers' => $i_skin['layers']
					],
					$i_skin['id']
				);
			}
			$return[$i_skin_original['handle']] = $i_skin['handle'];
		}
		
		return $return;
	}

	public function import_elements($import_elements, $import_ids, $check_append = true)
	{
		global $wpdb;
		
		$d = apply_filters('essgrid_import_elements', ['import_elements' => $import_elements, 'import_ids' => $import_ids, 'check_append' => $check_append]);
		$import_elements = $d['import_elements'];
		$import_ids = $d['import_ids'];
		$check_append = $d['check_append'];

		if (empty($import_elements)) return;
		if (empty($import_ids) || !is_array($import_ids)) return;
		
		$elements = Essential_Grid_Item_Element::get_essential_item_elements();

		foreach ($import_elements as $i_element) {
			if (!in_array($i_element['id'], $import_ids)) continue;
			if (Essential_Grid_Item_Element::isDefaultElement($i_element['handle'])) continue;

			$exist = false;
			if (!empty($elements)) {
				foreach ($elements as $element) {
					if ($element['handle'] == $i_element['handle']) {
						$i_element['id'] = $element['id']; //this will force an update
						$exist = true;
						break;
					}
				}
			}

			$append = true;
			if ($exist) {
				//skin exists - append or overwrite
				if ($check_append) {
					//check in data if append or overwrite
					$do = $this->base->getVar($this->overwrite_data, 'element-overwrite-' . $i_element['id'], 'append');
					$append = $do == 'append';
				}
			}

			if ($append) {
				//append
				if ($exist) {
					$i_element['handle'] = $i_element['handle'] . '-' . gmdate('His');
					$i_element['name'] = $i_element['name'] . '-' . gmdate('His');
				}
				Essential_Grid_Db::get_entity('elements')->insert( [ 'name' => $i_element['name'], 'handle' => $i_element['handle'], 'settings' => $i_element['settings'] ] );
			} else {
				//overwrite
				Essential_Grid_Db::get_entity('elements')->update( [ 'name' => $i_element['name'], 'handle' => $i_element['handle'], 'settings' => $i_element['settings'] ], $i_element['id'] );
			}
		}
	}

	/**
	 * @param array $import_navigation_skins
	 * @param array $import_ids
	 * @param bool $check_append
	 * @param bool $ignore_exists
	 * @return array  [original_handle] => imported handle
	 * @throws Exception
	 */
	public function import_navigation_skins($import_navigation_skins, $import_ids, $check_append = true, $ignore_exists = false)
	{
		global $wpdb;
		
		$d = apply_filters('essgrid_import_navigation_skins', ['import_navigation_skins' => $import_navigation_skins, 'import_ids' => $import_ids, 'check_append' => $check_append]);
		$import_navigation_skins = $d['import_navigation_skins'];
		$import_ids = $d['import_ids'];
		$check_append = $d['check_append'];

		$return = [];

		if (empty($import_navigation_skins)) return $return;
		if (empty($import_ids) || !is_array($import_ids)) return $return;

		$nav_skins = Essential_Grid_Navigation::get_essential_navigation_skins();

		foreach ($import_navigation_skins as $i_nav_skin) {
			if (!in_array($i_nav_skin['id'], $import_ids)) continue;

			$i_nav_skin_original = $i_nav_skin;

			$exist = false;
			if (!empty($nav_skins)) {
				foreach ($nav_skins as $nav_skin) {
					if ($nav_skin['handle'] == $i_nav_skin['handle']) {
						// this will force an update
						$i_nav_skin['id'] = $nav_skin['id'];
						$exist = true;
						break;
					}
				}
			}

			if ($ignore_exists && $exist) {
				continue;
			}

			$append = true;
			if ($exist) {
				// skin exists - append or overwrite
				if ($check_append) {
					// check in $_POST if append or overwrite
					$do = $this->base->getVar($this->overwrite_data, 'nav-skin-overwrite-' . $i_nav_skin_original['id'], 'append');
					$append = $do == 'append';
				}
			}

			$i_nav_skin['css'] = str_replace(['\n', '\t'], [chr(13), chr(9)], $i_nav_skin['css']);
			// remove first and last "
			if (!empty($i_nav_skin['css'])) {
				if (substr($i_nav_skin['css'], 0, 1) == '"') $i_nav_skin['css'] = substr($i_nav_skin['css'], 1);
				if (substr($i_nav_skin['css'], -1) == '"') $i_nav_skin['css'] = substr($i_nav_skin['css'], 0, -1);
			}
			$i_nav_skin['css'] = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', ['Essential_Grid_Import', 'mb_convert_string'], $i_nav_skin['css']);

			if ($append) {
				// append
				if ($exist) {
					$i_nav_skin['handle'] = $i_nav_skin['handle'] . '-' . gmdate('His');
					$i_nav_skin['name'] = $i_nav_skin['name'] . '-' . gmdate('His');
					// replace the class name to the new name
					$i_nav_skin['css'] = str_replace($i_nav_skin_original['handle'], $i_nav_skin['handle'], $i_nav_skin['css']);
				}
				Essential_Grid_Db::get_entity('nav_skins')->insert( [ 'name' => $i_nav_skin['name'], 'handle' => $i_nav_skin['handle'], 'css' => $i_nav_skin['css'] ] );
			} else {
				// overwrite
				Essential_Grid_Db::get_entity('nav_skins')->update( ['css' => $i_nav_skin['css']], $i_nav_skin['id'] );
			}
			$return[$i_nav_skin_original['handle']] = $i_nav_skin['handle'];
		}
		
		return $return;
	}

	public static function mb_convert_string($match)
	{
		return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
	}

	public function import_custom_meta($import_custom_meta, $import_handles = true, $check_append = true)
	{
		$d = apply_filters('essgrid_import_custom_meta', ['import_custom_meta' => $import_custom_meta, 'import_handles' => $import_handles, 'check_append' => $check_append]);
		$import_custom_meta = $d['import_custom_meta'];
		$import_handles = $d['import_handles'];
		$check_append = $d['check_append'];

		$metas = new Essential_Grid_Meta();
		$link_metas = new Essential_Grid_Meta_Linking();
		$custom_metas = $metas->get_all_meta();

		foreach ($import_custom_meta as $i_custom_meta) {
			$type = (isset($i_custom_meta['m_type']) && $i_custom_meta['m_type'] == 'link') ? 'link' : 'meta';

			if (!empty($import_handles) && is_array($import_handles)) {
				$found = in_array($i_custom_meta['handle'], $import_handles);
				if (!$found) continue;
			} else {
				if ($import_handles !== true)
					break;
			}

			$exist = false;
			if (!empty($custom_metas)) {
				foreach ($custom_metas as $meta) {
					if ($meta['handle'] == $i_custom_meta['handle']) {
						if ($type == $meta['m_type']) {
							$exist = true;
							break;
						}
					}
				}
			}

			//do not insert if handle exists. This is for the import demo data process
			if ($import_handles === true && $exist) continue;

			$append = true;
			if ($exist) {
				//skin exists - append or overwrite
				if ($check_append) {
					//check in $_POST if append or overwrite
					$do = $this->base->getVar($this->overwrite_data, 'custom-meta-overwrite-' . $i_custom_meta['handle'], 'append');
					$append = $do == 'append';
				}
			}
			
			if ($import_handles !== true) {
				if ($append) {
					//append
					if ($exist) {
						$i_custom_meta['handle'] = $i_custom_meta['handle'] . '-' . gmdate('His');
						$i_custom_meta['name'] = $i_custom_meta['name'] . '-' . gmdate('His');
					}

					if ($type == 'meta') {
						$metas->add_new_meta($i_custom_meta);
					} elseif ($type == 'link') {
						$link_metas->add_new_link_meta($i_custom_meta);
					}
				} else {
					//overwrite
					if ($type == 'meta') {
						$metas->edit_meta_by_handle($i_custom_meta);
					} elseif ($type == 'link') {
						$link_metas->edit_link_meta_by_handle($i_custom_meta);
					}
				}
			} else {
				//create or overwrite
				if ($exist) {
					if ($type == 'meta') {
						$metas->edit_meta_by_handle($i_custom_meta);
					} elseif ($type == 'link') {
						$link_metas->edit_link_meta_by_handle($i_custom_meta);
					}
				} else {
					if ($type == 'meta') {
						$metas->add_new_meta($i_custom_meta);
					} elseif ($type == 'link') {
						$link_metas->add_new_link_meta($i_custom_meta);
					}
				}
			}
		}
	}

	public function import_global_styles($import_global_styles, $check_append = true)
	{
		$d = apply_filters('essgrid_import_global_styles', ['import_global_styles' => $import_global_styles, 'import_handles' => true, 'check_append' => $check_append]);
		$import_global_styles = $d['import_global_styles'];
		$check_append = $d['check_append'];

		$c_css = new Essential_Grid_Global_Css();

		$append = true;
		if ($check_append) {
			//check in $_POST if append or overwrite
			$do = $this->base->getVar($this->overwrite_data, 'global-styles-overwrite', 'append');
			$append = $do == 'append';
		}

		$import_global_styles = str_replace(['\n', '\t'], [chr(13), chr(9)], $import_global_styles);
		//remove first and last "
		if (!empty($import_global_styles)) {
			if (substr($import_global_styles, 0, 1) == '"') $import_global_styles = substr($import_global_styles, 1);
			if (substr($import_global_styles, -1) == '"') $import_global_styles = substr($import_global_styles, 0, -1);
		}
		$import_global_styles = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', ['Essential_Grid_Import', 'mb_convert_string'], $import_global_styles);

		if ($append) {
			//append
			$global_styles = $c_css->get_global_css_styles();
			$import_global_styles = $global_styles . "\r\n" . $import_global_styles;
		}

		$c_css->set_global_css_styles($import_global_styles);
	}

}
