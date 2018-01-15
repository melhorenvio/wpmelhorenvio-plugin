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

                    $this->wpmelhorenvio_init();

                    $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Melhor Envio', 'wpmelhorenvio_melhorenvio' );
                }

                function wpmelhorenvio_init() {
//                    include_once .'views/apikey.php';
                    // Load the settings API
//                    $this->init_settings();
//
//                    // Save settings in admin if you have any defined
//                    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
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
        $methods['Wpmelhorenvio_Shipping_Method'] = 'wpmelhorenvio_Shipping_Method';
        return $methods;
    }

    add_filter('woocommerce_shipping_methods', 'wpmelhorenvio_add_shipping');


}

?>