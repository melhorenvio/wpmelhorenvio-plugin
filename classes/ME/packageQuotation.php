<?php

function wpmelhorenviopackage_getPackageInternal($package = [], $service, $ar = false, $mp = false){

    if (!isset($package['destination']['postcode']) || empty($package['destination']['postcode']) ) {
        return null;
    }
    
    global $woocommerce;

    $token = get_option('wpmelhorenvio_token');
    $client = new WP_Http();
    $products = [];
    $valorTotal = 0;
    $qtsCart = wpmelhorenvio_getQuantityProductsInCart();
    $packageDefault = wpmelhorenvio_getPackageDefault();
    $weight_unit = get_option('woocommerce_weight_unit');
    

    foreach ($package['contents'] as $item_id => $item) {

        $_product = $item['data'];

        $quantity = 1;
        if (!empty($qtsCart) && array_key_exists($_product->get_id(), $qtsCart)) {
            $quantity = $qtsCart[$_product->get_id()];
        }

        $price = round(($_product->get_price() * $quantity), 2);

        $weight = $_product->get_weight();
        if (empty($weight)) {
            $weight = $packageDefault['weight']/1000;
        }

        if ($weight_unit != 'kg' && !empty($weight)) {
            $weight = $weight / 1000;
        }

        $width  = $_product->get_width();
        $height = $_product->get_height();
        $length = $_product->get_length();        

        $products[] = [
            'id'       => $_product->get_id(),
            'weight'   => $weight,
            'width'    => !empty($width)  ? $width  : $packageDefault['width'],
            'height'   => !empty($height) ? $height : $packageDefault['height'],
            'length'   => !empty($length) ? $length : $packageDefault['length'],
            'quantity'  => $quantity,
            'insurance_value' => $_product->get_price()
        ];

        $valorTotal = $valorTotal + $price;
    }

    $address = str_replace("\\" ,"", get_option('wpmelhorenvio_address'));
    
    $body = [
        "from" => [
            "postal_code" => json_decode($address)->postal_code
        ],
        'to' => [
            'postal_code' => $package['destination']['postcode']
        ],
        'products' => $products,
        'options' => [
            "insurance_value" => $valorTotal,
            "receipt"         => $ar, 
            "own_hand"        => $mp, 
            "collect"         => false 
        ],
        "services" => $service 
    ];

    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token,
            'user-agent'    => sprintf("WordPress/%s; WooCommerce/%s; MelhorEnvio/%s (PHP %s); %s", 
                get_bloginfo('version'), 
                $woocommerce->version, 
                VERSION_PLUGIN_MELHOR_ENVIO, 
                phpversion(),
                "http://$_SERVER[HTTP_HOST]"
            )
        ],
        'body'  => json_encode($body),
        'timeout'=>10
    );

    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/calculate',$params);

    if (isset($response['headers']['X-Warning']) && !empty($response['headers']['X-Warning'])) {
        add_option('outdated_melhor_envio', VERSION_PLUGIN_MELHOR_ENVIO, true, true);
    } else {
        delete_option('outdated_melhor_envio');
    }

    if (!is_wp_error($response)) {
        if ($response['response']['code'] == 200) {
            $resposta = json_decode($response['body']);

            if (property_exists($resposta, 'error')) {
                insertLogErrorMelhorEnvio($resposta);
                return null;
            }

            if ($response['body']) {
                return $resposta;
            }

            return null;
        }
    }

    return null;
}

function wpmelhorenvio_getQuantityProductsInCart() {
    
    global $woocommerce;
    $items = $woocommerce->cart->get_cart();

    $response = [];
    foreach ( $items as $item) {
        $response[$item['product_id']] = $item['quantity'];
        $response[$item['variation_id']] = $item['quantity'];
    }

    return $response;  
}

function wpmelhorenvio_getPackageDefault() {

    $saved_optionals = json_decode(get_option('wpmelhorenvio_pluginconfig'));

    $package = [
        'width'  => $saved_optionals->comprimento_padrao,
        'height' => $saved_optionals->altura_padrao,
        'length' => $saved_optionals->largura_padrao,
        'weight' => $saved_optionals->peso_padrao
    ];

    return $package;
}
