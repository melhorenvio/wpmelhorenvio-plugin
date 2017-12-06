<?php
/**
 * Created by PhpStorm.
 * User: VHSoa
 * Date: 04/12/2017
 * Time: 15:30
 */

include_once ABSPATH.'/wp-content/plugins/woocommerce/includes/wc-order-functions.php';
include_once 'quotation.php';

function wpme_getJsonOrders(){
    $orders = wc_get_orders([]);
    $dp    = is_null( null ) ? wc_get_price_decimals() : 2 ;
    $datas = array();
    foreach($orders as $order){
        $data = array(
            'id'                   => $order->get_id(),
            'number'               => $order->get_order_number(),
            'currency'             => $order->get_currency(),
            'date_modified'        => wc_rest_prepare_date_response( $order->get_date_modified() ), // v1 API used UTC.
            'customer_id'          => $order->get_customer_id(),
            'shipping'             => array(),
            'customer_note'        => $order->get_customer_note(),
            'date_completed'       => wc_rest_prepare_date_response( $order->get_date_completed(), false ), // v1 API used local time.
            'date_paid'            => wc_rest_prepare_date_response( $order->get_date_paid(), false ), // v1 API used local time.
            'cart_hash'            => $order->get_cart_hash(),
            'line_items'           => array(),
        );
        // Add addresses.
        $data['shipping'] = $order->get_address( 'shipping' );
        // Add line items.
        foreach ( $order->get_items() as $item_id => $item ) {
            $product      = $order->get_product_from_item( $item );
            $product_id   = 0;
            $variation_id = 0;
            $product_sku  = null;
            // Check if the product exists.
            if ( is_object( $product ) ) {
                $product_id   = $item->get_product_id();
                $variation_id = $item->get_variation_id();
                $product_sku  = $product->get_sku();
                $product_height = $product->get_height();
                $product_width = $product->get_width();
                $product_length = $product->get_length();
                $product_weight = $product->get_weight();

            }
            $item_meta = array();
            $hideprefix = 'true' === false ? null : '_';
            foreach ( $item->get_formatted_meta_data( $hideprefix, true ) as $meta_key => $formatted_meta ) {
                $item_meta[] = array(
                    'key'   => $formatted_meta->key,
                    'label' => $formatted_meta->display_key,
                    'value' => wc_clean( $formatted_meta->display_value ),
                );
            }
            $line_item = array(
                'id'           => $item_id,
                'name'         => $item['name'],
                'sku'          => $product_sku,
                'product_id'   => (int) $product_id,
                'height'       =>  max($product_height , 0),
                'width'        =>  max($product_width,0),
                'length'       =>  max($product_length,0),
                'weight'       =>  max($product_weight,0),
//                'height'       => $item['product_id']->getHeight(),
//                'width'        => $item['product_id']->getWidth(),
//                  'length'        => $item['product_id']->getLength(),
//                'weight'       => $item['product_id']->getWeight(),
                'variation_id' => (int) $variation_id,
                'quantity'     => wc_stock_amount( $item['qty'] ),
                'tax_class'    => ! empty( $item['tax_class'] ) ? $item['tax_class'] : '',
                'price'        => wc_format_decimal( $order->get_item_total( $item, false, false ), $dp ),
            );

        }
        $data['line_items'][] = $line_item;

        // Add shipping.
        foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
            $shipping_line = array(
                'id'           => $shipping_item_id,
                'method_title' => $shipping_item['name'],
                'method_id'    => $shipping_item['method_id'],
                'total'        => wc_format_decimal( $shipping_item['cost'], $dp ),
                'total_tax'    => wc_format_decimal( '', $dp ),
                'taxes'        => array(),
            );
            $data['shipping_lines'][] = $shipping_line;
        }

        array_push($datas, $data);
    }

    return json_encode($datas);
}

function wpme_getCustomerCotacaoAPI($request){
    $client = new WP_Http();
    $height = 20;
    $width  = 20;
    $weight = 20;
    $length = 20;
    $valor  = 20;
    $cep_origin = 96010280;
    $cep_destination = 98700000;
    $opcionais = wpme_getOptionals();
    $seguro = wpme_getValueInsurance($valor,$opcionais->VD);
    $params = array(
        'headers'           =>  ['Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token],
        'body'  =>[
            'from'      => $cep_origin,
            'to'        => $cep_destination,
            'width'     => $width,
            'height'    => $height,
            'length'    => $length,
            'weight'    => $weight,
            'services'  => wpme_getSavedServices(),
            'receipt'   => $opcionais->AR,
            'own_hand'  => $opcionais->MP,
            'insurance_value' => $seguro
        ],
        'timeout'=>10);

    $response = $client->get("https://melhorenvio.com.br/api/v2/calculator",$params);
    return is_array($response) ?  $response['body'] : [];
}

function wpme_getCustomerTrackingAPI($request){
    return 20;
}

function wpme_ticketAcquirementAPI($request){

}

function wpme_ticketPrintingAPI($request){

}