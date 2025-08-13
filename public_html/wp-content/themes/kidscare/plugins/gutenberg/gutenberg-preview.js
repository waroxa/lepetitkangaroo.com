/* global jQuery, KIDSCARE_STORAGE */

jQuery( window ).on( 'load', function() {
	"use strict";
	kidscare_gutenberg_first_init();
	// Create the observer to reinit visual editor after switch from code editor to visual editor
	var kidscare_observers = {};
	if (typeof window.MutationObserver !== 'undefined') {
		kidscare_create_observer('check_visual_editor', jQuery('.block-editor').eq(0), function(mutationsList) {
			var gutenberg_editor = jQuery('.edit-post-visual-editor:not(.kidscare_inited)').eq(0);
			if (gutenberg_editor.length > 0) kidscare_gutenberg_first_init();
		});
	}

	function kidscare_gutenberg_first_init() {
		var gutenberg_editor = jQuery( '.edit-post-visual-editor:not(.kidscare_inited)' ).eq( 0 );
		if ( 0 == gutenberg_editor.length ) {
			return;
		}
		jQuery( '.editor-block-list__layout' ).addClass( 'scheme_' + KIDSCARE_STORAGE['color_scheme'] );
		gutenberg_editor.addClass( 'sidebar_position_' + KIDSCARE_STORAGE['sidebar_position'] );
		if ( KIDSCARE_STORAGE['expand_content'] > 0 ) {
			gutenberg_editor.addClass( 'expand_content' );
		}
		if ( KIDSCARE_STORAGE['sidebar_position'] == 'left' ) {
			gutenberg_editor.prepend( '<div class="editor-post-sidebar-holder"></div>' );
		} else if ( KIDSCARE_STORAGE['sidebar_position'] == 'right' ) {
			gutenberg_editor.append( '<div class="editor-post-sidebar-holder"></div>' );
		}

		gutenberg_editor.addClass('kidscare_inited');
	}

	// Create mutations observer
	function kidscare_create_observer(id, obj, callback) {
		if (typeof window.MutationObserver !== 'undefined' && obj.length > 0) {
			if (typeof kidscare_observers[id] == 'undefined') {
				kidscare_observers[id] = new MutationObserver(callback);
				kidscare_observers[id].observe(obj.get(0), { attributes: false, childList: true, subtree: true });
			}
			return true;
		}
		return false;
	}
} );
