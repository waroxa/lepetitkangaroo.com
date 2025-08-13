<?php
/**
 * The template for homepage posts with custom style
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.50
 */

kidscare_storage_set( 'blog_archive', true );

get_header();

if ( have_posts() ) {

	$kidscare_blog_style = kidscare_get_theme_option( 'blog_style' );
	$kidscare_parts      = explode( '_', $kidscare_blog_style );
	$kidscare_columns    = ! empty( $kidscare_parts[1] ) ? max( 1, min( 6, (int) $kidscare_parts[1] ) ) : 1;
	$kidscare_blog_id    = kidscare_get_custom_blog_id( $kidscare_blog_style );
	$kidscare_blog_meta  = kidscare_get_custom_layout_meta( $kidscare_blog_id );
	if ( ! empty( $kidscare_blog_meta['margin'] ) ) {
		kidscare_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( kidscare_prepare_css_value( $kidscare_blog_meta['margin'] ) ) ) );
	}
	$kidscare_custom_style = ! empty( $kidscare_blog_meta['scripts_required'] ) ? $kidscare_blog_meta['scripts_required'] : 'none';

	kidscare_blog_archive_start();

	$kidscare_classes    = 'posts_container blog_custom_wrap' 
							. ( ! kidscare_is_off( $kidscare_custom_style )
								? sprintf( ' %s_wrap', $kidscare_custom_style )
								: ( $kidscare_columns > 1 
									? ' columns_wrap columns_padding_bottom' 
									: ''
									)
								);
	$kidscare_stickies   = is_home() ? get_option( 'sticky_posts' ) : false;
	$kidscare_sticky_out = kidscare_get_theme_option( 'sticky_style' ) == 'columns'
							&& is_array( $kidscare_stickies ) && count( $kidscare_stickies ) > 0 && get_query_var( 'paged' ) < 1;
	if ( $kidscare_sticky_out ) {
		?>
		<div class="sticky_wrap columns_wrap">
		<?php
	}
	if ( ! $kidscare_sticky_out ) {
		if ( kidscare_get_theme_option( 'first_post_large' ) && ! is_paged() && ! in_array( kidscare_get_theme_option( 'body_style' ), array( 'fullwide', 'fullscreen' ) ) ) {
			the_post();
			get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'content', 'excerpt' ), 'excerpt' );
		}
		?>
		<div class="<?php echo esc_attr( $kidscare_classes ); ?>">
		<?php
	}
	while ( have_posts() ) {
		the_post();
		if ( $kidscare_sticky_out && ! is_sticky() ) {
			$kidscare_sticky_out = false;
			?>
			</div><div class="<?php echo esc_attr( $kidscare_classes ); ?>">
			<?php
		}
		$kidscare_part = $kidscare_sticky_out && is_sticky() ? 'sticky' : 'custom';
		get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'content', $kidscare_part ), $kidscare_part );
	}
	?>
	</div>
	<?php

	kidscare_show_pagination();

	kidscare_blog_archive_end();

} else {

	if ( is_search() ) {
		get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'content', 'none-search' ), 'none-search' );
	} else {
		get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'content', 'none-archive' ), 'none-archive' );
	}
}

get_footer();
