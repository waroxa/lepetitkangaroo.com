/* global jQuery, KIDSCARE_STORAGE */

//-------------------------------------------
// Theme Options fields manipulations
//-------------------------------------------
jQuery( document ).ready(
	function() {
		"use strict";

		// Submit form
		jQuery( '.kidscare_options_button_submit' ).on(
			'click', function() {
				jQuery( this ).parents( 'form' ).submit();
			}
		);

		// Toggle checkbox value
		jQuery( '.kidscare_options input[type="checkbox"]' ).on( 'change', function() {
			var fld = jQuery(this).prev();
			fld.val( jQuery(this).get(0).checked ? 1 : 0 );
		} );

		// Toggle inherit button and cover
		jQuery( '#kidscare_options_tabs' ).on(
			'click', '.kidscare_options_inherit_lock,.kidscare_options_inherit_cover', function (e) {
				var parent  = jQuery( this ).parents( '.kidscare_options_item' );
				var inherit = parent.hasClass( 'kidscare_options_inherit_on' );
				if (inherit) {
					parent.removeClass( 'kidscare_options_inherit_on' ).addClass( 'kidscare_options_inherit_off' );
					parent.find( '.kidscare_options_inherit_cover' ).fadeOut().find( 'input[type="hidden"]' ).val( '' ).trigger('change');
				} else {
					parent.removeClass( 'kidscare_options_inherit_off' ).addClass( 'kidscare_options_inherit_on' );
					parent.find( '.kidscare_options_inherit_cover' ).fadeIn().find( 'input[type="hidden"]' ).val( 'inherit' ).trigger('change');

				}
				e.preventDefault();
				return false;
			}
		);

		// Refresh linked field
		jQuery( '#kidscare_options_tabs' ).on(
			'change', '[data-linked] select,[data-linked] input', function (e) {
				var chg_name          = jQuery( this ).parent().data( 'param' );
				var chg_value         = jQuery( this ).val();
				var linked_name       = jQuery( this ).parent().data( 'linked' );
				var linked_data       = jQuery( '#kidscare_options_tabs [data-param="' + linked_name + '"]' );
				var linked_field      = linked_data.find( 'select' );
				var linked_field_type = 'select';
				if (linked_field.length == 0) {
					linked_field      = linked_data.find( 'input' );
					linked_field_type = 'input';
				}
				var linked_lock = linked_data.parent().parent().find( '.kidscare_options_inherit_lock' ).addClass( 'kidscare_options_wait' );
				// Prepare data
				var data = {
					action: 'kidscare_get_linked_data',
					nonce: KIDSCARE_STORAGE['ajax_nonce'],
					chg_name: chg_name,
					chg_value: chg_value
				};
				jQuery.post(
					KIDSCARE_STORAGE['ajax_url'], data, function(response) {
						var rez = {};
						try {
							rez = JSON.parse( response );
						} catch (e) {
							rez = { error: KIDSCARE_STORAGE['msg_ajax_error'] };
							console.log( response );
						}
						if (rez.error === '') {
							if (linked_field_type == 'select') {
								var opt_list = '';
								for (var i in rez.list) {
									opt_list += '<option value="' + i + '">' + rez.list[i] + '</option>';
								}
								linked_field.html( opt_list );
							} else {
								linked_field.val( rez.value );
							}
							linked_lock.removeClass( 'kidscare_options_wait' );
						}
					}
				);
				e.preventDefault();
				return false;
			}
		);

		// Blur the "load fonts" fields - regenerate options lists in the font-family controls
		jQuery( '.kidscare_options [name^="kidscare_options_field_load_fonts"]' ).on( 'focusout', kidscare_options_update_load_fonts );

		// Change theme fonts options if load fonts is changed
		function kidscare_options_update_load_fonts() {
			var opt_list = [], i, tag, sel, opt, name = '', family = '', val = '', new_val = '', sel_idx = 0;
			for (i = 1; i <= kidscare_options_vars['max_load_fonts']; i++) {
				name = jQuery( '[name="kidscare_options_field_load_fonts-' + i + '-name"]' ).val();
				if (name == '') {
					continue;
				}
				family = jQuery( '[name="kidscare_options_field_load_fonts-' + i + '-family"]' ).val();
				opt_list.push( [name, family] );
			}
			for (tag in kidscare_theme_fonts) {
				sel = jQuery( '[name="kidscare_options_field_' + tag + '_font-family"]' );
				if (sel.length == 1) {
					opt     = sel.find( 'option' );
					sel_idx = sel.find( ':selected' ).index();
					// Remove empty options
					if (opt_list.length < opt.length - 1) {
						for (i = opt.length - 1; i > opt_list.length; i--) {
							opt.eq( i ).remove();
						}
					}
					// Add new options
					if (opt_list.length >= opt.length) {
						for (i = opt.length - 1; i <= opt_list.length - 1; i++) {
							val = '&quot;' + opt_list[i][0] + '&quot;' + (opt_list[i][1] != 'inherit' ? ',' + opt_list[i][1] : '');
							sel.append( '<option value="' + val + '">' + opt_list[i][0] + '</option>' );
						}
					}
					// Set new value
					new_val = '';
					for (i = 0; i < opt_list.length; i++) {
						val = '"' + opt_list[i][0] + '"' + (opt_list[i][1] != 'inherit' ? ',' + opt_list[i][1] : '');
						if (sel_idx - 1 == i) {
							new_val = val;
						}
						opt.eq( i + 1 ).val( val ).text( opt_list[i][0] );
					}
					sel.val( sel_idx > 0 && sel_idx <= opt_list.length && new_val ? new_val : 'inherit' );
				}
			}
		}

		// Check for dependencies
		//-----------------------------------------------------------------------------
        function kidscare_options_start_check_dependencies() {
            jQuery( '.kidscare_options .kidscare_options_section' ).each(
                function () {
                    kidscare_options_check_dependencies( jQuery( this ) );
                }
            );
        }

// Check all inner dependencies
        jQuery( document ).ready( kidscare_options_start_check_dependencies );
// Check external dependencies (for example, "Page template" in the page edit mode)
        jQuery( window ).on( 'load', kidscare_options_start_check_dependencies );
// Check dependencies on any field change
		jQuery( '.kidscare_options .kidscare_options_item_field [name^="kidscare_options_field_"]' ).on(
			'change', function () {
				kidscare_options_check_dependencies( jQuery( this ).parents( '.kidscare_options_section' ) );
			}
		);

		// Return value of the field
		function kidscare_options_get_field_value(fld, num) {
			var ctrl = fld.parents( '.kidscare_options_item_field' );
			var val  = fld.attr( 'type' ) == 'checkbox' || fld.attr( 'type' ) == 'radio'
					? (ctrl.find( '[name^="kidscare_options_field_"]:checked' ).length > 0
						? (num === true
							? ctrl.find( '[name^="kidscare_options_field_"]:checked' ).parent().index() + 1
							: (ctrl.find( '[name^="kidscare_options_field_"]:checked' ).val() !== ''
								&& '' + ctrl.find( '[name^="kidscare_options_field_"]:checked' ).val() != '0'
									? ctrl.find( '[name^="kidscare_options_field_"]:checked' ).val()
									: 1
								)
							)
						: 0
						)
					: (num === true ? fld.find( ':selected' ).index() + 1 : fld.val());
			if (val === undefined || val === null) {
				val = '';
			}
			return val;
		}

		// Check for dependencies
        function kidscare_options_check_dependencies(cont) {
            cont.find( '.kidscare_options_item_field' ).each(
                function() {
                    var ctrl = jQuery( this ), id = ctrl.data( 'param' );
                    if (id === undefined) {
                        return;
                    }
                    var depend = false;
                    for (var fld in kidscare_dependencies) {
                        if (fld == id) {
                            depend = kidscare_dependencies[id];
                            break;
                        }
                    }
                    if (depend) {
                        var dep_cnt    = 0, dep_all = 0;
                        var dep_cmp    = typeof depend.compare != 'undefined' ? depend.compare.toLowerCase() : 'and';
                        var dep_strict = typeof depend.strict != 'undefined';
                        var fld        = null;
						var i;
						var val        = undefined;
						var name       = '', subname = '';
						var parts      = '', parts2 = '';
                        for (var i in depend) {
                            if (i == 'compare' || i == 'strict') {
                                continue;
                            }
                            dep_all++;
                            name    = i;
                            subname = '';
                            if (name.indexOf( '[' ) > 0) {
                                parts   = name.split( '[' );
                                name    = parts[0];
                                subname = parts[1].replace( ']', '' );
                            }
                            if (name.charAt( 0 ) == '#' || name.charAt( 0 ) == '.') {
                                fld = jQuery( name );
                                if (fld.length > 0 && ! fld.hasClass( 'kidscare_inited' )) {
                                    fld.addClass( 'kidscare_inited' ).on(
                                        'change', function () {
                                            jQuery( '.kidscare_options .kidscare_options_section' ).each(
                                                function () {
                                                    kidscare_options_check_dependencies( jQuery( this ) );
                                                }
                                            );
                                        }
                                    );
								} else if ( name == '#page_template' || name == '.editor-page-attributes__template select' ) {
									if ( typeof wp == 'object' && typeof wp.data == 'object' ) {
										if ( typeof KIDSCARE_STORAGE['page_template'] == 'undefined' ) {

											var tpl = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'template' );
											if ( tpl !== undefined ) {
												KIDSCARE_STORAGE['page_template'] = tpl;
											}
										}
										val = typeof KIDSCARE_STORAGE['page_template'] != 'undefined' ? KIDSCARE_STORAGE['page_template'] : '';
										var $body = jQuery( 'body' );
										if ( ! $body.hasClass( 'kidscare_page_template_inited' ) ) {
											$body.addClass( 'kidscare_page_template_inited' );
											// Call a check_dependencies() on a page template changed
											wp.data.subscribe( function() {
												var tpl = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'template' );
												if ( tpl !== undefined && ( typeof KIDSCARE_STORAGE['page_template'] == 'undefined' || tpl != KIDSCARE_STORAGE['page_template'] ) ) {
													KIDSCARE_STORAGE['page_template'] = tpl;
													jQuery('.kidscare_options .kidscare_options_section').each( function () {
														kidscare_options_check_dependencies(jQuery(this));
													} );
												}
											} );
										}
									}
								}
							} else {
                                fld = cont.find( '[name="kidscare_options_field_' + name + '"]' );
                            }
							if ( val !== undefined || ( fld && fld.length > 0 ) ) {
								if ( val === undefined ) {
									val = kidscare_options_get_field_value( fld );
								}
								if (subname !== '') {
                                    parts = val.split( '|' );
                                    for (var p = 0; p < parts.length; p++) {
                                        parts2 = parts[p].split( '=' );
                                        if (parts2[0] == subname) {
                                            val = parts2[1];
                                        }
                                    }
                                }
                                for (var j in depend[i]) {
                                    if (
                                        (depend[i][j] == 'not_empty' && val !== '')   // Main field value is not empty - show current field
                                        || (depend[i][j] == 'is_empty' && val === '') // Main field value is empty - show current field
                                        || (val !== '' && ( ! isNaN( depend[i][j] )   // Main field value equal to specified value - show current field
                                                    ? val == depend[i][j]
                                                    : (dep_strict
                                                            ? val == depend[i][j]
                                                            : ('' + val).indexOf( depend[i][j] ) == 0
                                                    )
                                            )
                                        )
                                        || (val !== '' && ("" + depend[i][j]).charAt( 0 ) == '^' && ('' + val).indexOf( depend[i][j].substr( 1 ) ) == -1)
                                    // Main field value not equal to specified value - show current field
                                    ) {
                                        dep_cnt++;
                                        break;
                                    }
                                }
                            } else {
                                dep_all--;
                            }
                            if (dep_cnt > 0 && dep_cmp == 'or') {
                                break;
                            }
                        }
                        if (((dep_cnt > 0 || dep_all == 0) && dep_cmp == 'or') || (dep_cnt == dep_all && dep_cmp == 'and')) {
                            ctrl.parents( '.kidscare_options_item' ).show().removeClass( 'kidscare_options_no_use' );
                        } else {
                            ctrl.parents( '.kidscare_options_item' ).hide().addClass( 'kidscare_options_no_use' );
                        }
                    }

                    // Individual dependencies
                    //------------------------------------

                    // Remove 'false' to disable color schemes less then main scheme!
                    // This behavious is not need for the version with sorted schemes (leave false)
                    if (false && id == 'color_scheme') {
                        fld = ctrl.find( '[name="kidscare_options_field_' + id + '"]' );
                        if (fld.length > 0) {
                            val     = kidscare_options_get_field_value( fld );
                            var num = kidscare_options_get_field_value( fld, true );
                            cont.find( '.kidscare_options_item_field' ).each(
                                function() {
                                    var ctrl2 = jQuery( this ), id2 = ctrl2.data( 'param' );
                                    if (id2 == undefined) {
                                        return;
                                    }
                                    if (id2 == id || id2.substr( -7 ) != '_scheme') {
                                        return;
                                    }
                                    var fld2 = ctrl2.find( '[name="kidscare_options_field_' + id2 + '"]' ),
                                        val2     = kidscare_options_get_field_value( fld2 );
                                    if (fld2.attr( 'type' ) != 'radio') {
                                        fld2 = fld2.find( 'option' );
                                    }
                                    fld2.each(
                                        function(idx2) {
                                            var dom_obj      = jQuery( this ).get( 0 );
                                            dom_obj.disabled = idx2 != 0 && idx2 < num;
                                            if (dom_obj.disabled) {
                                                if (jQuery( this ).val() == val2) {
                                                    if (fld2.attr( 'type' ) == 'radio') {
                                                        fld2.each(
                                                            function(idx3) {
                                                                jQuery( this ).get( 0 ).checked = idx3 == 0;
                                                            }
                                                        );
                                                    } else {
                                                        fld2.each(
                                                            function(idx2) {
                                                                jQuery( this ).get( 0 ).selected = idx3 == 0;
                                                            }
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    );
                                }
                            );
                        }
                    }
                }
            );
        }

	}
);
