 /* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($) {
    
    $( 'select#product-type' ).change( function () {
        var select_val = $( this ).val();
        if ( 'simple' === select_val ) {
                $( 'input#_mypos_other_store' ).change();
        } else {
                $( 'input#_mypos_other_store' ).prop( "checked", false );
        }
    });
    
});

