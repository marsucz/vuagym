<?php




function kawoo_set_regular_price_modal($product_id, $product_name, $regular_price) {
    
    return '<div class="modal fade" id="setPriceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="margin-top: 10%;>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Cập nhật giá sản phẩm</h4>
                </div>
                <form method="post" action="' . admin_url( 'admin-ajax.php' ) . '">
                    <div class="modal-body">
                        <div class="form-group">
                            <input class="form-control" type="hidden" name="product_id" value="' . $product_id . '">
                            <label for="heading">Tên sản phẩm</label>
                            <p class="form-control-static">' . $product_name . '</p>
                        </div>
                        <div class="form-group">
                            <label for="heading">Giá gốc mới:</label>
                            <input class="form-control" id="new_regular" name="new_regular" placeholder="Nhập giá sản phẩm mới" required>
                        </div>
                        <div class="form-group">
                            <label for="heading">Giá sale mới:</label>
                            <input class="form-control" id="new_sale" name="new_sale" value="' . $regular_price .'" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>';

}

function ja_ajax_kawoo_get_set_price_popup() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $product = wc_get_product($product_id);
    $product_name = mypos_get_variation_title($product);
    $regular_price = $product->get_regular_price();
    
    $template = kawoo_set_regular_price_modal($product_id, $product_name, $regular_price);
    
    $return = $template;
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_kawoo_get_set_price_popup', 'ja_ajax_kawoo_get_set_price_popup' );
add_action( 'wp_ajax_nopriv_kawoo_get_set_price_popup', 'ja_ajax_kawoo_get_set_price_popup' );