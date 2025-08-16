<?php
/**
 * Child-Theme functions and definitions
 */

function kidscare_child_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri(). '/style.css' );
    wp_enqueue_script( 'kidscare-child-mobile', get_stylesheet_directory_uri() . '/js/mobile-drawer.js', array(), null, true );
}

add_action( 'wp_enqueue_scripts', 'kidscare_child_scripts' );

class Kidscare_Main_Nav_Walker extends Walker_Nav_Menu {
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $item_output = '';
        parent::start_el( $item_output, $item, $depth, $args, $id );
        $item_output = preg_replace( '/\\s?sf-with-ul/', '', $item_output );
        $item_output = preg_replace( '/<span[^>]*icon-ellipsis[^>]*><\\/span>/', '', $item_output );
        $output .= $item_output;
    }
}
