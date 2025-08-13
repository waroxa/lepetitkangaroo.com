<?php
/**
 * The template to display single post
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

get_header();

while ( have_posts() ) {
	the_post();

	// Prepare theme-specific vars:

	// Full post loading
	$full_post_loading        = kidscare_get_value_gp( 'action' ) == 'full_post_loading';

	// Prev post loading
	$prev_post_loading        = kidscare_get_value_gp( 'action' ) == 'prev_post_loading';

	// Position of the related posts
	$kidscare_related_position = kidscare_get_theme_option( 'related_position' );

	// Type of the prev/next posts navigation
	$kidscare_posts_navigation = kidscare_get_theme_option( 'posts_navigation' );
	$kidscare_prev_post        = false;

	if ( 'scroll' == $kidscare_posts_navigation ) {
		$kidscare_prev_post = get_previous_post( true );         // Get post from same category
		if ( ! $kidscare_prev_post ) {
			$kidscare_prev_post = get_previous_post( false );    // Get post from any category
			if ( ! $kidscare_prev_post ) {
				$kidscare_posts_navigation = 'links';
			}
		}
	}

	// Override some theme options to display featured image, title and post meta in the dynamic loaded posts
	if ( $full_post_loading || ( $prev_post_loading && $kidscare_prev_post ) ) {
		kidscare_storage_set_array( 'options_meta', 'post_thumbnail_type', 'default' );
		if ( kidscare_get_theme_option( 'post_header_position' ) != 'below' ) {
			kidscare_storage_set_array( 'options_meta', 'post_header_position', 'above' );
		}
		kidscare_sc_layouts_showed( 'featured', false );
		kidscare_sc_layouts_showed( 'title', false );
		kidscare_sc_layouts_showed( 'postmeta', false );
	}

	// If related posts should be inside the content
	if ( strpos( $kidscare_related_position, 'inside' ) === 0 ) {
		ob_start();
	}

	// Display post's content
	get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'content', get_post_format() ), get_post_format() );

	// If related posts should be inside the content
	if ( strpos( $kidscare_related_position, 'inside' ) === 0 ) {
		$kidscare_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action( 'kidscare_action_related_posts' );
		$kidscare_related_content = ob_get_contents();
		ob_end_clean();

		$kidscare_related_position_inside = max( 0, min( 9, kidscare_get_theme_option( 'related_position_inside' ) ) );
		if ( 0 == $kidscare_related_position_inside ) {
			$kidscare_related_position_inside = mt_rand( 1, 9 );
		}
		
		$kidscare_p_number = 0;
		$kidscare_related_inserted = false;
		for ( $i = 0; $i < strlen( $kidscare_content ) - 3; $i++ ) {
			if ( $kidscare_content[ $i ] == '<' && $kidscare_content[ $i + 1 ] == 'p' && in_array( $kidscare_content[ $i + 2 ], array( '>', ' ' ) ) ) {
				$kidscare_p_number++;
				if ( $kidscare_related_position_inside == $kidscare_p_number ) {
					$kidscare_related_inserted = true;
					$kidscare_content = ( $i > 0 ? substr( $kidscare_content, 0, $i ) : '' )
										. $kidscare_related_content
										. substr( $kidscare_content, $i );
				}
			}
		}
		if ( ! $kidscare_related_inserted ) {
			$kidscare_content .= $kidscare_related_content;
		}

		kidscare_show_layout( $kidscare_content );
	}

	// Author bio
	if ( kidscare_get_theme_option( 'show_author_info' ) == 1
		&& ! is_attachment()
		&& get_the_author_meta( 'description' )
		&& ( 'scroll' != $kidscare_posts_navigation || kidscare_get_theme_option( 'posts_navigation_scroll_hide_author' ) == 0 )
		&& ( ! $full_post_loading || kidscare_get_theme_option( 'open_full_post_hide_author' ) == 0 )
	) {
		do_action( 'kidscare_action_before_post_author' );
		get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/author-bio' ) );
		do_action( 'kidscare_action_after_post_author' );
	}

	// Previous/next post navigation.
	if ( 'links' == $kidscare_posts_navigation && ! $full_post_loading ) {
		do_action( 'kidscare_action_before_post_navigation' );
		?>
		<div class="nav-links-single<?php
			if ( ! kidscare_is_off( kidscare_get_theme_option( 'posts_navigation_fixed' ) ) ) {
				echo ' nav-links-fixed fixed';
			}
		?>">
			<?php
			the_post_navigation(
				array(
					'next_text' => '<span class="nav-arrow"></span>'
						. '<span class="screen-reader-text">' . esc_html__( 'Next Post', 'kidscare' ) . '</span> '
						. '<h6 class="post-title">%title</h6>'
						. '<span class="post_date">%date</span>',
					'prev_text' => '<span class="nav-arrow"></span>'
						. '<span class="screen-reader-text">' . esc_html__( 'Previous Post', 'kidscare' ) . '</span> '
						. '<h6 class="post-title">%title</h6>'
						. '<span class="post_date">%date</span>',
				)
			);
			?>
		</div>
		<?php
		do_action( 'kidscare_action_after_post_navigation' );
	}

	// Related posts
	if ( 'below_content' == $kidscare_related_position
		&& ( 'scroll' != $kidscare_posts_navigation || kidscare_get_theme_option( 'posts_navigation_scroll_hide_related' ) == 0 )
		&& ( ! $full_post_loading || kidscare_get_theme_option( 'open_full_post_hide_related' ) == 0 )
	) {
		do_action( 'kidscare_action_related_posts' );
	}

	// If comments are open or we have at least one comment, load up the comment template.
	$kidscare_comments_number = get_comments_number();
	if ( comments_open() || $kidscare_comments_number > 0 ) {
		if ( kidscare_get_value_gp( 'show_comments' ) == 1 || ( ! $full_post_loading && ( 'scroll' != $kidscare_posts_navigation || kidscare_get_theme_option( 'posts_navigation_scroll_hide_comments' ) == 0 || kidscare_check_url( '#comment' ) ) ) ) {
			do_action( 'kidscare_action_before_comments' );
			comments_template();
			do_action( 'kidscare_action_after_comments' );
		} else {
			?>
			<div class="show_comments_single">
				<a href="<?php echo esc_url( add_query_arg( array( 'show_comments' => 1 ), get_comments_link() ) ); ?>" class="theme_button show_comments_button">
					<?php
					if ( $kidscare_comments_number > 0 ) {
						echo esc_html( sprintf( _n( 'Show comment', 'Show comments ( %d )', $kidscare_comments_number, 'kidscare' ), $kidscare_comments_number ) );
					} else {
						esc_html_e( 'Leave a comment', 'kidscare' );
					}
					?>
				</a>
			</div>
			<?php
		}
	}

	if ( 'scroll' == $kidscare_posts_navigation && ! $full_post_loading ) {
		?>
		<div class="nav-links-single-scroll"
			data-post-id="<?php echo esc_attr( get_the_ID( $kidscare_prev_post ) ); ?>"
			data-post-link="<?php echo esc_attr( get_permalink( $kidscare_prev_post ) ); ?>"
			data-post-title="<?php the_title_attribute( array( 'post' => $kidscare_prev_post ) ); ?>">
		</div>
		<?php
	}
}

get_footer();
