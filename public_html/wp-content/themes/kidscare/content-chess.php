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
	$kidscare_columns    = empty( $kidscare_template_args['columns'] ) ? 1 : max( 1, min( 3, $kidscare_template_args['columns'] ) );
	$kidscare_blog_style = array( $kidscare_template_args['type'], $kidscare_columns );
} else {
	$kidscare_blog_style = explode( '_', kidscare_get_theme_option( 'blog_style' ) );
	$kidscare_columns    = empty( $kidscare_blog_style[1] ) ? 1 : max( 1, min( 3, $kidscare_blog_style[1] ) );
}
$kidscare_expanded    = ! kidscare_sidebar_present() && kidscare_is_on( kidscare_get_theme_option( 'expand_content' ) );
$kidscare_post_format = get_post_format();
$kidscare_post_format = empty( $kidscare_post_format ) ? 'standard' : str_replace( 'post-format-', '', $kidscare_post_format );
$kidscare_animation   = kidscare_get_theme_option( 'blog_animation' );

?><article id="post-<?php the_ID(); ?>"	data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item'
		. ' post_layout_chess'
		. ' post_layout_chess_' . esc_attr( $kidscare_columns )
		. ' post_format_' . esc_attr( $kidscare_post_format )
		. ( ! empty( $kidscare_template_args['slider'] ) ? ' slider-slide swiper-slide' : '' )
	);
	echo ( ! kidscare_is_off( $kidscare_animation ) && empty( $kidscare_template_args['slider'] ) ? ' data-animation="' . esc_attr( kidscare_get_animation_classes( $kidscare_animation ) ) . '"' : '' );
	?>
>

	<?php
	// Add anchor
	if ( 1 == $kidscare_columns && ! is_array( $kidscare_template_args ) && shortcode_exists( 'trx_sc_anchor' ) ) {
		echo do_shortcode( '[trx_sc_anchor id="post_' . esc_attr( get_the_ID() ) . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '" icon="' . esc_attr( kidscare_get_post_icon() ) . '"]' );
	}

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
			'class'         => 1 == $kidscare_columns && ! is_array( $kidscare_template_args ) ? 'kidscare-full-height' : '',
			'hover'         => $kidscare_hover,
			'no_links'      => ! empty( $kidscare_template_args['no_links'] ),
			'show_no_image' => true,
			'thumb_ratio'   => '1:1',
			'thumb_bg'      => true,
			'thumb_size'    => kidscare_get_thumb_size(
				strpos( kidscare_get_theme_option( 'body_style' ), 'full' ) !== false
										? ( 1 < $kidscare_columns ? 'huge' : 'original' )
										: ( 2 < $kidscare_columns ? 'big' : 'huge' )
			),
		)
	);

	?>
	<div class="post_inner"><div class="post_inner_content"><div class="post_header entry-header">
		<?php
			do_action( 'kidscare_action_before_post_title' );

			// Post title
			if ( empty( $kidscare_template_args['no_links'] ) ) {
				the_title( sprintf( '<h3 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
			} else {
				the_title( '<h3 class="post_title entry-title">', '</h3>' );
			}

			do_action( 'kidscare_action_before_post_meta' );

			// Post meta
			$kidscare_components = kidscare_array_get_keys_by_value( kidscare_get_theme_option( 'meta_parts' ) );
			$kidscare_post_meta  = empty( $kidscare_components ) || in_array( $kidscare_hover, array( 'border', 'pull', 'slide', 'fade' ) )
										? ''
										: kidscare_show_post_meta(
											apply_filters(
												'kidscare_filter_post_meta_args', array(
													'components' => $kidscare_components,
													'seo'  => false,
													'echo' => false,
												), $kidscare_blog_style[0], $kidscare_columns
											)
										);
			kidscare_show_layout( $kidscare_post_meta );
			?>
		</div><!-- .entry-header -->

		<div class="post_content entry-content">
			<?php
			// Post content area
			if ( empty( $kidscare_template_args['hide_excerpt'] ) && kidscare_get_theme_option( 'excerpt_length' ) > 0 ) {
				kidscare_show_post_content( $kidscare_template_args, '<div class="post_content_inner">', '</div>' );
			}
			// Post meta
			if ( in_array( $kidscare_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
				kidscare_show_layout( $kidscare_post_meta );
			}
			// More button
			if ( empty( $kidscare_template_args['no_links'] ) && ! in_array( $kidscare_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
				kidscare_show_post_more_link( $kidscare_template_args, '<p>', '</p>' );
			}
			?>
		</div><!-- .entry-content -->

	</div></div><!-- .post_inner -->

</article><?php
// Need opening PHP-tag above, because <article> is a inline-block element (used as column)!
