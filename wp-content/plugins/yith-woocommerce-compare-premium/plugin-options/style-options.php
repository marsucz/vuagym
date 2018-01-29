<?php
/**
 * STYLE OPTIONS ARRAY
 *
 * @author Your Inspiration Themes
 * @package YITH Woocommerce Compare Premium
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
	exit;
} // Exit if accessed directly

$options = array(
    'style' => array(

        array(
            'name' => __( 'Category Filter Options', 'yith-woocommerce-compare' ),
            'type' => 'title',
            'desc' => '',
            'id' => 'yith-woocompare-filter-style-end'
        ),

	    array(
		    'name'      => __( 'Filter title', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The title for the category filter section', 'yith-woocommerce-compare' ),
		    'type'      => 'text',
		    'default'   => __( 'Category Filter', 'yith-woocommerce-compare' ),
		    'id'        => 'yith-woocompare-categories-filter-title'
	    ),

	    array(
		    'name'      => __( 'Filter title color', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The color for the title of the category filter section', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#333333',
		    'id'        => 'yith-woocompare-categories-filter-title-color'
	    ),

	    array(
		    'name'      => __( 'Filter link color', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The color for the link of the category filter', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#777777',
		    'id'        => 'yith-woocompare-categories-filter-link-color'
	    ),

	    array(
		    'name'      => __( 'Filter link hover color', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The color for the link of the category filter on mouse hover', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#333333',
		    'id'        => 'yith-woocompare-categories-filter-link-hover-color'
	    ),

	    array(
		    'type'      => 'sectionend',
		    'id'        => 'yith-woocompare-filter-end'
	    ),

	    array(
		    'name' => __( 'Table options', 'yith-woocommerce-compare' ),
		    'type' => 'title',
		    'desc' => '',
		    'id' => 'yith-woocompare-table-style'
	    ),

	    array(
			'name'      => __( 'Remove color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the "Remove Product" link', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#777777',
		    'id'        => 'yith-woocompare-table-remove-color'
	    ),

	    array(
			'name'      => __( 'Remove hover color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the "Remove Product" link on mouse hover', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#333333',
		    'id'        => 'yith-woocompare-table-remove-color-hover'
	    ),

	    array(
			'name'      => __( 'Button text color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the text of the button', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#ffffff',
		    'id'        => 'yith-woocompare-table-button-text-color'
	    ),

	    array(
			'name'      => __( 'Button text hover color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the text of the button on mouse hover', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#ffffff',
		    'id'        => 'yith-woocompare-table-button-text-color-hover'
	    ),

	    array(
			'name'      => __( 'Button color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the button background', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#b2b2b2',
		    'id'        => 'yith-woocompare-table-button-color'
	    ),

	    array(
			'name'      => __( 'Button hover color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the button background on mouse hover', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#303030',
		    'id'        => 'yith-woocompare-table-button-color-hover'
	    ),

	    array(
			'name'      => __( 'Star color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color of the star', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#303030',
		    'id'        => 'yith-woocompare-table-star'
	    ),

		array(
			'name'      => __( 'Highlighted row color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The background color of highlighted row', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#e4e4e4',
		    'id'        => 'yith-woocompare-highlights-color'
	    ),

	    array(
		    'type'      => 'sectionend',
		    'id'        => 'yith-woocompare-table-style-end'
	    ),

	    array(
		    'name' => __( 'Share Options', 'yith-woocommerce-compare' ),
		    'type' => 'title',
		    'desc' => '',
		    'id' => 'yith-woocompare-share-style'
	    ),

	    array(
		    'name'      => __( 'Share Title', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The title for the share section', 'yith-woocommerce-compare' ),
		    'type'      => 'text',
		    'default'   => __( 'Share on', 'yith-woocommerce-compare' ),
		    'id'        => 'yith-woocompare-share-title'
	    ),

	    array(
		    'name'      => __( 'Share Title Color', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The color for the title of the share section', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#333333',
		    'id'        => 'yith-woocompare-share-title-color'
	    ),

	    array(
		    'type'      => 'sectionend',
		    'id'        => 'yith-woocompare-share-style-end'
	    ),

	    array(
		    'name' => __( 'Related Options', 'yith-woocommerce-compare' ),
		    'type' => 'title',
		    'desc' => '',
		    'id' => 'yith-woocompare-related-style'
	    ),

	    array(
		    'name'      => __( 'Related Title', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The title for the related section', 'yith-woocommerce-compare' ),
		    'type'      => 'text',
		    'default'   => __( 'Related Products', 'yith-woocommerce-compare' ),
		    'id'        => 'yith-woocompare-related-title'
	    ),

	    array(
		    'name'      => __( 'Related Title Color', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The color for the title of the related section', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#333333',
		    'id'        => 'yith-woocompare-related-title-color'
	    ),

	    array(
		    'name'      => __( 'Button text color', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The color for the text of the button', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#ffffff',
		    'id'        => 'yith-woocompare-related-button-text-color'
	    ),

	    array(
		    'name'      => __( 'Button text hover color', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The color for the text of the button on mouse hover', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#ffffff',
		    'id'        => 'yith-woocompare-related-button-text-color-hover'
	    ),

	    array(
		    'name'      => __( 'Button color', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The color for the button background', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#b2b2b2',
		    'id'        => 'yith-woocompare-related-button-color'
	    ),

	    array(
		    'name'      => __( 'Button hover color', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'The color for the button background on mouse hover', 'yith-woocommerce-compare' ),
		    'type'      => 'color',
		    'default'   => '#303030',
		    'id'        => 'yith-woocompare-related-button-color-hover'
	    ),

	    array(
		    'type'      => 'sectionend',
		    'id'        => 'yith-woocompare-related-style-end'
	    ),
    )
);

return apply_filters( 'yith_woocompare_style_settings', $options );
