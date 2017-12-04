<?php
defined('ABSPATH') or die('No Script Kiddes');

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    function wpme_shipping(){
        if (! class_exists('WPME_Shipping_Method')){

            class WPME_Shipping_Method extends WC_Shipping_Method{

                public function __construct()
                {
                    $this->id = 'wpme_melhorenvioshipping';
                    $this->method_title = __("Melhor Envio (new)","wpme_melhorenvioshipping");
                    $this->method_description = __("Várias transportadoras, descontos, coletas e rastreamentos","wpme_melhorenvioshipping");

                    $this->init();

                    $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Melhor Envio (new)', 'wpme_melhorenvio' );
                }

                function init() {
                    // Load the settings API
                    $this->init_form_fields();
                    $this->init_settings();

                    // Save settings in admin if you have any defined
                    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                }

                public function calculate_shipping($package = array())
                {
                    $rate = array(
                        'id'       => $this->id,
                        'label'    => "Label for the rate",
                        'cost'     => '10.99',
                        'calc_tax' => 'per_item'
                    );

                    $this->add_rate( $rate );
                }
            }
        }
    }
    add_action( 'woocommerce_shipping_init', 'wpme_shipping');
    function wpme_add_shipping( $methods ) {
        $methods['WPME_Shipping_Method'] = 'WPME_Shipping_Method';
        return $methods;
    }

    add_filter('woocommerce_shipping_methods', 'wpme_add_shipping');


}

?>