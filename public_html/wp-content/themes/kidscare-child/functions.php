<?php
/**
 * Child-Theme functions and definitions
 */

function kidscare_child_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri(). '/style.css' );
}

add_filter('wp_enqueue_scripts', 'kidscare_child_scripts');

?>