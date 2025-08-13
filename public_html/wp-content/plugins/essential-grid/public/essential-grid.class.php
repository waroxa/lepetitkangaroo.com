<?php
/**
 * Essential Grid.
 *
 * @package  Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid
{
	private $grid_api_name = null;
	private $grid_div_name = null;
	private $grid_id = 0; //set to 0 at beginning for quick grids @since 2.0.2
	private $grid_name = '';
	private $grid_handle = '';
	private $grid_params = [];
	private $grid_postparams = [];
	private $grid_layers = [];
	private $grid_settings = [];
	private $grid_inline_js = '';
	private $is_gallery = false;

	public $custom_settings = null;
	public $custom_layers = null;
	public $custom_images = null;
	public $custom_posts = null;
	public $custom_special = null;

	//other changings
	private $filter_by_ids = [];
	private $load_more_post_array = [];
	
	private $grid_images = [];

	/**
	 * Instance of this class.
	 */
	protected static $instance = null;
	/**
	 * @var bool 
	 */
	protected static $enqueue_tptools = false;
	/**
	 * @var bool 
	 */
	protected static $is_inited = false;
	/**
	 * @var string 
	 */
	protected static $sort_direction = 'ASC';
	/**
	 * @var string 
	 */
	protected static $sort_handle = 'title';
	/**
	 * @var int 
	 */
	protected static $grid_serial = 0;
	/**
	 * @var array 
	 */
	protected static $grid_posts = [];
	/**
	 * @var array 
	 */
	protected static $post_filters = [];
	/**
	 * @var array 
	 */
	protected static $layer_filters = [];

	/**
	 * @var Essential_Grid_Meta
	 */
	public static $meta;
	/**
	 * @var Essential_Grid_Meta_Linking
	 */
	public static $meta_link;
	/**
	 * @var Essential_Grid_Base
	 */
	public static $base;
	/**
	 * @var string
	 */
	public static $sr_engine = 7;
	/**
	 * @var string 
	 */
	private static $front_token = '';
	
	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 */
	public function __construct() {
		if (self::$is_inited) return;

		self::$base = new Essential_Grid_Base();
		self::$meta = new Essential_Grid_Meta();
		self::$meta_link = new Essential_Grid_Meta_Linking();
		if (defined('RS_REVISION') && function_exists('get_sr_current_engine')) {
			self::$sr_engine = get_sr_current_engine();
		}

		// Load plugin text domain
		add_action('init', [$this, 'load_plugin_textdomain']);

		// Load public-facing style sheet and JavaScript.
		add_action('wp_enqueue_scripts', [$this, 'enqueue_styles'], 20);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 20);

		add_action('wp_ajax_Essential_Grid_Front_request_ajax', [$this, 'on_front_ajax_action']);
		add_action('wp_ajax_nopriv_Essential_Grid_Front_request_ajax', [$this, 'on_front_ajax_action']); //for not logged-in users

		add_action('essgrid_before_front_ajax_action', [$this, 'before_front_ajax_action']);
		
		// Post Like
		add_action('wp_ajax_nopriv_ess_grid_post_like', [$this, 'ess_grid_post_like']);
		add_action('wp_ajax_ess_grid_post_like', [$this, 'ess_grid_post_like']);

		//Woo Add to Cart Updater
		add_filter('woocommerce_add_to_cart_fragments', ['Essential_Grid_Woocommerce', 'woocommerce_header_add_to_cart_fragment']);

		// 2.2 lightbox post content
		add_filter('essgrid_lightbox_post_content', [$this, 'on_lightbox_post_content'], 10, 2);
		
		//add support for divi shortcodes in ajax requests
		add_filter('et_builder_load_requests', [$this, 'add_divi_support'], 10, 1);

		//add esg images to yoast xml sitemap
		add_filter('wpseo_sitemap_urlimages', [$this, 'wpseo_sitemap_urlimages'], 10, 2);

		//admin bar edit link
		add_action('wp_footer', [$this, 'add_admin_bar'], 100);
		add_action('wp_before_admin_bar_render', [$this, 'add_admin_menu_nodes']);

		self::$is_inited = true;
	}

	/**
	 * @return int
	 */
	public function get_grid_id() {
		return $this->grid_id;
	}

	/**
	 * @return string
	 */
	public function get_grid_name() {
		return $this->grid_name;
	}

	/**
	 * @return string
	 */
	public function get_grid_handle() {
		return $this->grid_handle;
	}

	/**
	 * @return array
	 */
	public function get_grid_params() {
		return $this->grid_params;
	}

	/**
	 * @return array
	 */
	public function get_grid_postparams() {
		return $this->grid_postparams;
	}

	/**
	 * @return array
	 */
	public function get_grid_layers() {
		return $this->grid_layers;
	}
	
	/**
	 * @param array $layers
	 * @return void
	 */
	public function set_grid_layers($layers) {
		$this->grid_layers = $layers;
	}

	/**
	 * @return array
	 */
	public function get_grid_settings() {
		return $this->grid_settings;
	}

	/**
	 * Return the plugin slug.
	 * @return string  Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return ESG_PLUGIN_SLUG;
	}

	/**
	 * Return an instance of this class.
	 * @return object  A single instance of this class.
	 */
	public static function get_instance() {
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function enqueue_tptools() {
		global $wp_scripts;

		if ( self::$enqueue_tptools ) {
			return;
		}

		$enqueue = true;

		// RS use 'tp-tools' handle in a funky way
		// frontend
		//     'tp-tools' = tptools.js
		// backend
		//     'tp-tools' = rbtools.min.js
		//     '_tpt' = tptools.js

		$handle = 'tp-tools';

		if (
			isset( $wp_scripts->registered['tp-tools']->src )
			&& 'rbtools.min.js' == basename( $wp_scripts->registered['tp-tools']->src )
		) {
			$handle = '_tpt';
		}

		if ( isset( $wp_scripts->registered[ $handle ]->ver ) ) {
			if ( version_compare( $wp_scripts->registered[ $handle ]->ver, ESG_TP_TOOLS, '<' ) ) {
				// dequeue tp-tools to make sure that always the latest is loaded
				wp_deregister_script( $handle );
				wp_dequeue_script( $handle );
			} else {
				// higher version already enqueued
				$enqueue = false;
			}
		}

		if ( $enqueue ) {
			$js_to_footer = Essential_Grid_Base::isJsToFooter();
			wp_enqueue_script( $handle, ESG_PLUGIN_URL . 'public/assets/js/libs/tptools.js', [], ESG_TP_TOOLS, [ 'strategy' => 'async', 'in_footer' => $js_to_footer ] );
		}

		$js = "
		window.ESG ??= {};
		ESG.E ??= {};
		ESG.E.site_url = '" . get_site_url( get_current_blog_id() ) . "';
		ESG.E.plugin_url = '" . str_replace( [ "\n", "\r" ], '', ESG_PLUGIN_URL ) . "';
		ESG.E.ajax_url = '" . admin_url( 'admin-ajax.php' ) . "';
		ESG.E.nonce = '" . wp_create_nonce( 'eg-ajax-nonce' ) . "';
		ESG.E.tptools = " . ( $enqueue ? 'true' : 'false' ) . ";
		ESG.E.waitTptFunc ??= [];
		ESG.F ??= {};
		ESG.F.waitTpt = () => {
			if ( typeof jQuery==='undefined' || !window?._tpt?.regResource || !ESG?.E?.plugin_url || (!ESG.E.tptools && !window?.SR7?.E?.plugin_url) ) return setTimeout(ESG.F.waitTpt, 29);
			if (!window._tpt.gsap) window._tpt.regResource({id: 'tpgsap', url : ESG.E.tptools && ESG.E.plugin_url+'/public/assets/js/libs/tpgsap.js' || SR7.E.plugin_url + 'public/js/libs/tpgsap.js'});
			_tpt.checkResources(['tpgsap']).then(() => { 
				if (window.tpGS && !_tpt?.Back) {
					_tpt.eases = tpGS.eases;
					Object.keys(_tpt.eases).forEach((e) => {_tpt[e] === undefined && (_tpt[e] = tpGS[e])});
				}
				ESG.E.waitTptFunc.forEach((f) => { typeof f === 'function' && f(); }); 
				ESG.E.waitTptFunc = []; 
			});
		}";
		wp_add_inline_script( $handle, Essential_Grid_Base::compress_assets( $js, true ), 'before' );

		self::$enqueue_tptools = true;
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain()
	{
		$locale = apply_filters('plugin_locale', get_locale(), 'essential-grid');
		load_textdomain('essential-grid', trailingslashit(WP_LANG_DIR) . 'essential-grid' . '/' . 'essential-grid' . '-' . $locale . '.mo');
		load_plugin_textdomain('essential-grid', false, dirname( plugin_basename( __FILE__ ), 2 ) . '/languages/');
		do_action('essgrid_load_plugin_textdomain', 'essential-grid');
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 */
	public function enqueue_styles()
	{
		ThemePunch_Fonts::register_icon_fonts();
		
		wp_register_style('esg-plugin-settings', ESG_PLUGIN_URL . 'public/assets/css/settings.css', [], ESG_REVISION);
		wp_register_style('esg-tp-boxextcss', ESG_PLUGIN_URL . 'public/assets/css/jquery.esgbox.min.css', [], ESG_REVISION);

		// Enqueue assets that need to be in HEAD
		ThemePunch_Fonts::enqueue_icon_fonts( "public" );
		wp_enqueue_style('esg-plugin-settings');

		// Enqueue Lightbox Style/Script
		$use_cache =  self::$base::isUseCache();
		if ($use_cache) {
			wp_enqueue_style('esg-tp-boxextcss');
		}

		do_action('essgrid_enqueue_styles', $use_cache, ESG_REVISION);
	}

	/**
	 * Register and enqueues public-facing JavaScript files for SR6 engine
	 */
	public function enqueue_scripts_sr6()
	{
		global $wp_scripts;

		$use_cache = self::$base::isUseCache();
		$js_to_footer = Essential_Grid_Base::isJsToFooter();

		wp_enqueue_script('jquery');

		if (get_option('tp_eg_use_lightbox') !== 'disabled') {
			wp_register_script('esg-tp-boxext', ESG_PLUGIN_URL . 'public/assets/js/sr6/esgbox.min.js', array('jquery'), ESG_REVISION, $js_to_footer);
		}

		/**
		 * dequeue tp-tools to make sure that always the latest is loaded
		 **/
		if (isset($wp_scripts->registered['tp-tools']->ver)) {
			if (version_compare($wp_scripts->registered['tp-tools']->ver, '6.5.14', '<')) {
				wp_deregister_script('tp-tools');
				wp_dequeue_script('tp-tools');
				wp_register_script('tp-tools', ESG_PLUGIN_URL . 'public/assets/js/sr6/rbtools.min.js', array('jquery'), '6.5.14', $js_to_footer);
			}
		} else {
			wp_register_script('tp-tools', ESG_PLUGIN_URL . 'public/assets/js/sr6/rbtools.min.js', array('jquery'), '6.5.14', $js_to_footer);
		}
	
		wp_register_script('esg-essential-grid-script', ESG_PLUGIN_URL . 'public/assets/js/sr6/esg.min.js', array('jquery', 'tp-tools'), ESG_REVISION, $js_to_footer);
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 */
	public function enqueue_scripts() {
		global $esg_dev_mode, $wp_scripts;

		// enqueue SR6 scripts only on frontend for SR6 engine with already registered SR tp-tools 
		// do not include sr6-compatible scripts if there is no SR 
		if ( ! is_admin() && self::$sr_engine == 6 && isset( $wp_scripts->registered['tp-tools']->ver ) ) {
			$this->enqueue_scripts_sr6();
			return;
		}

		$use_cache    = self::$base::isUseCache();
		$js_to_footer = Essential_Grid_Base::isJsToFooter();

		wp_enqueue_script( 'jquery' );
		self::enqueue_tptools();

		if ( $esg_dev_mode ) {
			if ( get_option( 'tp_eg_use_lightbox' ) !== 'disabled' ) {
				wp_register_script( 'esg-tp-boxext', ESG_PLUGIN_URL . 'public/assets/js/dev/esgbox.js', [], ESG_REVISION, [ 'strategy' => 'async', 'in_footer' => $js_to_footer ] );
			}
			wp_register_script( 'esg-essential-grid-script', ESG_PLUGIN_URL . 'public/assets/js/dev/esg.js', [], ESG_REVISION, [ 'strategy' => 'async', 'in_footer' => $js_to_footer ] );
		} else {
			if ( get_option( 'tp_eg_use_lightbox' ) !== 'disabled' ) {
				wp_register_script( 'esg-tp-boxext', ESG_PLUGIN_URL . 'public/assets/js/esgbox.min.js', [], ESG_REVISION, [ 'strategy' => 'async', 'in_footer' => $js_to_footer ] );
			}
			wp_register_script( 'esg-essential-grid-script', ESG_PLUGIN_URL . 'public/assets/js/esg.min.js', [], ESG_REVISION, [ 'strategy' => 'async', 'in_footer' => $js_to_footer ] );
		}

		do_action( 'essgrid_enqueue_scripts', $use_cache, ESG_REVISION, $js_to_footer );
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	public static function replace_token( $content ) {
		if ( empty ( self::$front_token ) ) {
			self::$front_token = wp_create_nonce( 'Essential_Grid_Front' );
		}

		return str_replace( '__Essential_Grid_Front_Token__', self::$front_token, $content );
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public static function output_protection($content)
	{
		$output_protection = get_option('tp_eg_output_protection', 'none');

		// handle output types
		switch ($output_protection) {
			case 'compress':
				$content = str_replace(["\r", "\n", "\r\n"], '', $content);
				break;

			case 'echo':
				// bypass the filters
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $content contain escaped HTML
				echo $content;
				$content = '';
				break;
				
			default:
				// return as is
		}

		return $content;
	}

	/**
	 * Register Shortcode
	 */
	public static function register_shortcode($args, $mid_content = null)
	{
		$args = apply_filters('essgrid_register_shortcode_pre', $args);

		$caching =  self::$base::getUseCache();
		$use_cache = self::$base::isUseCache();

		wp_enqueue_script('tp-tools');
		wp_enqueue_script('esg-essential-grid-script');

		// Enqueue Lightbox Style/Script
		if ($use_cache) {
			wp_enqueue_script('esg-tp-boxext');
		}

		$grid = new Essential_Grid;
		extract(shortcode_atts([
			'alias' => '',
			'settings' => '',
			'layers' => '',
			'images' => '',
			'posts' => '',
			'special' => ''
		], $args, 'ess_grid'));
		$eg_alias = ($alias != '') ? $alias : implode(' ', $args);

		if ($settings !== '')
			$grid->custom_settings = json_decode(str_replace(['({', '})', "'"], ['[', ']', '"'], $settings), true);
		if ($layers !== '')
			$grid->custom_layers = json_decode(str_replace(['({', '})', "'"], ['[', ']', '"'], $layers), true);
		if ($images !== '')
			$grid->custom_images = explode(',', $images);
		if ($posts !== '')
			$grid->custom_posts = explode(',', $posts);
		if ($special !== '')
			$grid->custom_special = $special;

		if ($settings !== '' || $layers !== '' || $images !== '' || $posts !== '' || $special !== '') { 
			//disable caching if one of this is set
			$caching = 'false';
		}

		//check for example on gallery shortcode and do stuff
		$grid->check_for_shortcodes($mid_content); 

		if ($grid->is_gallery)
			$caching = 'false';

		if ($eg_alias == '')
			$eg_alias = implode(' ', $args);

		$content = false;
		$grid_id = Essential_Grid_Db::get_entity('grids')->get_id_by_alias($eg_alias);

		if (!$grid_id) {
			//grid is created by custom settings. Check if layers and settings are set
			ob_start();
			$grid->output_essential_grid_by_settings();
			$content = ob_get_clean();
		} else {
			$lang_code = apply_filters('essgrid_get_lang_code', '');
			
			// filter to control cache per grid
			$use_cache_grid = apply_filters('essgrid_query_caching', $caching == 'true', $grid_id);

			if ($caching == 'true' && $use_cache_grid) {
				//check if we use total caching
				$addition = (wp_is_mobile()) ? '_m' : '';
				$addition .= '_' .  self::$base::detect_device();
				$addition .= ($addition !== '' && $lang_code !== '') ? '_' : '';
				$content = get_transient('ess_grid_trans_full_grid_' . $grid_id . $addition . $lang_code);
			}

			if (!$content) {
				ob_start();
				$grid->output_essential_grid_by_alias($eg_alias);
				$content = ob_get_clean();

				//do not cache grids with random sort
				$order_by_start = $grid->get_param_by_handle('sorting-order-by-start', 'none');
				if ($caching == 'true' && $use_cache_grid && 'rand' != $order_by_start) {
					set_transient('ess_grid_trans_full_grid_' . $grid_id . $addition . $lang_code, $content, 60 * 60 * 24 * 7);
				}
			}

		}

		return self::output_protection( self::replace_token( $content ) );
	}

	/**
	 * Register Shortcode For Ajax Content
	 * @since: 1.5.0
	 */
	public static function register_shortcode_ajax_target($args, $mid_content = null)
	{
		$args = apply_filters('essgrid_register_shortcode_ajax_target_pre', $args);
		extract(shortcode_atts(['alias' => ''], $args, 'ess_grid_ajax_target'));

		//no alias found
		if ($alias == '') return false; 

		$content = '';
		$grid = new Essential_Grid;
		$grid_id = Essential_Grid_Db::get_entity('grids')->get_id_by_alias($alias);
		if (empty($grid_id)) return false;
		
		$grid->init_by_id($grid_id);
		//check if shortcode is allowed
		$is_sc_allowed = $grid->get_param_by_handle('ajax-container-position');
		if ($is_sc_allowed != 'shortcode') return false;
		$content = $grid->output_ajax_container();

		return self::output_protection($content);
	}

	/**
	 * @param string $handle
	 * @param array $filter_selected
	 *
	 * @return array
	 */
	protected static function _restore_meta_values_order($handle, $filter_selected) {
		$meta_data = self::$meta->get_meta_by_handle($handle);
		if (!empty($meta_data['type']) && in_array($meta_data['type'], ['select', 'multi-select'])) {
			//restore original meta values order
			$original_values = explode(',', $meta_data['select']);
			foreach ($original_values as $v) {
				$_v =  self::$base::sanitize_utf8_to_unicode($v);
				if (($key = array_search($_v, $filter_selected)) !== false) {
					unset($filter_selected[$key]);
					$filter_selected[] = $_v;
				}
			}
		}
		
		return $filter_selected;
	}

	/**
	 * @param Essential_Grid $grid
	 * @param int $layer_key
	 * @param array $layer
	 *
	 * @return array
	 */
	protected static function _process_layer_custom_filter($grid, $layer_key, $layer) {
		// check if we have filters in cache
		if (!empty($grid->grid_id) && !empty(self::$layer_filters[$grid->grid_id][$layer_key])) {
			return self::$layer_filters[$grid->grid_id][$layer_key];
		}
		
		$result = [];
		if (empty($layer['custom-filter'])) return $result;
		
		$cats = explode(',', $layer['custom-filter']);

		foreach ($cats as $c) {
			$cur_filter = [];

			//check if it is meta filter
			if (strpos($c, 'meta-') === 0) {
				$handle = 'eg-' . substr($c, 5);
				if (!empty($layer[$handle])) {
					$cur_filter = (is_array($layer[$handle])) ? explode(',', $layer[$handle][0]) : [$layer[$handle]];
				}
			} else {
				$cur_filter = [$c];
			}

			foreach ($cur_filter as $v) {
				$v = trim($v);
				if ($v == '') continue;
				$_v =  self::$base::sanitize_utf8_to_unicode($v);
				$result[$_v] = [
					'name' => $v,
					'slug' => $_v
				];
			}
		}

		if (!empty($grid->grid_id)) {
			// cache filters
			self::$layer_filters[$grid->grid_id][$layer_key] = $result;
		}
		
		return $result;
	}

	/**
	 * @param Essential_Grid $grid
	 * @param array $filter_selected
	 *
	 * @return array[]
	 */
	protected static function _process_custom_grid_filter($grid, $filter_selected) {
		$return = [
			'filter-selected' => [],
			'filter-found' => [],
		];
		if ( empty($grid->grid_layers) || count($grid->grid_layers) < 1) return $return;
		
		// process filters values selected in admin
		foreach ($filter_selected as $filter) {
			$return['filter-selected'][] = self::$base::sanitize_utf8_to_unicode($filter);
			if (strpos($filter, 'meta-') !== 0) continue;

			// filter is meta
			// unset last added slug as we will replace it with meta slug(s)
			array_pop($return['filter-selected']);
			
			$handle = substr($filter, 5);
			foreach ($grid->grid_layers as $layer) {
				if (empty($layer['eg-' . $handle])) continue;

				$filter_meta = (is_array($layer['eg-' . $handle])) ? explode(',', $layer['eg-' . $handle][0]) : [$layer['eg-' . $handle]];
				if (empty($filter_meta)) continue;
				
				foreach ($filter_meta as $v) {
					if (trim($v) == '') continue;
					$_v = self::$base::sanitize_utf8_to_unicode($v);
					if (!in_array($_v, $return['filter-selected'])) $return['filter-selected'][] = $_v;
				}
			}

			$return['filter-selected'] = self::_restore_meta_values_order($handle, $return['filter-selected']);
		}
		
		// process layers
		foreach ($grid->grid_layers as $key => $layer) {
			$return['filter-found'] = $return['filter-found'] + self::_process_layer_custom_filter($grid, $key, $layer);
		}
		
		return $return;
	}

	/**
	 * @param array $result
	 * @param array $taxonomy
	 *
	 * @return array
	 */
	protected static function _process_post_taxonomy($result, $taxonomy) {
		if (empty($taxonomy)) return $result;

		foreach ($taxonomy as $item) {
			$result[$item->taxonomy . '_' . $item->term_id] = [
				'name' => $item->name,
				'slug' =>  self::$base::sanitize_utf8_to_unicode($item->slug),
				'parent' => $item->parent
			];
		}

		return $result;
	}

	/**
	 * fetch filter-selected(-X) from grid params
	 * 
	 * @param Essential_Grid $grid
	 * @param array $post
	 *
	 * @return array
	 */
	protected static function _get_grid_filters_selected($grid) {
		return array_filter(
			$grid->grid_params,
			function($k) {
				return strpos($k, 'filter-selected') !== false;
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * fetch filter meta value from post
	 *
	 * @param array $post
	 * @param string $handle
	 *
	 * @return array|string
	 */
	protected static function _get_filter_meta_from_post($post, $handle) {
		$filter_meta = self::$meta->get_meta_value_by_handle($post['ID'], 'eg-' . $handle, false);
		if (empty($filter_meta)) {
			//check if we are linking
			$filter_meta = self::$meta_link->get_link_meta_value_by_handle($post['ID'], 'egl-' . $handle);
		}
		
		if (!empty($filter_meta)) {
			$meta_decoded = json_decode($filter_meta, true);
			$filter_meta = (is_array($meta_decoded)) ? $meta_decoded : [$filter_meta];
			$filter_meta = array_map('trim', $filter_meta);
		}
		
		return $filter_meta;
	}

	/**
	 * @param Essential_Grid $grid
	 * @param array $post
	 *
	 * @return array
	 */
	protected static function _process_post_filter($grid, $post) {
		// check if we have filters in cache
		if (!empty($grid->grid_id) && !empty(self::$post_filters[$grid->grid_id][$post['ID']])) {
			return self::$post_filters[$grid->grid_id][$post['ID']];
		}
		
		$result = [];

		$default_filter_add = self::$base->getVar($grid->grid_params, 'add-filters-by', 'default');
		if (in_array($default_filter_add, ['default', 'categories'], true)) {
			$result = self::_process_post_taxonomy($result, self::$base->get_custom_taxonomies_by_post_id($post['ID']));
		}
		if (in_array($default_filter_add, ['default', 'tags'], true)) {
			$result = self::_process_post_taxonomy($result, get_the_tags($post['ID']));
		}

		$filters_in_params = self::_get_grid_filters_selected($grid);
		foreach ($filters_in_params as $f_values) {
			if (empty($f_values)) continue;
			
			foreach ($f_values as $handle) {
				if (strpos($handle, 'meta-') !== 0) continue;

				$handle = substr($handle, 5);
				$filter_meta = self::_get_filter_meta_from_post($post, $handle);
				if (empty($filter_meta)) continue;
				
				foreach ($filter_meta as $v) {
					$v = trim($v);
					if ($v == '') continue;
					$_v = self::$base::sanitize_utf8_to_unicode($v);
					$result[$_v] = [
						'name' => $v,
						'slug' => $_v,
						'parent' => '0'
					];
				}
			}
		}

		if (!empty($grid->grid_id)) {
			// cache filters
			self::$post_filters[$grid->grid_id][$post['ID']] = $result;
		}

		return $result;
	}

	/**
	 * @param Essential_Grid $grid
	 * @param array $filter_selected
	 *
	 * @return array[]
	 */
	protected static function _process_post_grid_filter($grid, $filter_selected) {
		$return = [
			'filter-selected' => [],
			'filter-found'    => [],
		];

		$posts = $grid->get_grid_categories_posts();
		if ( empty($posts) || count($posts) < 1 ) return $return;

		// process filters values selected in admin
		foreach ($filter_selected as $filter) {
			if (strpos($filter, 'meta-') !== 0) {
				$return['filter-selected'][] = self::$base::sanitize_utf8_to_unicode($filter);
				continue;
			}

			// filter is ESG meta
			$handle = substr($filter, 5);
			foreach ($posts as $post) {
				$filter_meta = self::_get_filter_meta_from_post($post, $handle);
				if (empty($filter_meta)) continue;
				
				foreach ($filter_meta as $v) {
					if (trim($v) == '') continue;
					$_v =  self::$base::sanitize_utf8_to_unicode($v);
					if (!in_array($_v, $return['filter-selected'])) $return['filter-selected'][] = $_v;
				}
			}

			$return['filter-selected'] = self::_restore_meta_values_order($handle, $return['filter-selected']);
		}
		
		foreach ($posts as $post) {
			if (!$grid->check_if_visible($post['ID'], $grid->grid_id)) continue;
			$return['filter-found'] = $return['filter-found'] + self::_process_post_filter($grid, $post);
		}
		
		// check selected filters for correct wpml terms ID
		// they have format [tax_slug]_[tax_id]
		foreach ($return['filter-selected'] as $k => $v) {
			list($taxonomy, $id) = self::$base::splitString($v);
			if (!$id) continue;
			$return['filter-selected'][$k] = $taxonomy . '_' . apply_filters('essgrid_get_taxonomy_id', $id, $taxonomy);
		}

		return $return;
	}

	/**
	 * @param string $f_id
	 * @param Essential_Grid $grid
	 *
	 * @return array
	 */
	public static function get_grid_filter($f_id, $grid) {
		$filter_data = [];
		$filter_data['filter-found'] = [];
		$filter_data['filter-all-text'] = self::$base->getVar($grid->grid_params, 'filter-all-text' . $f_id, esc_attr__('Filter - All', 'essential-grid'));
		$filter_data['filter-all-visible'] = self::$base->getVar($grid->grid_params, 'filter-all-visible' . $f_id, 'on');
		$filter_data['filter-counter'] = self::$base->getVar($grid->grid_params, 'filter-counter' . $f_id, 'off');
		$filter_data['filter-dropdown-text'] = self::$base->getVar($grid->grid_params, 'filter-dropdown-text' . $f_id, esc_attr__('Filter Categories', 'essential-grid'));
		
		$filter_data['filter-grouping'] = self::$base->getVar($grid->grid_params, 'filter-grouping' . $f_id, 'false');
		$filter_data['filter-listing'] = self::$base->getVar($grid->grid_params, 'filter-listing' . $f_id, 'list');
		$filter_data['filter-sort-alpha'] = self::$base->getVar($grid->grid_params, 'filter-sort-alpha' . $f_id, 'off');
		$filter_data['filter-sort-alpha-dir'] = self::$base->getVar($grid->grid_params, 'filter-sort-alpha-dir' . $f_id, 'asc');
		$filter_data['filter-selected'] = self::$base->getVar($grid->grid_params, 'filter-selected' . $f_id, []);
		
		if ( empty($filter_data['filter-selected']) ) return $filter_data;

		if (!is_array($filter_data['filter-selected'])) $filter_data['filter-selected'] = [$filter_data['filter-selected']];
		
		$data = [
			'filter-selected' => [],
			'filter-found' => [],
		];
		switch (self::$base->getVar($grid->grid_postparams, 'source-type')) {
			case 'custom':
				$filter_data['custom'] = true;
				$data = self::_process_custom_grid_filter($grid, $filter_data['filter-selected']);
				break;
				
			case 'post':
				$data = self::_process_post_grid_filter($grid, $filter_data['filter-selected']);
				break;
				
			default:
		}

		$filter_data['filter-selected'] = $data['filter-selected'];
		$filter_data['filter-found'] = $data['filter-found'];

		return $filter_data;
	}
	
	/**
	 * @param Essential_Grid_Navigation $nav
	 * @param Essential_Grid $grid
	 * @param array $filters_in_params_layout  pass if you want to prepare certain filter(s), i.e. single filter from shortcode
	 *
	 * @return void
	 */
	public static function prepare_filters($nav, $grid, $filters_in_params_layout = []) {

		if (empty($filters_in_params_layout)) $filters_in_params_layout = self::_get_grid_filters_selected($grid);

		$filters_arr = [];
		foreach ($filters_in_params_layout as $fkey => $fdata) {
			$f_id = intval(str_replace('filter-selected-', '', $fkey));
			$f_id = ($f_id == 0) ? '' : '-' . $f_id;
			$f_arr_key = 'filter' . $f_id;
			if (isset($filters_arr[$f_arr_key])) continue;

			$filters_arr[$f_arr_key] = self::get_grid_filter($f_id, $grid);
			
			$nav->set_filter_settings($f_arr_key, $filters_arr[$f_arr_key]);
			$nav->set_filter($filters_arr[$f_arr_key]['filter-found']);

			$nav->set_filter_text($filters_arr[$f_arr_key]['filter-all-text'], $f_id);
			$nav->set_filterall_visible($filters_arr[$f_arr_key]['filter-all-visible'], $f_id);
			$nav->set_dropdown_text($filters_arr[$f_arr_key]['filter-dropdown-text'], $f_id);
			$nav->set_show_count($filters_arr[$f_arr_key]['filter-counter'], $f_id);
		}

		$nav->set_filter_type(self::$base->getVar($grid->grid_params, 'filter-arrows', 'single'));
		$nav->set_filter_start_select(self::$base->getVar($grid->grid_params, 'filter-start'));
	}

	/**
	 * @return array
	 */
	public function get_grid_categories_posts()
	{
		// check if we have posts in cache
		if (!empty($this->grid_id) && !empty(self::$grid_posts[$this->grid_id])) {
			return self::$grid_posts[$this->grid_id];
		}
		
		$start_sortby = self::$base->getVar($this->grid_params, 'sorting-order-by-start', 'none');
		$start_sortby_type = self::$base->getVar($this->grid_params, 'sorting-order-type', 'ASC');
		$max_entries = $this->get_maximum_entries($this);
		
		if ($this->custom_posts !== null) {
			// output by specific set posts
			$posts =  self::$base::get_posts_by_ids($this->custom_posts, $start_sortby, $start_sortby_type);
		} elseif ($this->custom_special !== null) {
			// output by some special rule
			if (!intval($max_entries)) $max_entries = 20;

			switch ($this->custom_special) {
				case 'related':
					$related_by = self::$base->getVar($this->grid_params, 'relatedbased', 'both');
					$posts =  self::$base::get_related_posts($max_entries, $related_by);
					break;

				case 'popular':
					$posts =  self::$base::get_wp_posts($max_entries, "any", "comment_count", "popular");
					break;

				case 'recent':
				case 'latest':
					$posts =  self::$base::get_wp_posts( $max_entries, "any", "date", "latest" );
					break;

				default:
					$posts = apply_filters('essgrid_get_posts_custom_special', [], $this->custom_special, $this);
			}
		} else {
			$post_category = self::$base->getVar($this->grid_postparams, 'post_category');
			$post_types = self::$base->getVar($this->grid_postparams, 'post_types');
			$page_ids = explode(',', self::$base->getVar($this->grid_postparams, 'selected_pages', '-1'));
			$cat_relation = self::$base->getVar($this->grid_postparams, 'category-relation', 'OR');
			$additional_query = self::$base->getVar($this->grid_postparams, 'additional-query');
			if ($additional_query !== '')
				$additional_query = wp_parse_args($additional_query);
			
			// output with the grid settings from an existing grid
			$cat_tax = self::$base::getCatAndTaxData($post_category);
			$posts   = self::$base::getPostsByCategory(
				$this->grid_id,
				$cat_tax['cats'],
				$post_types,
				$cat_tax['tax'],
				$page_ids,
				$start_sortby,
				$start_sortby_type,
				$max_entries,
				$additional_query,
				$cat_relation
			);
		}
		
		if (!empty($this->grid_id)) {
			// cache posts
			self::$grid_posts[$this->grid_id] = $posts;
		}
		
		return $posts;
	}

	/**
	 * Register Shortcode For Filter
	 * @since: 1.5.0
	 */
	public static function register_shortcode_filter($args, $mid_content = null)
	{
		$args = apply_filters('essgrid_register_shortcode_filter_pre', $args);
		extract(shortcode_atts(['alias' => '', 'id' => ''], $args, 'ess_grid_nav'));

		if ($id == 'sort') $id = 'sorting';
		
		//no id / alias found
		if (empty($alias) || empty($id)) return false;

		$grid = new Essential_Grid;
		$grid_id = Essential_Grid_Db::get_entity('grids')->get_id_by_alias($alias);
		if (empty($grid_id) || !$grid->init_by_id($grid_id)) return false;
		
		$layout = $grid->get_param_by_handle('navigation-layout', []);
		// Check if selected element is in external list and also get the key to use it to get class
		if (!isset($layout[$id]['external'])) return false;
		
		$navig_special_class = $grid->get_param_by_handle('navigation-special-class', []); // has all classes in an ordered list
		$navig_special_skin = $grid->get_param_by_handle('navigation-special-skin', []); // has all classes in an ordered list

		$navigation_c = new Essential_Grid_Navigation($grid_id);
		$navigation_c->set_special_class( 'esg-nav-by-shortcode' );
		$navigation_c->set_special_class( self::$base::getVar($navig_special_class, $layout[$id]['external']) );
		$navigation_c->set_special_class( self::$base::getVar($navig_special_skin, $layout[$id]['external']) );
		$navigation_c->set_special_class('esg-fgc-' . $grid_id);

		ob_start();

		$filter = false;
		switch ($id) {
			case 'sorting':
				$order_by_start = $grid->get_param_by_handle('sorting-order-by-start', 'none');
				$order_type = $grid->get_param_by_handle('sorting-order-type', 'ASC');
				$sort_by_text = $grid->get_param_by_handle('sort-by-text', esc_attr__('Sort By ', 'essential-grid'));
				$order_by = explode(',', $grid->get_param_by_handle('sorting-order-by', 'date'));
				if (!is_array($order_by)) $order_by = [$order_by];
				//set filter's order 
				$navigation_c->set_orders_text($sort_by_text);
				$navigation_c->set_orders_start($order_by_start);
				$navigation_c->set_orders_order($order_type);
				$navigation_c->set_orders($order_by);

				$navigation_c->set_special_class( 'esg-filters' );
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
				echo $navigation_c->output_sorting();
				break;
			case 'cart':
				$navigation_c->set_special_class( 'esg-filters' );
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
				echo $navigation_c->output_cart();
				break;
			case 'left':
				$navigation_c->set_special_class( 'esg-navbutton-solo-left' );
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
				echo $navigation_c->output_navigation_left();
				break;
			case 'right':
				$navigation_c->set_special_class( 'esg-navbutton-solo-right' );
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
				echo $navigation_c->output_navigation_right();
				break;
			case 'pagination':
				$navigation_c->set_special_class( 'esg-filters' );
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
				echo $navigation_c->output_pagination();
				break;
			case 'search-input':
				$search_text = $grid->get_param_by_handle('search-text', esc_attr__('Search...', 'essential-grid'));
				$navigation_c->set_search_text($search_text);
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
				echo $navigation_c->output_search_input();
				break;
			case 'filter':
			default:
				if (strpos($id, 'filter') === false) return false;
				
				$filter_postfix = str_replace('filter', '', $id);
				self::prepare_filters($navigation_c, $grid, ['filter-selected' . $filter_postfix => 'filter-selected' . $filter_postfix]);

				$navigation_c->set_special_class( 'esg-filters' );
				$navigation_c->set_special_class( 'esg-' . $navigation_c->get_filter_type() );
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
				echo $navigation_c->output_filter_unwrapped(false, $filter_postfix);
		}

		$content = ob_get_clean();

		return self::output_protection( '<div class="' . $navigation_c->get_special_class() . '">' . $content . '</div>' );
	}

	/**
	 * We check the content for gallery shortcode.
	 * If existing, create Grid based on the images
	 * @since: 1.2.0
	 * @moved: 1.5.4: moved to Essential_Grid_Base->get_all_gallery_images($mid_content);
	 **/
	public function check_for_shortcodes($mid_content)
	{
		$mid_content = apply_filters('essgrid_check_for_shortcodes', $mid_content);
		$img = self::$base->get_all_gallery_images($mid_content);
		$this->custom_images = (empty($img)) ? null : $img;
		$this->is_gallery = !empty($img);
	}

	public static function fix_shortcodes($content)
	{
		$content = apply_filters('essgrid_fix_shortcodes_pre', $content);
		$columns = ["ess_grid"];
		$block = join("|", $columns);

		// opening tag
		$rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content);

		// closing tag
		$rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)/", "[/$2]", $rep);

		return apply_filters('essgrid_fix_shortcodes_post', $rep);
	}

	/**
	 * Register Custom Sidebars, created in Grids
	 * @since 1.0.6
	 */
	public static function register_custom_sidebars()
	{
		// Register custom Sidebars
		$sidebars = apply_filters('essgrid_register_custom_sidebars', get_option('esg-widget-areas', false));

		if (is_array($sidebars) && !empty($sidebars)) {
			foreach ($sidebars as $handle => $name) {
				register_sidebar([
					'name' => $name,
					'id' => 'eg-' . $handle,
					'before_widget' => '',
					'after_widget' => ''
				]);
			}
		}
	}

	/**
	 * Register the Custom Widget for Essential Grid
	 **/
	public static function register_custom_widget()
	{
		register_widget('Essential_Grids_Widget');
	}

	/**
	 * Get Certain Parameter
	 * @since: 1.5.0
	 */
	public function get_param_by_handle($handle, $default = '')
	{
		return self::$base->getVar($this->grid_params, $handle, $default);
	}

	/**
	 * Get Certain Post Parameter
	 * @since: 1.5.0
	 */
	public function get_postparam_by_handle($handle, $default = '')
	{
		return self::$base->getVar($this->grid_postparams, $handle, $default);
	}

	/**
	 * Output Essential Grid in Page by alias
	 */
	public function output_essential_grid_by_alias($alias)
	{
		$id = Essential_Grid_Db::get_entity('grids')->get_id_by_alias($alias);
		if (!empty($id)) {
			$this->output_essential_grid($id);
		}
		
		return false;
	}

	/**
	 * Output Essential Grid in Page by Custom Settings and Layers
	 * @since: 1.2.0
	 */
	public function output_essential_grid_by_settings()
	{
		do_action('essgrid_output_essential_grid_by_settings', $this);

		// set correct names for javascript and div id
		$this->set_api_names();
		
		if ($this->custom_special !== null) {
			if ($this->custom_settings !== null) //custom settings got added. Overwrite Grid Settings and element settings
				$this->apply_custom_settings(true);
			$this->apply_all_media_types();
			$this->output_by_posts();
		} else {
			if ($this->custom_settings == null || $this->custom_layers == null) {
				return false;
			} else {
				$this->output_essential_grid_custom();
			}
		}
	}

	

	/**
	 * get all post values / layer values at custom grid
	 * @since: 2.1.0
	 */
	public function get_layer_values()
	{
		return apply_filters('essgrid_get_layer_values', $this->grid_layers);
	}

	/**
	 * Init essential data by id
	 */
	public function init_by_id($grid_id)
	{
		$grid_id = apply_filters('essgrid_init_by_id_pre', $grid_id);
		$grid = Essential_Grid_Db::get_entity('grids')->get_grid_by_id( $grid_id );
		if (empty($grid)) return false;

		$this->init_by_data($grid);

		do_action('essgrid_init_by_id_post', $this, $grid);

		return true;
	}

	/**
	 * Init essential data by given data
	 */
	public function init_by_data($grid_data)
	{
		$grid_data = apply_filters('essgrid_init_by_data', $grid_data);

		$this->grid_id = (isset($grid_data['id'])) ? $grid_data['id'] : '';
		$this->grid_name = (isset($grid_data['name'])) ? $grid_data['name'] : '';
		$this->grid_handle = (isset($grid_data['handle'])) ? $grid_data['handle'] : '';
		$this->grid_postparams = (isset($grid_data['postparams'])) ? $grid_data['postparams'] : [];
		$this->grid_params = (isset($grid_data['params'])) ? $grid_data['params'] : [];
		$this->grid_settings = (isset($grid_data['settings'])) ? $grid_data['settings'] : [];
		$this->grid_layers = (isset($grid_data['layers'])) ? $grid_data['layers'] : [];

		if (!empty($this->grid_layers)) {
			foreach ($this->grid_layers as $k => $layer) {
				$v = json_decode($layer, true);
				$this->grid_layers[$k] = (json_last_error() === JSON_ERROR_NONE && !empty($v)) ? $v : [];
			}
		}

		return true;
	}

	/**
	 * Init essential data by id
	 */
	public function set_loading_ids($ids)
	{
		$this->filter_by_ids = apply_filters('essgrid_set_loading_ids', $ids);
	}

	/**
	 * Check if Grid is a Post
	 */
	public function is_custom_grid()
	{
		return apply_filters('essgrid_is_custom_grid', (self::$base->getVar($this->grid_postparams, 'source-type') == 'custom'), $this);
	}

	/**
	 * Check if Grid is a Stream
	 * @return bool
	 */
	public function is_stream_grid()
	{
		return apply_filters('essgrid_is_stream_grid', false, $this);
	}

	/**
	 * Output Essential Grid in Page
	 */
	public function output_essential_grid($grid_id, $data = [], $grid_preview = false, $by_id = false)
	{
		try {
			do_action('essgrid_output_essential_grid', $grid_id, $data, $grid_preview, $by_id);
			if ($grid_preview) {
				$data['id'] = $grid_id;
				$init = $by_id ? $this->init_by_id($grid_id) : $this->init_by_data($data);
				if (!$init) return false;
			} else {
				$init = $this->init_by_id($grid_id);
				if (!$init) return false;
				Essential_Grid_Global_Css::output_global_css_styles_wrapped();
			}

			if (!self::$base::isValid()) {
				$pg =  self::$base::getVar($this->grid_params, 'pg', 'false');
				if ($pg != 'false') throw new Exception(__('Please register the Essential Grid plugin to use premium templates.', 'essential-grid'));
			}

			do_action('essgrid_output_essential_grid_after_init', $this);

			//custom_special is always posts related ( popular, recent etc... ), so we change to post
			if ($this->custom_special !== null) 
				$this->grid_postparams['source-type'] = 'post';

			//custom post IDs are added, so we change to post
			if ($this->custom_posts !== null) 
				$this->grid_postparams['source-type'] = 'post';

			//custom images are added, so we change to gallery
			if ($this->custom_images !== null) 
				$this->grid_postparams['source-type'] = 'gallery';

			//custom settings got added. Overwrite Grid Settings and element settings
			if ($this->custom_settings !== null) 
				$this->apply_custom_settings();

			//custom layers got added. Overwrite Grid Layers
			if ($this->custom_layers !== null) {
				$this->apply_custom_layers();
				$this->grid_postparams['source-type'] = 'custom';
			}

			// set correct names for javascript and div id
			$this->set_api_names();
			
			$source_type = apply_filters('essgrid_output_essential_grid_get_source_type', $this->grid_postparams['source-type'], $this);
			switch ($source_type) {
				case 'post':
				case 'woocommerce':
					$this->output_by_posts($grid_preview);
					break;
				case 'custom':
					$this->output_by_custom($grid_preview);
					break;
				case 'gallery':
					$this->output_by_gallery($grid_preview);
					break;
				case 'stream':
					$this->output_by_stream($grid_preview);
					break;
				default:
					$this->display_grid_error_msg(false, true);
			}

		} catch (Exception $e) {
			$message = $e->getMessage();
			echo esc_html($message);
		}
	}

	/**
	 * set correct names for javascript and div id
	 * @since: 1.5.0
	 */
	public function set_api_names()
	{
		$ess_api = '';
		$ess_div = '';
		if ($this->grid_id != null) {
			$ess_api = $this->grid_id;
			$ess_div = $this->grid_id;
		}

		if ($this->custom_special !== null) {
			switch ($this->custom_special) {
				case 'related':
				case 'popular':
				case 'recent':
				case 'latest':
					$ess_api .= '_' . $this->custom_special;
					$ess_div .= '-' . $this->custom_special;
					break;
			}
		}
		if ($this->custom_posts !== null) {
			$ess_api .= '_custom_post';
			$ess_div .= '-custom_post';
		}
		if ($this->custom_settings !== null) {
			$ess_api .= '_custom';
			$ess_div .= '-custom';
		}
		if ($this->custom_layers !== null) {
			$ess_api .= '_layers';
			$ess_div .= '-layers';
		}
		if ($this->custom_images !== null) {
			$ess_api .= '_img';
			$ess_div .= '-img';
		}

		$this->grid_api_name = $ess_api;
		$this->grid_div_name = $ess_div;

		do_action('essgrid_set_api_names', $this);
	}

	/**
	 * Output Essential Grid in Page with Custom Layer and Settings
	 * @since: 1.2.0
	 */
	public function output_essential_grid_custom($grid_preview = false)
	{
		try {
			do_action('essgrid_output_essential_grid_custom', $this, $grid_preview);

			Essential_Grid_Global_Css::output_global_css_styles_wrapped();
			
			if ($this->custom_settings !== null) //custom settings got added. Overwrite Grid Settings and element settings
				$this->apply_custom_settings(true);

			if ($this->custom_layers !== null) //custom settings got added. Overwrite Grid Settings and element settings
				$this->apply_custom_layers();

			$this->apply_all_media_types();

			return $this->output_by_custom($grid_preview);

		} catch (Exception $e) {
			$message = $e->getMessage();
			echo esc_html($message);
		}
	}

	/**
	 * Apply all media types for custom grids that have not many settings
	 * @since: 1.2.0
	 */
	public function apply_all_media_types()
	{
		/**
		 * Add settings that need to be set
		 * - use all media sources, sorting does not matter since we only set one thing in each entry
		 * - use all poster sources for videos, sorting does not matter since we only set one thing in each entry
		 * - use all lightbox sources, sorting does not matter since we only set one thing in each entry
		 */
		$media_orders =  self::$base::get_media_source_order();
		foreach ($media_orders as $handle => $vals) {
			if ($handle == 'featured-image' || $handle == 'alternate-image')
				continue;
			$this->grid_postparams['media-source-order'][] = $handle;
		}
		$this->grid_postparams['media-source-order'][] = 'featured-image'; //set this as the last entry
		$this->grid_postparams['media-source-order'][] = 'alternate-image'; //set this as the last entry

		$poster_orders =  self::$base::get_poster_source_order();
		if (!empty($poster_orders)) {
			foreach ($poster_orders as $handle => $vals) {
				$this->grid_params['poster-source-order'][] = $handle;
			}
		}

		$lb_orders =  self::$base::get_lb_source_order();
		foreach ($lb_orders as $handle => $vals) {
			$this->grid_params['lb-source-order'][] = $handle;
		}

		$lb_buttons =  self::$base::get_lb_button_order();
		foreach ($lb_buttons as $handle => $vals) {
			$this->grid_params['lb-button-order'][] = $handle;
		}

		do_action('essgrid_apply_all_media_types', $this);
	}

	/**
	 * Apply Custom Settings to the Grid, so users can change everything in the settings they want to
	 * This allows to modify grid_params and grid_post_params
	 * @since: 1.2.0
	 */
	private function apply_custom_settings($has_handle = false)
	{
		if (empty($this->custom_settings) || !is_array($this->custom_settings))
			return false;

		$translate_variables = [
			'grid-layout' => 'layout'
		];

		foreach ($this->custom_settings as $handle => $new_setting) {
			if (isset($translate_variables[$handle])) {
				$handle = $translate_variables[$handle];
			}
			if ($has_handle) { //p- is in front of postparameters
				if (strpos($handle, 'p-') === 0)
					$this->grid_postparams[substr($handle, 2)] = $new_setting;
				else
					$this->grid_params[$handle] = $new_setting;
			} else {
				if (isset($this->grid_params[$handle])) {
					$this->grid_params[$handle] = $new_setting;
				} elseif (isset($this->grid_postparams[$handle])) {
					$this->grid_postparams[$handle] = $new_setting;
				} else {
					$this->grid_params[$handle] = $new_setting;
				}
			}
		}

		if (isset($this->grid_params['columns'])) { //change columns
			$columns = self::$base->set_basic_colums_custom($this->grid_params['columns']);
			$this->grid_params['columns'] = $columns;
		}

		if (isset($this->grid_params['rows-unlimited']) && $this->grid_params['rows-unlimited'] == 'off') { //add pagination
			$this->grid_params['navigation-layout']['pagination']['bottom-1'] = '0';
			$this->grid_params['bottom-1-margin-top'] = '10';
		}

		do_action('essgrid_apply_custom_settings', $this);

		return true;
	}

	/**
	 * Apply Custom Layers to the Grid
	 * @since: 1.2.0
	 */
	private function apply_custom_layers()
	{
		$this->grid_layers = [];
		if (!empty($this->custom_layers) && is_array($this->custom_layers)) {
			$add_poster_img = [];
			foreach ($this->custom_layers as $handle => $val_arr) {
				if (!empty($val_arr) && is_array($val_arr)) {
					//$custom_poster = false;
					foreach ($val_arr as $id => $value) {
						//if ($handle == 'custom-poster') $custom_poster = array($id, $value);
						if ($handle == 'custom-poster') {
							$add_poster_img[$id] = $value;
							continue;
						}
						$this->grid_layers[$id][$handle] = $value;
					}
				}
			}

			if (!empty($add_poster_img)) {
				foreach ($add_poster_img as $id => $value) {
					$this->grid_layers[$id]['custom-image'] = $value;
				}
			}
		}

		do_action('essgrid_apply_custom_layers', $this);
	}

	/**
	 * Output by Specific Stream
	 * @since: 2.1.0
	 */
	public function output_by_specific_stream()
	{
		ob_start();
		$this->output_by_stream(false, true, $this->filter_by_ids);
		$stream_html = ob_get_clean();

		return apply_filters('essgrid_output_by_specific_stream', $stream_html, $this);
	}

	/**
	 * Output by Stream
	 * @since: 2.1.0
	 */
	public function output_by_stream($grid_preview = false, $only_elements = false, $specific_ids = [])
	{
		do_action('essgrid_output_by_stream_pre', $grid_preview, $only_elements, $specific_ids);

		$this->grid_layers = [];

		$stream_found = apply_filters('essgrid_output_by_stream_get_grid_layers', false, $this);
		if (!$stream_found || empty($this->grid_layers)) {
			$this->display_grid_error_msg(true, false, apply_filters('essgrid_display_grid_error_msg', '', $this));
			return;
		}
		
		$order_by_start = self::$base->getVar($this->grid_params, 'sorting-order-by-start', 'none');
		$order_by_dir = self::$base->getVar($this->grid_params, 'sorting-order-type', 'ASC');
		$this->order_by_custom($order_by_start, $order_by_dir);

		if (!empty($specific_ids)) { 
			// remove all that we do not have in this array
			foreach ($this->grid_layers as $key => $layer) {
				if (!in_array($key, $specific_ids))
					unset($this->grid_layers[$key]);
			}
		}

		// limit layers in preview to default level
		if ($grid_preview === 'preview') {
			$stream_limit = apply_filters('essgrid_output_by_stream_preview_limit', 60);
			if (count($this->grid_layers) > $stream_limit) {
				$this->grid_layers = array_slice($this->grid_layers, 0, $stream_limit);
				echo '<div class="preview-notice esg-margin-b-15">' . 
					sprintf(
						/* translators: %s: stream items count limit */
						esc_html__( 'Stream items in preview mode is limited to %s items!', 'essential-grid' ),
						esc_html($stream_limit)
					) . '</div>';
			}
		}

		do_action('essgrid_output_by_stream_post', $this, $grid_preview, $only_elements);

		$do_load_more = !empty($specific_ids);
		return $this->output_by_custom($grid_preview, $only_elements, $do_load_more);
	}

	public function find_biggest_photo($image_urls, $wanted_size, $avail_sizes)
	{
		if (!empty($image_urls[$wanted_size])) return $image_urls[$wanted_size];
		
		$wanted_size_pos = array_search($wanted_size, $avail_sizes);
		for ($i = $wanted_size_pos; $i < 7; $i++) {
			if (!empty($image_urls[$avail_sizes[$i]])) return $image_urls[$avail_sizes[$i]];
		}
		for ($i = $wanted_size_pos; $i >= 0; $i--) {
			if (!empty($image_urls[$avail_sizes[$i]])) return $image_urls[$avail_sizes[$i]];
		}
	}

	/**
	 * add meta at the end for meta sorting
	 * if essential Meta, replace to meta name. Else -> replace - and _ with space, set each word uppercase
	 *
	 * @param string $order_by_start
	 *
	 * @return string
	 */
	protected function _check_meta_sort($order_by_start)
	{
		if (strpos($order_by_start, 'eg-') === 0 || strpos($order_by_start, 'egl-') === 0) { 
			$metas = self::$meta->get_all_meta();
			$f = false;
			if (!empty($metas)) {
				foreach ($metas as $meta) {
					if ('eg-' . $meta['handle'] == $order_by_start || 'egl-' . $meta['handle'] == $order_by_start) {
						$f = true;
						$order_by_start = $meta['name'];
						break;
					}
				}
			}

			if ($f === false) {
				$order_by_start = ucwords(str_replace(['-', '_'], ' ', $order_by_start));
			}
		}
		
		return $order_by_start;
	}
	
	protected function _get_nav_styles()
	{
		$top_1_align     = self::$base->getVar( $this->grid_params, 'top-1-align', 'center' );
		$top_2_align     = self::$base->getVar( $this->grid_params, 'top-2-align', 'center' );
		$top_1_margin    = self::$base->getVar( $this->grid_params, 'top-1-margin-bottom', 0, 'i' );
		$top_2_margin    = self::$base->getVar( $this->grid_params, 'top-2-margin-bottom', 0, 'i' );
		$bottom_1_align  = self::$base->getVar( $this->grid_params, 'bottom-1-align', 'center' );
		$bottom_2_align  = self::$base->getVar( $this->grid_params, 'bottom-2-align', 'center' );
		$bottom_1_margin = self::$base->getVar( $this->grid_params, 'bottom-1-margin-top', 0, 'i' );
		$bottom_2_margin = self::$base->getVar( $this->grid_params, 'bottom-2-margin-top', 0, 'i' );
		$left_margin     = self::$base->getVar( $this->grid_params, 'left-margin-left', 0, 'i' );
		$right_margin    = self::$base->getVar($this->grid_params, 'right-margin-right', 0, 'i');

		$nav_styles = [];
		$nav_styles['top-1'] = [
			'margin-bottom' => $top_1_margin . 'px',
			'text-align' => $top_1_align
		];
		$nav_styles['top-2'] = [
			'margin-bottom' => $top_2_margin . 'px',
			'text-align' => $top_2_align
		];
		$nav_styles['left'] = [
			'margin-left' => $left_margin . 'px'
		];
		$nav_styles['right'] = [
			'margin-right' => $right_margin . 'px'
		];
		$nav_styles['bottom-1'] = [
			'margin-top' => $bottom_1_margin . 'px',
			'text-align' => $bottom_1_align
		];
		$nav_styles['bottom-2'] = [
			'margin-top' => $bottom_2_margin . 'px',
			'text-align' => $bottom_2_align
		];
		
		return $nav_styles;
	}

	/**
	 * @param array $order_by
	 * @param string $order_by_start
	 * @param string $order_by_dir
	 *
	 * @return Essential_Grid_Navigation
	 */
	protected function _init_nav($order_by, $order_by_start, $order_by_dir)
	{
		$nav = new Essential_Grid_Navigation($this->grid_id);
		
		$nav->set_special_class('esg-fgc-' . $this->grid_id);
		$nav->set_specific_styles($this->_get_nav_styles());
		$nav->set_search_text(self::$base->getVar($this->grid_params, 'search-text', esc_attr__('Search...', 'essential-grid')));
		$nav->set_layout(self::$base->getVar($this->grid_params, 'navigation-layout', [])); //set the layout

		$nav->set_orders($order_by);
		$nav->set_orders_text(self::$base->getVar($this->grid_params, 'sort-by-text', esc_attr__('Sort By ', 'essential-grid')));
		$nav->set_orders_start($order_by_start);
		$nav->set_orders_order($order_by_dir);
		
		return $nav;
	}

	/**
	 * @return Essential_Grid_Item_Skin
	 */
	protected function _init_item_skin()
	{
		$item_skin = new Essential_Grid_Item_Skin();
		$item_skin->grid_id = $this->grid_id;
		$item_skin->grid_params = $this->grid_params;
		$item_skin->set_grid_type(self::$base->getVar($this->grid_params, 'layout', 'even'));

		$item_skin->set_default_image_by_id(self::$base->getVar($this->grid_postparams, 'default-image', 0, 'i'));
		$item_skin->set_default_youtube_image_by_id(self::$base->getVar($this->grid_postparams, 'youtube-default-image', 0, 'i'));
		$item_skin->set_default_vimeo_image_by_id(self::$base->getVar($this->grid_postparams, 'vimeo-default-image', 0, 'i'));
		$item_skin->set_default_html_image_by_id(self::$base->getVar($this->grid_postparams, 'html-default-image', 0, 'i'));

		$item_skin->set_grid_item_animation(self::$base, $this->grid_params);

		$item_skin->init_by_id(self::$base->getVar($this->grid_params, 'entry-skin', 0, 'i'));
		$this->grid_params['entry-skin-handle'] = $item_skin->get_handle();

		$lazy_load = self::$base->getVar($this->grid_params, 'lazy-loading', 'off');
		if ($lazy_load == 'on') {
			$item_skin->set_lazy_load(true);
			$lazy_load_blur = self::$base->getVar($this->grid_params, 'lazy-loading-blur', 'on');
			if ($lazy_load_blur == 'on')
				$item_skin->set_lazy_load_blur(true);
		}

		$layout = self::$base->getVar($this->grid_params, 'layout', 'even');
		$layout_sizing = self::$base->getVar($this->grid_params, 'layout-sizing', 'boxed');
		if ($layout_sizing !== 'fullwidth' && $layout == 'masonry') {
			$item_skin->set_poster_cropping(true);
		}

		$post_media_source_type = $this->_get_image_source_type();
		$item_skin->set_media_sources_type($post_media_source_type);

		//2.3.7 YouTube Playlist overview gets featured images media, not videos
		$default_media_source_order = self::$base->getVar($this->grid_postparams, 'media-source-order', []);
		if (isset($default_media_source_order[0]) && $default_media_source_order[0] == 'youtube' && self::$base->getVar($this->grid_postparams, 'youtube-type-source') == 'playlist_overview') {
			$default_media_source_order[0] = 'featured-image';
		}
		$item_skin->set_default_media_source_order($default_media_source_order);

		$default_lightbox_source_order = self::$base->getVar($this->grid_params, 'lb-source-order', []);
		$item_skin->set_default_lightbox_source_order($default_lightbox_source_order);

		$default_aj_source_order = self::$base->getVar($this->grid_params, 'aj-source-order', []);
		$item_skin->set_default_ajax_source_order($default_aj_source_order);

		$default_video_poster_order = self::$base->getVar($this->grid_params, 'poster-source-order', []);
		if (empty($default_video_poster_order)) {
			$default_video_poster_order = self::$base->getVar($this->grid_postparams, 'poster-source-order', []);
		}
		$item_skin->set_default_video_poster_order($default_video_poster_order);
		
		return $item_skin;
	}

	/**
	 * output grid with css / wrappers
	 * 
	 * @param bool|string $grid_preview
	 * @param Essential_Grid_Navigation $nav
	 * @param Essential_Grid_Item_Skin $item_skin
	 * @param string $skins_css
	 * @param string $skins_html
	 * @param bool $only_elements
	 *
	 * @return void
	 */
	protected function _output_grid($grid_preview, $nav, $item_skin, $skins_css, $skins_html, $only_elements)
	{
		if ($only_elements) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $skins_html contain escaped HTML
			echo $skins_html;
			return;
		}
		
		$ajax_container_position = self::$base->getVar( $this->grid_params, 'ajax-container-position', 'top' );
		$module_spacings = self::$base->getVar( $this->grid_params, 'module-spacings', '5' );
		$load_lightbox = $item_skin->do_lightbox_loading();

		ob_start();
		$item_skin->generate_element_css($grid_preview);
		$skins_css .= ob_get_clean();

		if ( $load_lightbox ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
			echo $item_skin->output_lighbox_css( $this->grid_div_name, $this->grid_params );
		}

		$navigation_skin = self::$base->getVar( $this->grid_params, 'navigation-skin', 'minimal-light' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
		echo $nav->output_navigation_skin( $navigation_skin );

		$found_skin = [ $navigation_skin => true ];
		$navigation_special_skin = self::$base->getVar( $this->grid_params, 'navigation-special-skin', [] );
		if ( ! empty( $navigation_special_skin ) ) {
			foreach ( $navigation_special_skin as $spec_skin ) {
				if ( ! isset( $found_skin[ $spec_skin ] ) ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
					echo $nav->output_navigation_skin( $spec_skin );
					$found_skin[ $spec_skin ] = true;
				}
			}
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $skins_css contain escaped HTML
		echo $skins_css;

		if ( $item_skin->ajax_loading && $ajax_container_position == 'top' ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
			echo $this->output_ajax_container();
		}

		$this->output_wrapper_pre( $grid_preview );

		$nav->output_layout( 'top-1', $module_spacings );
		$nav->output_layout( 'top-2', $module_spacings );

		$this->output_grid_pre();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $skins_html contain escaped HTML
		echo $skins_html;

		$this->output_grid_post();

		$nav->output_layout( 'bottom-1', $module_spacings );
		$nav->output_layout( 'bottom-2', $module_spacings );
		$nav->output_layout( 'left' );
		$nav->output_layout( 'right' );
		//check if search was added. If yes, we also need to add the "Filter All" filter if not existing
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
		echo $nav->check_for_search();

		$this->output_wrapper_post();

		if ( $item_skin->ajax_loading && $ajax_container_position == 'bottom' ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
			echo $this->output_ajax_container();
		}

		if ( $grid_preview === false ) {
			$this->output_grid_javascript( $load_lightbox );
		} elseif ($grid_preview !== 'preview' && $grid_preview !== 'custom') {
			$this->output_grid_javascript( $load_lightbox, true );
		}
	}

	/**
	 * Output by gallery
	 * Remove all custom elements, add image elements
	 * @since: 1.2.0
	 */
	public function output_by_gallery($grid_preview = false, $only_elements = false, $from_ajax = false)
	{
		$this->grid_layers = [];

		if (!empty($this->custom_images)) {
			foreach ($this->custom_images as $image_id) {
				
				$attachment = get_post($image_id);
				if (!is_object($attachment)) continue;
				
				$this->grid_layers[$image_id] = [
					'custom-image' => $image_id,
					'excerpt' => $attachment->post_excerpt,
					'caption' => $attachment->post_excerpt,
					'title' => $attachment->post_title,
					'content' => $attachment->post_content,
					'description' => $attachment->post_content
				];
			}
		}

		do_action('essgrid_output_by_gallery', $this, $grid_preview, $only_elements);

		return $this->output_by_custom($grid_preview, $only_elements, false, $from_ajax);
	}

	/**
	 * @return string
	 */
	protected function _get_image_source_type() {
		$post_media_source_type = self::$base->getVar($this->grid_postparams, 'image-source-type', 'full');
		if (wp_is_mobile()) {
			$post_media_source_type = self::$base->getVar($this->grid_postparams, 'image-source-type-mobile', $post_media_source_type);
		}
		
		return $post_media_source_type;
	}

	/**
	 * Output by custom grid
	 */
	public function output_by_custom($grid_preview = false, $only_elements = false, $set_load_more = false, $from_ajax = false)
	{
		$post_limit = 99999;

		do_action('essgrid_output_by_custom_pre', $this, $grid_preview, $only_elements, $set_load_more, $from_ajax);

		$order_by = explode(',', self::$base->getVar($this->grid_params, 'sorting-order-by', 'date'));
		if (!is_array($order_by)) $order_by = [$order_by];
		$order_by_start = $this->_check_meta_sort(self::$base->getVar($this->grid_params, 'sorting-order-by-start', 'none'));
		$order_by_dir = self::$base->getVar($this->grid_params, 'sorting-order-type', 'ASC');
		$this->order_by_custom($order_by_start, $order_by_dir);
		do_action('essgrid_output_by_custom_order_by', $this);

		$navigation_c = $this->_init_nav($order_by, $order_by_start, $order_by_dir);
		
		self::prepare_filters($navigation_c, $this);
		
		$item_skin = $this->_init_item_skin();
		if ($set_load_more) $item_skin->set_load_more();

		$rows_unlimited = self::$base->getVar($this->grid_params, 'rows-unlimited', 'on');
		$load_more = self::$base->getVar($this->grid_params, 'load-more', 'none');
		$load_more_start = self::$base->getVar($this->grid_params, 'load-more-start', 3, 'i');

		if ($rows_unlimited == 'on' && $load_more !== 'none' && !$grid_preview) { 
			// grid_preview means disable load more in preview
			$post_limit = $load_more_start;
		}

		$post_media_source_type = $this->_get_image_source_type();

		$skins_html = '';
		$skins_css = '';
		$i = 1;

		if (!empty($this->grid_layers) && count($this->grid_layers) > 0) {

			$image_sizes = false;
			$image_source_smart = self::$base->getVar($this->grid_postparams, 'image-source-smart', 'off');
			if ('on' === $image_source_smart) {
				$image_sizes = self::$base->getVar($this->grid_postparams, 'image-source-smart-size', false);
			}
			
			foreach ($this->grid_layers as $key => $entry) {
				if (!is_array($entry)) continue;
				
				$post_media_source_data = self::$base->get_custom_media_source_data($entry, $post_media_source_type, $image_sizes);
				$post_video_ratios = self::$meta->get_custom_video_ratios($entry);

				if (is_array($order_by) && !empty($order_by)) {
					$sort = $this->prepare_sorting_array_by_custom($entry, $order_by);
					$item_skin->set_sorting($sort);
				}

				// switch to different skin
				$use_item_skin_id = self::$base->getVar($entry, 'use-skin', '-1');
				if (intval($use_item_skin_id) === 0) {
					$use_item_skin_id = -1;
				}
				$item_skin->switch_item_skin($use_item_skin_id);
				$item_skin->register_layer_css();
				$item_skin->register_skin_css();

				$filters = self::_process_layer_custom_filter($this, $key, $entry);

				// skip if we load only elements from load more ajax request
				if ($only_elements !== true && $set_load_more !== true) {
					if ( $i > $post_limit ) {
						// Load only selected numbers of items at start (for load more)
						// set for load more, only on elements that will not be loaded from beginning
						$this->load_more_post_array[ $key ] = $filters;
						continue;
					}
				}
				
				$i++;

				$item_skin->set_filter($filters);
				$item_skin->set_media_sources($post_media_source_data);
				$item_skin->set_video_ratios($post_video_ratios);
				$item_skin->set_layer_values($entry);
				$item_skin->set_post_values($entry);
				$item_skin->set_item_counter($key);

				if (isset($entry['title'])) $item_skin->set_item_title($entry['title']);

				ob_start();
				$item_skin->output_item_skin($grid_preview);
				$skins_html .= ob_get_clean();

				// 2.2.6
				$id = (isset($entry['post_id'])) ? $entry['post_id'] : '';
				if (!empty($id)) {
					ob_start();
					$item_skin->output_element_css_by_meta($id);
					$skins_css .= ob_get_clean();
				}
			}
		} else {
			$skins_html .= apply_filters('essgrid_output_by_custom_no_items', '', $this);
		}

		if ($grid_preview !== false && !$only_elements) { 
			// add the add more box at the end
			ob_start();
			$item_skin->output_add_more();
			$skins_html .= ob_get_clean();
		}

		// return pure items html for ajax load
		if ($from_ajax) return $skins_html;

		$this->_output_grid($grid_preview, $navigation_c, $item_skin, $skins_css, $skins_html, $only_elements);
		
		do_action('essgrid_output_by_custom_post', $this, $grid_preview, $only_elements, $set_load_more, $from_ajax);
	}

	/**
	 * Output by posts
	 */
	public function output_by_posts($grid_preview = false, $only_elements = false, $set_load_more = false, $from_ajax = false)
	{
		do_action('essgrid_output_by_posts_pre', $this, $grid_preview, $only_elements, $set_load_more, $from_ajax);

		$post_limit = 99999;

		$order_by = explode(',', self::$base->getVar($this->grid_params, 'sorting-order-by', 'date'));
		if (!is_array($order_by)) $order_by = [$order_by];
		$order_by_start = $this->_check_meta_sort(self::$base->getVar($this->grid_params, 'sorting-order-by-start', 'none'));
		$order_by_dir = self::$base->getVar($this->grid_params, 'sorting-order-type', 'ASC');

		$navigation_c = $this->_init_nav($order_by, $order_by_start, $order_by_dir);

		self::prepare_filters($navigation_c, $this);
		
		$item_skin = $this->_init_item_skin();
		if ($set_load_more) $item_skin->set_load_more();

		$rows_unlimited = self::$base->getVar($this->grid_params, 'rows-unlimited', 'on');
		$load_more = self::$base->getVar($this->grid_params, 'load-more', 'none');
		$load_more_start = self::$base->getVar($this->grid_params, 'load-more-start', 3, 'i');

		// grid_preview means disable load more in preview
		if ($rows_unlimited == 'on' && $load_more !== 'none' && !$grid_preview) { 
			$post_limit = $load_more_start;
		}

		$lightbox_mode = self::$base->getVar($this->grid_params, 'lightbox-mode', 'single');
		$lightbox_exclude_original_media = self::$base->getVar($this->grid_params, 'lightbox-exclude-media', 'off');

		$post_media_source_type = $this->_get_image_source_type();

		$skins_html = '';
		$skins_css = '';
		$i = 1;
		$posts = $this->get_grid_categories_posts();

		if (!empty($posts) && count($posts) > 0) {

			$default_media_source_order = self::$base->getVar($this->grid_postparams, 'media-source-order');
			$default_lightbox_source_order = self::$base->getVar($this->grid_params, 'lb-source-order');
			$default_aj_source_order = self::$base->getVar($this->grid_params, 'aj-source-order');
			$media_sources = array_unique(
				array_filter(
					array_merge(
						(array)$default_media_source_order,
						(array)$default_lightbox_source_order,
						(array)$default_aj_source_order
					)
				)
			);

			$image_sizes = false;
			$image_source_smart = self::$base->getVar($this->grid_postparams, 'image-source-smart', 'off');
			if ('on' === $image_source_smart) {
				$image_sizes = self::$base->getVar($this->grid_postparams, 'image-source-smart-size', false);
			}

			foreach ($posts as $post) {
				// check if post should be visible or if its invisible on current grid settings
				if (!$grid_preview && !$this->check_if_visible($post['ID'], $this->grid_id)) continue;

				if ($lightbox_mode == 'content' || $lightbox_mode == 'content-gallery' || $lightbox_mode == 'woocommerce-gallery') {
					$item_skin->set_lightbox_rel('essgrid-' . $post['ID']);
				}
				
				$post_media_source_data = self::$base->get_post_media_source_data($post['ID'], $post_media_source_type, $media_sources, $image_sizes);
				$post_video_ratios = self::$meta->get_post_video_ratios($post['ID']);
				
				if (is_array($order_by) && !empty($order_by)) {
					$sort = $this->prepare_sorting_array_by_post($post, $order_by);
					$item_skin->set_sorting($sort);
				}

				// switch to different skin
				$use_item_skin_id = json_decode(get_post_meta($post['ID'], 'eg_use_skin', true), true);
				if ($use_item_skin_id !== false && isset($use_item_skin_id[$this->grid_id]['use-skin'])) {
					$use_item_skin_id = $use_item_skin_id[$this->grid_id]['use-skin'];
				} else {
					$use_item_skin_id = -1;
				}
				$use_item_skin_id = apply_filters('essgrid_modify_post_item_skin', $use_item_skin_id, $post, $this->grid_id);
				$item_skin->switch_item_skin($use_item_skin_id);
				$item_skin->register_layer_css();
				$item_skin->register_skin_css();

				$filters = self::_process_post_filter($this, $post);

				// skip if we load only elements from load more ajax request
				if ($only_elements !== true && $set_load_more !== true) {
					if ( $i > $post_limit ) {
						// Load only selected numbers of items at start (for load more)
						// set for load more, only on elements that will not be loaded from beginning
						$this->load_more_post_array[ $post['ID'] ] = $filters;
						continue;
					}
				}
				$i++;

				$this->_is_lightbox_additions($lightbox_mode, $lightbox_exclude_original_media, $item_skin, $post);

				$item_skin->set_filter($filters);
				$item_skin->set_media_sources($post_media_source_data);
				$item_skin->set_video_ratios($post_video_ratios);
				$item_skin->set_post_values($post);
				if (isset($post['post_title'])) $item_skin->set_item_title($post['post_title']);

				ob_start();
				$item_skin->output_item_skin($grid_preview);
				$skins_html .= ob_get_clean();

				// 2.2.6
				ob_start();
				$item_skin->output_element_css_by_meta($post['ID']);
				$skins_css .= ob_get_clean();
			}
		} else {
			if (is_admin()) {
				$this->display_grid_error_msg();
			}

			$no_items_msg = apply_filters('essgrid_output_by_posts_no_items', '', $this);
			if (!empty($no_items_msg)) {
				echo wp_kses_post($no_items_msg);
			}
			
			return false;
		}

		// return pure items html for ajax load
		if ($from_ajax) return $skins_html;

		$this->_output_grid($grid_preview, $navigation_c, $item_skin, $skins_css, $skins_html, $only_elements);
		
		do_action('essgrid_output_by_posts_post', $this, $grid_preview, $only_elements, $set_load_more, $from_ajax);
	}

	/**
	 * Output by specific posts for load more
	 */
	public function output_by_specific_posts()
	{
		do_action('essgrid_output_by_specific_posts_pre', $this);

		$start_sortby = self::$base->getVar($this->grid_params, 'sorting-order-by-start', 'none');
		$start_sortby_type = self::$base->getVar($this->grid_params, 'sorting-order-type', 'ASC');
		if (!empty($this->filter_by_ids)) {
			$posts = self::$base::get_posts_by_ids($this->filter_by_ids, $start_sortby, $start_sortby_type);
		} else {
			return false;
		}

		if (!empty($posts) && count($posts) > 0) {
			// cache posts for get_grid_categories_posts
			self::$grid_posts[$this->grid_id] = $posts;
			$skins_html = $this->output_by_posts(false, true, true, true);
		} else {
			$skins_html = apply_filters('essgrid_output_by_specific_posts_no_items', false, $this);
		}

		do_action('essgrid_output_by_specific_posts_post', $this, $skins_html);

		return apply_filters('essgrid_output_by_specific_posts_return', $skins_html, $this);
	}

	/**
	 * Output by specific ids for load more custom grid
	 */
	public function output_by_specific_ids()
	{
		do_action('essgrid_output_by_specific_ids_pre', $this);

		foreach ($this->grid_layers as $key => $entry) {
			if (!in_array($key, $this->filter_by_ids) || !is_array($entry)) unset($this->grid_layers[$key]);
		}

		if (!empty($this->grid_layers) && count($this->grid_layers) > 0) {
			$skins_html = $this->output_by_custom(false, true, true, true);
		} else {
			$skins_html = apply_filters('essgrid_output_by_specific_ids_no_items', false, $this);
		}

		do_action('essgrid_output_by_specific_ids_post', $this, $skins_html);

		return apply_filters('essgrid_output_by_specific_ids_return', $skins_html, $this);
	}

	public function prepare_sorting_array_by_post($post, $order_by)
	{
		$d = apply_filters('essgrid_prepare_sorting_array_by_post_pre', [
			'post' => $post,
			'order_by' => $order_by
		]);
		$post = $d['post'];
		$order_by = $d['order_by'];

		$m = self::$meta->get_all_meta(false);
		$lm = self::$meta_link->get_all_link_meta();

		$sorts = [];
		foreach ($order_by as $order) {
			switch ($order) {
				case 'date':
					$sorts['date'] = strtotime(self::$base->getVar($post, 'post_date'));
					break;
				case 'title':
					$sorts['title'] = self::$base->getVar($post, 'post_title');
					$sorts['title'] = (strlen($sorts['title']) > 32) ? substr($sorts['title'], 0, 32) : $sorts['title'];
					break;
				case 'excerpt':
					$sorts['excerpt'] = self::$base->getVar($post, 'post_excerpt');
					$sorts['excerpt'] = (strlen($sorts['excerpt']) > 32) ? substr($sorts['excerpt'], 0, 32) : $sorts['excerpt'];
					break;
				case 'id':
					$sorts['id'] = self::$base->getVar($post, 'ID');
					break;
				case 'slug':
					$sorts['slug'] = self::$base->getVar($post, 'post_name');
					break;
				case 'author':
					$authorID = self::$base->getVar($post, 'post_author');
					$sorts['author'] = get_the_author_meta('display_name', $authorID);
					break;
				case 'last-modified':
					$sorts['last-modified'] = strtotime(self::$base->getVar($post, 'post_modified'));
					break;
				case 'number-of-comments':
					$sorts['number-of-comments'] = self::$base->getVar($post, 'comment_count');
					break;
				case 'likespost':
					$post_id = self::$base->getVar($post, 'ID');
					$like_count = get_post_meta($post_id, "eg_votes_count", 0);
					$sorts['likespost'] = isset($like_count[0]) ? intval($like_count[0]) : 0;
					break;
				case 'random':
					$sorts['random'] = wp_rand(0, 9999);
					break;
				default: //check if meta. If yes, add meta values
					if (strpos($order, 'eg-') === 0) {
						if (!empty($m)) {
							foreach ($m as $me) {
								if ('eg-' . $me['handle'] == $order) {
									$sorts[$order] = self::$meta->get_meta_value_by_handle($post['ID'], $order);
									break;
								}
							}
						}
					} elseif (strpos($order, 'egl-') === 0) {
						if (!empty($lm)) {
							foreach ($lm as $me) {
								if ('egl-' . $me['handle'] == $order) {
									$sorts[$order] = self::$meta_link->get_link_meta_value_by_handle($post['ID'], $order);
									break;
								}
							}
						}
					}
					break;
			}
		}

		//add woocommerce sortings
		if (Essential_Grid_Woocommerce::is_woo_exists()) {
			$is_30 = Essential_Grid_Woocommerce::version_check('3.0');
			$product = ($is_30) ? wc_get_product($post['ID']) : get_product($post['ID']);

			if (!empty($product)) {
				foreach ($order_by as $order) {
					switch ($order) {
						case 'meta_num_total_sales':
							$sorts['total-sales'] = get_post_meta($post['ID'], 'total_sales', true);
							break;
						case 'meta_num__regular_price':
							$sorts['regular-price'] = $product->get_price();
							break;
						case 'meta__featured':
							$sorts['featured'] = ($product->is_featured()) ? '1' : '0';
							break;
						case 'meta__sku':
							$sorts['sku'] = $product->get_sku();
							break;
						case 'meta_num_stock':
							$sorts['in-stock'] = $product->get_stock_quantity();
							break;
					}
				}
			}
		}

		return apply_filters('essgrid_prepare_sorting_array_by_post_post', $sorts, $post, $order_by);
	}

	public function prepare_sorting_array_by_custom($post, $order_by)
	{
		$d = apply_filters('essgrid_prepare_sorting_array_by_custom_pre', [
			'post' => $post,
			'order_by' => $order_by
		]);
		$post = $d['post'];
		$order_by = $d['order_by'];

		$sorts = [];
		foreach ($order_by as $order) {
			switch ($order) {
				case 'date':
					$sorts['date'] = strtotime(self::$base->getVar($post, 'date', gmdate('Y-m-d H:i:s')));
					break;
				case 'title':
					$sorts['title'] = self::$base->getVar($post, 'title');
					$sorts['title'] = (strlen($sorts['title']) > 32) ? substr($sorts['title'], 0, 32) : $sorts['title'];
					break;
				case 'excerpt':
					$sorts['excerpt'] = self::$base->getVar($post, 'excerpt');
					$sorts['excerpt'] = (strlen($sorts['excerpt']) > 32) ? substr($sorts['excerpt'], 0, 32) : $sorts['excerpt'];
					break;
				case 'id':
					$sorts['id'] = self::$base->getVar($post, 'post_id');
					break;
				case 'slug':
					$sorts['slug'] = self::$base->getVar($post, 'alias');
					break;
				case 'author':
					$sorts['author'] = self::$base->getVar($post, 'author_name');
					break;
				case 'last-modified':
					$sorts['last-modified'] = strtotime(self::$base->getVar($post, 'date_modified'));
					break;
				case 'number-of-comments':
					$sorts['number-of-comments'] = self::$base->getVar($post, 'num_comments');
					break;
				case 'random':
					$sorts['random'] = wp_rand(0, 9999);
					break;
				case 'views':
					$sorts['views'] = self::$base->getVar($post, 'views');
					break;
				case 'likespost':
					$post_id = self::$base->getVar($post, 'ID');
					$like_count = get_post_meta($post_id, "eg_votes_count", 0);
					$sorts['likespost'] = isset($like_count[0]) ? $like_count[0] : 0;
					break;
				case 'likes':
					$sorts['likes'] = self::$base->getVar($post, 'likes');
					break;
				case 'dislikes':
					$sorts['dislikes'] = self::$base->getVar($post, 'dislikes');
					break;
				case 'retweets':
					$sorts['retweets'] = self::$base->getVar($post, 'retweets');
					break;
				case 'favorites':
					$sorts['favorites'] = self::$base->getVar($post, 'favorites');
					break;
				case 'itemCount':
					$sorts['itemCount'] = self::$base->getVar($post, 'itemCount');
					break;
				case 'duration':
					$sorts['duration'] = self::$base->getVar($post, 'duration');
					break;
				default: //check if meta. If yes, add meta values
					if (strpos($order, 'eg-') === 0 || strpos($order, 'egl-') === 0) {
						$sorts[$order] = self::$base->getVar($post, $order);
					}
					break;
			}
		}
		return apply_filters('essgrid_prepare_sorting_array_by_custom_post', $sorts, $post, $order_by);
	}

	public function prepare_sorting_array_by_stream($post, $order_by)
	{
		$d = apply_filters('essgrid_prepare_sorting_array_by_stream_pre', [
			'post' => $post,
			'order_by' => $order_by
		]);
		$post = $d['post'];
		$order_by = $d['order_by'];

		$sorts = [];
		foreach ($order_by as $order) {
			switch ($order) {
				case 'date':
					$sorts['date'] = strtotime(self::$base->getVar($post, 'date'));
					break;
				case 'title':
					$sorts['title'] = self::$base->getVar($post, 'title');
					$sorts['title'] = (strlen($sorts['title']) > 32) ? substr($sorts['title'], 0, 32) : $sorts['title'];
					break;
				case 'excerpt':
					$sorts['excerpt'] = self::$base->getVar($post, 'excerpt');
					$sorts['excerpt'] = (strlen($sorts['excerpt']) > 32) ? substr($sorts['excerpt'], 0, 32) : $sorts['excerpt'];
					break;
				case 'id':
					$sorts['id'] = self::$base->getVar($post, 'post_id');
					break;
				case 'slug':
					$sorts['slug'] = self::$base->getVar($post, 'alias');
					break;
				case 'author':
					$sorts['author'] = self::$base->getVar($post, 'author_name');
					break;
				case 'last-modified':
					$sorts['last-modified'] = strtotime(self::$base->getVar($post, 'date_modified'));
					break;
				case 'number-of-comments':
					$sorts['number-of-comments'] = self::$base->getVar($post, 'num_comments');
					break;
				case 'random':
					$sorts['random'] = wp_rand(0, 9999);
					break;
				case 'likespost':
					$post_id = self::$base->getVar($post, 'ID');
					$like_count = get_post_meta($post_id, "eg_votes_count", 0);
					$sorts['likespost'] = isset($like_count[0]) ? $like_count[0] : 0;
					break;
				case 'views':
					$sorts['views'] = self::$base->getVar($post, 'views');
					break;
				default: 
					// check if meta. If yes, add meta values
					if (strpos($order, 'eg-') === 0 || strpos($order, 'egl-') === 0) {
						$sorts[$order] = self::$base->getVar($post, $order);
					}
					break;
			}
		}
		return apply_filters('essgrid_prepare_sorting_array_by_stream_post', $sorts, $post, $order_by);
	}

	public function output_wrapper_pre($grid_preview = false)
	{
		self::$grid_serial++;
		
		if ($this->grid_div_name === null) $this->grid_div_name = $this->grid_id;

		$grid_id = ($grid_preview !== false) ? 'esg-preview-grid' : 'esg-grid-' . $this->grid_div_name . '-' . self::$grid_serial;
		$grid_id_wrap = $grid_id . '-wrap';
		$article_id = ($grid_preview !== false) ? ' esg-preview-skinlevel' : '';

		$hide_markup_before_load = self::$base->getVar($this->grid_params, 'hide-markup-before-load', 'off');
		$background_color = self::$base->getVar($this->grid_params, 'main-background-color', 'transparent');
		$navigation_skin = self::$base->getVar($this->grid_params, 'navigation-skin', 'minimal-light');
		$paddings = self::$base->getVar($this->grid_params, 'grid-padding', 0);
		$css_id = self::$base->getVar($this->grid_params, 'css-id');
		$source_type = self::$base->getVar($this->grid_postparams, 'source-type', 'post');
		$entry_skin_handle = self::$base->getVar($this->grid_params, 'entry-skin-handle');

		/* 2.1.6 */
		if (class_exists('ESGColorpicker')) {
			$background_col = ESGColorpicker::process($background_color);
			if (!empty($background_col) && is_array($background_col)) {
				$background_color = $background_col[0];
				if (empty($background_color))
					$background_color = '#FFFFFF';
			}
		}

		$pad_style = '';
		if (is_array($paddings) && !empty($paddings)) {
			$pad_style = 'padding: ';
			foreach ($paddings as $size) {
				$pad_style .= esc_attr($size) . 'px ';
			}
			$pad_style .= ';';
			$pad_style .= ' box-sizing:border-box;';
			$pad_style .= ' -moz-box-sizing:border-box;';
			$pad_style .= ' -webkit-box-sizing:border-box;';
		}

		$div_style = 'background: ' . esc_attr($background_color) . ';';
		$div_style .= $pad_style;
		if ($hide_markup_before_load == 'on') $div_style .= ' display:none';

		if ($css_id == '') $css_id = $grid_id_wrap;

		$do_fix_height = $this->add_start_height_css($css_id);

		$this->remove_load_more_button($css_id);

		$classes = ['myportfolio-container', 'esg-grid-wrap-container'];
		$classes[] = esc_attr($navigation_skin);
		if ($do_fix_height) $classes[] = 'eg-startheight';
		if ($entry_skin_handle) $classes[] = 'esg-entry-skin-' . esc_attr($entry_skin_handle);
		$classes[] = 'source_type_' . esc_attr($source_type);

		$n = '<!-- THE ESSENTIAL GRID ' . strtoupper(esc_attr($source_type)) . ' -->' . "\n\n";
		$n .= '<article id="' . esc_attr($css_id . $article_id) . '" class="' . implode(' ', $classes) . '" data-alias="' . esc_attr($this->grid_handle) . '">' . "\n\n";
		$n .= '   <div id="' . esc_attr($grid_id) . '" class="esg-grid" style="' . $div_style . '">' . "\n";

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters('essgrid_output_wrapper_pre', $n, $grid_preview);
	}

	public function output_wrapper_post()
	{
		$n = '    </div>' . "\n\n";
		$n .= '</article>' . "\n";
		$n .= '<div class="clear"></div>' . "\n";

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters('essgrid_output_wrapper_post', $n);
	}

	public function output_grid_pre()
	{
		$n = '<ul>' . "\n";
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters('essgrid_output_grid_pre', $n);
	}

	public function output_grid_post()
	{
		$n = '</ul>' . "\n";
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters('essgrid_output_grid_post', $n);
	}

	public function output_grid_javascript($load_lightbox = false, $is_demo = false)
	{
		$hide_markup_before_load = self::$base->getVar($this->grid_params, 'hide-markup-before-load', 'off');
		$layout = self::$base->getVar($this->grid_params, 'layout', 'even');
		$content_push = self::$base->getVar($this->grid_params, 'content-push', 'off');

		$rows_unlimited = self::$base->getVar($this->grid_params, 'rows-unlimited', 'on');
		$rows = self::$base->getVar($this->grid_params, 'rows', 4, 'i');

		if (wp_is_mobile()) {
			$mobile_rows = self::$base->getVar($this->grid_params, 'enable-rows-mobile', 'off') === 'on';
			if ($mobile_rows) $rows = self::$base->getVar($this->grid_params, 'rows-mobile', 3, 'i');
		}

		$columns = self::$base->getVar($this->grid_params, 'columns');
		$columns = self::$base->set_basic_colums($columns);

		$columns_advanced = self::$base->getVar($this->grid_params, 'columns-advanced', 'off');
		if ($columns_advanced == 'on') {
			$columns_width = self::$base->getVar($this->grid_params, 'columns-width');
			if ($layout == 'masonry') {
				$masonry_content_height = self::$base->getVar($this->grid_params, 'mascontent-height');
			} else {
				$masonry_content_height = []; //get defaults
			}
		} else {
			$columns_width = []; //get defaults
			$masonry_content_height = []; //get defaults
		}

		$columns_width = self::$base->set_basic_colums_width($columns_width);
		$masonry_content_height = self::$base->set_basic_masonry_content_height($masonry_content_height);

		// 2.2.6
		$hide_blankitems_at = self::$base->getVar($this->grid_params, 'blank-item-breakpoint', '1');
		$space = self::$base->getVar($this->grid_params, 'spacings', 0, 'i');
		$page_animation = self::$base->getVar($this->grid_params, 'grid-animation', 'scale');
		$layout_sizing = self::$base->getVar($this->grid_params, 'layout-sizing', 'boxed');
		$layout_offset_container = self::$base->getVar($this->grid_params, 'fullscreen-offset-container');

		// 2.2.5
		$start_animation = self::$base->getVar($this->grid_params, 'grid-start-animation', 'reveal');
		$start_animation_speed = self::$base->getVar($this->grid_params, 'grid-start-animation-speed', 1000, 'i');
		$start_animation_delay = self::$base->getVar($this->grid_params, 'grid-start-animation-delay', 100, 'i');
		$start_animation_type = self::$base->getVar($this->grid_params, 'grid-start-animation-type', 'item');
		$animation_type = self::$base->getVar($this->grid_params, 'grid-animation-type', 'item');

		if ($start_animation === 'reveal') {
			if ($layout_sizing !== 'fullscreen') {
				$start_animation = 'none';
				$hide_markup_before_load = 'on';
			} else {
				$start_animation = 'scale';
				$start_animation_delay = 0;
				$hide_markup_before_load = 'off';
			}
		}

		// 2.2.6
		if ($rows_unlimited === 'off') {
			$touchswipe = self::$base->getVar($this->grid_params, 'pagination-touchswipe', 'off');
			$dragvertical = self::$base->getVar($this->grid_params, 'pagination-dragvertical', 'on');
			$swipebuffer = self::$base->getVar($this->grid_params, 'pagination-swipebuffer', 30, 'i');
		} else {
			$touchswipe = 'off';
			$dragvertical = 'off';
			$swipebuffer = 30;
		}

		$anim_speed = self::$base->getVar($this->grid_params, 'grid-animation-speed', 800, 'i');
		$delay_basic = self::$base->getVar($this->grid_params, 'grid-animation-delay', 1, 'i');
		$delay_hover = self::$base->getVar($this->grid_params, 'hover-animation-delay', 1, 'i');
		$filter_type = self::$base->getVar($this->grid_params, 'filter-arrows', 'single');
		$filter_logic = self::$base->getVar($this->grid_params, 'filter-logic', 'or');
		$filter_show_on = self::$base->getVar($this->grid_params, 'filter-show-on', 'hover');

		$lightbox_mode = self::$base->getVar($this->grid_params, 'lightbox-mode', 'single');
		$lightbox_mode = ($lightbox_mode == 'content' || $lightbox_mode == 'content-gallery' || $lightbox_mode == 'woocommerce-gallery') ? 'contentgroup' : $lightbox_mode;

		/* 2.2 */
		$lb_button_order = self::$base->getVar($this->grid_params, 'lb-button-order', ['share', 'thumbs', 'close']);
		$lb_post_max_width = self::$base->getVar($this->grid_params, 'lightbox-post-content-max-width', '75');
		$lb_post_max_perc = self::$base->getVar($this->grid_params, 'lightbox-post-content-max-perc', 'on') == 'on' ? '%' : 'px';
		$lb_post_max_width = intval($lb_post_max_width) . $lb_post_max_perc;

		$lb_post_min_width = self::$base->getVar($this->grid_params, 'lightbox-post-content-min-width', '75');
		$lb_post_min_perc = self::$base->getVar($this->grid_params, 'lightbox-post-content-min-perc', 'on') == 'on' ? '%' : 'px';
		$lb_post_min_width = intval($lb_post_min_width) . $lb_post_min_perc;

		$no_filter_match_message = get_option('tp_eg_no_filter_match_message', 'No Items for the Selected Filter');

		/* 2.1.6 for lightbox post content addition */
		$lb_post_spinner = self::$base->getVar($this->grid_params, 'lightbox-post-spinner', 'off');
		$lb_featured_img = self::$base->getVar($this->grid_params, 'lightbox-post-content-img', 'off');
		$lb_featured_pos = self::$base->getVar($this->grid_params, 'lightbox-post-content-img-position', 'top');
		$lb_featured_width = self::$base->getVar($this->grid_params, 'lightbox-post-content-img-width', '100');
		$lb_featured_margin = self::$base->getVar($this->grid_params, 'lightbox-post-content-img-margin', ['0', '0', '0', '0']);
		$lb_post_title = self::$base->getVar($this->grid_params, 'lightbox-post-content-title', 'off');
		$lb_post_title_tag = self::$base->getVar($this->grid_params, 'lightbox-post-content-title-tag', 'h2');

		// 2.2 Deeplinking
		$filter_deep_linking = self::$base->getVar($this->grid_params, 'filter-deep-link', 'off');

		// 2.2.5 Mobile Filter Conversion
		$single_filters = self::$base->getVar($this->grid_params, 'filter-arrows', 'single');
		$filter_mobile_conversion = $single_filters === 'single' ? self::$base->getVar($this->grid_params, 'convert-mobile-filters', 'off') : false;
		$filter_mobile_conversion = $filter_mobile_conversion === 'on' ? 'true' : 'false';
		$filter_mobile_conversion_width = self::$base->getVar($this->grid_params, 'convert-mobile-filters-width', '768');

		if (!is_array($lb_featured_margin) || count($lb_featured_margin) !== 4) $lb_featured_margin = ['0', '0', '0', '0'];
		$lb_featured_margin = implode('|', $lb_featured_margin);

		$aspect_ratio_x = self::$base->getVar($this->grid_params, 'x-ratio', 4, 'i');
		$aspect_ratio_y = self::$base->getVar($this->grid_params, 'y-ratio', 3, 'i');
		$auto_ratio = self::$base->getVar($this->grid_params, 'auto-ratio', 'true');

		$wait_for_viewport = self::$base->getVar($this->grid_params, 'wait-for-viewport', 'on');
		$lazy_load = self::$base->getVar($this->grid_params, 'lazy-loading', 'off');
		$lazy_load_color = self::$base->getVar($this->grid_params, 'lazy-load-color', '#FFFFFF');

		$spinner = self::$base->getVar($this->grid_params, 'use-spinner', '0');
		$spinner_color = self::$base->getVar($this->grid_params, 'spinner-color', '#FFFFFF');

		/* 2.1.6 */
		if (class_exists('ESGColorpicker')) {
			$spinner_col = ESGColorpicker::process($spinner_color);
			$lazy_load_col = ESGColorpicker::process($lazy_load_color);
			if (!empty($spinner_col) && is_array($spinner_col)) {
				$spinner_color = $spinner_col[0];
				if (empty($spinner_color))
					$spinner_color = '#FFFFFF';
			}
			if (!empty($lazy_load_col) && is_array($lazy_load_col)) {
				$lazy_load_color = $lazy_load_col[0];
				if (empty($lazy_load_color))
					$lazy_load_color = '#FFFFFF';
			}
		}

		$lightbox_effect_open_close = self::$base->getVar($this->grid_params, 'lightbox-effect-open-close', 'fade');

		$lightbox_effect_open_close_speed = self::$base->getVar($this->grid_params, 'lightbox-effect-open-close-speed', '500');
		if (!is_numeric($lightbox_effect_open_close_speed))
			$lightbox_effect_open_close_speed = '500';

		$lightbox_effect_next_prev = self::$base->getVar($this->grid_params, 'lightbox-effect-next-prev', 'fade');

		$lightbox_effect_next_prev_speed = self::$base->getVar($this->grid_params, 'lightbox-effect-next-prev-speed', '366');
		if (!is_numeric($lightbox_effect_next_prev_speed))
			$lightbox_effect_next_prev_speed = '366';

		$lightbox_deep_link = self::$base->getVar($this->grid_params, 'lightbox-deep-link', 'group');
		if (empty($lightbox_deep_link))
			$lightbox_deep_link = 'group';

		$lightbox_mousewheel = self::$base->getVar($this->grid_params, 'lightbox-mousewheel', 'off') == 'on' ? 'auto' : false;
		$lightbox_arrows = self::$base->getVar($this->grid_params, 'lightbox-arrows', 'off') == 'on' ? 'true' : 'false';
		$lightbox_caption_position = self::$base->getVar($this->grid_params, 'lightbox-title-position', 'bottom');
		$lightbox_base_class = 'esgbox-container-' . $this->grid_div_name;

		$lbox_autoplay = self::$base->getVar($this->grid_params, 'lightbox-autoplay', 'off') == 'on' ? 'true' : 'false';
		$lbox_videoautoplay = self::$base->getVar($this->grid_params, 'lightbox-videoautoplay', 'on') == 'on' ? 'true' : 'false';
		$lbox_playspeed = self::$base->getVar($this->grid_params, 'lbox-playspeed', '3000');
		$lbox_padding = self::$base->getVar($this->grid_params, 'lbox-padding', ['0', '0', '0', '0']);
		$lbox_numbers = self::$base->getVar($this->grid_params, 'lightbox-numbers', 'on') === 'on' ? 'true' : 'false';
		$lbox_loop = self::$base->getVar($this->grid_params, 'lightbox-loop', 'on') === 'on' ? 'true' : 'false';
		
		$lbox_margin = self::$base->getVar($this->grid_params, 'lbox-padding', ['0', '0', '0', '0']);
		if (!is_array($lbox_margin) || count($lbox_margin) !== 4) $lbox_margin = ['0', '0', '0', '0'];
		$lbox_margin = implode('|', $lbox_margin);

		$lbox_inpadding = self::$base->getVar($this->grid_params, 'lbox-content_padding', ['0', '0', '0','0']);
		if (!is_array($lbox_inpadding) || count($lbox_inpadding) !== 4) $lbox_inpadding = ['0', '0', '0', '0'];
		$lbox_inpadding = implode('|', $lbox_inpadding);

		$lbox_overflow = self::$base->getVar($this->grid_params, 'lightbox-post-content-overflow', 'on') == 'on' ? 'auto' : 'hidden';

		$rtl = self::$base->getVar($this->grid_params, 'rtl', 'off');

		$pagination_numbers = self::$base->getVar($this->grid_params, 'pagination-numbers', 'smart');
		$pagination_scroll = self::$base->getVar($this->grid_params, 'pagination-scroll', 'off');
		$pagination_scroll_offset = self::$base->getVar($this->grid_params, 'pagination-scroll-offset', '0', 'i');

		if (self::$base->getVar($this->grid_params, 'rows-unlimited', 'on') == 'off') {
			$pagination_autoplay = self::$base->getVar($this->grid_params, 'pagination-autoplay', 'off');
			$pagination_autoplay_delay = self::$base->getVar($this->grid_params, 'pagination-autoplay-speed', '5000', 'i');
		} else {
			$pagination_autoplay = 'off';
			$pagination_autoplay_delay = 5000;
		}

		$ajax_callback = self::$base->getVar($this->grid_params, 'ajax-callback');
		$ajax_css_url = self::$base->getVar($this->grid_params, 'ajax-css-url');
		$ajax_js_url = self::$base->getVar($this->grid_params, 'ajax-js-url');
		$ajax_scroll_onload = self::$base->getVar($this->grid_params, 'ajax-scroll-onload', 'on');
		$ajax_callback_argument = self::$base->getVar($this->grid_params, 'ajax-callback-arg', 'on');
		$ajax_content_id = self::$base->getVar($this->grid_params, 'ajax-container-id');
		$ajax_scrollto_offset = self::$base->getVar($this->grid_params, 'ajax-scrollto-offset', '0');
		$ajax_close_button = self::$base->getVar($this->grid_params, 'ajax-close-button', 'off');
		$ajax_button_nav = self::$base->getVar($this->grid_params, 'ajax-nav-button', 'off');
		$ajax_content_sliding = self::$base->getVar($this->grid_params, 'ajax-content-sliding', 'on');
		$ajax_button_type = self::$base->getVar($this->grid_params, 'ajax-button-type', 'button');
		if ($ajax_button_type == 'type2') {
			$ajax_button_text = self::$base->getVar($this->grid_params, 'ajax-button-text', esc_attr__('Close', 'essential-grid'));
		}
		$ajax_button_skin = self::$base->getVar($this->grid_params, 'ajax-button-skin', 'light');
		$ajax_button_inner = self::$base->getVar($this->grid_params, 'ajax-button-inner', 'false');
		$ajax_button_h_pos = self::$base->getVar($this->grid_params, 'ajax-button-h-pos', 'r');
		$ajax_button_v_pos = self::$base->getVar($this->grid_params, 'ajax-button-v-pos', 't');

		$cobbles_pattern = self::$base->getVar($this->grid_params, 'cobbles-pattern', []);
		$cobbles_to_even = self::$base->getVar($this->grid_params, 'show-even-on-device', '0');
		$use_cobbles_pattern = self::$base->getVar($this->grid_params, 'use-cobbles-pattern', 'off');

		$cookie_time = intval(self::$base->getVar($this->grid_params, 'cookie-save-time', '30'));
		$cookie_search = self::$base->getVar($this->grid_params, 'cookie-save-search', 'off');
		$cookie_filter = self::$base->getVar($this->grid_params, 'cookie-save-filter', 'off');
		$cookie_pagination = self::$base->getVar($this->grid_params, 'cookie-save-pagination', 'off');

		$load_more_start = self::$base->getVar($this->grid_params, 'load-more-start', 3, 'i');
		$load_more_error = self::$base->getVar($this->grid_params, 'load-more-error');

		$js_to_footer = Essential_Grid_Base::isJsToFooter();
		$use_crossorigin = get_option('tp_eg_use_crossorigin', 'false') == 'true';

		$grid_api_serial = esc_js($this->grid_api_name . '_' . self::$grid_serial);
		$grid_div_serial = esc_js($this->grid_div_name . '-' . self::$grid_serial);

		ob_start();
		
		if ($hide_markup_before_load == 'off') {
			echo 'function eggbfc(winw,resultoption) {' . "\n";
			echo '  var lasttop = winw,' . "\n";
			echo '  lastbottom = 0,' . "\n";
			echo '  smallest =9999,' . "\n";
			echo '  largest = 0,' . "\n";
			echo '  samount = 0,' . "\n";
			echo '  lamount = 0,' . "\n";
			echo '  lastamount = 0,' . "\n";
			echo '  resultid = 0,' . "\n";
			echo '  resultidb = 0,' . "\n";
			echo '  responsiveEntries = [' . "\n";
			$amount = count( self::$base::get_basic_devices());
			for ($i = 0; $i < $amount; $i++) {
				echo '            { width:' . esc_js($columns_width[$i]) . ',amount:' . esc_js($columns[$i]) . ',mmheight:' . esc_js($masonry_content_height[$i]) . '},' . "\n";
			}
			echo '            ];' . "\n";
			echo '  if (responsiveEntries!=undefined && responsiveEntries.length>0)' . "\n";
			echo '    jQuery.each(responsiveEntries, function(index,obj) {' . "\n";
			echo '      var curw = obj.width != undefined ? obj.width : 0,' . "\n";
			echo '        cura = obj.amount != undefined ? obj.amount : 0;' . "\n";
			echo '      if (smallest>curw) {' . "\n";
			echo '        smallest = curw;' . "\n";
			echo '        samount = cura;' . "\n";
			echo '        resultidb = index;' . "\n";
			echo '      };' . "\n";
			echo '      if (largest<curw) {' . "\n";
			echo '        largest = curw;' . "\n";
			echo '        lamount = cura;' . "\n";
			echo '      };' . "\n";
			echo '      if (curw>lastbottom && curw<=lasttop) {' . "\n";
			echo '        lastbottom = curw;' . "\n";
			echo '        lastamount = cura;' . "\n";
			echo '        resultid = index;' . "\n";
			echo '      };' . "\n";
			echo '    });' . "\n";
			echo '    if (smallest>winw) {' . "\n";
			echo '      lastamount = samount;' . "\n";
			echo '      resultid = resultidb;' . "\n";
			echo '    };' . "\n";
			echo '    var obj = new Object;' . "\n";
			echo '    obj.index = resultid;' . "\n";
			echo '    obj.column = lastamount;' . "\n";
			echo '    if (resultoption=="id"){' . "\n";
			echo '      return obj;' . "\n";
			echo '    }else{' . "\n";
			echo '      return lastamount;' . "\n";
			echo '    }' . "\n";
			echo '  };' . "\n";
			echo '  var coh=0,' . "\n";
			echo '    container = jQuery("#esg-grid-' . esc_js($grid_div_serial) . '");' . "\n";
			if ($layout_sizing == 'fullscreen') {
				echo 'coh = jQuery(window).height();' . "\n";

				if ($layout_offset_container !== '') {
					echo 'try{' . "\n";
					echo '  var offcontainers = "' . esc_js($layout_offset_container) . '".split(",");' . "\n";
					echo '  jQuery.each(offcontainers,function(index,searchedcont) {' . "\n";
					echo '    coh = coh - jQuery(searchedcont).outerHeight(true);' . "\n";
					echo '  })' . "\n";
					echo '} catch(e) {}' . "\n";
				}
			} else {
				echo '  var cwidth = "' . esc_js($layout_sizing) . '" == "boxed" ? container.width() : jQuery(window).width(),' . "\n";
				echo '    ar = "' . esc_js($aspect_ratio_x) . ':' . esc_js($aspect_ratio_y) . '",' . "\n";
				echo '    gbfc = eggbfc(cwidth,"id"),' . "\n";
				if ($rows_unlimited == 'on') {
					$load_more_start = self::$base->getVar($this->grid_params, 'load-more-start', 3, 'i');
					echo '  row = Math.ceil(' . esc_js($load_more_start) . ' / gbfc.column);' . "\n";
				} else {
					echo '  row = ' . esc_js($rows) . ';' . "\n";
				}
				echo 'ar = ar.split(":");' . "\n";
				echo 'var aratio=parseInt(ar[0],0) / parseInt(ar[1],0);' . "\n";
				echo 'coh = cwidth / aratio;' . "\n";
				echo 'coh = coh/gbfc.column*row;' . "\n";
			}
			echo '  var ul = container.find("ul").first();' . "\n";
			echo '  ul.css({display:"block",height:coh+"px"});' . "\n";
		}

		echo 'var essapi_' . esc_js($grid_api_serial) . ';' . "\n";
		echo 'window.ESG ??={};' . "\n";

		echo 'window.ESG.E ??={};' . "\n";
		echo 'window.ESG.E.plugin_url = "' .esc_js(ESG_PLUGIN_URL). '";' . "\n";
		echo 'window.ESG.E.crossorigin = ' . ($use_crossorigin ? 'true' : 'false'). ';' . "\n";
		echo 'window.ESG.inits ??={};' . "\n";
		echo 'window.ESG.inits.v' . esc_js($grid_api_serial) . ' ??={ state:false};' . "\n";
		echo 'window.ESG.inits.v' . esc_js($grid_api_serial) . '.call = () => {' . "\n";
		echo 'jQuery(document).ready(function() {' . "\n";
		
		/////////////////
		// lightbox:start
		
		/* 2.2 */
		/* lightbox options written first, then custom JS from grid can override them if desired */
		echo '  var lightboxOptions = {' . "\n";

		echo '        margin : [' . esc_js($lbox_padding[0]) . ',' . esc_js($lbox_padding[1]) . ',' . esc_js($lbox_padding[2]) . ',' . esc_js($lbox_padding[3]) . '],' . "\n";
		// implode arguments switched for PHP 7.4
		echo '        buttons : ["' . implode('","', array_map('esc_js', $lb_button_order)) . '"],' . "\n";
		echo '        infobar : ' . esc_js($lbox_numbers) . ',' . "\n";
		echo '        loop : ' . esc_js($lbox_loop) . ',' . "\n";
		echo '        slideShow : {"autoStart": ' . esc_js($lbox_autoplay) . ', "speed": ' . esc_js($lbox_playspeed) . '},' . "\n";
		echo '        videoAutoPlay : ' . esc_js($lbox_videoautoplay) . ',' . "\n";
		echo '        animationEffect : ' . ($lightbox_effect_open_close != 'false' ? '"' . esc_js($lightbox_effect_open_close) . '"' : esc_js($lightbox_effect_open_close) ) . ',' . "\n"; 
		echo '        animationDuration : ' . esc_js($lightbox_effect_open_close_speed) . ',' . "\n";

		echo '        beforeShow: function(a, c) {' . "\n";
		if ($lightbox_arrows !== 'true') {
			echo '              jQuery("body").addClass("esgbox-hidearrows");' . "\n";
		}
		echo '          var i = 0,' . "\n";
		echo '              multiple = false;' . "\n";
		echo '          a = a.slides;' . "\n";
		echo '          for(var b in a) {' . "\n";
		echo '            i++;' . "\n";
		echo '            if (i > 1) {' . "\n";
		echo '              multiple = true;' . "\n";
		echo '              break;' . "\n";
		echo '            }' . "\n";
		echo '          };' . "\n";
		echo '          if (!multiple) jQuery("body").addClass("esgbox-single");' . "\n";
		echo '          if (c.type === "image") jQuery(".esgbox-button--zoom").show();' . "\n";
		echo '          if (c.contentType === "html") c.$slide.addClass("esgbox-slide--overflow-" + c.opts.overflow);' . "\n";
		echo '        },' . "\n";

		echo '        beforeLoad: function(a, b) {' . "\n";
		echo '          jQuery("body").removeClass("esg-four-by-three");' . "\n";
		echo '          if (b.opts.$orig.data("ratio") === "4:3") jQuery("body").addClass("esg-four-by-three");' . "\n";
		echo '        },' . "\n";

		echo '        afterLoad: function() {jQuery(window).trigger("resize.esglb");},' . "\n";
		echo '        afterClose : function() {jQuery("body").removeClass("esgbox-hidearrows esgbox-single");},' . "\n";

		echo '        transitionEffect : ' . ($lightbox_effect_next_prev != 'false' ? '"' . esc_js($lightbox_effect_next_prev) . '"' : esc_js($lightbox_effect_next_prev) ) . ',' . "\n";
		echo '        transitionDuration : ' . esc_js($lightbox_effect_next_prev_speed) . ',' . "\n";

		echo '        hash : "' . esc_js($lightbox_deep_link) . '",' . "\n";
		echo '        arrows : ' . esc_js($lightbox_arrows) . ',' . "\n";
		echo '        wheel : ' . wp_json_encode($lightbox_mousewheel) . ',' . "\n";
		echo '        baseClass : "' . esc_js($lightbox_base_class) . '",' . "\n";
		echo '        captionPosition : "' . esc_js($lightbox_caption_position) . '",' . "\n";
		echo '        overflow : "' . esc_js($lbox_overflow) . '",' . "\n";

		echo '  };' . "\n\n";

		echo '  jQuery("#esg-grid-' . esc_js($grid_div_serial) . '").data("lightboxsettings", lightboxOptions);' . "\n\n";

		// lightbox:end
		///////////////
		
		echo '  essapi_' . esc_js($grid_api_serial) . ' = jQuery("#esg-grid-' . esc_js($grid_div_serial) . '").tpessential({' . "\n";

		echo '        gridID:' . esc_js($this->grid_id) . ',' . "\n";
		echo '        layout:"' . esc_js($layout) . '",' . "\n";

		if ($rtl == 'on')
			echo '        rtl:"on",' . "\n";

		echo '        waitForViewport:"' . esc_js($wait_for_viewport) . '",' . "\n";
		echo '        lazyLoad:"' . esc_js($lazy_load) . '",' . "\n";
		if ($lazy_load == 'on')
			echo '        lazyLoadColor:"' . esc_js($lazy_load_color) . '",' . "\n";

		if ($rows_unlimited == 'on') {
			$load_more = self::$base->getVar($this->grid_params, 'load-more', 'button');
			$load_more_amount = self::$base->getVar($this->grid_params, 'load-more-amount', 3, 'i');
			$load_more_show_number = self::$base->getVar($this->grid_params, 'load-more-show-number', 'on');

			if ($load_more !== 'none') {
				$load_more_text = self::$base->getVar($this->grid_params, 'load-more-text', esc_attr__('Load More', 'essential-grid'));
				echo '        loadMoreType:"' . esc_js($load_more) . '",' . "\n";
				echo '        loadMoreAmount:' . esc_js($load_more_amount) . ',' . "\n";
				echo '        loadMoreTxt:"' . esc_js($load_more_text) . '",' . "\n";
				echo '        loadMoreNr:"' . esc_js($load_more_show_number) . '",' . "\n";
				echo '        loadMoreEndTxt:"' . esc_attr__('No More Items for the Selected Filter', 'essential-grid') . '",' . "\n";
				echo '        loadMoreItems:';
				$this->output_load_more_list();
				echo ',' . "\n";

				/* 2.1.5 */
				if (!empty($this->custom_images)) {
					echo '        customGallery: true,' . "\n";
				}
			}
			echo '        row:9999,' . "\n";
		} else {
			echo '        row:' . esc_js($rows) . ',' . "\n";
		}

		echo '        apiName: "essapi_' . esc_js($grid_api_serial) . '",' . "\n";
		echo '        loadMoreAjaxToken:"__Essential_Grid_Front_Token__",' . "\n";
		echo '        loadMoreAjaxUrl:"' . esc_js(admin_url('admin-ajax.php')) . '",' . "\n";
		echo '        loadMoreAjaxAction:"Essential_Grid_Front_request_ajax",' . "\n";
		if (!empty($load_more_error)) {
			echo '        loadMoreErrorMessage:"' . esc_js($load_more_error) . '",' . "\n";
		}

		echo '        ajaxContentTarget:"' . esc_js($ajax_content_id) . '",' . "\n";
		echo '        ajaxScrollToOffset:"' . esc_js($ajax_scrollto_offset) . '",' . "\n";
		echo '        ajaxCloseButton:"' . esc_js($ajax_close_button) . '",' . "\n";
		echo '        ajaxContentSliding:"' . esc_js($ajax_content_sliding) . '",' . "\n";
		if ($ajax_callback !== '')
			echo '        ajaxCallback:"' . esc_js($ajax_callback) . '",' . "\n";
		if ($ajax_css_url !== '')
			echo '        ajaxCssUrl:"' . esc_js($ajax_css_url) . '",' . "\n";
		if ($ajax_js_url !== '')
			echo '        ajaxJsUrl:"' . esc_js($ajax_js_url) . '",' . "\n";
		if ($ajax_scroll_onload !== 'off')
			echo '        ajaxScrollToOnLoad:"on",' . "\n";

		if ($ajax_callback_argument === 'on' || $ajax_callback_argument == 'true')
			echo '        ajaxCallbackArgument:"on",' . "\n";
		else
			echo '        ajaxCallbackArgument:"off",' . "\n";

		echo '        ajaxNavButton:"' . esc_js($ajax_button_nav) . '",' . "\n";
		echo '        ajaxCloseType:"' . esc_js($ajax_button_type) . '",' . "\n";
		if ($ajax_button_type == 'type2') {
			echo '        ajaxCloseTxt:"' . esc_js($ajax_button_text) . '",' . "\n";
		}
		echo '        ajaxCloseInner:"' . esc_js($ajax_button_inner) . '",' . "\n";
		echo '        ajaxCloseStyle:"' . esc_js($ajax_button_skin) . '",' . "\n";

		if ($ajax_button_h_pos == 'c') {
			echo '        ajaxClosePosition:"' . esc_js($ajax_button_v_pos) . '",' . "\n";
		} else {
			echo '        ajaxClosePosition:"' . esc_js($ajax_button_v_pos . $ajax_button_h_pos) . '",' . "\n";
		}

		echo '        space:' . esc_js($space) . ',' . "\n";
		echo '        pageAnimation:"' . esc_js($page_animation) . '",' . "\n";

		// 2.3.7
		$videoplaybackingrid = self::$base->getVar($this->grid_params, 'videoplaybackingrid', 'on');
		$videoloopingrid = self::$base->getVar($this->grid_params, 'videoloopingrid', 'on');
		$videoplaybackonhover = self::$base->getVar($this->grid_params, 'videoplaybackonhover', 'off');
		$videomuteinline = self::$base->getVar($this->grid_params, 'videomuteinline', 'on');
		$videocontrolsinline = self::$base->getVar($this->grid_params, 'videocontrolsinline', 'off');
		$keeplayersovermedia = self::$base->getVar($this->grid_params, 'keeplayersovermedia', 'off');

		echo '        videoPlaybackInGrid: "' . esc_js($videoplaybackingrid) . '",' . "\n";
		echo '        videoLoopInGrid: "' . esc_js($videoloopingrid) . '",' . "\n";
		echo '        videoPlaybackOnHover: "' . esc_js($videoplaybackonhover) . '",' . "\n";
		echo '        videoInlineMute: "' . esc_js($videomuteinline) . '",' . "\n";
		echo '        videoInlineControls: "' . esc_js($videocontrolsinline) . '",' . "\n";
		echo '        keepLayersInline: "' . esc_js($keeplayersovermedia) . '",' . "\n";

		// 2.2.5
		echo '        startAnimation: "' . esc_js($start_animation) . '",' . "\n";
		echo '        startAnimationSpeed: ' . esc_js($start_animation_speed) . ',' . "\n";
		echo '        startAnimationDelay: ' . esc_js($start_animation_delay) . ',' . "\n";
		echo '        startAnimationType: "' . esc_js($start_animation_type) . '",' . "\n";
		echo '        animationType: "' . esc_js($animation_type) . '",' . "\n";

		if ($pagination_numbers == 'full')
			echo '        smartPagination:"off",' . "\n";

		echo '        paginationScrollToTop:"' . esc_js($pagination_scroll) . '",' . "\n";
		if ($pagination_scroll == 'on') {
			echo '        paginationScrollToTopOffset:' . esc_js($pagination_scroll_offset) . ',' . "\n";
		}

		echo '        paginationAutoplay:"' . esc_js($pagination_autoplay) . '",' . "\n";
		if ($pagination_autoplay == 'on') {
			echo '        paginationAutoplayDelay:' . esc_js($pagination_autoplay_delay) . ',' . "\n";
		}

		echo '        spinner:"spinner' . esc_js($spinner) . '",' . "\n";
		
		if ($spinner != '0' && $spinner != '5')
			echo '        spinnerColor:"' . esc_js($spinner_color) . '",' . "\n";

		if ($layout_sizing == 'fullwidth') {
			echo '        forceFullWidth:"on",' . "\n";
		} elseif ($layout_sizing == 'fullscreen') {
			echo '        forceFullScreen:"on",' . "\n";
			if ($layout_offset_container !== '') {
				echo '        fullScreenOffsetContainer:"' . esc_js($layout_offset_container) . '",' . "\n";
			}
		}

		echo '        minVisibleItems:' . esc_js($load_more_start) . "," . "\n";

		if ($layout == 'even')
			echo '        evenGridMasonrySkinPusher:"' . esc_js($content_push) . '",' . "\n";

		echo '        lightBoxMode:"' . esc_js($lightbox_mode) . '",' . "\n";

		/* 2.2 */
		echo '        lightboxHash:"' . esc_js($lightbox_deep_link) . '",' . "\n";
		echo '        lightboxPostMinWid:"' . esc_js($lb_post_max_width) . '",' . "\n";
		echo '        lightboxPostMaxWid:"' . esc_js($lb_post_min_width) . '",' . "\n";

		/* 2.1.6 */
		echo '        lightboxSpinner:"' . esc_js($lb_post_spinner) . '",' . "\n";
		echo '        lightBoxFeaturedImg:"' . esc_js($lb_featured_img) . '",' . "\n";
		if ($lb_featured_img === 'on') {
			echo '        lightBoxFeaturedPos:"' . esc_js($lb_featured_pos) . '",' . "\n";
			echo '        lightBoxFeaturedWidth:"' . esc_js($lb_featured_width) . '",' . "\n";
			echo '        lightBoxFeaturedMargin:"' . esc_js($lb_featured_margin) . '",' . "\n";
		}
		echo '        lightBoxPostTitle:"' . esc_js($lb_post_title) . '",' . "\n";
		echo '        lightBoxPostTitleTag:"' . esc_js($lb_post_title_tag) . '",' . "\n";
		echo '        lightboxMargin : "' . esc_js($lbox_margin) . '",' . "\n";
		echo '        lbContentPadding : "' . esc_js($lbox_inpadding) . '",' . "\n";
		echo '        lbContentOverflow : "' . esc_js($lbox_overflow) . '",' . "\n";

		if (!empty($cobbles_pattern) && $layout == 'cobbles' && $use_cobbles_pattern == 'on') {
			echo '        cobblesPattern:"' . implode(',', array_map('esc_js', $cobbles_pattern)) . '",' . "\n";
		}
		
		if (!empty($cobbles_to_even) && $layout == 'cobbles' && $cobbles_to_even !== '0') {
			echo '      cobblesToEven:' . esc_js($cobbles_to_even) . ',' . "\n";
		}
		echo '        animSpeed:' . esc_js($anim_speed) . ',' . "\n";
		echo '        delayBasic:' . esc_js($delay_basic) . ',' . "\n";
		echo '        mainhoverdelay:' . esc_js($delay_hover) . ',' . "\n";


		echo '        filterType:"' . esc_js($filter_type) . '",' . "\n";

		if ($filter_type == 'multi') {
			echo '        filterLogic:"' . esc_js($filter_logic) . '",' . "\n";
		}
		echo '        showDropFilter:"' . esc_js($filter_show_on) . '",' . "\n";

		echo '        filterGroupClass:"esg-fgc-' . esc_js($this->grid_id) . '",' . "\n";

		// 2.2
		echo '        filterNoMatch:"' . esc_js($no_filter_match_message) . '",' . "\n";
		echo '        filterDeepLink:"' . esc_js($filter_deep_linking) . '",' . "\n";

		// 2.2.5
		echo '        hideMarkups: "' . esc_js($hide_markup_before_load) . '",' . "\n";
		echo '        youtubeNoCookie:"' . esc_js(get_option('tp_eg_enable_youtube_nocookie', 'false')) . '",' . "\n";
		echo '        convertFilterMobile:' . esc_js($filter_mobile_conversion) . ',' . "\n";
		echo '        convertFilterMobileWidth:' . esc_js($filter_mobile_conversion_width) . ',' . "\n";

		// 2.2.6
		echo '        paginationSwipe: "' . esc_js($touchswipe) . '",' . "\n";
		echo '        paginationDragVer: "' . esc_js($dragvertical) . '",' . "\n";
		echo '        pageSwipeThrottle: ' . esc_js($swipebuffer) . ',' . "\n";

		if ($cookie_search === 'on' || $cookie_filter === 'on' || $cookie_pagination === 'on') {
			echo '        cookies: {' . "\n";
			if ($cookie_search == 'on')
				echo '                search:"' . esc_js($cookie_search) . '",' . "\n";
			if ($cookie_filter == 'on')
				echo '                filter:"' . esc_js($cookie_filter) . '",' . "\n";
			if ($cookie_pagination == 'on')
				echo '                pagination:"' . esc_js($cookie_pagination) . '",' . "\n";
			echo '                timetosave:"' . esc_js($cookie_time) . '"' . "\n";
			echo '        },' . "\n";
		}

		if ($layout != 'masonry' || $auto_ratio != 'true') {
			echo '        aspectratio:"' . esc_js($aspect_ratio_x) . ':' . esc_js($aspect_ratio_y) . '",' . "\n";
		}

		// 2.2.6
		echo '        hideBlankItemsAt: "' . esc_js($hide_blankitems_at) . '",' . "\n";

		echo '        responsiveEntries: [' . "\n";
		$amount = count( self::$base::get_basic_devices());
		for ($i = 0; $i < $amount; $i++) {
			echo '            { width:' . esc_js($columns_width[$i]) . ',amount:' . esc_js($columns[$i]) . ',mmheight:' . esc_js($masonry_content_height[$i]) . '},' . "\n";
		}
		echo '            ]';

		if ($columns_advanced == 'on')
			$this->output_ratio_list();

		/**
		 * output should start from comma with newline
		 * 
		 * echo ",\n";
		 * echo '        googleFonts: ' . wp_json_encode($aUrl);
		 * 
		 * @param Essential_Grid
		 */
		do_action('essgrid_output_grid_javascript_options', $this);

		echo "\n";
		echo '  });' . "\n\n";

		//output custom javascript if any is set
		$custom_javascript = stripslashes(self::$base->getVar($this->grid_params, 'custom-javascript'));
		if ($custom_javascript !== '') {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $custom_javascript;
		}

		do_action('essgrid_output_grid_javascript_custom', $this);
		echo "\n";
		echo '});' . "\n";

		echo '}; // End of EsgInitScript' . "\n";
		//window.ESG.inits.v
		echo 'if (document.readyState === "loading")';
		echo 'document.addEventListener(\'readystatechange\',function() { ';
		echo ' if ((document.readyState === "interactive" || document.readyState === "complete") && !window.ESG.inits.v' . esc_js($grid_api_serial) . '.state )';
		echo '  {';
		echo ' if ((jQuery?.fn?.tpessential)) {';
		echo '   window.ESG.inits.v' . esc_js($grid_api_serial) . '.state = true;';
		echo '   window.ESG.inits.v' . esc_js($grid_api_serial) . '.call();';
		echo '}';
		echo '  }';
		echo '});';
		echo 'else';
		echo '{';
		echo ' if ((jQuery?.fn?.tpessential)) {';
		echo '  window.ESG.inits.v' . esc_js($grid_api_serial) . '.state = true; ';
		echo '  window.ESG.inits.v' . esc_js($grid_api_serial) . '.call();';
		echo '}';
		echo'}' . "\n";

		$js_content = ob_get_clean();
		
		$js_content = '<script type="text/javascript">' 
					  . Essential_Grid_Base::compress_assets( $js_content, true ) 
					  . '</script>' . "\n";

		if ($js_to_footer && !$is_demo) {
			$this->grid_inline_js = $js_content;
			add_action('wp_footer', [$this, 'add_inline_js']);
		} else {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already contain escaped js
			echo $js_content;
		}
	}

	/**
	 * Output the Load More list of posts
	 */
	public function output_load_more_list() {
		if (empty($this->load_more_post_array)) {
			echo '[]';
			return;
		}
		
		$rows = [];
		foreach ($this->load_more_post_array as $id => $filter) {
			$filters = empty($filter) ? [] : array_keys($filter);
			array_unshift($filters, -1);
			$rows[$id] = '            ' . wp_json_encode([$id, $filters]);
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $rows already contain json encoded strings
		echo '[' . "\n" . implode( ',' . "\n", $rows ) . ']';
	}

	/**
	 * Output the custom row sizes if its set
	 */
	public function output_ratio_list() {
		$columns = self::$base->getVar($this->grid_params, 'columns');
		$columns = self::$base->set_basic_colums($columns);

		$columns_advanced = self::$base->get_advanced_colums($this->grid_params);

		$found_rows = 0;
		foreach ($columns_advanced as $adv) {
			if (empty($adv))
				continue;
			$found_rows++;
		}

		if ($found_rows > 0) {
			echo ',' . "\n";
			echo '    rowItemMultiplier: [' . "\n";

			echo '            [';
			echo implode(',', array_map('esc_js', $columns));
			echo ']';

			foreach ($columns_advanced as $adv) {
				if (empty($adv)) continue;

				echo ',' . "\n";
				echo '            [';
				echo implode(',', array_map('esc_js', $adv));
				echo ']';
			}

			echo "\n" . '          ]';
		}
	}

	/**
	 * check if post is visible in grid
	 */
	public function check_if_visible($post_id, $grid_id) {
		$pr_visibility = json_decode(get_post_meta($post_id, 'eg_visibility', true), true);

		$is_visible = true;

		// check if element is visible in grid
		if (!empty($pr_visibility) && is_array($pr_visibility)) {
			foreach ($pr_visibility as $pr_grid => $pr_setting) {
				if ($pr_grid == $grid_id) {
					$is_visible = $pr_setting;
					break;
				}
			}
		}

		return apply_filters('essgrid_check_if_visible', $is_visible, $post_id, $grid_id);
	}

	/**
	 * Output Sorting from current Grid (used for Widgets)
	 * @since 1.0.6
	 */
	public function output_grid_sorting() {
		do_action('essgrid_output_grid_sorting_pre', $this);

		switch ($this->grid_postparams['source-type']) {
			case 'post':
				$this->output_sorting_by_posts();
				break;
			case 'custom':
				$this->output_sorting_by_custom();
				break;
			case 'streams':
				break;
		}

		do_action('essgrid_output_grid_sorting_post', $this);
	}

	/**
	 * Output Sorting from post based
	 * @since 1.0.6
	 */
	public function output_sorting_by_posts() {
		do_action('essgrid_output_sorting_by_posts_pre', $this);
		$this->output_sorting_by_all_types();
		do_action('essgrid_output_sorting_by_posts_post', $this);
	}

	/**
	 * Output Sorting from custom grid
	 * @since 1.0.6
	 */
	public function output_sorting_by_custom() {
		do_action('essgrid_output_sorting_by_custom_pre', $this);
		$this->output_sorting_by_all_types();
		do_action('essgrid_output_sorting_by_custom_post', $this);
	}

	/**
	 * Output Sorting from custom grid
	 * @since 1.0.6
	 */
	public function output_sorting_by_all_types() {
		do_action('essgrid_output_sorting_by_all_types', $this);

		$nav = new Essential_Grid_Navigation();

		$order_by = explode(',', self::$base->getVar($this->grid_params, 'sorting-order-by', 'date'));
		if (!is_array($order_by)) $order_by = [$order_by];

		$order_by_dir = self::$base->getVar($this->grid_params, 'sorting-order-type', 'ASC');
		$order_by_start = $this->_check_meta_sort(self::$base->getVar($this->grid_params, 'sorting-order-by-start', 'none'));

		$nav->set_orders($order_by); //set order of filter
		$nav->set_orders_start($order_by_start); //set order of filter
		$nav->set_orders_order($order_by_dir);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
		echo $nav->output_sorting();
	}

	/**
	 * Output Ajax Container
	 * @since 1.5.0
	 */
	public function output_ajax_container() {
		$container_id = self::$base->getVar($this->grid_params, 'ajax-container-id');
		$container_css = self::$base->getVar($this->grid_params, 'ajax-container-css');
		$container_pre = self::$base->getVar($this->grid_params, 'ajax-container-pre');
		$container_post = self::$base->getVar($this->grid_params, 'ajax-container-post');

		$cont = '<div class="eg-ajax-target-container-wrapper" id="' . esc_attr($container_id) . '">' . "\n";
		$cont .= '  <div class="eg-ajax-target-prefix-wrapper">' . "\n";
		$cont .= html_entity_decode($container_pre);
		$cont .= '  </div>' . "\n";
		$cont .= '  <div class="eg-ajax-target"></div>' . "\n";
		$cont .= '  <div class="eg-ajax-target-sufffix-wrapper">' . "\n";
		$cont .= html_entity_decode($container_post);
		$cont .= '  </div>' . "\n";
		$cont .= '</div>' . "\n";

		if ($container_css !== '' && $container_id !== '') {
			$cont .= '<style type="text/css">' . "\n";
			$cont .= '#' . esc_attr($container_id) . ' {' . "\n";
			$cont .= $container_css;
			$cont .= '}' . "\n";
			$cont .= '</style>';
		}

		$cont = do_shortcode($cont);
		return apply_filters('essgrid_output_ajax_container', $cont, $this);
	}

	/**
	 * Output Inline JS
	 * @since 1.1.0
	 */
	public function add_inline_js() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters( 'essgrid_add_inline_js', self::replace_token( $this->grid_inline_js ) );
	}

	/**
	 * Check the maximum entries that should be loaded
	 * @since: 1.5.3
	 */
	public function get_maximum_entries($grid) {
		$max_entries = intval($grid->get_postparam_by_handle('max_entries', '-1'));

		//2.2
		if (is_admin()) $max_entries = intval($grid->get_postparam_by_handle('max_entries_preview', '-1'));
		if ($max_entries !== -1) return $max_entries;

		$layout = $grid->get_param_by_handle('navigation-layout', []);
		if (isset($layout['pagination']) || isset($layout['left']) || isset($layout['right']))
			return $max_entries;

		$rows_unlimited = $grid->get_param_by_handle('rows-unlimited', 'on');

		$rows = intval($grid->get_param_by_handle('rows', '3'));

		$columns_advanced = $grid->get_param_by_handle('columns-advanced', 'off');

		$columns = $grid->get_param_by_handle('columns', ''); //this is the first line
		$columns = self::$base->set_basic_colums($columns);

		$max_column = 0;
		foreach ($columns as $column) {
			if ($max_column < $column)
				$max_column = $column;
		}

		if ($columns_advanced === 'on') {
			$columns_advanced = self::$base->get_advanced_colums($this->grid_params, $columns);

			$match = array_fill(0, count($columns_advanced), 0);
			for ($i = 0; $i <= $rows; $i++) {
				foreach ($columns_advanced as $col_adv) {
					if (!empty($col_adv)) {
						foreach ($col_adv as $key => $val) {
							$match[$key] += $val;
						}
						$i++;
					}
					if ($i >= $rows)
						break;
				}
			}

			foreach ($match as $highest) {
				if ($max_column < $highest)
					$max_column = $highest;
			}
		}
		
		if ($rows_unlimited === 'off') {
			if ($columns_advanced === 'off') {
				$max_entries = $max_column * $rows;
			} else {
				$max_entries = $max_column;
			}
		}

		return apply_filters('essgrid_get_maximum_entries', $max_entries, $this, $grid);
	}

	/**
	 * Adds functionality for authors to modify things at activation of plugin
	 * @since 1.1.0
	 */
	public static function activation_hooks($networkwide = false)
	{
		//set all starting options
		$options = [];
		$options = apply_filters('essgrid_mod_activation_option', $options);
		if (function_exists('is_multisite') && is_multisite() && $networkwide) { //do for each existing site
			global $wpdb;

			// 2.2.5
			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				foreach ($options as $opt => $val) {
					update_option('tp_eg_' . $opt, $val);
				}
				// 2.2.5
				restore_current_blog();
			}
		} else {
			foreach ($options as $opt => $val) {
				update_option('tp_eg_' . $opt, $val);
			}
		}
		
		Essential_Grid_Db::create_tables();
		Essential_Grid_Item_Skin::propagate_default_item_skins();
		Essential_Grid_Navigation::propagate_default_navigation_skins();
		Essential_Grid_Global_Css::propagate_default_global_css();
		ThemePunch_Fonts::propagate_default_fonts();
	}

	/**
	 * Hide Load More button
	 * @since 2.1.0
	 */
	public function remove_load_more_button($grid_id_wrap)
	{
		$css = '';
		if (self::$base->getVar($this->grid_params, 'load-more-hide', 'off') == 'on' && self::$base->getVar($this->grid_params, 'load-more', 'none') == 'scroll') {
			$css = '<style type="text/css">';
			$css .= '#' . esc_attr($grid_id_wrap) . ' .esg-loadmore { display: none !important; }';
			$css .= '</style>';
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters('essgrid_remove_load_more_button', $css, $grid_id_wrap);
	}

	/**
	 * Adds start height CSS for the Grid, to prevent jumping of Site on loading
	 * @since 2.0.4
	 */
	public function add_start_height_css($grid_id_wrap)
	{
		$columns_advanced = self::$base->getVar($this->grid_params, 'columns-advanced', 'off');
		if ($columns_advanced == 'on') {
			$columns_width = self::$base->getVar($this->grid_params, 'columns-width');
			$columns_height = self::$base->getVar($this->grid_params, 'columns-height');
			$columns_width = self::$base->set_basic_colums_height($columns_width);
			$columns_height = self::$base->set_basic_colums_height($columns_height);

			// 2.2.5
			if (!is_array($columns_width))
				$columns_width = [0, 0, 0, 0, 0, 0];
			if (!is_array($columns_height))
				$columns_height = [0, 0, 0, 0, 0, 0];

			$col_height = array_reverse($columns_height); // reverse to start with the lowest value
			$col_width = array_reverse($columns_width); // reverse to start with the lowest value

			$first = true;

			$css = '<style type="text/css">';
			foreach ($col_height as $key => $height) {
				if ($height > 0) {
					$height = intval($height);
					$mw = intval($col_width[$key] - 1);
					if ($first) { //first set up without restriction of width
						$first = false;
						$css .= '
  #' . esc_attr($grid_id_wrap) . '.eg-startheight{ height: ' . esc_attr($height) . 'px; }';
					} else {
						$css .= '
  @media only screen and (min-width: ' . esc_attr($mw) . 'px) {
  #' . esc_attr($grid_id_wrap) . '.eg-startheight{ height: ' . esc_attr($height) . 'px; }
  }';
					}
				}
			}
			$css .= '</style>';

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $css . "\n";
			if ($css !== '<style type="text/css"></style>')
				return true;
		}

		return false;
	}

	/**
	 * Does the uninstallation process, also multisite checks
	 * @since 1.5.0
	 */
	public static function uninstall_plugin($networkwide = false)
	{
		global $wpdb;
		
		// If uninstall not called from WordPress, then exit
		if (!defined('WP_UNINSTALL_PLUGIN')) {
			exit;
		}
		if (function_exists('is_multisite') && is_multisite() && $networkwide) { //do for each existing site
			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				self::_uninstall_plugin();
				// 2.2.5
				restore_current_blog();
			}
		} else {
			self::_uninstall_plugin();
		}
	}

	/**
	 * Does the uninstallation process
	 * @since 1.5.0
	 * @moved from uninstall.php
	 */
	public static function _uninstall_plugin()
	{
		// If uninstall not called from WordPress, then exit
		if (!defined('WP_UNINSTALL_PLUGIN')) {
			exit;
		}

		//Delete Database Tables
		Essential_Grid_Db::get_entity('grids')->drop();
		Essential_Grid_Db::get_entity('skins')->drop();
		Essential_Grid_Db::get_entity('elements')->drop();
		Essential_Grid_Db::get_entity('nav_skins')->drop();

		//Delete Options
		delete_option('tp_eg_role');
		delete_option('tp_eg_grids_version');
		delete_option('tp_eg_custom_css');

		delete_option('tp_eg_output_protection');
		delete_option('tp_eg_tooltips');
		delete_option('tp_eg_js_to_footer');
		delete_option('tp_eg_use_cache');
		delete_option('tp_eg_query_type');
		delete_option('tp_eg_show_stream_failure_msg');
		delete_option('tp_eg_stream_failure_custom_msg');
		delete_option('tp_eg_use_lightbox');
		delete_option('tp_eg_use_crossorigin');
		delete_option('tp_eg_overview_show_grid_info');
		delete_option('tp_eg_frontend_nonce_check');
		delete_option('tp_eg_post_date');

		delete_option('tp_eg_update-check');
		delete_option('tp_eg_update-check-short');
		delete_option('tp_eg_latest-version');
		delete_option('tp_eg_code');
		delete_option('tp_eg_valid');
		delete_option('tp_eg_valid-notice');

		delete_option('esg-widget-areas');
		delete_option('esg-custom-meta');
		delete_option('esg-custom-link-meta');
		delete_option('esg-search-settings');

		delete_option('tp_eg_custom_css_imported');

		do_action('essgrid__uninstall_plugin');

	}

	/** 
	 * get lightbox post content for ajax action "load_post_content"
	 * 
	 * @return string
	 */
	public static function on_lightbox_post_content($settings, $id)
	{
		global $rs_loaded_by_editor;

		$content = '';
		$raw_content = '';
		
		if ( empty( $settings ) ) return $content;

		$rs_loaded_by_editor = true;
		
		$featured = (isset($settings['featured'])) ? $settings['featured'] : '';
		$titl = (isset($settings['titl'])) ? $settings['titl'] : '';
		$lbTitle = (isset($settings['lbTitle'])) ? $settings['lbTitle'] : '';
		$lbTag = (isset($settings['lbTag'])) ? $settings['lbTag'] : '';
		$lbImg = (isset($settings['lbImg'])) ? $settings['lbImg'] : '';

		$wid = (isset($settings['lbWidth'])) ? $settings['lbWidth'] : '';
		$lbPos = (isset($settings['lbPos'])) ? $settings['lbPos'] : '';

		$minW = (isset($settings['lbMin'])) ? $settings['lbMin'] : '';
		$maxW = (isset($settings['lbMax'])) ? $settings['lbMax'] : '';

		$margin = (isset($settings['margin'])) ? $settings['margin'] : '';
		$margin = explode('|', $margin);

		$padding = (isset($settings['padding'])) ? $settings['padding'] : '';
		$padding = explode('|', $padding);

		if (!empty($margin) && count($margin) === 4) {
			$margin = intval($margin[0]) . 'px ' . intval($margin[1]) . 'px ' . intval($margin[2]) . 'px ' . intval($margin[3]) . 'px';
		} else {
			$margin = '0';
		}

		if (!empty($padding) && count($padding) === 4) {
			$padding = intval($padding[0]) . 'px ' . intval($padding[1]) . 'px ' . intval($padding[2]) . 'px ' . intval($padding[3]) . 'px';
		} else {
			$padding = '0';
		}

		$html = '<div class="eg-lightbox-post-content" style="width: ' . esc_attr($maxW) . ';min-width: ' . esc_attr($minW) . '; max-width: ' . esc_attr($maxW) . '; margin: ' . esc_attr($margin) . '">' . '<div class="eg-lightbox-post-content-inner" style="padding: ' . esc_attr($padding) . '">';

		if ( !empty($settings['revslider']) && class_exists('RevSliderSlider') ) {
			$id = intval( $settings['revslider'] );

			ob_start();
			
			global $SR_GLOBALS;
			if ( $SR_GLOBALS['front_version'] === 6 ){
				$slider = new RevSliderOutput();
				$slider->set_ajax_loaded();
				$slider_class = $slider->add_slider_to_stage($id, '', '', '', '');
			} else {
				$slider = new RevSlider7Output();
				$slider->set_ajax_loaded();
				$slider_class = $slider->add_slider_to_stage($id);
				echo '<script>var sr_id = ' . wp_json_encode($slider->get_html_id()) . ';
if (!SR7.initialised) { SR7.F.init(); } else { SR7.F.module.register( document.getElementById(sr_id), sr_id ); }
jQuery(document).one("afterClose.egbx", () => {
	var apiID = "revapi" + ( sr_id.includes("SR7_") ? sr_id.split("_")[1] : sr_id );
	window[apiID] && window[apiID].kill();
	setTimeout(() => delete SR7.M[sr_id], 19);
});
</script>';
			}

			$content = ob_get_clean();

			if ( !empty($slider_class) && $content !== '' ) {
				return $html . '<div class="esg-overflow-hidden">' .$content . '</div></div></div>';
			}
		} else if (!empty($settings['essgrid'])) {
			$content = do_shortcode('[ess_grid alias="' . $settings['essgrid'] . '"][/ess_grid]');
			if ($content) {
				return $html . $content . '</div></div>';
			}
		} else {
			if (!empty($settings['ispost']) && $id > 0) {
				$post = get_post($id);
				if ( $post && empty($post->post_password) && 'publish' === $post->post_status ) {
					$raw_content = $post->post_content;
				} else {
					// throw access error?
					return '';
				}
			} else {
				$gridid = isset($settings['gridid']) ? $settings['gridid'] : false;
				if (is_numeric($gridid)) {
					$grid = new Essential_Grid();
					$result = $grid->init_by_id($gridid);
					if ($result) {
						$itm = $grid->get_layer_values();
						if (!empty($itm) && isset($itm[$id])) {
							$itm = $itm[$id];
							$raw_content = !empty($itm['content']) ? $itm['content'] : '';
						}
					}
				}
			}

			if (!is_wp_error($raw_content)) {
				$content = apply_filters('essgrid_the_content', $raw_content);
				if (method_exists('WPBMap', 'addAllMappedShortcodes')) {
					WPBMap::addAllMappedShortcodes();
				}
				
				$rs_loaded_by_editor = true;
				$content = do_shortcode($content);
				$rs_loaded_by_editor = false;
			}
		}

		if (!empty($titl) && $lbTitle === 'on') {
			if (empty($lbTag))
				$lbTag = 'h2';
			$titl = '<' . esc_attr($lbTag) . '>' . esc_html($titl) . '</' . esc_attr($lbTag) . '>';
		} else {
			$titl = '';
		}

		if (!empty($featured) && $lbImg === 'on') {
			$margin = $settings['lbMargin'];
			$margin = explode('|', $margin);
			if (!empty($margin) && count($margin) === 4) {
				$margin = esc_attr($margin[0]) . 'px ' . esc_attr($margin[1]) . 'px ' . esc_attr($margin[2]) . 'px ' . esc_attr($margin[3]) . 'px';
			} else {
				$margin = '0';
			}

			if (!is_numeric($wid)) $wid = 50;
			$wid = intval($wid);

			$dif = 100 - $wid;
			$dif = 'width: ' . esc_attr($dif) . '%';
			$wid = 'width: ' . esc_attr($wid) . '%';
			$featured = self::$base::get_image_tag(
				$featured,
				[
					'class' => 'esg-post-featured-img',
					'style' => 'width: 100%; height: auto; padding: ' . $margin,
				]
			);
			

			switch ($lbPos) {

				case 'top':
					$html .= $featured . $titl . $content;
					break;

				case 'left':
					$html .= '<div class="esg-f-left" style="' . esc_attr($wid) . '">' . $featured . '</div>';
					$html .= '<div class="esg-f-left" style="' . esc_attr($dif) . '">' . $titl . $content . '</div>';
					$html .= '<div class="esg-clearfix" ></div>';
					break;

				case 'right':
					$html .= '<div class="esg-f-left" style="' . esc_attr($dif) . '">' . $titl . $content . '</div>';
					$html .= '<div class="esg-f-left" style="' . esc_attr($wid) . '">' . $featured . '</div>';
					$html .= '<div class="esg-clearfix" ></div>';
					break;

				case 'bottom':
					$html .= $titl . $content . $featured;
					break;

			}

		} else {
			$html .= $titl . $content;
		}
		
		return $html . '</div></div>';
	}

	/**
	 * action fired before actually processing any ajax actions
	 * @return void 
	 */
	public function before_front_ajax_action()
	{
	}

	/**
	 * Handle Ajax Requests
	 */
	public static function on_front_ajax_action()
	{
		do_action('essgrid_before_front_ajax_action');
		
		$error = false;
		$token = self::$base->getRequestVar('token', false);
		$action = self::$base->getRequestVar('client_action', false);
		$data = self::$base->getPostVar('data', false);

		$frontend_nonce_check = get_option( 'tp_eg_frontend_nonce_check', 'true' );
		$isVerified = wp_verify_nonce( $token, 'Essential_Grid_Front' );
		if ( 'true' != $frontend_nonce_check || ( 'true' == $frontend_nonce_check && $isVerified ) ) {
			
			switch ($action) {

				case 'load_more_items':
					$gridid = self::$base->getPostVar('gridid', 0, 'i');
					if (!empty($data) && $gridid > 0) {
						$grid = new Essential_Grid();

						$result = $grid->init_by_id($gridid);
						if (!$result) {
							$error = esc_attr__('Grid not found', 'essential-grid');
						} else {
							// set to only load choosen items
							$grid->set_loading_ids($data); 

							//check if we are custom grid
							if ($grid->is_custom_grid()) {
								$html = $grid->output_by_specific_ids();
							} elseif ($grid->is_stream_grid()) {
								$html = $grid->output_by_specific_stream();
							} else {
								$html = $grid->output_by_specific_posts();
							}

							/* 2.1.5 */
							if (!empty($html)) {
								self::ajaxResponseData($html);
							} else {
								/* 2.1.5 */
								$customGallery = self::$base->getPostVar('customgallery', false);
								if (!empty($customGallery)) {
									$grid->custom_images = $data;
									$html = $grid->output_by_gallery(false, true, true);
								}
								if (!empty($html)) {
									self::ajaxResponseData($html);
								} else {
									$error = esc_attr__('Items Not Found', 'essential-grid');
								}
							}
						}
					} else {
						$error = esc_attr__('No Data Received', 'essential-grid');
					}
					break;
					
				case 'load_more_content':
					$postid = self::$base->getPostVar('postid', 0, 'i');
					if ($postid > 0) {
						$post = get_post($postid);
						if ( $post && empty($post->post_password) && 'publish' === $post->post_status ) {
							//filter apply for qTranslate and other
							$content = apply_filters('essgrid_the_content', $post->post_content);
							if (method_exists('WPBMap', 'addAllMappedShortcodes')) {
								WPBMap::addAllMappedShortcodes();
							}
							$content = do_shortcode($content);

							if (strpos($content, '<rs-module-wrap') !== false && class_exists('RevSliderFront')) {
								ob_start();
								RevSliderFront::add_inline_js();
								$content .= ob_get_clean();
							}

							self::ajaxResponseData($content);
						}
					}
					$error = esc_attr__('Post Not Found', 'essential-grid');
					break;

				case 'load_post_content':
					$postid = self::$base->getGetVar('postid', 0, 'i');
					$settings = self::$base->getGetVar('settings', false);
					if ( !empty( $settings ) ) {
						$settings = json_decode( stripslashes( $settings ), true );
						if ( !empty( $settings ) ) {
							foreach ( $settings as $name => $setting ) {
								$settings[ $name ] = esc_attr( $setting );
							}
							$settings['lbTag'] = sanitize_key( $settings['lbTag'] );
							if ( ! in_array( $settings['lbTag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ] ) ) {
								$settings['lbTag'] = 'h2';
							}
						}
					}
					
					$content = apply_filters('essgrid_lightbox_post_content', $settings, $postid);
					
					if ( !empty($content) ) {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $content;
						die();
					}
					
					$error = esc_attr__('Post Not Found', 'essential-grid');
					
					break;
					
				case 'get_search_results':
					$search_string = self::$base->getVar($data, 'search');
					$search_skin = self::$base->getVar($data, 'skin', 0, 'i');
					if ($search_string !== '' && $search_skin > 0) {
						$search = new Essential_Grid_Search();
						$return = $search->output_search_result($search_string, $search_skin);
						self::ajaxResponseData($return);
					}
					$error = esc_attr__( 'Not found', 'essential-grid' );
					break;
					
				case 'get_grid_search_ids':
					$return = Essential_Grid_Search::search_grid_items_ids(
						self::$base->getVar($data, 'search'),
						self::$base->getVar($data, 'id', 0, 'i')
					);
					if ( ! empty( $return ) ) {
						self::ajaxResponseSuccess( '', $return );
					}
					$error = esc_attr__( 'Not found', 'essential-grid' );
					break;
					
			}

			$error = apply_filters('essgrid_on_front_ajax_action', $error, $data);
		} else {
			$error = esc_attr__('Request not verified', 'essential-grid');
		}

		if ($error !== false) {
			$showError = esc_attr__('Loading Error', 'essential-grid');
			if ($error !== true) $showError = $error;
			self::ajaxResponseError($showError, false);
		}
		exit();
	}

	/**
	 * echo json ajax response
	 */
	public static function ajaxResponse($success, $message, $arrData = null)
	{
		$response = [];
		$response["success"] = $success;
		$response["message"] = $message;
		if (!empty($arrData)) {
			if (gettype($arrData) == "string" || gettype($arrData) == "boolean") $arrData = ["data" => $arrData];
			$response = array_merge($response, $arrData);
		}
		echo wp_json_encode($response);
		exit();
	}

	/**
	 * echo json ajax response, without message, only data
	 */
	public static function ajaxResponseData($arrData)
	{
		if (gettype($arrData) == "string") $arrData = ["data" => $arrData];
		self::ajaxResponse(true, "", $arrData);
	}

	/**
	 * echo json ajax response
	 */
	public static function ajaxResponseError($message, $arrData = null)
	{
		self::ajaxResponse(false, $message, $arrData);
	}

	/**
	 * echo ajax success response
	 */
	public static function ajaxResponseSuccess($message, $arrData = null)
	{
		self::ajaxResponse(true, $message, $arrData);
	}

	/**
	 * echo ajax success response
	 */
	public static function ajaxResponseSuccessRedirect($message, $url)
	{
		$arrData = [
			"is_redirect" => true,
			"redirect_url" => $url
		];
		self::ajaxResponse(true, $message, $arrData);
	}

	public static function custom_sorter_int($x, $y)
	{
		if (!isset($x[self::$sort_handle])) $x[self::$sort_handle] = 0;
		if (!isset($y[self::$sort_handle])) $y[self::$sort_handle] = 0;
		if (in_array(self::$sort_handle, ['date_modified', 'date', 'modified'])) {
			$x[self::$sort_handle] = strtotime($x[self::$sort_handle]);
			$y[self::$sort_handle] = strtotime($y[self::$sort_handle]);
		} elseif ( self::$sort_handle == 'duration') {
			$x[self::$sort_handle] = Essential_Grid::time_to_seconds($x[self::$sort_handle]);
			$y[self::$sort_handle] = Essential_Grid::time_to_seconds($y[self::$sort_handle]);
		}

		if (!is_numeric($x[self::$sort_handle])) $x[self::$sort_handle] = intval(preg_replace("/[^0-9]/", "", $x[self::$sort_handle]));
		if (!is_numeric($y[self::$sort_handle])) $y[self::$sort_handle] = intval(preg_replace("/[^0-9]/", "", $y[self::$sort_handle]));

		if ( self::$sort_direction == 'ASC') {
			return $x[self::$sort_handle] - $y[self::$sort_handle];
		} else {
			return $y[self::$sort_handle] - $x[self::$sort_handle];
		}
	}

	public static function time_to_seconds($time_string)
	{
		$timeArr = array_reverse(explode(":", $time_string));
		$seconds = 0;
		foreach ($timeArr as $key => $value) {
			if ($key > 2) break;
			$seconds += pow(60, $key) * $value;
		}
		return $seconds;
	}

	public static function custom_sorter($x, $y)
	{
		$sort_x = !isset($x[self::$sort_handle]) ? '' : sanitize_title($x[self::$sort_handle]);
		$sort_y = !isset($y[self::$sort_handle]) ? '' : sanitize_title($y[self::$sort_handle]);

		if ( self::$sort_direction == 'ASC') {
			return strcasecmp($sort_x, $sort_y);
		} else {
			return strcasecmp($sort_y, $sort_x);
		}
	}

	public function set_custom_sorter($handle, $direction)
	{
		self::$sort_direction = $direction;
		self::$sort_handle    = $handle;
	}

	public function order_by_custom($order_by_start, $order_by_dir)
	{
		if (!empty($order_by_start) && !empty($this->grid_layers)) {
			if (is_array($order_by_start)) {
				$order_by_start = $order_by_start[0];
			}

			switch ($order_by_start) {
				case 'rand':
					$this->grid_layers = self::$base->shuffle_assoc($this->grid_layers);
					break;
				case 'title':
				case 'post_url':
				case 'excerpt':
				case 'meta':
				case 'alias':
				case 'name':
				case 'content':
				case 'author_name':
				case 'author':
				case 'cat_list':
				case 'tag_list':
					if ($order_by_start == 'name')
						$order_by_start = 'alias';
					if ($order_by_start == 'author')
						$order_by_start = 'author_name';
					//check if values are existing and if not, add them to the layers
					$this->set_custom_sorter($order_by_start, $order_by_dir);
					usort($this->grid_layers, [
						'Essential_Grid',
						'custom_sorter'
					]);
					break;
				case 'post_id':
				case 'ID':
				case 'num_comments':
				case 'comment_count':
				case 'date':
				case 'modified':
				case 'date_modified':
				case 'views':
				case 'likes':
				case 'dislikes':
				case 'retweets':
				case 'favorites':
				case 'itemCount':
				case 'duration':
					if ($order_by_start == 'comment_count')
						$order_by_start = 'num_comments';
					if ($order_by_start == 'modified')
						$order_by_start = 'date_modified';
					if ($order_by_start == 'ID')
						$order_by_start = 'post_id';

					$this->set_custom_sorter($order_by_start, $order_by_dir);
					usort($this->grid_layers, [
						'Essential_Grid',
						'custom_sorter_int'
					]);
					break;
			}
		}
	}

	/**
	 * Ajax Call to save Post Like
	 *
	 * @since   2.2
	 */
	public function ess_grid_post_like()
	{
		// Check for nonce security
		$nonce = !empty($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (!wp_verify_nonce($nonce, 'eg-ajax-nonce')) die('Busted!');

		if (isset($_POST['post_like'])) {
			// Retrieve user IP address
			$ip = !empty($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
			$post_id = !empty($_POST['post_id']) ? sanitize_text_field(wp_unslash($_POST['post_id'])) : '';

			// Get voters'IPs for the current post
			$meta_IP = get_post_meta($post_id, "eg_voted_IP");
			$voted_IP = $meta_IP[0];
			if (!is_array($voted_IP)) $voted_IP = [];

			// Get votes count for the current post
			$meta_count = get_post_meta($post_id, "eg_votes_count", true);

			// Use has already voted ?
			if (!$this->hasAlreadyVoted($post_id)) {
				$voted_IP[$ip] = time();

				// Save IP and increase votes count
				update_post_meta($post_id, "eg_voted_IP", $voted_IP);
				update_post_meta($post_id, "eg_votes_count", ++$meta_count);

				// Display count (ie jQuery return value)
				echo esc_html($meta_count);
			} else
				esc_html_e("already", 'essential-grid');
		}
		exit;
	}

	/**
	 * Check if Post was already voted for
	 *
	 * @since   2.2
	 */
	public function hasAlreadyVoted($post_id)
	{
		$timebeforerevote = get_option('tp_eg_post_like_ip_lockout', '');
		if (empty($timebeforerevote))
			return false;

		// Retrieve post votes IPs
		$meta_IP = get_post_meta($post_id, "eg_voted_IP");
		$voted_IP = $meta_IP[0];
		if (!is_array($voted_IP)) $voted_IP = [];

		// Retrieve current user IP
		$ip = !empty($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';

		// If user has already voted
		if (in_array($ip, array_keys($voted_IP))) {
			$time = $voted_IP[$ip];
			$now = time();

			// Compare between current time and vote time
			if (round(($now - $time) / 60) > $timebeforerevote) return false;

			return true;
		}

		return false;
	}

	public static function post_thumbnail_replace($html, $post_id, $post_thumbnail_id, $size, $attr)
	{
		$post_grid_id = get_post_meta($post_id, 'eg_featured_grid', true);
		if (!empty($post_grid_id)) $html = do_shortcode('[ess_grid alias="' . $post_grid_id . '"][/ess_grid]');
		
		return $html;
	}

	/**
	 * Improve UX for empty grids or when social stream data isn't available
	 * @since: 3.0.12
	 */
	private function display_grid_error_msg($fromStream = false, $sourceType = false, $custom_msg = '')
	{
		if (!empty($custom_msg)) {
			echo wp_kses_post($custom_msg);
			return;
		}
		
		if (!is_admin()) {
			$show_stream_failure_msg = get_option('tp_eg_show_stream_failure_msg', 'true');
			if (is_user_logged_in()) {
				if ($show_stream_failure_msg == 'true') {
					echo '<p><span class="esg-font-italic">';
					esc_html_e('No grid items found', 'essential-grid');
					echo '</span><br><a href="' . esc_url(admin_url() . 'admin.php?page=' . ESG_PLUGIN_SLUG . '&view=grid-create&create=' . $this->grid_id . '&sourcetab=true') . '">';
					esc_html_e('Review source settings', 'essential-grid');
					echo '</a></p>';
					return;
				}
			}
			if ($show_stream_failure_msg === 'custom') {
				$stream_failure_custom_msg = get_option('tp_eg_stream_failure_custom_msg', '');
				echo wp_kses_post( $stream_failure_custom_msg );
			}
		} else {
			echo '<div>';
			if ($sourceType) {
				esc_html_e('Grid "Source type" is empty! It might be connected to missing addons ( Like Social Media, Real Media Library or NextGen )', 'essential-grid');
				echo '<br><br>';
				echo '<a id="go-to-source" class="esg-btn esg-purple" href="#">';
				esc_html_e('Edit Source Settings', 'essential-grid');
				echo '</a> ';
				
			} elseif (!$fromStream) {
				esc_html_e('No posts found for this Grid.', 'essential-grid');
				echo '<br><br>';
				echo '<a id="go-to-source" class="esg-btn esg-purple" href="#">';
				esc_html_e('Edit Source Settings', 'essential-grid');
				echo '</a> <a class="esg-btn esg-purple" href="' . esc_url(admin_url()) . 'edit.php" target="_blank">Create Posts</a>';
			} else {
				$stream_err = apply_filters('essgrid_display_grid_error_msg_stream', '');
				if ( !empty($stream_err) ) {
					echo wp_kses_post( $stream_err );
					echo '<br/>';
				}
				
				esc_html_e('Please check stream source type settings.', 'essential-grid');
				echo '<br/><br/><a id="go-to-source" class="esg-btn esg-purple" href="#">';
				esc_html_e('Check Settings', 'essential-grid');
				echo '</a>';
			}
			echo '</div>';
		}
	}

	/**
	 * check if lightbox need additions
	 * 
	 * @param string $lightbox_mode
	 * @param string $lightbox_exclude_original_media
	 * @param Essential_Grid_Item_Skin $item_skin
	 * @param array $post
	 * @return void
	 */
	private function _is_lightbox_additions($lightbox_mode, $lightbox_exclude_original_media, $item_skin, $post)
	{
		if ($lightbox_mode == 'content' || $lightbox_mode == 'content-gallery' || $lightbox_mode == 'woocommerce-gallery') {
			$lb_add_images = [];
			$lb_add_images_thumb = [];
			switch ($lightbox_mode) {
				case 'content':
					$lb_add_images = self::$base->get_all_content_images($post['ID']);
					$lb_add_images_thumb = self::$base->get_all_content_images($post['ID'], false, 'thumbnail');
					break;
				case 'content-gallery':
					$lb_add_images = self::$base->get_all_gallery_images($post['post_content'], true);
					$lb_add_images_thumb = self::$base->get_all_gallery_images($post['post_content'], true, 'thumbnail');
					break;
				case 'woocommerce-gallery':
					if (Essential_Grid_Woocommerce::is_woo_exists()) {
						$lb_add_images = Essential_Grid_Woocommerce::get_image_attachements($post['ID'], true);
						$lb_add_images_thumb = Essential_Grid_Woocommerce::get_image_attachements($post['ID'], true, 'thumbnail');
					}
					break;
			}

			$item_skin->set_lightbox_addition([
				'items' => $lb_add_images,
				'thumbs' => $lb_add_images_thumb,
				'base' => $lightbox_exclude_original_media
			]);
		}
	}

	/**
	 * add esg ajax action to divi actions list to load builder and process divi shortcodes
	 * 
	 * @param array $actions
	 * @return array
	 */
	public function add_divi_support($actions)
	{
		if (empty($actions['action'])) {
			$actions['action'] = [];
		}
		
		$actions['action'][] = 'Essential_Grid_Front_request_ajax';
		
		return $actions;
	}

	/**
	 * add admin menu points in ToolBar Top
	 * @return void
	 */
	public function add_admin_bar()
	{
		if (is_admin() || !is_super_admin() || !is_admin_bar_showing()) return;
		?>
		<script>
			function esg_adminBarToolBarTopFunction() {
				if(jQuery('#wp-admin-bar-esg-default').length > 0 && jQuery('.esg-grid-wrap-container').length > 0){
					var aliases = [];
					jQuery('.esg-grid-wrap-container').each(function(){
						aliases.push(jQuery(this).data('alias'));
					});

					if(aliases.length > 0){
						jQuery('#wp-admin-bar-esg-default li').each(function(){
							var li = jQuery(this),
								t = li.find('.ab-item .esg-label').data('alias'); //text()
							t = t!==undefined && t!==null ? t.trim() : t;
							if(jQuery.inArray(t,aliases)!=-1){
							}else{
								li.remove();
							}
						});
					}
				}else{
					jQuery('#wp-admin-bar-esg').remove();
				}
			}
			
			var esg_adminBarLoaded_once = false;
			if (document.readyState === "loading")
				document.addEventListener('readystatechange',function(){
					if ((document.readyState === "interactive" || document.readyState === "complete") && !esg_adminBarLoaded_once) {
						esg_adminBarLoaded_once = true;
						esg_adminBarToolBarTopFunction()
					}
				});
			else {
				esg_adminBarLoaded_once = true;
				esg_adminBarToolBarTopFunction();
			}
		</script>
		<?php
	}

	/**
	 * add admin nodes
	 * @return void
	 */
	public function add_admin_menu_nodes()
	{
		if(is_admin() || !is_super_admin() || !is_admin_bar_showing()){
			return;
		}

		$this->_add_node(
			'<span class="esg-label">Essential Grid</span>',
			false,
			admin_url('admin.php?page=essential-grid'),
			['class' => 'esg-menu'],
			'esg'
		);

		//add all nodes of all grids
		$arrGrids = Essential_Grid_Db::get_entity('grids')->get_grids();
		if (empty($arrGrids)) return;
		
		foreach ($arrGrids as $grid){
			$this->_add_node(
				'<span class="esg-label" data-alias="' . esc_attr($grid->handle) . '">' . esc_html($grid->name) . '</span>',
				'esg',
				admin_url('admin.php?page=essential-grid&view=grid-create&create=' . $grid->id),
				['class' => 'esg-sub-menu'],
				esc_attr($grid->handle)
			);
		}
	}

	/**
	 * add admin node
	 * @return void
	 */
	protected function _add_node($title, $parent = false, $href = '', $custom_meta = [], $id = '')
	{
		global $wp_admin_bar;
		
		if (is_admin() || !is_super_admin() || !is_admin_bar_showing()) return;

		$id = ($id == '') ? strtolower(str_replace(' ', '-', $title)) : $id;
		$meta = (strpos($href, site_url()) !== false) ? [] : ['target' => '_blank'];
		$meta = array_merge($meta, $custom_meta);
		
		$wp_admin_bar->add_node(['parent' => $parent, 'id' => $id, 'title' => $title, 'href' => $href, 'meta' => $meta]);
	}

	/**
	 * @param array $images
	 * @param int $post_id
	 * @return array
	 */
	public function wpseo_sitemap_urlimages($images, $post_id)
	{
		if ( !has_action('essgrid_get_media_html', [$this, 'get_grid_images']) )
			add_action('essgrid_get_media_html', [$this, 'get_grid_images'], 10, 2);
		
		$content = get_post_field('post_content', $post_id);
		if (!has_shortcode($content, 'ess_grid')) return $images;

		preg_match_all( '/' . get_shortcode_regex() . '/', $content, $matches, PREG_SET_ORDER );
		
		foreach ($matches as $m) {
			if ($m[2] != 'ess_grid') continue;
			
			$this->grid_images = [];
			self::register_shortcode(shortcode_parse_atts($m[3]));
			$images = array_merge($images, $this->grid_images);
		}
		
		return $images;
	}

	/**
	 * @param string $img
	 * @param int $grid_id
	 * @return void
	 */
	public function get_grid_images($img, $grid_id)
	{
		// skip external
		if ( strpos($img, get_bloginfo('url')) !== 0 ) return;

		$add = ['src' => $img, 'title' => ''];
		
		$media_id = attachment_url_to_postid($img);
		if ($media_id) {
			$media_info =  self::$base::get_attachment_info($media_id);
			$add['title'] = !empty($media_info['title']) ? $media_info['title'] : $media_info['alt'];
		}
		
		$this->grid_images[] = $add;
	}

	/**
	 * Get all Grids in Database
	 * @deprecated 3.1.3
	 */
	public static function get_essential_grids($order = false, $raw = true)
	{
		_deprecated_function( __METHOD__, '3.1.3', "Essential_Grid_Db::get_entity('grids')->get_grids()" );
		return Essential_Grid_Db::get_entity('grids')->get_grids($order, $raw);
	}

	/**
	 * Get grid by ID
	 * @deprecated 3.1.3
	 */
	public static function get_essential_grid_by_id($id = 0, $raw = false)
	{
		_deprecated_function( __METHOD__, '3.1.3', "Essential_Grid_Db::get_entity('grids')->get_grid_by_id()" );
		return Essential_Grid_Db::get_entity('grids')->get_grid_by_id($id, $raw);
	}

	/**
	 * Get grid by handle
	 * @deprecated 3.1.3
	 */
	public static function get_essential_grid_by_handle($handle = '', $raw = false)
	{
		_deprecated_function( __METHOD__, '3.1.3', "Essential_Grid_Db::get_entity('grids')->get_grid_by_handle()" );
		return Essential_Grid_Db::get_entity('grids')->get_grid_by_handle($handle, $raw);
	}

}
