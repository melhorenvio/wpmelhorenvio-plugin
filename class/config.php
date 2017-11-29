<?php
/**
 * Created by PhpStorm.
 * User: VHSoa
 * Date: 29/11/2017
 * Time: 09:26
 */

include_once ABSPATH.WPINC.'/option.php';

//Função de update dos dados do usuário
function updateUserData($token)
{
    $params = array('headers'=>['Content-Type' => 'application/json','Accept'=>'application/json','Authorization' => 'Bearer '.$token]);
    $client = new WP_Http();
    $response = $client->get('https://melhorenvio.com.br/api/v2/me',$params);
    var_dump($response);
    if($response['headers']['code'] == "200"){
        updateOptionalData($response['body']);
        update_option('wpme_token',$token);
        var_dump($response);
    }
    return false;
}

function updateOptionalData(array $api_response)
{
    update_option('wpme_id',$api_response->id);
    update_option('wpme_firstname',$api_response->firstname);
    update_option('wpme_lastname',$api_response->lastname);
    update_option('wpme_email',$api_response->email);
    update_option('wpme_picture',$api_response->picture);
    update_option('wpme_document',$api_response->document);
    update_option('wpme_phone',$api_response->phone->phone);
}