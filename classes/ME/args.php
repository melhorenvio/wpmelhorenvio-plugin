<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function wpmelhorenvio_mountArgsGetOrders($data) {
    
    $args['limit'] = 200;

    if (!empty($data['limit'])) {
        $args['limit'] = $data['limit'];
    }

    $statusWc = [];

    foreach (wc_get_order_statuses() as $status => $item) {
        $statusWc[] = str_replace('wc-', '', $status);
    }
    
    if (isset($data['status']) && in_array($data['status'], $statusWc)) {
        $args['status'] = $data['status'];
    }

    if (empty($data['status'])) {
        $args['status'] = 'processing';
    }

    $args['statusme'] = 'paid';
    if (!empty($data['statusme'])) {
        $args['statusme'] = $data['statusme'];
    }

    if (isset($data['statusme']) && $data['statusme'] == 'all') {
        unset($args['statusme']);
    }

    if (empty($data['time']) && !isset($data['datestart'])) {
        $args['date_after'] = wpmelhorenvio_getDateInPast('day');
    }

    if (!empty($data['datestart']) && !empty($data['dateend']) ) {
        $args['date_after']  = $data['datestart'] . ' 00:00:01';
        $args['date_before'] = $data['dateend'] . ' 23:59:59';
    }

    if (isset($data['time'])) {
        $args['date_after'] = wpmelhorenvio_getDateInPast($data['time']);
    }

    return $args;
}


function wpmelhorenvio_getDateInPast($time) {

    if ($time == 'day') {
        return date('Y-m-d') . ' 00:00:00';
    }

    if ($time == 'week') {
        $date = new DateTime('-1 week');    
        return  $date->format('Y-m-d') . ' 00:00:00';
    }

    if ($time == 'month') {
        $date = new DateTime('-1 month');    
        return  $date->format('Y-m-d') . ' 00:00:00';
    }

    if ($time == 'year') {
        $date = new DateTime('-1 year');    
        return  $date->format('Y-m-d') . ' 00:00:00';
    }

    return '2010-10-10 00:00:00';
}
