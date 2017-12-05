<?php
/**
 * Created by PhpStorm.
 * User: VHSoa
 * Date: 04/12/2017
 * Time: 15:30
 */

include_once ABSPATH.'/wp-content/plugins/woocommerce/includes/wc-order-functions.php';

function wpme_getJsonOrders(){
    $orders = wc_get_orders([]);
    $dp    = is_null( null ) ? wc_get_price_decimals() : absint( $request['dp'] );
    $datas = array();

    foreach($orders as $order){
        $data = array(
            'id'                   => $order->get_id(),
            'parent_id'            => $order->get_parent_id(),
            'status'               => $order->get_status(),
            'order_key'            => $order->get_order_key(),
            'number'               => $order->get_order_number(),
            'currency'             => $order->get_currency(),
            'version'              => $order->get_version(),
            'prices_include_tax'   => $order->get_prices_include_tax(),
            'date_created'         => wc_rest_prepare_date_response( $order->get_date_created() ),  // v1 API used UTC.
            'date_modified'        => wc_rest_prepare_date_response( $order->get_date_modified() ), // v1 API used UTC.
            'customer_id'          => $order->get_customer_id(),
            'discount_total'       => wc_format_decimal( $order->get_total_discount(), $dp ),
            'discount_tax'         => wc_format_decimal( $order->get_discount_tax(), $dp ),
            'shipping_total'       => wc_format_decimal( $order->get_shipping_total(), $dp ),
            'shipping_tax'         => wc_format_decimal( $order->get_shipping_tax(), $dp ),
            'cart_tax'             => wc_format_decimal( $order->get_cart_tax(), $dp ),
            'total'                => wc_format_decimal( $order->get_total(), $dp ),
            'total_tax'            => wc_format_decimal( $order->get_total_tax(), $dp ),
            'billing'              => array(),
            'shipping'             => array(),
            'payment_method'       => $order->get_payment_method(),
            'payment_method_title' => $order->get_payment_method_title(),
            'transaction_id'       => $order->get_transaction_id(),
            'customer_ip_address'  => $order->get_customer_ip_address(),
            'customer_user_agent'  => $order->get_customer_user_agent(),
            'created_via'          => $order->get_created_via(),
            'customer_note'        => $order->get_customer_note(),
            'date_completed'       => wc_rest_prepare_date_response( $order->get_date_completed(), false ), // v1 API used local time.
            'date_paid'            => wc_rest_prepare_date_response( $order->get_date_paid(), false ), // v1 API used local time.
            'cart_hash'            => $order->get_cart_hash(),
            'line_items'           => array(),
            'tax_lines'            => array(),
            'shipping_lines'       => array(),
            'fee_lines'            => array(),
            'coupon_lines'         => array(),
            'refunds'              => array(),
        );
        // Add addresses.
        $data['billing']  = $order->get_address( 'billing' );
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
                'variation_id' => (int) $variation_id,
                'quantity'     => wc_stock_amount( $item['qty'] ),
                'tax_class'    => ! empty( $item['tax_class'] ) ? $item['tax_class'] : '',
                'price'        => wc_format_decimal( $order->get_item_total( $item, false, false ), $dp ),
                'subtotal'     => wc_format_decimal( $order->get_line_subtotal( $item, false, false ), $dp ),
                'subtotal_tax' => wc_format_decimal( $item['line_subtotal_tax'], $dp ),
                'total'        => wc_format_decimal( $order->get_line_total( $item, false, false ), $dp ),
                'total_tax'    => wc_format_decimal( $item['line_tax'], $dp ),
                'taxes'        => array(),
                'meta'         => $item_meta,
            );
            $item_line_taxes = maybe_unserialize( $item['line_tax_data'] );
            if ( isset( $item_line_taxes['total'] ) ) {
                $line_tax = array();
                foreach ( $item_line_taxes['total'] as $tax_rate_id => $tax ) {
                    $line_tax[ $tax_rate_id ] = array(
                        'id'       => $tax_rate_id,
                        'total'    => $tax,
                        'subtotal' => '',
                    );
                }
                foreach ( $item_line_taxes['subtotal'] as $tax_rate_id => $tax ) {
                    $line_tax[ $tax_rate_id ]['subtotal'] = $tax;
                }
                $line_item['taxes'] = array_values( $line_tax );
            }
            $data['line_items'][] = $line_item;
        }
        // Add taxes.
        foreach ( $order->get_items( 'tax' ) as $key => $tax ) {
            $tax_line = array(
                'id'                 => $key,
                'rate_code'          => $tax['name'],
                'rate_id'            => $tax['rate_id'],
                'label'              => isset( $tax['label'] ) ? $tax['label'] : $tax['name'],
                'compound'           => (bool) $tax['compound'],
                'tax_total'          => wc_format_decimal( $tax['tax_amount'], $dp ),
                'shipping_tax_total' => wc_format_decimal( $tax['shipping_tax_amount'], $dp ),
            );
            $data['tax_lines'][] = $tax_line;
        }
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
            $shipping_taxes = $shipping_item->get_taxes();
            if ( ! empty( $shipping_taxes['total'] ) ) {
                $shipping_line['total_tax'] = wc_format_decimal( array_sum( $shipping_taxes['total'] ), $dp );
                foreach ( $shipping_taxes['total'] as $tax_rate_id => $tax ) {
                    $shipping_line['taxes'][] = array(
                        'id'       => $tax_rate_id,
                        'total'    => $tax,
                    );
                }
            }
            $data['shipping_lines'][] = $shipping_line;
        }

        array_push($datas, $data);
    }

    return json_encode($datas);
}