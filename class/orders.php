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
        'id'                             => $order->get_id(),
            'number'                     => $order->get_order_number(),
            'currency'                   => $order->get_currency(),
            'price'                      => $order->get_total(),
            'date_modified'              => wc_rest_prepare_date_response( $order->get_date_modified() ), // v1 API used UTC.
            'customer_id'                => $order->get_customer_id(),
            'customer_email'             => $order->get_billing_email(),
            'customer_phone'             => $order->get_billing_phone(),
            'customer_document'          => $order->billing_cpf,
            'customer_company_document'  => $order->billing_cnpj,
            'customer_state_register'    => $order->billing_ie,
            'cotacoes'                   => array(),
            'shipping'                   => array(),
            'customer_note'              => $order->get_customer_note(),
            'date_completed'             => wc_rest_prepare_date_response( $order->get_date_completed(), false ), // v1 API used local time.
            'date_paid'                  => wc_rest_prepare_date_response( $order->get_date_paid(), false ), // v1 API used local time.
            'cart_hash'                  => $order->get_cart_hash(),
            'line_items'                 => array(),
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
                'weight'       =>  max($product_weight,1),
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
        $data['cotacoes'] = json_decode(wpme_getCustomerCotacaoAPI($data));
        array_push($datas, $data);
    }
    return json_encode($datas);
}

function wpme_buyShipment(){
    $shipment = new stdClass();
    $shipment->service = $_POST['service_id'];
    $shipment->from = wpme_getObjectFrom(); //semi-ok
    $shipment->to = wpme_getObjectTo(); //semi-ok
    $shipment->package = wpme_getObjectPackage();
//    $shipment->options = wpme_getObjectOptions();

    var_dump($shipment);

}

function wpme_getCustomerCotacaoAPI($order){
    $client = new WP_Http();

    $pacote = wpme_getPackageInternal($order);

    $cep_origin = wpme_getFrom();
    $token = get_option('wpme_token');
    $cep_destination = $order['shipping']['postcode'];
    $opcionais = wpme_getOptionals();
    $seguro = wpme_getValueInsurance($pacote->value,$opcionais->VD);
    $params = array(
        'headers'           =>  ['Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token],
        'body'  =>[
            'from'      => $cep_origin,
            'to'        => $cep_destination,
            'width'     => $pacote->width,
            'height'    => $pacote->height,
            'length'    => $pacote->length,
            'weight'    => $pacote->weight,
            'services'  => wpme_getSavedServices(),
            'receipt'   => $opcionais->AR,
            'own_hand'  => $opcionais->MP,
            'insurance_value' => $seguro
        ],
        'timeout'=>10);

    $response = $client->get("https://melhorenvio.com.br/api/v2/calculator",$params);
    return is_array($response) ?  $response['body'] : [];
}


function wpme_getPackageInternal($package){
    $volume =0;
    $weight =0;
    $total  =0;
    $pacote = new stdClass();
    foreach ($package['line_items'] as $item){
        $width = $item['width'];
        $height = $item['height'];
        $length = $item['length'];
        $weight = $item['weight']  * $item['quantity'];
        $valor = wc_get_product($item['product_id'])->get_price() * $item['quantity'];
        $volume  = $volume +  (int) ($width * $length * $height) * $item['quantity'];
        $total += $valor ;
    }
    $side   =  ceil(pow($volume,1/3));
    $pacote->width  = $side >= 12  ? $side : 12;
    $pacote->height = $side >= 4   ? $side : 4;
    $pacote->length = $side >= 17  ? $side : 17;
    $pacote->weight = $weight;
    $pacote->value = $valor;
    return $pacote;
}


function wpme_ticketAcquirementAPI(){
     wpme_buyShipment();

//    $client = new WP_Http();
//
//
//    $params = array(
//        'headers'           =>  ['Content-Type'  => 'application/json',
//            'Accept'        =>  'application/json',
//            'Authorization' =>  'Bearer '.$token],
//        'body'  =>[
//            'from'      => $object_from,
//            'to'        => $object_to,
//            'package'   => $object_package,
//            'options'   => $object_options,
//            'coupon'    => ''
//        ],
//        'timeout'=>10);
//    $response = $client->post('https://melhorenvio.com.br/api/v2/me/cart');
}


function wpme_getObjectFrom(){
    $from = wpme_getFrom();
    $address = json_decode(get_option('wpme_address'));
    $return = new stdClass();
    $return->name = $_POST['from_name'];
    $return->phone = get_option('wpme_phone');
    $return->email = get_option('wpme_email');
    $return->document = get_option('wpme_document');
    $return->company_document = '';// Falta aqui
    $return->state_register = '';// Falta // Falta aquiaqui
    $return->address = $address->address;
    $return->complement = ''; $address->complement;
    $return->number = $address->number;
    $return->district = $address->district;
    $return->city = $address->city->city;
    $return->state_abbr = $address->state->state_abbr;
    $return->country_id = $address->country->id;
    $return->postal_code = $address->postal_code;
    $return->note = '';

    return $return;
}

function wpme_getObjectTo(){
    $return = new stdClass();
    $return->name = $_POST['to_name'];
    $return->phone = $_POST['to_phone'];
    $return->email = $_POST['to_email'];
    $return->document = $_POST['to_document'];
    $return->company_document = $_POST['to_company_document'];
    $return->state_register = $_POST['to_state_register'];
    $return->address = $_POST['to_address'];
    $return->complement = $_POST['to_complement'];
    $return->number = $_POST['to_number'];
    $return->district = $_POST['to_district'];
    $return->city = $_POST['to_city'];
    $return->state_abbr = $_POST['to_state_abbr'];
    $return->country_id = $_POST['to_country_id'];
    $return->postal_code = $_POST['to_postal_code'];
    $return->note = '';

    return $return;
}

function wpme_getObjectPackage(){
    $return = new stdClass();

    $volume =0;
    $weight =0;
    $total  =0;
    $pacote = new stdClass();
    foreach ($_POST['line_items'] as $item){
        $width = $_POST['width'];
        $height = $_POST['height'];
        $length = $_POST['length'];
        $weight = $_POST['weight']  * $_POST['quantity'];
        $valor = wc_get_product($item['product_id'])->get_price() * $item['quantity'];
        $volume  = $volume +  (int) ($width * $length * $height) * $item['quantity'];
        $total += $valor;
    }
    $side   =  ceil(pow($volume,1/3));
    $return->width =  $side > 12 ? $side : 12;
    $return->height = $side > 4 ? $side : 4;
    $return->length = $side > 17 ? $side : 17;

    return $return;
}

function wpme_getObjectOptions(){

    $options = wpme_getOptionals();
    $return = new stdClass();
    if($options->VD){
        $return->insurance_value = $_POST['valor_declarado'];
    }else{
        $return->insurance_value = '';
    }
    $return->receipt = $options->AR;
    $return->own_hand = $options->MP;
    $return->collect = false;
    $return->reverse = false;
    $return->non_commercial = true; //rever
    $return->invoice = new stdClass();
        $return->invoice->number = ''; //rever
        $return->invoice->key = ''; //rever
    $return->reminder = ''; //rever
}



function wpme_ticketPrintingAPI(){

}

function wpme_getBalanceAPI(){
    $token = get_option('wpme_token');
    $params = array('headers'=>['Content-Type' => 'application/json','Accept'=>'application/json','Authorization' => 'Bearer '.$token]);
    $client = new WP_Http();
    $response = $client->get('https://melhorenvio.com.br/api/v2/me/balance',$params);
    if( $response instanceof WP_Error){
        return false;
    }else{
        return $response['body'];
    }
}

function wpme_getCustomerInfoAPI(){

    $token = get_option('wpme_token');
    $params = array('headers'=>['Content-Type' => 'application/json','Accept'=>'application/json','Authorization' => 'Bearer '.$token]);
    $client = new WP_Http();
    $response = $client->get('https://melhorenvio.com.br/api/v2/me',$params);
    if( $response instanceof WP_Error){
        return false;
    }else{
        return $response['body'];
    }
}