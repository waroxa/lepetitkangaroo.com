<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */
 
if( !defined( 'ABSPATH') ) exit();

class Essential_Grid_Wpml
{
	public function __construct()
	{
		add_action( 'init', [ $this, 'init_wpml' ] );
	}
	
	public function init_wpml()
	{
		if (!$this->is_wpml_exists()) return;
		
		add_filter( 'essgrid_get_lang_code', [ $this, 'get_current_lang_code' ] );
		add_filter( 'essgrid_get_taxonomy_id', [ $this, 'get_taxonomy_id' ], 10, 2 );
		add_filter( 'essgrid_strip_category_additions', [ $this, 'strip_category_additions' ] );
	}

	/**
	 * is wpml plugin exists
	 * 
	 * @return bool
	 */
	public function is_wpml_exists(){
		return class_exists('SitePress') && function_exists('wpml_object_id_filter');
	}

	/**
	 * get current wpml language code
	 * 
	 * @return string
	 */
	public function get_current_lang_code(){
		return ICL_LANGUAGE_CODE;
	}

	/**
	 * @param int $id
	 * @param string $type
	 *
	 * @return int|NULL
	 */
	public function get_taxonomy_id($id, $type = 'category'){
		return wpml_object_id_filter($id, $type, true);
	}

	/**
	 * remove for example @en from categories
	 * 
	 * @param string $text
	 * @return string
	 */
	public function strip_category_additions($text){
		return apply_filters('essgrid_wpml_strip_category_additions', $text);
	}

}
