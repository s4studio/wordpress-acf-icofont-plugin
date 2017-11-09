(function($){
	
	function update_preview( value, parent ) {
		$( '.acf-field-setting-icofont_live_preview .acf-input', parent ).html( '<i class="icofont ' + value + '" aria-hidden="true"></i>' );
		$( '.icon_preview', parent ).html( '<i class="icofont ' + value + '" aria-hidden="true"></i>' );
	}

	function select2_init_args( element, parent ) {
		return {
			key			: $( parent ).data('key'),
			allow_null	: $( element ).data('allow_null'),
			ajax		: 1,
			ajax_action	: 'acf/fields/icofont/query'
		}
	}

	function select2_init( icfnt_field ) {
		var $select = $( icfnt_field );
		var parent = $( $select ).closest('.acf-field-icofont');

		update_preview( $select.val(), parent );

		acf.select2.init( $select, select2_init_args( icfnt_field, parent ), $( icfnt_field ) );
	}

	acf.add_action( 'select2_init', function( $input, args, settings, $field ) {
		if ( $field instanceof jQuery && $field.hasClass('icofont-edit') ) {
			$field.addClass('select2_initalized');
		}
	});

	// Add our classes to IconFont select2 fields
	acf.add_filter( 'select2_args', function( args, $select, settings, $field ) {

		if ( $select.hasClass('select2-icofont') ) {
			args.dropdownCssClass = 'icofont-select2-drop';
			args.containerCssClass = 'icofont-select2';
		}

		return args;
	});

	// Update IconFont field previews in field create area
	acf.add_action( 'open_field/type=icofont', function( $el ) {
		var $field_objects = $('.acf-field-object[data-type="icofont"]');

		$field_objects.each( function( index, field_object ) {
			update_preview( $( 'select.icofont-create', field_object ).val(), field_object );
		});
	});

	// Handle new menu items with IconFont fields assigned to them
	$( document ).on( 'menu-item-added', function( event, $menuMarkup ) {
		var $icfnt_fields = $( 'select.icofont-edit:not(.select2_initalized)', $menuMarkup );

		if ( $icfnt_fields.length ) {
			$icfnt_fields.each( function( index, icfnt_field ) {
				select2_init( icfnt_field );
			});
		}
	});

	// Update IconFont field previews and init select2 in field edit area
	acf.add_action( 'ready_field/type=icofont append_field/type=icofont show_field/type=icofont', function( $el ) {
		var $icfnt_fields = $( 'select.icofont-edit:not(.select2_initalized)', $el );

		if ( $icfnt_fields.length ) {
			$icfnt_fields.each( function( index, icfnt_field ) {
				select2_init( icfnt_field );
			});
		}
	});

	// Update IconFont field previews when value changes
	acf.add_action( 'change', function( $input ) {

		if ( $input.hasClass('icofont-create') ) {
			update_preview( $input.val(), $input.closest('.acf-field-object') );
		}

		if ( $input.hasClass('icofont-edit') ) {
			update_preview( $input.val(), $input.closest('.acf-field-icofont') );
		}
	});

})(jQuery);
