<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

use InstagramFeed\Admin\SBI_Support_Tool;
use InstagramFeed\Builder\SBI_Db;
use InstagramFeed\Helpers\Util;
use InstagramFeed\SB_Instagram_Data_Encryption;
use InstagramFeed\SBI_Feed_Cache_Manager;

/**
 * The main function the creates the feed from a shortcode.
 * Can be safely added directly to templates using
 * 'echo do_shortcode( "[instagram-feed]" );'
 *
 * @param array $atts The attributes of the shortcode.
 * @param bool  $preview_settings Whether to use preview settings.
 *
 * @return string The HTML of the feed.
 */
function display_instagram($atts = array(), $preview_settings = false)
{
	do_action('sbi_before_display_instagram');

	$database_settings = sbi_get_database_settings();

	if (
		$database_settings['sb_instagram_ajax_theme'] !== 'on'
		&& $database_settings['sb_instagram_ajax_theme'] !== 'true'
		&& $database_settings['sb_instagram_ajax_theme'] !== '1'
	) {
		wp_enqueue_script('sbi_scripts');
	}

	if ($database_settings['enqueue_css_in_shortcode'] === 'on' || $database_settings['enqueue_css_in_shortcode'] === 'true' || $database_settings['enqueue_css_in_shortcode'] === true) {
		wp_enqueue_style('sbi_styles');
	}

	$moderation_mode = (isset($_GET['sbi_moderation_mode']) && $_GET['sbi_moderation_mode'] === 'true' && current_user_can('edit_posts'));
	if ($moderation_mode) {
		if (is_array($atts)) {
			$atts['doingModerationMode'] = true;
			wp_enqueue_style('sbi_moderation_mode');
		} else {
			$atts = array(
				'doingModerationMode' => true
			);
		}
	}

	$instagram_feed_settings = new SB_Instagram_Settings_Pro($atts, $database_settings, $preview_settings);

	$early_settings = $instagram_feed_settings->get_settings();
	if (empty($early_settings)) {
		$style = current_user_can('manage_instagram_feed_options') ? ' style="display: block;"' : '';
		$id = isset($atts['feed']) ? (int)$atts['feed'] : false;
		if ($id) {
			/* translators: Account user id */
			$message = sprintf(__('Error: No feed with the ID %s found.', 'instagram-feed'), $id);
		} else {
			$message = __('Error: No feed found.', 'instagram-feed');
		}
		ob_start(); ?>
		<div id="sbi_mod_error" <?php echo $style; ?>>
			<span><?php esc_html_e('This error message is only visible to WordPress admins', 'instagram-feed'); ?></span><br/>
			<p><strong><?php echo esc_html($message); ?></strong>
			<p><?php esc_html_e('Please go to the Instagram Feed settings page to create a feed.', 'instagram-feed'); ?></p>
		</div>
		<?php
		$html = ob_get_contents();
		ob_get_clean();
		return $html;
	}

	$instagram_feed_settings->set_feed_type_and_terms();

	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();
	$settings = $instagram_feed_settings->get_settings();

	$feed_cache_manager = new SBI_Feed_Cache_Manager('sbi_feed_update', $settings['caching_type']);

	$settings['caching_type'] = $feed_cache_manager->get_caching_type();

	$customizer = $settings['customizer'];

	if (empty($settings['sources']) && empty($settings['connected_accounts'])) {
		$style = current_user_can('manage_instagram_feed_options') ? ' style="display: block;"' : '';
		ob_start(); ?>
		<div id="sbi_mod_error" <?php echo $style; ?>>
			<span><?php esc_html_e('This error message is only visible to WordPress admins', 'instagram-feed'); ?></span><br/>
			<p><strong><?php esc_html_e('Error: No connected account.', 'instagram-feed'); ?></strong>
			<p><?php esc_html_e('Please go to the Instagram Feed settings page to connect an account.', 'instagram-feed'); ?></p>
		</div>
		<?php
		$html = ob_get_contents();
		ob_get_clean();
		return $html;
	}

	if (!$moderation_mode && ($settings['mediavine'] === 'on' || $settings['mediavine'] === 'true' || $settings['mediavine'] === true)) {
		wp_enqueue_script('sb_instagram_mediavine_scripts');
	}

	$feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();

	$instagram_feed = new SB_Instagram_Feed_Pro($transient_name);

	$instagram_feed->set_cache($instagram_feed_settings->get_cache_time_in_seconds(), $settings);

	if (SB_Instagram_Feed_Locator::should_do_locating()) {
		$instagram_feed->add_report('doing feed locating');

		$feed_details = array(
			'feed_id' => $transient_name,
			'atts' => $atts,
			'location' => array(
				'post_id' => get_the_ID(),
				'html' => 'unknown'
			)
		);
		$locator = new SB_Instagram_Feed_Locator($feed_details);
		$locator->add_or_update_entry();
	}

	if ($settings['caching_type'] === 'permanent' && empty($settings['doingModerationMode'])) {
		$instagram_feed->add_report('trying to use permanent cache');
		$post_cache_success = $instagram_feed->maybe_set_post_data_from_backup();

		if (!$post_cache_success) {
			$num_needed = $settings['num'];
			if (!empty($settings['whitelist'])) {
				$num_needed = $settings['whitelist_num'];
			}
			$raised_num_settings = $settings;
			$raised_num_settings['num'] = 100;

			if ($instagram_feed->need_posts($num_needed) && $instagram_feed->can_get_more_posts()) {
				while ($instagram_feed->need_posts($num_needed) && $instagram_feed->can_get_more_posts()) {
					$instagram_feed->add_remote_posts($raised_num_settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
				}
				$instagram_feed->cache_feed_data($instagram_feed_settings->get_cache_time_in_seconds(), true);
			}
		}
	} elseif ($settings['caching_type'] === 'background') {
		$instagram_feed->add_report('background caching used');
		if ($instagram_feed->regular_cache_exists()) {
			$instagram_feed->add_report('setting posts from cache');
			$instagram_feed->set_post_data_from_cache();
		}

		if ($instagram_feed->need_to_start_cron_job()) {
			$instagram_feed->add_report('setting up feed for cron cache');
			$to_cache = array(
				'atts' => $atts,
				'last_requested' => time(),
			);

			$instagram_feed = SB_Instagram_Cron_Updater_Pro::do_single_feed_cron_update($instagram_feed_settings, $to_cache, $atts, false);
			$instagram_feed->set_cache($instagram_feed_settings->get_cache_time_in_seconds(), $settings);
			$instagram_feed->set_post_data_from_cache();
		} elseif ($instagram_feed->should_update_last_requested()) {
			$instagram_feed->add_report('updating last requested');
			$to_cache = array(
				'last_requested' => time(),
			);

			$instagram_feed->set_cron_cache($to_cache, $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled']);
		}
	} elseif ($instagram_feed->regular_cache_exists()) {
		$instagram_feed->add_report('page load caching used and regular cache exists');
		$instagram_feed->set_post_data_from_cache();

		if ($instagram_feed->need_posts($settings['num']) && $instagram_feed->can_get_more_posts()) {
			while ($instagram_feed->need_posts($settings['num']) && $instagram_feed->can_get_more_posts()) {
				$instagram_feed->add_remote_posts($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
			}

			$instagram_feed->cache_feed_data($instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled']);
		}
	} else {
		$instagram_feed->add_report('no feed cache found');

		while ($instagram_feed->need_posts($settings['num']) && $instagram_feed->can_get_more_posts()) {
			$instagram_feed->add_remote_posts($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
		}

		if ($instagram_feed->out_of_next_pages() || $instagram_feed->should_look_for_db_only_posts($settings, $feed_type_and_terms)) {
			$instagram_feed->add_report('Adding Db only posts');
			$instagram_feed->add_db_only_posts($transient_name, $settings, $feed_type_and_terms);
		}

		if (!$instagram_feed->should_use_backup()) {
			$instagram_feed->cache_feed_data($instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled']);
		} elseif ($instagram_feed->should_cache_error()) {
			$cache_time = min($instagram_feed_settings->get_cache_time_in_seconds(), 15 * 60);
			$instagram_feed->add_report('caching an error');
			$instagram_feed->cache_feed_data($cache_time, false);
		}
	}

	if ($instagram_feed->should_use_backup()) {
		$instagram_feed->add_report('trying to use backup');
		$instagram_feed->maybe_set_post_data_from_backup();
		if (!empty($settings['hidephotos'])) {
			$instagram_feed->add_report('filtering backup');

			$post_data = $instagram_feed->get_post_data();
			$was_one_post = count($post_data) > 0;
			$post_data = $instagram_feed->process_hide_photos($post_data, $settings);
			$is_one_post_after_filter = count($post_data) > 0;

			$instagram_feed->set_post_data($post_data);
			if ($was_one_post && !$is_one_post_after_filter) {
				global $sb_instagram_posts_manager;
				$error_message_return = array(
					'error_message' => __('Error: No posts found.', 'instagram-feed'),
					'admin_only' => __('No posts found that weren\'t hidden by your moderation settings.', 'instagram-feed'),
					'frontend_directions' => '',
					'backend_directions' => ''
				);
				$sb_instagram_posts_manager->maybe_set_display_error('configuration', $error_message_return);
				$cache_time = min($instagram_feed_settings->get_cache_time_in_seconds(), 15 * 60);
				$instagram_feed->add_report('caching an error');
				$instagram_feed->cache_feed_data($cache_time, false, true);
			}
		}


		$instagram_feed->maybe_set_header_data_from_backup();
		$header_data = $instagram_feed->get_header_data();
	}

	// if need a header.
	if ($instagram_feed->need_header($settings, $feed_type_and_terms)) {
		if (empty($header_data)) {
			if (($instagram_feed->should_use_backup() || $settings['caching_type'] === 'permanent') && empty($settings['doingModerationMode'])) {
				$instagram_feed->add_report('trying to set header from backup');
				$header_cache_success = $instagram_feed->maybe_set_header_data_from_backup();
				if (!$header_cache_success) {
					$instagram_feed->set_remote_header_data($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
					$header_data = $instagram_feed->get_header_data();
					if ($settings['stories'] && !empty($header_data)) {
						$instagram_feed->set_remote_stories_data($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
					}
					$instagram_feed->cache_header_data($instagram_feed_settings->get_cache_time_in_seconds(), true);
				} else {
					$instagram_feed->cache_header_data($instagram_feed_settings->get_cache_time_in_seconds(), false);
				}
			} elseif ($instagram_feed->regular_header_cache_exists()) {
				$instagram_feed->add_report('page load caching used and regular header cache exists');
				$instagram_feed->set_header_data_from_cache();
			} else {
				$instagram_feed->add_report('no header cache exists');
				$instagram_feed->set_remote_header_data($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
				$header_data = $instagram_feed->get_header_data();
				if ($settings['stories'] && !empty($header_data)) {
					$instagram_feed->set_remote_stories_data($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
				}
				$instagram_feed->cache_header_data($instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled']);
			}
		}
	} else {
		$showheader = ($settings['showheader'] === 'on' || $settings['showheader'] === 'true' || $settings['showheader'] === true);

		if ($showheader) {
			$settings['generic_header'] = true;
			$instagram_feed->set_generic_header_data($feed_type_and_terms);
			$instagram_feed->add_report('using generic header');
		} else {
			$instagram_feed->add_report('no header needed');
		}
	}

	$settings['feed_avatars'] = array();
	if ($instagram_feed->need_avatars($settings)) {
		$instagram_feed->set_up_feed_avatars($instagram_feed_settings->get_connected_accounts_in_feed(), $feed_type_and_terms);
		$settings['feed_avatars'] = $instagram_feed->get_username_avatars();
	}

	if ($settings['resizeprocess'] === 'page') {
		$instagram_feed->add_report('resizing images for post set');
		$post_data = $instagram_feed->get_post_data();
		$post_data = array_slice($post_data, 0, $settings['num']);
		$fill_in_timestamp = date('Y-m-d H:i:s', time() + 150);

		$image_sizes = array(
			'personal' => array('full' => 640, 'low' => 320, 'thumb' => 150),
			'business' => array('full' => 640, 'low' => 320, 'thumb' => 150)
		);

		$post_set = new SB_Instagram_Post_Set($post_data, $transient_name, $fill_in_timestamp, $image_sizes);

		$post_set->maybe_save_update_and_resize_images_for_posts();
	}

	if ($settings['disable_js_image_loading'] || $settings['imageres'] !== 'auto') {
		global $sb_instagram_posts_manager;
		$post_data = $instagram_feed->get_post_data();

		if (!$sb_instagram_posts_manager->image_resizing_disabled($feed_type_and_terms)) {
			$image_ids = array();
			foreach ($post_data as $post) {
				$image_ids[] = SB_Instagram_Parse::get_post_id($post);
			}
			$resized_images = SB_Instagram_Feed::get_resized_images_source_set($image_ids, 0, $transient_name);

			$instagram_feed->set_resized_images($resized_images);
		}
	}

	$instagram_feed->maybe_offset_posts((int)$settings['offset']);

	return $instagram_feed->get_the_feed_html($settings, $atts, $instagram_feed_settings->get_feed_type_and_terms(), $instagram_feed_settings->get_connected_accounts_in_feed());
}

add_shortcode('instagram-feed', 'display_instagram');
add_filter('widget_text', 'do_shortcode');

/**
 * For efficiency, local versions of image files available for the images actually displayed on the page
 * are added at the end of the feed.
 *
 * @param object $instagram_feed The feed object.
 * @param string $feed_id The feed ID.
 */
function sbi_add_resized_image_data($instagram_feed, $feed_id)
{
	global $sb_instagram_posts_manager;

	if (!$sb_instagram_posts_manager->image_resizing_disabled($feed_id)) {
		if ($instagram_feed->should_update_last_requested()) {
			SB_Instagram_Feed::update_last_requested($instagram_feed->get_image_ids_post_set());
		}
	}

	?>
	<span class="sbi_resized_image_data" data-feed-id="<?php echo esc_attr($feed_id); ?>"
		  data-resized="<?php echo esc_attr(sbi_json_encode(SB_Instagram_Feed::get_resized_images_source_set($instagram_feed->get_image_ids_post_set(), 0, $feed_id))); ?>">
	</span>
	<?php
}

add_action('sbi_before_feed_end', 'sbi_add_resized_image_data', 10, 2);

/**
 * Conditionally adds palette styles to Instagram feed posts.
 *
 * @param array $posts An array of Instagram feed posts.
 * @param array $settings An array of settings for the Instagram feed.
 *
 * @return void
 */
function sbi_maybe_palette_styles($posts, $settings)
{
	$custom_palette_class = trim(SB_Instagram_Display_Elements::get_palette_class($settings));
	if (SB_Instagram_Display_Elements::palette_type($settings) !== 'custom') {
		return;
	}

	$feed_selector = '.' . $custom_palette_class;
	$header_selector = '.' . trim(SB_Instagram_Display_Elements::get_palette_class($settings, '_header'));
	$custom_colors = array(
		'bg1' => $settings['custombgcolor1'],
		'text1' => $settings['customtextcolor1'],
		'text2' => $settings['customtextcolor2'],
		'link1' => $settings['customlinkcolor1'],
		'button1' => $settings['custombuttoncolor1'],
		'button2' => $settings['custombuttoncolor2']
	);
	?>
	<style type="text/css">
		<?php if (! empty($custom_colors['bg1'])) : ?>
			<?php echo $header_selector ?>
		,
		#sb_instagram<?php echo $feed_selector ?>,
		#sbi_lightbox .sbi_lb-outerContainer .sbi_lb-dataContainer,
		#sbi_lightbox .sbi_lightbox_tooltip,
		#sbi_lightbox .sbi_share_close {
			background: <?php echo esc_html($custom_colors['bg1']); ?> !important;
		}

		<?php endif; ?>
		<?php if (! empty($custom_colors['text1'])) : ?>
		#sb_instagram<?php echo $feed_selector ?> .sbi_caption,
		#sbi_lightbox .sbi_lb-outerContainer .sbi_lb-dataContainer .sbi_lb-details .sbi_lb-caption,
		#sbi_lightbox .sbi_lb-outerContainer .sbi_lb-dataContainer .sbi_lb-number,
		#sbi_lightbox.sbi_lb-comments-enabled .sbi_lb-commentBox p {
			color: <?php echo esc_html($custom_colors['text1']); ?> !important;
		}

		<?php endif; ?>
		<?php if (! empty($custom_colors['text2'])) : ?>
			<?php echo $header_selector ?>
		.sbi_bio,
		#sb_instagram<?php echo $feed_selector ?> .sbi_meta {
			color: <?php echo esc_html($custom_colors['text2']); ?> !important;
		}

		<?php endif; ?>
		<?php if (! empty($custom_colors['link1'])) : ?>
			<?php echo $header_selector ?>
		a,
			<?php echo $header_selector ?> a h3,
		#sb_instagram<?php echo $feed_selector ?> .sbi_expand a,
		#sbi_lightbox .sbi_lb-outerContainer .sbi_lb-dataContainer .sbi_lb-details a,
		#sbi_lightbox.sbi_lb-comments-enabled .sbi_lb-commentBox .sbi_lb-commenter {
			color: <?php echo esc_html($custom_colors['link1']); ?> !important;
		}

		<?php endif; ?>
		<?php if (! empty($custom_colors['button1'])) : ?>
		#sb_instagram<?php echo $feed_selector ?> #sbi_load .sbi_load_btn {
			background: <?php echo esc_html($custom_colors['button1']); ?> !important;
		}

		<?php endif; ?>
		<?php if (! empty($custom_colors['button2'])) : ?>
		#sb_instagram<?php echo $feed_selector ?> #sbi_load .sbi_follow_btn a {
			background: <?php echo esc_html($custom_colors['button2']); ?> !important;
		}

		<?php endif; ?>
	</style>
	<?php
}

add_action('sbi_after_feed', 'sbi_maybe_palette_styles', 10, 2);

/**
 * Conditionally adds hover styles to Instagram feed buttons.
 *
 * @param array $posts An array of Instagram feed posts.
 * @param array $settings An array of settings for the Instagram feed.
 *
 * @return void
 */
function sbi_maybe_button_hover_styles($posts, $settings)
{
	$follow_hover_color = str_replace('#', '', SB_Instagram_Display_Elements::get_follow_hover_color($settings));
	$load_hover_color = str_replace('#', '', SB_Instagram_Display_Elements::get_load_button_hover_color($settings));

	if (empty($load_hover_color) && empty($follow_hover_color)) {
		return;
	}

	?>
	<style type="text/css">
		<?php if (! empty($load_hover_color)) : ?>
		#sb_instagram #sbi_load .sbi_load_btn:focus,
		#sb_instagram #sbi_load .sbi_load_btn:hover {
			outline: none;
			box-shadow: inset 0 0 20px 20px<?php echo sanitize_hex_color('#' . $load_hover_color); ?>;
		}

		<?php endif; ?>
		<?php if (! empty($follow_hover_color)) : ?>
		#sb_instagram .sbi_follow_btn a:hover,
		#sb_instagram .sbi_follow_btn a:focus {
			outline: none;
			box-shadow: inset 0 0 10px 20px<?php echo sanitize_hex_color('#' . $follow_hover_color); ?>;
		}

		<?php endif; ?>
	</style>
	<?php
}

add_action('sbi_after_feed', 'sbi_maybe_button_hover_styles', 10, 2);

/**
 * Called after the load more button is clicked using admin-ajax.php
 */
function sbi_get_next_post_set()
{
	if (empty($_POST['feed_id']) || !preg_match('/^(sbi|\*)/', $_POST['feed_id'])) {
		wp_send_json_error('invalid feed ID');
	}

	$feed_id = sanitize_text_field($_POST['feed_id']);
	$post_id = isset($_POST['post_id']) && $_POST['post_id'] !== 'unknown' ? intval($_POST['post_id']) : 'unknown';

	$atts_raw = isset($_POST['atts']) ? json_decode(stripslashes($_POST['atts']), true) : array();
	$atts = is_array($atts_raw) ? SB_Instagram_Settings_Pro::pro_sanitize_raw_atts($atts_raw) : array();

	$database_settings = sbi_get_database_settings();
	$instagram_feed_settings = new SB_Instagram_Settings_Pro($atts, $database_settings);

	$instagram_feed_settings->set_feed_type_and_terms();
	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();

	$nonce = isset($_POST['locator_nonce']) ? sanitize_text_field(wp_unslash($_POST['locator_nonce'])) : '';
	if (!wp_verify_nonce($nonce, 'sbi-locator-nonce-' . $post_id . '-' . $transient_name) || $transient_name !== $feed_id) {
		wp_send_json_error('nonce check failed, details do not match');
	}

	$location = isset($_POST['location']) && in_array($_POST['location'], array('header', 'footer', 'sidebar', 'content'), true) ? sanitize_text_field($_POST['location']) : 'unknown';
	$feed_details = array(
		'feed_id' => $transient_name,
		'atts' => $atts,
		'location' => array(
			'post_id' => $post_id,
			'html' => $location
		)
	);

	sbi_do_background_tasks($feed_details);

	$settings = $instagram_feed_settings->get_settings();
	$offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;
	$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

	$feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();

	$instagram_feed = new SB_Instagram_Feed_Pro($transient_name);
	$instagram_feed->set_cache($instagram_feed_settings->get_cache_time_in_seconds(), $settings);

	if ($settings['caching_type'] === 'permanent' && empty($settings['doingModerationMode'])) {
		$instagram_feed->add_report('trying to use permanent cache');
		$instagram_feed->maybe_set_post_data_from_backup();
	} elseif ($settings['caching_type'] === 'background') {
		$instagram_feed->add_report('background caching used');
		if ($instagram_feed->regular_cache_exists()) {
			$instagram_feed->add_report('setting posts from cache');
			$instagram_feed->set_post_data_from_cache();
		}

		if ($instagram_feed->need_posts((int)$settings['minnum'] + (int)$settings['offset'], $offset, $page) && $instagram_feed->can_get_more_posts()) {
			while ($instagram_feed->need_posts((int)$settings['minnum'] + (int)$settings['offset'], $offset, $page) && $instagram_feed->can_get_more_posts()) {
				$instagram_feed->add_remote_posts($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
			}

			$normal_method = true;
			if ($instagram_feed->need_to_start_cron_job()) {
				$instagram_feed->add_report('needed to start cron job');
				$to_cache = array(
					'atts' => $atts,
					'last_requested' => time(),
				);


				$normal_method = false;
			} else {
				$instagram_feed->add_report('updating last requested and adding to cache');
				$to_cache = array(
					'last_requested' => time(),
				);
			}

			if ($instagram_feed->out_of_next_pages() && $instagram_feed->should_look_for_db_only_posts($settings, $feed_type_and_terms)) {
				$instagram_feed->add_report('Adding Db only posts');
				$instagram_feed->add_db_only_posts($transient_name, $settings, $feed_type_and_terms);
			}

			if ($normal_method) {
				$instagram_feed->set_cron_cache($to_cache, $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled']);
			} else {
				$instagram_feed->set_cron_cache($to_cache, $instagram_feed_settings->get_cache_time_in_seconds());
			}
		}
	} elseif ($instagram_feed->regular_cache_exists()) {
		$instagram_feed->add_report('regular cache exists');
		$instagram_feed->set_post_data_from_cache();

		if ($instagram_feed->need_posts((int)$settings['minnum'] + (int)$settings['offset'], $offset, $page) && $instagram_feed->can_get_more_posts()) {
			while ($instagram_feed->need_posts((int)$settings['minnum'] + (int)$settings['offset'], $offset, $page) && $instagram_feed->can_get_more_posts()) {
				$instagram_feed->add_remote_posts($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
			}

			if ($instagram_feed->out_of_next_pages() || $instagram_feed->should_look_for_db_only_posts($settings, $feed_type_and_terms)) {
				$instagram_feed->add_report('Adding Db only posts');
				$instagram_feed->add_db_only_posts($transient_name, $settings, $feed_type_and_terms);
			}

			$instagram_feed->add_report('adding to cache');
			$instagram_feed->cache_feed_data($instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled']);
		}

		if ($instagram_feed->using_an_allow_list($settings)) {
			$instagram_feed->add_report('Adding allow list only posts');
			$instagram_feed->add_db_only_allow_list_posts($settings);
		}
	} else {
		$instagram_feed->add_report('no feed cache found');

		while ($instagram_feed->need_posts((int)$settings['minnum'] + (int)$settings['offset'], $offset, $page) && $instagram_feed->can_get_more_posts()) {
			$instagram_feed->add_remote_posts($settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed());
		}

		if ($instagram_feed->should_use_backup()) {
			$instagram_feed->add_report('trying to use a backup cache');
			$instagram_feed->maybe_set_post_data_from_backup();
		} else {
			$instagram_feed->add_report('transient gone, adding to cache');
			$instagram_feed->cache_feed_data($instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled']);
		}
	}

	$settings['feed_avatars'] = array();
	if ($instagram_feed->need_avatars($settings)) {
		$instagram_feed->set_up_feed_avatars($instagram_feed_settings->get_connected_accounts_in_feed(), $feed_type_and_terms);
		$settings['feed_avatars'] = $instagram_feed->get_username_avatars();
	}

	$should_paginate_offset = (int)$offset + (int)$settings['offset'];
	$feed_status = array('shouldPaginate' => $instagram_feed->should_use_pagination($settings, $should_paginate_offset));

	if ($settings['disable_js_image_loading'] || $settings['imageres'] !== 'auto') {
		global $sb_instagram_posts_manager;
		$post_data = array_slice($instagram_feed->get_post_data(), $offset, (int)$settings['minnum']);

		if (!$sb_instagram_posts_manager->image_resizing_disabled($feed_type_and_terms)) {
			$image_ids = array();
			foreach ($post_data as $post) {
				$image_ids[] = SB_Instagram_Parse::get_post_id($post);
			}
			$resized_images = SB_Instagram_Feed::get_resized_images_source_set($image_ids, 0, $feed_id);

			$instagram_feed->set_resized_images($resized_images);
		}
	}

	$instagram_feed->maybe_offset_posts((int)$settings['offset']);

	$return = array(
		'html' => $instagram_feed->get_the_items_html($settings, $offset, $instagram_feed_settings->get_feed_type_and_terms(), $instagram_feed_settings->get_connected_accounts_in_feed()),
		'feedStatus' => $feed_status,
		'report' => $instagram_feed->get_report(),
		'resizedImages' => SB_Instagram_Feed::get_resized_images_source_set($instagram_feed->get_image_ids_post_set(), 1, $feed_id)
	);

	SB_Instagram_Feed::update_last_requested($instagram_feed->get_image_ids_post_set());
	wp_send_json_success($return);
}

add_action('wp_ajax_sbi_load_more_clicked', 'sbi_get_next_post_set');
add_action('wp_ajax_nopriv_sbi_load_more_clicked', 'sbi_get_next_post_set');

/**
 * Posts that need resized images are processed after being sent to the server
 * using AJAX
 *
 * @return void
 */
function sbi_process_submitted_resize_ids()
{
	if (empty($_POST['feed_id']) || !preg_match('/^(sbi|\\*)/', $_POST['feed_id'])) {
		wp_send_json_error('invalid feed ID');
	}

	$feed_id = sanitize_text_field($_POST['feed_id']);
	$post_id = isset($_POST['post_id']) && $_POST['post_id'] !== 'unknown' ? intval($_POST['post_id']) : 'unknown';

	$atts_raw = isset($_POST['atts']) ? json_decode(stripslashes($_POST['atts']), true) : array();
	$atts = is_array($atts_raw) ? SB_Instagram_Settings_Pro::pro_sanitize_raw_atts($atts_raw) : array();

	$database_settings = sbi_get_database_settings();
	$instagram_feed_settings = new SB_Instagram_Settings_Pro($atts, $database_settings);

	$instagram_feed_settings->set_feed_type_and_terms();
	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();
	$settings = $instagram_feed_settings->get_settings();

	$nonce = isset($_POST['locator_nonce']) ? sanitize_text_field(wp_unslash($_POST['locator_nonce'])) : '';
	if (!wp_verify_nonce($nonce, 'sbi-locator-nonce-' . $post_id . '-' . $transient_name) || $transient_name !== $feed_id) {
		wp_send_json_error('nonce check failed, details do not match');
	}

	$images_need_resizing_raw = isset($_POST['needs_resizing']) ? $_POST['needs_resizing'] : array();
	$images_need_resizing = is_array($images_need_resizing_raw) ? array_map('sbi_sanitize_instagram_ids', $images_need_resizing_raw) : array();

	$offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;
	$cache_all = isset($_POST['cache_all']) && $_POST['cache_all'] === 'true';
	if ($cache_all) {
		$settings['cache_all'] = true;
	}

	$location = isset($_POST['location']) && in_array($_POST['location'], array('header', 'footer', 'sidebar', 'content'), true) ? sanitize_text_field($_POST['location']) : 'unknown';
	$feed_details = array(
		'feed_id' => $transient_name,
		'atts' => $atts,
		'location' => array(
			'post_id' => $post_id,
			'html' => $location
		)
	);

	sbi_do_background_tasks($feed_details);
	sbi_resize_posts_by_id($images_need_resizing, $transient_name, $settings);
	sbi_delete_image_cache($transient_name);

	global $sb_instagram_posts_manager;
	if (!$sb_instagram_posts_manager->image_resizing_disabled($transient_name)) {
		$num = (int)$settings['minnum'] * 2 + 5;
		wp_send_json_success(SB_Instagram_Feed::get_resized_images_source_set($num, $offset - (int)$settings['minnum'], $feed_id, false));
	}

	wp_send_json_success('resizing success');
}

add_action('wp_ajax_sbi_resized_images_submit', 'sbi_process_submitted_resize_ids');
add_action('wp_ajax_nopriv_sbi_resized_images_submit', 'sbi_process_submitted_resize_ids');

/**
 * Locates the feed on the page and adds it to the database
 */
function sbi_do_locator()
{
	if (!isset($_POST['feed_id']) || !preg_match('/^(sbi|\\*)/', $_POST['feed_id'])) {
		wp_send_json_error('invalid feed ID');
	}

	$feed_id = sanitize_text_field(wp_unslash($_POST['feed_id']));
	$post_id = isset($_POST['post_id']) && $_POST['post_id'] !== 'unknown' ? intval($_POST['post_id']) : 'unknown';

	$atts_raw = isset($_POST['atts']) ? json_decode(wp_unslash($_POST['atts']), true) : array();
	$atts = is_array($atts_raw) ? SB_Instagram_Settings::sanitize_raw_atts($atts_raw) : array();

	$database_settings = sbi_get_database_settings();
	$instagram_feed_settings = new SB_Instagram_Settings($atts, $database_settings);

	$instagram_feed_settings->set_feed_type_and_terms();
	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();

	$nonce = isset($_POST['locator_nonce']) ? sanitize_text_field(wp_unslash($_POST['locator_nonce'])) : '';
	if (!wp_verify_nonce($nonce, 'sbi-locator-nonce-' . $post_id . '-' . $transient_name)) {
		wp_send_json_error('nonce check failed');
	}

	$location = isset($_POST['location']) && in_array($_POST['location'], array('header', 'footer', 'sidebar', 'content'), true) ? sanitize_text_field($_POST['location']) : 'unknown';
	$feed_details = array(
		'feed_id' => $feed_id,
		'atts' => $atts,
		'location' => array(
			'post_id' => $post_id,
			'html' => $location
		)
	);

	sbi_do_background_tasks($feed_details);
	wp_send_json_success('locating success');
}

add_action('wp_ajax_sbi_do_locator', 'sbi_do_locator');
add_action('wp_ajax_nopriv_sbi_do_locator', 'sbi_do_locator');

/**
 * Background tasks for the locator.
 *
 * @param array $feed_details The feed details.
 * @return void
 */
function sbi_do_background_tasks($feed_details)
{
	if (
		is_admin()
		&& isset($_GET['page'])
		&& $_GET['page'] === 'sbi-feed-builder'
	) {
		return;
	}
	$locator = new SB_Instagram_Feed_Locator($feed_details);
	$locator->add_or_update_entry();
	if ($locator->should_clear_old_locations()) {
		$locator->delete_old_locations();
	}
}

/**
 * Outputs an organized error report for the front end.
 * This hooks into the end of the feed before the closing div
 *
 * @param object $instagram_feed The feed object.
 * @param string $feed_id The feed ID.
 */
function sbi_error_report($instagram_feed, $feed_id)
{
	global $sb_instagram_posts_manager;

	$style = sbi_current_user_can('manage_instagram_feed_options') ? ' style="display: block;"' : '';

	$error_messages = $sb_instagram_posts_manager->get_frontend_errors($instagram_feed);

	if (!empty($error_messages)) { ?>
		<div id="sbi_mod_error"<?php echo $style; ?>>
			<span><?php esc_html_e('This error message is only visible to WordPress admins', 'instagram-feed'); ?></span><br/>
			<?php foreach ($error_messages as $error_message) {
				echo '<div><strong>' . esc_html($error_message['error_message']) . '</strong>';
				if (sbi_current_user_can('manage_instagram_feed_options')) {
					echo '<br>' . $error_message['admin_only'];
					echo '<br>' . $error_message['frontend_directions'];
				}
				echo '</div>';
			} ?>
		</div>
		<?php
	}

	$sb_instagram_posts_manager->reset_frontend_errors();
}

add_action('sbi_before_feed_end', 'sbi_error_report', 10, 2);

/**
 * Deletes the resized images cache for a feed
 *
 * @param string $transient_name The transient name.
 * @return void
 */
function sbi_delete_image_cache($transient_name)
{
	$cache = new SB_Instagram_Cache($transient_name);

	$cache->clear('resized_images');
}

/**
 * Checks if the user can manage the Instagram feed options
 *
 * @param string $cap The capability to check.
 * @return bool
 */
function sbi_current_user_can($cap)
{
	if ($cap === 'manage_instagram_feed_options') {
		$cap = current_user_can('manage_instagram_feed_options') ? 'manage_instagram_feed_options' : 'manage_options';
	}
	$cap = apply_filters('sbi_settings_pages_capability', $cap);

	return current_user_can($cap);
}

/**
 * Checks if the server has the OpenSSL extension loaded
 *
 * @return bool
 */
function sbi_doing_openssl()
{
	return extension_loaded('openssl');
}

/**
 * Debug report added at the end of the feed when sbi_debug query arg is added to a page
 * that has the feed on it.
 *
 * @param SB_Instagram_Feed $instagram_feed The feed object.
 * @param string            $feed_id The feed ID.
 */
function sbi_debug_report($instagram_feed, $feed_id)
{
	if (!Util::isDebugging()) {
		return;
	}
	global $sb_instagram_posts_manager;

	$feed = $instagram_feed->get_feed_id();
	$atts = array();
	if (!empty($feed)) {
		$atts = array('feed' => 1);
	}

	$settings_obj = new SB_Instagram_Settings_Pro($atts, sbi_get_database_settings());

	$settings = $settings_obj->get_settings();

	$public_settings_keys = SB_Instagram_Settings_Pro::get_public_db_settings_keys();
	?>

	<p>Status</p>
	<ul>
		<li>Time: <?php echo esc_html(date("Y-m-d H:i:s", time())); ?></li>
		<?php foreach ($instagram_feed->get_report() as $item) : ?>
			<li><?php echo esc_html($item); ?></li>
		<?php endforeach; ?>

	</ul>
	<p>Settings</p>
	<ul>
		<?php foreach ($public_settings_keys as $key) :
			if (isset($settings[$key])) : ?>
				<li>
					<small><?php echo esc_html($key); ?>:</small>
					<?php if (!is_array($settings[$key])) :
						echo esc_html($settings[$key]);
					else : ?>
						<ul>
							<?php foreach ($settings[$key] as $sub_key => $value) {
								echo '<li><small>' . esc_html($sub_key) . ':</small> ' . esc_html($value) . '</li>';
							} ?>
						</ul>
					<?php endif; ?>
				</li>

			<?php endif;
		endforeach; ?>
	</ul>
	<p>GDPR</p>
	<ul>
		<?php
		$statuses = SB_Instagram_GDPR_Integrations::statuses();
		foreach ($statuses as $status_key => $value) : ?>
			<li>
				<small><?php echo esc_html($status_key); ?>:</small>
				<?php if ($value == 1) {
					echo 'success';
				} else {
					echo 'failed';
				} ?>
			</li>

		<?php endforeach; ?>
		<li>
			<small>Enabled:</small>
			<?php echo SB_Instagram_GDPR_Integrations::doing_gdpr($settings); ?>
		</li>
	</ul>
	<?php
}

add_action('sbi_before_feed_end', 'sbi_debug_report', 11, 2);

/**
 * Uses post IDs to process images that may need resizing
 *
 * @param array  $ids The post IDs.
 * @param string $transient_name The transient name.
 * @param array  $settings The settings.
 * @param int    $offset The offset.
 */
function sbi_resize_posts_by_id($ids, $transient_name, $settings, $offset = 0)
{
	$instagram_feed = new SB_Instagram_Feed($transient_name);

	$instagram_feed->set_cache(MONTH_IN_SECONDS, $settings);

	if ($instagram_feed->regular_cache_exists()) {
		// set_post_data_from_cache.
		$instagram_feed->set_post_data_from_cache();

		$cached_post_data = $instagram_feed->get_post_data();
	} elseif (sbi_current_user_can('manage_instagram_feed_options') && is_admin()) {
		$customizer_cache = new SB_Instagram_Cache($transient_name, 1, MONTH_IN_SECONDS);

		$cached_post_data = $customizer_cache->get_customizer_cache();
	} else {
		return array();
	}

	if (!isset($settings['cache_all']) || !$settings['cache_all']) {
		$num_ids = count($ids);
		$found_posts = array();
		$i = 0;
		while (count($found_posts) < $num_ids && isset($cached_post_data[$i])) {
			if (!empty($cached_post_data[$i]['id']) && in_array($cached_post_data[$i]['id'], $ids, true)) {
				$found_posts[] = $cached_post_data[$i];
			}
			$i++;
		}
	} else {
		$found_posts = array_slice($cached_post_data, 0, 50);
	}

	$fill_in_timestamp = date('Y-m-d H:i:s', time() + 120);

	if ($offset !== 0) {
		$fill_in_timestamp = date('Y-m-d H:i:s', strtotime($instagram_feed->get_earliest_time_stamp($transient_name)) - 120);
	}

	$image_sizes = array(
		'personal' => array('full' => 640, 'low' => 320, 'thumb' => 150),
		'business' => array('full' => 640, 'low' => 320, 'thumb' => 150)
	);

	$post_set = new SB_Instagram_Post_Set($found_posts, $transient_name, $fill_in_timestamp, $image_sizes);

	$post_set->maybe_save_update_and_resize_images_for_posts();
}

/**
 * Creates a local avatar for a user
 *
 * @param string $username The username.
 * @param string $file_name The file name.
 * @return bool
 */
function sbi_create_local_avatar($username, $file_name)
{
	return SB_Instagram_Connected_Account::create_local_avatar($username, $file_name);
}

/**
 * Get the settings in the database with defaults
 *
 * @return array
 */
function sbi_get_database_settings()
{
	$sbi_settings = get_option('sb_instagram_settings', array());

	if (!is_array($sbi_settings)) {
		$sbi_settings = array();
	}

	$return_settings = array_merge(sbi_defaults(), $sbi_settings);

	return apply_filters('sbi_database_settings', $return_settings);
}

/**
 * Get the settings in the database with defaults
 *
 * @return array
 */
function sbi_defaults()
{
	return array(
		'sb_instagram_at' => '',
		'sb_instagram_user_id' => '',
		'sb_instagram_preserve_settings' => '',
		'sb_instagram_ajax_theme' => false,
		'sb_instagram_disable_resize' => false,
		'image_format' => 'webp',
		'sb_instagram_cache_time' => 1,
		'sb_instagram_cache_time_unit' => 'hours',
		'sbi_caching_type' => 'background',
		'sbi_cache_cron_interval' => '12hours',
		'sbi_cache_cron_time' => '1',
		'sbi_cache_cron_am_pm' => 'am',
		'sb_instagram_width' => '100',
		'sb_instagram_width_unit' => '%',
		'sb_instagram_feed_width_resp' => false,
		'sb_instagram_height' => '',
		'sb_instagram_num' => '20',
		'sb_instagram_height_unit' => '',
		'sb_instagram_cols' => '4',
		'sb_instagram_disable_mobile' => false,
		'sb_instagram_image_padding' => '5',
		'sb_instagram_image_padding_unit' => 'px',
		'sb_instagram_sort' => 'none',
		'sb_instagram_background' => '',
		'sb_instagram_show_btn' => true,
		'sb_instagram_btn_background' => '',
		'sb_instagram_btn_text_color' => '',
		'sb_instagram_btn_text' => __('Load More...', 'instagram-feed'),
		'sb_instagram_image_res' => 'auto',
		'sb_instagram_lightbox_comments' => true,
		'sb_instagram_num_comments' => 20,
		'sb_instagram_show_bio' => true,
		'sb_instagram_show_followers' => true,
		// Header.
		'sb_instagram_show_header' => true,
		'sb_instagram_header_size' => 'small',
		'sb_instagram_header_color' => '',
		'sb_instagram_stories' => true,
		'sb_instagram_stories_time' => 5000,
		// Follow button.
		'sb_instagram_show_follow_btn' => true,
		'sb_instagram_folow_btn_background' => '',
		'sb_instagram_follow_btn_text_color' => '',
		'sb_instagram_follow_btn_text' => __('Follow on Instagram', 'instagram-feed'),
		// Misc.
		'sb_instagram_cron' => 'no',
		'sb_instagram_backup' => true,
		'sb_ajax_initial' => false,
		'enqueue_css_in_shortcode' => false,
		'enqueue_js_in_head' => false,
		'disable_js_image_loading' => false,
		'disable_admin_notice' => false,
		'enable_email_report' => true,
		'email_notification' => 'monday',
		'email_notification_addresses' => get_option('admin_email'),

		'sb_instagram_disable_mob_swipe' => false,
		'sb_instagram_disable_awesome' => false,
		'sb_instagram_disable_font' => false,
		'gdpr' => 'auto',
		'enqueue_legacy_css' => false,
	);
}

/**
 * Checks if the user is currently in the customizer
 *
 * @param array $settings The settings for the feed.
 * @return bool
 */
function sbi_doing_customizer($settings)
{
	return !empty($settings['customizer']) && $settings['customizer'] == true;
}

/**
 * Renders the header HTML for the feed
 *
 * @param array  $settings The settings for the feed.
 * @param array  $header_data The header data for the feed.
 * @param string $location The location of the header.
 * @return void
 */
function sbi_header_html($settings, $header_data, $location = 'inside')
{
	$customizer = sbi_doing_customizer($settings);
	if (
		!$customizer && (
			($location === 'inside' && $settings['headeroutside']) ||
			($location === 'outside' && !$settings['headeroutside']) ||
			empty($header_data)
		)
	) {
		return;
	}

	if ($location === 'inside') {
		$settings['vue_args'] = [
			'condition' => ' && !$parent.valueIsEnabled($parent.customizerFeedData.settings.headeroutside)'
		];
	} else {
		$settings['vue_args'] = [
			'condition' => ' && $parent.valueIsEnabled($parent.customizerFeedData.settings.headeroutside)'
		];
	}
	if ($customizer) {
		include sbi_get_feed_template_part('header', $settings);
		include sbi_get_feed_template_part('header-boxed', $settings);
		include sbi_get_feed_template_part('header-generic', $settings);
		include sbi_get_feed_template_part('header-text', $settings);
	} else {
		include sbi_get_feed_template_part(SB_Instagram_Display_Elements_Pro::header_type($settings), $settings);
	}
}

/**
 * Retrieves the full path to a template file for the Instagram Feed Pro plugin.
 *
 * @param string $template_part The name of the template file to retrieve.
 * @param array  $settings      Optional. Additional settings for retrieving the template. Default empty array.
 *
 * @return string The full path to the template file.
 *
 * @since 5.2 Custom templates supported.
 */
function sbi_get_feed_template_part($template_part, $settings = array())
{
	/**
	 * Whether or not to search for custom templates in theme folder
	 *
	 * @param boolean  Setting from DB or shortcode to use custom templates
	 *
	 * @since 5.2
	 */
	$custom_templates_enabled = isset($settings['customtemplates']) ? $settings['customtemplates'] : false;
	$custom_templates_enabled = apply_filters('sbi_use_theme_templates', $custom_templates_enabled);

	$template_name = strpos($template_part, '.php') === false ? $template_part . '.php' : $template_part;

	$cache_key = sanitize_key(implode('-', ['template', $template_name]));
	$template = (string)wp_cache_get($cache_key, 'instagram-feed');

	if (!$template) {
		$template = sbi_locate_template($template_name, $custom_templates_enabled);
		if (empty($template)) {
			return '';
		}
		wp_cache_set($cache_key, $template, 'instagram-feed');
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	return apply_filters('sbi_feed_template_part', $template, $template_part, $settings);
}

/**
 * Locate a template and return the path for inclusion.
 *
 * @param string $template_name Template name.
 * @param bool   $custom_templates_enabled If custom templates are enabled. (default: false).
 *
 * @return string Template path.
 */
function sbi_locate_template($template_name, $custom_templates_enabled = false)
{
	if (empty($template_name) || !is_string($template_name)) {
		return '';
	}

	$template = '';
	$template_path = apply_filters('sbi_template_path', 'sbi/');

	$default_path = untrailingslashit(SBI_PLUGIN_DIR) . '/templates/';
	$default_path = apply_filters('sbi_default_template_path', $default_path);

	if ($custom_templates_enabled === true) {
		$template = locate_template(
			[
				trailingslashit($template_path) . $template_name,
			]
		);
	}

	// Get default template.
	if (empty($template)) {
		$template = $default_path . $template_name;
		if (!file_exists($template)) {
			return '';
		}
	}

	return apply_filters('sbi_locate_template', $template, $template_name, $custom_templates_enabled);
}

/**
 * Triggered by a cron event to update feeds
 */
function sbi_cron_updater()
{
	$cron_updater = new SB_Instagram_Cron_Updater_Pro();

	$cron_updater->do_feed_updates();

	sbi_do_background_tasks(array());
	SBI_Support_Tool::delete_expired_users();
}

add_action('sbi_feed_update', 'sbi_cron_updater');

/**
 * If there are more feeds than a single batch
 */
function sbi_process_additional_batch()
{
	$args = array(
		'cron_update' => true,
		'additional_batch' => true,
	);
	$cron_records = SBI_Db::feed_caches_query($args);

	$num = count($cron_records);
	if ($num === SBI_Db::RESULTS_PER_CRON_UPDATE) {
		wp_schedule_single_event(time() + 120, 'sbi_cron_additional_batch');
	}

	SB_Instagram_Cron_Updater_Pro::update_batch($cron_records);

	sbi_do_background_tasks(array());
}

add_action('sbi_cron_additional_batch', 'sbi_process_additional_batch');

/**
 * Cleans the provided input if necessary.
 *
 * @param mixed $maybe_dirty The input that may need to be cleaned.
 * @return mixed The cleaned input.
 */
function sbi_maybe_clean($maybe_dirty)
{
	$encryption = new SB_Instagram_Data_Encryption();

	$decrypted = $encryption->decrypt($maybe_dirty);
	if ($decrypted) {
		$maybe_dirty = $decrypted;
	}
	if (substr_count($maybe_dirty, '.') < 3) {
		return str_replace('634hgdf83hjdj2', '', $maybe_dirty);
	}

	$parts = explode('.', trim($maybe_dirty));
	$last_part = $parts[2] . $parts[3];

	return $parts[0] . '.' . base64_decode($parts[1]) . '.' . base64_decode($last_part);
}

/**
 * Deletes any cache or setting that may contain Instagram platform data
 */
function sbi_delete_all_platform_data()
{
	global $sb_instagram_posts_manager;
	$manager = new SB_Instagram_Data_Manager();
	$sb_instagram_posts_manager->add_action_log('Deleted all platform data.');
	$sb_instagram_posts_manager->reset_api_errors();
	$manager->delete_caches();
	SBI_Db::clear_sbi_sources();
	$manager->delete_comments_data();
	$manager->delete_hashtag_data();
	SB_Instagram_Connected_Account::update_connected_accounts(array());
}

/**
 * Gets the current time for use in tests.
 *
 * @return int
 */
function sbi_get_current_time()
{
	return time();
}

/**
 * Used for manipulating the current timestamp during tests
 *
 * @return int
 */
function sbi_get_current_timestamp()
{
	return sbi_get_current_time();
}

/**
 * Splits a given string into parts.
 *
 * @param string $whole The string to be split into parts.
 * @return string An array containing the parts of the string.
 */
function sbi_get_parts($whole)
{
	if (substr_count($whole, '.') !== 2) {
		return $whole;
	}

	$parts = explode('.', trim($whole));
	$return = $parts[0] . '.' . base64_encode($parts[1]) . '.' . base64_encode($parts[2]);

	return substr($return, 0, 40) . '.' . substr($return, 40, 100);
}

/**
 * Used to shorten screen reader and alt text but still
 * have the text end on a full word.
 *
 * @param string $text The text to be shortened.
 * @param int    $max_characters The maximum number of characters to allow.
 *
 * @return string
 */
function sbi_shorten_paragraph($text, $max_characters)
{

	if (strlen($text) <= $max_characters) {
		return $text;
	}

	$parts = preg_split('/([\s\n\r]+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
	$parts_count = count($parts);

	$length = 0;
	$last_part = 0;
	for (; $last_part < $parts_count; ++$last_part) {
		$length += strlen($parts[$last_part]);
		if ($length > $max_characters) {
			break;
		}
	}

	$i = 0;
	$last_part = $last_part !== 0 ? $last_part - 1 : 0;
	$final_parts = array();
	if ($last_part > 0) {
		while ($i <= $last_part && isset($parts[$i])) {
			$final_parts[] = $parts[$i];
			$i++;
		}
	} else {
		return $text;
	}

	$final_parts[$last_part] = $final_parts[$last_part] . '...';

	return implode(' ', $final_parts);
}

if (!function_exists('shorten_paragraph')) {
	/**
	 * Used to shorten screen reader and alt text but still
	 * have the text end on a full word.
	 *
	 * @param string $text The text to be shortened.
	 * @param int    $max_characters The maximum number of characters to allow.
	 *
	 * @return string
	 */
	function shorten_paragraph($text, $max_characters)
	{
		return sbi_shorten_paragraph($text, $max_characters);
	}
}

/**
 * Used to check if the access token is valid
 *
 * @param string $code The access token.
 * @return bool
 */
function sbi_code_check($code)
{
	if (strpos($code, '634hgdf83hjdj2') !== false) {
		return true;
	}
	return false;
}

/**
 * Used to fix the access token
 *
 * @param string $code The access token.
 * @return string
 */
function sbi_fixer($code)
{
	if (strpos($code, '634hgdf83hjdj2') !== false) {
		return $code;
	} else {
		return substr_replace($code, '634hgdf83hjdj2', 15, 0);
	}
}

/**
 * Compares the dates of two posts for sorting
 *
 * @param array $a The first post.
 * @param array $b The second post.
 *
 * @return int
 */
function sbi_date_sort($a, $b)
{
	$time_stamp_a = SB_Instagram_Parse::get_timestamp($a);
	$time_stamp_b = SB_Instagram_Parse::get_timestamp($b);

	if (isset($time_stamp_a)) {
		return $time_stamp_b - $time_stamp_a;
	} else {
		return rand(-1, 1);
	}
}

/**
 * Compares the likes of two posts for sorting
 *
 * @param array $a The first post.
 * @param array $b The second post.
 *
 * @return int
 */
function sbi_likes_sort($a, $b)
{
	$likes_a = SB_Instagram_Parse_Pro::get_likes_count($a);
	$likes_b = SB_Instagram_Parse_Pro::get_likes_count($b);

	if (isset($likes_a)) {
		return (int)$likes_b - (int)$likes_a;
	} else {
		return rand(-1, 1);
	}
}

/**
 * Sorts posts randomly
 *
 * @param array $a The first post.
 * @param array $b The second post.
 *
 * @return int
 */
function sbi_rand_sort($a, $b)
{
	return rand(-1, 1);
}

/**
 * Converts a hex code to RGB so opacity can be
 * applied more easily
 *
 * @param string $hex The hex code.
 *
 * @return string
 */
function sbi_hextorgb($hex)
{
	// allows someone to use rgb in shortcode.
	if (strpos($hex, ',') !== false) {
		return $hex;
	}

	$hex = str_replace('#', '', $hex);

	if (strlen($hex) === 3) {
		$r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
		$g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
		$b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
	} else {
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));
	}
	$rgb = array($r, $g, $b);

	return implode(',', $rgb); // returns the rgb values separated by commas.
}

/**
 * Used to encode comments before returning in AJAX call
 *
 * @param string $uri The URI.
 *
 * @return string
 */
function sbi_encode_uri($uri)
{
	$unescaped = array(
		'%2D' => '-', '%5F' => '_', '%2E' => '.', '%21' => '!', '%7E' => '~',
		'%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')'
	);
	$reserved = array(
		'%3B' => ';', '%2C' => ',', '%2F' => '/', '%3F' => '?', '%3A' => ':',
		'%40' => '@', '%26' => '&', '%3D' => '=', '%2B' => '+', '%24' => '$'
	);
	$score = array(
		'%23' => '#'
	);

	return strtr(rawurlencode($uri), array_merge($reserved, $unescaped, $score));
}

/**
 * Added to workaround MySQL tables that don't use utf8mb4 character sets
 *
 * @param string $string The string to sanitize.
 * @since 2.2.1/5.3.1
 */
function sbi_sanitize_emoji($string)
{
	$encoded = array(
		'jsonencoded' => $string
	);
	return sbi_json_encode($encoded);
}

/**
 * Added to workaround MySQL tables that don't use utf8mb4 character sets
 *
 * @param string $string The string to decode.
 * @since 2.2.1/5.3.1
 */
function sbi_decode_emoji($string)
{
	if (strpos($string, '{"') !== false) {
		$decoded = json_decode($string, true);
		return $decoded['jsonencoded'];
	}
	return $string;
}

/**
 * Used to sanitize the Instagram ID
 *
 * @param string $raw_id The raw ID.
 *
 * @return string
 */
function sbi_sanitize_instagram_ids($raw_id)
{
	return preg_replace('/[^0-9_]/', '', $raw_id);
}

/**
 * Used to sanitize the Alphanumeric and Equals
 *
 * @param string $value The value to sanitize.
 *
 * @return string
 */
function sbi_sanitize_alphanumeric_and_equals($value)
{
	return preg_replace('/[^A-Za-z0-9=]/', '', $value);
}

/**
 * Used to sanitize the username
 *
 * @param string $value The value to sanitize.
 *
 * @return string
 */
function sbi_sanitize_username($value)
{
	return preg_replace('/[^A-Za-z0-9_.]/', '', $value);
}

/**
 * Used for caching posts in the background
 *
 * @return int
 */
function sbi_get_utc_offset()
{
	return get_option('gmt_offset', 0) * HOUR_IN_SECONDS;
}

/**
 * Various warnings and workarounds are triggered
 * or changed by whether or not this function returns
 * true
 *
 * @return bool
 */
function sbi_is_after_deprecation_deadline()
{
	$current_time = sbi_get_current_timestamp();

	return $current_time > strtotime('June 29, 2020');
}

/**
 * JSON encode with fallback
 *
 * @param mixed $thing The thing to encode.
 * @return string|false
 */
function sbi_json_encode($thing)
{
	if (function_exists('wp_json_encode')) {
		return wp_json_encode($thing);
	} else {
		return json_encode($thing);
	}
}

/**
 * Retrieves the default type and terms for the Instagram feed.
 *
 * @return array An associative array containing the default type and terms.
 */
function sbi_get_default_type_and_terms()
{
	$if_atts = array();
	$if_database_settings = sbi_get_database_settings();

	$instagram_feed_settings = new SB_Instagram_Settings_Pro($if_atts, $if_database_settings);

	$instagram_feed_settings->set_feed_type_and_terms();
	$if_feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();
	$connected_accounts = $instagram_feed_settings->get_connected_accounts_in_feed();

	$return = array(
		'type' => '',
		'term_label' => '',
		'terms' => ''
	);
	$terms_array = array();
	$type = '';

	foreach ($if_feed_type_and_terms as $key => $values) {
		if (empty($type) && $key === 'users') {
			$return['type'] = 'user';
			$return['term_label'] = 'user(s)';
			foreach ($values as $value) {
				if (isset($connected_accounts[$value['term']])) {
					$terms_array[] = $connected_accounts[$value['term']]['username'];
				} else {
					$terms_array[] = $value['term'];
				}
			}
		}
	}

	$return['terms'] = implode(', ', $terms_array);

	return $return;
}

/**
 * Retrieves account and feed information for the Instagram Feed Pro plugin.
 *
 * @return array An associative array containing account and feed information.
 */
function sbi_get_account_and_feed_info()
{
	$return = array();
	$if_atts = array();
	$if_database_settings = sbi_get_database_settings();

	$instagram_feed_settings = new SB_Instagram_Settings_Pro($if_atts, $if_database_settings);

	$instagram_feed_settings->set_feed_type_and_terms();
	$if_feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();
	$connected_accounts = SB_Instagram_Connected_Account::get_all_connected_accounts();

	$type_and_terms = array(
		'type' => '',
		'term_label' => '',
		'terms' => ''
	);
	$terms_array = array();
	$type = '';

	foreach ($if_feed_type_and_terms as $key => $values) {
		if (empty($type) && $key === 'users') {
			$type_and_terms['type'] = 'user';
			$type_and_terms['term_label'] = 'user(s)';
			foreach ($values as $value) {
				if (isset($connected_accounts[$value['term']])) {
					$terms_array[] = $connected_accounts[$value['term']]['username'];
				} else {
					$terms_array[] = $value['term'];
				}
			}
		}
	}

	$sbi_statuses = get_option('sbi_statuses', array());
	if (empty($sbi_statuses['support_legacy_shortcode'])) {
		$return['support_legacy'] = false;
	} else {
		$return['support_legacy'] = true;
	}

	$return['feeds'] = SBI_Db::feeds_query(array('social_wall_summary' => 1));

	$type_and_terms['terms'] = $terms_array;

	$return['type_and_terms'] = $type_and_terms;
	$return['connected_accounts'] = $connected_accounts;
	$return['available_types'] = array(
		'user' => array(
			'label' => 'User',
			'shortcode' => 'user',
			'term_shortcode' => 'user',
			'key' => 'username',
			'input' => 'connected'
		),
		'hashtag' => array(
			'label' => 'Hashtag',
			'shortcode' => 'hashtag',
			'term_shortcode' => 'hashtag',
			'key' => false,
			'input' => 'text',
			'instructions' => __('Any hashtags (comma separated)', 'instagram-feed')
		),
		'tagged' => array(
			'label' => 'Tagged',
			'shortcode' => 'tagged',
			'term_shortcode' => 'tagged',
			'key' => 'username',
			'input' => 'connected'
		)
	);
	$return['settings'] = array(
		'type' => 'type'
	);


	return $return;
}

/**
 * Checks if a connected Instagram account is near expiration.
 *
 * @param array $connected_account An associative array containing the connected
 *                                 account's information, including the 'expires_timestamp'.
 * @return bool True if the account's access token expires in less than 10 days, false otherwise.
 */
function sbi_private_account_near_expiration($connected_account)
{
	$expires_in = max(0, floor(($connected_account['expires_timestamp'] - time()) / DAY_IN_SECONDS));
	return $expires_in < 10;
}

/**
 * Updates the connected Instagram account with the provided data.
 *
 * @param string $account_id The ID of the Instagram account to update.
 * @param array  $to_update An associative array of data to update the account with.
 *
 * @return void
 */
function sbi_update_connected_account($account_id, $to_update)
{
	$args = [
		'id' => $account_id
	];
	$results = InstagramFeed\Builder\SBI_Db::source_query($args);

	if (!empty($results)) {
		$source = $results[0];
		$info = !empty($source['info']) ? json_decode($source['info'], true) : array();

		if (isset($to_update['private'])) {
			$info['private'] = $to_update['private'];
		}

		foreach ($to_update as $key => $value) {
			if (isset($source[$key])) {
				$source[$key] = $value;
			}
		}

		$source['id'] = $account_id;

		InstagramFeed\Builder\SBI_Source::update_or_insert($source);
	}
}

/**
 * Retrieves the URL for resized uploads.
 *
 * This function constructs and returns the URL for resized images that have been uploaded.
 *
 * @return string The URL for resized uploads.
 */
function sbi_get_resized_uploads_url()
{
	$upload = wp_upload_dir();

	$base_url = $upload['baseurl'];
	$home_url = home_url();

	if (strpos($home_url, 'https:') !== false) {
		$base_url = str_replace('http:', 'https:', $base_url);
	}

	return apply_filters('sbi_resize_url', trailingslashit($base_url) . trailingslashit(SBI_UPLOADS_NAME));
}

/**
 * Used to clear caches when transients aren't working
 * properly
 */
function sb_instagram_cron_clear_cache()
{
}

/**
 * Clears the caches for the Instagram Feed Pro plugin.
 *
 * This function is responsible for clearing any cached data related to the Instagram Feed Pro plugin.
 * It ensures that the latest data is fetched and displayed.
 *
 * @return void
 */
function sbi_clear_caches()
{
	global $wpdb;

	$cache_table_name = $wpdb->prefix . 'sbi_feed_caches';

	$sql = "
		UPDATE $cache_table_name
		SET cache_value = ''
		WHERE cache_key NOT IN ( 'posts_backup', 'header_backup' );";
	$wpdb->query($sql);
}

/**
 * When certain events occur, page caches need to
 * clear or errors occur or changes will not be seen
 */
function sb_instagram_clear_page_caches()
{
	$clear_page_caches = apply_filters('sbi_clear_page_caches', true);
	if (!$clear_page_caches) {
		return;
	}

	if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
		/* Clear WP fastest cache*/
		$GLOBALS['wp_fastest_cache']->deleteCache();
	}

	if (function_exists('wp_cache_clear_cache')) {
		wp_cache_clear_cache();
	}

	if (class_exists('W3_Plugin_TotalCacheAdmin')) {
		$plugin_totalcacheadmin = &w3_instance('W3_Plugin_TotalCacheAdmin');

		$plugin_totalcacheadmin->flush_all();
	}

	if (function_exists('rocket_clean_domain')) {
		rocket_clean_domain();
	}

	if (class_exists('autoptimizeCache')) {
		/* Clear autoptimize */
		autoptimizeCache::clearall();
	}

	// Litespeed Cache.
	if (method_exists('LiteSpeed_Cache_API', 'purge')) {
		LiteSpeed_Cache_API::purge('esi.instagram-feed');
	}
}

/**
 * Meant to be updated in an AJAX request from moderation mode
 * on the front end
 */
function sbi_update_mod_mode_settings()
{
	if (!sbi_current_user_can('manage_instagram_feed_options')) {
		wp_send_json_error('cant');
	}
	$post_id = isset($_POST['post_id']) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';

	$atts_raw = isset($_POST['atts']) ? json_decode(wp_unslash($_POST['atts']), true) : array();
	if (is_array($atts_raw)) {
		$atts_raw = SB_Instagram_Settings::sanitize_raw_atts($atts_raw);
	} else {
		$atts_raw = array();
	}
	$atts = $atts_raw; // now sanitized.

	$database_settings = sbi_get_database_settings();
	$instagram_feed_settings = new SB_Instagram_Settings($atts, $database_settings);

	$instagram_feed_settings->set_feed_type_and_terms();
	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();

	$nonce = isset($_POST['locator_nonce']) ? sanitize_text_field(wp_unslash($_POST['locator_nonce'])) : '';
	if (!wp_verify_nonce($nonce, esc_attr('sbi-locator-nonce-' . $post_id . '-'))) {
		wp_send_json_error('cant nonce');
	}

	$legacy_settings = SB_Instagram_Settings_Pro::get_legacy_feed_settings();
	$remove_ids = array();

	// phpcs:ignore WordPress.Security.NonceVerification
	if (!empty($_POST['ids'])) {
		$remove_ids = array_map('sbi_sanitize_instagram_ids', $_POST['ids']);
	}

	// save the new setting as string.
	$legacy_settings['hidephotos'] = implode(', ', $remove_ids);

	$legacy_setting_string = sbi_json_encode($legacy_settings);
	update_option('sbi_legacy_feed_settings', $legacy_setting_string);

	header('Content-Type: application/json; charset=utf-8');
	echo $legacy_setting_string;
	die();
}

add_action('wp_ajax_sbi_update_mod_mode_settings', 'sbi_update_mod_mode_settings');

/**
 * Meant to be updated in an AJAX request from moderation mode
 * on the front end
 */
function sbi_update_mod_mode_white_list()
{
	if (!sbi_current_user_can('manage_instagram_feed_options')) {
		wp_send_json_error('cant');
	}
	$post_id = isset($_POST['post_id']) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';

	$atts_raw = isset($_POST['atts']) ? json_decode(wp_unslash($_POST['atts']), true) : array();
	if (is_array($atts_raw)) {
		$atts_raw = SB_Instagram_Settings::sanitize_raw_atts($atts_raw);
	} else {
		$atts_raw = array();
	}
	$atts = $atts_raw; // now sanitized.

	$database_settings = sbi_get_database_settings();
	$instagram_feed_settings = new SB_Instagram_Settings($atts, $database_settings);

	$instagram_feed_settings->set_feed_type_and_terms();
	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();

	$nonce = isset($_POST['locator_nonce']) ? sanitize_text_field(wp_unslash($_POST['locator_nonce'])) : '';
	if (!wp_verify_nonce($nonce, esc_attr('sbi-locator-nonce-' . $post_id . '-'))) {
		wp_send_json_error('cant nonce');
	}

	$white_index = isset($_POST['db_index']) ? sanitize_text_field($_POST['db_index']) : false;
	$permanent = isset($_POST['permanent']) && $_POST['permanent'] == 'true';
	$current_white_names = get_option('sb_instagram_white_list_names', array());

	if ($white_index == '') {
		$new_index = count($current_white_names) + 1;

		while (in_array($new_index, $current_white_names)) {
			$new_index++;
		}
		$white_index = (string)$new_index;

		// user doesn't know the new name so echo it out here and add a message.
		echo esc_html($white_index);
	}

	$white_list_name = 'sb_instagram_white_lists_' . $white_index;
	$white_ids = array();

	// append new id to remove id list if unique.
	if (isset($_POST['ids']) && is_array($_POST['ids'])) {
		foreach ($_POST['ids'] as $id) {
			$white_ids[] = sanitize_text_field($id);
		}

		update_option($white_list_name, $white_ids, false);
	}

	// update white list names.
	if (!in_array($white_index, $current_white_names)) {
		$current_white_names[] = $white_index;
		update_option('sb_instagram_white_list_names', $current_white_names, false);
	}

	$permanent_white_lists = get_option('sb_permanent_white_lists', array());

	if ($permanent) {
		if (!in_array($white_index, $permanent_white_lists, true)) {
			$permanent_white_lists[] = $white_index;
		}
		update_option('sb_permanent_white_lists', $permanent_white_lists, false);
	} elseif (in_array($white_index, $permanent_white_lists, true)) {
		$update_wl = array();
		foreach ($permanent_white_lists as $wl) {
			if ($wl !== $white_index) {
				$update_wl[] = $wl;
			}
		}
		update_option('sb_permanent_white_lists', $update_wl, false);
	}

	sbi_clear_caches();

	set_transient('sb_wlupdated_' . $white_index, 'true', 3600);

	die();
}

add_action('wp_ajax_sbi_update_mod_mode_white_list', 'sbi_update_mod_mode_white_list');

/**
 * Makes the JavaScript file available and enqueues the stylesheet
 * for the plugin
 *
 * @param bool $enqueue Whether or not to enqueue the script and stylesheet.
 */
function sb_instagram_scripts_enqueue($enqueue = false)
{
	// Options to pass to JS file.
	$sb_instagram_settings = sbi_get_database_settings();

	// legacy settings.
	$path = ! is_admin() && Util::sbi_legacy_css_enabled() ? 'css/legacy/' : 'css/';
	wp_enqueue_script("jquery");

	$js_file = 'js/sbi-scripts.min.js';
	$css_file = $path . 'sbi-styles.min.css';
	if (Util::isDebugging() || Util::is_script_debug()) {
		$js_file = 'js/sbi-scripts.js';
		$css_file = $path . 'sbi-styles.css';
	}

	if (isset($sb_instagram_settings['enqueue_js_in_head']) && $sb_instagram_settings['enqueue_js_in_head']) {
		wp_enqueue_script('sbi_scripts', trailingslashit(SBI_PLUGIN_URL) . $js_file, array('jquery'), SBIVER, false);
	} else {
		wp_register_script('sbi_scripts', trailingslashit(SBI_PLUGIN_URL) . $js_file, array('jquery'), SBIVER, true);
	}

	if (isset($sb_instagram_settings['enqueue_css_in_shortcode']) && $sb_instagram_settings['enqueue_css_in_shortcode']) {
		wp_register_style('sbi_styles', trailingslashit(SBI_PLUGIN_URL) . $css_file, array(), SBIVER);
	} else {
		wp_enqueue_style('sbi_styles', trailingslashit(SBI_PLUGIN_URL) . $css_file, array(), SBIVER);
	}
	wp_register_style('sbi_moderation_mode', trailingslashit(SBI_PLUGIN_URL) . 'css/sbi-moderation-mode.css', array('sbi_styles'), SBIVER);

	$br_adjust = !(isset($sb_instagram_settings['sbi_br_adjust']) && ($sb_instagram_settings['sbi_br_adjust'] == 'false' || $sb_instagram_settings['sbi_br_adjust'] == '0' || !$sb_instagram_settings['sbi_br_adjust']));

	$data = array(
		'font_method' => 'svg',
		'resized_url' => sbi_get_resized_uploads_url(),
		'placeholder' => trailingslashit(SBI_PLUGIN_URL) . 'img/placeholder.png',
		'br_adjust' => $br_adjust,
	);
	if (isset($sb_instagram_settings['sb_instagram_disable_mob_swipe']) && $sb_instagram_settings['sb_instagram_disable_mob_swipe']) {
		$data['no_mob_swipe'] = true;
	}

	$translations = array(
		'share' => __('Share', 'instagram-feed')
	);
	// Pass option to JS file.
	wp_localize_script('sbi_scripts', 'sb_instagram_js_options', $data);
	wp_localize_script('sbi_scripts', 'sbiTranslations', $translations);
	if ($enqueue) {
		wp_enqueue_style('sbi_styles');
		wp_enqueue_script('sbi_scripts', trailingslashit(SBI_PLUGIN_URL) . $js_file, array('jquery'), SBIVER, true);
	}
}

add_action('wp_enqueue_scripts', 'sb_instagram_scripts_enqueue', 2);

/**
 * Registers the media vine JavaScript file
 */
function sb_instagram_media_vine_js_register()
{
	wp_register_script('sb_instagram_mediavine_scripts', trailingslashit(SBI_PLUGIN_URL) . 'js/sb-instagram-mediavine.js', array('jquery', 'sbi_scripts'), SBIVER, true);
}

add_action('wp_enqueue_scripts', 'sb_instagram_media_vine_js_register');

/**
 * Adds the ajax url and custom JavaScript to the page
 */
function sb_instagram_custom_js()
{
	$options = get_option('sb_instagram_settings');
	isset($options['sb_instagram_custom_js']) ? $sb_instagram_custom_js = trim($options['sb_instagram_custom_js']) : $sb_instagram_custom_js = '';

	echo '<!-- Custom Feeds for Instagram JS -->';
	echo "\r\n";
	echo '<script type="text/javascript">';
	echo "\r\n";
	echo 'var sbiajaxurl = "' . esc_url(admin_url('admin-ajax.php')) . '";';
	echo "\r\n";

	if (!empty($sb_instagram_custom_js)) { ?>
		window.sbi_custom_js = function(){
		$ = jQuery;
		<?php echo stripslashes($sb_instagram_custom_js); ?>
		}
	<?php }

	echo "\r\n";
	echo '</script>';
	echo "\r\n";
}

add_action('wp_footer', 'sb_instagram_custom_js');

add_action('wp_head', 'sb_instagram_custom_css');

/**
 * Adds the custom CSS to the page
 */
function sb_instagram_custom_css()
{
	$options = sbi_get_database_settings();

	isset($options['sb_instagram_custom_css']) ? $sb_instagram_custom_css = trim($options['sb_instagram_custom_css']) : $sb_instagram_custom_css = '';

	// Show CSS if an admin (so can see Hide Photos link), if including Custom CSS or if hiding some photos.
	(current_user_can('edit_posts') || !empty($sb_instagram_custom_css)) ? $sbi_show_css = true : $sbi_show_css = false;

	if ($sbi_show_css) {
		echo '<!-- Custom Feeds for Instagram CSS -->';
	}
	if ($sbi_show_css) {
		echo "\r\n";
	}
	if ($sbi_show_css) {
		echo '<style type="text/css">';
	}

	if (!empty($sb_instagram_custom_css)) {
		echo "\r\n";
		echo wp_strip_all_tags(stripslashes($sb_instagram_custom_css));
	}

	if (current_user_can('edit_posts')) {
		echo "\r\n";
		echo "#sbi_mod_link, #sbi_mod_error{ display: block !important; }";
	}

	if ($sbi_show_css) {
		echo "\r\n";
	}
	if ($sbi_show_css) {
		echo '</style>';
	}
	if ($sbi_show_css) {
		echo "\r\n";
	}
}

/**
 * Used to change the number of posts in the api request. Useful for filtered posts
 * or special caching situations.
 *
 * @param int   $num The number of posts to request.
 * @param array $settings The settings for the feed.
 *
 * @return int
 */
function sbi_raise_num_in_request($num, $settings)
{
	if (
		$settings['sortby'] === 'random'
		|| !empty($settings['includewords'])
		|| !empty($settings['excludewords'])
		|| $settings['media'] !== 'all'
		|| !empty($settings['whitelist_ids'])
	) {
		if ($num > 6) {
			return min($num * 4, 100);
		} else {
			return 30;
		}
	}
	return $num;
}

add_filter('sbi_num_in_request', 'sbi_raise_num_in_request', 5, 2);

/**
 * Load the critical notice for logged in users.
 */
function sbi_critical_error_notice()
{
	// Don't do anything for guests.
	if (!is_user_logged_in()) {
		return;
	}

	// Only show this to users who are not tracked.
	if (!current_user_can('manage_instagram_feed_options')) {
		return;
	}

	global $sb_instagram_posts_manager;
	if (!$sb_instagram_posts_manager->are_critical_errors()) {
		return;
	}


	// Don't show if already dismissed.
	if (get_option('sbi_dismiss_critical_notice', false)) {
		return;
	}

	$db_settings = sbi_get_database_settings();
	if (isset($db_settings['disable_admin_notice']) && ($db_settings['disable_admin_notice'] === 'on' || $db_settings['disable_admin_notice'] === true)) {
		return;
	}

	?>
	<div class="sbi-critical-notice sbi-critical-notice-hide">
		<div class="sbi-critical-notice-icon">
			<img src="<?php echo esc_url(SBI_PLUGIN_URL . 'img/insta-logo.png'); ?>" width="45"
				 alt="Instagram Feed icon"/>
		</div>
		<div class="sbi-critical-notice-text">
			<h3><?php esc_html_e('Instagram Feed Critical Issue', 'instagram-feed'); ?></h3>
			<p>
				<?php
				$doc_url = admin_url() . 'admin.php?page=sbi-settings';
				// Translators: %s is the link to the article where more details about critical are listed.
				printf(esc_html__('An issue is preventing your Instagram Feeds from updating. %1$sResolve this issue%2$s.', 'instagram-feed'), '<a href="' . esc_url($doc_url) . '" target="_blank">', '</a>');
				?>
			</p>
		</div>
		<div class="sbi-critical-notice-close">&times;</div>
	</div>
	<style type="text/css">
		.sbi-critical-notice {
			position: fixed;
			bottom: 20px;
			right: 15px;
			font-family: Arial, Helvetica, "Trebuchet MS", sans-serif;
			background: #fff;
			box-shadow: 0 0 10px 0 #dedede;
			padding: 10px 10px;
			display: flex;
			align-items: center;
			justify-content: center;
			width: 325px;
			max-width: calc(100% - 30px);
			border-radius: 6px;
			transition: bottom 700ms ease;
			z-index: 10000;
		}

		.sbi-critical-notice h3 {
			font-size: 13px;
			color: #222;
			font-weight: 700;
			margin: 0 0 4px;
			padding: 0;
			line-height: 1;
			border: none;
		}

		.sbi-critical-notice p {
			font-size: 12px;
			color: #7f7f7f;
			font-weight: 400;
			margin: 0;
			padding: 0;
			line-height: 1.2;
			border: none;
		}

		.sbi-critical-notice p a {
			color: #7f7f7f;
			font-size: 12px;
			line-height: 1.2;
			margin: 0;
			padding: 0;
			text-decoration: underline;
			font-weight: 400;
		}

		.sbi-critical-notice p a:hover {
			color: #666;
		}

		.sbi-critical-notice-icon img {
			height: auto;
			display: block;
			margin: 0;
		}

		.sbi-critical-notice-icon {
			padding: 0;
			border-radius: 4px;
			flex-grow: 0;
			flex-shrink: 0;
			margin-right: 12px;
			overflow: hidden;
		}

		.sbi-critical-notice-close {
			padding: 10px;
			margin: -12px -9px 0 0;
			border: none;
			box-shadow: none;
			border-radius: 0;
			color: #7f7f7f;
			background: transparent;
			line-height: 1;
			align-self: flex-start;
			cursor: pointer;
			font-weight: 400;
		}

		.sbi-critical-notice-close:hover,
		.sbi-critical-notice-close:focus {
			color: #111;
		}

		.sbi-critical-notice.sbi-critical-notice-hide {
			bottom: -200px;
		}
	</style>
	<?php

	if (!wp_script_is('jquery', 'queue')) {
		wp_enqueue_script('jquery');
	}
	?>
	<script>
		if ('undefined' !== typeof jQuery) {
			jQuery(document).ready(function ($) {
				/* Don't show the notice if we don't have a way to hide it (no js, no jQuery). */
				$(document.querySelector('.sbi-critical-notice')).removeClass('sbi-critical-notice-hide');
				$(document.querySelector('.sbi-critical-notice-close')).on('click', function (e) {
					e.preventDefault();
					$(this).closest('.sbi-critical-notice').addClass('sbi-critical-notice-hide');
					$.ajax({
						url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
						method: 'POST',
						data: {
							action: 'sbi_dismiss_critical_notice',
							nonce: '<?php echo esc_js(wp_create_nonce('sbi-critical-notice')); ?>',
						}
					});
				});
			});
		}
	</script>
	<?php
}

add_action('wp_footer', 'sbi_critical_error_notice', 300);

/**
 * Ajax handler to hide the critical notice.
 */
function sbi_dismiss_critical_notice()
{
	if (!sbi_current_user_can('manage_instagram_feed_options')) {
		wp_send_json_error();
	}
	check_ajax_referer('sbi-critical-notice', 'nonce');

	update_option('sbi_dismiss_critical_notice', 1, false);

	wp_die();
}

add_action('wp_ajax_sbi_dismiss_critical_notice', 'sbi_dismiss_critical_notice');

/**
 * Schedule the report email to be sent
 *
 * @return void
 */
function sbi_schedule_report_email()
{
	$options = get_option('sb_instagram_settings', array());

	$input = isset($options['email_notification']) ? $options['email_notification'] : 'monday';
	$timestamp = strtotime('next ' . $input);
	$timestamp = $timestamp + (3600 * 24 * 7);

	$six_am_local = $timestamp + sbi_get_utc_offset() + (6 * 60 * 60);

	wp_schedule_event($six_am_local, 'sbiweekly', 'sb_instagram_feed_issue_email');
}

/**
 * Send the report email
 *
 * @return bool
 */
function sbi_send_report_email()
{
	$options = get_option('sb_instagram_settings');

	$to_string = !empty($options['email_notification_addresses']) ? str_replace(' ', '', $options['email_notification_addresses']) : get_option('admin_email', '');

	$to_array_raw = explode(',', $to_string);
	$to_array = array();

	foreach ($to_array_raw as $email) {
		if (is_email($email)) {
			$to_array[] = $email;
		}
	}

	if (empty($to_array)) {
		return false;
	}

	$headers = array('Content-Type: text/html; charset=utf-8');

	$header_image = SBI_PLUGIN_URL . 'img/balloon-120.png';

	$link = admin_url('admin.php?page=sbi-settings');
	$footer_link = admin_url('admin.php?page=sbi-settings&view=advanced&flag=emails');

	$is_expiration_notice = false;

	if (isset($options['connected_accounts'])) {
		foreach ($options['connected_accounts'] as $account) {
			if (
				$account['type'] === 'basic'
				&& isset($account['private'])
				&& sbi_private_account_near_expiration($account)
			) {
				$is_expiration_notice = true;
			}
		}
	}

	if (!$is_expiration_notice) {
		/* translators: Home url */
		$title = sprintf(__('Instagram Feed Report for %s', 'instagram-feed'), str_replace(array('http://', 'https://'), '', home_url()));
		$bold = __('There\'s an Issue with an Instagram Feed on Your Website', 'instagram-feed');
		$details = '<p>' . __('An Instagram feed on your website is currently unable to connect to Instagram to retrieve new posts. Don\'t worry, your feed is still being displayed using a cached version, but is no longer able to display new posts.', 'instagram-feed') . '</p>';
		/* translators: 1. Opening <a> tag 2. Closing </a> tag */
		$details .= '<p>' . sprintf(__('This is caused by an issue with your Instagram account connecting to the Instagram API. For information on the exact issue and directions on how to resolve it, please visit the %1$sInstagram Feed settings page%2$s on your website.', 'instagram-feed'), '<a href="' . esc_url($link) . '">', '</a>') . '</p>';
	} else {
		$title = __('Your Private Instagram Feed Account Needs to be Reauthenticated', 'instagram-feed');
		$bold = __('Access Token Refresh Needed', 'instagram-feed');
		$details = '<p>' . __('As your Instagram account is set to be "Private", Instagram requires that you reauthenticate your account every 60 days. This a courtesy email to let you know that you need to take action to allow the Instagram feed on your website to continue updating. If you don\'t refresh your account, then a backup cache will be displayed instead.', 'instagram-feed') . '</p>';
		/* translators: 1$s: opening anchor tag to Instagram help page, 2$s: closing anchor tag, 3$s: opening anchor tag to Instagram Feed settings page, 4$s: closing anchor tag */
		$details .= '<p>' . sprintf(__('To prevent your account expiring every 60 days %1$sswitch your account to be public%2$s. For more information and to refresh your account, click here to visit the %3$sInstagram Feed settings page%4$s on your website.', 'instagram-feed'), '<a href="https://help.instagram.com/116024195217477/In">', '</a>', '<a href="' . esc_url($link) . '">', '</a>') . '</p>';
	}
	$message_content = '<h6 style="padding:0;word-wrap:normal;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;font-weight:bold;line-height:130%;font-size: 16px;color:#444444;text-align:inherit;margin:0 0 20px 0;Margin:0 0 20px 0;">' . $bold . '</h6>' . $details;
	include_once SBI_PLUGIN_DIR . 'inc/class-sb-instagram-education.php';
	$educator = new SB_Instagram_Education();
	$dyk_message = $educator->dyk_display();
	ob_start();
	include SBI_PLUGIN_DIR . 'inc/email.php';
	$email_body = ob_get_contents();
	ob_get_clean();

	return wp_mail($to_array, $title, $email_body, $headers);
}

/**
 * Check if the email should be sent and send it
 *
 * @return void
 */
function sbi_maybe_send_feed_issue_email()
{
	global $sb_instagram_posts_manager;
	if (!$sb_instagram_posts_manager->are_critical_errors()) {
		return;
	}
	$options = get_option('sb_instagram_settings');

	if (isset($options['enable_email_report']) && empty($options['enable_email_report'])) {
		return;
	}

	sbi_send_report_email();
}

add_action('sb_instagram_feed_issue_email', 'sbi_maybe_send_feed_issue_email');

/**
 * Updates the value of a specified option in the database.
 *
 * @param string $option_name The name of the option to update.
 * @param mixed  $option_value The new value for the option.
 * @param bool   $autoload Optional. Whether to load the option when WordPress starts up. Default is true.
 *
 * @return bool True if the option was updated successfully, false otherwise.
 */
function sbi_update_option($option_name, $option_value, $autoload = true)
{
	return update_option($option_name, $option_value, $autoload = true);
}

/**
 * Retrieves the value of a specified option from the database.
 *
 * @param string $option_name The name of the option to retrieve.
 * @param mixed  $default Optional. The default value to return if the option does not exist. Default is false.
 *
 * @return mixed The value of the option if it exists, the default value otherwise.
 */
function sbi_get_option($option_name, $default)
{
	return get_option($option_name, $default);
}

/**
 * Checks if the current version of the plugin is the pro version.
 *
 * @return bool True if the current version is the pro version, false otherwise.
 */
function sbi_is_pro_version()
{
	return defined('SBI_STORE_URL');
}

/**
 * Check if license is in inactive state
 *
 * @return bool
 */
function sbi_license_inactive_state()
{
	return empty(sbi_builder_pro()->license_service->get_license_key);
}

/**
 * Check if license is expired and need to limit the Pro function and should display notices
 *
 * @return bool
 */
function sbi_license_notices_active()
{
	if (empty(sbi_builder_pro()->license_service->get_license_key)) {
		return true;
	}
	return sbi_builder_pro()->license_service->expiredLicenseWithGracePeriodEnded;
}

/**
 * Check should add free plugin submenu for the free version
 *
 * @param string $plugin The plugin name.
 *
 * @return bool
 * @since 2.0
 */
function sbi_should_add_free_plugin_submenu($plugin)
{
	if (!sbi_builder_pro()->license_service->should_disable_pro_features) {
		return false;
	}

	if ($plugin === 'facebook' && !is_plugin_active('custom-facebook-feed/custom-facebook-feed.php') && !is_plugin_active('custom-facebook-feed-pro/custom-facebook-feed.php')) {
		return true;
	}

	if ($plugin === 'youtube' && !is_plugin_active('youtube-feed-pro/youtube-feed-pro.php') && !is_plugin_active('feeds-for-youtube/youtube-feed.php')) {
		return true;
	}

	if ($plugin === 'twitter' && !is_plugin_active('custom-twitter-feeds/custom-twitter-feed.php') && !is_plugin_active('custom-twitter-feeds-pro/custom-twitter-feed.php')) {
		return true;
	}

	return false;
}

/**
 * Check if there are critical errors.
 *
 * @return bool
 */
function sbi_has_critical_errors()
{
	return Util::sbi_has_admin_errors();
}

add_filter('sb_instagram_feed_has_admin_errors', 'sbi_has_critical_errors');
