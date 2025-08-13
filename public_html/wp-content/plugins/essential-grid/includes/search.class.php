<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 * @since      2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Essential_Grid_Search {

	/**
	 * @var string
	 */
	private $plugin_slug;
	/**
	 * @var array
	 */
	private $settings;
	/**
	 * @var Essential_Grid_Base
	 */
	private $base;

	public function __construct( $force = false ) {
		$base       = new Essential_Grid_Base();
		$this->base = $base;

		$plugin            = Essential_Grid::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		$settings = get_option( 'esg-search-settings', [ 'settings' => [], 'global' => [], 'shortcode' => [] ] );

		if ( $force ) {
			//change settings to force inclusion by setting search-enable to on
			$settings['settings']['search-enable'] = 'on';
		}

		$settings       = Essential_Grid_Base::stripslashes_deep( $settings );
		$this->settings = $settings;

		if ( ! is_admin() ) { //only for frondend
			if ( $base->getVar( $settings, [ 'settings', 'search-enable' ], 'off' ) == 'on' ) {
				add_action( 'wp_footer', [ $this, 'enqueue_styles' ] );
				add_action( 'wp_footer', [ $this, 'enqueue_scripts' ] );
			}
		}

		do_action( 'essgrid_search__construct', $this );
	}

	/**
	 * add search shortcode functionality
	 * @since: 2.0
	 */
	public static function register_shortcode_search( $args, $mid_content = null ) {
		extract( shortcode_atts( [ 'handle' => '' ], $args, 'ess_grid_search' ) );
		if ( trim( $handle ) === '' ) {
			return false;
		}

		$settings = get_option( 'esg-search-settings', [ 'settings' => [], 'global' => [], 'shortcode' => [] ] );
		$settings = Essential_Grid_Base::stripslashes_deep( $settings );
		if ( ! isset( $settings['shortcode']['sc-handle'] ) ) {
			return false;
		}

		$use_key = false;
		foreach ( $settings['shortcode']['sc-handle'] as $key => $sc_handle ) {
			if ( $sc_handle === $handle ) {
				$use_key = $key;
			}
		}
		if ( $key === false ) {
			return false;
		}

		//we have found it, now proceed if correct handle and a text was set it
		$class = 'eg-' . sanitize_html_class( $settings['shortcode']['sc-handle'][ $use_key ] );
		if ( $class === '' ) {
			return false;
		}

		$text = trim( $settings['shortcode']['sc-html'][ $use_key ] );
		if ( $text === '' ) {
			return false;
		}

		//modify text so that we add
		// 1. the class to existing if there is a tag element in it (add only to first wrap).
		// 2. the class as new if there is a tag element inside.
		// 3. wrap text around it if there is no tag element

		//true will enqueue scripts to page
		new Essential_Grid_Search( true );

		preg_match_all( '/<(.*?)>/', $text, $matches );
		if ( ! empty( $matches[0] ) ) {
			//check if first tag has class, if not add it
			$string = $matches[0][0];
			if ( strpos( $string, 'class="' ) !== false ) {
				$new_text = str_replace( 'class="', 'class="' . esc_attr( $class ) . ' ', $string );
			} elseif ( strpos( $string, "class='" ) !== false ) {
				$new_text = str_replace( "class='", "class='" . esc_attr( $class ) . ' ', $string );
			} else {
				$use_string = $matches[1][0];
				$new_text   = '<' . $use_string . ' class="' . esc_attr( $class ) . '">';
			}
			$text = str_replace( $string, $new_text, $text );
		} else {
			$text = '<a href="javascript:void(0);" class="' . esc_attr( $class ) . '">' . esc_html( $text ) . '</a>';
		}

		return apply_filters( 'essgrid_register_shortcode_search', $text, $args );
	}

	/**
	 * enqueue styles on startup
	 * @since: 2.0
	 */
	public function enqueue_styles() {
		add_action( 'essgrid_add_search_style', (object) $this->settings );
	}

	/**
	 * enqueue scripts on startup
	 * @since: 2.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'tp-tools' );
		wp_enqueue_script( 'esg-essential-grid-script' );

		$globals   = $this->base->getVar( $this->settings, 'global', [] );
		$shortcode = $this->base->getVar( $this->settings, 'shortcode', [] );

		$search_classes = $this->base->getVar( $globals, 'search-class', [] );
		$search_styles  = $this->base->getVar( $globals, 'search-style', [] );
		$search_skins   = $this->base->getVar( $globals, 'search-grid-id', [] );

		$sc_classes = $this->base->getVar( $shortcode, 'sc-handle', [] );
		$sc_styles  = $this->base->getVar( $shortcode, 'sc-style', [] );
		$sc_skins   = $this->base->getVar( $shortcode, 'sc-grid-id', [] );

		//add shortcodes also here
		if ( ! empty( $sc_classes ) ) {
			foreach ( $sc_classes as $key => $handle ) {
				$sc_classes[ $key ] = '.eg-' . sanitize_html_class( $handle );
				if ( $sc_classes[ $key ] === '.eg-' ) {
					unset( $sc_classes[ $key ] );
					unset( $sc_styles[ $key ] );
					unset( $sc_skins[ $key ] );
				} else {
					$search_classes[] = $sc_classes[ $key ];
					$search_styles[]  = $sc_styles[ $key ];
					$search_skins[]   = $sc_skins[ $key ];
				}
			}
		}

		$search_class = trim( implode( ', ', $search_classes ), ',' );
		if ( trim( $search_class ) === '' ) {
			return true;
		}

		?>
		<script type="text/javascript">
			jQuery('body').on('click', '<?php echo esc_attr( $search_class ); ?>', function (e) {

				if (jQuery('#esg_search_bg').length > 0) return true; //only allow one instance at a time

				var identifier = 0;
				var overlay_skin = <?php echo wp_json_encode( $search_styles ); ?>;
				var skins = <?php echo wp_json_encode( $search_skins ); ?>;

				<?php
				foreach ( $search_classes as $k => $ident ) {
					if ( $k > 0 ) {
						echo 'else ';
					}
					echo 'if(jQuery(this).is(\'' . esc_attr( $ident ) . '\')){' . "\n";
					echo '				identifier = ' . esc_attr( $k ) . ';' . "\n";
					echo '			}';
				}
				?>

				var counter = {val: jQuery(document).scrollTop()};

				_tpt.gsap.to(counter, 0.5, {
					val: 0, ease: _tpt.Power4.easeOut,
					onUpdate: function () {
						forcescrolled = true;
						_tpt.gsap.set(jQuery(window), {scrollTop: counter.val});
					},
					onComplete: function () {
						forcescrolled = false;
					}
				});

				var forcescrolled = true;

				jQuery('body').append('<div id="esg_search_bg" class="' + overlay_skin[identifier] + '"></div><div id="esg_search_wrapper"></div>');
				var sw = jQuery('#esg_search_wrapper'),
					sb = jQuery('#esg_search_bg'),
					onfocus = "if(this.value == '<?php esc_attr_e( 'Enter your search', 'essential-grid' ); ?>') { this.value = ''; }",
					onblur = "if(this.value == '') { this.value = '<?php esc_attr_e( 'Enter your search', 'essential-grid' ); ?>'; }",
					ivalue = "<?php esc_attr_e( 'Enter your search', 'essential-grid' ); ?>";

				sw.append('<div class="esg_searchcontainer ' + overlay_skin[identifier] + '"></div>');
				var cont = sw.find('.esg_searchcontainer');
				cont.append('<div id="esg_big_search_wrapper" class="' + overlay_skin[identifier] + '"><div id="esg_big_search_fake_txt"><?php esc_html_e( 'Enter your search', 'essential-grid' ); ?></div><input class="bigsearchfield" name="bigsearchfield" type="text"></input></div><div class="esg_big_search_close"><i class="eg-icon-cancel"></i></div>');
				cont.append('<div class="esg_searchresult_title"></div>');

				var bsft = jQuery('#esg_big_search_fake_txt'),
					myst = new _tpt.SplitText(bsft, {type: "words,chars"}),
					mytl = new _tpt.TimelineLite();
				mytl.pause(0);

				mytl.add(_tpt.gsap.to(bsft, 0.4, {x: 30, ease: _tpt.Power2.easeOut}));
				jQuery.each(myst.chars, function (index, chars) {
					mytl.add(_tpt.gsap.to(chars, 0.2, {
						autoAlpha: 0,
						scale: 0.8,
						ease: _tpt.Power2.easeOut
					}), (Math.random() * 0.2));
				});

				var inp = cont.find('input');
				setTimeout(function () {
					inp.trigger('focus');
				}, 450);

				inp.on('keyup', function (e) {
					if (inp.val().length == 0)
						mytl.reverse();
					else
						mytl.play();
				});
				inp.on('keypress', function (e) {
					if (inp.val().length == 0)
						mytl.reverse();
					else
						mytl.play();

					if (e.keyCode == 13) {
						cont.find('.esg_searchresult').remove();

						var objData = {
							action: 'Essential_Grid_Front_request_ajax',
							client_action: 'get_search_results',
							token: '<?php echo esc_js( wp_create_nonce( 'Essential_Grid_Front' ) ); ?>',
							data: {search: inp.val(), skin: skins[identifier]}
						};

						jQuery.ajax({
							type: 'post',
							url: "<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>",
							dataType: 'json',
							data: objData,
							success: function (result, status, arg3) {
								if (typeof (result.data) !== 'undefined') {
									jQuery('#esg_search_wrapper .esg_searchcontainer').append("<div class='esg_searchresult'>" + result.data + "</div>");
								}
							},
							error: function (arg1, arg2, arg3) {
								jQuery('#esg_search_wrapper .esg_searchcontainer').html("<div class='esg_searchresult'><p class='futyi'>FAILURE: " + arg2 + "   " + arg3 + "</p></div>");
							}
						});

					}
				});

				_tpt.gsap.fromTo(sw, 0.4, {x: 0, y: 0, scale: 0.7, autoAlpha: 0, transformOrigin: "50% 0%"}, {
					scale: 1,
					autoAlpha: 1,
					x: 0,
					ease: _tpt.Power3.easeOut,
					delay: 0.1
				});
				_tpt.gsap.fromTo(sb, 0.4, {x: 0, y: 0, scale: 0.9, autoAlpha: 0, transformOrigin: "50% 0%"}, {
					scale: 1,
					autoAlpha: 1,
					x: 0,
					ease: _tpt.Power3.easeOut
				});
				var bgs = jQuery('.esg_big_search_close');

				bgs.on('mouseenter', function () {
					_tpt.gsap.to(bgs, 0.4, {rotation: 180});
				});
				bgs.on('mouseleave', function () {
					_tpt.gsap.to(bgs, 0.4, {rotation: 0});
				});
				bgs.on('click', function () {
					_tpt.gsap.to(sw, 0.4, {
						x: 0, y: 0, scale: 0.8, autoAlpha: 0, ease: _tpt.Power3.easeOut, onComplete: function () {
							sw.remove();
							//kill everything from essential !!!!
						}
					});
					_tpt.gsap.to(sb, 0.4, {
						x: 0,
						y: 0,
						scale: 0.9,
						delay: 0.1,
						autoAlpha: 0,
						ease: _tpt.Power3.easeOut,
						onComplete: function () {
							sb.remove();
						}
					});
				});
			});
		</script>
		<?php
		add_action( 'essgrid_add_search_script', (object) $this->settings );
	}

	/**
	 * return search result HTML
	 * @since: 2.0
	 */
	public function output_search_result( $search, $skin_id = 0 ) {
		$skin_id = intval( $skin_id );

		if ( $search == '' || $skin_id === 0 ) {
			return esc_attr__( 'Not found', 'essential-grid' );
		}

		$post_types       = get_post_types( [ 'public' => true, 'exclude_from_search' => false ], 'objects' );
		$searchable_types = [];
		if ( $post_types ) {
			foreach ( $post_types as $type ) {
				$searchable_types[] = $type->name;
			}
		}
		$args = [
			's'         => $search,
			'showposts' => - 1,
			'post_type' => $searchable_types
		];
		$args = apply_filters( 'essgrid_modify_search_query', $args );

		$wp_query = new WP_Query();
		$wp_query->parse_query( $args );

		$tp_allsearch = $wp_query->get_posts();
		if ( empty( $tp_allsearch ) ) {
			return esc_attr__( 'Not found', 'essential-grid' );
		}

		$posts = [];
		foreach ( $tp_allsearch as $search ) {
			$posts[] = $search->ID;
		}

		$alias = Essential_Grid_Db::get_entity( 'grids' )->get_alias_by_id( $skin_id );
		if ( $alias == '' ) {
			return esc_attr__( 'Not found', 'essential-grid' );
		}

		$content = do_shortcode( apply_filters( 'essgrid_output_search_result', '[ess_grid alias="' . $alias . '" posts="' . implode( ',', $posts ) . '"][/ess_grid]' ) );
		wp_reset_postdata();

		return $content;
	}

	/**
	 * return grid items ID's that match search string
	 * 
	 * @param string $search
	 * @param int $grid_id
	 * @return array
	 */
	public static function search_grid_items_ids( $search, $grid_id = 0 ) {
		$ids     = [];
		$grid_id = intval( $grid_id );

		if ( empty( $search ) || empty( $grid_id ) ) {
			return $ids;
		}

		$grid = new Essential_Grid();
		if ( $grid->init_by_id( $grid_id ) === false ) {
			return $ids;
		}

		if ( $grid->is_custom_grid() ) {
			$custom_entries = $grid->get_layer_values();
			if ( empty( $custom_entries ) ) {
				return $ids;
			}
			
			foreach ( $custom_entries as $key => $entry ) {
				$text_found = self::search_in_array( $entry, $search, 'custom-' );
				if ( $text_found === false && isset( $entry['custom-image'] ) ) {
					//search in image information
					$title = get_the_title( esc_attr( $entry['custom-image'] ) );
					$text_found = stripos( $title, $search ) !== false;
				}

				if ( $text_found ) {
					$ids[] = $key;
				}
			}
		} else {
			$post_category     = $grid->get_postparam_by_handle( 'post_category' );
			$post_types        = $grid->get_postparam_by_handle( 'post_types' );
			$page_ids          = explode( ',', $grid->get_postparam_by_handle( 'selected_pages', '-1' ) );
			$start_sortby      = $grid->get_param_by_handle( 'sorting-order-by-start', 'none' );
			$start_sortby_type = $grid->get_param_by_handle( 'sorting-order-type', 'ASC' );
			$max_entries       = $grid->get_maximum_entries( $grid );
			$cat_tax           = Essential_Grid_Base::getCatAndTaxData( $post_category );
			$additional_query  = $grid->get_postparam_by_handle( 'additional-query' );
			if ( $additional_query !== '' ) {
				$additional_query .= '&s=' . $search;
			} else {
				$additional_query .= 's=' . $search;
			}
			$additional_query = wp_parse_args( $additional_query );

			$posts = Essential_Grid_Base::getPostsByCategory( $grid_id, $cat_tax['cats'], $post_types, $cat_tax['tax'], $page_ids, $start_sortby, $start_sortby_type, $max_entries, $additional_query );
			if ( empty( $posts ) || count( $posts ) === 0 ) {
				return $ids;
			}

			foreach ( $posts as $post ) {
				$ids[] = $post['ID'];
			}
		}

		return $ids;
	}

	/**
	 * return if in array the search string can be found
	 * @since: 2.1.0
	 */
	public static function search_in_array( $array, $search, $ignore ) {
		if ( empty( $search ) || empty( $array ) || !is_array( $array ) ) {
			return false;
		}
		
		foreach ( $array as $key => $val ) {

			if ( is_object( $val ) ) {
				$val = (array) $val;
			}
			
			if ( is_array( $val ) ) {
				$result = self::search_in_array( $val, $search, $ignore );
				if ( $result ) {
					return true;
				}
				continue;
			}
			
			if ( strpos( $key, $ignore ) === 0 ) {
				continue;
			}

			if ( stripos( $val, $search ) !== false ) {
				return true;
			}
		}

		return false;
	}

}
