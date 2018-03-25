== Changelog ==

= 2.3.0 = Released on Feb 01, 2017

* New: Support to WordPress 4.9.2.
* New: Support to WooCommerce 3.3.0.
* Update: Plugin Core.
* Update: Language Files.
* Fix: Custom attributes missing for not latin charset.
* Fix: Removed unused widget options.

= 2.2.4 = Released on Dec 21, 2017

* New: Support to WordPress 4.9.1.
* New: Support to WooCommerce 3.2.6.
* New: Product image in compare widget.
* New: Compare widget template templates/yith-compare-widget-item.php.
* Update: Plugin Core.
* Update: Language Files.
* Fix: Reset query and postdata after each compare table.

= 2.2.3 = Released on Nov 23, 2017

* New: Support to WordPress 4.9.0.
* New: Support to WooCommerce 3.2.5.
* New: Widget reloads using AJAX.
* Fix: WPML config XML.

= 2.2.2 = Released on Nov 10, 2017

* New: Support to WordPress 4.8.3.
* New: Support to WooCommerce 3.2.3.
* New: Product SKU in compare table.
* Update: Plugin Core.
* Fix: Related products and share link after an ajax event on compare table.
* Fix: Make sure to get always WPML translated product on add compare link.
* Fix: Removed unused global product in compare table.
* Dev: New filter yith_woocompare_widget_products_html for compare widget html.

= 2.2.1 = Released on Oct 20, 2017

* New: Support to WordPress 4.8.2.
* New: Support to WooCommerce 3.2.1.
* Update: Plugin Core.
* Update: dataTables and FixedColumns scripts.
* Fix: Fixed columns for compare table.
* Fix: RTL style.
* Dev: New filter yith_woocompare_get_product_categories for product categories on compare.

= 2.2.0 = Released on Aug 16, 2017

* New: Support to WordPress 4.8.1.
* New: Support to WooCommerce 3.1.2.
* New: Compatibility with YITH WooCommerce Request A Quote Premium. Now is possible show quote button on Compare table.
* New: Show Vendor Name under product name in compare table if YITH WooCommerce Multi Vendor is active.
* New: German Translation.
* Update: Plugin Core.
* Update: Language Files.
* Fix: Empty compare template missing main div closure.
* Fix: Problem with WPML and add to cart action from Compare table.
* Fix: Remove "WooCommerce Product Filter" plugin scripts from Compare popup causing JavaScript error

= 2.1.0 = Released on Apr 04, 2017

* New: Support to WordPress 4.7.3.
* New: Support to WooCommerce 3.0.0.
* New: Option for select compare page.
* New: Option for add "Clear all" button on Compare table.
* Update: Plugin Core.
* Fix: Compatibility issue with YITH WooCommerce Colors and Labels Variations Premium.
* Fix: Table shortcode return value.

= 2.0.7 = Released on Oct 25, 2016

* New: Option for change the label of the compare button/link for products that already are in compare table.
* New: Auto remove from compare list for products that are private or were cancelled.
* New: Add as default fields Weight and Dimensions.
* New: Italian Translation.
* New: Spanish Translation.
* Update: Language Files
* Update: Plugin Core.
* Fix: Button "Compare" on widget always loads popup.
* Fix: Empty compare table for translated products with WPML.

= 2.0.6 = Released on Apr 15, 2016

* New: Compatibility with WordPress 4.5
* Update: Plugin Core.

= 2.0.5 = Released on Apr 01, 2016

* New: Full WPML compatibility.
* New: Hide widget if compare table is empty.
* New: Option to choose the number of fixed columns in compare table.
* New: Action "yith_woocompare_popup_head" on compare popup to add custom style or scripts
* New: Support to WooCommerce Product Bundle plugin.
* Update: Plugin Core.
* Update: Language file.
* Fix: Refresh Compare page after a products are removed from the widget.
* Fix: Facebook sharing link. Added Facebook App ID field on Social Network Sites Sharing tab.
* Fix: Product status now updates automatically when the product is removed from the compare widget.
* Fix: Redirect correctly to cart page after "Add to cart" is clicked if "Redirect to the cart page after successful addition" option is enabled.
* Fix: Redirect correctly to product page after "Add to cart" is clicked when "Enable AJAX add to cart buttons on archives" option is disabled.

= 2.0.4 = Released on Jan 11, 2016

* New: Compatibility with WooCommerce 2.5 RC.
* New: Translation on slovak language
* Update: Plugin template yith-compare-related.php
* Update: Plugin template yith-compare-table.php
* Update: Change all ajax call from admin-ajax to wc-ajax.
* Update: Plugin Core
* Fix: Multiple custom product attributes have same values.
* Fix: Save option with WooCommerce version 2.3.x

= 2.0.3 = Released on Oct 20, 2015

* New: Ability to compare also custom product attributes
* New: Shortocode to add compare table with custom products
* New: Admin tab where you can build your own compare shortcode
* New: Choose to highlights the attributes that have different value
* Update: yith-compare-table template to version 2.0.3
* Update: Changed textdomain to yith-wcmp to yith-woocommerce-compare
* Update: Language file
* Update: PLugin Core

= 2.0.2 = Released on Aug 21, 2015

* New: Compatibility with WooCommerce 2.4.x
* New: Compatibility with WordPress 4.3
* Update: PLugin Core

= 2.0.1 = Released on Aug 10, 2015

* Update: main plugin js
* Fix: product without category and category navigation

= 2.0.0 = Released on Jul 14, 2015

* Initial release