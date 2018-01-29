jQuery( function ( $ ) {
    /**=============================
     *  Toggle
     * =============================
     */
    $( '.yith-wcbep-toggle' ).each( function () {
        var $toggleAnchor = $( this ),
            $target       = $( $toggleAnchor.data( 'target' ) );

        $toggleAnchor.on( 'click', function () {
            if ( $( this ).is( '.closed' ) ) {
                $( this ).removeClass( 'closed' );
                $target.show();
            } else {
                $( this ).addClass( 'closed' );
                $target.hide();
            }
        } );
    } );


} );