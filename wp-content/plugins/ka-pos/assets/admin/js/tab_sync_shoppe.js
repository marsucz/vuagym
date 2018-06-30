 /* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function updateExcelCell(button, product_id, cell_type, cell_pos, cell_value) {
        
            $(button).prop('disabled', true);

            $.post(
            global.ajax,
            {
                product_id: product_id,
                cell_type: cell_type,
                cell_pos: cell_pos,
                cell_value: cell_value,
                action: 'set_excel_cell_value' 
            }, 
            function(data) {
                console.log(data);
                $(button).html('<i class="fa fa-check"></i>  Done');
            });
};

jQuery(document).ready(function($) {
    
});
