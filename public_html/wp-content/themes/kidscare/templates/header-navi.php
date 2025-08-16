<?php
/**
 * The template to display the main menu
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */
?>
<div class="top_panel_navi sc_layouts_row sc_layouts_row_type_compact sc_layouts_row_delimiter
	<?php
	if ( kidscare_is_on( kidscare_get_theme_option( 'header_mobile_enabled' ) ) ) {
		?>
		sc_layouts_hide_on_mobile
		<?php
	}
	?>
">
	<div class="content_wrap">
		<div class="columns_wrap columns_fluid">
			<div class="sc_layouts_column sc_layouts_column_align_left sc_layouts_column_icons_position_left sc_layouts_column_fluid column-1_4">
				<div class="sc_layouts_item">
					<?php
					// Logo
					get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-logo' ) );
					?>
				</div>
			</div><div class="sc_layouts_column sc_layouts_column_align_right sc_layouts_column_icons_position_left sc_layouts_column_fluid column-3_4">
				<div class="sc_layouts_item">
<?php
// Main menu
?>
<nav class="main-nav" itemscope itemtype="//schema.org/SiteNavigationElement">
<?php
wp_nav_menu(
array(
'theme_location' => 'menu_main',
'container'      => false,
'walker'         => class_exists( 'Kidscare_Main_Nav_Walker' ) ? new Kidscare_Main_Nav_Walker() : '',
)
);
?>
</nav>
                                </div>
                                <div class="sc_layouts_item mobile-menu-button">
                                        <button class="mobile-menu-toggle" aria-label="<?php esc_attr_e( 'Menu', 'kidscare' ); ?>">
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                        </button>
                                </div>
                                <?php
                                if ( kidscare_exists_trx_addons() ) {
                                        ?>
                                        <div class="sc_layouts_item">
                                                <?php
                                                // Display search field
                                                do_action(
                                                        'kidscare_action_search',
                                                        array(
								'style' => 'fullscreen',
								'class' => 'header_search',
								'ajax'  => false
							)
						);
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div><!-- /.columns_wrap -->
	</div><!-- /.content_wrap -->
</div><!-- /.top_panel_navi -->
