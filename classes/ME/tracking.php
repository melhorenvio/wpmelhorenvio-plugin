<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function wpmelhorenvio_data_getTracking($id){
    global $wpdb;
    $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}tracking_codes_wpme where order_id = '%d'",$id);
    return $wpdb->get_results($sql);
}

function wpmelhorenvio_data_getAllTrackings(){
    global $wpdb;
    $sql = "SELECT * FROM {$wpdb->prefix}tracking_codes_wpme limit 3000";
    return $wpdb->get_results($sql);
}

function wpmelhorenvio_data_getAllTrackingsStatusId($orders){

    $ordersId = '';
    foreach ($orders as $order) {
        $ordersId = $ordersId . $order->get_id() . ',';
    }
    $ordersId = rtrim($ordersId,",");
    
    if ($ordersId == '' || is_null($ordersId) || empty($ordersId)) {
        return [];
    }

    global $wpdb;
    $sql = "SELECT * FROM {$wpdb->prefix}tracking_codes_wpme WHERE order_id IN (" . $ordersId. ") limit 3000";
    $results = $wpdb->get_results($sql);

    $response = [];
    foreach ($results as $item) {
        $response[] = $item->order_id;
    }
    return $response;
}

function wpmelhorenvio_data_getAllTrackingsStatus() {


    global $wpdb;
    $sql = "SELECT * FROM {$wpdb->prefix}tracking_codes_wpme limit 3000";
    $results = $wpdb->get_results($sql);

    $response = [];
    foreach ($results as $item) {

        if ($item->status == 'removed') {
            continue;
        }
        
        $response[$item->order_id] = $item->status;
    }
    return $response;
}

function wpmelhorenvio_getAllInfoTrackings($orders, $args = []) {

    $ordersId = '';
    foreach ($orders as $order) {
        $ordersId = $ordersId . $order->get_id() . ',';
    }
    $ordersId = rtrim($ordersId,",");

    if ($ordersId == '' || is_null($ordersId) || empty($ordersId)) {
        return [];
    }

    global $wpdb;

    $limit = 3000;
    if (isset($args['limit'])) {
        $limit = $args['limit'];
    }

    $sql = "SELECT * FROM {$wpdb->prefix}tracking_codes_wpme WHERE order_id IN (" . $ordersId. ") limit " . $limit;
    if (isset($args['statusme']) && $args['statusme'] != 'all' && isset($_GET['statusme'])) {
        $sql = "SELECT * FROM {$wpdb->prefix}tracking_codes_wpme WHERE order_id IN (" . $ordersId. ") AND status = '". $args['statusme'] ."' limit " . $limit;
    }

    $results = $wpdb->get_results($sql);

    $statusMe = [];
    foreach ($results as $item) {
        $statusMe[$item->order_id]['tracking_id'] = $item->tracking_id;
        $statusMe[$item->order_id]['status_me'] = $item->status;
    }

    $response = [];

    foreach ($orders as $item) {
        
        $id = $item->get_id();
        $mr = get_post_meta($id, 'wpmelhorenvio_getAllInfoTrackings');

        $response[$id] = [
            'status_wc'   => $item->get_status(),
            'status_me'   => (isset($statusMe[$id]['status_me'])) ? $statusMe[$id]['status_me'] : null,
            'tracking_id' => (isset($statusMe[$id]['tracking_id'])) ? $statusMe[$id]['tracking_id'] : null,
            'tracking_mr' => (!empty($mr) ) ? end($mr) : null
        ];
    }

    return $response;
}

function wpmelhorenvio_data_getAllTrackingsTrackingId($orders) {

    $ordersId = '';
    foreach ($orders as $order) {
        $ordersId = $ordersId . $order->get_id() . ',';
    }
    $ordersId = rtrim($ordersId,",");

    if ($ordersId == '' || is_null($ordersId) || empty($ordersId)) {
        return [];
    }

    global $wpdb;
    $sql = "SELECT * FROM {$wpdb->prefix}tracking_codes_wpme WHERE order_id IN (" . $ordersId. ") limit 3000";
    $results = $wpdb->get_results($sql);

    $response = [];
    foreach ($results as $item) {
        $response[$item->order_id] = $item->tracking_id;
    }

    return $response;
}

function wpmelhorenvio_data_getAllTrackingsIds(){
    global $wpdb;
    $sql = "SELECT * FROM {$wpdb->prefix}tracking_codes_wpme limit 3000";
    $result =  $wpdb->get_results($sql);

    $ids = [];
    foreach ($result as $item) {
        $ids[] = intval($item->order_id);
    }
    
    return $ids;
}

function wpmelhorenvio_data_insertTracking($id,$tracking,$service){
    global $wpdb;
    $sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}tracking_codes_wpme (order_id,tracking_id,service_id,status) VALUES ('%d','%s','%s','cart')",$id,$tracking,$service);
    return $wpdb->query($sql);
}

function wpmelhorenvio_data_deleteTracking($tracking){
    global $wpdb;
    return $wpdb->delete($wpdb->prefix.'tracking_codes_wpme' , array('tracking_id' => $tracking));
}

function wpmelhorenvio_data_updateTracking($order_id, $tracking_id, $status){

    global $wpdb;
    
    $find = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}tracking_codes_wpme WHERE order_id = '%d'",$order_id);
    $result = $wpdb->query($find);

    if (!$result) {
        $insert = $wpdb->prepare("INSERT INTO {$wpdb->prefix}tracking_codes_wpme (order_id, tracking_id, status) VALUES ('%d', '%s', '%s')",$order_id, $tracking_id, $status);
        return $wpdb->query($insert);
    }

    $sql = $wpdb->prepare("DELETE FROM  {$wpdb->prefix}tracking_codes_wpme  WHERE order_id = '%s'", $order_id);
    $wpdb->query($sql);

    $insert = $wpdb->prepare("INSERT INTO {$wpdb->prefix}tracking_codes_wpme (order_id, tracking_id, status) VALUES ('%d', '%s', '%s')",$order_id, $tracking_id, $status);
    return $wpdb->query($insert);

}