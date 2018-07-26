<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once WC_ABSPATH.'/includes/wc-order-functions.php';

function wpmelhorenvio_getPackageInternal($package){

    $pacote = new stdClass();
    $result = wpmelhorenvio_getProducts($package['line_items']);
    
    $token = get_option('wpmelhorenvio_token');
    $client = new WP_Http();

    $body = [
        "from" => [
            "postal_code" => "96020360",
            'address'     => 'Endereço do remetente',
            'number'      => '1'
        ],
        'to' => [
            'postal_code' => '96065710',
            'address'     => 'Endereço do destinatario',
            'number'      => '2'
        ],
        'products' => $result['products'],
        'options' => [
            "insurance_value" => 0,
            "receipt"         => false, 
            "own_hand"        => false, 
            "collect"         => false 
        ],
        "services" => "1,2" 
    ];

    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'body'  => json_encode($body),
        'timeout'=>10);

    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/calculate',$params);
    $resposta = json_decode($response['body']);

    $pacote->width  = $resposta[0]->packages[0]->dimensions->width >= 12  ? $resposta[0]->packages[0]->dimensions->width : 12;
    $pacote->height = $resposta[0]->packages[0]->dimensions->height >= 4   ? $resposta[0]->packages[0]->dimensions->height : 4;
    $pacote->length = $resposta[0]->packages[0]->dimensions->length >= 17  ? $resposta[0]->packages[0]->dimensions->length : 17;
    $pacote->weight = $weightTotal;
    $pacote->value = $valor;

    return $pacote;
}

function wpmelhorenvio_getCustomerCotacaoAPI($order){

    $quotation = get_post_meta($order['id'], 'quotation_estimate');

    if (!empty($quotation)) {  
        return $quotation[0];
    }
    
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
            'Authorization' =>  'Bearer '.$token
        ],
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

    add_post_meta($order['id'], 'quotation_estimate', wpmelhorenvio_normalizeData($response['body']));

    return is_array($response) ?  $response['body'] : [];
}