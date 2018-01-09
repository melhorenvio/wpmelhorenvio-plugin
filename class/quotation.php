<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Created by PhpStorm.
 * User: VHSoa
 * Date: 03/12/2017
 * Time: 23:33
 */
include_once ABSPATH.WPINC.'/option.php';
include_once ABSPATH.WPINC.'/class-requests.php';
//include_once ABSPATH . 'wp-content/plugins/woocommerce-extra-checkout-fields-for-brazil/includes/class-extra-checkout-fields-for-brazil-api.php';

function wpme_getCotacao($package){
    $client = new WP_Http();
    $token = get_option('wpme_token');
    $pacote =  wpme_getPackage($package);
    $opcionais = wpme_getOptionals();
    $seguro = wpme_getValueInsurance($pacote->value,$opcionais->VD);
    $params = array(
        'headers'=>['Content-Type'  => 'application/json',
            'Accept'        =>'application/json',
            'Authorization' => 'Bearer '.$token],
        'body'  =>['from'   => wpme_getFrom(),
            'to'        => wpme_getTo($package),
            'width'     => $pacote->width,
            'height'    => $pacote->height,
            'length'    => $pacote->length,
            'weight'    => $pacote->weight,
            'receipt'   => $opcionais->AR,
            'own_hand'  => $opcionais->MP,
            'insurance_value' => $seguro,
            'services'  => wpme_getSavedServices()
        ],
        'timeout'=>10);

    $response = $client->get("https://melhorenvio.com.br/api/v2/calculator",$params);
    return is_array($response) ?  json_decode($response['body']) : [];
}

function wpme_getPackage($package){
    $volume =0;
    $weight =0;
    $total  =0;
    $pacote = new stdClass();
    foreach ($package['contents'] as $item){
        $width = wc_get_product($item['product_id'])->get_width();
        $height = wc_get_product($item['product_id'])->get_height();
        $length = wc_get_product($item['product_id'])->get_length();
        $weight = $weight + wc_get_product($item['product_id'])->get_weight()  * $item['quantity'];
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

function wpme_getFrom(){
    $remetente = json_decode(get_option('wpme_address'));
    $from = $remetente->postal_code;

    return $from;
}

function wpme_getTo($package){
    $destinatario = $package['destination']['postcode'];
    return $destinatario;
}

function wpme_getOptionals(){
    $optionals = json_decode(get_option('wpme_pluginconfig'));
    $optionals->AR = $optionals->AR? 'true' : 'false';
    $optionals->MP = $optionals->MP? 'true' : 'false';
    $optionals->PL = is_numeric($optionals->PL)? $optionals->PL : 0;
    $optionals->DE = is_numeric($optionals->DE)? $optionals->DE : 0;
    return $optionals;
}

function wpme_getPostOptionals(){
    $optionals = json_decode(get_option('wpme_pluginconfig'));
    $optionals->PL = is_numeric($optionals->PL)? $optionals->PL : 0;
    $optionals->DE = is_numeric($optionals->DE)? $optionals->DE : 0;
    return $optionals;
}

function wpme_getSavedServices(){
    $services = join(',',json_decode(get_option('wpme_services')));
    return $services;
}

function wpme_getValueInsurance($valor,$situacao){
    return $situacao ? $valor : 0;
}

function addExtraValues(){

}

function addExtraVolume(){

}

function addRates(){

}