<?php
/**
 * Created by PhpStorm.
 * User: VHSoa
 * Date: 03/12/2017
 * Time: 23:33
 */
include_once ABSPATH.WPINC.'/option.php';
include_once ABSPATH.WPINC.'/class-requests.php';
include_once ABSPATH . 'wp-content/plugins/woocommerce-extra-checkout-fields-for-brazil/includes/class-extra-checkout-fields-for-brazil-api.php';


function getCotacao(){
    $client = new WP_Http();
    $token = get_option('wpme_token');
    $params = array('headers'=>['Content-Type' => 'application/json','Accept'=>'application/json','Authorization' => 'Bearer '.$token]);
    $response = $client->get("https://melhorenvio.com.br/api/v2/calculator")
}

function getPackage($package){
    $volume =0;
    $weight =0;
    $total  =0;
    $pacote = new stdClass();
    foreach ($package['contents'] as $item){
        $width = wc_get_product($item['product_id'])->get_width();
        $height = wc_get_product($item['product_id'])->get_height();
        $length = wc_get_product($item['product_id'])->get_length();
        $weight += wc_get_product($item['product_id'])->get_weight()  * $item['quantity'];
        $valor = wc_get_product($item['product_id'])->get_price() * $item['quantity'];
        $volume  = $volume +  (int) ($width * $length * $height) * $item['quantity'];
        $total += $valor ;
    }
    $side   =  ceil(pow($volume,1/3));
    $pacote->width  = $side >= 12  ? $side : 12;
    $pacote->height = $side >= 4   ? $side : 4;
    $pacote->length = $side >= 17  ? $side : 17;
    $pacote->weight = $weight;
    return $pacote;
}

function getFrom(){
    $remetente = json_decode(get_option('wpme_address'));
    $from = $remetente->postal_code;

    return $from;
}

function getTo($package){
    $destinatario = $package['destination']['postcode'];
    return $destinatario;
}

function addExtraValues(){

}

function addExtraVolume(){

}

function addRates(){

}