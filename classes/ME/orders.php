<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once WC_ABSPATH.'/includes/wc-order-functions.php';
include_once plugin_dir_path(__FILE__).'quotation.php';
include_once plugin_dir_path(__FILE__).'tracking.php';
include_once plugin_dir_path(__FILE__).'args.php';
include_once plugin_dir_path(__FILE__).'docs.php';
include_once plugin_dir_path(__FILE__).'cart.php';
include_once plugin_dir_path(__FILE__).'quotation.php';
include_once plugin_dir_path(__FILE__).'apiMelhorEnvio.php';

function wpmelhorenvio_getJsonOrders() {
    
    // Criar os argumentos para fazer a busca dos pedidos.
    $args = wpmelhorenvio_mountArgsGetOrders($_POST);

    $orders = wc_get_orders($args);

    $shop_name = get_option('blogname');
    $agency = json_decode(str_replace("\\" ,"", get_option('wpmelhorenvio_address')));

    $dp    = is_null( null ) ? wc_get_price_decimals() : 2 ;
    $datas = array();
    
    foreach($orders as $order){

        $wcOrder = new WC_Order($order->get_id());
        $data = array(
            'id'                         => $order->get_id(),
            'order_id_me'                => end(get_post_meta($order->get_id(), 'wpmelhor_envio_pedido_id')),
            'tracking_mr'                => end(get_post_meta($order->get_id(), 'wpmelhorenvio_tracking_melhor_rastreio')),
            'number'                     => $wcOrder->get_order_number(),
            'currency'                   => $wcOrder->get_currency(),
            'price'                      => $wcOrder->get_total(),
            'date_modified'              => wc_rest_prepare_date_response( $wcOrder->get_date_modified() ), // v1 API used UTC.
            'customer_id'                => $wcOrder->get_customer_id(),
            'customer_email'             => $wcOrder->get_billing_email(),
            'customer_phone'             => $wcOrder->get_billing_phone(),
            'customer_document'          => $wcOrder->billing_cpf,
            'customer_company_document'  => $wcOrder->billing_cnpj,
            'customer_state_register'    => $wcOrder->billing_ie,
            'cotacoes'                   => array(),
            'shipping'                   => $wcOrder->get_address('shipping'),
            'customer_note'              => $wcOrder->get_customer_note(),
            'date_completed'             => wc_rest_prepare_date_response( $wcOrder->get_date_completed(), false ), // v1 API used local time.
            'date_paid'                  => wc_rest_prepare_date_response( $wcOrder->get_date_paid(), false ), // v1 API used local time.
            'cart_hash'                  => $wcOrder->get_cart_hash(),
            'line_items'                 => array(),
            'link_edit'                  => 'post.php?post=' . $order->get_id() . '&action=edit'
        );

        // Add addresses.
        $data['shipping'] = $order->get_address('shipping');

        // Add line items.
        if (!empty( $wcOrder->get_items())) {

            $line_item = [];
            foreach ( $wcOrder->get_items() as $item_id => $item ) {

                $product = wpmelhorenvio_getInfoProduct($item);
                
                if (!$product) {
                    continue;
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

                $line_item[] = array(
                    'id'           => $item_id,
                    'name'         => $item['name'],
                    'sku'          => $product['product_sku'],
                    'product_id'   => $product['product_id'],
                    'height'       => $product['height'],
                    'width'        => $product['width'],
                    'length'       => $product['length'],
                    'weight'       => $product['weight'],
                    'variation_id' => $product['variation_id'],
                    'quantity'     => wc_stock_amount( $item['qty'] ),
                    'tax_class'    => ! empty( $item['tax_class'] ) ? $item['tax_class'] : '',
                    'price'        => wc_format_decimal( $order->get_item_total( $item, false, false ), $dp ),
                );

            }
            $data['line_items'] = $line_item;
        }

        // Add shipping.
        foreach ( $wcOrder->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
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
       
        // Aqui pega a cotação na listagem de pedidos do admin.
        $data['cotacoes'] = wpmelhorenvio_getQuotation($order->get_id());
        
        // Aqui adiciona os documentos (NF, chave NF, IE, CNPJ/CPF)
        $data['docs'] = wpmelhorenvio_getDocsOrder($order->get_id());

        // Aqui pega o nome da Loja para colocar no pedido
        $data['shop_name'] = $shop_name;
        
        // Aqui pega informações do agencia para colocar no pedido
        $data['agency'] = $agency;
        
        // Adiciona cada pedido no array de pedidos que é enviado para a view.
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

function wpmelhorenvio_getStatusTags() {

    return [
        'cart' => 'No carrinho',
        'paid' => 'Pago',
        'pending' => 'Aguardando',
        'waiting' => 'Aguardando aprovação de pagamento',
        'printed' => 'Impresso'
    ];
}

function wpmelhorenvio_getHumanTitle($word, $type) {

    $me =  [
        'cart' => 'No carrinho',
        'paid' => 'Pago',
        'pending' => 'Aguardando',
        'waiting' => 'Aguardando aprovação de pagamento',
        'printed' => 'Impresso'
    ];

    $wc = wc_get_order_statuses();

    if ($type == 'wc') { return $wc['wc-' . $word]; }
    if ($type == 'me') {  return $me[$word]; }

    return '';
}

function wpmelhorenvio_getLineItems($orderWc) {

    $line_item = [];
    if (!empty( $orderWc->get_items())) {

        foreach ( $orderWc->get_items() as $item_id => $item ) {
            $product = wpmelhorenvio_getInfoProduct($item);

            if (!$product) {
                continue;
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

            $line_item[] = array(
                'id'           => $item_id,
                'name'         => $item['name'],
                'sku'          => $product['product_sku'],
                'product_id'   => $product['product_id'],
                'height'       => $product['height'],
                'width'        => $product['width'],
                'length'       => $product['length'],
                'weight'       => $product['weight'],
                'variation_id' => $product['variation_id'],
                'quantity'     => wc_stock_amount( $item['qty'] ),
                'tax_class'    => ! empty( $item['tax_class'] ) ? $item['tax_class'] : '',
                'price'        => $product['price'],
            );
        }
    }

    return $line_item;
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function wpmelhorenvio_getBlogName() {
    return get_option('blogname');
}

function wpmelhorenvio_getAgency() {
    return  htmlspecialchars( str_replace("\\" ,"",  get_option('wpmelhorenvio_address')));
}

function wpmelhorenvio_normalizeData($data) {

    global $woocommerce;
   
    $chose = explode('_', $woocommerce->session->chosen_shipping_methods[0]);
    $chose = end($chose);

    if ($chose == 'Rodoviário') {
        $chose = 'Jamef';
    }

    $arrQuotation = json_decode($data);
    
    foreach (json_decode($data) as $index => $item) {

        if ($chose == 'Jamef' && $item->name == 'Rodoviário') {
            $arrQuotation[$index]->selected = true;
            $arrQuotation[$index]->name = 'Rodoviario';
        }

        if ($item->name == $chose) {
            $arrQuotation[$index]->selected = true;
        }
    }

    return json_encode($arrQuotation);
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
        'timeout'=>10
    );


    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/cart',$params);
    $order = json_decode($response['body']);

    add_post_meta($_POST['id'], 'wpmelhor_envio_pedido_id', $order->id);
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

    $json_object = json_encode($object);
    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'body' => $json_object,
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
    $return->document = str_replace('-', '',get_option('wpmelhorenvio_document'));
    $return->company_document = str_replace('-', '', sanitize_key($_POST['company_document']));
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
    $return->document = str_replace('-', '', sanitize_key($_POST['to_document']));
    $return->company_document = str_replace('-', '', sanitize_key($_POST['to_company_document']));
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

// No futuro, aplicar no calculo de volume pela API da melhor envio
// Rever
function wpmelhorenvio_getObjectPackage(){

    $return = new stdClass();
    $volume =0;
    $weight =0;
    $weightTotal =0;
    $total  =0;
    $pacote = new stdClass();

    $products = json_decode(str_replace("\\" ,"", $_POST['line_items']));

    if(is_array($products)){

        foreach ( $products as $item){

            $weightTotal = $weightTotal +  floatval(sanitize_text_field($item->weight)  * sanitize_text_field($item->quantity));

            $width =  sanitize_text_field($item->width);
            $height = sanitize_text_field($item->height);
            $length = sanitize_text_field($item->length);
            $weight = sanitize_text_field($item->weight)  * sanitize_text_field($item->quantity);
            $valor = wc_get_product($item->product_id)->get_price() * sanitize_text_field($item->quantity);
            $volume  = $volume +  (int) ($width * $length * $height)  * sanitize_text_field($item->quantity);
            $total += $valor ;
        }
    }

    $side   =  ceil(pow($volume,1/3));
    $return->width =  $side > 12 ? $side : 12;
    $return->height = $side > 4 ? $side : 4;
    $return->length = $side > 17 ? $side : 17;
    $return->weight = $weightTotal;

    return $return;
}

function wpmelhorenvio_getObjectOptions(){

    $options = wpmelhorenvio_getPostOptionalsByService($_POST['service_id']);
    $return = new stdClass();

    if($_POST['service_id'] > 2){
        $return->insurance_value = wpmelhorenvio_getPriceProductsOrder($_POST['line_items']);
    }

    if($_POST['service_id'] <= 2 && $options->VD){
        $return->insurance_value = wpmelhorenvio_getPriceProductsOrder($_POST['line_items']);
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

function wpmelhorenvio_getPriceProductsOrder($products) {

    $products = json_decode(str_replace("\\" ,"", $products));

    $valueTotal = 0;
    foreach ($products as $item) {

        $price = floatval($item->price) * $item->quantity;
        $valueTotal = $valueTotal + $price;
    }
    return $valueTotal;
} 

function wpmelhorenvio_addTrackingAPI(){
    $order_id = sanitize_key($_POST['order_id']);
    $tracking = sanitize_key($_POST['tracking']);
    $service  = sanitize_key($_POST['service']);
    echo json_encode(wpmelhorenvio_data_insertTracking($order_id, $tracking,$service));
}

function wpmelhorenvio_updateTrackingData(){
    $tracking_code = sanitize_key($_POST['tracking_code']);
    $order_id = sanitize_text_field($_POST['order_id']);
    $status = sanitize_text_field($_POST['status']);

    echo json_encode(wpmelhorenvio_data_updateTracking($order_id, $tracking_code,$status));
}

function wpmelhorenvio_getTrackingsData(){
    $order_id = sanitize_key($_POST['order_id']);
    echo json_encode(wpmelhorenvio_data_getTracking($order_id));
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
            'Content-Type'  => '    ',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'body'  => $body,
        'timeout'=>10);

    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/tracking',$params);
    echo json_encode($response);

}

function wpmelhorenvio_getStatusOrder($trackings){

    if(empty($trackings)) {
        return [];
    }
    
    $body = new stdClass();
    $tracking_codes = array();
    if(is_array($trackings)){
        foreach ($trackings as $tracking_code){
            array_push($tracking_codes,sanitize_key($tracking_code));
        }
    }

    $body->orders = $tracking_codes;
    $token = get_option('wpmelhorenvio_token');
    $client = new WP_Http();
    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'body'  => json_encode($body),
        'timeout'=>10);

        
    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/tracking',$params);

    if (is_wp_error($response)) {
        return [];
    }

    $orders = json_decode($response['body']);

    if (!is_object($orders)) {
        return [];
    }

    $result = [];
    foreach  ($orders as $id => $item) {
        $result[$id] = $item->status;
    }

    return $result;
}

function wpmelhorenvio_getTrackingApiMR() {

    if (!$_POST['tracking'] || !$_POST['order_id']) {
        return 0;
    }

    $token = get_option('wpmelhorenvio_token');
    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'timeout'=>10
    );

    $client = new WP_Http();
    $resp = $client->get('https://www.melhorenvio.com.br/api/v2/me/orders/search?q=' . $_POST['tracking'], $params);
    $data = end(json_decode($resp['body']));

    if (is_null($data->tracking)) {
        $return = [
            'error' => true,
            'message' => 'Etiqueta ainda não postada, tente novamente mais tarde'
        ];

        return json_encode($return);
    }

    add_post_meta($_POST['order_id'], 'wpmelhorenvio_getAllInfoTrackings', $data->tracking, true);
    return $data->tracking;
}

function wpmelhorenvio_getTrackingApiMRClient($order_id, $tracking) {

    $token = get_option('wpmelhorenvio_token');
    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'timeout'=>100
    );

    $client = new WP_Http();
    $resp = $client->get('https://www.melhorenvio.com.br/api/v2/me/orders/search?q=' . $tracking, $params);

    if ($resp instanceof WP_Error) {
        return 'not found';
    }

    $data = end(json_decode($resp['body']));
    if (is_null($data->tracking)) {
        return 'not found';
    }

    add_post_meta($order_id, 'wpmelhorenvio_tracking_melhor_rastreio', $data->tracking, true);
    return $data->tracking;
}

function wpmelhorenvio_getBalanceAPI(){
    $token = get_option('wpmelhorenvio_token');
    $params = array('headers'=>[
        'Content-Type' => 'application/json',
        'Accept'=>'application/json',
        'Authorization' => 'Bearer '.$token],
    );
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
    $params = array('headers'=>[
        'Content-Type' => 'application/json',
        'Accept'=>'application/json',
        'Authorization' => 'Bearer '.$token
        ],'body'  => $json_object,
    );
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
        $tracking = sanitize_key($_POST['tracking']);


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

    $products = json_decode(str_replace("\\" ,"", $_POST['line_items']));
    
    foreach ($products as $line_item){
        $item = new stdClass();
        $item->name = sanitize_text_field($line_item->name);
        $item->quantity = (int) sanitize_text_field($line_item->quantity);
        $item->unitary_value =  (float) sanitize_text_field($line_item->price);
        $item->weight = (float) sanitize_text_field($line_item->weight); //REVER
        array_push($items, $item);
    }

    return $items;
}

function addOrUpdateQuotation() {

    global $woocommerce;

    if($woocommerce->session == null) {
        return null;
    }
    
    $data = $woocommerce->session->get('shipping_for_package_0')['rates'];

    foreach ($data as $key => $item) {
        $data[$key]->code = getCodeServiceByMethodId($item->get_id());
    }
    
    if (!isset($woocommerce->session->order_awaiting_payment) || $woocommerce->session->order_awaiting_payment == NULL) {
        return false;
    }

    $post_meta = get_post_meta($woocommerce->session->order_awaiting_payment, 'quotation_estimate');
    if (!empty($post_meta)) {
        update_post_meta($woocommerce->session->order_awaiting_payment, 'quotation_estimate', $data, true);
    } else {
        add_post_meta($woocommerce->session->order_awaiting_payment, 'quotation_estimate', $data, true);
    }

    melhorenvio_saveShipmentChoose();
}   

function melhorenvio_saveShipmentChoose() {
    global $woocommerce;

    $choose = end($woocommerce->session->get('chosen_shipping_methods'));
    $choose = getCodeServiceByMethodId($choose);

    $post_meta = get_post_meta($woocommerce->session->order_awaiting_payment, 'quotation_choose');
    if (!empty($post_meta)) {
        update_post_meta($woocommerce->session->order_awaiting_payment, 'quotation_choose', $choose, true);
    } else {
        add_post_meta($woocommerce->session->order_awaiting_payment, 'quotation_choose', $choose, true);
    }
}

function getIdServices() {

    $client = new WP_Http();
    $companies = $client->get('https://www.melhorenvio.com.br/api/v2/me/shipment/companies?pretty');
    
    $companiesApi = json_decode($companies['body']);
    $companiesServices = [];
    foreach ($companiesApi as $item) {
        foreach ($item->services as $service) {
            if ($service->name == 'Rodoviário') {
                $companiesServices[$service->id] = 'Jamef';
            } else  {
                $companiesServices[$service->id] = $service->name;
            }
        }
    }

    return $companiesServices;
}

function calculateQuotationCart() {

    global $woocommerce;
    return $woocommerce->session->get('shipping_for_package_0')['rates'];
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
