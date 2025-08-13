<?php
/**
 * The custom template to display the content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.50
 */

$kidscare_template_args = get_query_var( 'kidscare_template_args' );
if ( is_array( $kidscare_template_args ) ) {
	$kidscare_columns    = empty( $kidscare_template_args['columns'] ) ? 2 : max( 1, $kidscare_template_args['columns'] );
	$kidscare_blog_style = array( $kidscare_template_args['type'], $kidscare_columns );
} else {
	$kidscare_blog_style = explode( '_', kidscare_get_theme_option( 'blog_style' ) );
	$kidscare_columns    = empty( $kidscare_blog_style[1] ) ? 2 : max( 1, $kidscare_blog_style[1] );
}
$kidscare_blog_id       = kidscare_get_custom_blog_id( join( '_', $kidscare_blog_style ) );
$kidscare_blog_style[0] = str_replace( 'blog-custom-', '', $kidscare_blog_style[0] );
$kidscare_expanded      = ! kidscare_sidebar_present() && kidscare_is_on( kidscare_get_theme_option( 'expand_content' ) );
$kidscare_animation     = kidscare_get_theme_option( 'blog_animation' );
$kidscare_components    = kidscare_array_get_keys_by_value( kidscare_get_theme_option( 'meta_parts' ) );

$kidscare_post_format   = get_post_format();
$kidscare_post_format   = empty( $kidscare_post_format ) ? 'standard' : str_replace( 'post-format-', '', $kidscare_post_format );

$kidscare_blog_meta     = kidscare_get_custom_layout_meta( $kidscare_blog_id );
$kidscare_custom_style  = ! empty( $kidscare_blog_meta['scripts_required'] ) ? $kidscare_blog_meta['scripts_required'] : 'none';

if ( ! empty( $kidscare_template_args['slider'] ) || $kidscare_columns > 1 || ! kidscare_is_off( $kidscare_custom_style ) ) {
	?><div class="
		<?php
		if ( ! empty( $kidscare_template_args['slider'] ) ) {
			echo 'slider-slide swiper-slide';
		} else {
			echo ( kidscare_is_off( $kidscare_custom_style ) ? 'column' : sprintf( '%1$s_item %1$s_item', $kidscare_custom_style ) ) . '-1_' . esc_attr( $kidscare_columns );
		}
		?>
	">
	<?php
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
			'post_item post_format_' . esc_attr( $kidscare_post_format )
					. ' post_layout_custom post_layout_custom_' . esc_attr( $kidscare_columns )
					. ' post_layout_' . esc_attr( $kidscare_blog_style[0] )
					. ' post_layout_' . esc_attr( $kidscare_blog_style[0] ) . '_' . esc_attr( $kidscare_columns )
					. ( ! kidscare_is_off( $kidscare_custom_style )
						? ' post_layout_' . esc_attr( $kidscare_custom_style )
							. ' post_layout_' . esc_attr( $kidscare_custom_style ) . '_' . esc_attr( $kidscare_columns )
						: ''
						)
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
	// Custom layout
	do_action( 'kidscare_action_show_layout', $kidscare_blog_id, get_the_ID() );
	?>
</article><?php
if ( ! empty( $kidscare_template_args['slider'] ) || $kidscare_columns > 1 || ! kidscare_is_off( $kidscare_custom_style ) ) {
	?></div><?php
	// Need opening PHP-tag above just after </div>, because <div> is a inline-block element (used as column)!
}
