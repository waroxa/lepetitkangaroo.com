<?php
/**
 * Post Type Class For Essential Grid
 * Adds custom post type & taxonomy
 *
 * @package Essential_Grid
 * @author  ThemePunch <info@themepunch.com>
 * @link    https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

class Essential_Grid_Post_Type
{
	public function __construct()
	{
		$add_cpt = Essential_Grid_Base::getCpt();
		if ( $add_cpt == 'true' ) {
			add_action( 'init', [ $this, 'register_custom_post_type' ] );
		}
	}
	
	/**
	 * Register Custom Post Type & Taxonomy
	 */
	public function register_custom_post_type()
	{
		$postType = apply_filters('essgrid_PunchPost_custom_post_type', 'essential_grid');
		$taxonomy = apply_filters('essgrid_PunchPost_category', 'essential_grid_category');

		$taxArgs = [];
		$taxArgs["hierarchical"] = true;
		$taxArgs["label"] = esc_attr__("Categories", 'essential-grid');
		$taxArgs["singular_label"] = esc_attr__("Category", 'essential-grid');
		$taxArgs["rewrite"] = true;
		$taxArgs["public"] = true;
		$taxArgs["show_admin_column"] = true;

		$postArgs = [];
		$postArgs["label"] = esc_attr__("ESG Posts", 'essential-grid');
		$postArgs["singular_label"] = esc_attr__("Grid Post", 'essential-grid');
		$postArgs["public"] = true;
		$postArgs["capability_type"] = "post";
		$postArgs["hierarchical"] = false;
		$postArgs["show_ui"] = true;
		$postArgs["show_in_menu"] = true;
		$postArgs["show_in_rest"] = true;
		$postArgs["supports"] = [
			'title',
			'editor',
			'thumbnail',
			'author',
			'comments',
			'excerpt'
		];
		$postArgs["show_in_admin_bar"] = false;
		$postArgs["taxonomies"] = [
			$taxonomy,
			'post_tag'
		];

		$postArgs["rewrite"] = [
			"slug" => $postType,
			"with_front" => true
		];

		$d = apply_filters('essgrid_register_custom_post_type', [
			'postArgs' => $postArgs,
			'taxArgs' => $taxArgs
		]);
		$postArgs = $d['postArgs'];
		$taxArgs = $d['taxArgs'];

		register_taxonomy($taxonomy, [
			$postType
		], $taxArgs);
		register_post_type($postType, $postArgs);
	}
}
