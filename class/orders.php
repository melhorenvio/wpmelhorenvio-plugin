<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Created by PhpStorm.
 * User: VHSoa
 * Date: 04/12/2017
 * Time: 15:30
 */

include_once WC_ABSPATH.'/includes/wc-order-functions.php';
include_once plugin_dir_path(__FILE__).'quotation.php';
include_once plugin_dir_path(__FILE__).'tracking.php';

function wpmelhorenvio_getJsonOrders(){
    $orders = wc_get_orders(['limit' => '600']);
    $dp    = is_null( null ) ? wc_get_price_decimals() : 2 ;
    $datas = array();
    foreach($orders as $order){
        $data = array(
            'id'                         => $order->get_id(),
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
        $data['cotacoes'] = json_decode(wpmelhorenvio_getCustomerCotacaoAPI($data));
        array_push($datas, $data);
    }
    return json_encode($datas);
}

function wpmelhorenvio_buyShipment(){
    $shipment = new stdClass();

    if( isset($_POST['agency'])){
        $shipment->agency = (int) sanitize_text_field($_POST['agency']);
    }


    $shipment->service = (int) sanitize_text_field($_POST['service_id']);
    $shipment->from = wpmelhorenvio_getObjectFrom(); //semi-ok
    $shipment->to = wpmelhorenvio_getObjectTo(); //semi-ok
    $shipment->package = wpmelhorenvio_getObjectPackage();
    $shipment->options = wpmelhorenvio_getObjectOptions();
    $shipment->products = wpmelhorenvio_makeItemDeclaration();
    return $shipment;

}

function wpmelhorenvio_getFinalCotacao(){
    $pedido_id = sanitize_key($_POST['pedido_id']);
    $order = wc_get_order($pedido_id);
    echo wpmelhorenvio_getCustomerCotacaoAPI($order);
}

function wpmelhorenvio_getCustomerCotacaoAPI($order){
    $client = new WP_Http();

    $pacote = wpmelhorenvio_getPackageInternal($order);
    $cep_origin = wpmelhorenvio_getFrom();
    $token = get_option('wpmelhorenvio_token');
    $cep_destination = $order['shipping']['postcode'];
    $opcionais = wpmelhorenvio_getOptionals();
    $seguro = wpmelhorenvio_getValueInsurance($pacote->value,$opcionais->VD);
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
            'services'  => wpmelhorenvio_getSavedServices(),
            'receipt'   => $opcionais->AR,
            'own_hand'  => $opcionais->MP,
            'insurance_value' => $seguro
        ],
        'timeout'=>10);
    $response = $client->get("https://www.melhorenvio.com.br/api/v2/calculator",$params);
    return is_array($response) ?  $response['body'] : [];
}

function wpmelhorenvio_getPackageInternal($package){
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

function wpmelhorenvio_ticketAcquirementAPI(){
    $object = wpmelhorenvio_buyShipment();
    $token = get_option('wpmelhorenvio_token');
    $client = new WP_Http();
    $json_object = json_encode($object);
    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'body'  => $json_object,
        'timeout'=>10);
    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/cart',$params);
    return $response['body'];
}

function wpmelhorenvio_payTicket(){
    $client = new WP_Http();
    $token = get_option('wpmelhorenvio_token');

    $object = new stdClass();
    $object->orders     = $_POST['orders'];

    if($_POST['gateway'] != "99"){
        $object->gateway =  sanitize_key($_POST['gateway']);
    }

//  $object->redirect   = $_POST['redirect'];
    $json_object = json_encode($object);
    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'body'  => $json_object,
        'timeout'=>10);

    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/checkout',$params);
    echo $response['body'];

}

function wpmelhorenvio_cancelTicketAPI(){
    $trk = wp_filter_nohtml_kses(sanitize_text_field($_POST['tracking']));
    $client = new WP_Http();
    $token = get_option('wpmelhorenvio_token');

    $object[0] = new stdClass();
    $object[0]->id = $trk;
    $object[0]->reason_id = 2;
    $object[0]->description = 'Cancelado via Plugin';

    $json_object = json_encode($object);
    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'body'  => $json_object,
        'timeout'=>10);
    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/cancel',$params);
    return $response['body'];
}

function wpmelhorenvio_getObjectFrom(){
    $from = wpmelhorenvio_getFrom();
    $address = json_decode(str_replace("\\" ,"", get_option('wpmelhorenvio_address')));
    $return = new stdClass();
    $return->name = sanitize_text_field($_POST['from_name']);
    $return->phone = get_option('wpmelhorenvio_phone');
    $return->email = sanitize_email(get_option('wpmelhorenvio_email'));
    $return->document = get_option('wpmelhorenvio_document');
    $return->company_document = sanitize_key($_POST['company_document']);
    $return->state_register = sanitize_key($_POST['company_state_register']);
    $return->address = $address->address;
    $return->complement = ''; $address->complement;
    $return->number = $address->number;
    $return->district = $address->district;
    $return->city = $address->city->city;
    $return->state_abbr = $address->city->state->state_abbr;
    $return->country_id = 'BR';
    $return->postal_code = $address->postal_code;
    $return->note = '';

    return $return;
}

function wpmelhorenvio_getObjectTo(){
    $return = new stdClass();
    $return->name = sanitize_text_field($_POST['to_name']);
    $return->phone = str_replace("-","",str_replace(")","",str_replace("(","",$_POST['to_phone'])));
    $return->email = sanitize_email($_POST['to_email']);
    $return->document = sanitize_key($_POST['to_document']);
    $return->company_document = sanitize_key($_POST['to_company_document']);
    $return->state_register = sanitize_key($_POST['to_state_register']);
    $return->address = sanitize_text_field($_POST['to_address']);
    $return->complement =  sanitize_text_field($_POST['to_complement']);
    $return->number = sanitize_key($_POST['to_number']);
    $return->district = sanitize_text_field($_POST['to_district']);
    $return->city = sanitize_text_field($_POST['to_city']);
    $return->state_abbr = sanitize_text_field($_POST['to_state_abbr']);
    $return->country_id = sanitize_key($_POST['to_country_id']);
    $return->postal_code = sanitize_key($_POST['to_postal_code']);
    $return->note = '';

    return $return;
}

function wpmelhorenvio_getObjectPackage(){
    $return = new stdClass();
    $volume =0;
    $weight =0;
    $total  =0;
    $pacote = new stdClass();
    if(is_array($_POST['line_items'])){
        foreach ( $_POST['line_items'] as $item){
            $width =  sanitize_text_field($item['width']);
            $height = sanitize_text_field($item['height']);
            $length = sanitize_text_field($item['length']);
            $weight = sanitize_text_field($item['weight'])  * sanitize_text_field($item['quantity']);
            $valor = wc_get_product($item['product_id'])->get_price() * sanitize_text_field($item['quantity']);
            $volume  = $volume +  (int) ($width * $length * $height)  * sanitize_text_field($item['quantity']);
            $total += $valor ;
        }
    }
    $side   =  ceil(pow($volume,1/3));
    $return->width =  $side > 12 ? $side : 12;
    $return->height = $side > 4 ? $side : 4;
    $return->length = $side > 17 ? $side : 17;
    $return->weight = $weight > 1 ? $weight :1;

    return $return;
}

function wpmelhorenvio_getObjectOptions(){
    $options = wpmelhorenvio_getPostOptionals();
    $return = new stdClass();
    if($options->VD){
        $return->insurance_value = wpmelhorenvio_getValueInsurance(wpmelhorenvio_getPackageInternal($_POST)->value,$options->VD);
    }
    $return->receipt = $options->AR;
    $return->own_hand = $options->MP;
    $return->collect = false;
    $return->reverse = false;
    $return->non_commercial = true; //rever
    $return->invoice = new stdClass();
    if($_POST['nf'] != null){
        $return->invoice->number = sanitize_key($_POST['nf']); //rever
        $return->invoice->key = sanitize_key($_POST['key_nf']); //rever
    }
    $return->reminder = ''; //rever
    $return->platform= "WooCommerce";
    return $return;
}

function wpmelhorenvio_addTrackingAPI(){
    $order_id = sanitize_key($_POST['order_id']);
    $tracking = sanitize_key($_POST['tracking']);
    $service  = sanitize_key($_POST['service']);
    echo json_encode(wpmelhorenvio_data_insertTracking($order_id, $tracking,$service));
}

function wpmelhorenvio_updateTrackingData(){
    $tracking_code = sanitize_key($_POST['tracking_code']);
    $status = sanitize_text_field($_POST['status']);
    echo json_encode(wpmelhorenvio_data_updateTracking($tracking_code,$status));
}

function wpmelhorenvio_getTrackingsData(){
    $order_id = sanitize_key($_POST['order_id']);
    echo json_encode(wpmelhorenvio_data_getTracking($order_id));
//    var_dump(wpmelhorenvio_data_getTracking($order_id));
}

function wpmelhorenvio_ticketPrintingAPI(){
    $trk = array();
    if(is_array($_POST['tracking']) && count($_POST['tracking']) < 11) {
        foreach ($_POST['tracking'] as $tracking_code){
            $tracking_code = sanitize_key($tracking_code);
            array_push($trk,$tracking_code);
        }
    }
    $client = new WP_Http();
    $token = get_option('wpmelhorenvio_token');
    $object = new stdClass();
    $object->orders = $trk;

    $json_object = json_encode($object);
    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'body'  => $json_object,
        'timeout'=>10);
    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/preview?pretty',$params);
    return $response['body'];

}

function wpmelhorenvio_getTrackingAPI(){
    $body = new stdClass();
    $tracking_codes = array();
    if(is_array($_POST['tracking_codes'])){
        foreach ($_POST['tracking_codes'] as $tracking_code){
            array_push($tracking_codes,sanitize_key($tracking_code));
        }
    }

    $body->orders = json_encode($tracking_codes);
    $token = get_option('wpmelhorenvio_token');
    $client = new WP_Http();
    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'body'  => $body,
        'timeout'=>10);
    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/tracking',$params);
    echo json_encode($response);

}

function wpmelhorenvio_getBalanceAPI(){
    $token = get_option('wpmelhorenvio_token');
    $params = array('headers'=>['Content-Type' => 'application/json','Accept'=>'application/json','Authorization' => 'Bearer '.$token]);
    $client = new WP_Http();
    $response = $client->get('https://www.melhorenvio.com.br/api/v2/me/balance',$params);
    if( $response instanceof WP_Error){
        return false;
    }else{
        return $response['body'];
    }
}

function wpmelhorenvio_getLimitsAPI(){
    $token = get_option('wpmelhorenvio_token');
    $params = array('headers'=>['Content-Type' => 'application/json','Accept'=>'application/json','Authorization' => 'Bearer '.$token]);
    $client = new WP_Http();
    $response = $client->get('https://www.melhorenvio.com.br/api/v2/me/limits',$params);
    if( $response instanceof WP_Error){
        return false;
    }else{
        return $response['body'];
    }
}

function wpmelhorenvio_getCustomerInfoAPI(){
    $customer = new stdClass();
    $customer->firstname = get_option("wpmelhorenvio_firstname");
    $customer->lastname = get_option("wpmelhorenvio_lastname");
    $customer->thumbnail = get_option("wpmelhorenvio_picture");

    echo json_encode($customer);

}

function wpmelhorenvio_cancelTicketData(){
        $trk = sanitize_key($_POST['tracking']);
        return wpmelhorenvio_data_deleteTracking($trk);
}

function wpmelhorenvio_removeFromCart()
{
    $curl = curl_init();
    $token = get_option('wpmelhorenvio_token');
    $tracking = '';
    if(is_array($_POST['tracking'])){
        $tracking = sanitize_key($_POST['tracking'][0]);
    }

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.melhorenvio.com.br/api/v2/me/cart/" . $tracking,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "DELETE",
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Bearer " . $token,
            "cache-control: no-cache",
            "postman-token: a3c50b4f-eea7-b391-9acb-cf7780a53983"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        wpmelhorenvio_data_deleteTracking($tracking);
        return '{"succcess":true}';
    }
}

function wpmelhorenvio_makeItemDeclaration(){
    $items = array();
    foreach ($_POST['line_items'] as $line_item){
        $item = new stdClass();
        $item->name = sanitize_text_field($line_item['name']);
        $item->quantity = (int) sanitize_text_field($line_item['quantity']);
        $item->unitary_value =  (float) sanitize_text_field($line_item['price']);
        $item->weight = (float) sanitize_text_field($line_item['weight']);
        array_push($items, $item);
    }
    return $items;
}

function wpmelhorenvio_updateStatusTracking(){
    $trackings = wpmelhorenvio_data_getAllTrackings();

    $update_request = array();

    foreach ($trackings as $tracking){
        array_push($update_request,$tracking->tracking_id);
    }
    $object = new stdClass();
    $object->orders = $update_request;
    $token = get_option('wpmelhorenvio_token');
    $params = array(
        'headers'=>
            [
                'Content-Type' => 'application/json',
                'Accept'=>'application/json',
                'Authorization' => 'Bearer '.$token
            ],
        'body' => json_encode($object)
    );
    $client = new WP_Http();
    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/tracking',$params);
    if( $response instanceof WP_Error){
        return false;
    }else{
        $resposta = json_decode($response['body']);
        foreach ($resposta as $index => $rastreio){
            if($rastreio->status != 'pending'){
                if($rastreio->status == 'released' || $rastreio->status == 'delivered'){
                    wpmelhorenvio_data_updateTracking($index,'paid');
                }else{
                    if($rastreio->status == 'canceled' ){
                        wpmelhorenvio_data_deleteTracking($index);
                    }
                }
            }
        }
        echo $response['body'];
    }
}
