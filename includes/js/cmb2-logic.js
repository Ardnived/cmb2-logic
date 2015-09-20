
var cmb2_logic_data; // Filled by php.

var CMB2_Logic = {
	data: cmb2_logic_data,

	init: function() {
		console.log('test', CMB2_Logic.data );
		for ( var field_slug in CMB2_Logic.data ) {
			var dependents = CMB2_Logic.parse_dependents( CMB2_Logic.data );

			console.log( field_slug, "has dependents", dependents );

			if ( dependents.length > 0 ) {
				dependents = dependents.reduce( function(p, c) {
					if ( p.indexOf(c) < 0 ) p.push(c);
					return p;
				}, [] );

				for ( var i = dependents.length - 1; i >= 0; i-- ) {
					CMB2_Logic.set_listener( '#'+dependents[i], field_slug );
				};

				jQuery( '#'+dependents[0] ).change();
			}
		}
	},

	parse_dependents: function( data ) {
		var list = [];

		for ( var key in data ) {
			if ( key == 'field' ) {
				list.push( data['field'] );
			} else if ( data[key].constructor === Object ) {
				list.push.apply( list, CMB2_Logic.parse_dependents( data[key] ) );
			}
		}

		return list;
	},

	set_listener: function( source, target_field ) {
		jQuery(source).on( 'change', function() {
			console.log('revalidating', CMB2_Logic.data);
			var field = jQuery( '.cmb2-id-' + target_field );

			if ( CMB2_Logic.validate_logic( CMB2_Logic.data[target_field] ) ) {
				console.log('success');
				field.show();
			} else {
				console.log('failure');
				field.hide();
			}
		} );
	},

	validate_logic: function( data ) {
		if ( data.hasOwnProperty( 'op' ) ) {
			return CMB2_Logic.validate_logic_group( data );
		} else {
			return CMB2_Logic.validate_logic_clause( data );
		}
	},

	validate_logic_group: function( data ) {
		// If the 'not' attribute is set, then invert true and false.
		var affirmative = ( data.not == true ? false : true );

		if ( group.op === 'and' ) {
			for ( var i = data.group.length - 1; i >= 0; i-- ) {
				if ( ! CMB2_Logic.validate_logic( data.group[i] ) ) {
					// If the sublogic fails, then return a failure.
					return ! affirmative;
				}
			};

			// If none of the sublogic fails, then return a success.
			return affirmative;
		} else if ( group.op === 'or' ) {
			for ( var i = data.group.length - 1; i >= 0; i-- ) {
				if ( CMB2_Logic.validate_logic( data.group[i] ) ) {
					// If the sublogic succeeds, then return a success.
					return affirmative;
				}
			};

			// If none of the sublogic succeeds, then return a failure.
			return ! affirmative;
		} else {
			console.log( "CMB2_Logic: tried to parse unrecognized group operation, '", group.op, "'" );
		}
	},

	validate_logic_clause: function( data ) {
		// If the 'not' attribute is set, then invert true and false.
		var affirmative = ( data.not == true ? false : true );
		var value = jQuery( '#'+data.field ).val();
		var test;

		if ( data.value.constructor === Array ) {
			test = ( data.value.indexOf( value ) != -1 );
		} else {
			test = ( data.value == value );
		}

		console.log("compare", data.value, "/", value, "/", test);

		if ( test ) {
			return affirmative;
		} else {
			return ! affirmative;
		}
	},

}

jQuery(document).ready( function() {
	jQuery('.cmb-form').hide();
	CMB2_Logic.init();
	jQuery('.cmb-form').fadeIn();
} );