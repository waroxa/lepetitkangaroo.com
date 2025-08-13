<?php
/**
 * The Gallery template to display posts
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

$kidscare_template_args = get_query_var( 'kidscare_template_args' );
if ( is_array( $kidscare_template_args ) ) {
	$kidscare_columns    = empty( $kidscare_template_args['columns'] ) ? 2 : max( 1, $kidscare_template_args['columns'] );
	$kidscare_blog_style = array( $kidscare_template_args['type'], $kidscare_columns );
} else {
	$kidscare_blog_style = explode( '_', kidscare_get_theme_option( 'blog_style' ) );
	$kidscare_columns    = empty( $kidscare_blog_style[1] ) ? 2 : max( 1, $kidscare_blog_style[1] );
}
$kidscare_post_format = get_post_format();
$kidscare_post_format = empty( $kidscare_post_format ) ? 'standard' : str_replace( 'post-format-', '', $kidscare_post_format );
$kidscare_animation   = kidscare_get_theme_option( 'blog_animation' );
$kidscare_image       = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

?><div class="
<?php
if ( ! empty( $kidscare_template_args['slider'] ) ) {
	echo ' slider-slide swiper-slide';
} else {
	echo 'masonry_item masonry_item-1_' . esc_attr( $kidscare_columns );
}
?>
"><article id="post-<?php the_ID(); ?>" 
	<?php
	post_class(
		'post_item post_format_' . esc_attr( $kidscare_post_format )
		. ' post_layout_portfolio'
		. ' post_layout_portfolio_' . esc_attr( $kidscare_columns )
		. ' post_layout_gallery'
		. ' post_layout_gallery_' . esc_attr( $kidscare_columns )
	);
	echo ( ! kidscare_is_off( $kidscare_animation ) && empty( $kidscare_template_args['slider'] ) ? ' data-animation="' . esc_attr( kidscare_get_animation_classes( $kidscare_animation ) ) . '"' : '' );
	?>
	data-size="
		<?php
		if ( ! empty( $kidscare_image[1] ) && ! empty( $kidscare_image[2] ) ) {
			echo intval( $kidscare_image[1] ) . 'x' . intval( $kidscare_image[2] );}
		?>
	"
	data-src="
		<?php
		if ( ! empty( $kidscare_image[0] ) ) {
			echo esc_url( $kidscare_image[0] );}
		?>
	"
>
<?php

	// Sticky label
if ( is_sticky() && ! is_paged() ) {
	?>
		<span class="post_label label_sticky"></span>
		<?php
}

	// Featured image
	$kidscare_image_hover = 'icon';
if ( in_array( $kidscare_image_hover, array( 'icons', 'zoom' ) ) ) {
	$kidscare_image_hover = 'dots';
}
$kidscare_components = kidscare_array_get_keys_by_value( kidscare_get_theme_option( 'meta_parts' ) );
kidscare_show_post_featured(
	array(
		'hover'         => $kidscare_image_hover,
		'no_links'      => ! empty( $kidscare_template_args['no_links'] ),
		'thumb_size'    => kidscare_get_thumb_size( strpos( kidscare_get_theme_option( 'body_style' ), 'full' ) !== false || $kidscare_columns < 3 ? 'masonry-big' : 'masonry' ),
		'thumb_only'    => true,
		'show_no_image' => true,
		'post_info'     => '<div class="post_details">'
						. '<h2 class="post_title">'
							. ( empty( $kidscare_template_args['no_links'] )
								? '<a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>'
								: esc_html( get_the_title() )
								)
						. '</h2>'
						. '<div class="post_description">'
							. ( ! empty( $kidscare_components )
								? kidscare_show_post_meta(
									apply_filters(
										'kidscare_filter_post_meta_args', array(
											'components' => $kidscare_components,
											'seo'      => false,
											'echo'     => false,
										), $kidscare_blog_style[0], $kidscare_columns
									)
								)
								: ''
								)
							. ( empty( $kidscare_template_args['hide_excerpt'] )
								? '<div class="post_description_content">' . get_the_excerpt() . '</div>'
								: ''
								)
							. ( empty( $kidscare_template_args['no_links'] )
								? '<a href="' . esc_url( get_permalink() ) . '" class="theme_button post_readmore"><span class="post_readmore_label">' . esc_html__( 'Learn more', 'kidscare' ) . '</span></a>'
								: ''
								)
						. '</div>'
					. '</div>',
	)
);
?>
</article></div><?php
// Need opening PHP-tag above, because <article> is a inline-block element (used as column)!
