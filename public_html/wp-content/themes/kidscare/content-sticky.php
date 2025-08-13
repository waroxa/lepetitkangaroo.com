<?php
/**
 * The Sticky template to display the sticky posts
 *
 * Used for index/archive
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

$kidscare_columns     = max( 1, min( 3, count( get_option( 'sticky_posts' ) ) ) );
$kidscare_post_format = get_post_format();
$kidscare_post_format = empty( $kidscare_post_format ) ? 'standard' : str_replace( 'post-format-', '', $kidscare_post_format );
$kidscare_animation   = kidscare_get_theme_option( 'blog_animation' );

?><div class="column-1_<?php echo esc_attr( $kidscare_columns ); ?>"><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_sticky post_format_' . esc_attr( $kidscare_post_format ) ); ?>
	<?php echo ( ! kidscare_is_off( $kidscare_animation ) ? ' data-animation="' . esc_attr( kidscare_get_animation_classes( $kidscare_animation ) ) . '"' : '' ); ?>
	>

	<?php
	if ( is_sticky() && is_home() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}

	// Featured image
	kidscare_show_post_featured(
		array(
			'thumb_size' => kidscare_get_thumb_size( 1 == $kidscare_columns ? 'big' : ( 2 == $kidscare_columns ? 'med' : 'avatar' ) ),
		)
	);

	if ( ! in_array( $kidscare_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
		?>
		<div class="post_header entry-header">
			<?php
			// Post title
			the_title( sprintf( '<h6 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h6>' );
			// Post meta
			kidscare_show_post_meta( apply_filters( 'kidscare_filter_post_meta_args', array(), 'sticky', $kidscare_columns ) );
			?>
		</div><!-- .entry-header -->
		<?php
	}
	?>
</article></div><?php

// div.column-1_X is a inline-block and new lines and spaces after it are forbidden
