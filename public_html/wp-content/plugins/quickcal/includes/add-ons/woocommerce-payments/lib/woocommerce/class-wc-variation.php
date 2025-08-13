<?php

class QuickCal_WC_Variation {

	private static $variations = array();

	private function __construct( $variation_id ) {

	}

	# ------------------
	# Filter To Modify the aailable variation output
	# woocommerce_available_variation
	# in class-wc-product.php
	# apply_filters('woocommerce_available_variation', $variation_data_to_return, $product_class_obj, $variation_class_obj);
	# ------------------

	public static function woocommerce_available_variation($variation_data_to_return, $product_class_obj, $variation_class_obj) {

		$attributes = $variation_data_to_return['attributes'];
		$price = strip_tags($variation_data_to_return['price_html']);

		$variation_title = $price ? $price . ' - ' : '';
		$i = 0;
		$separator = ', ';
        foreach ($attributes as $taxonomy_name => $term_slug) {
            if ($i > 0) {
                $variation_title .= $separator;
            }

            // Check if the term is stored as a taxonomy term
            $term_name = get_term_by('slug', $term_slug, $taxonomy_name);

            if ($term_name) {
                // Use the human-readable name from the taxonomy
                $variation_title .= $term_name->name;
            } else {
                // If no term was found, the value might already be human-readable
                $variation_title .= ucwords(str_replace('-', ' ', $term_slug));
            }

            $i++;
        }

		$variation_data_to_return['variation_title'] = $variation_title;

		return $variation_data_to_return;
	}
	
}