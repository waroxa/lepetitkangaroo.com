<?php
/**
 * The template for homepage posts with "Portfolio" style
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

kidscare_storage_set( 'blog_archive', true );

get_header();

if ( have_posts() ) {

	kidscare_blog_archive_start();

	$kidscare_stickies   = is_home() ? get_option( 'sticky_posts' ) : false;
	$kidscare_sticky_out = kidscare_get_theme_option( 'sticky_style' ) == 'columns'
							&& is_array( $kidscare_stickies ) && count( $kidscare_stickies ) > 0 && get_query_var( 'paged' ) < 1;

	// Show filters
	$kidscare_cat          = kidscare_get_theme_option( 'parent_cat' );
	$kidscare_post_type    = kidscare_get_theme_option( 'post_type' );
	$kidscare_taxonomy     = kidscare_get_post_type_taxonomy( $kidscare_post_type );
	$kidscare_show_filters = kidscare_get_theme_option( 'show_filters' );
	$kidscare_tabs         = array();
	if ( ! kidscare_is_off( $kidscare_show_filters ) ) {
		$kidscare_args           = array(
			'type'         => $kidscare_post_type,
			'child_of'     => $kidscare_cat,
			'orderby'      => 'name',
			'order'        => 'ASC',
			'hide_empty'   => 1,
			'hierarchical' => 0,
			'taxonomy'     => $kidscare_taxonomy,
			'pad_counts'   => false,
		);
		$kidscare_portfolio_list = get_terms( $kidscare_args );
		if ( is_array( $kidscare_portfolio_list ) && count( $kidscare_portfolio_list ) > 0 ) {
			$kidscare_tabs[ $kidscare_cat ] = esc_html__( 'All', 'kidscare' );
			foreach ( $kidscare_portfolio_list as $kidscare_term ) {
				if ( isset( $kidscare_term->term_id ) ) {
					$kidscare_tabs[ $kidscare_term->term_id ] = $kidscare_term->name;
				}
			}
		}
	}
	if ( count( $kidscare_tabs ) > 0 ) {
		$kidscare_portfolio_filters_ajax   = true;
		$kidscare_portfolio_filters_active = $kidscare_cat;
		$kidscare_portfolio_filters_id     = 'portfolio_filters';
		?>
		<div class="portfolio_filters kidscare_tabs kidscare_tabs_ajax">
			<ul class="portfolio_titles kidscare_tabs_titles">
				<?php
				foreach ( $kidscare_tabs as $kidscare_id => $kidscare_title ) {
					?>
					<li><a href="<?php echo esc_url( kidscare_get_hash_link( sprintf( '#%s_%s_content', $kidscare_portfolio_filters_id, $kidscare_id ) ) ); ?>" data-tab="<?php echo esc_attr( $kidscare_id ); ?>"><?php echo esc_html( $kidscare_title ); ?></a></li>
					<?php
				}
				?>
			</ul>
			<?php
			$kidscare_ppp = kidscare_get_theme_option( 'posts_per_page' );
			if ( kidscare_is_inherit( $kidscare_ppp ) ) {
				$kidscare_ppp = '';
			}
			foreach ( $kidscare_tabs as $kidscare_id => $kidscare_title ) {
				$kidscare_portfolio_need_content = $kidscare_id == $kidscare_portfolio_filters_active || ! $kidscare_portfolio_filters_ajax;
				?>
				<div id="<?php echo esc_attr( sprintf( '%s_%s_content', $kidscare_portfolio_filters_id, $kidscare_id ) ); ?>"
					class="portfolio_content kidscare_tabs_content"
					data-blog-template="<?php echo esc_attr( kidscare_storage_get( 'blog_template' ) ); ?>"
					data-blog-style="<?php echo esc_attr( kidscare_get_theme_option( 'blog_style' ) ); ?>"
					data-posts-per-page="<?php echo esc_attr( $kidscare_ppp ); ?>"
					data-post-type="<?php echo esc_attr( $kidscare_post_type ); ?>"
					data-taxonomy="<?php echo esc_attr( $kidscare_taxonomy ); ?>"
					data-cat="<?php echo esc_attr( $kidscare_id ); ?>"
					data-parent-cat="<?php echo esc_attr( $kidscare_cat ); ?>"
					data-need-content="<?php echo ( false === $kidscare_portfolio_need_content ? 'true' : 'false' ); ?>"
				>
					<?php
					if ( $kidscare_portfolio_need_content ) {
						kidscare_show_portfolio_posts(
							array(
								'cat'        => $kidscare_id,
								'parent_cat' => $kidscare_cat,
								'taxonomy'   => $kidscare_taxonomy,
								'post_type'  => $kidscare_post_type,
								'page'       => 1,
								'sticky'     => $kidscare_sticky_out,
							)
						);
					}
					?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	} else {
		kidscare_show_portfolio_posts(
			array(
				'cat'        => $kidscare_cat,
				'parent_cat' => $kidscare_cat,
				'taxonomy'   => $kidscare_taxonomy,
				'post_type'  => $kidscare_post_type,
				'page'       => 1,
				'sticky'     => $kidscare_sticky_out,
			)
		);
	}

	kidscare_blog_archive_end();

} else {

	if ( is_search() ) {
		get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'content', 'none-search' ), 'none-search' );
	} else {
		get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'content', 'none-archive' ), 'none-archive' );
	}
}

get_footer();
