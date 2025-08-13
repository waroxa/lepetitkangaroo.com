<?php
/**
 * The template for homepage posts with "Classic" style
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

kidscare_storage_set( 'blog_archive', true );

get_header();

if ( have_posts() ) {

	kidscare_blog_archive_start();

	$kidscare_classes    = 'posts_container '
						. ( substr( kidscare_get_theme_option( 'blog_style' ), 0, 7 ) == 'classic'
							? 'columns_wrap columns_padding_bottom'
							: 'masonry_wrap'
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
		$kidscare_part = $kidscare_sticky_out && is_sticky() ? 'sticky' : 'classic';
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
