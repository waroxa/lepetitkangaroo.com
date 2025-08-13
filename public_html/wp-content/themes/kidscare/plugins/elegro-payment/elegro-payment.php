<?php
/* Elegro Crypto Payment support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'kidscare_elegro_payment_theme_setup9' ) ) {
    add_action( 'after_setup_theme', 'kidscare_elegro_payment_theme_setup9', 9 );
    function kidscare_elegro_payment_theme_setup9() {
        if ( kidscare_exists_elegro_payment() ) {
            add_filter( 'kidscare_filter_merge_styles', 'kidscare_elegro_payment_merge_styles' );
        }
        if ( is_admin() ) {
            add_filter( 'kidscare_filter_tgmpa_required_plugins', 'kidscare_elegro_payment_tgmpa_required_plugins' );
        }
    }
}

// Filter to add in the required plugins list
if ( ! function_exists( 'kidscare_elegro_payment_tgmpa_required_plugins' ) ) {

    function kidscare_elegro_payment_tgmpa_required_plugins( $list = array() ) {
        if ( kidscare_storage_isset( 'required_plugins', 'elegro-payment' ) && kidscare_storage_get_array( 'required_plugins', 'elegro-payment', 'install' ) !== false ) {
            // Elegro plugin
            $list[] = array(
                'name'     => kidscare_storage_get_array( 'required_plugins', 'elegro-payment', 'title' ),
                'slug'     => 'elegro-payment',
                'required' => false,
            );

        }
        return $list;
    }
}

// Check if this plugin installed and activated
if ( ! function_exists( 'kidscare_exists_elegro_payment' ) ) {
    function kidscare_exists_elegro_payment() {
        return class_exists( 'WC_Elegro_Payment' );
    }
}

// Merge custom styles
if ( ! function_exists( 'kidscare_elegro_payment_merge_styles' ) ) {
    function kidscare_elegro_payment_merge_styles( $list ) {
        $list[] = 'plugins/elegro-payment/_elegro-payment.scss';
        return $list;
    }
}