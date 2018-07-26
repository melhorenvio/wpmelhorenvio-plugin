<?php
defined('ABSPATH') or die('No Script Kiddes');

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    function wpmelhorenvio_shipping(){
        if (! class_exists('Wpmelhorenvio_Shipping_Method')){

            class Wpmelhorenvio_Shipping_Method extends WC_Shipping_Method{

                public function __construct()
                {
                    $this->id = 'wpmelhorenvio_melhorenvioshipping';
                    $this->method_title = __("Melhor Envio","wpmelhorenvio_melhorenvioshipping");
                    $this->method_description = __("Várias transportadoras, descontos, coletas e rastreamentos","wpmelhorenvio_melhorenvioshipping");
                    $this->supports = array('instance_shipping');

                    $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Melhor Envio', 'wpmelhorenvio_melhorenvio' );
                }

                public function calculate_shipping($package = array())
                {
                    include_once plugin_dir_path(__FILE__).'quotation.php';
                    $rates = wpmelhorenvio_getCotacao($package);
                    $optionals = wpmelhorenvio_getOptionals();
                    if($optionals->CF){
                        foreach($rates as $rating){
                            if(isset($rating->price)){
                                $rating->price = $rating->price * (100+$optionals->PL) / 100;
                                $rating->delivery_time = $rating->delivery_time + (int) $optionals->DE;
                                $label = $rating->delivery_time > 1 ? " (".$rating->delivery_time." Dias)" : " (".$rating->delivery_time." Dia)";
                                if($rating->price > 0){
                                    $rate = array(
                                        'id'       => "wpmelhorenvio_".$rating->company->name."_".$rating->name,
                                        'label'    => $rating->company->name." ".$rating->name.$label,
                                        //'label'    => apply_filters('wpmelhorenvio_rate_label', $label, $rating->company->name, $rating->name, $rating->delivery_time),
                                        'cost'     => $rating->price,
                                        'calc_tax' => 'per_item'
                                    );
                                }

                                $this->add_rate( $rate );
                            }
                        }
                    }
                }
            }
        }
    }
    add_action( 'woocommerce_shipping_init', 'wpmelhorenvio_shipping');  
    function wpmelhorenvio_add_shipping( $methods ) {
        return $methods;
    }

    add_filter('woocommerce_shipping_methods', 'wpmelhorenvio_add_shipping');
}
