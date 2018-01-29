/*global wc_address_i18n_params */
jQuery( function( $ ) {

    // wc_address_i18n_params is required to continue, ensure the object exists
    if ( typeof wc_address_i18n_params === 'undefined' ) {
        return false;
    }

    var locale_json = wc_address_i18n_params.locale.replace( /&quot;/g, '"' ),
        locale = $.parseJSON( locale_json );

    function field_is_required( field, is_required ) {
        if ( is_required ) {
            field.find( 'label' ).append( ' <abbr class="required" title="' + wc_address_i18n_params.i18n_required_text + '">*</abbr>' );
            field.addClass( 'validate-required' );
        } else {
            field.find( 'label abbr' ).remove();
            field.removeClass( 'validate-required' );
        }
    }

    $( document.body )
    // Handle locale
        .bind( 'country_to_state_changing', function( event, country, wrapper ) {

            var thisform = wrapper, thislocale;

            if ( typeof locale[ country ] !== 'undefined' ) {
                thislocale = locale[ country ];
            } else {
                thislocale = locale['default'];
            }

            // Handle locale fields
            var locale_fields = $.parseJSON( wc_address_i18n_params.locale_fields );

            $.each( locale_fields, function( key, value ) {

                var field = thisform.find( value );

                if ( thislocale[ key ] ) {

                    if ( thislocale[ key ].label ) {
                        field.find( 'label' ).html( thislocale[ key ].label );
                    }

                    if ( thislocale[ key ].placeholder ) {
                        field.find( 'input' ).attr( 'placeholder', thislocale[ key ].placeholder );
                    }

                    field_is_required( field, false );

                    if ( typeof thislocale[ key ].required === 'undefined' && locale['default'][ key ].required === true ) {
                        field_is_required( field, true );
                    } else if ( thislocale[ key ].required === true ) {
                        field_is_required( field, true );
                    }

                    if ( key !== 'state' ) {
                        if ( thislocale[ key ].hidden === true ) {
                            field.hide().find( 'input' ).val( '' );
                        } else {
                            field.show();
                        }
                    }

                } else if ( locale['default'][ key ] ) {

                    if ( 'state' !== key ) {
                        if ( typeof locale['default'][ key ].hidden === 'undefined' || locale['default'][ key ].hidden === false ) {
                            field.show();
                        } else if ( locale['default'][ key ].hidden === true ) {
                            field.hide().find( 'input' ).val( '' );
                        }
                    }

                    if ( 'postcode' === key || 'city' === key || 'state' === key ) {
                        if ( locale['default'][ key ].label ) {
                            field.find( 'label' ).html( locale['default'][ key ].label );
                        }

                        if ( locale['default'][ key ].placeholder ) {
                            field.find( 'input' ).attr( 'placeholder', locale['default'][ key ].placeholder );
                        }
                    }

                    if ( locale['default'][ key ].required === true ) {
                        if ( field.find( 'label abbr' ).length === 0 ) {
                            field_is_required( field, true );
                        }
                    }
                }

            });
        });
});
