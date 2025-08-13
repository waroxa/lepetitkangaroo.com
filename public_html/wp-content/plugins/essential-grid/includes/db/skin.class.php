<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Db_Skin extends Essential_Grid_Db_Abstract
{
	/**
	 * @inheritdoc 
	 */
	public function get_all( $output = ARRAY_A ) {
		return parent::get_all( $output );
	}

	/**
	 * @inheritdoc
	 */
	public function get( $id, $output = ARRAY_A ) {
		return parent::get( $id, $output );
	}

	/**
	 * @param string $handle
	 *
	 * @return array|null  Database query result or null on failure.
	 */
	public function get_by_handle( $handle ) {
		global $wpdb;

		if ( empty( $handle ) ) return null;

		$value = wp_cache_get( 'by_handle_' . $handle, $this->cache_group, false, $found );

		if ( ! $found ) {
			// phpcs:ignore
			$value = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE handle = %s", $handle ), ARRAY_A );
			if ( !empty( $value ) ) {
				wp_cache_set( 'by_handle_' . $handle, $value, $this->cache_group, $this->cache_expire );
			}
		}

		return $value;
	}

	/**
	 * @return int
	 */
	public function get_max_id() {
		global $wpdb;

		// phpcs:ignore
		$value = $wpdb->get_row( "SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1", ARRAY_A );
		if ( !empty( $value ) ) return $value['id'];
		
		return 0;
	}

}
