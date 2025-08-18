<?php
/**
 * Child-Theme functions and definitions
 */

function kidscare_child_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'parent-style' ) );
    wp_enqueue_script( 'desktop-header', get_stylesheet_directory_uri() . '/js/desktop-header.js', array(), null, true );
}

add_action( 'wp_enqueue_scripts', 'kidscare_child_scripts' );

function kidscare_child_disable_menu_fallback() {
    wp_dequeue_script( 'kidscare-init' );
    wp_deregister_script( 'kidscare-init' );
    wp_dequeue_script( 'superfish' );
    wp_deregister_script( 'superfish' );
}

add_action( 'wp_enqueue_scripts', 'kidscare_child_disable_menu_fallback', 1001 );

?>
