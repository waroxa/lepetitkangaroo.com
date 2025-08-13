<?php
/**
 * Essential Grid.
 *
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

/**
 * @package Essential_Grid_Admin
 * @author  ThemePunch <info@themepunch.com>
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Admin extends Essential_Grid_Base
{

	const ROLE_ADMIN = "admin";
	const ROLE_EDITOR = "editor";
	const ROLE_AUTHOR = "author";

	const VIEW_START = "grid";
	const VIEW_OVERVIEW = "grid-overview";
	const VIEW_GRID_CREATE = "grid-create";
	const VIEW_GRID = "grid-details";
	const VIEW_META_BOX = "grid-meta-box";
	const VIEW_ITEM_SKIN_EDITOR = "grid-item-skin-editor";
	const VIEW_GOOGLE_FONTS = "global-settings";
	const VIEW_IMPORT_EXPORT = "grid-import-export";

	const VIEW_WIDGET_AREAS = "grid-widget-areas";

	const VIEW_SEARCH = "global-settings";
	const VIEW_SUB_ITEM_SKIN_OVERVIEW = "grid-item-skin";
	const VIEW_SUB_CUSTOM_META = "global-settings";

	const VIEW_GLOBAL_SETTINGS = "grid-global-settings";
	const VIEW_SUB_CUSTOM_META_AJAX = "global-settings";

	const VIEW_SUB_WIDGET_AREA_AJAX = "widget-areas";

	/**
	 * @var string 
	 */
	private $plugin_slug;

	/**
	 * @var string
	 */
	protected static $view;

	/**
	 * Instance of this class.
	 * @var null|object
	 */
	protected static $instance = null;

	/**
	 * ESG pages slugs
	 * @var array
	 */
	protected $screens = [];
	
	/**
	 * @var string 
	 */
	private static $menuRole = self::ROLE_ADMIN;

	/**
	 * Return an instance of this class.
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return apply_filters('essgrid_get_instance', self::$instance);
	}

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 */
	public function __construct()
	{
		global $wp_version, $pagenow;

		$library = new Essential_Grid_Library();
		$plugin = Essential_Grid::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		$role = get_option('tp_eg_role', self::ROLE_ADMIN);
		if (empty($role)) $role = self::ROLE_ADMIN;
		self::setMenuRole($role);

		// Add the options page and menu item.
		add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
		add_action('admin_init', [$this, 'display_external_redirects']);
		add_action('admin_head', [$this, 'add_js_menu_open_blank']);

		// Add the meta box to post/pages
		$enable_post_meta = get_option('tp_eg_enable_post_meta', 'true');
		if ('true' === $enable_post_meta) {
			add_action('registered_post_type', [$this, 'prepare_add_plugin_meta_box'], 10, 1);
			add_action('save_post', [$this, 'add_plugin_meta_box_save']);
		}
		
		add_action('wp_ajax_Essential_Grid_request_ajax', [$this, 'on_ajax_action']);

		$validated = Essential_Grid_Base::getValid();
		$notice = Essential_Grid_Base::getValidNotice();
		if ($validated === 'false' && $notice === 'true') {
			add_action('admin_notices', [$this, 'add_activate_notification']);
		}

		// Plugin page extra go premium button
		if(isset($pagenow) && $pagenow == 'plugins.php'){
			add_filter('admin_notices', [$this, 'add_plugins_page_notices']);
			if($validated == 'false'){
				add_filter('plugin_action_links_' . ESG_PLUGIN_SLUG_PATH, [$this, 'add_plugin_action_links']);
			}
		}

		$upgrade		= new Essential_Grid_Update(ESG_REVISION);
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- wp_nonce is not required here
		$upgrade->force	= ( (isset($_GET['checkforupdates']) && $_GET['checkforupdates'] == 'true') || !empty($_GET['force-check']) );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- wp_nonce is not required here
		$shop_upgrade	= isset($_GET['update_shop']);

		$upgrade->_retrieve_version_info();
		$upgrade->add_update_checks();

		$library->_get_template_list($shop_upgrade);
		
		add_action('admin_notices', [$this, 'add_notices']);

		//add calls to delete transient if needed
		add_action('save_post', [$this, 'check_for_transient_deletion']);
		add_action('future_to_publish', [$this, 'check_for_transient_deletion']);
		add_action('publish_post', [$this, 'check_for_transient_deletion']);
		add_action('publish_future_post', [$this, 'check_for_transient_deletion']);

		// add ThemePunch block category
		if (version_compare($wp_version, '5.8', '>=')) {
			add_filter('block_categories_all', [$this, 'create_block_category'], 10, 2);
		} else { 
			// block_categories is deprecated since 5.8.0
			add_filter('block_categories', [$this, 'create_block_category'], 10, 2);
		}

		// Privacy
		add_action('admin_init', [$this, 'add_suggested_privacy_content'], 15);
		
		//body class
		add_filter('admin_body_class', [$this, 'add_body_class'], 10, 1);
	}

	/**
	 * @return array
	 */
	public function get_screens()
	{
		return $this->screens;
	}

	/**
	 * @param string $classes
	 * @return string
	 */
	public function add_body_class($classes)
	{
		// we are not on esg page
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- wp_nonce is not required here
		if (!isset($_GET['page']) || $_GET['page'] != ESG_PLUGIN_SLUG) return $classes;

		self::$view = self::getGetVar("view");
		if (empty(self::$view))
			self::$view = self::VIEW_OVERVIEW;
		
		return $classes . ' esg-' . preg_replace('/[^a-zA-Z-]/', '', self::$view);
	}

	/**
	 * Return the default suggested privacy policy content.
	 *
	 * @return string  The default policy content.
	 */
	public function get_default_privacy_content()
	{
		return __('<h2>Essential Grid core itself does not collect any data from website visitors. In case you’re using things like Google Web Fonts (default) or connect to external sources in your Essential Grid please add the corresponding text phrase to your privacy police:</h2>
    <h3>Google Web Fonts</h3> <p>For uniform representation of fonts, this page uses web fonts provided by Google. When you open a page, your browser loads the required web fonts into your browser cache to display texts and fonts correctly.</p> <p>For this purpose your browser has to establish a direct connection to Google servers. Google thus becomes aware that our web page was accessed via your IP address. The use of Google Web fonts is done in the interest of a uniform and attractive presentation of our plugin. This constitutes a justified interest pursuant to Art. 6 (1) (f) DSGVO.</p> <p>If your browser does not support web fonts, a standard font is used by your computer.</p> <p>Further information about handling user data, can be found at <a href="https://developers.google.com/fonts/faq" target="_blank">https://developers.google.com/fonts/faq</a> and in Google\'s privacy policy at <a href="https://www.google.com/policies/privacy/" target="_blank">https://www.google.com/policies/privacy/</a>.</p>
    <h3>YouTube</h3> <p>Our website uses plugins from YouTube, which is operated by Google. The operator of the pages is YouTube LLC, 901 Cherry Ave., San Bruno, CA 94066, USA.</p> <p>If you visit one of our pages featuring a YouTube plugin, a connection to the YouTube servers is established. Here the YouTube server is informed about which of our pages you have visited.</p> <p>If you\'re logged in to your YouTube account, YouTube allows you to associate your browsing behavior directly with your personal profile. You can prevent this by logging out of your YouTube account.</p> <p>YouTube is used to help make our plugin appealing. This constitutes a justified interest pursuant to Art. 6 (1) (f) DSGVO.</p> <p>Further information about handling user data, can be found in the data protection declaration of YouTube under <a href="https://www.google.de/intl/de/policies/privacy" target="_blank">https://www.google.de/intl/de/policies/privacy</a>.</p>
    <h3>Vimeo</h3> <p>Our website uses features provided by the Vimeo video portal. This service is provided by Vimeo Inc., 555 West 18th Street, New York, New York 10011, USA.</p> <p>If you visit one of our pages featuring a Vimeo plugin, a connection to the Vimeo servers is established. Here the Vimeo server is informed about which of our pages you have visited. In addition, Vimeo will receive your IP address. This also applies if you are not logged in to Vimeo when you visit our plugin or do not have a Vimeo account. The information is transmitted to a Vimeo server in the US, where it is stored.</p> <p>If you are logged in to your Vimeo account, Vimeo allows you to associate your browsing behavior directly with your personal profile. You can prevent this by logging out of your Vimeo account.</p> <p>For more information on how to handle user data, please refer to the Vimeo Privacy Policy at <a href="https://vimeo.com/privacy" target="_blank">https://vimeo.com/privacy</a>.</p>
    <h3>SoundCloud</h3><p>On our pages, plugins of the SoundCloud social network (SoundCloud Limited, Berners House, 47-48 Berners Street, London W1T 3NF, UK) may be integrated. The SoundCloud plugins can be recognized by the SoundCloud logo on our site.</p>
      <p>When you visit our site, a direct connection between your browser and the SoundCloud server is established via the plugin. This enables SoundCloud to receive information that you have visited our site from your IP address. If you click on the “Like” or “Share” buttons while you are logged into your SoundCloud account, you can link the content of our pages to your SoundCloud profile. This means that SoundCloud can associate visits to our pages with your user account. We would like to point out that, as the provider of these pages, we have no knowledge of the content of the data transmitted or how it will be used by SoundCloud. For more information on SoundCloud’s privacy policy, please go to https://soundcloud.com/pages/privacy.</p><p>If you do not want SoundCloud to associate your visit to our site with your SoundCloud account, please log out of your SoundCloud account.</p>
    <h3>Facebook</h3>
      <p>Our website includes plugins for the social network Facebook, Facebook Inc., 1 Hacker Way, Menlo Park, California 94025, USA. For an overview of Facebook plugins, see <a href="https://developers.facebook.com/docs/plugins/" target="_blank" rel="noopener">https://developers.facebook.com/docs/plugins/</a>.</p><p>When you visit our site, a direct connection between your browser and the Facebook server is established via the plugin. This enables Facebook to receive information that you have visited our site from your IP address. If you click on the Facebook &#8220;Like button&#8221; while you are logged into your Facebook account, you can link the content of our site to your Facebook profile. This allows Facebook to associate visits to our site with your user account. Please note that, as the operator of this site, we have no knowledge of the content of the data transmitted to Facebook or of how Facebook uses these data. For more information, please see Facebook&#8217;s privacy policy at <a href="https://de-de.facebook.com/policy.php" target="_blank" rel="noopener">https://de-de.facebook.com/policy.php</a>.</p><p>If you do not want Facebook to associate your visit to our site with your Facebook account, please log out of your Facebook account.</p>
    <h3>Twitter</h3>
      <p>Functions of the Twitter service have been integrated into our website and app. These features are offered by Twitter Inc., 1355 Market Street, Suite 900, San Francisco, CA 94103, USA. When you use Twitter and the “Retweet” function, the websites you visit are connected to your Twitter account and made known to other users. In doing so, data will also be transferred to Twitter. We would like to point out that, as the provider of these pages, we have no knowledge of the content of the data transmitted or how it will be used by Twitter. For more information on Twitter&#8217;s privacy policy, please go to <a href="https://twitter.com/privacy" target="_blank" rel="noopener">https://twitter.com/privacy</a>.</p><p>Your privacy preferences with Twitter can be modified in your account settings at <a href="https://twitter.com/account/settings" target="_blank" rel="noopener">https://twitter.com/account/settings</a>.</p>
    <h3>Instagram</h3>
      <p>Our website contains functions of the Instagram service. These functions are offered by Instagram Inc., 1601 Willow Road, Menlo Park, CA 94025, USA.</p><p>If you are logged into your Instagram account, you can click the Instagram button to link the content of our pages with your Instagram profile. This means that Instagram can associate visits to our pages with your user account. As the provider of this website, we expressly point out that we receive no information on the content of the transmitted data or its use by Instagram.</p><p>For more information, see the Instagram Privacy Policy: <a href="https://instagram.com/about/legal/privacy/" target="_blank" rel="noopener">https://instagram.com/about/legal/privacy/</a>.</p>', 'essential-grid');
	}

	/**
	 * Add the suggested privacy policy text to the policy eg-pbox.
	 */
	public function add_suggested_privacy_content()
	{
		if (function_exists("wp_add_privacy_policy_content")) {
			$content = $this->get_default_privacy_content();
			wp_add_privacy_policy_content(esc_attr__('Essential Grid', 'essential-grid'), $content);
		}
	}

	/**
	 * add notices from ThemePunch
	 * @since: 2.1.0
	 * @return void
	 */
	public function add_notices()
	{
		//check permissions here
		if (!current_user_can('administrator')) return;

		$enable_newschannel = apply_filters('essgrid_set_notifications', 'on');
		if ($enable_newschannel == 'on') {
			$nonce = wp_create_nonce("Essential_Grid_actions");
			$notices = get_option('essential-notices', false);
			if (!empty($notices) && is_array($notices)) {
				$notices_discarded = get_option('essential-notices-dc', []);
				$screen = get_current_screen();
				foreach ($notices as $notice) {
					if ($notice->is_global !== true && !in_array($screen->id, $this->screens)) continue; //check if global or just on plugin related pages

					if (!in_array($notice->code, $notices_discarded) && version_compare($notice->version, ESG_REVISION, '>=')) {
						$text = '<div class="esg-notices-button-container"><a href="javascript:void(0);"  class="esg-notices-button esg-notice-' . esc_attr($notice->code) . '">' . esc_attr__('Close & don\'t show again', 'essential-grid') . '<b>X</b></a></div>';
						if ($notice->disable) $text = '';
						?>
						<div class="<?php echo esc_attr($notice->color); ?> below-h2 esg-update-notice-wrap" id="message">
							<div class="esg-update-notice-table">
								<div class="esg-update-notice-table-cell"><?php echo esc_html($notice->text); ?></div>
								<?php echo esc_html($text); ?>
							</div>
						</div>
						<?php
					}
				}
				?>
				<script type="text/javascript">
					jQuery('.esg-notices-button').on('click', function () {

						let notice_id = jQuery(this).attr('class').replace('esg-notices-button', '').replace('esg-notice-', '');

						let objData = {
							action: "Essential_Grid_request_ajax",
							client_action: 'dismiss_dynamic_notice',
							token: '<?php echo esc_js($nonce); ?>',
							data: {'id': notice_id}
						};

						jQuery.ajax({
							type: "post",
							url: ajaxurl,
							dataType: 'json',
							data: objData
						});

						jQuery(this).closest('.esg-update-notice-wrap').slideUp(200);
					});
				</script>
				<?php
			}
		}
	}

	/**
	 * show notification message if plugin is not activated
	 */
	public function add_activate_notification()
	{
		require_once(ESG_PLUGIN_ADMIN_PATH . '/views/elements/activate-notification.php');
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	public function add_plugin_admin_menu()
	{
		$role = self::getPluginPermission();

		$this->screens[] = add_menu_page(esc_attr__('Essential Grid', 'essential-grid'), esc_attr__('Essential Grid', 'essential-grid'), $role, $this->plugin_slug, [$this, 'display_plugin_admin_page'], 'dashicons-screenoptions');
		$this->screens[] = add_submenu_page($this->plugin_slug, esc_attr__('Item Skin Editor', 'essential-grid'), esc_attr__('Item Skin Editor', 'essential-grid'), $role, $this->plugin_slug . '-item-skin', [$this, 'display_plugin_submenu_page_item_skin']);
		$this->screens[] = add_submenu_page($this->plugin_slug, esc_attr__('Global Settings', 'essential-grid'), esc_attr__('Global Settings', 'essential-grid'), $role, $this->plugin_slug . '-global-settings', [$this, 'display_plugin_submenu_page_global_settings']);
		$this->screens[] = add_submenu_page($this->plugin_slug, esc_attr__('Import/Export', 'essential-grid'), esc_attr__('Import/Export', 'essential-grid'), $role, $this->plugin_slug . '-import-export', [$this, 'display_plugin_submenu_page_import_export']);
		//since 3.0.14
		$this->screens[] = add_submenu_page($this->plugin_slug, esc_attr__('Getting Started', 'essential-grid'), '<div id="essgrid_manual_link" style="margin-top:15px">Getting Started</div>', $role, 'essgrid-documentation', [$this, 'display_external_redirects']);
		$this->screens[] = add_submenu_page($this->plugin_slug, esc_attr__('Help Center', 'essential-grid'), '<div id="essgrid_helpcenter_link">Help Center</div>', $role, 'essgrid-help-center', [$this, 'display_external_redirects']);
		$this->screens[] = add_submenu_page($this->plugin_slug, esc_attr__('Grid Library', 'essential-grid'), '<div id="essgrid_templates_link">Grid Library</div>', $role, 'essgrid-templates', [$this, 'display_external_redirects']);
		$this->screens[] = add_submenu_page($this->plugin_slug, esc_attr__('Premium Support', 'essential-grid'), '<div id="essgrid_ticket_link">Premium Support</div>', $role, 'essgrid-ticket', [$this, 'display_external_redirects']);
		
		if (!Essential_Grid_Base::isValid()){
			$this->screens[] = add_submenu_page($this->plugin_slug, esc_attr__('Go Premium', 'essential-grid'), '<div id="essgrid_premium_link" style="color:#f7345e;margin-top:15px"><span class="dashicons dashicons-star-filled" style="font-size: 17px"></span> '.__('Go Premium', 'essential-grid')."</div>", $role, 'essgrid-buy-license', [$this, 'display_external_redirects']);
		}

		// Full Site Editing
		$this->screens[] = 'widgets';
		$this->screens[] = 'site-editor';

		do_action('essgrid_add_plugin_admin_menu', $role, $this->plugin_slug, $this);
	}

	/**
	 * redirect to external URLs
	 * @since 3.0.14
	 */
	public function display_external_redirects() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- wp_nonce is not required here
		if ( empty( $_GET['page'] ) ) return;

		$eg_premium = Essential_Grid_Base::getValid();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- wp_nonce is not required here
		switch ( $_GET['page'] ) {
			case 'essgrid-buy-license':
				wp_redirect('https://account.essential-grid.com/licenses/pricing//?utm_source=admin&utm_medium=button&utm_campaign=egusers&utm_content=buykey');
				exit;
			case 'essgrid-documentation':
				wp_redirect('https://www.essential-grid.com/manual/installing-activating-and-registering-essential-grid/?utm_source=admin&utm_medium=button&utm_campaign=egusers&utm_content=usedocumentation&premium='.$eg_premium);
				exit;
			case 'essgrid-help-center':
				wp_redirect('https://www.essential-grid.com/help-center/?utm_source=admin&utm_medium=button&utm_campaign=egusers&utm_content=help&premium='.$eg_premium);
				exit;
			case 'essgrid-templates':
				wp_redirect('https://www.essential-grid.com/grids/?utm_source=admin&utm_medium=button&utm_campaign=egusers&utm_content=templates&premium='.$eg_premium);
				exit;
			case 'essgrid-ticket':
				wp_redirect('https://support.essential-grid.com?utm_source=admin&utm_medium=button&utm_campaign=egusers&utm_content=support&premium='.$eg_premium);
				exit;
			default:
		}
	}

	/**
	 * opens the external EssGrid URLs in a blank tab
	 * @since 3.0.15
	 */
	public function add_js_menu_open_blank() {
		echo '<script>
				jQuery(document).ready(function(){
					jQuery("#essgrid_manual_link, #essgrid_helpcenter_link, #essgrid_templates_link, #essgrid_ticket_link, #essgrid_premium_link").parent().attr("target","_blank");
				});
			</script>';
	}

	/**
	 * prepare the meta box inclusion if right post_type (includes all custom post types
	 */
	public static function prepare_add_plugin_meta_box($post_type)
	{
		global $pagenow;
		if (in_array($pagenow, ['comment.php'])) return;
		
		$skip_post_types = apply_filters('essgrid_prepare_add_plugin_meta_box_skip_post_types', [
				'attachment',
				'revision',
				'nav_menu_item',
				'custom_css',
				'customize_changeset',
				'oembed_cache',
				'user_request',
				'wp_block',
				'wp_template',
				'wp_template_part',
				'wp_global_styles',
				'wp_navigation',
		]);
		if (in_array($post_type, $skip_post_types)) return;
		
		add_action('add_meta_boxes', [self::$instance, 'add_plugin_meta_box'], 10, 2);

		do_action('essgrid_prepare_add_plugin_meta_box', $post_type);
	}

	/**
	 * Register the meta box in post / pages
	 */
	public function add_plugin_meta_box($post_type, $post)
	{
		add_meta_box(
			'eg-meta-box', 
			esc_attr__('Essential Grid', 'essential-grid'), 
			[self::$instance, 'display_plugin_meta_box'], 
			$post_type, 
			'normal', 
			'high', 
			[$post]
		);
		do_action('essgrid_add_plugin_meta_box', $post_type, $post);
	}

	/**
	 * Display the meta box
	 */
	public static function display_plugin_meta_box($post)
	{
		require_once(ESG_PLUGIN_ADMIN_PATH . '/views/elements/' . self::VIEW_META_BOX . '.php');
		do_action('essgrid_display_plugin_meta_box', $post);
	}

	/**
	 * Register the meta box save in post / pages
	 */
	public function add_plugin_meta_box_save($post_id)
	{
		// Bail if we're doing an auto save
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce is verified in 'custom_meta_box_save()'
		self::custom_meta_box_save($post_id, $_POST);
		do_action('essgrid_add_plugin_meta_box_save', $post_id);
	}

	/**
	 * This function deletes transient of certain grids where the Post is included in
	 * @since: 1.2.0
	 */
	public static function check_for_transient_deletion($post_id)
	{
		$categories = self::get_custom_taxonomies_by_post_id($post_id);
		$tags = get_the_tags($post_id);

		$cat = [];
		if (!empty($categories) || !empty($tags)) {
			if (!empty($categories)) {
				foreach ($categories as $c) {
					$cat[$c->taxonomy][$c->term_id] = true;
				}
			}
			if (!empty($tags)) {
				foreach ($categories as $c) {
					$cat[$c->taxonomy][$c->term_id] = true;
				}
			}

			//get all grids, then check all grids
			$grids = Essential_Grid_Db::get_entity('grids')->get_grids();
			foreach ($grids as $grid) {
				$selected = json_decode($grid->postparams, true);
				$post_category = self::getVar($selected, 'post_category');
				$cat_tax = self::getCatAndTaxData($post_category);
				$cats = [];
				if (!empty($cat_tax['cats']))
					$cats = explode(',', $cat_tax['cats']);

				$taxes = ['post_tag'];
				if (!empty($cat_tax['tax']))
					$taxes = explode(',', $cat_tax['tax']);

				$cont = false;
				if (!empty($cats)) {
					foreach ($taxes as $tax) {
						foreach ($cats as $c) {
							if (isset($cat[$tax][$c])) { 
								//if set, cache of grid needs to be killed
								self::clear_transients('ess_grid_trans_full_grid_' . $grid->id);
								
								$cont = true;
							}
							if ($cont) break;
						}
						if ($cont) break;
					}
				}
			}
		}

		do_action('essgrid_check_for_transient_deletion', $post_id);
	}

	/**
	 * Adds functionality to do certain things on an upgrade
	 * @since: 1.1.0
	 */
	public static function do_update_checks()
	{
		$grid_ver = Essential_Grid_Db::get_version();
		$updates = new Essential_Grid_Plugin_Update($grid_ver);
		$updates->do_update_process();
		do_action('essgrid_do_update_checks', $grid_ver);
	}

	/**
	 * Include wanted page
	 * 
	 * @return bool
	 */
	public static function custom_meta_box_save($post_id, $metas, $ajax = false)
	{
		$metas = apply_filters('essgrid_custom_meta_box_save', $metas, $post_id, $ajax);

		// if our nonce isn't there, or we can't verify it, bail
		if (!isset($metas['essential_grid_meta_box_nonce']) || !wp_verify_nonce($metas['essential_grid_meta_box_nonce'], 'eg_meta_box_nonce')) return false;

		if (isset($metas['eg_sources_html5_mp4']))
			update_post_meta($post_id, 'eg_sources_html5_mp4', esc_attr($metas['eg_sources_html5_mp4']));

		if (isset($metas['eg_sources_html5_ogv']))
			update_post_meta($post_id, 'eg_sources_html5_ogv', esc_attr($metas['eg_sources_html5_ogv']));

		if (isset($metas['eg_sources_html5_webm']))
			update_post_meta($post_id, 'eg_sources_html5_webm', esc_attr($metas['eg_sources_html5_webm']));

		if (isset($metas['eg_sources_youtube']))
			update_post_meta($post_id, 'eg_sources_youtube', esc_attr($metas['eg_sources_youtube']));

		if (isset($metas['eg_sources_vimeo']))
			update_post_meta($post_id, 'eg_sources_vimeo', esc_attr($metas['eg_sources_vimeo']));

		if (isset($metas['eg_sources_wistia']))
			update_post_meta($post_id, 'eg_sources_wistia', esc_attr($metas['eg_sources_wistia']));

		if (isset($metas['eg_sources_image']))
			update_post_meta($post_id, 'eg_sources_image', esc_attr($metas['eg_sources_image']));

		if (isset($metas['eg_sources_iframe']))
			update_post_meta($post_id, 'eg_sources_iframe', esc_attr($metas['eg_sources_iframe']));

		if (isset($metas['eg_sources_soundcloud']))
			update_post_meta($post_id, 'eg_sources_soundcloud', esc_attr($metas['eg_sources_soundcloud']));

		if (isset($metas['eg_settings_type']))
			update_post_meta($post_id, 'eg_settings_type', esc_attr($metas['eg_settings_type']));

		if (isset($metas['eg_settings_custom_display']))
			update_post_meta($post_id, 'eg_settings_custom_display', esc_attr($metas['eg_settings_custom_display']));

		if (isset($metas['eg_vimeo_ratio']))
			update_post_meta($post_id, 'eg_vimeo_ratio', esc_attr($metas['eg_vimeo_ratio']));

		if (isset($metas['eg_youtube_ratio']))
			update_post_meta($post_id, 'eg_youtube_ratio', esc_attr($metas['eg_youtube_ratio']));

		if (isset($metas['eg_wistia_ratio']))
			update_post_meta($post_id, 'eg_wistia_ratio', esc_attr($metas['eg_wistia_ratio']));

		if (isset($metas['eg_html5_ratio']))
			update_post_meta($post_id, 'eg_html5_ratio', esc_attr($metas['eg_html5_ratio']));

		if (isset($metas['eg_soundcloud_ratio']))
			update_post_meta($post_id, 'eg_soundcloud_ratio', esc_attr($metas['eg_soundcloud_ratio']));

		if (isset($metas['eg_image_fit']))
			update_post_meta($post_id, 'eg_image_fit', esc_attr($metas['eg_image_fit']));

		if (isset($metas['eg_image_repeat']))
			update_post_meta($post_id, 'eg_image_repeat', esc_attr($metas['eg_image_repeat']));

		if (isset($metas['eg_image_align_h']))
			update_post_meta($post_id, 'eg_image_align_h', esc_attr($metas['eg_image_align_h']));

		if (isset($metas['eg_image_align_v']))
			update_post_meta($post_id, 'eg_image_align_v', esc_attr($metas['eg_image_align_v']));

		/* 2.2 ?? */
		if (isset($metas['eg_sources_revslider'])) {
			update_post_meta($post_id, 'eg_sources_revslider', esc_attr($metas['eg_sources_revslider']));
		}

		if (isset($metas['eg_sources_essgrid']))
			update_post_meta($post_id, 'eg_sources_essgrid', esc_attr($metas['eg_sources_essgrid']));

		if (isset($metas['eg_featured_grid']))
			update_post_meta($post_id, 'eg_featured_grid', esc_attr($metas['eg_featured_grid']));

		/**
		 * Save Custom Meta Things that Modify Skins
		 **/
		if (isset($metas['eg-custom-meta-skin']))
			update_post_meta($post_id, 'eg_settings_custom_meta_skin', $metas['eg-custom-meta-skin']);
		else
			update_post_meta($post_id, 'eg_settings_custom_meta_skin', '');

		if (isset($metas['eg-custom-meta-element']))
			update_post_meta($post_id, 'eg_settings_custom_meta_element', $metas['eg-custom-meta-element']);
		else
			update_post_meta($post_id, 'eg_settings_custom_meta_element', '');

		if (isset($metas['eg-custom-meta-setting']))
			update_post_meta($post_id, 'eg_settings_custom_meta_setting', $metas['eg-custom-meta-setting']);
		else
			update_post_meta($post_id, 'eg_settings_custom_meta_setting', '');

		if (isset($metas['eg-custom-meta-style']))
			update_post_meta($post_id, 'eg_settings_custom_meta_style', $metas['eg-custom-meta-style']);
		else
			update_post_meta($post_id, 'eg_settings_custom_meta_style', '');

		if (isset($metas['eg_custom_meta_216']))
			update_post_meta($post_id, 'eg_custom_meta_216', $metas['eg_custom_meta_216']);

		if (!is_numeric(get_post_meta($post_id, 'eg_votes_count', true))) {
			update_post_meta($post_id, 'eg_votes_count', 0);
		}

		/**
		 * Save Custom Meta from Custom Meta Submenu
		 */
		$m = new Essential_Grid_Meta();
		$cmetas = $m->get_all_meta(false);
		if (!empty($cmetas)) {
			foreach ($cmetas as $meta) {
				if (isset($metas['eg-' . $meta['handle']])) {
					if ($meta['type'] == 'multi-select') {
						// multi select values come in two formats:
						// from post / page - array of values
						// from esg editor - string of values, separated by comma, located in first array item, we need to convert it to array
						if ($ajax !== false) {
							$metas['eg-' . $meta['handle']] = explode(',', $metas['eg-' . $meta['handle']][0]);
						}
					}
					if (is_array($metas['eg-' . $meta['handle']])) $metas['eg-' . $meta['handle']] = wp_json_encode($metas['eg-' . $meta['handle']], JSON_UNESCAPED_UNICODE);
					update_post_meta($post_id, 'eg-' . $meta['handle'], $metas['eg-' . $meta['handle']]);
				}
			}
		}

		do_action('essgrid_custom_meta_box_save', $metas, $post_id, $ajax);

		if ($ajax !== false) return true;
	}

	/**
	 * Include wanted page
	 */
	public function display_plugin_admin_page()
	{
		//set view
		self::$view = self::getGetVar("view");
		if (empty(self::$view))
			self::$view = self::VIEW_OVERVIEW;

		$add_folder = '';
		//require styles by view
		switch (self::$view) {
			case self::VIEW_OVERVIEW:
			case self::VIEW_GRID_CREATE:
			case self::VIEW_GRID:
				break;
			case self::VIEW_ITEM_SKIN_EDITOR:
				$add_folder = 'elements/';
				break;
			default: //go back to default
				self::$view = self::VIEW_OVERVIEW;
		}

		try {
			require_once(ESG_PLUGIN_ADMIN_PATH . '/views/header.php');
			$r = apply_filters('essgrid_display_plugin_admin_page_pre', ['add_folder' => $add_folder, 'view' => self::$view]);
			require_once(ESG_PLUGIN_ADMIN_PATH . '/views/' . $r['add_folder'] . $r['view'] . '.php');
			$r = apply_filters('essgrid_display_plugin_admin_page_post', ['add_folder' => $add_folder, 'view' => self::$view]);
			require_once(ESG_PLUGIN_ADMIN_PATH . '/views/footer.php');
		} catch (Exception $e) {
			echo wp_kses("<br><br>View (" . self::$view . ") Error: <b>" . $e->getMessage() . "</b>", ['br' => [], 'b' => [], ]);
		}
	}

	/**
	 * Include wanted submenu page
	 */
	public function display_plugin_submenu_page_item_skin()
	{
		do_action('essgrid_display_plugin_submenu_page_item_skin_pre');
		self::display_plugin_submenu('grid-item-skin');
		do_action('essgrid_display_plugin_submenu_page_item_skin_post');
	}

	/**
	 * Include wanted submenu page
	 */
	public function display_plugin_submenu_page_import_export()
	{
		do_action('essgrid_display_plugin_submenu_page_import_export_pre');
		self::display_plugin_submenu('grid-import-export');
		do_action('essgrid_display_plugin_submenu_page_import_export_post');
	}

	/**
	 * Include wanted submenu page
	 * Since 1.0.6
	 */
	public function display_plugin_submenu_page_widget_areas()
	{
		do_action('essgrid_display_plugin_submenu_page_widget_areas_pre');
		self::display_plugin_submenu('grid-widget-areas');
		do_action('essgrid_display_plugin_submenu_page_widget_areas_post');
	}

	/**
	 * Include wanted submenu page
	 * Since 2.1.0
	 */
	public function display_plugin_submenu_page_global_settings()
	{
		do_action('essgrid_display_plugin_submenu_page_global_settings_pre');
		self::display_plugin_submenu('grid-global-settings');
		do_action('essgrid_display_plugin_submenu_page_global_settings_post');
	}

	/**
	 * Include wanted submenu page
	 */
	public function display_plugin_submenu($subMenu)
	{
		if (empty($subMenu))
			$subMenu = self::VIEW_SUB_ITEM_SKIN_OVERVIEW;

		//require styles by view
		switch ($subMenu) {
			case self::VIEW_SUB_ITEM_SKIN_OVERVIEW:
			case self::VIEW_SUB_CUSTOM_META:
			case self::VIEW_GOOGLE_FONTS:
			case self::VIEW_IMPORT_EXPORT:
			case self::VIEW_GLOBAL_SETTINGS:
			case self::VIEW_WIDGET_AREAS:
			case self::VIEW_SEARCH:
				break;
			default: 
				//go back to default
				$subMenu = self::VIEW_SUB_ITEM_SKIN_OVERVIEW;
		}

		try {
			require_once(ESG_PLUGIN_ADMIN_PATH . '/views/header.php');
			$subMenu = apply_filters('essgrid_display_plugin_submenu_pre', $subMenu);
			require_once(ESG_PLUGIN_ADMIN_PATH . '/views/' . $subMenu . '.php');
			$subMenu = apply_filters('essgrid_display_plugin_submenu_post', $subMenu);
			require_once(ESG_PLUGIN_ADMIN_PATH . '/views/footer.php');
		} catch (Exception $e) {
			echo wp_kses("<br><br>View (" . $subMenu . ") Error: <b>" . $e->getMessage() . "</b>", ['br' => [], 'b' => [], ]);
		}
	}

	/**
	 * Set Menu Role
	 * @param string $role  administrator | author etc...
	 */
	private function setMenuRole($role)
	{
		self::$menuRole = apply_filters('essgrid_setMenuRole', $role);
	}

	/**
	 * Get Menu Role
	 * @return string  the current role
	 */
	public static function getPluginPermission()
	{
		switch (self::$menuRole) {
			case self::ROLE_AUTHOR:
				$role = "edit_published_posts";
				break;
			case self::ROLE_EDITOR:
				$role = "edit_pages";
				break;
			default:
			case self::ROLE_ADMIN:
				$role = "manage_options";
				break;
		}

		return apply_filters('essgrid_getPluginPermission', $role);
	}

	/**
	 * Get Menu Role
	 * @return    string    $role    the current role
	 */
	public static function getPluginPermissionValue()
	{
		$role = self::$menuRole;
		switch (self::$menuRole) {
			case self::ROLE_AUTHOR:
			case self::ROLE_EDITOR:
			case self::ROLE_ADMIN:
				break;
			default:
				$role = self::ROLE_ADMIN;
				break;
		}

		return apply_filters('essgrid_getPluginPermissionValue', $role);
	}

	/**
	 * Save Menu Role
	 * @return    boolean true
	 */
	private static function savePluginPermission($newPermission)
	{
		$return = true;

		switch ($newPermission) {
			case self::ROLE_AUTHOR:
			case self::ROLE_EDITOR:
			case self::ROLE_ADMIN:
				break;
			default:
				$return = false;
				break;
		}

		$r = apply_filters('essgrid_getPluginPermissionValue', ['return' => $return, 'newPermission' => $newPermission]);
		if ($r['return'] === true) {
			update_option('tp_eg_role', $r['newPermission']);
		}

		return $r['return'];
	}

	/**
	 * update grid name
	 * 
	 * @param array $data
	 * @return string|bool
	 */
	public static function update_grid_name($data)
	{
		$data = apply_filters('essgrid_update_grid_name', $data);
		if (!isset($data['id']) || intval($data['id']) == 0) return esc_attr__('Invalid ID', 'essential-grid');
		if (!isset($data['name']) || strlen($data['name']) < 2) return esc_attr__('Title needs to have at least 2 characters', 'essential-grid');
		
		$response = Essential_Grid_Db::get_entity('grids')->update( $data, $data['id'] );
		if ($response === false) return esc_attr__('Grid could not be changed', 'essential-grid');

		return true;
	}

	/**
	 * get first post for post-based grid
	 * 
	 * @param array $grid
	 * @return array
	 */
	public static function get_grid_first_post($grid)
	{
		$start_sortby = Essential_Grid_Base::getVar($grid['params'], 'sorting-order-by-start', 'none');
		$start_sortby_type = Essential_Grid_Base::getVar($grid['params'], 'sorting-order-type', 'ASC');
		$page_ids = explode(',', Essential_Grid_Base::getVar($grid['postparams'], 'selected_pages', '-1'));
		$post_types = Essential_Grid_Base::getVar($grid['postparams'], 'post_types');
		$post_category = Essential_Grid_Base::getVar($grid['postparams'], 'post_category');
		$cat_relation = Essential_Grid_Base::getVar($grid['postparams'], 'category-relation', 'OR');
		$cat_tax = Essential_Grid_Base::getCatAndTaxData($post_category);
		$additional_query = Essential_Grid_Base::getVar($grid['postparams'], 'additional-query');
		if ($additional_query !== '')
			$additional_query = wp_parse_args($additional_query);

		return Essential_Grid_Base::getPostsByCategory(0, $cat_tax['cats'], $post_types, $cat_tax['tax'], $page_ids, $start_sortby, $start_sortby_type, 1, $additional_query, $cat_relation);
	}

	/**
	 * Update/Create Grid
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public static function update_create_grid($data)
	{
		$data = apply_filters('essgrid_update_create_grid', $data);
		if (!isset($data['name']) || strlen($data['name']) < 2) return esc_attr__('Title needs to have at least 2 characters', 'essential-grid');
		if (!isset($data['handle']) || strlen($data['handle']) < 2) return esc_attr__('Alias needs to have at least 2 characters', 'essential-grid');
		if (preg_replace('/[^a-zA-Z0-9 \-_]/', '', $data['handle']) != $data['handle']) return esc_attr__('Alias contain forbidden characters!', 'essential-grid');
		if (empty($data['params'])) return esc_attr__('No setting informations received!', 'essential-grid');

		if ($data['postparams']['source-type'] == 'custom') {
			if (empty($data['layers'])) return esc_attr__('Please add at least one element in Custom Grid mode', 'essential-grid');
		} elseif ($data['postparams']['source-type'] == 'post') {
			if (empty($data['postparams']['post_types'])) return esc_attr__('Please select a Post Type', 'essential-grid');
		} elseif (!isset($data['postparams']['source-type'])) {
			return esc_attr__('Invalid data received, this could be the cause of server limitations. If you use a custom grid, please lower the number of entries.', 'essential-grid');
		}

		// layers used for source-type = custom
		if (empty($data['layers'])) $data['layers'] = [];
		
		// fix custom filter doubles
		// TODO: rewrite js part completely
		foreach ($data['layers'] as $k => $v) {
			$layer = json_decode($v, true);
			if (!empty($layer['custom-filter'])) {
				$layer['custom-filter'] = implode(',', array_unique(explode(',', $layer['custom-filter'])));
				$data['layers'][$k] = wp_json_encode($layer);
			}
		}

		// add grid thumb
		if (empty($data['settings'])) {
			$data['settings'] = [];
		} else if (!is_array($data['settings'])) {
			$data['settings'] = json_decode($data['settings'], true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				$data['settings'] = [];
			}
		}
		
		switch ($data['postparams']['source-type']) {
			case 'custom':
				// get first layer image
				if (empty($data['layers'][0])) break;
				
				$layer = json_decode($data['layers'][0], true);
				if (json_last_error() !== JSON_ERROR_NONE || empty($layer['custom-image'])) break;

				$img = wp_get_attachment_image_src($layer['custom-image'], 'full');
				if (!empty($img[0])) $data['settings']['bg'] = $img[0];
				break;
				
			case 'post':
				// get first post image
				$posts = self::get_grid_first_post($data);
				if (empty($posts[0]['ID'])) break;
				
				$attach_id = get_post_thumbnail_id($posts[0]['ID']);
				if ($attach_id) {
					$img = wp_get_attachment_image_src($attach_id, 'full');
					if (!empty($img[0])) $data['settings']['bg'] = $img[0];
				}
				break;
		}

		$data = apply_filters('essgrid_before_update_create_grid', $data);
		$data_db = [
			'name' => $data['name'],
			'handle' => $data['handle'],
			'postparams' => wp_json_encode($data['postparams']),
			'params' => wp_json_encode($data['params']),
			'layers' => wp_json_encode($data['layers']),
			'settings' => wp_json_encode($data['settings']),
			'last_modified' => gmdate('Y-m-d H:i:s')
		];

		$grid_by_handle = Essential_Grid_Db::get_entity('grids')->get_by_handle( $data['handle'] );
		
		if (isset($data['id']) && intval($data['id']) > 0) { 
			// update

			//check if exists
			$entry = Essential_Grid_Db::get_entity('grids')->get( $data['id'] );
			if (empty($entry)) {
				return esc_attr__('Invalid Grid ID.', 'essential-grid');
			}
			
			// check if entry with handle has same ID, because this is unique
			if ( !empty($grid_by_handle) && $grid_by_handle['id'] != $data['id'] ) {
				return esc_attr__('Grid with chosen alias already exists, please choose a different alias.', 'essential-grid');
			}

			$response = Essential_Grid_Db::get_entity('grids')->update( $data_db, $data['id'] );
			if ($response === false) return esc_attr__('Ess. Grid could not be changed', 'essential-grid');

			return true;
		}

		//check if entry with handle exists, because this is unique
		if (!empty($grid_by_handle)) {
			return esc_attr__('Ess. Grid with chosen alias already exists, please choose a different alias', 'essential-grid');
		}

		//insert if function did not return yet
		$response = Essential_Grid_Db::get_entity('grids')->insert( $data_db );
		return boolval( $response );
	}

	/**
	 * Delete Grid
	 * @return    boolean true
	 */
	private static function delete_grid_by_id($data)
	{
		$data = apply_filters('essgrid_delete_grid_by_id', $data);
		if (!isset($data['id']) || intval($data['id']) == 0) return esc_attr__('Invalid ID', 'essential-grid');

		$response = Essential_Grid_Db::get_entity('grids')->delete( $data['id'] );
		do_action('essgrid_on_delete_grid_by_id', $response, $data);
		if ($response === false) return esc_attr__('Ess. Grid could not be deleted', 'essential-grid');

		return true;
	}

	/**
	 * Duplicate Grid
	 * 
	 * @param array $data
	 * @return  string|array
	 */
	private static function duplicate_grid_by_id($data)
	{
		global $wpdb;

		$data = apply_filters('essgrid_duplicate_grid_by_id', $data);
		if (!isset($data['id']) || intval($data['id']) == 0) return esc_attr__('Invalid ID', 'essential-grid');

		//check if ID exists
		$duplicate = Essential_Grid_Db::get_entity('grids')->get( $data['id'] );
		if (empty($duplicate)) return esc_attr__('Grid could not be duplicated', 'essential-grid');

		//check if handle 'grid-n' does exist
		$i = $data['id'] - 1;
		do {
			$i++;
			$result = Essential_Grid_Db::get_entity('grids')->get_by_handle( 'grid-' . $i );
		} while (!empty($result));

		//now add new Entry
		unset($duplicate['id']);
		$duplicate['name'] = 'Copy of ' . $duplicate['name'] . ' ' . $i;
		$duplicate['handle'] = 'grid-' . $i;

		$response = Essential_Grid_Db::get_entity('grids')->insert( $duplicate );
		if ($response === false) return esc_attr__('Grid could not be duplicated', 'essential-grid');

		$duplicate['id'] = $wpdb->insert_id;
		/**
		 * @param array $duplicate  duplicated grid data with new ID
		 * @param int|false  $wpdb->insert result, The number of rows inserted, or false on error
		 */
		do_action('essgrid_duplicate_grid_by_id', $duplicate, $response);

		return $duplicate;
	}

	/**
	 * get grid settings
	 * 
	 * @param int $id
	 * @return false|array
	 * @throws Exception
	 */
	public static function get_grid_settings($id)
	{
		$id = apply_filters('essgrid_get_grid_settings', $id);
		$id = intval($id);
		if ($id === 0) return false;

		$row = Essential_Grid_Db::get_entity('grids')->get( $id );

		$settings = [];
		if (!empty($row['settings'])) {
			$settings = json_decode($row['settings'], true);
			if (json_last_error() !== JSON_ERROR_NONE || !is_array($settings)) {
				$settings = [];
			}
		}
		
		return $settings;
	}

	/**
	 * @param array $data
	 * @param int $id
	 * @return int|false  The number of rows updated, or false on error.
	 * @throws Exception
	 */
	public static function set_grid_settings($data, $id)
	{
		return Essential_Grid_Db::get_entity('grids')->update( [ 'settings' => wp_json_encode($data) ], $id );
	}

	/**
	 * Toggle Favorite State of Grid
	 */
	public static function toggle_favorite_by_id($id)
	{
		global $wpdb;
		
		$return = [
			'error' => esc_attr__('Grid not found', 'essential-grid'),
			'data' => '',
		];

		$settings = self::get_grid_settings($id);
		if ($settings === false) return $return;

		if (!isset($settings['favorite']) || $settings['favorite'] == 'false') {
			$settings['favorite'] = 'true';
		} else {
			$settings['favorite'] = 'false';
		}

		$result = self::set_grid_settings($settings, $id);
		if (!$result) {
			$return['error'] = esc_attr__('Grid could not be changed!', 'essential-grid');
			return $return;
		}

		return [
			'error' => false,
			'data' => ['settings' => $settings],
		];
	}

	/**
	 * Handle Ajax Requests
	 */
	public static function on_ajax_action()
	{
		$clientAction = self::getPostVar("client_action", false);
		$action_allowed = false;
		$no_permission_actions = [
			'load_more_items',
			'load_more_content',
			'get_grid_search_ids',
			'load_post_content',
		];
		
		$current_user = wp_get_current_user();
		if ( 0 != $current_user->ID ) {
			switch (self::$menuRole) {
				case self::ROLE_AUTHOR:
					$roles = ['administrator', 'editor', 'author'];
					break;
				case self::ROLE_EDITOR:
					$roles = ['administrator', 'editor'];
					break;
				case self::ROLE_ADMIN:
					$roles = ['administrator'];
					break;
				default:
					$roles = [];
			}
			if (is_multisite() && is_super_admin()) $current_user->roles = ['administrator'];
			if (!empty(array_intersect($roles, $current_user->roles))) $action_allowed = true;
		}
		
		//allow no_permission_actions for all
		if (in_array($clientAction, $no_permission_actions)) $action_allowed = true;
		
		try {
			$token = self::getPostVar('token', false);

			//verify the token
			$isVerified = wp_verify_nonce($token, 'Essential_Grid_actions');

			$error = false;
			if ($action_allowed && $isVerified) {
				$data = apply_filters('essgrid_on_ajax_action_data', self::getPostVar("data", false));
				
				switch ($clientAction) {
						
					case 'add_widget_area':
						$wa = new Essential_Grid_Widget_Areas();
						$result = $wa->add_new_sidebar($data);
						if ($result === true) {
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Widget Area successfully created!", 'essential-grid'), ['data' => true, 'is_redirect' => true, 'redirect_url' => self::getSubViewUrl(Essential_Grid_Admin::VIEW_SUB_WIDGET_AREA_AJAX)]);
						} else {
							$error = is_string($result) ? $result : esc_attr__('Failed to create Widget Area', 'essential-grid');
						}
						break;
						
					case 'edit_widget_area':
						$wa = new Essential_Grid_Widget_Areas();
						$result = $wa->edit_widget_area_by_handle($data);
						if ($result === true) {
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Widget Area successfully changed!", 'essential-grid'), ['data' => true]);
						} else {
							$error = is_string($result) ? $result : esc_attr__('Failed to change Widget Area', 'essential-grid');
						}
						break;
						
					case 'remove_widget_area':
						$wa = new Essential_Grid_Widget_Areas();
						$result = $wa->remove_widget_area_by_handle($data['handle']);
						if ($result === true) {
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Widget Area successfully removed!", 'essential-grid'), ['data' => true]);
						} else {
							$error = is_string($result) ? $result : esc_attr__('Failed to change Widget Area', 'essential-grid');
						}
						break;
						
					case 'get_preview_html_markup':
						$result = self::output_demo_skin_html($data);
						if (isset($result['error'])) {
							Essential_Grid::ajaxResponseData($result);
						} else {
							$html_result = $result['html'];
							if (empty($html_result)) {
								$html_result = Essential_Grid_Admin::empty_grid_markup();
							}
							Essential_Grid::ajaxResponseData(["data" => ['html' => $html_result, 'preview' => self::getVar($result, 'preview')]]);
						}
						break;
						
					case 'save_color_preset':
						$presets = (isset($data['presets'])) ? $data['presets'] : [];
						$color_presets = ESGColorpicker::save_color_presets($presets);
						Essential_Grid::ajaxResponseData(['presets' => $color_presets]);
						break;
						
					case 'save_search_settings':
						if (!empty($data)) {
							update_option('esg-search-settings', $data);
						}
						Essential_Grid::ajaxResponseSuccess(esc_attr__("Search Settings succesfully saved!", 'essential-grid'));
						break;
						
					case 'update_general_settings':
						$result = self::savePluginPermission($data['permission']);
						if ($result !== true) {
							$error = esc_attr__("Global Settings has not changed!", 'essential-grid');
							break;
						}

						$cur_query = get_option('tp_eg_query_type', 'wp_query');
						if ($cur_query !== $data['query_type']) {
							//delete cache
							self::clear_transients('ess_grid_trans_');
						}

						update_option('tp_eg_output_protection', self::getVar($data, 'protection'));
						update_option('tp_eg_tooltips', self::getVar($data, 'tooltips'));
						update_option('tp_eg_use_cache', self::getVar($data, 'use_cache'));
						update_option('tp_eg_query_type', self::getVar($data, 'query_type'));
						update_option('tp_eg_use_lightbox', self::getVar($data, 'use_lightbox'));
						update_option('tp_eg_use_crossorigin', self::getVar($data, 'use_crossorigin'));
						update_option('tp_eg_overview_show_grid_info', self::getVar($data, 'overview_show_grid_info'));
						update_option('tp_eg_frontend_nonce_check', self::getVar($data, 'frontend_nonce_check'));
						update_option('tp_eg_post_date', self::getVar($data, 'post_date'));
						update_option('tp_eg_show_stream_failure_msg', self::getVar($data, 'show_stream_failure_msg'));
						update_option('tp_eg_stream_failure_custom_msg', self::getVar($data, 'stream_failure_custom_msg'));
						update_option('tp_eg_enable_post_meta', self::getVar($data, 'enable_post_meta'));
						update_option('tp_eg_enable_custom_post_type', self::getVar($data, 'enable_custom_post_type'));
						update_option('tp_eg_enable_extended_search', self::getVar($data, 'enable_extended_search'));
						update_option('tp_eg_global_default_img', self::getVar($data, 'global_default_img'));
						update_option('tp_eg_no_filter_match_message', self::getVar($data, 'no_filter_match_message'));
						update_option('tp_eg_global_enable_pe7', self::getVar($data, 'enable_pe7'));
						update_option('tp_eg_global_enable_fontello', self::getVar($data, 'enable_fontello'));
						update_option('tp_eg_global_enable_font_awesome', self::getVar($data, 'enable_font_awesome'));
						update_option('tp_eg_enable_youtube_nocookie', self::getVar($data, 'enable_youtube_nocookie'));
						Essential_Grid_Base::setExcludePostTypes(self::getVar($data, 'exclude_post_types'));
						Essential_Grid_Base::setJsToFooter(self::getVar($data, 'js_to_footer'));

						Essential_Grid::ajaxResponseSuccess(esc_attr__("Global Settings succesfully saved!", 'essential-grid'), true);
						break;
						
					case 'dismiss_dynamic_notice':
						if (trim($data['id']) !== 'DISCARD') {
							$notices_discarded = get_option('essential-notices-dc', []);
							$notices_discarded[] = esc_attr(trim($data['id']));
							update_option('essential-notices-dc', $notices_discarded);
						} else {
							update_option('essential-deact-notice', false);
						}
						Essential_Grid::ajaxResponseSuccess(esc_attr__(".", 'essential-grid'));
						break;
						
					case 'update_create_grid':
						$result = self::update_create_grid($data);
						if ($result !== true) {
							$error = $result;
						} else {
							if (isset($data['id']) && intval($data['id']) > 0) {
								self::clear_transients('ess_grid_trans_full_grid_' . $data['id']);
								Essential_Grid::ajaxResponseSuccess(esc_attr__("Grid successfully saved/changed!", 'essential-grid'), true);
							} else {
								$grid_id = Essential_Grid_Db::get_entity('grids')->get_insert_id();
								Essential_Grid::ajaxResponseSuccess(esc_attr__("Grid successfully saved/changed!", 'essential-grid'), ['data' => true, 'is_redirect' => false, 'redirect_url' => self::getViewUrl(Essential_Grid_Admin::VIEW_OVERVIEW), 'grid_id' => $grid_id]);
							}
						}
						break;

					case 'update_grid_name':
						$result = self::update_grid_name($data);
						if ($result !== true) {
							$error = $result;
						} else {
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Grid title updated", 'essential-grid'), ['name' => $data['name']]);
						}
						break;
						
					case 'delete_grid':
						$result = self::delete_grid_by_id($data);
						if ($result !== true)
							$error = $result;
						else
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Grid deleted", 'essential-grid'), ['data' => true, 'redirect_url' => self::getViewUrl(Essential_Grid_Admin::VIEW_OVERVIEW)]);
						break;
						
					case 'duplicate_grid':
						$result = self::duplicate_grid_by_id($data);
						if (!is_array($result)) {
							$error = $result;
						} else {
							// duplicate return raw data
							// convert it to array
							$result['params'] = json_decode($result['params'], true);
							$result['postparams'] = json_decode($result['postparams'], true);
							$result['layers'] = json_decode($result['layers'], true);
							$result['settings'] = json_decode($result['settings'], true);
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Grid duplicated", 'essential-grid'), ['grid' => $result]);
						}
						break;
						
					case 'update_create_item_skin':
						$result = Essential_Grid_Item_Skin::update_save_item_skin($data);
						if ($result !== true) {
							$error = $result;
						} else {
							if (isset($data['id']) && intval($data['id']) > 0)
								Essential_Grid::ajaxResponseSuccess(esc_attr__("Item Skin changed", 'essential-grid'), ['data' => true]);
							else
								Essential_Grid::ajaxResponseSuccess(esc_attr__("Item Skin created/changed", 'essential-grid'), ['data' => true, 'is_redirect' => true, 'redirect_url' => self::getViewUrl("", "", 'essential-' . Essential_Grid_Admin::VIEW_SUB_ITEM_SKIN_OVERVIEW)]);
						}
						break;
						
					case 'update_custom_css':
						if (isset($data['global_css'])) {
							Essential_Grid_Global_Css::set_global_css_styles($data['global_css']);
							Essential_Grid::ajaxResponseSuccess(esc_attr__("CSS saved!", 'essential-grid'), '');
						} else {
							$error = esc_attr__("No CSS Received", 'essential-grid');
						}
						break;
						
					case 'delete_item_skin':
						if (!is_array($data)) $data = [];
						$result = Essential_Grid_Item_Skin::delete_item_skin_by_id($data);
						if ($result !== true)
							$error = $result;
						else
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Item Skin deleted", 'essential-grid'), ['data' => true]);
						break;
						
					case 'duplicate_item_skin':
						if (!is_array($data)) $data = [];
						$result = Essential_Grid_Item_Skin::duplicate_item_skin_by_id($data);
						if ($result !== true)
							$error = $result;
						else
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Item Skin duplicated", 'essential-grid'), ['data' => true, 'is_redirect' => true, 'redirect_url' => self::getViewUrl("", "", 'essential-' . Essential_Grid_Admin::VIEW_SUB_ITEM_SKIN_OVERVIEW)]);
						break;
						
					case 'star_item_skin':
						if (!is_array($data)) $data = [];
						$result = Essential_Grid_Item_Skin::star_item_skin_by_id($data);
						if ($result !== true) {
							$error = $result;
						} else {
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Favorite Changed", 'essential-grid'), ['data' => true]);
						}
						break;
						
					case 'update_create_item_element':
						$result = Essential_Grid_Item_Element::update_create_essential_item_element($data);
						if ($result !== true) {
							$error = $result;
						} else {
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Item Element created/changed", 'essential-grid'), ['data' => true]);
						}
						break;
						
					case 'check_item_element_existence':
						$handle = self::getVar($data, 'name');
						if (trim($handle) == '') {
							Essential_Grid::ajaxResponseData( [ 'data' => ['existence' => esc_attr__('Chosen name is too short', 'essential-grid')] ] );
						}
						
						$handle = sanitize_title( $handle );
						$element = Essential_Grid_Db::get_entity('elements')->get_by_handle($handle);
						Essential_Grid::ajaxResponseData( [ "data" => [ 'existence' => (!empty($element) ? 'true' : 'false') ] ] );
						break;
						
					case 'get_predefined_elements':
						$elements = Essential_Grid_Item_Element::getElementsForJavascript();
						$html_elements = Essential_Grid_Item_Element::prepareDefaultElementsForEditor();
						$html_elements .= Essential_Grid_Item_Element::prepareTextElementsForEditor();
						Essential_Grid::ajaxResponseData(["data" => ['elements' => $elements, 'html' => $html_elements]]);
						break;
						
					case 'delete_predefined_elements':
						$result = Essential_Grid_Item_Element::delete_element_by_handle($data);
						if ($result !== true) {
							$error = $result;
						} else {
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Item Element successfully deleted", 'essential-grid'), ['data' => true]);
						}
						break;
						
					case 'update_create_navigation_skin_css':
						$nav = new Essential_Grid_Navigation();
						$result = $nav->update_create_navigation_skin_css($data);
						if ($result !== true) {
							$error = $result;
						} else {
							$skin_css = Essential_Grid_Navigation::output_navigation_skins();
							$skins = Essential_Grid_Navigation::get_essential_navigation_skins();
							$select = '';
							foreach ($skins as $skin) {
								$select .= '<option value="' . esc_attr($skin['handle']) . '">' . esc_html($skin['name']) . '</option>' . "\n";
							}
							if (isset($data['sid']) && intval($data['sid']) > 0)
								Essential_Grid::ajaxResponseSuccess(esc_attr__("Navigation Skin successfully changed!", 'essential-grid'), ['css' => $skin_css, 'select' => $select, 'default_skins' => $skins]);
							else
								Essential_Grid::ajaxResponseSuccess(esc_attr__("Navigation Skin successfully created", 'essential-grid'), ['css' => $skin_css, 'select' => $select, 'default_skins' => $skins]);
						}
						break;
						
					case 'delete_navigation_skin_css':
						$result = Essential_Grid_Navigation::delete_navigation_skin_css($data);
						if ($result !== true) {
							$error = $result;
						} else {
							$skin_css = Essential_Grid_Navigation::output_navigation_skins();
							$skins = Essential_Grid_Navigation::get_essential_navigation_skins();
							$select = '';
							foreach ($skins as $skin) {
								$select .= '<option value="' . esc_attr($skin['handle']) . '">' . esc_html($skin['name']) . '</option>' . "\n";
							}
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Navigation Skin successfully deleted!", 'essential-grid'), ['css' => $skin_css, 'select' => $select, 'default_skins' => $skins]);
						}
						break;
						
					case 'get_post_meta_html_for_editor':
						if (!isset($data['post_id']) || intval($data['post_id']) == 0) {
							$error = esc_attr__('No Post ID/Wrong Post ID!', 'essential-grid');
							break;
						}
						if (!isset($data['grid_id']) || intval($data['grid_id']) == 0) {
							$error = esc_attr__('Please save the grid first to use this feature!', 'essential-grid');
							break;
						}

						$post = get_post($data['post_id']);
						$disable_advanced = true; //nessecary, so that only normal things can be changed in preview mode
						if (!empty($post)) {
							$grid_id = $data['grid_id'];
							ob_start();
							require_once(ESG_PLUGIN_ADMIN_PATH . '/views/elements/' . self::VIEW_META_BOX . '.php');
							$content = ob_get_clean();

							Essential_Grid::ajaxResponseData(["data" => ['html' => $content]]);
						} else {
							$error = esc_attr__('Post not found!', 'essential-grid');
						}
						break;
						
					case 'update_post_meta_through_editor':
						$result = false;

						if (!empty($data['metas'])) {
							foreach ($data['metas'] as $meta) {
								if (!isset($meta['post_id']) || intval($meta['post_id']) == 0) continue;
								if (!isset($meta['grid_id']) || intval($meta['grid_id']) == 0) continue;

								//set the cobbles setting to the post
								$cobbles = json_decode(get_post_meta($meta['post_id'], 'eg_cobbles', true), true);
								$cobbles[$meta['grid_id']]['cobbles'] = $meta['eg_cobbles_size'];
								$cobbles = wp_json_encode($cobbles);
								update_post_meta($meta['post_id'], 'eg_cobbles', $cobbles);


								//set the use_skin setting to the post
								$use_skin = json_decode(get_post_meta($meta['post_id'], 'eg_use_skin', true), true);
								$use_skin[$meta['grid_id']]['use-skin'] = $meta['eg_use_skin'];
								$use_skin = wp_json_encode($use_skin);
								update_post_meta($meta['post_id'], 'eg_use_skin', $use_skin);


								$result = self::custom_meta_box_save($meta['post_id'], $meta, true);
								$result = apply_filters('essgrid_after_update_post_meta_through_editor', $result, $meta);

								self::check_for_transient_deletion($meta['post_id']);
							}
						}

						if ($result === true) {
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Post Meta saved!", 'essential-grid'), []);
						} else {
							$error = esc_attr__('Post not found!', 'essential-grid');
						}
						break;
						
					case 'trigger_post_meta_visibility':
						if (!isset($data['post_id']) || intval($data['post_id']) == 0) {
							$error = esc_attr__('No Post ID/Wrong Post ID!', 'essential-grid');
							break;
						}
						if (!isset($data['grid_id']) || intval($data['grid_id']) == 0) {
							$error = esc_attr__('Please save the grid first to use this feature!', 'essential-grid');
							break;
						}

						$visibility = json_decode(get_post_meta($data['post_id'], 'eg_visibility', true), true);
						$found = false;
						if (!empty($visibility) && is_array($visibility)) {
							foreach ($visibility as $grid => $setting) {
								if ($grid == $data['grid_id']) {
									$visibility[$grid] = !$setting;
									$found = true;
									break;
								}
							}
						}

						if (!$found) {
							$visibility[$data['grid_id']] = false;
						}

						$visibility = wp_json_encode($visibility);
						update_post_meta($data['post_id'], 'eg_visibility', $visibility);
						self::check_for_transient_deletion($data['post_id']);
						Essential_Grid::ajaxResponseSuccess(esc_attr__("Visibility of Post for this Grid changed!", 'essential-grid'), []);
						break;
						
					case 'get_image_by_id':
						if (!isset($data['img_id']) || intval($data['img_id']) == 0) {
							$error = esc_attr__('Wrong Image ID given', 'essential-grid');
						} else {
							$img = wp_get_attachment_image_src($data['img_id'], 'full');
							if ($img !== false) {
								Essential_Grid::ajaxResponseSuccess('', ['url' => $img[0]]);
							} else {
								$error = esc_attr__('Image with given ID does not exist', 'essential-grid');
							}
						}
						break;
						
					case 'activate_purchase_code':
						$result = false;
						if (!empty($data['code'])) {
							$esg_license = new Essential_Grid_License();
							$result = $esg_license->activate_plugin($data['code']);
						} else {
							$error = esc_attr__('The API key, the Purchase Code and the Username need to be set!', 'essential-grid');
						}
						if ($result === true) {
							Essential_Grid::ajaxResponseSuccess(
								esc_attr__('Purchase Code Successfully Activated', 'essential-grid'), 
								[
									'data' => true, 
									'is_redirect' => true, 
									'redirect_url' => self::getViewUrl("", "", 'essential-' . Essential_Grid_Admin::VIEW_START)
								]
							);
						} else {
							if ($result !== false)
								$error = $result;
							else
								$error = esc_attr__('Purchase Code is invalid', 'essential-grid');
						}
						break;
						
					case 'deactivate_purchase_code':
						$esg_license = new Essential_Grid_License();
						$result = $esg_license->deactivate_plugin();
						if ($result === true) {
							Essential_Grid::ajaxResponseSuccess(
								esc_attr__('Successfully removed validation', 'essential-grid'), 
								[
									'data' => true, 
									'is_redirect' => true, 
									'redirect_url' => self::getViewUrl("", "", 'essential-' . Essential_Grid_Admin::VIEW_START)
								]
							);
						} else {
							if ($result !== false)
								$error = $result;
							else
								$error = esc_attr__('Could not remove Validation!', 'essential-grid');
						}
						break;
						
					case 'dismiss_notice':
						Essential_Grid_Base::setValidNotice('false');
						Essential_Grid::ajaxResponseSuccess('.');
						break;
						
					case 'import_default_post_data':
						try {
							require(ESG_PLUGIN_PATH . 'includes/assets/default-posts.php');
							require(ESG_PLUGIN_PATH . 'includes/assets/default-grids-meta-fonts.php');

							if (isset($json_tax)) {
								$import_tax = new PunchPost;
								$import_tax->import_taxonomies($json_tax);
							}

							//insert meta, grids
							$im = new Essential_Grid_Import();
							if (isset($tp_grid_meta_fonts)) {
								$tp_grid_meta_fonts = json_decode($tp_grid_meta_fonts, true);
								$custom_metas = self::getVar($tp_grid_meta_fonts, 'custom-meta');
								if (!empty($custom_metas) && is_array($custom_metas)) {
									$im->import_custom_meta($custom_metas);
								}
							}

							if (isset($json_posts)) {
								$import = new PunchPort;
								$import->set_tp_import_posts($json_posts);
								$import->import_custom_posts();
							}
							Essential_Grid::ajaxResponseSuccess(esc_attr__('Demo data successfully imported', 'essential-grid'), []);
						} catch (Exception $d) {
							$error = esc_attr__('Something wrong, please contact the developer', 'essential-grid');
						}
						break;
						
					case 'get_grid_export_resources':
						if (!isset($data['id'])) {
							$error = esc_attr__('No ID given', 'essential-grid');
						} else {
							$grid = Essential_Grid_Db::get_entity('grids')->get_grid_by_id($data['id'], true);
							if (empty($grid)) {
								$error = esc_attr__('Grid not found', 'essential-grid');
							} else {
								$import = new Essential_Grid_Import();
								$export = new Essential_Grid_Export();
								$item_skin = new Essential_Grid_Item_Skin();
								$nav_skin = new Essential_Grid_Navigation();

								$skins = $item_skin->get_essential_item_skins();
								$navigation_skins = $nav_skin->get_essential_navigation_skins();
								$global_css = Essential_Grid_Global_Css::get_global_css_styles();

								$resources = $export->getGridResources(
									$import,
									[
										'grid' => $grid,
										'skins' => $skins,
										'navigation_skins' => $navigation_skins,
										'global_css' => $global_css
									]
								);
								Essential_Grid::ajaxResponseSuccess(esc_attr__('Grid resources fetched', 'essential-grid'), ['resources' => $resources]);
							}
						}
						break;
						
					case 'export_data':
						$export_grid_name = self::getPostVar('esg_export_grid_name', '');
						$export_grids = self::getPostVar('export-grids-id', false);
						$export_skins = self::getPostVar('export-skins-id', false);
						$export_elements = self::getPostVar('export-elements-id', false);
						$export_navigation_skins = self::getPostVar('export-navigation-skins-id', false);
						$export_global_styles = self::getPostVar('export-global-styles', false);
						$export_custom_meta = self::getPostVar('export-custom-meta-handle', false);
						
						$filename = 'ess_grid.json';
						if (!empty($export_grid_name)) {
							$filename = sanitize_file_name('esg-' . $export_grid_name . '.json');
						}
						
						$export = [];
						$ex = new Essential_Grid_Export();

						//export Grids
						if (!empty($export_grids))
							$export['grids'] = $ex->export_grids($export_grids);

						//export Skins
						if (!empty($export_skins))
							$export['skins'] = $ex->export_skins($export_skins);

						//export Elements
						if (!empty($export_elements))
							$export['elements'] = $ex->export_elements($export_elements);

						//export Navigation Skins
						if (!empty($export_navigation_skins))
							$export['navigation-skins'] = $ex->export_navigation_skins($export_navigation_skins);

						//export Custom Meta
						if (!empty($export_custom_meta))
							$export['custom-meta'] = $ex->export_custom_meta($export_custom_meta);

						//export Global Styles
						if ($export_global_styles == 'on')
							$export['global-css'] = $ex->export_global_styles($export_global_styles);

						$export = apply_filters('essgrid_export_data_before_json_encode', $export);
						
						header('Content-Type: text/json');
						header('Content-Disposition: attachment;filename=' . $filename);
						echo wp_json_encode($export);
						exit();
						break;
						
					case 'import_data':
						if (empty($data['imports'])) {
							$error = esc_attr__('No data for import selected', 'essential-grid');
							break;
						}
						
						try {
							$imported_data = [];
							
							$im = new Essential_Grid_Import();
							
							$temp_d = self::getVar($data, 'imports', [], 'r');
							unset($temp_d['data-grids']);
							unset($temp_d['data-skins']);
							unset($temp_d['data-elements']);
							unset($temp_d['data-navigation-skins']);
							unset($temp_d['data-custom-meta']);
							unset($temp_d['data-global-css']);
							unset($temp_d['import-grids-id']);
							unset($temp_d['import-skins-id']);
							unset($temp_d['import-navigation-skins-id']);
							unset($temp_d['import-custom-meta-handle']);
							unset($temp_d['import-create-taxonomies']);

							//set overwrite data global to class
							$im->set_overwrite_data($temp_d);

							$skins = self::getVar($data, ['imports', 'data-skins'], [], 'r');
							if (!empty($skins) && is_array($skins)) {
								
								foreach ($skins as $key => $skin) {
									$skin = base64_decode($skin);
									$tskin = json_decode($skin, true);
									if (class_exists('Essential_Grid_Plugin_Update')) {
										$tskin = Essential_Grid_Plugin_Update::process_update_216($tskin, true);
									}
									$skins[$key] = $tskin;
								}
								
								if (!empty($skins)) {
									$skins_ids = self::getVar($data, ['imports', 'import-skins-id']);
									$imported_data['skins'] = $im->import_skins($skins, $skins_ids);
								}
							}

							$navigation_skins = self::getVar($data, ['imports', 'data-navigation-skins'], [], 'r');
							if (!empty($navigation_skins) && is_array($navigation_skins)) {
								
								foreach ($navigation_skins as $key => $navigation_skin) {
									$navigation_skin = base64_decode($navigation_skin);
									$tnavigation_skin = json_decode($navigation_skin, true);
									$navigation_skins[$key] = $tnavigation_skin;
								}
								
								if (!empty($navigation_skins)) {
									$navigation_skins_ids = self::getVar($data, ['imports', 'import-navigation-skins-id']);
									$imported_data['nav_skins'] = $im->import_navigation_skins($navigation_skins, $navigation_skins_ids);
								}
							}

							$create_taxonomies = self::getVar($data, ['imports', 'import-create-taxonomies'], 'off', 'r'); 
							$grids = self::getVar($data, ['imports', 'data-grids'], [], 'r');
							if (!empty($grids) && is_array($grids)) {
								
								foreach ($grids as $key => $grid) {
									$grid = base64_decode($grid);
									$tgrid = json_decode($grid, true);
									$grids[$key] = $tgrid;
								}

								if (!empty($grids)) {
									$grids_ids = self::getVar($data, ['imports', 'import-grids-id']);
									$im->import_grids($grids, $grids_ids, true, $imported_data, $create_taxonomies);
								}
							}

							$elements = self::getVar($data, ['imports', 'data-elements'], [], 'r');
							if (!empty($elements) && is_array($elements)) {
								
								foreach ($elements as $key => $element) {
									$element = base64_decode($element);
									$telement = json_decode($element, true);
									$elements[$key] = $telement;
								}
								
								if (!empty($elements)) {
									$elements_ids = self::getVar($data, ['imports', 'import-elements-id']);
									$im->import_elements($elements, $elements_ids);
								}
							}

							$custom_metas = self::getVar($data, ['imports', 'data-custom-meta'], [], 'r');
							if (!empty($custom_metas) && is_array($custom_metas)) {
								
								foreach ($custom_metas as $key => $custom_meta) {
									$custom_meta = base64_decode($custom_meta);
									$tcustom_meta = json_decode($custom_meta, true);
									$custom_metas[$key] = $tcustom_meta;
								}
								
								if (!empty($custom_metas)) {
									$custom_metas_handle = self::getVar($data, ['imports', 'import-custom-meta-handle']);
									$im->import_custom_meta($custom_metas, $custom_metas_handle);
								}
							}

							if (self::getVar($data, ['imports', 'import-global-styles']) == 'on') {
								$global_css = self::getVar($data, ['imports', 'data-global-css']);
								$global_css = base64_decode($global_css);
								$im->import_global_styles($global_css);
							}
							
							do_action('essgrid_import_data', $data);
							
							Essential_Grid::ajaxResponseSuccess(esc_attr__('Successfully imported data', 'essential-grid'), ['is_redirect' => true, 'redirect_url' => self::getViewUrl("", "", 'essential-' . Essential_Grid_Admin::VIEW_START)]);
						} catch (Exception $d) {
							$error = $d->getMessage();
						}
						break;
						
					case 'delete_full_cache':
						self::clear_transients('ess_grid_trans_');
						self::clear_transients('essgrid_');
						Essential_Grid::ajaxResponseSuccess(esc_attr__('Successfully deleted all cache', 'essential-grid'), []);
						break;
						
					case "toggle_grid_favorite":
						if (isset($data['id']) && intval($data['id']) > 0) {
							$return = self::toggle_favorite_by_id($data['id']);
							if ($return['error'] === false) {
								Essential_Grid::ajaxResponseSuccess(esc_attr__('Favorite successfully updated', 'essential-grid'), $return['data']);
							} else {
								$error = $return['error'];
							}
						} else {
							$error = esc_attr__('No ID given', 'essential-grid');
						}
						break;
						
					case "subscribe_to_newsletter":
						if (!empty($data['email'])) {
							$return = ThemePunch_Newsletter::subscribe($data['email']);
							if ($return !== false) {
								if (!isset($return['status']) || $return['status'] === 'error') {
									$error = (!empty($return['message'])) ? $return['message'] : esc_attr__('Invalid Email', 'essential-grid');
								} else {
									Essential_Grid::ajaxResponseSuccess(esc_attr__("Success! Please check your Emails to finish the subscribtion", 'essential-grid'), $return);
								}
							} else {
								$error = esc_attr__('Invalid Email/Could not connect to the Newsletter server', 'essential-grid');
							}
						} else {
							$error = esc_attr__('No Email given', 'essential-grid');
						}
						break;
						
					case "unsubscribe_to_newsletter":
						if (!empty($data['email'])) {
							$return = ThemePunch_Newsletter::unsubscribe($data['email']);
							if ($return !== false) {
								if (!isset($return['status']) || $return['status'] === 'error') {
									$error = (!empty($return['message'])) ? $return['message'] : esc_attr__('Invalid Email', 'essential-grid');
								} else {
									Essential_Grid::ajaxResponseSuccess(esc_attr__("Success! Please check your Emails to finish the process", 'essential-grid'), $return);
								}
							} else {
								$error = esc_attr__('Invalid Email/Could not connect to the Newsletter server', 'essential-grid');
							}
						} else {
							$error = esc_attr__('No Email given', 'essential-grid');
						}
						break;
						
					case "load_library":
						$library = new Essential_Grid_Library();
						$grids = $library->get_tp_template_grids();
						$html = $library->get_library_grids_html($grids);

						if ($html !== false) {
							//set new templates counter back to 0
							$library->set_templates_counter(0);
							
							Essential_Grid::ajaxResponseData($html);
						} else {
							$error = esc_attr__('Library could not be loaded', 'essential-grid');
						}
						break;
						
					case 'import_grid_online':
						$library = new Essential_Grid_Library();
						$uid = self::getVar($data, 'uid');
						$zip = self::getVar($data, 'zip');
						$bg = self::getVar($data, 'bg');
						$addons = self::getVar($data, 'addons', []);

						$return = $library->import_grid($uid, $zip, true);
						if (is_array($return) && !empty($return)) {
							$id = (isset($return['grids_imported'])) ? current($return['grids_imported']) : 1;
							
							//check for non-global addons
							if (is_array($addons) && !empty($addons)) {
								$esg_addons = Essential_Grid_Addons::instance();
								foreach ($addons as $handle) {
									$global = $esg_addons->get_addon_attr($handle, 'global');
									if (!$global) {
										$esg_addons->enable_grid_addon($handle, $id);
									}
								}
							}

							$settings = self::get_grid_settings($id);
							$settings['bg'] = $bg;
							self::set_grid_settings($settings, $id);
							
							$grid = Essential_Grid_Db::get_entity('grids')->get_grid_by_id($id);
							if ($grid !== false) {
								Essential_Grid::ajaxResponseSuccess( esc_attr__( 'Successfully imported Grid', 'essential-grid' ), [ 'grid' => $grid ] );
							} else {
								$error = esc_attr__('Failed to import Grid', 'essential-grid');
							}
						} else {
							$error = is_string($return) ? $return : esc_attr__('Failed to import Grid', 'essential-grid');
						}
						break;
						
					case 'save_metas':
						$m = new Essential_Grid_Meta();
						$lm = new Essential_Grid_Meta_Linking();
						$metas = self::getVar($data, 'CustomMetas');
						$link_meta = self::getVar($data, 'LinkMetas');

						$result = $m->save_all_metas($metas);
						if ($result !== true) {
							$error = $result;
							break;
						}
						$result = $lm->save_all_link_metas($link_meta);
						if ($result !== true) {
							$error = $result;
							break;
						}

						Essential_Grid::ajaxResponseSuccess(esc_attr__("Meta successfully saved!", 'essential-grid'));
						break;

					case 'get_addons_list':
						$grid_id = self::getVar($data, 'grid_id');
						$esg_addons = Essential_Grid_Addons::instance();
						
						if (empty($grid_id)) {
							$esg_addons_list = $esg_addons->get_addons_list();
						} else {
							$esg_addons_list = $esg_addons->get_grid_addons_list($grid_id);
						}

						//set new addons counter back to 0
						$esg_addons->set_addons_counter(0);

						Essential_Grid::ajaxResponseSuccess(esc_attr__("Addons loaded", 'essential-grid'), ['addons' => $esg_addons_list]);
						break;

					case 'install_addon':
						$handle = self::getVar($data, 'addon');
						$esg_addons = Essential_Grid_Addons::instance();
						$return = $esg_addons->install_addon($handle);
						
						if ($return === true) {
							$version = $esg_addons->get_addon_version($handle);
							$data = apply_filters('essgrid_install_addon', [], $handle);
							Essential_Grid::ajaxResponseData([$handle => $data, 'version' => $version]);
						} else {
							$error = ($return === false) ? esc_attr__('AddOn could not be installed!', 'essential-grid') : $return;
						}
						break;
						
					case 'update_addon':
						$handle = self::getVar($data, 'addon');
						$esg_addons = Essential_Grid_Addons::instance();
						$return = $esg_addons->install_addon($handle, true);

						if ($return === true) {
							$version = $esg_addons->get_addon_version($handle);
							$data = apply_filters('essgrid_update_addon', [], $handle);
							Essential_Grid::ajaxResponseData([$handle => $data, 'version' => $version]);
						} else {
							$error = ($return === false) ? esc_attr__('AddOn could not be updated!', 'essential-grid') : $return;
						}
						break;
						
					case 'activate_addon':
						$handle = self::getVar($data, 'addon');
						$enable = self::getVar($data, 'enable');
						$grid_id = self::getVar($data, 'grid_id');
						
						$esg_addons = Essential_Grid_Addons::instance();
						$return = $esg_addons->activate_addon($handle);

						if ($return === true) {
							if ($enable) {
								$global = $esg_addons->get_addon_attr($handle, 'global');
								if ($global) {
									$esg_addons->enable_addon($handle);
								} else {
									$esg_addons->enable_grid_addon($handle, $grid_id);
								}
							}
							$version = $esg_addons->get_addon_version($handle);
							$data = apply_filters('essgrid_activate_addon', [], $handle);
							Essential_Grid::ajaxResponseData([$handle => $data, 'version' => $version]);
						} else {
							$error = ($return === false) ? esc_attr__('AddOn could not be activated!', 'essential-grid') : $return;
						}
						break;
						
					case 'deactivate_addon':
						$handle = self::getVar($data, 'addon');
						$esg_addons = Essential_Grid_Addons::instance();
						$esg_addons->deactivate_addon($handle);

						do_action('essgrid_deactivate_addon', $handle);
						Essential_Grid::ajaxResponseSuccess(__('AddOn deactivated', 'essential-grid'));
						break;

					case 'enable_addon':
						$handle = self::getVar($data, 'addon');
						$grid_id = self::getVar($data, 'grid_id');
						$esg_addons = Essential_Grid_Addons::instance();
						
						$global = $esg_addons->get_addon_attr($handle, 'global');
						if ($global) {
							$return = $esg_addons->enable_addon($handle);
						} else {
							$return = $esg_addons->enable_grid_addon($handle, $grid_id);
						}
						
						if (true === $return) {
							do_action('essgrid_enable_addon', $handle, $grid_id);
							Essential_Grid::ajaxResponseSuccess(__('AddOn enabled', 'essential-grid'));
						} else {
							$error = ($return === false) ? esc_attr__('AddOn could not be activated!', 'essential-grid') : $return;
						}
						break;
						
					case 'disable_addon':
						$handle = self::getVar($data, 'addon');
						$grid_id = self::getVar($data, 'grid_id');
						$esg_addons = Essential_Grid_Addons::instance();
						
						$global = $esg_addons->get_addon_attr($handle, 'global');
						if ($global) {
							$return = $esg_addons->disable_addon($handle);
						} else {
							$return = $esg_addons->disable_grid_addon($handle, $grid_id);
						}

						if ($return) {
							do_action('essgrid_disable_addon', $handle, $grid_id);
							Essential_Grid::ajaxResponseSuccess(__('AddOn disabled', 'essential-grid'));
						} else {
							$error = esc_attr__('AddOn could not be disabled!', 'essential-grid');
						}
						break;

					case 'update_favorites':
						$action = self::getVar($data, 'action', 'add');
						$type = self::getVar($data, 'type', 'grid');
						$id = esc_attr(self::getVar($data, 'id'));

						$favorites = new Essential_Grid_Favorite();
						$favorites->update_favorites($action, $type, $id);

						Essential_Grid::ajaxResponseSuccess(esc_attr__("Favorites updated", 'essential-grid'));
						break;

					case 'check_for_updates':
						$update = new Essential_Grid_Update(ESG_REVISION);
						$update->force = true;
						$version = $update->_retrieve_version_info();
						if ($version !== false) {
							Essential_Grid::ajaxResponseData(['version' => $version]);
						} else {
							$error = esc_attr__('Connection to Update Server Failed!', 'essential-grid');
						}
						break;
						
					case 'check_for_updates_shop':
						$library = new Essential_Grid_Library();
						if ($library->_get_template_list(true)) {
							Essential_Grid::ajaxResponseSuccess(esc_attr__("Templates Library updated", 'essential-grid'));
						} else {
							$error = esc_attr__('Connection to Update Server Failed!', 'essential-grid');
						}
						break;

					case 'tip_dont_show':
						$id = self::getVar($data, 'id');
						if (!empty($id)) {
							$tips = get_option('tp_eg_tips_dont_show', []);
							if (!in_array($id, $tips)) $tips[] = $id;
							update_option('tp_eg_tips_dont_show', $tips);
						}
						
						Essential_Grid::ajaxResponseSuccess(esc_attr__("Tips Data Updated", 'essential-grid'));
						break;

					default:
						$return = apply_filters('essgrid_on_ajax_action', false, $clientAction, $data);
						if ($return) {
							if (is_array($return)) {
								if (isset($return['error'])) {
									$error = $return['error'];
									break;
								}
								if (isset($return['message'])) {
									Essential_Grid::ajaxResponseSuccess($return['message'], $return['data']);
								}
								Essential_Grid::ajaxResponseData(['data' => $return['data']]);
							} else {
								Essential_Grid::ajaxResponseSuccess($return);
							}
						}
						
						$error = true;
						break;
				}
			} else {
				$error = true;
			}
			if ($error !== false) {
				$showError = esc_attr__("Wrong Request!", 'essential-grid');
				if ($error !== true)
					$showError = esc_attr__("Ajax Error: ", 'essential-grid') . $error;
				Essential_Grid::ajaxResponseError($showError, false);
			}
			exit();
		} catch (Exception $e) {
			exit();
		}
	}

	/**
	 * Add Essential Grid Gutenberg Block Category
	 * 
	 * @param array $categories
	 * @param WP_Post $post
	 * @return array
	 */
	public function create_block_category($categories, $post)
	{
		if ($this->in_array_r('essgrid', $categories)) return $categories;
		return array_merge(
			$categories,
			[
				[
					'slug' => 'essgrid',
					'title' => esc_attr__('Essential Grid', 'essential-grid'),
				],
			]
		);
	}

	/**
	 * Check Array for Value
	 *
	 * @param mixed $needle
	 * @param array $haystack
	 * @param bool $strict
	 * @return bool
	 */
	public function in_array_r($needle, $haystack, $strict = false)
	{
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Improve UX for empty grids or when social stream data isn't available
	 * @since: 3.0.12
	 * @return string
	 */
	public static function empty_grid_markup()
	{
		return
			'<div>No posts found for this Grid.<br><br>' .
			'<a id="go-to-source" class="esg-btn esg-purple" href="#">Edit Source Settings</a> ' .
			'<a class="esg-btn esg-purple" href="' . esc_url(admin_url('edit.php')) . '" target="_blank">Create Posts</a>' .
			'</div>';
	}

	/**
	 * add a go premium button to the plugins page
	 **/
	public function add_plugin_action_links($links){
		$links['go_premium'] = '<a href="https://account.essential-grid.com/licenses/pricing/?utm_source=admin&utm_medium=button&utm_campaign=esgusers&utm_content=buykey" target="_blank" style="color: #F7345E; font-weight: 700;">'.esc_html__('Go Premium', 'essential-grid').'</a>';
		return $links;
	}

	/**
	 * add plugin notices to the Slider Essential Grid Plugin at the overview page of plugins
	 **/
	public static function add_plugins_page_notices() {
		$plugins = get_plugins();
		$esg_latest_version = Essential_Grid_Base::getLatestVersion();

		foreach ( $plugins as $plugin_id => $plugin ) {
			$slug = dirname( $plugin_id );
			if ( empty( $slug ) || $slug !== ESG_PLUGIN_SLUG ) {
				continue;
			}
			if ( ! Essential_Grid_Base::isValid() && version_compare( $esg_latest_version, $plugin['Version'], '>' ) ) {
				add_action( 'after_plugin_row_' . $plugin_id, [ 'Essential_Grid_Admin', 'show_purchase_notice' ], 10, 3 );
			}
			break;
		}
	}

	/**
	 * Show message for activation benefits
	 **/
	public static function show_purchase_notice($plugin_file, $plugin_data, $plugin_status)
	{
		$wp_list_table = _get_list_table('WP_Plugins_List_Table');
		$esg_latest_version = Essential_Grid_Base::getLatestVersion();
		?>
		<tr class="plugin-update-tr active">
			<td colspan="<?php echo esc_attr($wp_list_table->get_column_count()); ?>" class="plugin-update colspanchange">
				<div class="update-message notice inline notice-warning notice-alt">
					<p>
						<?php printf(
							/* translators: 1:open link tag to changelog 2:esg version 3:close link tag 4:open link tag to plugin activation 5:open link tag to ESG pricing 6:open link tag to ESG site  */
							esc_html__('There is a new version (%1$s%2$s%3$s) of Essential Grid available. To update directly %4$sregister your license key now%3$s or %5$spurchase a new license key%3$s to access %6$sall premium features%3$s.', 'essential-grid'),
							'<a href="https://www.essential-grid.com/documentation/changelog/?utm_source=admin&utm_medium=wpplugins&utm_campaign=esgusers&utm_content=updateinfo#' . esc_attr($esg_latest_version) . '" target="_blank">',
							esc_attr($esg_latest_version),
							'</a>',
							'<a href="admin.php?page=essential-grid#activateplugin" onclick="#">',
							'<a href="https://account.essential-grid.com/licenses/pricing/?utm_source=admin&utm_medium=wpplugins&utm_campaign=esgusers&utm_content=updateinfo" target="_blank">',
							'<a href="https://www.essential-grid.com/?utm_source=admin&utm_medium=wpplugins&utm_campaign=esgusers&utm_content=updateinfo" target="_blank">'
						);
						?>
					</p>
				</div>
			</td>
		</tr>
		<style>tr[data-slug="essential-grid"] td, tr[data-slug="essential-grid"] th { box-shadow: none !important }</style>
		<?php
	}
}
