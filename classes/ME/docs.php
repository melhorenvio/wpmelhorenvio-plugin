<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// include_once WC_ABSPATH.'/includes/wc-order-functions.php';
include_once plugin_dir_path(__FILE__).'config.php';

function wpmelhorenvio_getDocsOrder($order_id) {

    $saved_company =  json_decode(str_replace("\\",'',get_option('wpmelhorenvio_company')));
    $docs = end(get_post_meta($order_id, 'wpme_info_order_docs'));

    $ie = $saved_company->state_register;
    if (isset($docs['ie'])) {
        $ie = $docs['ie'];
    }

    $document = $saved_company->document;
    if (isset($docs['cnpj'])) {
        $document = $docs['cnpj'];
    }   
    
    $key_nf = '';
    if (isset($docs['key_nf'])) {
        $key_nf = $docs['key_nf'];
    }

    $nf = '';
    if (isset($docs['nf'])) {
        $nf = $docs['nf'];
    }

    return [
        'key_nf' => $key_nf,
        'nf' => $nf,
        'ie' => $ie,
        'cnpj' => $document
    ];
    
}

function wpmelhorenvio_updateInfoOrder($data) {

    $id = $data['id'];

    unset($data['action']);
    unset($data['security']);

    $info = get_post_meta($data['id'], 'wpme_info_order_docs');
  
    if (empty($info)) {
        add_post_meta($data['id'], 'wpme_info_order_docs', $data);
    } else {
        update_post_meta($data['id'], 'wpme_info_order_docs', $data);
    }

    return json_encode($data);
}

