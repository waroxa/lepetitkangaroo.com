<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Deposits plan product admin
 *
 * @package woocommerce-deposits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Plans_Product_Admin class.
 */
class WC_Deposits_Plans_Product_Admin {

	/**
	 * Class instance
	 *
	 * @var WC_Deposits_Plans_Product_Admin
	 */
	private static $instance;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'styles_and_scripts' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_data' ), 20 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_data' ), 10, 2 );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ), 5 );
		add_action( 'woocommerce_product_data_panels', array( $this, 'deposit_panels' ) );
		add_action( 'woocommerce_variation_options_pricing', array( $this, 'variation_deposit_data' ), 10, 3 );

		// Import/Export support for _wc_deposit_payment_plans meta field.
		add_filter( 'woocommerce_product_export_meta_value', array( $this, 'format_deposit_payment_plans_export' ), 10, 4 );
		add_filter( 'woocommerce_product_import_process_item_data', array( $this, 'format_deposit_payment_plans_import' ) );
	}

	/**
	 * Scripts.
	 */
	public function styles_and_scripts() {
		WC_Deposits::register_script( 'woocommerce-deposits-admin', 'admin' );
	}

	/**
	 * Show the deposits tab.
	 */
	public function add_tab() {
		include 'views/html-deposits-tab.php';
	}

	/**
	 * Show the deposits panel.
	 */
	public function deposit_panels() {
		wp_enqueue_script( 'woocommerce-deposits-admin' );
		include 'views/html-deposit-data.php';
	}

	/**
	 * Show deposits settings for variation.
	 *
	 * @since 2.1.3
	 *
	 * @param int     $loop           Position in the loop.
	 * @param array   $variation_data Variation data.
	 * @param WP_Post $variation      Post data.
	 * @return void
	 */
	public function variation_deposit_data( $loop, $variation_data, $variation ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		include 'views/html-deposit-data.php';
	}

	/**
	 * Save data.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_product_data( $post_id ) {
		$meta_to_save = array(
			'_wc_deposit_enabled'                          => '',
			'_wc_deposit_type'                             => '',
			'_wc_deposit_amount'                           => 'float',
			'_wc_deposit_payment_plans'                    => 'int',
			'_wc_deposit_selected_type'                    => '',
			'_wc_deposit_multiple_cost_by_booking_persons' => 'issetyesno',
		);
		foreach ( $meta_to_save as $meta_key => $sanitize ) {
			$value = ! empty( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : ''; // phpcs:ignore WordPress.Security -- Conditional sanitize, see below

			/**
			 * Payment Plans form data is an array. On a product level it contains both
			 * the product and all variations array elements.
			 *
			 * Filter out variation nested arrays.
			 */
			if ( '_wc_deposit_payment_plans' === $meta_key && is_array( $value ) ) {
				$value = array_filter(
					$value,
					function ( $v ) {
						return ! is_array( $v );
					}
				);
			}

			switch ( $sanitize ) {
				case 'int':
					$value = $value ? ( is_array( $value ) ? array_map( 'absint', $value ) : absint( $value ) ) : '';
					break;
				case 'float':
					$value = $value ? ( is_array( $value ) ? array_map( 'floatval', $value ) : floatval( $value ) ) : '';
					break;
				case 'yesno':
					$value = 'yes' === $value ? 'yes' : 'no';
					break;
				case 'issetyesno':
					$value = $value ? 'yes' : 'no';
					break;
				default:
					$value = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value );
			}
			WC_Deposits_Product_Meta::update_meta( $post_id, $meta_key, $value );
		}
	}

	/**
	 * Save variation data.
	 *
	 * @since 2.1.3
	 *
	 * @param int $variation_id Variation.
	 * @param int $i Variation number.
	 * @return void
	 */
	public function save_variation_data( $variation_id, $i ) {
		/**
		 * If custom deposits configuration is disabled for this variation,
		 * skip saving to ensure it uses parent product settings.
		 */
		if ( WC_Deposits_Product_Manager::is_custom_deposits_configuration_disabled( $variation_id ) ) {
			return;
		}

		$meta_to_save = array(
			'_wc_deposit_enabled'                          => '',
			'_wc_deposit_type'                             => '',
			'_wc_deposit_amount'                           => 'float',
			'_wc_deposit_payment_plans'                    => 'int',
			'_wc_deposit_selected_type'                    => '',
			'_wc_deposit_multiple_cost_by_booking_persons' => 'issetyesno',
		);
		foreach ( $meta_to_save as $meta_key => $sanitize ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput -- Sanitized later.
			$value = ! empty( $_POST[ $meta_key ][ $i ] ) ? $_POST[ $meta_key ][ $i ] : '';
			switch ( $sanitize ) {
				case 'int':
					$value = $value ? ( is_array( $value ) ? array_map( 'absint', $value ) : absint( $value ) ) : '';
					break;
				case 'float':
					$value = $value ? ( is_array( $value ) ? array_map( 'floatval', $value ) : floatval( $value ) ) : '';
					break;
				case 'yesno':
					$value = 'yes' === $value ? 'yes' : 'no';
					break;
				case 'issetyesno':
					$value = $value ? 'yes' : 'no';
					break;
				default:
					$value = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value );
			}
			WC_Deposits_Product_Meta::update_meta( $variation_id, $meta_key, $value );
		}
	}

	/**
	 * Unserialize _wc_deposit_payment_plans value for export
	 *
	 * @since 1.5.9
	 * @param string     $value   Meta Value.
	 * @param mixed      $meta    Meta Object.
	 * @param WC_Product $product Product being exported.
	 * @param array      $row     Row data.
	 * @return string $value
	 */
	public function format_deposit_payment_plans_export( $value, $meta, $product, $row ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( '_wc_deposit_payment_plans' === $meta->key ) {
			$plans = maybe_unserialize( $value );
			if ( is_array( $plans ) ) {
				return implode( ',', $plans );
			}
		}
		return $value;
	}

	/**
	 * Serialize _wc_deposit_payment_plans value for import
	 *
	 * @since 1.5.9
	 * @param  array $data Raw CSV data.
	 * @return array $data
	 */
	public function format_deposit_payment_plans_import( $data ) {
		if ( ! empty( $data['meta_data'] ) ) {
			foreach ( $data['meta_data'] as $index => $meta ) {
				if ( '_wc_deposit_payment_plans' === $meta['key'] && ! empty( $meta['value'] ) ) {
					$value = explode( ',', $meta['value'] );
					if ( ! empty( $value ) ) {
						$data['meta_data'][ $index ]['value'] = array_map( 'absint', $value );
					}
				}
			}
		}
		return $data;
	}
}

WC_Deposits_Plans_Product_Admin::get_instance();
