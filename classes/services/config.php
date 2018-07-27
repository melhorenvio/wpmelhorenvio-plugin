<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wpmelhorenvio_getPrefixService($service_id) {

    switch ($service_id) {
        case 1:
            return 'woocommerce_pac_';
            break;
        case 2:
            return 'woocommerce_sedex_';
            break;
        case 3:
            return 'woocommerce_jadlog_package_';
            break;
        case 4:
            return 'woocommerce_jadlog_com_';
            break;
        case 7  :
            return 'wpmelhorenvio_Jamef_Rodoviário_';
            break;
        default:
            return null;
    }
}

function getCodeServiceByMethodId($name) {

    //TODO check it
    if ($name == 'wpmelhorenvio_Correios_PAC') {
        return 1;
    }

    if ($name == 'wpmelhorenvio_Correios_SEDEX') {
        return 2;
    }

    if ($name == 'wpmelhorenvio_Correios_EXPRESSO') {
        return 2;
    }

    if ($name == 'wpmelhorenvio_JadLog_.Package') {
        return 3;
    }

    if ($name == 'wpmelhorenvio_JadLog_.Com') {
        return 4;
    }

    if ($name == 'wpmelhorenvio_Jamef_Rodoviário') {
        return 7;
    }

    return null;
}

function getnameServiceByCode($code) {

    if ($code == 1) {
        return 'wpmelhorenvio_Correios_PAC';
    }

    if ($code == 2) {
        return 'wpmelhorenvio_Correios_SEDEX';
    }

    if ($code == 2) {
        return 'wpmelhorenvio_Correios_EXPRESSO';
    }

    if ($code == 3) {
        return 'wpmelhorenvio_JadLog_.Package';
    }

    if ($code == 4) {
        return 'wpmelhorenvio_JadLog_.Com';
    }

    if ($code == 7) {
        return 'wpmelhorenvio_Jamef_Rodoviário';
    }

    return null;
}

function getPrefixServiceByCode($code) {

    if ($code == 1) {
        return 'pac';
    }

    if ($code == 2) {
        return 'sedex';
    }

    if ($code == 3) {
        return 'jadlog_package';
    }

    if ($code == 4) {
        return 'jadlog_com';
    }

    if ($code == 7) {
        return 'jamef';
    }

    return null;
}

function getServicesActive() {
    return ['1', '2', '3', '4', '7'];
}

/**
 * Get shipping classes options.
 *
 * @return array
 */
function get_shipping_classes_options() {
    $shipping_classes = WC()->shipping->get_shipping_classes();
    $options          = array(
        '' => 'Selecione o tipo de classe de entrega',
    );

    if ( ! empty( $shipping_classes ) ) {
        $options += wp_list_pluck( $shipping_classes, 'name', 'slug' );
    }

    return $options;
}

