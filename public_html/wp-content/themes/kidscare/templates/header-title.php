<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

// Page (category, tag, archive, author) title

if ( kidscare_need_page_title() ) {
	kidscare_sc_layouts_showed( 'title', true );
	kidscare_sc_layouts_showed( 'postmeta', false );
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( false && is_single() ) {
							?>
							<div class="sc_layouts_title_meta">
							<?php
								kidscare_show_post_meta(
									apply_filters(
										'kidscare_filter_post_meta_args', array(
											'components' => kidscare_array_get_keys_by_value( kidscare_get_theme_option( 'meta_parts' ) ),
											'counters'   => kidscare_array_get_keys_by_value( kidscare_get_theme_option( 'counters' ) ),
											'seo'        => kidscare_is_on( kidscare_get_theme_option( 'seo_snippets' ) ),
										), 'header', 1
									)
								);
							?>
							</div>
							<?php
						}

						// Blog/Post title
						?>
						<div class="sc_layouts_title_title">
							<?php
							$kidscare_blog_title           = kidscare_get_blog_title();
							$kidscare_blog_title_text      = '';
							$kidscare_blog_title_class     = '';
							$kidscare_blog_title_link      = '';
							$kidscare_blog_title_link_text = '';
							if ( is_array( $kidscare_blog_title ) ) {
								$kidscare_blog_title_text      = $kidscare_blog_title['text'];
								$kidscare_blog_title_class     = ! empty( $kidscare_blog_title['class'] ) ? ' ' . $kidscare_blog_title['class'] : '';
								$kidscare_blog_title_link      = ! empty( $kidscare_blog_title['link'] ) ? $kidscare_blog_title['link'] : '';
								$kidscare_blog_title_link_text = ! empty( $kidscare_blog_title['link_text'] ) ? $kidscare_blog_title['link_text'] : '';
							} else {
								$kidscare_blog_title_text = $kidscare_blog_title;
							}
							?>
							<h1 itemprop="headline" class="sc_layouts_title_caption<?php echo esc_attr( $kidscare_blog_title_class ); ?>">
								<?php
								$kidscare_top_icon = kidscare_get_term_image_small();
								if ( ! empty( $kidscare_top_icon ) ) {
									$kidscare_attr = kidscare_getimagesize( $kidscare_top_icon );
									?>
									<img src="<?php echo esc_url( $kidscare_top_icon ); ?>" alt="<?php esc_attr_e( 'Site icon', 'kidscare' ); ?>"
										<?php
										if ( ! empty( $kidscare_attr[3] ) ) {
											kidscare_show_layout( $kidscare_attr[3] );
										}
										?>
									>
									<?php
								}
								echo wp_kses( $kidscare_blog_title_text, 'kidscare_kses_content' );
								?>
							</h1>
							<?php
							if ( ! empty( $kidscare_blog_title_link ) && ! empty( $kidscare_blog_title_link_text ) ) {
								?>
								<a href="<?php echo esc_url( $kidscare_blog_title_link ); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html( $kidscare_blog_title_link_text ); ?></a>
								<?php
							}

							// Category/Tag description
							if ( is_category() || is_tag() || is_tax() ) {
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
							}

							?>
						</div>
						<?php

						// Breadcrumbs
						?>
						<div class="sc_layouts_title_breadcrumbs">
							<?php
							do_action( 'kidscare_action_breadcrumbs' );
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
