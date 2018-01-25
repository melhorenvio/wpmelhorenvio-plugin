<?php
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Check if WooCommerce is active
 * @package melhorenvio
 * @category Integration
 * @author Vítor Soares
 */

if ( !class_exists('WPME_WPMelhorEnvioIntegration')):

class WPME_WPMelhorEnvioIntegration extends WC_Integration
{

    /**
     * MelhorEnvio constructor.
     * @access public
     * @return void
     */

    public function __construct()
    {

        global $woocommerce;

        $this->id = 'woocommercemelhorenviointegration';
        $this->method_title = __('Melhor Envio', 'wpme_melhorenvio');
        $this->method_description = __('Um modo de integração que mostra para o cliente final o cálculo do seu frete no Melhor Envio.');
        $this->init_settings();
    }

    public function init_settings()
    {
        //require '../views/apikey.php';
        echo 'Over';
    }
}



endif;
