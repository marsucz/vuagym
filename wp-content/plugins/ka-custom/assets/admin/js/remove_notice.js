/* 
 Remove red notice
 */

jQuery(document).ready(function($) {
    
    // An thong bao license
    const el_error = document.getElementsByClassName("notice-error");
    while (el_error.length > 0) el_error[0].remove();
    
    // An thong bao update
    const el_update = document.getElementsByClassName("update-nag");
    while (el_update.length > 0) el_update[0].remove();
    
    // An thong bao update plugin
//    $('#setting-error-tgmpa').remove();

    $('#product-type').width('180px');
});

