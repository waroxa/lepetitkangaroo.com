<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Db_Navigation_Skin extends Essential_Grid_Db_Abstract
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
		$value = wp_cache_get( 'by_id_' . $id, $this->cache_group, false, $found );

		if ( ! $found ) {
			$value = parent::get( $id, $output );
			if (!empty($value)) {
				$value['css'] = Essential_Grid_Base::stripslashes_deep($value['css']);
				wp_cache_set( 'by_id_' . $id, $value, $this->cache_group, $this->cache_expire );
			}
		}

		return $value;
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
			if (!empty($value)) {
				$value['css'] = Essential_Grid_Base::stripslashes_deep($value['css']);
				wp_cache_set( 'by_handle_' . $handle, $value, $this->cache_group, $this->cache_expire );
			}
		}

		return $value;
	}

	/**
	 * @param string $handle
	 *
	 * @return int|false  The number of rows deleted, or false on error.
	 */
	public function delete_by_handle( $handle ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->delete( $this->table, ['handle' => $handle] );

		$this->clear_cache();

		return $result;
	}
}
