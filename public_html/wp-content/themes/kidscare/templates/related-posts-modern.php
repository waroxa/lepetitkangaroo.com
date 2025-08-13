<?php
/**
 * The template 'Style 1' to displaying related posts
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

$kidscare_link        = get_permalink();
$kidscare_post_format = get_post_format();
$kidscare_post_format = empty( $kidscare_post_format ) ? 'standard' : str_replace( 'post-format-', '', $kidscare_post_format );
?><div id="post-<?php the_ID(); ?>" <?php post_class( 'related_item post_format_' . esc_attr( $kidscare_post_format ) ); ?>>
	<?php
	kidscare_show_post_featured(
		array(
			'thumb_size'    => apply_filters( 'kidscare_filter_related_thumb_size', kidscare_get_thumb_size( (int) kidscare_get_theme_option( 'related_posts' ) == 1 ? 'huge' : 'big' ) ),
			'show_no_image' => kidscare_get_no_image() != '',
			'post_info'     => '<div class="post_header entry-header">'
									. '<div class="post_categories">' . wp_kses( kidscare_get_post_categories( '' ), 'kidscare_kses_content' ) . '</div>'
									. '<h6 class="post_title entry-title"><a href="' . esc_url( $kidscare_link ) . '">' . wp_kses_data( '' == get_the_title() ? esc_html__( '- No title -', 'kidscare' ) : get_the_title() )
									. '</a></h6>'
									. ( in_array( get_post_type(), array( 'post', 'attachment' ) )
											? '<div class="post_meta"><a href="' . esc_url( $kidscare_link ) . '" class="post_meta_item post_date">' . wp_kses_data( kidscare_get_date() ) . '</a></div>'
											: '' )
								. '</div>',
		)
	);
	?>
</div>
