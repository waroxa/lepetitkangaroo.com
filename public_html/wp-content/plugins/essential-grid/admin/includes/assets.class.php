<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Load admin stylesheets and JavaScript.
 */
class Essential_Grid_Assets {

	/**
	 * Instance of this class.
	 * @var null|object
	 */
	protected static $instance = null;

	/**
	 * @var Essential_Grid_Builders
	 */
	protected $builders;

	/**
	 * Return an instance of this class.
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		
		$this->builders = Essential_Grid_Builders::get_instance();

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ], 20 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 20 );

		add_action( 'admin_head', [ $this, 'add_tinymce_editor' ] );
		add_action( 'admin_head', [ $this, 'add_header_data' ] );
	}

	/**
	 * enqueue admin styles
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		ThemePunch_Fonts::register_icon_fonts();

		$this->add_global_fonts();
		$this->add_global_styles();

		if ( $this->should_include_screen_assets() ) {
			$this->add_screen_fonts();
			$this->add_screen_styles();
		}

		$screens = Essential_Grid_Admin::get_instance()->get_screens();
		do_action( 'essgrid_enqueue_admin_styles', $screens );
	}

	/**
	 * enqueue admin scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( $this->should_include_screen_assets() ) {
			$this->add_screen_scripts();
		}

		do_action( 'essgrid_enqueue_admin_scripts' );
	}

	/**
	 * Add interface for custom shortcodes to tinymce
	 *
	 * @return void
	 */
	public function add_tinymce_editor() {
		do_action( 'essgrid_add_tinymce_editor' );

		// check user permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( ! $this->check_post_type() ) {
			return;
		}

		// check if WYSIWYG is enabled
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_action( 'before_wp_tiny_mce', [ $this, 'add_tinymce_lang' ] );
			add_filter( 'mce_buttons', [ $this, 'add_tinymce_shortcode_editor_button' ], 10, 1 );
		}

		add_action( 'in_admin_footer', [ 'Essential_Grid_Dialogs', 'essgrid_add_shortcode_builder' ] );
	}

	/**
	 * add ESG_LANG before tinyMCE js init
	 *
	 * @return void
	 */
	public function add_tinymce_lang() {
		echo '<script type="text/javascript">';
		echo 'var ESG_LANG = ' . wp_json_encode( Essential_Grid_Base::get_javascript_multilanguage() );
		echo '</script>';
	}

	/**
	 * Add button to tinymce
	 *
	 * @param array $buttons
	 *
	 * @return array
	 */
	public static function add_tinymce_shortcode_editor_button( $buttons ) {
		$buttons[] = "essgrid_sc_button";

		return apply_filters( 'essgrid_add_tinymce_shortcode_editor_button', $buttons );
	}

	/**
	 * Add ESG JS object to header
	 *
	 * @return void
	 */
	public function add_header_data() {
		if ( ! $this->should_include_screen_assets() ) {
			return;
		}
		?>
		<script type="text/javascript">
			window.ESG ??= {};
			window.ESG.F ??= {};
			window.ESG.C ??= {};
			window.ESG.E ??= {};
			window.ESG.LIB ??= {};
			window.ESG.V ??= {};
			window.ESG.S ??= {};
			window.ESG.DOC ??= jQuery(document);
			window.ESG.WIN ??= jQuery(window);
			window.ESG.E.plugin_url ??= "<?php echo esc_url( ESG_PLUGIN_URL ); ?>";
			ESG.LIB.COLOR_PRESETS = <?php echo wp_json_encode( ESGColorpicker::get_color_presets() ); ?>;
		</script>
		<?php
	}

	/**
	 * get wp version without postfix like -beta etc
	 *
	 * @return string
	 */
	protected function get_wp_version() {
		global $wp_version;

		if ( strpos( $wp_version, '-' ) !== false ) {
			$_wpver     = explode( '-', $wp_version );
			$wp_version = $_wpver[0];
		}

		return $wp_version;
	}

	/**
	 * check if we should include assets for current page
	 *
	 * @return bool
	 */
	protected function should_include_screen_assets() {
		return $this->check_screens()
			   || $this->check_post_type()
			   || $this->builders->check_pagenow();
	}

	/**
	 * check if current screen belong to ESG
	 *
	 * @return bool
	 */
	protected function check_screens() {
		$screens = Essential_Grid_Admin::get_instance()->get_screens();
		$screen  = get_current_screen();

		return in_array( $screen->id, $screens );
	}

	/**
	 * check if current page process post type
	 *
	 * @return bool
	 */
	protected function check_post_type() {
		global $typenow;

		$post_types = get_post_types();
		if ( ! is_array( $post_types ) ) {
			$post_types = [ 'post', 'page' ];
		}

		return in_array( $typenow, $post_types );
	}

	

	/**
	 * enqueue global styles
	 *
	 * @return void
	 */
	public function add_global_styles() {
		wp_register_style( 'esg-tp-boxextcss', ESG_PLUGIN_URL . 'public/assets/css/jquery.esgbox.min.css', [], ESG_REVISION );
		wp_enqueue_style( 'esg-tp-boxextcss' );

		wp_enqueue_style( 'esg-plugin-settings', ESG_PLUGIN_URL . 'public/assets/css/settings.css', [], ESG_REVISION );
		wp_enqueue_style( 'esg-global-styles', ESG_PLUGIN_URL . 'admin/assets/css/esg-global.css', [], ESG_REVISION );
	}

	/**
	 * enqueue global fonts
	 *
	 * @return void
	 */
	public function add_global_fonts() {
		$google_fonts_admin = [
			'Roboto'         => [
				'css' => ESG_PLUGIN_URL . 'public/assets/font/roboto/roboto.css',
				'url' => 'Roboto:300,400,500,700,900',
			],
			'Open Sans'      => [
				'css' => ESG_PLUGIN_URL . 'public/assets/font/opensans/opensans.css',
				'url' => 'Open+Sans:300,400,600,700,800',
			],
			'Material Icons' => [
				'css' => ESG_PLUGIN_URL . 'public/assets/font/material/material-icons.css',
				'url' => 'Material+Icons',
			],
		];
		$google_fonts_admin = apply_filters( 'essgrid_enqueue_admin_styles_google_fonts', $google_fonts_admin );
		foreach ( $google_fonts_admin as $f => $data ) {
			wp_enqueue_style( 'tp-' . sanitize_title( $f ), $data['css'], '', ESG_REVISION );
		}

		ThemePunch_Fonts::enqueue_icon_fonts( "admin" );
	}

	/**
	 * enqueue styles for screens
	 *
	 * @return void
	 */
	public function add_screen_styles() {
		wp_enqueue_style( [ 'wp-jquery-ui', 'wp-jquery-ui-core', 'wp-jquery-ui-dialog', 'wp-color-picker' ] );
		wp_enqueue_style( 'esg-admin-styles', ESG_PLUGIN_URL . 'admin/assets/css/esg-admin.css', [], ESG_REVISION );
		wp_enqueue_style( 'esg-dialog-styles', ESG_PLUGIN_URL . 'admin/assets/css/esg-dialog.css', [], ESG_REVISION );
		wp_enqueue_style( 'esg-mirror-styles', ESG_PLUGIN_URL . 'admin/assets/css/esg-mirror.css', [], ESG_REVISION );
		wp_enqueue_style( 'esg-tooltipser-styles', ESG_PLUGIN_URL . 'admin/assets/css/tooltipster.css', [], ESG_REVISION );
		wp_enqueue_style( 'esg-color-picker-css', ESG_PLUGIN_URL . 'admin/assets/css/tp-color-picker.css', [], ESG_REVISION );
		wp_enqueue_style( 'esg-ddtp-css', ESG_PLUGIN_URL . 'admin/assets/css/ddTP.css', [], ESG_REVISION );
	}

	/**
	 * enqueue fonts for screens
	 *
	 * @return void
	 */
	public function add_screen_fonts() {
		$font = new ThemePunch_Fonts();
		$font->register_icon_fonts( "admin" );

		wp_enqueue_style( 'tp-' . sanitize_title( 'Material Icons' ), ESG_PLUGIN_URL . 'public/assets/font/material/material-icons.css', '', ESG_REVISION );
	}

	/**
	 * enqueue scripts for screens
	 *
	 * @return void
	 */
	public function add_screen_scripts() {
		global $esg_dev_mode;

		$wp_version = $this->get_wp_version();

		wp_enqueue_script( [
			'jquery',
			'jquery-ui-core',
			'jquery-ui-dialog',
			'jquery-ui-slider',
			'jquery-ui-autocomplete',
			'jquery-ui-droppable',
			'jquery-ui-draggable',
			'jquery-ui-resizable',
			'jquery-ui-sortable',
			'jquery-ui-tabs',
			'wp-color-picker',
			'wpdialogs',
			'updates',
		] );

		if ( version_compare( $wp_version, '5.6', '<' ) ) {
			wp_enqueue_script( [ 'jquery-ui-sortable', 'jquery-ui-draggable' ] );
		}

		Essential_Grid::enqueue_tptools();

		if ( $esg_dev_mode ) {
			// DEV VERSION
			wp_enqueue_script( 'esg-admin-script', ESG_PLUGIN_URL . 'admin/assets/js/modules/dev/admin.js', [
				'jquery',
				'wp-color-picker'
			], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-admin-sortable-script', ESG_PLUGIN_URL . 'admin/assets/js/modules/dev/sortable.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-admin-dialog-script', ESG_PLUGIN_URL . 'admin/assets/js/modules/dev/dialog.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-admin-addons-script', ESG_PLUGIN_URL . 'admin/assets/js/modules/dev/addons.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-admin-templates-script', ESG_PLUGIN_URL . 'admin/assets/js/modules/dev/templates.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-admin-overview-script', ESG_PLUGIN_URL . 'admin/assets/js/modules/dev/overview.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-admin-tip-script', ESG_PLUGIN_URL . 'admin/assets/js/modules/dev/tip.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-admin-scroll-tabs-script', ESG_PLUGIN_URL . 'admin/assets/js/modules/dev/scroll-tabs.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-shortcode', ESG_PLUGIN_URL . 'admin/assets/js/modules/dev/shortcode.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );

			//UTILS
			wp_enqueue_script( 'esg-perfect-scrollbar-script', ESG_PLUGIN_URL . 'admin/assets/js/plugins/dev/esg-perfect-scrollbar.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-tooltipser-script', ESG_PLUGIN_URL . 'admin/assets/js/plugins/dev/tooltipster.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-esgmirror-script', ESG_PLUGIN_URL . 'admin/assets/js/plugins/dev/esgmirror.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-color-picker-js', ESG_PLUGIN_URL . 'admin/assets/js/plugins/dev/tp-color-picker.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );
			wp_enqueue_script( 'esg-ddtp-js', ESG_PLUGIN_URL . 'admin/assets/js/plugins/dev/ddTP.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );

			//ESG GRID
			wp_enqueue_script( 'esg-tp-boxext', ESG_PLUGIN_URL . 'public/assets/js/dev/esgbox.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => true ] );
			wp_enqueue_script( 'esg-script', ESG_PLUGIN_URL . 'public/assets/js/dev/esg.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => true ] );
		} else {
			// PROD VERSION

			//perfect-scrollbar + ToolTipser + Admin.js
			wp_enqueue_script( 'esg-admin-script', ESG_PLUGIN_URL . 'admin/assets/js/modules/admin.min.js', [ 'jquery', 'wp-color-picker' ], ESG_REVISION, [ 'in_footer' => false ] );
			//ESGMirror + ColorPicker JS + ddTP
			wp_enqueue_script( 'esg-utils', ESG_PLUGIN_URL . 'admin/assets/js/plugins/utils.min.js', [ 'jquery', 'jquery-ui-dialog' ], ESG_REVISION, [ 'in_footer' => false ] );
			//shortcode
			wp_enqueue_script( 'esg-shortcode', ESG_PLUGIN_URL . 'admin/assets/js/modules/shortcode.min.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => false ] );

			//ESG Box
			wp_enqueue_script( 'esg-tp-boxext', ESG_PLUGIN_URL . 'public/assets/js/esgbox.min.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => true ] );

			//ESG GRID
			wp_enqueue_script( 'esg-script', ESG_PLUGIN_URL . 'public/assets/js/esg.min.js', [ 'jquery' ], ESG_REVISION, [ 'in_footer' => true ] );
		}

		wp_enqueue_media();

		wp_localize_script( 'esg-admin-script', 'ESG_LANG', Essential_Grid_Base::get_javascript_multilanguage() );
	}

}
