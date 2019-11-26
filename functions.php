<?php

class Storefront_Child {

    function __construct() {

        // increment count of product views
        add_action('woocommerce_before_single_product', array($this, 'views_counter'));

        // output product custom fields at single page
        add_action('woocommerce_product_meta_end', array($this, 'print_views_counter'));
        add_action('woocommerce_product_meta_end', array($this, 'print_last_purchase_date'));

        // output product custom fields at loop
        add_action('woocommerce_after_shop_loop_item_title', array($this, 'print_loop_views_counter'));
        add_action('woocommerce_after_shop_loop_item_title', array($this, 'print_loop_last_purchase_date'));

        // add/update product purchase date
        add_action('woocommerce_checkout_create_order', array($this, 'update_product_purchase'));
    }

    function views_counter() {
        $prod_id = get_the_ID();

        $prod_views = $this->get_views($prod_id);
        $prod_views++;

        update_post_meta($prod_id, '_storefront_child_views', $prod_views);
    }

    function get_views($prod_id) {
        $prod_views = get_post_meta($prod_id, '_storefront_child_views', true);
        if (!$prod_views) {
            $prod_views = 0;
        }
        return $prod_views;
    }

    function get_last_purchase_date($prod_id) {
        $prod_last_purchase_date = get_post_meta($prod_id, '_storefront_child_last_purchase_date', true);
        if ($prod_last_purchase_date) {
            $prod_last_purchase_date = get_date_from_gmt(
                $prod_last_purchase_date,
                get_option('date_format').' | '.get_option('time_format')
            );
        }
        return $prod_last_purchase_date;
    }

    function print_views_counter() {
        $prod_views = $this->get_views(get_the_ID());
        echo "<span class='views'>Views: <span>{$prod_views}</span></span>";
    }

    function print_last_purchase_date() {
        $prod_last_purchase_date = $this->get_last_purchase_date(get_the_ID());

        if ($prod_last_purchase_date) {
            echo "<span class='purchase_date'>Last purchase date: <span>{$prod_last_purchase_date}</span></span>";
        }
    }

    function print_loop_views_counter() {
        global $product;

        $prod_views = $this->get_views($product->get_id());
        echo "<span class='views'>Views: <span>{$prod_views}</span></span>";

    }

    function print_loop_last_purchase_date() {
        global $product;

        $prod_last_purchase_date = $this->get_last_purchase_date($product->get_id());
        if ($prod_last_purchase_date) {
            echo "<span class='purchase_date'>Last purchase date: <br><span>{$prod_last_purchase_date}</span></span>";
        }
    }

    function update_product_purchase($order) {
        /**
         * @var $order WC_Order
         * @var $product WC_Product
         */
        $items = $order->get_items();
        foreach ($items as  $item_id => $item_data)
        {
            $product = $item_data->get_product();
            update_post_meta($product->get_id(), '_storefront_child_last_purchase_date', current_time('mysql', true));
        }
    }
}

new Storefront_Child();
