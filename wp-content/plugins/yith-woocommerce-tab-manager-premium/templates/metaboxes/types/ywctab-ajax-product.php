<?php
if( !defined( 'ABSPATH' ) )
    exit;

extract( $args );

global $post , $YWC_Product_Slider;
$placeholder_txt    =   isset( $placeholder ) ? $placeholder : '';
$is_multiple = isset( $multiple ) && $multiple;
$multiple = ( $is_multiple ) ? 'true' : 'false';


$product_ids =  get_post_meta( $post->ID, $id  , true   ) ;

if( !is_array( $product_ids )  ){
    $product_ids = explode(',', $product_ids );

}


$json_ids   =   array();

if( $product_ids ) {

    foreach ( $product_ids as $product_id ) {

        $product = wc_get_product( $product_id );
        if( is_object( $product ) ) {

            $product_name = wp_kses_post( $product->get_formatted_name() );

            $json_ids[$product_id] =  $product_name;
        }
    }
}

?>

<div id="<?php echo $id ?>-container" <?php if ( isset( $deps ) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps['ids'] ?>" data-value="<?php echo $deps['values'] ?>" <?php endif ?>>
    <label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html($label ); ?></label>
    <?php if( version_compare( WC()->version,'2.7.0','>=' ) ):?>
    <select class="wc-product-search" multiple="multiple" style="width: 50%;" name="<?php echo esc_attr( $name );?>[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'yith-woocommerce-product-slider-carousel' ); ?>" data-action="woocommerce_json_search_products_and_variations">
        <?php

        foreach ( $json_ids as $product_id => $product_name ) {

            echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . $product_name. '</option>';
        }

        ?>
    </select>

    <?php else:?>
    <input type="hidden" style="width:80%;" class="wc-product-search" id="<?php echo esc_attr( $id );?>" name="<?php echo esc_attr( $name );?>" data-placeholder="<?php echo $placeholder_txt; ?>" data-multiple="<?php echo $multiple;?>" data-selected="<?php echo esc_attr( json_encode( $json_ids ) ); ?>"
           value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />
    <?php endif;?>
        <span class="desc inline"><?php echo $desc ?></span>
</div>