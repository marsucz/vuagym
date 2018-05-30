 /* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function setPreOrder(product_id) {
        
        $('#setPreOrder_' + product_id).prop('disabled', true);
        
        $.post(
        global.ajax, 
        {   
            product_id: product_id,
            action: 'mypos_set_pre_order' 
        }, 
        function(data) {
            console.log(data);
            $('#setPreOrder_' + product_id).html('<i class="fa fa-check"></i>  Done');
        });
};

jQuery(document).ready(function($) {
    
    show_type_change();
    $('#sync_by_web_show_type').on('change', function() {
        show_type_change();
    });
    
    function show_type_change() {
        if ($('#sync_by_web_show_type').val() == 1) {
            $("#importfile").show();
            $("#sync_by_web_products_label").hide();
            $("#sync_by_web_products").hide();
            $("input[type=submit]").val("Upload");
        } else {
            $("#importfile").hide();
            $("#sync_by_web_products_label").show();
            $("#sync_by_web_products").show();
            $("input[type=submit]").val("Áp dụng");
        }
    }
    
    $("#import_manager_form").submit(function(event){
    // Prevent form submission until we can call the server
    
    $("#notice").remove();
    
    if ($('#importfile').get(0).files.length === 0) {
        $("#importfile").remove();
        $("#import_manager_form").unbind("submit");
        $("#import_manager_form").submit();
        return;
    }
    
    event.preventDefault();
    
    var filename = $('input[name=importfile]').val().split('\\').pop();
    $("input[type=submit]").val("Đang xử lý...");
    $("input[type=submit]").prop('disabled', true);
    $.post(
        global.ajax, 
        {   
            file_name: filename,
            action: 'mypos_check_exists_file' 
        }, 
        function(response) {
            $("input[type=submit]").val("Áp dụng");
            $("input[type=submit]").prop('disabled', false);
            
            console.log(response);
            if (response.data.status == true) {
                var r = confirm("File đã tồn tại trên hệ thống, bạn có muốn ghi đè không?");
                if (r == true) {
                    $("#import_manager_form").unbind("submit");
                    $("#import_manager_form").submit();
                } else {
                    console.log("Không ghi đè");
                }
                return;
            }
            if (response.data.status == false) {
                $("#import_manager_form").unbind("submit");
                $("#import_manager_form").submit();
            }
        });
    });
});

function getImportFile(e, filename) {
        
        $('#import-detail').remove();
        $(e).html('<i class="fa fa-check"></i>  Đang xử lý...');
        $(e).prop('disabled', true);
        
        $.post(
        global.ajax, 
        {
            file_name: filename,
            action: 'mypos_import_file_detail' 
        }, 
        function(response) {
            console.log(response);
            $(e).html('<i class="fa fa-check"></i>  Xem chi tiết');
            $(e).prop('disabled', false);
            $('#wpwrap').append(response.data.html);
            $('#import-detail').modal('show');
        });
};

function deleteImportFile(e, filename) {
        
        $(e).html('<i class="fa fa-check"></i>  Đang xử lý...');
        $(e).prop('disabled', true);
        
        $.post(
        global.ajax, 
        {   
            file_name: filename,
            action: 'mypos_delete_import_file' 
        }, 
        function(response) {
            console.log(response);
            $(e).html('<i class="fa fa-check"></i>  Done');
        });
};