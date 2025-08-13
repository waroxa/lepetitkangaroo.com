<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Item_Element
{

	/**
	 * Return all Item Elements
	 */
	public static function get_essential_item_elements()
	{
		return Essential_Grid_Db::get_entity('elements')->get_all();
	}

	/**
	 * Update Item Element by ID from Database
	 */
	public static function update_create_essential_item_element( $data ) {
		if ( empty( $data['name'] ) ) return esc_attr__( 'Empty element name', 'essential-grid' );

		$data['handle'] = sanitize_title( $data['name'] );

		$element = Essential_Grid_Db::get_entity('elements')->get_by_handle( $data['handle'] );
		if (!empty($element)) {
			$success = self::update_essential_item_element(apply_filters('essgrid_update_create_essential_item_element', $data, 'update'));
		} else {
			$success = self::insert_essential_item_element(apply_filters('essgrid_update_create_essential_item_element', $data, 'insert'));
		}

		return $success;
	}

	/**
	 * Update Item Element by ID from Database
	 */
	public static function update_essential_item_element($data) {
		if (empty($data['settings'])) return esc_attr__('Element Item has no attributes', 'essential-grid');

		//check if element is default element (these are not deletable)
		$is_default = self::isDefaultElement($data['name']);
		if ($is_default) return esc_attr__('Choosen name is reserved for default Item Elements. Please choose a different name', 'essential-grid');
		
		$data['settings'] = self::clean_settings_from_elements($data['settings']);
		$data = apply_filters('essgrid_update_essential_item_element', $data);

		$response = Essential_Grid_Db::get_entity('elements')->update_by_handle( [ 'settings' => wp_json_encode($data['settings']) ], $data['handle'] );
		if ($response === false) return esc_attr__('Element Item could not be changed', 'essential-grid');

		return true;
	}

	/**
	 * Insert Item Element by ID from Database
	 */
	public static function insert_essential_item_element($data)
	{
		if (empty($data['settings'])) return esc_attr__('Element Item has no attributes', 'essential-grid');

		//check if element is default element (these are not deletable)
		$is_default = self::isDefaultElement($data['name']);
		if ($is_default) return esc_attr__('Choosen name is reserved for default Item Elements. Please choose a different name', 'essential-grid');

		$data['settings'] = self::clean_settings_from_elements($data['settings']);
		$data = apply_filters('essgrid_insert_essential_item_element', $data);

		$response = Essential_Grid_Db::get_entity('elements')->insert( [ 'name' => $data['name'], 'handle' => $data['handle'], 'settings' => wp_json_encode($data['settings']) ] );
		if ($response === false) return false;

		return true;
	}

	/**
	 * Delete Item Element by handle from Database
	 */
	public static function delete_element_by_handle($data)
	{
		$data = apply_filters('essgrid_delete_element_by_handle', $data);
		if (empty($data['handle'])) return esc_attr__('Element Item does not exist', 'essential-grid');

		//check if element is default element (these are not deletable)
		$is_default = self::isDefaultElement($data['handle']);
		if ($is_default) return esc_attr__('Default Item Elements can\'t be deleted', 'essential-grid');

		$response = Essential_Grid_Db::get_entity('elements')->delete_by_handle( $data['handle'] );
		if ($response === false) return esc_attr__('Element Item could not be deleted', 'essential-grid');

		return true;
	}

	/**
	 * Clean the "element-" from the settings
	 */
	public static function clean_settings_from_elements($settings)
	{
		if (empty($settings)) return $settings;
		if (!is_array($settings)) return str_replace('element-', '', $settings);

		$clean_setting = [];
		foreach ($settings as $key => $value) {
			$clean_setting[str_replace('element-', '', $key)] = $value;
		}

		return apply_filters('essgrid_clean_settings_from_elements', $clean_setting, $settings);
	}

	/**
	 * Get Array of Text Elements
	 */
	public static function getTextElementsArray()
	{
		$custom = [];
		$elements = self::get_essential_item_elements();
		if (!empty($elements)) {
			foreach ($elements as $element) {
				$custom[$element['handle']] = ['id' => $element['id'], 'name' => $element['name'], 'settings' => json_decode($element['settings'], true)];
			}
		}
		Essential_Grid_Base::stripslashes_deep($custom);

		return apply_filters('essgrid_getTextElementsArray', $custom, $elements);
	}

	/**
	 * Get Array of Special Elements
	 */
	public static function getSpecialElementsArray()
	{
		$default = [
			'eg-line-break' => [
				'id' => '-1',
				'name' => 'eg-line-break',
				'display' => '<i class="eg-icon-level-down"></i><span>' . esc_html__('LINEBREAK ELEMENT', 'essential-grid') . '</span>',
				'settings' => [
					'background-color' => '#FFFFFF',
					'bg-alpha' => '20',
					'clear' => 'both',
					'border-width' => '0',
					'color' => 'transparent',
					'display' => 'block',
					'font-size' => '10',
					'font-style' => 'italic',
					'font-weight' => '700',
					'line-height' => '5',
					'margin' => ['0', '0', '0', '0'],
					'padding' => ['0', '0', '0', '0'],
					'text-align' => 'center',
					'transition' => 'none',
					'text-transform' => 'uppercase',
					'letter-spacing' => 'normal',
					'source' => 'text',
					'source-text' => esc_attr__('LINE-BREAK', 'essential-grid'),
					'special' => 'true',
					'special-type' => 'line-break'
				]
			]
		];

		return apply_filters('essgrid_getSpecialElementsArray', $default);
	}

	/**
	 * Get Array of Additional Elements
	 * @since: 2.0
	 */
	public static function getAdditionalElementsArray()
	{
		$default = [
			'eg-blank-element' => [
				'id' => '-2',
				'name' => 'eg-blank-element',
				'display' => '<i class="eg-icon-doc"></i><span>' . esc_html__('Blank HTML', 'essential-grid') . '</span>',
				'settings' => [
					'background-color' => 'transparent',
					'source-text-style-disable' => 'true',
					'bg-alpha' => '20',
					'clear' => 'both',
					'border-width' => '0',
					'color' => '#000000',
					'display' => 'block',
					'font-size' => '13',
					'font-weight' => '400',
					'line-height' => '15',
					'margin' => ['0', '0', '0', '0'],
					'padding' => ['0', '0', '0', '0'],
					'text-align' => 'center',
					'transition' => 'none',
					'source' => 'text',
					'source-text' => esc_attr__('Blank HTML', 'essential-grid'),
					'special' => 'true',
					'special-type' => 'blank-element'
				]
			]
		];

		return apply_filters('essgrid_getAdditionalElementsArray', $default);
	}

	/**
	 * Get Array of Post Elements
	 */
	public static function getPostElementsArray()
	{
		$post = [
			'title' => ['name' => esc_attr__('Title', 'essential-grid'), 'type' => 'text'],
			'cat_list' => ['name' => esc_attr__('Cat. List', 'essential-grid'), 'type' => 'text'],
			'tag_list' => ['name' => esc_attr__('Tag List', 'essential-grid'), 'type' => 'text'],
			'excerpt' => ['name' => esc_attr__('Excerpt', 'essential-grid'), 'type' => 'text'],
			'meta' => ['name' => esc_attr__('Meta', 'essential-grid'), 'type' => 'text'],
			'num_comments' => ['name' => esc_attr__('Num. Comments', 'essential-grid'), 'type' => 'text'],
			'date' => ['name' => esc_attr__('Date', 'essential-grid'), 'type' => 'text'],
			'date_day' => ['name' => esc_attr__('Date Day', 'essential-grid'), 'type' => 'text'],
			'date_month' => ['name' => esc_attr__('Date Month', 'essential-grid'), 'type' => 'text'],
			'date_month_abbr' => ['name' => esc_attr__('Date Month Abbr.', 'essential-grid'), 'type' => 'text'],
			'date_month_name' => ['name' => esc_attr__('Date Month Name', 'essential-grid'), 'type' => 'text'],
			'date_year' => ['name' => esc_attr__('Date Year', 'essential-grid'), 'type' => 'text'],
			'date_year_abbr' => ['name' => esc_attr__('Date Year Abbr.', 'essential-grid'), 'type' => 'text'],
			'date_modified' => ['name' => esc_attr__('Date Modified', 'essential-grid'), 'type' => 'text'],
			'author_name' => ['name' => esc_attr__('Author Name', 'essential-grid'), 'type' => 'text'],
			'author_profile' => ['name' => esc_attr__('Author Website', 'essential-grid'), 'type' => 'text'],
			'author_posts' => ['name' => esc_attr__('Author Posts Page', 'essential-grid'), 'type' => 'text'],
			'author_avatar_32' => ['name' => esc_attr__('Author Avatar 32px', 'essential-grid'), 'type' => 'text'],
			'author_avatar_64' => ['name' => esc_attr__('Author Avatar 64px', 'essential-grid'), 'type' => 'text'],
			'author_avatar_96' => ['name' => esc_attr__('Author Avatar 96px', 'essential-grid'), 'type' => 'text'],
			'author_avatar_512' => ['name' => esc_attr__('Author Avatar 512px', 'essential-grid'), 'type' => 'text'],
			'post_id' => ['name' => esc_attr__('Post ID', 'essential-grid'), 'type' => 'text'],
			'post_url' => ['name' => esc_attr__('Post URL', 'essential-grid'), 'type' => 'text'],
			'content' => ['name' => esc_attr__('Post Content', 'essential-grid'), 'type' => 'text'],
			'alternate-image' => ['name' => esc_attr__('Alt. Image', 'essential-grid'), 'type' => 'image'],
			'alias' => ['name' => esc_attr__('Alias', 'essential-grid'), 'type' => 'text'],
			'taxonomy' => ['name' => esc_attr__('Taxonomy List', 'essential-grid'), 'type' => 'text'],
			'caption' => ['name' => esc_attr__('Caption', 'essential-grid'), 'type' => 'text'],
			'description' => ['name' => esc_attr__('Description', 'essential-grid'), 'type' => 'text'],
			'likespost' => ['name' => esc_attr__('Likes (Posts)', 'essential-grid'), 'type' => 'text'],
			'likes' => ['name' => esc_attr__('Likes (Facebook,YouTube,Vimeo,Instagram)', 'essential-grid'), 'type' => 'text'],
			'likes_short' => ['name' => esc_attr__('Likes Short (Facebook,YouTube,Vimeo,Instagram)', 'essential-grid'), 'type' => 'text'],
			'dislikes' => ['name' => esc_attr__('Dislikes (YouTube)', 'essential-grid'), 'type' => 'text'],
			'dislikes_short' => ['name' => esc_attr__('Dislikes Short (YouTube)', 'essential-grid'), 'type' => 'text'],
			'favorites' => ['name' => esc_attr__('Favorites (YouTube, flickr)', 'essential-grid'), 'type' => 'text'],
			'favorites_short' => ['name' => esc_attr__('Favorites Short (YouTube, flickr)', 'essential-grid'), 'type' => 'text'],
			'retweets' => ['name' => esc_attr__('Retweets (Twitter)', 'essential-grid'), 'type' => 'text'],
			'retweets_short' => ['name' => esc_attr__('Retweets Short (Twitter)', 'essential-grid'), 'type' => 'text'],
			'views' => ['name' => esc_attr__('Views (flickr,YouTube, Vimeo)', 'essential-grid'), 'type' => 'text'],
			'views_short' => ['name' => esc_attr__('Views Short (flickr,YouTube, Vimeo)', 'essential-grid'), 'type' => 'text'],
			'itemCount' => ['name' => esc_attr__('Playlist Item Count (YouTube)', 'essential-grid'), 'type' => 'text'],
			'channel_title' => ['name' => esc_attr__('Channel Title (YouTube)', 'essential-grid'), 'type' => 'text'],
			'duration' => ['name' => esc_attr__('Duration (Vimeo)', 'essential-grid'), 'type' => 'text'],
			'iframe' => ['name' => esc_attr__('iFrame (url)', 'essential-grid'), 'type' => 'text'],
			'revslider' => ['name' => esc_attr__('Slider Revolution', 'essential-grid'), 'type' => 'revslider'],
			'essgrid' => ['name' => esc_attr__('Essential Grid', 'essential-grid'), 'type' => 'essgrid'],
			'wistia' => ['name' => esc_attr__('Wistia Video (ID)', 'essential-grid'), 'type' => 'wistia']
		];

		$post = apply_filters('essgrid_post_meta_handle', $post); //stays for backwards compatibility
		return apply_filters('essgrid_getPostElementsArray', $post);
	}

	/**
	 * Get Array of Event Elements
	 */
	public static function getEventElementsArray()
	{
		$event = [
			'event_start_date' => ['name' => esc_attr__('Event Start Date', 'essential-grid')],
			'event_end_date' => ['name' => esc_attr__('Event End Date', 'essential-grid')],
			'event_start_time' => ['name' => esc_attr__('Event Start Time', 'essential-grid')],
			'event_end_time' => ['name' => esc_attr__('Event End Time', 'essential-grid')],
			'event_event_id' => ['name' => esc_attr__('Event Event ID', 'essential-grid')],
			'event_location_name' => ['name' => esc_attr__('Event Location Name', 'essential-grid')],
			'event_location_slug' => ['name' => esc_attr__('Event Location Slug', 'essential-grid')],
			'event_location_address' => ['name' => esc_attr__('Event Location Address', 'essential-grid')],
			'event_location_town' => ['name' => esc_attr__('Event Location Town', 'essential-grid')],
			'event_location_state' => ['name' => esc_attr__('Event Location State', 'essential-grid')],
			'event_location_postcode' => ['name' => esc_attr__('Event Location Postcode', 'essential-grid')],
			'event_location_region' => ['name' => esc_attr__('Event Location Region', 'essential-grid')],
			'event_location_country' => ['name' => esc_attr__('Event Location Country', 'essential-grid')]
		];

		return apply_filters('essgrid_getEventElementsArray', $event);
	}

	/**
	 * Get Array of Default Elements
	 */
	public static function getDefaultElementsArray()
	{
		$default = [];
		include('assets/default-item-elements.php');
		return apply_filters('essgrid_getDefaultElementsArray', $default);
	}

	/**
	 * Check if element is default one
	 */
	public static function isDefaultElement($handle)
	{
		$sanitized_handle = sanitize_title($handle);
		$default = self::getDefaultElementsArray();
		foreach ($default as $_handle => $_settings) {
			if ($_handle == $sanitized_handle) return true;
		}
		return false;
	}

	/**
	 * Get Array of Elements
	 * 
	 * @param array $elements
	 * @param bool $set_loaded
	 * @return string
	 */
	public static function prepareElementsForEditor($elements, $set_loaded = false)
	{
		$html = '';
		$load_class = '';

		if ($set_loaded)
			$load_class = ' eg-newli';

		foreach ($elements as $handle => $element) {
			$filter_type = 'text';
			$data_id = 1;
			if (!empty($element['settings'])) {
				if ($element['settings']['source'] == 'icon') {
					$text = '<i class="' . esc_attr($element['settings']['source-icon']) . '"></i>';
				} elseif ($element['settings']['source'] == 'text') {
					$text = $element['settings']['source-text'];
				} else {
					$text = esc_html($element['name']);
				}
				if ($element['settings']['source'] == 'icon') $filter_type = 'icon';
				$data_id = $element['id'];
			} else {
				$text = $element['name'];
			}

			if (isset($element['default']) && $element['default'] == 'true') $filter_type .= ' filter-default';

			$html .= '<li class="filterall filter-' . esc_attr($filter_type . $load_class) . '" data-title="' . esc_attr(strtolower($handle)) . '" data-date="' . esc_attr($data_id) . '">' . "\n";
			$html .= '   <div class="esg-entry-content">';
			$html .= '       <div class="eg-elements-format-wrapper"><div class="skin-dz-elements" data-handle="' . esc_attr($handle) . '">';
			$html .= $text;
			$html .= '       </div></div>' . "\n";
			$html .= '   </div>' . "\n";
			$html .= '</li>' . "\n";
		}

		return apply_filters('essgrid_prepareElementsForEditor', $html, $elements, $set_loaded);
	}

	/**
	 * Get Array of Special Elements
	 */
	public static function prepareSpecialElementsForEditor()
	{
		$html = '';
		$elements = self::getSpecialElementsArray();
		foreach ($elements as $handle => $element) {
			if (!empty($element['settings'])) {
				$text = $element['display'];
			} else {
				$text = $element['name'];
			}
			$html .= '<div class="skin-dz-elements eg-special-element" data-handle="' . esc_attr($handle) . '">';
			$html .= $text;
			$html .= '</div>' . "\n";
		}

		return apply_filters('essgrid_prepareSpecialElementsForEditor', $html, $elements);
	}

	/**
	 * Get Array of Additional Elements
	 */
	public static function prepareAdditionalElementsForEditor()
	{
		$html = '';
		$elements = self::getAdditionalElementsArray();
		foreach ($elements as $handle => $element) {
			if (!empty($element['settings'])) {
				$text = $element['display'];
			} else {
				$text = $element['name'];
			}
			$html .= '<div class="skin-dz-elements eg-special-blank-element eg-additional-element eg-special-element-margin" data-handle="' . esc_attr($handle) . '">';
			$html .= $text;
			$html .= '</div>' . "\n";
		}

		return apply_filters('essgrid_prepareAdditionalElementsForEditor', $html, $elements);
	}

	/**
	 * Get Array of Default Elements
	 */
	public static function prepareDefaultElementsForEditor()
	{
		return self::prepareElementsForEditor(self::getDefaultElementsArray(), true);
	}

	/**
	 * Get Array of Post Elements
	 */
	public static function prepareTextElementsForEditor()
	{
		$elements = self::getTextElementsArray();
		$elements = apply_filters('essgrid_prepareTextElementsForEditor', $elements);

		return self::prepareElementsForEditor($elements, true);
	}

	/**
	 * Get Array of Elements
	 */
	public static function getElementsForJavascript()
	{
		$default = self::getDefaultElementsArray();
		$text = self::getTextElementsArray();
		$special = self::getSpecialElementsArray();
		$additional = self::getAdditionalElementsArray();

		$all = array_merge($default, $text, $special, $additional);

		return apply_filters('essgrid_getElementsForJavascript', $all);
	}

	/**
	 * Get Array of Elements
	 */
	public static function getElementsForDropdown()
	{
		$post = self::getPostElementsArray();
		$all['post'] = $post;

		if (Essential_Grid_Woocommerce::is_woo_exists()) {
			$woocommerce = [];
			$tmp_wc = Essential_Grid_Woocommerce::get_meta_array();
			foreach ($tmp_wc as $handle => $name) {
				$woocommerce[$handle]['name'] = $name;
			}
			$all['woocommerce'] = $woocommerce;
		}

		return apply_filters('essgrid_getElementsForDropdown', $all);
	}

	/**
	 * create css from settings
	 */
	public static function get_existing_elements($only_styles = false)
	{
		$styles = [
			'font-size' => [
				'value' => 'int',
				'type' => 'text-slider',
				'values' => ['min' => '6', 'max' => '120', 'step' => '1', 'default' => '12'],
				'style' => 'idle',
				'unit' => 'px'],

			'line-height' => [
				'value' => 'int',
				'type' => 'text-slider',
				'values' => ['min' => '7', 'max' => '150', 'step' => '1', 'default' => '14'],
				'style' => 'idle',
				'unit' => 'px'],

			'color' => [
				'value' => 'string',
				'type' => 'colorpicker',
				'values' => ['default' => '#000'],
				'style' => 'idle',
				'unit' => ''],

			'font-family' => [
				'value' => 'string',
				'values' => ['default' => ''],
				'style' => 'idle',
				'type' => 'text',
				'unit' => ''],

			'font-weight' => [
				'value' => 'string',
				'values' => ['default' => '400'],
				'style' => 'idle',
				'type' => 'select',
				'unit' => ''],

			'text-decoration' => [
				'value' => 'string',
				'values' => ['default' => 'none'],
				'style' => 'idle',
				'type' => 'select',
				'unit' => ''],

			'font-style' => [
				'value' => 'string',
				'values' => ['default' => false],
				'style' => 'idle',
				'type' => 'checkbox',
				'unit' => ''],

			'text-transform' => [
				'value' => 'string',
				'values' => ['default' => 'none'],
				'style' => 'idle',
				'type' => 'select',
				'unit' => ''],

			'letter-spacing' => [
				'value' => 'string',
				'values' => ['default' => 'normal'],
				'style' => 'idle',
				'type' => 'text',
				'unit' => ''],

			'display' => [
				'value' => 'string',
				'values' => ['default' => 'inline-block'],
				'style' => 'idle',
				'type' => 'select',
				'unit' => ''],

			'float' => [
				'value' => 'string',
				'values' => ['default' => 'none'],
				'style' => 'idle',
				'type' => 'select',
				'unit' => ''],

			'text-align' => [
				'value' => 'string',
				'values' => ['default' => 'center'],
				'style' => 'idle',
				'type' => 'select',
				'unit' => ''],

			'clear' => [
				'value' => 'string',
				'values' => ['default' => 'none'],
				'style' => 'idle',
				'type' => 'select',
				'unit' => ''],

			'margin' => [
				'value' => 'int',
				'type' => 'multi-text',
				'values' => ['default' => '0'],
				'style' => 'idle',
				'unit' => 'px'],

			'padding' => [
				'value' => 'int',
				'type' => 'multi-text',
				'values' => ['default' => '0'],
				'style' => 'idle',
				'unit' => 'px'],

			'border' => [
				'value' => 'int',
				'type' => 'multi-text',
				'values' => ['default' => '0'],
				'style' => 'idle',
				'unit' => 'px'],

			'border-radius' => [
				'value' => 'int',
				'type' => 'multi-text',
				'values' => ['default' => '0'],
				'style' => 'idle',
				'unit' => ['px', 'percentage']],

			'border-color' => [
				'value' => 'string',
				'values' => ['default' => 'transparent'],
				'style' => 'idle',
				'type' => 'colorpicker',
				'unit' => ''],

			'border-style' => [
				'value' => 'string',
				'values' => ['default' => 'solid'],
				'style' => 'idle',
				'type' => 'select',
				'unit' => ''],

			'background-color' => [
				'value' => 'string',
				'type' => 'colorpicker',
				'values' => ['default' => '#FFF'],
				'style' => 'idle',
				'unit' => ''],

			'bg-alpha' => [
				'value' => 'string',
				'values' => ['min' => '0', 'max' => '100', 'step' => '1', 'default' => '100'],
				'style' => 'false',
				'type' => 'text-slider',
				'unit' => ''],

			'shadow-color' => [
				'value' => 'string',
				'type' => 'colorpicker',
				'values' => ['default' => '#000'],
				'style' => 'false',
				'unit' => ''],

			'shadow-alpha' => [
				'value' => 'string',
				'values' => ['min' => '0', 'max' => '100', 'step' => '1', 'default' => '100'],
				'style' => 'false',
				'type' => 'text-slider',
				'unit' => ''],

			'box-shadow' => [
				'value' => 'int',
				'type' => 'multi-text',
				'values' => ['default' => '0'],
				'style' => 'idle',
				'unit' => 'px'],

			'position' => [
				'value' => 'string',
				'type' => 'select',
				'values' => ['default' => 'relative'],
				'style' => 'idle',
				'unit' => ''],

			'top-bottom' => [
				'value' => 'int',
				'type' => 'text',
				'values' => ['default' => '0'],
				'style' => 'false',
				'unit' => 'px'],

			'left-right' => [
				'value' => 'int',
				'type' => 'text',
				'values' => ['default' => '0'],
				'style' => 'false',
				'unit' => 'px']

		];

		$styles = apply_filters('essgrid_get_existing_elements_styles', $styles, $only_styles);

		$hover_styles = [
			'font-size-hover' => [
				'value' => 'int',
				'type' => 'text-slider',
				'values' => ['min' => '6', 'max' => '120', 'step' => '1', 'default' => '12'],
				'style' => 'hover',
				'unit' => 'px'],

			'line-height-hover' => [
				'value' => 'int',
				'type' => 'text-slider',
				'values' => ['min' => '7', 'max' => '150', 'step' => '1', 'default' => '14'],
				'style' => 'hover',
				'unit' => 'px'],

			'color-hover' => [
				'value' => 'string',
				'type' => 'colorpicker',
				'values' => ['default' => '#000'],
				'style' => 'hover',
				'unit' => ''],

			'font-family-hover' => [
				'value' => 'string',
				'values' => ['default' => ''],
				'style' => 'hover',
				'type' => 'text',
				'unit' => ''],

			'font-weight-hover' => [
				'value' => 'string',
				'values' => ['default' => '400'],
				'style' => 'hover',
				'type' => 'select',
				'unit' => ''],

			'text-decoration-hover' => [
				'value' => 'string',
				'values' => ['default' => 'none'],
				'style' => 'hover',
				'type' => 'select',
				'unit' => ''],

			'font-style-hover' => [
				'value' => 'string',
				'values' => ['default' => false],
				'style' => 'hover',
				'type' => 'checkbox',
				'unit' => ''],

			'text-transform-hover' => [
				'value' => 'string',
				'values' => ['default' => 'none'],
				'style' => 'hover',
				'type' => 'select',
				'unit' => ''],

			'letter-spacing-hover' => [
				'value' => 'string',
				'values' => ['default' => 'normal'],
				'style' => 'hover',
				'type' => 'text',
				'unit' => ''],

			'border-hover' => [
				'value' => 'int',
				'type' => 'multi-text',
				'values' => ['default' => '0'],
				'style' => 'hover',
				'unit' => 'px'],

			'border-radius-hover' => [
				'value' => 'int',
				'type' => 'multi-text',
				'values' => ['default' => '0'],
				'style' => 'hover',
				'unit' => ['px', 'percentage']],

			'border-color-hover' => [
				'value' => 'string',
				'values' => ['default' => 'transparent'],
				'style' => 'hover',
				'type' => 'colorpicker',
				'unit' => ''],

			'border-style-hover' => [
				'value' => 'string',
				'values' => ['default' => 'solid'],
				'style' => 'hover',
				'type' => 'select',
				'unit' => ''],

			'background-color-hover' => [
				'value' => 'string',
				'type' => 'colorpicker',
				'values' => ['default' => '#FFF'],
				'style' => 'hover',
				'unit' => ''],

			'bg-alpha-hover' => [
				'value' => 'string',
				'values' => ['min' => '0', 'max' => '100', 'step' => '1', 'default' => '100'],
				'style' => 'false',
				'type' => 'text-slider',
				'unit' => ''],

			'shadow-color-hover' => [
				'value' => 'string',
				'type' => 'colorpicker',
				'values' => ['default' => '#000'],
				'style' => 'false',
				'unit' => ''],

			'shadow-alpha-hover' => [
				'value' => 'string',
				'values' => ['min' => '0', 'max' => '100', 'step' => '1', 'default' => '100'],
				'style' => 'false',
				'type' => 'text-slider',
				'unit' => ''],

			'box-shadow-hover' => [
				'value' => 'int',
				'type' => 'multi-text',
				'values' => ['default' => '0'],
				'style' => 'hover',
				'unit' => 'px'],
		];

		$hover_styles = apply_filters('essgrid_get_existing_elements_hover_styles', $hover_styles, $only_styles);

		$other = [];
		if (!$only_styles) {
			$other = [
				'source' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => 'post'],
					'style' => 'false',
					'unit' => ''],

				'transition' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => 'fade'],
					'style' => 'attribute',
					'unit' => ''],

				'source-separate' => [
					'value' => 'string',
					'type' => 'text',
					'values' => ['default' => ','],
					'style' => 'attribute',
					'unit' => ''],

				'source-catmax' => [
					'value' => 'string',
					'type' => 'text',
					'values' => ['default' => '-1'],
					'style' => 'attribute',
					'unit' => ''],

				'always-visible-desktop' => [
					'value' => 'string',
					'type' => 'checkbox',
					'values' => ['default' => ''],
					'style' => 'false',
					'unit' => ''],

				'always-visible-mobile' => [
					'value' => 'string',
					'type' => 'checkbox',
					'values' => ['default' => ''],
					'style' => 'false',
					'unit' => ''],

				'source-function' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => 'link'],
					'style' => 'attribute',
					'unit' => ''],

				'limit-type' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => 'none'],
					'style' => 'attribute',
					'unit' => ''],

				'limit-num' => [
					'value' => 'string',
					'type' => 'text',
					'values' => ['default' => '10'],
					'style' => 'attribute',
					'unit' => ''],

				'min-height' => [
					'value' => 'string',
					'type' => 'text',
					'values' => ['default' => '0'],
					'style' => 'attribute',
					'unit' => ''],

				'max-height' => [
					'value' => 'string',
					'type' => 'text',
					'values' => ['default' => 'none'],
					'style' => 'attribute',
					'unit' => ''],

				'transition-type' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => ''],
					'style' => 'false',
					'unit' => ''],

				'delay' => [
					'value' => 'string',
					'type' => 'text-slider',
					'values' => ['min' => '0', 'max' => '60', 'step' => '1', 'default' => '10'],
					'style' => 'attribute',
					'unit' => ''],

				'duration' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => 'default'],
					'style' => 'false',
					'unit' => ''],

				'link-type' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => 'none'],
					'style' => 'false',
					'unit' => ''],

				'hideunder' => [
					'value' => 'string',
					'type' => 'text',
					'values' => ['default' => '0'],
					'style' => 'false',
					'unit' => ''],

				'hideunderheight' => [
					'value' => 'string',
					'type' => 'text',
					'values' => ['default' => '0'],
					'style' => 'false',
					'unit' => ''],

				'hidetype' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => 'visibility'],
					'style' => 'false',
					'unit' => ''],

				'hide-on-video' => [
					'value' => 'string',
					'type' => 'select', //was checkbock before with values 'false', 'true'
					'values' => ['default' => false],
					'style' => 'false',
					'unit' => ''],

				'show-on-lightbox-video' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => false],
					'style' => 'false',
					'unit' => ''],

				'enable-hover' => [
					'value' => 'string',
					'type' => 'checkbox',
					'values' => ['default' => false],
					'style' => 'false',
					'unit' => ''],

				'attribute' => [
					'value' => 'string',
					'type' => 'text',
					'values' => ['default' => ''],
					'style' => 'false',
					'unit' => ''],

				'class' => [
					'value' => 'string',
					'type' => 'text',
					'values' => ['default' => ''],
					'style' => 'false',
					'unit' => ''],

				'rel' => [
					'value' => 'string',
					'type' => 'text',
					'values' => ['default' => ''],
					'style' => 'false',
					'unit' => ''],

				'tag-type' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => 'div'],
					'style' => 'false',
					'unit' => ''],

				'rel-nofollow' => [
					'value' => 'string',
					'type' => 'checkbox',
					'values' => ['default' => false],
					'style' => 'false',
					'unit' => ''],

				'force-important' => [
					'value' => 'string',
					'type' => 'checkbox',
					'values' => ['default' => true],
					'style' => 'false',
					'unit' => ''],

				'align' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => 't_l'],
					'style' => 'false',
					'unit' => ''],

				'link-target' => [
					'value' => 'string',
					'type' => 'select',
					'values' => ['default' => '_self'],
					'style' => 'false',
					'unit' => ''],

				'source-text-style-disable' => [
					'value' => 'string',
					'type' => 'checkbox',
					'values' => ['default' => false],
					'style' => 'false',
					'unit' => '']
			];

			if (Essential_Grid_Woocommerce::is_woo_exists()) {
				$other['show-on-sale'] = [
					'value' => 'string',
					'type' => 'checkbox',
					'values' => ['default' => false],
					'style' => 'false',
					'unit' => ''];
				$other['show-if-featured'] = [
					'value' => 'string',
					'type' => 'checkbox',
					'values' => ['default' => false],
					'style' => 'false',
					'unit' => ''];
			}

			$other = apply_filters('essgrid_get_existing_elements_other', $other, $only_styles);
		}

		$styles = array_merge($styles, $other, $hover_styles);

		return apply_filters('essgrid_get_existing_elements', $styles, $only_styles);
	}

	/**
	 * get list of allowed styles on tags
	 */
	public static function get_allowed_styles_for_tags()
	{
		return apply_filters('essgrid_get_allowed_styles_for_tags',
			[
				'font-size',
				'line-height',
				'color',
				'font-family',
				'font-weight',
				'text-decoration',
				'font-style',
				'text-transform',
				'letter-spacing',
				'background-color'
			]
		);
	}

	/**
	 * get list of allowed styles on tags
	 */
	public static function get_allowed_styles_for_cat_tag()
	{
		return apply_filters('essgrid_get_allowed_styles_for_cat_tag',
			[
				'font-size',
				'line-height',
				'color',
				'font-family',
				'font-weight',
				'text-decoration',
				'font-style',
				'text-transform',
				'letter-spacing',
			]
		);
	}

	/**
	 * get list of allowed styles on wrap
	 */
	public static function get_allowed_styles_for_wrap()
	{
		return apply_filters('essgrid_get_allowed_styles_for_wrap',
			[
				'display',
				'clear',
				'position',
				'text-align',
				'margin',
				'float',
				'left',
				'top',
				'right',
				'bottom'
			]
		);
	}

	/**
	 * get list of allowed styles on wrap
	 */
	public static function get_wait_until_output_styles()
	{
		return apply_filters('essgrid_get_wait_until_output_styles',
			[
				'border-style' => [
					'wait' => ['border', 'border-color', 'border-style', 'border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width'],
					'not-if' => 'none'
				],
				'border-style-hover' => [
					'wait' => ['border-hover', 'border-color-hover', 'border-style-hover', 'border-top-width-hover', 'border-right-width-hover', 'border-bottom-width-hover', 'border-left-width-hover'],
					'not-if' => 'none'
				],
				'box-shadow' => [
					'wait' => ['box-shadow'],
					'not-if' => ['0px 0px 0px 0px', '0)']
				],
				'-moz-box-shadow' => [
					'wait' => ['-moz-box-shadow'],
					'not-if' => ['0px 0px 0px 0px', '0)']
				],
				'-webkit-box-shadow' => [
					'wait' => ['-webkit-box-shadow'],
					'not-if' => ['0px 0px 0px 0px', '0)']
				],
				'text-decoration' => [
					'wait' => ['text-decoration'],
					'not-if' => 'none'
				],
				'text-transform' => [
					'wait' => ['text-transform'],
					'not-if' => 'none'
				],
				'letter-spacing' => [
					'wait' => ['letter-spacing'],
					'not-if' => 'normal'
				],
				'font-family' => [
					'wait' => ['font-family'],
					'not-if' => ''
				]
			]
		);
	}

	/**
	 * get list of allowed things on meta
	 */
	public function get_allowed_meta()
	{
		$base = new Essential_Grid_Base();
		$transitions_media = $base->get_hover_animations(true); //true will get with in/out
		return apply_filters('essgrid_get_allowed_meta',
			[
				[
					'name' => ['handle' => 'color', 'text' => esc_attr__('Font Color', 'essential-grid')],
					'type' => 'color',
					'default' => '#FFFFFF',
					'container' => 'style',
					'hover' => 'true',
					'cpmode' => 'single'
				],
				[
					'name' => ['handle' => 'font-style', 'text' => esc_attr__('Font Style', 'essential-grid')],
					'type' => 'select',
					'default' => 'normal',
					'values' => ['normal' => esc_attr__('Normal', 'essential-grid'), 'italic' => esc_attr__('Italic', 'essential-grid')],
					'container' => 'style',
					'hover' => 'true'
				],
				[
					'name' => ['handle' => 'text-decoration', 'text' => esc_attr__('Text Decoration', 'essential-grid')],
					'type' => 'select',
					'default' => 'none',
					'values' => ['none' => esc_attr__('None', 'essential-grid'), 'underline' => esc_attr__('Underline', 'essential-grid'), 'overline' => esc_attr__('Overline', 'essential-grid'), 'line-through' => esc_attr__('Line Through', 'essential-grid')],
					'container' => 'style',
					'hover' => 'true'
				],
				[
					'name' => ['handle' => 'text-transform', 'text' => esc_attr__('Text Transform', 'essential-grid')],
					'type' => 'select',
					'default' => 'none',
					'values' => ['none' => esc_attr__('None', 'essential-grid'), 'capitalize' => esc_attr__('Capitalize', 'essential-grid'), 'uppercase' => esc_attr__('Uppercase', 'essential-grid'), 'lowercase' => esc_attr__('Lowercase', 'essential-grid')],
					'container' => 'style',
					'hover' => 'true'
				],
				[
					'name' => ['handle' => 'letter-spacing', 'text' => esc_attr__('Letter Spacing', 'essential-grid')],
					'type' => 'text',
					'default' => 'normal',
					'container' => 'style',
					'hover' => 'true'
				],
				[
					'name' => ['handle' => 'border-color', 'text' => esc_attr__('Border Color', 'essential-grid')],
					'type' => 'color',
					'default' => '#FFFFFF',
					'container' => 'style',
					'hover' => 'true',
					'cpmode' => 'single'
				],
				[
					'name' => ['handle' => 'border-style', 'text' => esc_attr__('Border Style', 'essential-grid')],
					'type' => 'select',
					'default' => 'none',
					'values' => ['none' => esc_attr__('None', 'essential-grid'), 'solid' => esc_attr__('solid', 'essential-grid'), 'dotted' => esc_attr__('dotted', 'essential-grid'), 'dashed' => esc_attr__('dashed', 'essential-grid'), 'double' => esc_attr__('double', 'essential-grid')],
					'container' => 'style',
					'hover' => 'true'
				],
				[
					'name' => ['handle' => 'background', 'text' => esc_attr__('Background Color', 'essential-grid')],
					'type' => 'color',
					'default' => '#FFFFFF',
					'container' => 'style',
					'hover' => 'true',
					'cpmode' => 'full'
				],
				[
					'name' => ['handle' => 'box-shadow', 'text' => esc_attr__('Box Shadow', 'essential-grid')],
					'type' => 'text',
					'default' => '0px 0px 0px 0px #000000',
					'container' => 'style',
					'hover' => 'true'
				],
				[
					'name' => ['handle' => 'transition', 'text' => esc_attr__('Transition', 'essential-grid')],
					'type' => 'select',
					'default' => 'fade',
					'values' => $transitions_media,
					'container' => 'anim'
				],
				[
					'name' => ['handle' => 'transition-delay', 'text' => esc_attr__('Transition Delay', 'essential-grid')],
					'type' => 'number',
					'default' => '0',
					'values' => ['0', '60', '1'],
					'container' => 'anim'
				],
				[
					'name' => ['handle' => 'cover-bg-color', 'text' => esc_attr__('Cover BG Color', 'essential-grid')],
					'type' => 'color',
					'default' => '#FFFFFF',
					'container' => 'layout',
					'cpmode' => 'full'
				],
				[
					'name' => ['handle' => 'item-bg-color', 'text' => esc_attr__('Item BG Color', 'essential-grid')],
					'type' => 'color',
					'default' => '#FFFFFF',
					'container' => 'layout',
					'cpmode' => 'full'
				],
				[
					'name' => ['handle' => 'content-bg-color', 'text' => esc_attr__('Content BG Color', 'essential-grid')],
					'type' => 'color',
					'default' => '#FFFFFF',
					'container' => 'layout',
					'cpmode' => 'full'
				],
			]
		);
	}
}
