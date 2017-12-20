<?php
/**
 * Created by PhpStorm.
 * User: Melhor Envio
 * Date: 14/12/2017
 * Time: 10:30
 */

function wpme_data_getTracking($id){
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    $sql = "SELECT * FROM {$wpdb->prefix}tracking_codes_wpme where order_id = '{$id}'";
    return $wpdb->get_results($sql);
}

function wpme_data_insertTracking($id,$tracking,$service){
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    $sql = "INSERT INTO {$wpdb->prefix}tracking_codes_wpme (order_id,tracking_id,service_id) VALUES ('{$id}','{$tracking}','{$service}')";
    return $wpdb->query($sql);
}

function wpme_data_deleteTracking($id,$tracking){
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    $sql = "DELETE FROM {$wpdb->prefix}tracking_codes_wpme WHERE order_id ='{$id}' AND tracking_id = '{$tracking}'";
    return $wpdb->query($sql);
}