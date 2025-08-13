<?php

namespace InstagramFeed\Integrations\Elementor;

use InstagramFeed\Builder\SBI_Feed_Builder;
use InstagramFeed\Helpers\Util;

class SBI_Elementor_Base
{
	private const VERSION = SBIVER;
	private const MINIMUM_ELEMENTOR_VERSION = '3.6.0';
	private const MINIMUM_PHP_VERSION = '5.6';
	private const NAME_SPACE = 'InstagramFeed.Integrations.Elementor.';

	/**
	 * The singleton instance of the class.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Get the singleton instance of the class.
	 *
	 * @return self The singleton instance of the class.
	 */
	public static function instance()
	{
		if (!self::is_compatible()) {
			return;
		}

		if (!isset(self::$instance) && !self::$instance instanceof SBI_Elementor_Base) {
			self::$instance = new SBI_Elementor_Base();
			self::$instance->apply_hooks();
		}
		return self::$instance;
	}

	/**
	 * Compatibility Checks
	 *
	 * @return bool
	 */
	public static function is_compatible()
	{
		if (!did_action('elementor/loaded')) {
			return false;
		}

		if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
			return false;
		}

		if (!version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=')) {
			return false;
		}

		return true;
	}

	/**
	 * Applies necessary hooks for the Elementor integration.
	 *
	 * @return void
	 */
	private function apply_hooks()
	{
		add_action('elementor/frontend/after_register_scripts', [$this, 'register_frontend_scripts']);
		add_action('elementor/frontend/after_register_styles', [$this, 'register_frontend_styles'], 10);
		add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_frontend_styles'], 10);
		add_action('elementor/controls/register', [$this, 'register_controls']);
		add_action('elementor/widgets/register', [$this, 'register_widgets']);
		add_action('elementor/elements/categories_registered', [$this, 'add_smashballon_categories']);
	}

	/**
	 * Registers custom controls with the Elementor controls manager.
	 *
	 * @param \Elementor\Controls_Manager $controls_manager The controls manager instance.
	 */
	public function register_controls($controls_manager)
	{
		$controls_manager->register(new SBI_Feed_Elementor_Control());
	}

	/**
	 * Registers widgets with the Elementor widgets manager.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager The Elementor widgets manager instance.
	 *
	 * @return void
	 */
	public function register_widgets($widgets_manager)
	{
		$widgets_manager->register(new SBI_Elementor_Widget());

		$installed_plugins = SBI_Feed_Builder::get_smashballoon_plugins_info();
		unset($installed_plugins['instagram']);

		foreach ($installed_plugins as $plugin) {
			if (!$plugin['installed']) {
				$plugin_class = str_replace('.', '\\', self::NAME_SPACE) . $plugin['class'];
				$widgets_manager->register(new $plugin_class());
			}
		}
	}


	/**
	 * Registers the frontend scripts for the Elementor integration.
	 *
	 * @return void
	 */
	public function register_frontend_scripts()
	{
		$upload = wp_upload_dir();
		$resized_url = trailingslashit($upload['baseurl']) . trailingslashit(SBI_UPLOADS_NAME);

		$js_options = array(
			'font_method' => 'svg',
			'placeholder' => trailingslashit(SBI_PLUGIN_URL) . 'img/placeholder.png',
			'resized_url' => $resized_url,
			'ajax_url' => admin_url('admin-ajax.php'),
		);

		wp_register_script(
			'sbiscripts',
			SBI_PLUGIN_URL . 'js/sbi-scripts.min.js',
			array('jquery'),
			SBIVER,
			true
		);
		wp_localize_script('sbiscripts', 'sb_instagram_js_options', $js_options);

		$data_handler = array(
			'smashPlugins' => SBI_Feed_Builder::get_smashballoon_plugins_info(),
			'nonce' => wp_create_nonce('sbi-admin'),
			'ajax_handler' => admin_url('admin-ajax.php'),
		);

		wp_register_script(
			'elementor-handler',
			SBI_PLUGIN_URL . 'admin/assets/js/elementor-handler.js',
			array('jquery'),
			SBIVER,
			true
		);

		wp_localize_script('elementor-handler', 'sbHandler', $data_handler);


		wp_register_script(
			'elementor-preview',
			SBI_PLUGIN_URL . 'admin/assets/js/elementor-preview.js',
			array('jquery'),
			SBIVER,
			true
		);
	}

	/**
	 * Registers the frontend styles for the Elementor integration.
	 *
	 * @return void
	 */
	public function register_frontend_styles()
	{
		// legacy settings.
		$path = Util::sbi_legacy_css_enabled() ? 'css/legacy/' : 'css/';

		wp_register_style(
			'sbistyles',
			SBI_PLUGIN_URL . $path . 'sbi-styles.min.css',
			array(),
			SBIVER
		);
	}

	/**
	 * Enqueues the necessary frontend styles for the Elementor integration.
	 *
	 * @return void
	 */
	public function enqueue_frontend_styles()
	{
		wp_enqueue_style('sbistyles');
	}

	/**
	 * Adds Smash Balloon categories to the Elementor elements manager.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager The Elementor elements manager instance.
	 *
	 * @return void
	 */
	public function add_smashballon_categories($elements_manager)
	{
		$elements_manager->add_category(
			'smash-balloon',
			[
				'title' => esc_html__('Smash Balloon', 'instagram-feed'),
				'icon' => 'fa fa-plug',
			]
		);
	}
}
