<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Navigation
{

	private $grid_id;
	private $filter = [];
	private $filter_show = true;
	private $filter_type = 'singlefilters';
	private $filter_start_select = [];
	private $sorting = [];
	private $sorting_start = 'none';
	private $sorting_order = 'asc';

	private $layout = [
		'top-1' => [],
		'top-2' => [],
		'left' => [],
		'right' => [],
		'bottom-1' => [],
		'bottom-2' => [],
	];
	private $specific_styles = [
		'top-1' => ['margin-bottom' => 0, 'text-align' => 'center'],
		'top-2' => ['margin-bottom' => 0, 'text-align' => 'center'],
		'left' => ['margin-left' => 0],
		'right' => ['margin-right' => 0],
		'bottom-1' => ['margin-top' => 0, 'text-align' => 'center'],
		'bottom-2' => ['margin-top' => 0, 'text-align' => 'center'],
	];
	private $filter_settings = [
		'filter' => [
			'filter-grouping' => 'false', 
			'filter-listing' => 'list', 
			'filter-selected' => [],
		],
	];

	private $styles = [];
	private $filter_all_text = [];
	private $filterall_visible = [];
	private $filter_dropdown_text = [];
	private $filter_show_count = [];
	private $spacing = false;
	private $sort_by_text;
	private $search_text;
	private $special_class = '';
	private $search_found = false;
	private $filter_added = false;
	private $filter_group_count = 0;
	
	public function __construct($grid_id = 0) {
		$this->grid_id = $grid_id;
		$this->filter_all_text['filter'] = esc_attr__('Filter - All', 'essential-grid');
		$this->filterall_visible['filter'] = 'on';
		$this->filter_dropdown_text['filter'] = esc_attr__('Filter Categories', 'essential-grid');
		$this->filter_show_count['filter'] = 'off';
		$this->sort_by_text = esc_attr__('Sort By ', 'essential-grid');
		$this->search_text = esc_attr__('Search...', 'essential-grid');

		self::get_essential_navigation_skins();
	}

	/**
	 * Return all Navigation skins
	 */
	public static function get_essential_navigation_skins() {
		$navigation_skins = Essential_Grid_Db::get_entity('nav_skins')->get_all();

		if (empty($navigation_skins)) { 
			//empty, insert defaults again
			self::propagate_default_navigation_skins();
			$navigation_skins = Essential_Grid_Db::get_entity('nav_skins')->get_all();
		}

		return apply_filters('essgrid_get_navigation_skins', $navigation_skins);
	}

	/**
	 * All default Item Skins
	 */
	public static function get_default_navigation_skins()
	{
		$default = [];
		include('assets/default-navigation-skins.php');
		return apply_filters('essgrid_add_default_nav_skins', $default);
	}

	public static function propagate_default_navigation_skins($networkwide = false)
	{
		$skins = self::get_default_navigation_skins();

		if (function_exists('is_multisite') && is_multisite() && $networkwide) {
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				self::insert_default_navigation_skins($skins);
				restore_current_blog();
			}
		} else {
			self::insert_default_navigation_skins($skins);
		}
	}

	/**
	 * Insert Default Skin if they are not already installed
	 */
	public static function insert_default_navigation_skins($data)
	{
		if ( empty( $data ) ) return;

		/** @var Essential_Grid_Db_Navigation_Skin $entity */
		$entity = Essential_Grid_Db::get_entity('nav_skins');

		foreach ( $data as $skin ) {
			$check = $entity->get_by_handle( $skin['handle'] );
			if ( ! empty( $check ) ) continue;
			$entity->insert( [ 'name' => $skin['name'], 'handle' => $skin['handle'], 'css' => $skin['css'] ] );
		}
	}

	/**
	 * Update / Save Navigation Skins
	 */
	public static function update_create_navigation_skin_css($data)
	{
		$data = apply_filters('essgrid_update_create_navigation_skin_css', $data);

		if (empty($data['name']) && empty($data['sid'])) return esc_attr__('No Navigation Skin ID or Name provided', 'essential-grid');
		if (empty($data['skin_css'])) return esc_attr__('Empty Navigation Skin CSS', 'essential-grid');
		
		$data['skin_css'] = stripslashes($data['skin_css']);
		
		if (isset($data['name'])) {
			// create new skin
			$handle = sanitize_title($data['name']);
			
			if (strlen($data['name']) < 2) return esc_attr__('Invalid name. Name has to be at least 2 characters long.', 'essential-grid');
			if (strlen($handle) < 2) return esc_attr__('Cant create handle from name. Handle has to be at least 2 characters long.', 'essential-grid');

			$response = Essential_Grid_Db::get_entity('nav_skins')->insert( ['name' => $data['name'], 'handle' => $handle, 'css' => $data['skin_css']] );
			return boolval($response);
		}

		if (isset($data['sid']) && intval($data['sid'])) {
			// update skin
			$response = Essential_Grid_Db::get_entity('nav_skins')->update( ['css' => $data['skin_css']], $data['sid'] );
			return boolval($response);
		}
		
		return esc_attr__('Invalid Navigation Skin. Wrong ID given.', 'essential-grid');
	}

	/**
	 * Update / Save Navigation Skins
	 */
	public static function delete_navigation_skin_css($data)
	{
		$data = apply_filters('essgrid_delete_navigation_skin_css', $data);
		if (!isset($data['skin'])) {
			return esc_attr__('Invalid Navigation Skin. Wrong handle given.', 'essential-grid');
		}
		
		$response = Essential_Grid_Db::get_entity('nav_skins')->delete_by_handle( $data['skin'] );
		if ($response === false) return esc_attr__('An error occurred while attempting to delete Navigation Skin', 'essential-grid');

		return true;
	}

	/**
	 * Set all Layout Elements
	 */
	public function set_layout($layout)
	{
		$layout = apply_filters('essgrid_set_layout', $layout);
		if (!empty($layout)) {
			foreach ($layout as $type => $position) {
				if (!empty($position)) {
					$pos = current(array_keys($position));
					$this->layout[$pos][$layout[$type][$pos]] = $type;
				}
			}
			foreach ($this->layout as $key => $val)
				ksort($this->layout[$key]);
		}
	}

	/**
	 * Set specific styles
	 */
	public function set_specific_styles($styles)
	{
		$styles = apply_filters('essgrid_set_specific_styles', $styles);
		$this->specific_styles = $styles;
	}

	/**
	 * Set special class to wrapper
	 * @since: 1.5.0
	 */
	public function set_special_class($classes)
	{
		$classes = apply_filters('essgrid_set_special_class', $classes);
		$this->special_class .= ' ' . esc_attr($classes);
	}

	/**
	 * Get special class to wrapper
	 * @since: 2.3.7
	 * @return string
	 */
	public function get_special_class()
	{
		return $this->special_class;
	}

	/**
	 * Set filter added to true/false
	 * @since: 2.1.0
	 */
	public function set_filter_added($setto)
	{
		$this->filter_added = apply_filters('essgrid_set_filter_added', $setto);
	}

	/**
	 * Set Filter All Text
	 */
	public function set_filter_text($text, $key = '')
	{
		$a = apply_filters('essgrid_set_filter_text', ['text' => $text, 'key' => $key]);
		$this->filter_all_text['filter' . $a['key']] = esc_attr($a['text']);
	}

	/**
	 * Set Filter All Visiblity
	 */
	public function set_filterall_visible($visible, $key = '')
	{
		$a = apply_filters('essgrid_set_filterall_visible', ['visible' => $visible, 'key' => $key]);
		$this->filterall_visible['filter' . $a['key']] = esc_attr($a['visible']);
	}

	/**
	 * Set Sort By Text
	 * @since: 1.5.0
	 */
	public function set_orders_text($text)
	{
		$text = apply_filters('essgrid_set_orders_text', $text);
		$this->sort_by_text = $text;
	}

	/**
	 * Set Filter Dropdown Text
	 */
	public function set_dropdown_text($text, $key = '')
	{
		$a = apply_filters('essgrid_set_dropdown_text', ['text' => $text, 'key' => $key]);
		$this->filter_dropdown_text['filter' . $a['key']] = $a['text'];
	}

	/**
	 * Set Filter Option To Show Count
	 * @since: 2.0
	 */
	public function set_show_count($opt, $key = '')
	{
		$a = apply_filters('essgrid_set_show_count', ['opt' => $opt, 'key' => $key]);
		$this->filter_show_count['filter' . $a['key']] = $a['opt'];
	}

	/**
	 * Set specific filter settings
	 * @since: 1.1.0
	 */
	public function set_filter_settings($key, $settings)
	{
		$a = apply_filters('essgrid_set_filter_settings', ['settings' => $settings, 'key' => $key]);
		$this->filter_settings[$a['key']] = $a['settings'];
	}

	/**
	 * Output Container
	 */
	public function output_layout($layout_container, $spacing = 0)
	{
		$fclass = $this->filter_type ? ' esg-' . $this->filter_type : '';
		do_action('essgrid_output_layout_pre', $layout_container, $this->layout);
		if (!empty($this->layout[$layout_container])) {
			$l = '<article class="esg-filters' . esc_attr($fclass);
			if ($layout_container == 'left') $l .= ' esg-navbutton-solo-left';
			if ($layout_container == 'right') $l .= ' esg-navbutton-solo-right';
			$l .= '"';
			$l .= ' style="';
			if (!empty($this->styles)) {
				foreach ($this->styles as $style => $value) {
					$l .= $style . ': ' . $value . '; ';
				}
			}

			if (!empty($this->specific_styles[$layout_container])) {
				foreach ($this->specific_styles[$layout_container] as $style => $value) {
					$l .= $style . ': ' . $value . '; ';
				}
			}

			$l .= '"';
			$l .= '>';

			$real_spacing = ($spacing !== 0) ? $spacing / 2 : 0;
			foreach ($this->layout[$layout_container] as $what) {
				// set important if we are the arrows, because they have already !important in settings.css
				$important = ($what == 'right' || $what == 'left') ? ' !important' : ''; 
				$this->spacing = ' style="margin-left: ' . esc_attr($real_spacing) . 'px' . $important 
				                 . '; margin-right: ' . esc_attr($real_spacing) . 'px' . $important . ';';
				// hide navigation buttons left & right at start
				if ($what == 'right' || $what == 'left') $this->spacing .= ' display: none;'; 
				$this->spacing .= '"';

				switch ($what) {
					case 'sorting':
						$l .= self::output_sorting();
						break;
					case 'cart':
						$l .= self::output_cart();
						break;
					case 'left':
						$l .= self::output_navigation_left();
						break;
					case 'right':
						$l .= self::output_navigation_right();
						break;
					case 'pagination':
						$l .= self::output_pagination();
						break;
					case 'filter':
						$l .= self::output_filter_unwrapped();
						break;
					case 'search-input':
						$l .= self::output_search_input();
						break;
					default:
						//check if its one of the filter fields
						if (strpos($what, 'filter-') !== false) {
							$cur_id = intval(str_replace('filter-', '', $what));
							$l .= self::output_filter_unwrapped(false, '-' . $cur_id);
						} else {
							do_action('essgrid_output_layout_' . $what);
						}
						break;
				}
			}

			$l .= '</article>';
			$l .= '<div class="esg-clear-no-height"></div>';

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- var contain escaped HTML
			echo apply_filters('essgrid_output_layout', $l);
		}

		do_action('essgrid_output_layout_post', $layout_container, $this->layout);
	}

	public function set_filter($data)
	{
		if (!empty($data)) {
			$this->filter = $data + $this->filter; //merges the array and preserves the key
			asort($this->filter);
			$this->filter = apply_filters('essgrid_set_filter', $this->filter);
		}
	}

	public function set_orders($data)
	{
		$this->sorting = $data + $this->sorting; //merges the array and preserves the key
		arsort($this->sorting);
		$this->sorting = apply_filters('essgrid_set_orders', $this->sorting);
	}

	public function set_orders_start($start_by)
	{
		$this->sorting_start = apply_filters('essgrid_set_orders_start', strtolower($start_by));
	}
	
	public function set_orders_order($order)
	{
		$this->sorting_order = apply_filters('essgrid_set_orders_order', strtolower($order));
	}

	public function set_filter_type($type)
	{
		if ($type == 'single') {
			$this->filter_type = 'singlefilters';
		} elseif ($type == 'multi') {
			$this->filter_type = 'multiplefilters';
		} else {
			$this->filter_type = false;
		}

		$this->filter_type = apply_filters('essgrid_set_filter_type', $this->filter_type);
	}

	/**
	 * @return string
	 */
	public function get_filter_type()
	{
		return $this->filter_type;
	}

	/**
	 * Set filter that are selected at start
	 * @since: 2.0.1
	 **/
	public function set_filter_start_select($select)
	{
		$this->filter_start_select = apply_filters('essgrid_set_filter_start_select', explode(',', $select));
		foreach ($this->filter_start_select as $key => $val) {
			if (trim($val) == '') unset($this->filter_start_select[$key]);
		}
	}

	public function set_style($name, $value)
	{
		$a = apply_filters('essgrid_set_style', ['name' => $name, 'value' => $value]);
		$this->styles[$a['name']] = $a['value'];
	}

	public function output_filter($demo = false)
	{
		if (!$this->filter_show || !$this->filter_type) return true;

		$f = '<article class="esg-filters esg-' . esc_attr($this->filter_type) . ' ' . $this->get_special_class() . '"';

		if (!empty($this->styles)) {
			$f .= ' style="';
			foreach ($this->styles as $style => $value) {
				$f .= $style . ': ' . $value . '; ';
			}
			$f .= '"';
		}

		$f .= '>'; //<!-- USE esg-multiplefilters FOR MIXED FILTERING, AND esg-singlefilters FOR SINGLE FILTERING -->
		$f .= '<div class="esg-filter-wrapper">';

		// 2.2.5
		$f .= '<div class="esg-mobile-filter-button esg-display-none"><span>' . esc_html($this->filter_dropdown_text['filter']) . '</span><i class="eg-icon-down-open"></i></div>';

		$sel = (!empty($this->filter_start_select)) ? '' : ' selected';

		/* 2.2.6 hide Filter-All button if text is empty */
		$all_filter_text = $this->filter_all_text['filter'];
		if (empty($all_filter_text)) $all_filter_text = esc_attr__('Filter - All', 'essential-grid');
		$sel .= $this->filterall_visible['filter'] === 'on' ? '' : ' esg-display-none-i ';
		$f .= '<div class="esg-filterbutton' . $sel . ' esg-allfilter" data-filter="filterall" data-fid="-1"><span>' . esc_html($all_filter_text) . '</span></div>';

		if ($demo === 'skinchoose') {
			$f .= '<div class="esg-filterbutton" data-filter="filter-selectedskin"><span>' . esc_html__('Selected Skin', 'essential-grid') . '</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>';
		}
		if ($demo !== false) {
			$f .= '<div class="esg-filterbutton" data-filter="filter-favorite"><span>' . esc_html__('Favorites', 'essential-grid') . '</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>';
		}

		if (!empty($this->filter)) {
			foreach ($this->filter as $filter_id => $filter) {
				$_v = Essential_Grid_Base::sanitize_utf8_to_unicode($filter['slug']);

				$filter_text = ($demo !== false) ? self::translate_demo_filter($filter['slug']) : apply_filters('essgrid_strip_category_additions', $filter['name']);
				$sel = (in_array($_v, $this->filter_start_select)) ? ' selected' : '';
				$f .= '<div class="esg-filterbutton' . $sel . '" data-fid="' . esc_attr($filter_id) . '" data-filter="filter-' . esc_attr($_v) . '"><span>' . esc_html($filter_text) . '</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>';
			}
		}
		$f .= '</div>';
		$f .= '<div class="clear"></div>';
		$f .= '</article>'; //<!-- END OF FILTERING, SORTING AND  CART BUTTONS -->';
		$f .= '<div class="clear eg-filter-clear"></div>';

		$this->set_filter_added(true);

		return apply_filters('essgrid_output_filter', $f, $demo);
	}

	/**
	 * @param bool $demo
	 * @param string $type  names what settings we need to check for: filter, filter2, filter3
	 * @return mixed|void
	 */
	public function output_filter_unwrapped($demo = false, $type = '')
	{
		$listing = (isset($this->filter_settings['filter' . $type]['filter-listing'])) ? $this->filter_settings['filter' . $type]['filter-listing'] : 'list';
		$amount = (isset($this->filter_show_count['filter' . $type]) && $this->filter_show_count['filter' . $type] == 'on') ? ' eg-show-amount' : '';
		$do_show = @$this->filter_settings['filter' . $type]['filter-selected'];
		$is_post = !isset($this->filter_settings['filter' . $type]['custom']);
		$sort_alpha = Essential_Grid_Base::getVar($this->filter_settings['filter' . $type], 'filter-sort-alpha', 'off');
		$sort_alpha_dir = Essential_Grid_Base::getVar($this->filter_settings['filter' . $type], 'filter-sort-alpha-dir', 'asc');
		
		$dropdown = '';
		switch ($listing) {
			case 'dropdown': //use dropdown
				$dropdown = ' dropdownstyle';
				break;
			case 'mobiledropdownstyle': //use dropdown only on mobile
				$dropdown = ' mobiledropdownstyle';
				break;
		}

		// 3.0.12
		$fclass = $this->filter_group_count === 0 ? '' : ' eg-filter-group';
		$this->filter_group_count++;
		
		$f = '<!-- THE FILTER BUTTON -->';
		$f .= '<div class="esg-filter-wrapper' . esc_attr($dropdown . $amount) . ' ' . $this->get_special_class() . esc_attr($fclass) . '"';
		if ($this->spacing !== false) $f .= $this->spacing;
		$f .= '>';

		$filtertext = esc_attr__('Filter Categories', 'essential-grid');
		if (array_key_exists('filter' . $type, $this->filter_dropdown_text)) {
			$filtertext = $this->filter_dropdown_text['filter' . $type];
		}

		if ($listing == 'dropdown') {
			// 2.2.5
			$f .= '<div class="esg-selected-filterbutton esg-mobile-filter-button"><span>' . esc_html($filtertext) . '</span><i class="eg-icon-down-open"></i></div>';
			$f .= '<div class="esg-dropdown-wrapper">';
		} else {
			// 2.2.5
			$f .= '<div class="esg-mobile-filter-button"><span>' . esc_html($filtertext) . '</span><i class="eg-icon-down-open"></i></div>';
		}

		$filters_html = [];

		if ($is_post && !empty($do_show) && is_array($do_show)) {
			
			//we are a post based grid
			foreach ($do_show as $f_id) {
				
				if (!isset($this->filter[$f_id])) continue;
				
				$filter_text = $demo ? self::translate_demo_filter($this->filter[$f_id]['slug']) : apply_filters('essgrid_strip_category_additions', $this->filter[$f_id]['name']);

				// 2.2.5
				// sanitize multi-select custom meta
				if (strpos($filter_text, "['") !== false && strpos($filter_text, "']") !== false) {
					$filter_text = preg_replace("/\[\'|\'\]/", '', $filter_text);
					$filter_text = preg_replace("/\'\,\'/", ' & ', $filter_text);
				}

				$classes  = 'esg-filterbutton';
				$classes .= in_array($this->filter[$f_id]['slug'], $this->filter_start_select) ? ' selected' : '';

				$data_attr  = 'data-fid="' . esc_attr($f_id) . '" ';
				$data_attr .= 'data-filter="filter-' . esc_attr($this->filter[$f_id]['slug']) . '" ';
				$data_attr .= !empty($this->filter[$f_id]['parent']) ? 'data-pid="' . esc_attr($this->filter[$f_id]['parent']) . '" ' : '';

				$filters_html[$filter_text] = '<div class="' . $classes . '" ' . $data_attr . ' ><span>' . esc_html($filter_text) . '</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>';
			}
			
		} else {
			if (!empty($do_show) && is_array($do_show)) {
				foreach ($do_show as $slug) {
					if (!empty($this->filter) && isset($this->filter[$slug])) {
						$filter = $this->filter[$slug];
						$filter_text = ($demo) ? self::translate_demo_filter($filter['slug']) : apply_filters('essgrid_strip_category_additions', $filter['name']);

						// 2.2.5
						// sanitize multi-select custom meta
						if (strpos($filter_text, "['") !== false && strpos($filter_text, "']") !== false) {
							$filter_text = preg_replace("/\[\'|\'\]/", '', $filter_text);
							$filter_text = preg_replace("/\'\,\'/", ' & ', $filter_text);
						}
						$_v = Essential_Grid_Base::sanitize_utf8_to_unicode($filter['slug']);

						$sel = (in_array($_v, $this->filter_start_select)) ? ' selected' : '';
						$parent_id = (isset($filter['parent']) && intval($filter['parent']) > 0) ? $filter['parent'] : 0;

						$parent = ($parent_id > 0) ? ' data-pid="' . esc_attr($parent_id) . '"' : '';
						$filters_html[$filter_text] = '<div class="esg-filterbutton' . $sel . '" data-fid="' . esc_attr($slug) . '"' . $parent . ' data-filter="filter-' . esc_attr($_v) . '"><span>' . esc_html($filter_text) . '</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>';
					}
				}
			} else { //fallback to old version
				if (!empty($this->filter)) {
					foreach ($this->filter as $filter_id => $filter) {
						$filter_text = ($demo) ? self::translate_demo_filter($filter['slug']) : apply_filters('essgrid_strip_category_additions', $filter['name']);

						// 2.2.5
						// sanitize multi-select custom meta
						if (strpos($filter_text, "['") !== false && strpos($filter_text, "']") !== false) {
							$filter_text = preg_replace("/\[\'|\'\]/", '', $filter_text);
							$filter_text = preg_replace("/\'\,\'/", ' & ', $filter_text);
						}
						$_v = Essential_Grid_Base::sanitize_utf8_to_unicode($filter['slug']);

						$sel = (in_array($_v, $this->filter_start_select)) ? ' selected' : '';
						$parent_id = (isset($filter['parent']) && intval($filter['parent']) > 0) ? $filter['parent'] : 0;

						$parent = ($parent_id > 0) ? ' data-pid="' . esc_attr($parent_id) . '"' : '';
						$filters_html[$filter_text] = '<div class="esg-filterbutton' . $sel . '" data-fid="' . esc_attr($filter_id) . '"' . $parent . ' data-filter="filter-' . esc_attr($_v) . '"><span>' . esc_html($filter_text) . '</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>';
					}
				}
			}
		}

		if ('on' === $sort_alpha && !empty($filters_html)) {
			ksort($filters_html);
			if ('desc' === $sort_alpha_dir) $filters_html = array_reverse($filters_html);
		}

		if ($demo) {
			array_unshift($filters_html, '<div class="esg-filterbutton" data-filter="filter-favorite"><span>' . esc_html__('Favorites', 'essential-grid') . '</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>');
		}

		/* 2.2.6 hide Filter-All button if text is empty */
		$all_filter_text = @$this->filter_all_text['filter' . $type];
		if (empty($all_filter_text)) $all_filter_text = esc_attr__('Filter - All', 'essential-grid');
		$sel = (!empty($this->filter_start_select)) ? '' : ' selected';
		$sel .= @$this->filterall_visible['filter' . $type] === 'on' ? '' : ' esg-display-none-i ';
		array_unshift($filters_html, '<div class="esg-filterbutton' . $sel . ' esg-allfilter" data-filter="filterall" data-fid="-1"><span>' . esc_html($all_filter_text) . '</span></div>');

		//add a class to last element
		$last = $this->array_key_last($filters_html);
		$filters_html[$last] = str_replace('esg-filterbutton', 'esg-filterbutton-last esg-filterbutton', $filters_html[$last]);
		$f .= implode('', $filters_html);

		if ($listing == 'dropdown') {
			$f .= '</div>';
		}
		$f .= '<div class="eg-clearfix"></div>';
		$f .= '</div>';

		$this->set_filter_added(true);

		return apply_filters('essgrid_output_filter_unwrapped', $f, $demo, $type);
	}

	public function output_sorting()
	{
		$s = '';
		if (!empty($this->sorting)) {
			$sort_dir = $this->sorting_order === 'asc' ? 'data-dir="asc"' : 'data-dir="desc"';
			$sort_class = $this->sorting_order === 'asc' ? 'tp-asc' : 'tp-desc';
			$s .= '<div class="esg-sortbutton-wrapper ' . $this->get_special_class() . '"';
			if ($this->spacing !== false) $s .= $this->spacing;
			$s .= '>';
			$s .= '<div class="esg-sortbutton"><span>' . esc_html($this->sort_by_text) . '</span><span class="sortby_data">' . esc_html($this->set_sorting_text($this->sorting_start)) . '</span>';
			$s .= '<select class="esg-sorting-select" data-start="' . esc_attr($this->set_sorting_value($this->sorting_start)) . '">';
			foreach ($this->sorting as $sort) {
				$s .= '<option value="' . esc_attr($this->set_sorting_value($sort)) . '" ' . selected($this->set_sorting_value($this->sorting_start), $this->set_sorting_value($sort), false) . '>' . esc_html($this->set_sorting_text($sort)) . '</option>';
			}
			$s .= '</select>';
			$s .= '</div><div class="esg-sortbutton-order eg-icon-down-open ' . $sort_class . '" ' . $sort_dir . '></div>';
			$s .= '</div>';
		}

		return apply_filters('essgrid_output_sorting', $s);
	}

	public function set_sorting_text($san_text)
	{
		$orig = $san_text;
		
		if (strpos($san_text, 'eg-') === 0) {
			$meta = new Essential_Grid_Meta();
			$m = $meta->get_all_meta(false);
			if (!empty($m)) {
				foreach ($m as $me) {
					if ('eg-' . $me['handle'] == $san_text) return apply_filters('essgrid_set_sorting_text', $me['name'], $san_text);
				}
			}
		} elseif (strpos($san_text, 'egl-') === 0) {
			$meta = new Essential_Grid_Meta_Linking();
			$m = $meta->get_all_link_meta();
			if (!empty($m)) {
				foreach ($m as $me) {
					if ('egl-' . $me['handle'] == $san_text) return apply_filters('essgrid_set_sorting_text', $me['name'], $san_text);
				}
			}
		} else {
			switch ($san_text) {
				case 'date':
					$san_text = esc_attr__('Date', 'essential-grid');
					break;
				case 'title':
					$san_text = esc_attr__('Title', 'essential-grid');
					break;
				case 'excerpt':
					$san_text = esc_attr__('Excerpt', 'essential-grid');
					break;
				case 'id':
					$san_text = esc_attr__('ID', 'essential-grid');
					break;
				case 'slug':
					$san_text = esc_attr__('Slug', 'essential-grid');
					break;
				case 'author':
					$san_text = esc_attr__('Author', 'essential-grid');
					break;
				case 'last-modified':
					$san_text = esc_attr__('Last Modified', 'essential-grid');
					break;
				case 'number-of-comments':
					$san_text = esc_attr__('Comments', 'essential-grid');
					break;
				case 'meta_num_total_sales':
					$san_text = esc_attr__('Total Sales', 'essential-grid');
					break;
				case 'meta_num__regular_price':
					$san_text = esc_attr__('Regular Price', 'essential-grid');
					break;
				case 'meta_num__sale_price':
					$san_text = esc_attr__('Sale Price', 'essential-grid');
					break;
				case 'meta__featured':
					$san_text = esc_attr__('Featured', 'essential-grid');
					break;
				case 'meta__sku':
					$san_text = esc_attr__('SKU', 'essential-grid');
					break;
				case 'meta_num_stock':
					$san_text = esc_attr__('In Stock', 'essential-grid');
					break;
				default:
					$san_text = ucfirst($san_text);
					break;
			}
		}

		return apply_filters('essgrid_set_sorting_text', $san_text, $orig);
	}

	public function set_sorting_value($san_handle)
	{
		$orig = $san_handle;
		switch ($orig) {
			case 'meta_num_total_sales':
				$san_handle = 'total-sales';
				break;
			case 'meta_num__regular_price':
				$san_handle = 'regular-price';
				break;
			case 'meta_num__sale_price':
				$san_handle = 'sale-price';
				break;
			case 'meta__featured':
				$san_handle = 'featured';
				break;
			case 'meta__sku':
				$san_handle = 'sku';
				break;
			case 'meta_num_stock':
				$san_handle = 'in-stock';
				break;
		}
		return apply_filters('essgrid_set_sorting_value', $san_handle, $orig);
	}

	public function output_cart()
	{
		if (!Essential_Grid_Woocommerce::is_woo_exists()) return '';
		if (!function_exists('wc_cart_totals_subtotal_html')) return '';
		if (is_null(WC()->cart)) return '';
		ob_start();
		echo '<div class="esg-cartbutton-wrapper ' . esc_attr($this->get_special_class()) . '"';
		if ($this->spacing !== false) echo esc_attr($this->spacing);
		echo '>';
		echo '<div class="esg-cartbutton">';
		echo '<a href="' . esc_url(wc_get_cart_url()) . '">';
		echo '<i class="eg-icon-basket"></i><span class="ess-cart-content">';
		echo esc_html(WC()->cart->get_cart_contents_count());
		echo esc_html__(' items - ', 'essential-grid');
		wc_cart_totals_subtotal_html();
		echo '</span>';
		echo '</a>';
		echo '</div>';
		echo '</div>';

		$c = ob_get_clean();

		return apply_filters('essgrid_output_cart', $c);
	}

	public function output_navigation()
	{
		$n = '<article class="navigationbuttons ' . $this->get_special_class() . '">';
		$n .= self::output_navigation_left();
		$n .= self::output_navigation_right();
		$n .= '</article>';

		return apply_filters('essgrid_output_navigation', $n);
	}

	public function output_navigation_left()
	{
		$n = '<div class="esg-navigationbutton esg-left ' . $this->get_special_class() . '" ';
		if ($this->spacing !== false) $n .= $this->spacing;
		$n .= '><i class="eg-icon-left-open"></i></div>';

		return apply_filters('essgrid_output_navigation_left', $n);
	}

	public function output_navigation_right()
	{
		$n = '<div class="esg-navigationbutton esg-right ' . $this->get_special_class() . '" ';
		if ($this->spacing !== false) $n .= $this->spacing;
		$n .= '><i class="eg-icon-right-open"></i></div>';

		return apply_filters('essgrid_output_navigation_right', $n);
	}

	public function output_pagination($backend = false)
	{
		$p = '<div class="esg-pagination ' . $this->get_special_class() . '"';
		if ($this->spacing !== false) $p .= $this->spacing;
		$p .= '></div>';

		return apply_filters('essgrid_output_pagination', $p, $backend);
	}

	public function output_navigation_skin( $handle ) {
		$css = Essential_Grid_Db::get_entity( 'nav_skins' )->get_by_handle( $handle );

		$n = '';
		if ( isset( $css['css'] ) ) {
			$css = apply_filters( 'essgrid_before_output_navigation_skin', $css );
			$n   = '<style type="text/css">' . Essential_Grid_Base::compress_assets( $css['css'] ) . '</style>' . "\n";
		}

		return apply_filters( 'essgrid_output_navigation_skin', $n, $handle );
	}

	public static function output_navigation_skins() {
		$skins = self::get_essential_navigation_skins();

		$css = '';
		if ( ! empty( $skins ) ) {
			foreach ( $skins as $skin ) {
				$skin = apply_filters( 'essgrid_before_output_navigation_skin', $skin );
				$css  .= '<style class="navigation-skin-css-' . esc_attr( $skin['id'] ) . '" type="text/css">';
				$css  .= Essential_Grid_Base::compress_assets( $skin['css'] );
				$css  .= '</style>' . "\n";
			}
		}

		return apply_filters( 'essgrid_output_navigation_skins', $css );
	}

	private function translate_demo_filter($name)
	{
		$post = Essential_Grid_Item_Element::getPostElementsArray();
		$event = Essential_Grid_Item_Element::getEventElementsArray();
		$woocommerce = [];
		if (Essential_Grid_Woocommerce::is_woo_exists()) {
			$tmp_wc = Essential_Grid_Woocommerce::get_meta_array();
			foreach ($tmp_wc as $handle => $wc_name) {
				$woocommerce[$handle]['name'] = $wc_name;
			}
		}

		if (array_key_exists($name, $post)) return $post[$name]['name'];
		if (array_key_exists($name, $event)) return $event[$name]['name'];
		if (array_key_exists($name, $woocommerce)) return $woocommerce[$name]['name'];

		return apply_filters('essgrid_translate_demo_filter', ucwords($name), $name);
	}

	/**
	 * Change the Search Text
	 * @since: 2.0
	 */
	public function set_search_text($text)
	{
		$this->search_text = apply_filters('essgrid_set_search_text', $text);
	}

	/**
	 * Output the Search Input Field
	 * @since: 2.0
	 */
	public function output_search_input()
	{
		$s = '<div class="esg-filter-wrapper eg-search-wrapper' . $this->get_special_class() . '"';
		if ($this->spacing !== false) $s .= $this->spacing;
		$s .= '>';
		$s .= '<input name="eg-search-input-' . esc_attr($this->grid_id) . '" class="eg-search-input" type="text" value="" placeholder="' . esc_attr($this->search_text) . '">';
		$s .= '<span class="eg-search-submit"><i class="eg-icon-search"></i></span>';
		$s .= '<span class="eg-search-clean"><i class="eg-icon-cancel"></i></span>';
		$s .= '</div>';

		$this->search_found = true; //trigger this to add

		return apply_filters('essgrid_output_search_input', $s);
	}

	/**
	 * Output "Filter All" markup if search is existing in any container
	 * @since: 2.0.7
	 */
	public function check_for_search()
	{
		$s = '';
		if ($this->filter_added === false && $this->search_found === true) {
			$s .= '<article class="esg-filters esg-' . esc_attr($this->filter_type) . ' esg-display-none-i">';
			$s .= '<div class="esg-filter-wrapper ' . $this->get_special_class() . '">';
			$s .= '<div class="esg-filterbutton selected esg-allfilter" data-filter="filterall" data-fid="-1" ><span>' . esc_html($this->filter_all_text['filter']) . '</span></div>';
			$s .= '</div>';
			$s .= '</article>';
		}

		return apply_filters('essgrid_check_for_search', $s, $this->filter_added, $this->search_found);
	}

	public function array_key_last(array $array) {
		if( !empty($array) ) return key(array_slice($array, -1, 1, true));
		return null;
	}

}
