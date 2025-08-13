<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */
 
if( !defined( 'ABSPATH') ) exit();

class Essential_Grid_Woocommerce {

	const ARG_REGULAR_PRICE_FROM = 'reg_price_from';
	const ARG_REGULAR_PRICE_TO = 'reg_price_to';
	const ARG_SALE_PRICE_FROM = 'sale_price_from';
	const ARG_SALE_PRICE_TO = 'sale_price_to';
	const ARG_IN_STOCK_ONLY = 'instock_only';
	const ARG_FEATURED_ONLY = 'featured_only';
	
	const META_REGULAR_PRICE = '_regular_price';
	const META_SALE_PRICE = '_sale_price';
	const META_STOCK_STATUS = '_stock_status'; //can be 'instock' or 'outofstock'
	const META_SKU = '_sku'; //can be 'instock' or 'outofstock'
	const META_FEATURED = '_featured'; //can be 'instock' or 'outofstock'
	const META_STOCK = '_stock'; //can be 'instock' or 'outofstock'
	
	const SORTBY_NUMSALES = 'meta_num_total_sales';
	const SORTBY_REGULAR_PRICE = 'meta_num__regular_price';
	const SORTBY_SALE_PRICE = 'meta_num__sale_price';
	const SORTBY_SKU = 'meta__sku';
	const SORTBY_STOCK = 'meta_num_stock';

	/**
	 * add hooks 
	 */
	public static function add_hooks(){
		add_filter('essgrid_get_all_meta_handle', ['Essential_Grid_Woocommerce', 'get_meta_handles']);
		add_action('essgrid_meta_dialog_post', ['Essential_Grid_Woocommerce', 'add_handles_to_meta_picker']);
	}
	
	/**
	 * true / false if the wpml plugin exists
	 */
	public static function is_woo_exists(){
		return class_exists('Woocommerce');
	}
	
	/**
	 * valdiate that wpml exists
	 */
	private static function validate_woo_exists(){
		if(!self::is_woo_exists())
			Essential_Grid_Base::throw_error(esc_attr__('The Woocommerce plugin does not exist', 'essential-grid'));
	}
	
	/**
	 * get wc post types
	 */
	public static function get_custom_post_types(){
		$arr = [];
		$arr['product'] = esc_attr__('Product', 'essential-grid');
		$arr['product_variation'] = esc_attr__('Product Variation', 'essential-grid');
		
		return(apply_filters('essgrid_wc_get_custom_post_types', $arr));
	}
	
	/**
	 * get price query
	 */
	private static function get_price_query($priceFrom, $priceTo, $metaTag){
		if(empty($priceFrom))
			$priceFrom = 0;
			
		if(empty($priceTo))
			$priceTo = 9999999999;
		
		$query = [
			'key' => $metaTag,
			'value' => [$priceFrom, $priceTo],
			'type' => 'numeric',
			'compare' => 'BETWEEN'
		];
		
		return(apply_filters('essgrid_wc_get_price_query', $query));
	}
	
	/**
	 * get meta query for filtering woocommerce posts. 
	 */
	public static function get_meta_query($args){
		$base = new Essential_Grid_Base();
		
		$regPriceFrom = $base->getVar($args, self::ARG_REGULAR_PRICE_FROM);
		$regPriceTo = $base->getVar($args, self::ARG_REGULAR_PRICE_TO);
		
		$salePriceFrom = $base->getVar($args, self::ARG_SALE_PRICE_FROM);
		$salePriceTo = $base->getVar($args, self::ARG_SALE_PRICE_TO);
		
		$inStockOnly = $base->getVar($args, self::ARG_IN_STOCK_ONLY);
		$featuredOnly = $base->getVar($args, self::ARG_FEATURED_ONLY);
		
		$arrQueries = [];
		
		//get regular price array
		if(!empty($regPriceFrom) || !empty($regPriceTo)){
			$arrQueries[] = self::get_price_query($regPriceFrom, $regPriceTo, self::META_REGULAR_PRICE);
		}
		
		//get sale price array
		if(!empty($salePriceFrom) || !empty($salePriceTo)){
			$arrQueries[] = self::get_price_query($salePriceFrom, $salePriceTo, self::META_SALE_PRICE);
		}
		
		if($inStockOnly == 'true'){
			$query = ['key' => self::META_STOCK_STATUS, 'value' => 'instock'];
			$arrQueries[] = $query;
		}
		
		if($featuredOnly == 'true'){
			$query = ['key' => self::META_FEATURED, 'value' => 'yes'];
			$arrQueries[] = $query;
		}
		
		$query = [];
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		if(!empty($arrQueries)) $query = ['meta_query'=>$arrQueries];
			
		return(apply_filters('essgrid_wc_get_meta_query', $query));
	}	
	
	/**
	 * get sortby function including standart wp sortby array
	 */
	public static function get_arr_sort_by(){
		$arrSortBy = [];
		$arrSortBy[self::SORTBY_REGULAR_PRICE] = esc_attr__('Price', 'essential-grid');
		$arrSortBy[self::SORTBY_NUMSALES] = esc_attr__('Number Of Sales', 'essential-grid');
		$arrSortBy[self::SORTBY_SKU] = esc_attr__('SKU', 'essential-grid');
		$arrSortBy[self::SORTBY_STOCK] = esc_attr__('Stock Quantity', 'essential-grid');
		
		$arrOutput = [];
		$arrOutput['opt_disabled_1'] = esc_attr__('---- WooCommerce Filters ----', 'essential-grid');
		$arrOutput = array_merge($arrOutput, $arrSortBy);
		$arrOutput['opt_disabled_2'] = esc_attr__('---- Regular Filters ----', 'essential-grid');
		
		return(apply_filters('essgrid_wc_get_arr_sort_by', $arrOutput));
	}
	
	/**
	 * check if product is on sale
	 */
	public static function check_if_on_sale($post_id){
		$is_30 = self::version_check('3.0');
		$product = ($is_30) ? wc_get_product($post_id) : get_product($post_id);
		$is_on_sale = $product !== false && $product->is_on_sale();
		
		return apply_filters('essgrid_wc_check_if_on_sale', $is_on_sale, $post_id);
	}
	
	/**
	 * check if product is on sale
	 */
	public static function check_if_is_featured($post_id){
		$is_30 = self::version_check('3.0');
		$product = ($is_30) ? wc_get_product($post_id) : get_product($post_id);
		$is_featured = $product !== false && $product->is_featured();
		
		return apply_filters('essgrid_wc_check_if_is_featured', $is_featured, $post_id);
	}
	
	/**
	 * get sortby function including standart wp sortby array
	 */
	public static function get_value_by_meta($post_id, $meta, $separator = ',', $catmax = false){
		$meta_value = '';
		$is_30 = self::version_check('3.0');
		$product = ($is_30) ? wc_get_product($post_id) : get_product($post_id);
		if($product !== false){
			switch($meta){
				case 'wc_price':
					if ( $product->is_in_stock() ) {
						$meta_value = wc_price($product->get_price());
					} else {
						$meta_value = esc_html__( 'Out of Stock', 'essential-grid' );
					}
				break;
				case 'wc_price_no_cur':
					if ( $product->is_in_stock() ) {
						$meta_value = $product->get_price();
					} else {
						$meta_value = esc_html__( 'Out of Stock', 'essential-grid' );
					}
				break;
				case 'wc_full_price':
					if ( $product->is_in_stock() ) {
						$meta_value = $product->get_price_html();
					} else {
						$meta_value = esc_html__( 'Out of Stock', 'essential-grid' );
					}
				break;
				case 'wc_stock':
					if($is_30){
						$meta_value = $product->get_stock_quantity();
					}else{
						$meta_value = $product->get_total_stock();
					}
				break;
				case 'wc_rating':
					if($is_30){
						$meta_value = @wc_get_rating_html( $product->get_average_rating() );
					}else{
						$meta_value = $product->get_rating_html();
					}
				break;
				case 'wc_star_rating':
					if($is_30){
						$cur_rating = @wc_get_rating_html( $product->get_average_rating() );
					}else{
						$cur_rating = $product->get_rating_html();
					}
					if($cur_rating !== '')
						$meta_value = '<div class="esg-starring">'.$cur_rating.'</div>';
				break;
				case 'wc_categories':
					if($is_30){
						$categories = wc_get_product_category_list($post_id, $separator);
						
						// new catmax option only available for WC v3.0+
						if($catmax !== false) {
							$categories = explode($separator, $categories);
							$categories = array_slice($categories, 0, $catmax, true);
							$categories = implode($separator, $categories);
						}
						
					}else{
						$categories = $product->get_categories($separator);
					}
					$meta_value = $categories;
				break;
				case 'wc_add_to_cart':
					$meta_value = $product->add_to_cart_url();
				break;
				case 'wc_add_to_cart_button': //get whole button from WooCommerce
					$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
					$ajax_cart_en = get_option( 'woocommerce_enable_ajax_add_to_cart' ) == 'yes';
					$assets_path = str_replace( ['http:', 'https:'], '', WC()->plugin_url() ) . '/assets/';
					$frontend_script_path = $assets_path . 'js/frontend/';
					
					if ( $ajax_cart_en ){
						wp_enqueue_script( 'esg-wc-add-to-cart', $frontend_script_path . 'add-to-cart' . $suffix . '.js', ['jquery'], WC_VERSION, true );
						
						global $esg_wc_is_localized;
						if($esg_wc_is_localized === false){ //load it only one time
							wp_localize_script( 'esg-wc-add-to-cart', 'wc_add_to_cart_params', apply_filters( 'wc_add_to_cart_params', [
								'ajax_url'                => WC()->ajax_url(),
								'wc_ajax_url'             => WC_AJAX::get_endpoint( "%%endpoint%%" ),
								'ajax_loader_url'         => apply_filters( 'woocommerce_ajax_loader_url', $assets_path . 'images/ajax-loader@2x.gif' ),
								'i18n_view_cart'          => esc_attr__( 'View Cart', 'essential-grid' ),
								'cart_url'                => get_permalink( wc_get_page_id( 'cart' ) ),
								'is_cart'                 => is_cart(),
								'cart_redirect_after_add' => get_option( 'woocommerce_cart_redirect_after_add' )
							]) );
							$esg_wc_is_localized = true;
						}
					}

					$product_type = $is_30 ? $product->get_type() : $product->product_type;

					$meta_value = apply_filters(
						'woocommerce_loop_add_to_cart_link',
						sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s %s product_type_%s">%s</a>',
							esc_url( $product->add_to_cart_url() ),
							esc_attr( $post_id ),
							esc_attr( $product->get_sku() ),
							$product->is_purchasable() ? 'add_to_cart_button' : '',
							$product->is_purchasable() && $ajax_cart_en && esc_attr( $product_type ) !="variable" ? 'ajax_add_to_cart' : '',
							esc_attr( $product_type ),
							esc_html( $product->add_to_cart_text() )
						),
						$product
					);
				break;
				default:
					$meta_value = apply_filters('essgrid_woocommerce_meta_content', $meta_value, $meta, $post_id, $product);
				break;
			}
		}
		
		return apply_filters('essgrid_wc_get_value_by_meta', $meta_value, $meta, $post_id, $product);
	}
	
	/**
	 * get sortby function including standart wp sortby array
	 */
	public static function get_meta_array(){
		$wc_array = [
					'wc_full_price' => esc_attr__('Full Price', 'essential-grid'),
					'wc_price' => esc_attr__('Single Price', 'essential-grid'),
					'wc_price_no_cur' => esc_attr__('Single Price without currency', 'essential-grid'),
					'wc_stock' => esc_attr__('In Stock', 'essential-grid'),
					'wc_rating' => esc_attr__('Text Rating', 'essential-grid'),
					'wc_star_rating' => esc_attr__('Star Rating', 'essential-grid'),
					'wc_categories' => esc_attr__('Categories', 'essential-grid'),
					'wc_add_to_cart' => esc_attr__('Add to Cart URL', 'essential-grid'),
					'wc_add_to_cart_button' => esc_attr__('Add to Cart Button', 'essential-grid'),
		];
		
		return apply_filters('essgrid_woocommerce_meta_handle', $wc_array);
	}
	
	/**
	 * get all attached images
	 * @since: 1.5.4
	 */
	public static function get_image_attachements($post_id, $url = false, $source = 'full'){
		$is_30 = self::version_check('3.0');
		$product = ($is_30) ? wc_get_product($post_id) : get_product($post_id);
		$ret_img = '';
		if($product !== false){
			$wc_img = $product->get_gallery_attachment_ids();
			if($url){
				$images = [];
				foreach($wc_img as $img){
					$t_img = wp_get_attachment_image_src($img, $source);
					if($t_img !== false){
						$images[] = $t_img[0];
					}
				}
				$ret_img = $images;
			}else{ 
				//get URL instead of ID
				$ret_img = $wc_img;
			}
		}
		
		return apply_filters('essgrid_wc_get_image_attachements', $ret_img, $post_id, $url);
	}

	/**
	 * update Cart Overview after Ajax call
	 * @since: 2.1.0.2
	 */
	public static function woocommerce_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;
		
		ob_start();
		?>
		<span class="ess-cart-content"><?php echo esc_html($woocommerce->cart->cart_contents_count); ?><?php esc_html_e(' items - ', 'essential-grid'); ?><span class="woocommerce-Price-amount amount"><?php echo esc_html($woocommerce->cart->get_cart_total()); ?></span></span>
		<?php
		$fragments['span.ess-cart-content'] = ob_get_clean();
		return $fragments;
	}
	
	/**
	 * compare wc current version to given version
	 * 
	 * @param string $version
	 * @return bool
	 */
	public static function version_check( $version = '1.0' ) {
		global $woocommerce;
		
		if (self::is_woo_exists()) {
			if (version_compare($woocommerce->version, $version, '>=')) {
				return true;
			}
		}
		return false;
	}

	/**
	 * add meta handles to data source in skin editor as post meta
	 *
	 * @param array $meta
	 * @return array
	 */
	public static function get_meta_handles($meta)
	{
		if (!Essential_Grid_Woocommerce::is_woo_exists()) return $meta;
		
		$wc_meta = Essential_Grid_Woocommerce::get_meta_array();
		foreach($wc_meta as $handle => $name){
			$meta[] = $handle;
		}
		
		return $meta;
	}

	/**
	 * add meta handles to meta key picker dialog
	 *
	 * @return void
	 */
	public static function add_handles_to_meta_picker()
	{
		if (!Essential_Grid_Woocommerce::is_woo_exists()) return;
		
		$wc_meta = Essential_Grid_Woocommerce::get_meta_array();
		foreach($wc_meta as $handle => $name){
			echo '<tr><td>%'.esc_html($handle).'%</td><td>'.esc_html($name).'</td></tr>';
		}
	}

}
