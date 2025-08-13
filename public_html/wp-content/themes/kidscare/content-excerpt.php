<?php
/**
 * The default template to display the content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

$kidscare_template_args = get_query_var( 'kidscare_template_args' );
if ( is_array( $kidscare_template_args ) ) {
	$kidscare_columns    = empty( $kidscare_template_args['columns'] ) ? 1 : max( 1, $kidscare_template_args['columns'] );
	$kidscare_blog_style = array( $kidscare_template_args['type'], $kidscare_columns );
	if ( ! empty( $kidscare_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $kidscare_columns > 1 ) {
		?>
		<div class="column-1_<?php echo esc_attr( $kidscare_columns ); ?>">
		<?php
	}
}
$kidscare_expanded    = ! kidscare_sidebar_present() && kidscare_is_on( kidscare_get_theme_option( 'expand_content' ) );
$kidscare_post_format = get_post_format();
$kidscare_post_format = empty( $kidscare_post_format ) ? 'standard' : str_replace( 'post-format-', '', $kidscare_post_format );
$kidscare_animation   = kidscare_get_theme_option( 'blog_animation' );
$kidscare_components = kidscare_array_get_keys_by_value( kidscare_get_theme_option( 'meta_parts' ) );



$date_val = 'date';
$date = '';
$pos = strpos($kidscare_components, $date_val);
if ( $pos !== false ) {
    $date = '<div class="date-style">';
    $date .= '<span class="wrap"><span class="d">'.get_the_time( 'j' ).'</span>';
    $date .= '<span class="m">'.get_the_time( 'M' ).'</span></span></div>';
}
$vowels = array(",date", "date,", "date");
$kidscare_components = str_replace($vowels, "", $kidscare_components);


?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php post_class( 'post_item post_layout_excerpt post_format_' . esc_attr( $kidscare_post_format ) ); ?>
	<?php echo ( ! kidscare_is_off( $kidscare_animation ) && empty( $kidscare_template_args['slider'] ) ? ' data-animation="' . esc_attr( kidscare_get_animation_classes( $kidscare_animation ) ) . '"' : '' ); ?>
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
			'no_links'   => ! empty( $kidscare_template_args['no_links'] ),
			'hover'      => $kidscare_hover,
			'thumb_size' => kidscare_get_thumb_size( strpos( kidscare_get_theme_option( 'body_style' ), 'full' ) !== false ? 'full' : ( $kidscare_expanded ? 'huge' : 'big' ) ),
		    'post_info'  => $date
		)
	);

	// Title and post meta
	if ( get_the_title() != '' ) {
		?>
		<div class="post_header entry-header">
			<?php
			do_action( 'kidscare_action_before_post_title' );

			// Post title
			if ( empty( $kidscare_template_args['no_links'] ) ) {
				the_title( sprintf( '<h2 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
			} else {
				the_title( '<h2 class="post_title entry-title">', '</h2>' );
			}

			do_action( 'kidscare_action_before_post_meta' );

			$vowels = array(
			        ",comments", "comments,", "comments",
			        ",likes", "likes,", "likes",
			        ",views", "views,", "views",
			);
			$kidscare_components2 = '';
            $kidscare_components2 = str_replace($vowels, "", $kidscare_components);
			// Post meta
			if ( ! empty( $kidscare_components2 ) && ! in_array( $kidscare_hover, array( 'border', 'pull', 'slide', 'fade' ) ) ) {
				kidscare_show_post_meta(
					apply_filters(
						'kidscare_filter_post_meta_args', array(
							'components' => $kidscare_components2,
							'seo'        => false,
						), 'excerpt', 1
					)
				);
			}
			?>
		</div><!-- .post_header -->
		<?php
	}

	// Post content
	if ( empty( $kidscare_template_args['hide_excerpt'] ) && kidscare_get_theme_option( 'excerpt_length' ) > 0 ) {
		?>
		<div class="post_content entry-content">
			<?php
			if ( kidscare_get_theme_option( 'blog_content' ) == 'fullpost' ) {
				// Post content area
				?>
				<div class="post_content_inner">
					<?php
					do_action( 'kidscare_action_before_full_post_content' );
					the_content( '' );
					do_action( 'kidscare_action_after_full_post_content' );
					?>
				</div>
				<?php
				// Inner pages
				wp_link_pages(
					array(
						'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'kidscare' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
						'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'kidscare' ) . ' </span>%',
						'separator'   => '<span class="screen-reader-text">, </span>',
					)
				);
			} else {
				// Post content area
				kidscare_show_post_content( $kidscare_template_args, '<div class="post_content_inner">', '</div>' );

                kidscare_show_layout('<div class="bottom-info">');

                // More button
				if ( empty( $kidscare_template_args['no_links'] ) ) {
					kidscare_show_post_more_link( $kidscare_template_args, '', '' );
				}

                $vowels = array(
			        ",categories", "categories,", "categories",
			        ",author", "author,", "author",
			        ",share", "share,", "share",
			        ",edit", "edit,", "edit",
                );
                $kidscare_components3 = '';
                $kidscare_components3 = str_replace($vowels, "", $kidscare_components);

                // Post meta
                if ( ! empty( $kidscare_components3 ) && ! in_array( $kidscare_hover, array( 'border', 'pull', 'slide', 'fade' ) ) ) {
                    kidscare_show_post_meta(
                        apply_filters(
                            'kidscare_filter_post_meta_args', array(
                                'components' => $kidscare_components3,
                                'seo'        => false,
                            ), 'excerpt', 1
                        )
                    );
                }

				kidscare_show_layout('</div>');

			}
			?>
		</div><!-- .entry-content -->
		<?php
	}
	?>
</article>
<?php

if ( is_array( $kidscare_template_args ) ) {
	if ( ! empty( $kidscare_template_args['slider'] ) || $kidscare_columns > 1 ) {
		?>
		</div>
		<?php
	}
}
