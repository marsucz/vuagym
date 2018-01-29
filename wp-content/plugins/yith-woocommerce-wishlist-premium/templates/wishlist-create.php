<?php
/**
 * Wishlist create template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.5
 */

if ( ! defined( 'YITH_WCWL' ) ) {
    exit;
} // Exit if accessed directly
?>

<form id="yith-wcwl-form" action="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'create' ) ) ?>" method="post">
    <!-- TITLE -->
    <?php
    do_action( 'yith_wcwl_before_wishlist_title' );

    if( ! empty( $page_title ) ) {
        echo apply_filters( 'yith_wcwl_wishlist_title', '<h2>' . $page_title . '</h2>' );
    }

    do_action( 'yith_wcwl_before_wishlist_create' );
    ?>

    <div class="yith-wcwl-wishlist-new">

        <label for="wishlist_name"><?php echo apply_filters( 'yith_wcwl_new_list_title_text', __( 'Wishlist name', 'yith-woocommerce-wishlist' ) ) ?></label>
        <input name="wishlist_name" class="wishlist-name" type="text" class="wishlist-name" />
        <select name="wishlist_visibility" class="wishlist-visibility selectBox">
            <option value="0" class="public-visibility"><?php echo apply_filters( 'yith_wcwl_public_wishlist_visibility', __( 'Public', 'yith-woocommerce-wishlist' ) )?></option>
            <option value="1" class="shared-visibility"><?php echo apply_filters( 'yith_wcwl_shared_wishlist_visibility', __( 'Shared', 'yith-woocommerce-wishlist' ) )?></option>
            <option value="2" class="private-visibility"><?php echo apply_filters( 'yith_wcwl_private_wishlist_visibility', __( 'Private', 'yith-woocommerce-wishlist' ) )?></option>
        </select>

        <button class="create-wishlist-button">
            <?php echo apply_filters( 'yith_wcwl_wishlist_create_icon', '<i class="icon-ok"></i>' )?>
            <?php _e( 'Save', 'yith-woocommerce-wishlist' ) ?>
        </button>

        <?php wp_nonce_field( 'yith_wcwl_create_action', 'yith_wcwl_create' )?>

    </div>
    <?php do_action( 'yith_wcwl_after_wishlist_create' ); ?>
</form>