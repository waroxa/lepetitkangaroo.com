<?php
/**
 * The Portfolio template to display the content
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
		. ( is_sticky() && ! is_paged() ? ' sticky' : '' )
	);
	echo ( ! kidscare_is_off( $kidscare_animation ) && empty( $kidscare_template_args['slider'] ) ? ' data-animation="' . esc_attr( kidscare_get_animation_classes( $kidscare_animation ) ) . '"' : '' );
	?>
>
<?php

// Sticky label
if ( is_sticky() && ! is_paged() ) {
	?>
		<span class="post_label label_sticky"></span>
		<?php
}

	$kidscare_image_hover = ! empty( $kidscare_template_args['hover'] ) && ! kidscare_is_inherit( $kidscare_template_args['hover'] )
								? $kidscare_template_args['hover']
								: kidscare_get_theme_option( 'image_hover' );
	// Featured image
	kidscare_show_post_featured(
		array(
			'hover'         => $kidscare_image_hover,
			'no_links'      => ! empty( $kidscare_template_args['no_links'] ),
			'thumb_size'    => kidscare_get_thumb_size(
				strpos( kidscare_get_theme_option( 'body_style' ), 'full' ) !== false || $kidscare_columns < 3
								? 'masonry-big'
				: 'masonry'
			),
			'show_no_image' => true,
			'class'         => 'dots' == $kidscare_image_hover ? 'hover_with_info' : '',
			'post_info'     => 'dots' == $kidscare_image_hover ? '<div class="post_info">' . esc_html( get_the_title() ) . '</div>' : '',
		)
	);
	?>
</article></div><?php
// Need opening PHP-tag above, because <article> is a inline-block element (used as column)!