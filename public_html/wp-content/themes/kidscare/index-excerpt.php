<?php
/**
 * The template for homepage posts with "Excerpt" style
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

kidscare_storage_set( 'blog_archive', true );

get_header();

if ( have_posts() ) {

	kidscare_blog_archive_start();

	?><div class="posts_container">
		<?php

		$kidscare_stickies   = is_home() ? get_option( 'sticky_posts' ) : false;
		$kidscare_sticky_out = kidscare_get_theme_option( 'sticky_style' ) == 'columns'
								&& is_array( $kidscare_stickies ) && count( $kidscare_stickies ) > 0 && get_query_var( 'paged' ) < 1;
		if ( $kidscare_sticky_out ) {
			?>
			<div class="sticky_wrap columns_wrap">
			<?php
		}
		while ( have_posts() ) {
			the_post();
			if ( $kidscare_sticky_out && ! is_sticky() ) {
				$kidscare_sticky_out = false;
				?>
				</div>
				<?php
			}
			$kidscare_part = $kidscare_sticky_out && is_sticky() ? 'sticky' : 'excerpt';
			get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'content', $kidscare_part ), $kidscare_part );
		}
		if ( $kidscare_sticky_out ) {
			$kidscare_sticky_out = false;
			?>
			</div>
			<?php
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
