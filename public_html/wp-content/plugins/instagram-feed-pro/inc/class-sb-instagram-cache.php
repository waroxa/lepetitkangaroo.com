<?php

use InstagramFeed\SB_Instagram_Data_Encryption;

/**
 * Instagram Feed Cache
 *
 * For the new feed builder
 *
 * @since 6.0
 */
class SB_Instagram_Cache
{
	/**
	 * Feed ID
	 *
	 * @var int
	 */
	protected $feed_id;

	/**
	 * Page number
	 *
	 * @var int
	 */
	protected $page;

	/**
	 * Suffix for cache keys
	 *
	 * @var string
	 */
	protected $suffix;

	/**
	 * Whether the feed is a legacy feed
	 *
	 * @var bool
	 */
	protected $is_legacy;

	/**
	 * Cache time in seconds
	 *
	 * @var int
	 */
	protected $cache_time;

	/**
	 * Posts cache
	 *
	 * @var array
	 */
	protected $posts;

	/**
	 * Posts page cache
	 *
	 * @var array
	 */
	protected $posts_page;

	/**
	 * Whether the cache is expired
	 *
	 * @var bool
	 */
	protected $is_expired;

	/**
	 * Header cache
	 *
	 * @var array
	 */
	protected $header;

	/**
	 * Resized images cache
	 *
	 * @var array
	 */
	protected $resized_images;

	/**
	 * Meta cache
	 *
	 * @var array
	 */
	protected $meta;

	/**
	 * Posts backup cache
	 *
	 * @var array
	 */
	protected $posts_backup;

	/**
	 * Header backup cache
	 *
	 * @var array
	 */
	protected $header_backup;

	/**
	 * Encryption object
	 *
	 * @var object|SB_Instagram_Data_Encryption
	 */
	protected $encryption;

	/**
	 * SBI_Cache constructor. Set the feed id, cache key, legacy
	 *
	 * @param string $feed_id Feed ID.
	 * @param int    $page Page number.
	 * @param int    $cache_time Cache time in seconds.
	 *
	 * @since 6.0
	 */
	public function __construct($feed_id, $page = 1, $cache_time = 0)
	{
		$this->cache_time = (int)$cache_time;
		$this->is_legacy = strpos($feed_id, '*') !== 0;
		$this->page = $page;

		if ($this->page === 1) {
			$this->suffix = '';
		} else {
			$this->suffix = '_' . $this->page;
		}

		$this->feed_id = str_replace('*', '', $feed_id);

		if (is_admin()) {
			$this->feed_id .= $this->maybe_customizer_suffix();
		}

		$this->encryption = new SB_Instagram_Data_Encryption();
	}

	/**
	 * Add suffix to cache keys used in the customizer
	 *
	 * @return string
	 *
	 * @since 6.0
	 */
	protected function maybe_customizer_suffix()
	{
		$additional_suffix = '';
		$in_customizer = !empty($_POST['previewSettings']) || (isset($_GET['page']) && $_GET['page'] === 'sbi-feed-builder');
		if ($in_customizer) {
			$additional_suffix .= '_CUSTOMIZER';

			if (!empty($_POST['moderationShoppableMode'])) {
				$additional_suffix .= '_MODMODE';
				$offset = $this->page > 1 ? $this->page : '';
				$additional_suffix .= $offset;
				if (!empty($_POST['moderationShoppableShowSelected'])) {
					$additional_suffix .= '_SELECTED';
				}
			}
		}

		return $additional_suffix;
	}

	/**
	 * Clears caches in the WP Options table used mostly by legacy feeds.
	 * Also resets caches created by common page caching plugins
	 *
	 * @param false $hard_clear Hard clear.
	 * @since 6.0
	 */
	public static function clear_legacy($hard_clear = false)
	{
		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'sbi_feed_caches';

		if ($hard_clear) {
			$wpdb->query(
				"DELETE FROM $cache_table_name
				WHERE feed_id LIKE ('sbi\_%')
				AND cache_key NOT IN ( 'posts_backup', 'header_backup' );"
			);
		} else {
			$wpdb->query(
				"UPDATE $cache_table_name
				SET cache_value = ''
				WHERE feed_id LIKE ('sbi\_%')
				AND cache_key NOT IN ( 'posts_backup', 'header_backup' );"
			);
		}

		sb_instagram_clear_page_caches();
	}

	/**
	 * Set all caches based on available data.
	 *
	 * @since 6.0
	 */
	public function retrieve_and_set()
	{
		$expired = true;
		$existing_caches = $this->query_sbi_feed_caches();

		foreach ($existing_caches as $cache) {
			switch ($cache['cache_key']) {
				case 'posts':
					$this->posts = $cache['cache_value'];
					if (strtotime($cache['last_updated']) > time() - $this->cache_time) {
						$expired = false;
					}

					if (empty($cache['cache_value'])) {
						$expired = true;
					}
					break;
				case 'posts' . $this->suffix:
					$this->posts_page = $cache['cache_value'];
					break;
				case 'header':
					$this->header = $cache['cache_value'];
					break;
				case 'resized_images' . $this->suffix:
					$this->resized_images = $cache['cache_value'];
					break;
				case 'meta' . $this->suffix:
					$this->meta = $cache['cache_value'];
					break;
				case 'posts_backup' . $this->suffix:
					$this->posts_backup = $cache['cache_value'];
					break;
				case 'header_backup' . $this->suffix:
					$this->header_backup = $cache['cache_value'];
					break;
			}
		}

		$this->is_expired = $expired;

		if ($this->cache_time < 1) {
			$this->is_expired = true;
		}
	}

	/**
	 * Get all available caches from the sbi_cache table.
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	protected function query_sbi_feed_caches()
	{
		$feed_cache = wp_cache_get($this->get_wp_cache_key());
		if (false === $feed_cache) {
			global $wpdb;
			$cache_table_name = $wpdb->prefix . 'sbi_feed_caches';

			if ($this->page === 1) {
				$sql = $wpdb->prepare(
					"SELECT * FROM $cache_table_name
					WHERE feed_id = %s",
					$this->feed_id
				);
			} else {
				$sql = $wpdb->prepare(
					"SELECT * FROM $cache_table_name
					WHERE feed_id = %s
					AND cache_key IN ( 'posts', %s, %s, %s )",
					$this->feed_id,
					'posts_' . $this->page,
					'resized_images_' . $this->page,
					'meta_' . $this->page
				);
			}

			$feed_cache = $wpdb->get_results($sql, ARRAY_A);

			wp_cache_set($this->get_wp_cache_key(), $feed_cache);
		}

		return $feed_cache;
	}

	/**
	 * Key used to get the wp cache key
	 *
	 * @return string
	 *
	 * @since 6.0
	 */
	protected function get_wp_cache_key()
	{
		return 'sbi_feed_' . $this->feed_id . '_' . $this->page;
	}

	/**
	 * Whether the cache needs to be refreshed
	 *
	 * @param string $cache_type Cache type.
	 *
	 * @return bool
	 *
	 * @since 6.0
	 */
	public function is_expired($cache_type = 'posts')
	{

		if ($cache_type !== 'posts') {
			$cache = $this->get($cache_type);

			return empty($cache) || $this->is_expired;
		}
		if ($this->page === 1) {
			return $this->is_expired;
		}

		if ($this->is_expired) {
			return true;
		}
		if (empty($this->posts_page)) {
			return true;
		}
		return false;
	}

	/**
	 * Get data currently stored in the database for the type
	 *
	 * @param string $type Type of cache to get.
	 *
	 * @return string
	 *
	 * @since 6.0
	 */
	public function get($type)
	{
		$return = array();
		switch ($type) {
			case 'posts':
				$return = $this->posts;
				break;
			case 'posts' . $this->suffix:
				$return = $this->posts_page;
				break;
			case 'header':
				$return = $this->header;
				break;
			case 'resized_images':
				$return = $this->resized_images;
				break;
			case 'meta':
				$return = $this->meta;
				break;
			case 'posts_backup':
				$return = $this->posts_backup;
				break;
			case 'header_backup':
				$return = $this->header_backup;
				break;
		}

		return $this->maybe_decrypt($return);
	}

	/**
	 * Uses a raw value and attempts to decrypt it
	 *
	 * @param string $value Raw value.
	 *
	 * @return bool|string
	 *
	 * @since 6.0
	 */
	protected function maybe_decrypt($value)
	{
		if (!is_string($value)) {
			return $value;
		}
		if (strpos($value, '{') === 0) {
			return $value;
		}

		$decrypted = $this->encryption->decrypt($value);

		if (!$decrypted) {
			return $value;
		}

		return $decrypted;
	}

	/**
	 * Update a single cache with new data. Try to accept any data and convert it
	 * to JSON if needed
	 *
	 * @param string              $cache_type Cache type.
	 * @param array|object|string $cache_value Cache value.
	 * @param bool                $include_backup Include backup.
	 * @param bool                $cron_update Cron update.
	 *
	 * @return int
	 *
	 * @since 6.0
	 */
	public function update_or_insert($cache_type, $cache_value, $include_backup = true, $cron_update = true)
	{
		$this->clear_wp_cache();

		if ($this->page > 1 || ($cache_type !== 'posts' && $cache_type !== 'header')) {
			$cron_update = false;
		}

		if (strpos($this->feed_id, '_CUSTOMIZER') !== false) {
			$cron_update = false;
		}

		$cache_key = $cache_type . $this->suffix;

		$this->set($cache_key, $cache_value);

		if (is_array($cache_value) || is_object($cache_value)) {
			$cache_value = sbi_json_encode($cache_value);
		}

		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'sbi_feed_caches';

		$sql = $wpdb->prepare(
			"SELECT * FROM $cache_table_name
			WHERE feed_id = %s
			AND cache_key = %s",
			$this->feed_id,
			$cache_key
		);

		$existing = $wpdb->get_results($sql, ARRAY_A);
		$data = array();
		$where = array();
		$format = array();

		$data['cache_value'] = $this->maybe_encrypt($cache_value);
		$format[] = '%s';

		$data['last_updated'] = date('Y-m-d H:i:s');
		$format[] = '%s';

		if (!empty($existing[0])) {
			$where['feed_id'] = $this->feed_id;
			$where_format[] = '%s';

			$where['cache_key'] = $cache_key;
			$where_format[] = '%s';

			$affected = $wpdb->update($cache_table_name, $data, $where, $format, $where_format);
		} else {
			$data['cache_key'] = $cache_key;
			$format[] = '%s';

			$data['cron_update'] = $cron_update === true ? 'yes' : '';
			$format[] = '%s';

			$data['feed_id'] = $this->feed_id;
			$format[] = '%s';

			$affected = $wpdb->insert($cache_table_name, $data, $format);
		}

		return $affected;
	}

	/**
	 * Delete the wp_cache
	 *
	 * @since 6.0
	 */
	protected function clear_wp_cache()
	{
		wp_cache_delete($this->get_wp_cache_key());
	}

	/**
	 * Set a single cache with new data
	 *
	 * @param string $type Cache type.
	 * @param array  $cache_value Cache value.
	 *
	 * @since 6.0
	 */
	public function set($type, $cache_value)
	{
		switch ($type) {
			case 'posts':
				$this->posts = $cache_value;
				break;
			case 'posts' . $this->suffix:
				$this->posts_page = $cache_value;
				break;
			case 'header':
				$this->header = $cache_value;
				break;
			case 'resized_images':
				$this->resized_images = $cache_value;
				break;
			case 'meta':
				$this->meta = $cache_value;
				break;
			case 'posts_backup':
				$this->posts_backup = $cache_value;
				break;
			case 'header_backup':
				$this->header_backup = $cache_value;
				break;
		}
	}

	/**
	 * Uses a raw value and attempts to encrypt it
	 *
	 * @param string $value Raw value.
	 *
	 * @return bool|string
	 *
	 * @since 6.0
	 */
	protected function maybe_encrypt($value)
	{
		if (!empty($value) && !is_string($value)) {
			$value = sbi_json_encode($value);
		}
		if (empty($value)) {
			return $value;
		}

		return $this->encryption->encrypt($value);
	}

	/**
	 * Tasks to do after a new set of posts are retrieved
	 *
	 * @since 6.0
	 */
	public function after_new_posts_retrieved()
	{
		if ($this->page === 1) {
			$this->clear('all');
		}
	}

	/**
	 * Resets caches after they expire
	 *
	 * @param string $type Type of cache to clear.
	 *
	 * @return bool|false|int
	 *
	 * @since 6.0
	 */
	public function clear($type)
	{
		$this->clear_wp_cache();

		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'sbi_feed_caches';

		$feed_id = str_replace(array('_MODMODE', '_CUSTOMIZER', '_CUSTOMIZER_MODMODE', '_CUSTOMIZER_MODMODE_SELECTED'), '', $this->feed_id);
		if ($type === 'show_selected') {
			$feed_id = str_replace(array('_SELECTED'), '', $feed_id);

			$mod_mode_where = esc_sql($feed_id) . '_CUSTOMIZER_MODMODE_SELECTED%';
			$affected = $wpdb->query(
				$wpdb->prepare(
					"UPDATE $cache_table_name
				SET cache_value = ''
				WHERE feed_id like %s",
					$mod_mode_where
				)
			);
		} elseif ($type === 'all') {
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE $cache_table_name
					SET cache_value = ''
					WHERE feed_id = %s
					AND cache_key NOT IN ( 'posts', 'posts_backup', 'header_backup' );",
					$feed_id
				)
			);

			$affected = $wpdb->query(
				$wpdb->prepare(
					"UPDATE $cache_table_name
					SET cache_value = ''
					WHERE feed_id = %s",
					$feed_id . '_CUSTOMIZER'
				)
			);

			if (isset($_POST['moderationShoppableMode']) && isset($_POST['moderationShoppableModeOffset']) && empty($_POST['moderationShoppableModeOffset'])) {
				$mod_mode_where = esc_sql($feed_id) . '_CUSTOMIZER_MODMODE%';
				$affected = $wpdb->query(
					$wpdb->prepare(
						"UPDATE $cache_table_name
						SET cache_value = ''
						WHERE feed_id like %s",
						$mod_mode_where
					)
				);
			}
		} else {
			$data = array('cache_value' => '');
			$format = array('%s');

			$where['feed_id'] = $feed_id;
			$where_format[] = '%s';

			$where['cache_key'] = $type . $this->suffix;
			$where_format[] = '%s';

			$wpdb->update($cache_table_name, $data, $where, $format, $where_format);

			$where['feed_id'] = $feed_id . '_CUSTOMIZER';

			$affected = $wpdb->update($cache_table_name, $data, $where, $format, $where_format);

			if (isset($_POST['moderationShoppableMode'])) {
				$where['feed_id'] = $feed_id . '_CUSTOMIZER_MODMODE';

				$affected = $wpdb->update($cache_table_name, $data, $where, $format, $where_format);
			}
		}

		return $affected;
	}

	/**
	 * Get the cache for the customizer
	 *
	 * @return array|mixed
	 */
	public function get_customizer_cache()
	{
		if (strpos($this->feed_id, '_CUSTOMIZER') === false) {
			$feed_id = $this->feed_id . '_CUSTOMIZER';
		} else {
			$feed_id = $this->feed_id;
		}
		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'sbi_feed_caches';

		$sql = $wpdb->prepare(
			"SELECT * FROM $cache_table_name
			WHERE feed_id = %s
			AND cache_key = 'posts'",
			$feed_id
		);
		$results = $wpdb->get_results($sql, ARRAY_A);

		$return = array();
		if (!empty($results[0])) {
			$return = $this->maybe_decrypt($results[0]['cache_value']);
			$return = json_decode($return, true);

			$return = isset($return['data']) ? $return['data'] : array();
		}

		return $return;
	}

	/**
	 * Update the last updated time for the posts and header cache.
	 *
	 * @return bool|int
	 */
	public function update_last_updated()
	{
		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'sbi_feed_caches';

		$data = array();
		$format = array();
		$where_format = array();

		$data['last_updated'] = date('Y-m-d H:i:s');
		$format[] = '%s';

		$where['feed_id'] = $this->feed_id;
		$where_format[] = '%s';

		$where['cache_key'] = 'posts';
		$where_format[] = '%s';

		$wpdb->update($cache_table_name, $data, $where, $format, $where_format);

		$data = array();
		$format = array();
		$where_format = array();

		$data['last_updated'] = date('Y-m-d H:i:s');
		$format[] = '%s';

		$where['feed_id'] = $this->feed_id;
		$where_format[] = '%s';

		$where['cache_key'] = 'header';
		$where_format[] = '%s';

		return $wpdb->update($cache_table_name, $data, $where, $format, $where_format);
	}

	/**
	 * Get active/all cache count.
	 *
	 * @param bool $active when set to true only items updated in the last months are returned.
	 *
	 * @return int
	 */
	public function get_cache_count($active = false)
	{
		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'sbi_feed_caches';
		$query = "SELECT COUNT(DISTINCT feed_id, cache_key) as cache_count FROM $cache_table_name WHERE feed_id Not Like '%_CUSTOMIZER%'";

		if ($active === true) {
			$query .= " AND feed_id Not Like '%_MODMODE%' AND last_updated >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
		}

		$sql = $wpdb->prepare($query);
		$caches = $wpdb->get_results($sql);

		if (!empty($caches)) {
			return $caches[0]->cache_count;
		}

		return 0;
	}
}
