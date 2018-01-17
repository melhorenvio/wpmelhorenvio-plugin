<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Created by PhpStorm.
 * User: Melhor Envio
 * Date: 14/12/2017
 * Time: 10:30
 */

function wpmelhorenvio_data_getTracking($id){
    global $wpdb;
    $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}tracking_codes_wpme where order_id = '%d'",$id);
    return $wpdb->get_results($sql);
}

function wpmelhorenvio_data_getAllTrackings(){
    global $wpdb;

    $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}tracking_codes_wpme limit 3000");

    return $wpdb->get_results($sql);
}

function wpmelhorenvio_data_insertTracking($id,$tracking,$service){
    global $wpdb;
    $sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}tracking_codes_wpme (order_id,tracking_id,service_id,status) VALUES ('%d','%s','%s','cart')",$id,$tracking,$service);
    return $wpdb->query($sql);
}

function wpmelhorenvio_data_deleteTracking($tracking){
    global $wpdb;
//    $sql = "DELETE FROM {$wpdb->prefix}tracking_codes_wpme WHERE tracking_id = '{$tracking}'";
    return $wpdb->delete($wpdb->prefix.'tracking_codes_wpme' , array('tracking_id' => $tracking));
}

function wpmelhorenvio_data_updateTracking($tracking,$valor){
    global $wpdb;
    $sql = $wpdb->prepare("UPDATE {$wpdb->prefix}tracking_codes_wpme SET status='%s' WHERE tracking_id = '%s'",$valor,$tracking);
    return $wpdb->query($sql);
}