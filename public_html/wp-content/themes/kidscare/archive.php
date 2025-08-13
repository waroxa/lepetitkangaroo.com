<?php
/**
 * The template file to display taxonomies archive
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.57
 */

// Redirect to the template page (if exists) for output current taxonomy
if ( is_category() || is_tag() || is_tax() ) {
	$kidscare_term = get_queried_object();
	global $wp_query;
	if ( ! empty( $kidscare_term->taxonomy ) && ! empty( $wp_query->posts[0]->post_type ) ) {
		$kidscare_taxonomy  = kidscare_get_post_type_taxonomy( $wp_query->posts[0]->post_type );
		if ( $kidscare_taxonomy == $kidscare_term->taxonomy ) {
			$kidscare_template_page_id = kidscare_get_template_page_id( array(
				'post_type'  => $wp_query->posts[0]->post_type,
				'parent_cat' => $kidscare_term->term_id
			) );
			if ( 0 < $kidscare_template_page_id ) {
				wp_safe_redirect( get_permalink( $kidscare_template_page_id ) );
				exit;
			}
		}
	}
}
// If template page is not exists - display default blog archive template
get_template_part( apply_filters( 'kidscare_filter_get_template_part', kidscare_blog_archive_get_template() ) );
