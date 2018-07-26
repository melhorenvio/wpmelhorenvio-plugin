<?php

function wpmelhorenvio_getProducts($items) {

    $response = [];

    $saved_optionals = json_decode(get_option('wpmelhorenvio_pluginconfig'));
    $weight      = 0;
    $valor       = 0;
    $valorTotal  = 0;
    $weightTotal = 0;

    foreach($items as $key => $item) { 

        $weightProd = wc_get_product($item['product_id'])->get_weight();
        if (empty($weightProd) || $weightProd == 0) {
            if (!isset($saved_optionals->peso_padrao)) {
                $saved_optionals->peso_padrao = 2;
            }
            $weightProd = $saved_optionals->peso_padrao;
        }

        $widthProd = wc_get_product($item['product_id'])->get_width();
        if (empty($widthProd) || $widthProd == 0) {
            if (!isset($saved_optionals->largura_padrao)) {
                $saved_optionals->largura_padrao = 17;
            }
            $widthProd = $saved_optionals->largura_padrao;
        }

        $heightProd = wc_get_product($item['product_id'])->get_height();
        if (empty($heightProd) || $heightProd == 0) {
            if (!isset($saved_optionals->altura_padrao)) {
                $saved_optionals->altura_padrao = 4;
            }
            $heightProd = $saved_optionals->altura_padrao;
        }

        $lengthProd = wc_get_product($item['product_id'])->get_length();
        if (empty($lengthProd) || $lengthProd == 0) {
            if (!isset($saved_optionals->comprimento_padrao)) {
                $saved_optionals->comprimento_padrao = 12;
            }
            $lengthProd = $saved_optionals->comprimento_padrao;
        }

        $weightTotal = $weightTotal + $weightProd;
        $weight = $weight + $weightProd;
        $valor = wc_get_product($item['product_id'])->get_price();

        $products[] = [
            "id"       => $item['product_id'], 
            "weight"   => $weightProd,
            "width"    => $widthProd, 
            "height"   => $heightProd,
            "length"   => $lengthProd,
            "quantity" => $item['quantity'], 
            "insurance_value" => wc_get_product($item['product_id'])->get_price() 
        ];

        $valorTotal = $valorTotal + wc_get_product($item['product_id'])->get_price();
    }

    return [
        'products'    => $products,
        'valor_total' => $valorTotal,
        'weight'      => $weightTotal
    ];
}

function wpmelhorenvio_getInfoProduct($item) {

    $wcOrder = new WC_Order();
    $product = $wcOrder->get_product_from_item( $item );
    $saved_optionals = json_decode(get_option('wpmelhorenvio_pluginconfig'));
    $variation_id = 0;
    $product_sku  = null;

    if ( !is_object( $product ) ) {
        return false;
    }

    $dimensions = wpmelhorenvio_getProducts([$item]);
    $dimensions = end($dimensions['products']);

    $data = [
        'product_id'     => $item->get_product_id(),
        'variation_id'   => $item->get_variation_id(),
        'product_sku'    => $product->get_sku(),
        'height'         => $dimensions['height'],
        'width'          => $dimensions['width'],
        'length'         => $dimensions['length'],
        'weight'         => $dimensions['weight'],
        'price'          => round($product->get_price(),2)
    ];

   return $data;
}

