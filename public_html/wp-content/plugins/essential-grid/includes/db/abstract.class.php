<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

abstract class Essential_Grid_Db_Abstract
{
	/**
	 * @var string
	 */
	protected $cache_group;

	/**
	 * @var int
	 */
	protected $cache_expire;

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @param string $table
	 * @param string $group
	 * @param int    $expire
	 */
	public function __construct($table, $group, $expire) {
		$this->table = $table;
		$this->cache_expire = $expire;
		$this->cache_group = $group;
	}

	public function clear_cache() {
		if ( wp_cache_supports( 'flush_group' ) ) {
			wp_cache_flush_group( $this->cache_group );
		} else {
			wp_cache_flush();
		}
	}
	
	/**
	 * @param string $output  Optional. The required return type, see $wpdb->get_results for description
	 *
	 * @return array|object|null  Database query results.
	 */
	public function get_all( $output = OBJECT ) {
		global $wpdb;

		$value = wp_cache_get( 'all', $this->cache_group, false, $found );

		if ( ! $found ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$value = $wpdb->get_results( "SELECT * FROM {$this->table}", $output );
			wp_cache_set( 'all', $value, $this->cache_group, $this->cache_expire );
		}

		return $value;
	}

	/**
	 * @param int $id
	 * @param string $output  Optional. The required return type, see $wpdb->get_row for description
	 *
	 * @return array|object|null|void  Database query result in format specified by $output or null on failure.
	 */
	public function get( $id, $output = OBJECT ) {
		global $wpdb;

		$id = intval( $id );
		if ( ! $id ) return false;

		$value = wp_cache_get( $id, $this->cache_group, false, $found );

		if ( ! $found ) {
			// phpcs:ignore
			$value = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ), $output );
			if (!empty($value)) {
				wp_cache_set( $id, $value, $this->cache_group, $this->cache_expire );
			}
		}

		return $value;
	}

	/**
	 * @param array $data
	 *
	 * @return int|false  The number of rows inserted, or false on error.
	 */
	public function insert( $data ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert( $this->table, $data );

		$this->clear_cache();

		return $result;
	}
	
	/**
	 * @return int
	 */
	public function get_insert_id() {
		global $wpdb;
		return $wpdb->insert_id;
	}

	/**
	 * @param array $data
	 * @param int   $id
	 *
	 * @return int|false  The number of rows updated, or false on error.
	 */
	public function update($data, $id) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->update( $this->table, $data, ['id' => $id] );

		$this->clear_cache();

		return $result;
	}

	/**
	 * @param int $id
	 *
	 * @return int|false  The number of rows deleted, or false on error.
	 */
	public function delete( $id ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->delete( $this->table, ['id' => $id] );

		$this->clear_cache();

		return $result;
	}

	public function drop() {
		global $wpdb;

		// phpcs:ignore
		$wpdb->query( "DROP TABLE {$this->table}" );

		$this->clear_cache();
	}

}
