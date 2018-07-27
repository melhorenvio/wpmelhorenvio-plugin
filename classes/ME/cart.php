<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once WC_ABSPATH.'/includes/wc-order-functions.php';

function getProductsInCart() {

    global $woocommerce;
    $items = $woocommerce->cart->get_cart();
    $pacote = new stdClass();

    return wpmelhorenvio_getProducts($items);
}