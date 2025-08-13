<?php
/**
 * The Classic template to display the content
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
$kidscare_expanded   = ! kidscare_sidebar_present() && kidscare_is_on( kidscare_get_theme_option( 'expand_content' ) );
$kidscare_animation  = kidscare_get_theme_option( 'blog_animation' );
$kidscare_components = kidscare_array_get_keys_by_value( kidscare_get_theme_option( 'meta_parts' ) );

$kidscare_post_format = get_post_format();
$kidscare_post_format = empty( $kidscare_post_format ) ? 'standard' : str_replace( 'post-format-', '', $kidscare_post_format );

?><div class="
<?php
if ( ! empty( $kidscare_template_args['slider'] ) ) {
	echo ' slider-slide swiper-slide';
} else {
	echo ( 'classic' == $kidscare_blog_style[0] ? 'column' : 'masonry_item masonry_item' ) . '-1_' . esc_attr( $kidscare_columns );
}
?>
"><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item post_format_' . esc_attr( $kidscare_post_format )
				. ' post_layout_classic post_layout_classic_' . esc_attr( $kidscare_columns )
				. ' post_layout_' . esc_attr( $kidscare_blog_style[0] )
				. ' post_layout_' . esc_attr( $kidscare_blog_style[0] ) . '_' . esc_attr( $kidscare_columns )
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

	// Featured image
	$kidscare_hover = ! empty( $kidscare_template_args['hover'] ) && ! kidscare_is_inherit( $kidscare_template_args['hover'] )
						? $kidscare_template_args['hover']
						: kidscare_get_theme_option( 'image_hover' );
	kidscare_show_post_featured(
		array(
			'thumb_size' => kidscare_get_thumb_size(
				'classic' == $kidscare_blog_style[0]
						? ( strpos( kidscare_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $kidscare_columns > 2 ? 'big' : 'huge' )
								: ( $kidscare_columns > 2
									? ( $kidscare_expanded ? 'med' : 'small' )
									: ( $kidscare_expanded ? 'big' : 'med' )
									)
							)
						: ( strpos( kidscare_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $kidscare_columns > 2 ? 'masonry-big' : 'full' )
								: ( $kidscare_columns <= 2 && $kidscare_expanded ? 'masonry-big' : 'masonry' )
							)
			),
			'hover'      => $kidscare_hover,
			'no_links'   => ! empty( $kidscare_template_args['no_links'] ),
		)
	);

	if ( ! in_array( $kidscare_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
		?>
		<div class="post_header entry-header">
			<?php
			do_action( 'kidscare_action_before_post_title' );

			// Post title
			if ( empty( $kidscare_template_args['no_links'] ) ) {
				the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
			} else {
				the_title( '<h4 class="post_title entry-title">', '</h4>' );
			}

			do_action( 'kidscare_action_before_post_meta' );

			// Post meta
			if ( ! empty( $kidscare_components ) && ! in_array( $kidscare_hover, array( 'border', 'pull', 'slide', 'fade' ) ) ) {
				kidscare_show_post_meta(
					apply_filters(
						'kidscare_filter_post_meta_args', array(
							'components' => $kidscare_components,
							'seo'        => false,
						), $kidscare_blog_style[0], $kidscare_columns
					)
				);
			}

			do_action( 'kidscare_action_after_post_meta' );
			?>
		</div><!-- .entry-header -->
		<?php
	}
	?>

	<div class="post_content entry-content">
		<?php
		if ( empty( $kidscare_template_args['hide_excerpt'] ) && kidscare_get_theme_option( 'excerpt_length' ) > 0 ) {
			// Post content area
			kidscare_show_post_content( $kidscare_template_args, '<div class="post_content_inner">', '</div>' );
		}
		
		// Post meta
		if ( in_array( $kidscare_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
			if ( ! empty( $kidscare_components ) ) {
				kidscare_show_post_meta(
					apply_filters(
						'kidscare_filter_post_meta_args', array(
							'components' => $kidscare_components,
						), $kidscare_blog_style[0], $kidscare_columns
					)
				);
			}
		}
		
		// More button
		if ( empty( $kidscare_template_args['no_links'] ) && ! empty( $kidscare_template_args['more_text'] ) && ! in_array( $kidscare_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
			kidscare_show_post_more_link( $kidscare_template_args, '<p>', '</p>' );
		}
		?>
	</div><!-- .entry-content -->

</article></div><?php
// Need opening PHP-tag above, because <div> is a inline-block element (used as column)!
