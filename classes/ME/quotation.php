<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include_once WC_ABSPATH.'/includes/wc-order-functions.php';
// include_once plugin_dir_path(__FILE__).'packages.php';

function wpmelhorenvio_getQuotation($order_id) {

    $quotation = wpmelhorenvio_getQuotationDataBase($order_id);

    //Check if exists quotation o database
    if ($quotation != null) {
        return $quotation;
    }

    return wpmelhorenvio_makeQuotation($order_id);
}

function wpmelhorenvio_getQuotationDataBase($order_id) {

    $data = get_post_meta($order_id, 'quotation_estimate');

    if (empty($data)) {
        return null;
    }

    $choosenData = end(get_post_meta($order_id, 'quotation_choose'));

    $response = wpmelhorenvio_extractDataObject(end($data), $choosenData);

    return $response;
}

function wpmelhorenvio_extractDataObject($data, $choosenData) {

    $response = [];

    if (empty($data) ||  is_null($data) || is_string($data)) {
        return $response;
    }

    foreach ($data as $key => $item) {

        if(!is_array($item)){

            $selected = false;
            if (isset($choosenData) && is_numeric($choosenData) && $choosenData != 0 && $choosenData == $item->code) {
                $selected = true;
            }

            if(strpos($item->get_id(), 'wpmelhorenvio') != 0) {
                continue;
            }

            if(empty($item->code)) {
                continue;
            }

            $response[] = [
                'id'            => $item->code,
                'name'          => $item->label,
                'price'         => $item->cost,
                'method_id'     => $item->method_id,
                'instance_id'   => $item->instance_id,
                'selected'      => $selected,
                'delivery_time' => $item->get_meta_data()['delivery_time'],
                'currency'      => 'R$',
                'taxe_extra'    => get_price_extra($item->code),
                'company' => [
                    'name' => $item->get_meta_data()['company']
                ]
            ];
            
        } else {
            $selected = false;
            if (isset($choosenData) && is_numeric($choosenData) && $choosenData != 0 && $choosenData == $item['id']) {
                $selected = true;
            }

            if(strpos($item['method_id'], 'wpmelhorenvio') != 0) {
                continue;
            }

            if(empty($item['id'])) {
                continue;
            }

            $response[] = [
                'id'            => $item['id'],
                'name'          => $item['name'],
                'price'         => $item['price'],
                'method_id'     => $item['method_id'],
                'selected'      => $selected,
                'delivery_time' => $item['delivery_time'],
                'currency'      => 'R$',
                'company' => [
                    'name' => $item['company']['name']
                ]
            ];
        }
    }
    
    return $response;
}

function wpmelhorenvio_makeQuotation($order_id) {

    $shipments = get_shipping_melhorenvio();
    $data = getProductsByOrder($order_id);
    $data['services'] = contactStringCodesServices($shipments);
    $quotation = wpmelhorenviopackage_getPackageApi($data);
    $response = [];
    foreach($quotation as $item_id => $shipping_item_obj ){
        
        $selected = false;
        if (isset($data['method_selected']) && $data['method_selected'] == getnameServiceByCode($shipping_item_obj->id) ) {
            $selected = true;
        }

        if ($shipping_item_obj->price <= 0) {
            continue;
        }

        $response[] = [
            'id'            => $shipping_item_obj->id,
            'name'          => $shipping_item_obj->name,
            'price'         => calcute_value_shipping_by_service($shipping_item_obj->price, $shipping_item_obj->id),
            'method_id'     => getnameServiceByCode($shipping_item_obj->id),
            'delivery_time' => calculte_delivery_time_by_service($shipping_item_obj->delivery_time, $shipping_item_obj->id),
            'selected'      => $selected,
            'currency'      => 'R$',
            'company' => [
                'name' => $shipping_item_obj->company->name
            ]
        ];
    }

    return $response;
}

function wpmelhorenvio_getFrom(){
    $remetente = json_decode(str_replace("\\" ,"", get_option('wpmelhorenvio_address')));
    $from = $remetente->postal_code;
    return $from;
}

function wpmelhorenvio_getPostOptionals(){
    $optionals = json_decode(get_option('wpmelhorenvio_pluginconfig'));
    $optionals->PL = is_numeric($optionals->PL)? $optionals->PL : 0;
    $optionals->DE = is_numeric($optionals->DE)? $optionals->DE : 0;
    return $optionals;
}

function wpmelhorenvio_getPostOptionalsByService($service_id){

    $prefix = wpmelhorenvio_getPrefixService($service_id);
    $pl = get_option($prefix. 'pl_custom_shipping');
    if (!$pl) {
        $pl = 0;
    }

    $de = get_option($prefix. 'days_extra_custom_shipping');
    if (!$de) {
        $de = 0;
    }

    $vd = get_option($prefix. 'vd_custom_shipping');
    if (!$vd || $vd == 'no') {
        $vd = false;
    }
    if ($vd == 'yes') {
        $vd = true;
    }

    $mp = get_option($prefix. 'mp_custom_shipping');
    if (!$mp || $mp == 'no') {
        $mp = false;
    }
    if ($mp == 'yes') {
        $mp = true;
    }

    $ar = get_option($prefix. 'ar_custom_shipping');
    if (!$ar || $ar == 'no') {
        $ar = false;
    }
    if ($ar == 'yes') {
        $ar = true;
    } 

    $ee = get_option($prefix. 'ee_custom_shipping');
    if (!$ee || $ee == 'no') {
        $ee = false;
    }
    if ($ee == 'yes') {
        $ee = true;
    }

    $optionals = new stdClass();
    $optionals->PL = $pl;
    $optionals->DE = $de;
    $optionals->VD = $vd;
    $optionals->MP = $mp;
    $optionals->AR = $ar;
    $optionals->EE = $ee;

    return $optionals;
}

function wpmelhorenvio_getValueInsurance($valor,$situacao){
    return $situacao ? $valor : 0;
}

function wpmelhorenvio_updateQuotationOrder($data) {

    $response = wpmelhorenvio_makeQuotation($data['id']); 

    $dataMeta = get_post_meta($data['id'], 'quotation_estimate');
  
    if (!$dataMeta) {
        add_post_meta($data['id'], 'quotation_estimate', $response, true);
    } else {
        $response = update_post_meta(intval($data['id']), 'quotation_estimate', $response);
    }
}

function wpmelhorenvio_saveQuotationDatabase($quotation, $order_id) {

    $dataMeta = get_post_meta($order_id, 'quotation_estimate');
    if (!$dataMeta) {
        add_post_meta($order_id, 'quotation_estimate', $quotation, true);
    } else {
       update_post_meta($order_id, 'quotation_estimate', $quotation);
    }
}

function get_shipping_melhorenvio()
{
    $shipping_methods = WC()->shipping->get_shipping_methods();
    foreach ($shipping_methods as $shipping_method){
        if (key($shipping_method->rates) == NULL || getCodeServiceByMethodId(key($shipping_method->rates)) == NULL) {
            continue;
        }
        $response[getCodeServiceByMethodId(key($shipping_method->rates))] = key($shipping_method->rates);
    }
    return $response;
}

function contactStringCodesServices($shipments) {
    $string = '';

    if (empty($shipments)) {
        return null;
    }

    foreach ($shipments as $id => $item) {
        $string = $string . $id .',';
    }
    return rtrim($string,",");
}

function wpmelhorenviopackage_getPackageApi($data){

    if (!isset($data['to']) || empty($data['to'])) {
        return null;
    }

    $token = get_option('wpmelhorenvio_token');
    $client = new WP_Http();

    $respostas = [];
    foreach(getServicesActive() as $code_service) {

        $ar = get_option_company($code_service, 'ar');
        $mp = get_option_company($code_service, 'mp');

        $body = [
            "from" => [
                "postal_code" => wpmelhorenvio_getFrom()
            ],
            'to' => [
                'postal_code' => $data['to']
            ],
            'products' => $data['products'],
            'options' => [
                "insurance_value" => $data['value'],
                "receipt"         => $ar, 
                "own_hand"        => $mp, 
                "collect"         => false 
            ],
            "services" => $code_service
        ];

        $params = array(
            'headers'           =>  [
                'Content-Type'  => 'application/json',
                'Accept'        =>  'application/json',
                'Authorization' =>  'Bearer '.$token
            ],
            'body'  => json_encode($body),
            'timeout'=>10
        );

        $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/calculate',$params);

        if ( is_wp_error( $response )) {
            continue;
        }

        $respostas[] = json_decode($response['body']);
    }



    return $respostas;
}

function getProductsByOrder($order_id) {

    global $woocommerce;

    $wcOrder = new WC_Order($order_id);
    $items = $wcOrder->get_items();

    $shipping_method = @array_shift($wcOrder->get_shipping_methods());
    $shipping_method_id = $shipping_method['method_id'];

    if (empty($items)) {
        return null;
    }

    $order_data = $wcOrder->get_data();

    $pacote = new stdClass();

    $result = wpmelhorenvio_getProducts($items);

    return [
        'products' => $result['products'],
        'value'    => $result['valor_total'],
        'to'       => $order_data['billing']['postcode'],
        'method_selected' => $shipping_method_id
    ]; 
}

function get_option_company($code, $option) {

    $prefix = getPrefixServiceByCode($code);
    $data = get_option('woocommerce_' . $prefix . '_' . $option . '_custom_shipping');

    if (!$data) {
        return false;
    }

    if ($data == 'yes') {
        return true;
    }

    return false;
}

function get_price_extra($code) {
    $prefix = getPrefixServiceByCode($code);
    $price = get_option('woocommerce_' . $prefix . '_pl_custom_shipping');

    if (!$price) {
        return 0;
    }
    return $price;
}

function calcute_value_shipping_by_service($price, $code) {
    
    $prefix = getPrefixServiceByCode($code);
    $price = floatval($price);
    $valueExtra = get_option('woocommerce_' . $prefix . '_pl_custom_shipping');

    $pos = strpos($valueExtra, '%');
    if ($pos) {
        $percent = ($price / 100 ) * floatval($valueExtra);
        $value =  $percent + $price;
        return round($value,2);
    }

    $valueExtra = floatval($valueExtra);
    $value = $price + $valueExtra;
    return round($value,2);
}

function calculte_delivery_time_by_service($delivery_time, $code) {

    $prefix = getPrefixServiceByCode($code);
    $days_extras = intval(get_option('woocommerce_' . $prefix . '_days_extra_custom_shipping'));	

    return $delivery_time + $days_extras;
}
