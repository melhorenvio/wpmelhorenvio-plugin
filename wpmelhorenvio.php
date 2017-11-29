<?php
/**
 * Created by PhpStorm.
 * User: VHSoa
 * Date: 27/11/2017
 * Time: 09:28
 */

/*
 *
Plugin Name: Melhor Envio - Cotação
Plugin URI:  http://www.melhorenvio.com.br/
Description: Plugin que permite a cotação de fretes utilizando a API do Melhor Envio. Ainda é possível disponibilizar as informações da cotação de frete diretamente para o consumidor final.
Version:     2.0.0
Author:      Vítor Soares
Author URI:  https://vhsoares.github.io/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
Domain Path: /languages
*/

if( !class_exists('WP_MelhorEnvio')):

class WPMelhorEnvio
{
    public function __construct()
    {
        add_action('plugins_loaded',array($this,'init'));
    }

    public function init()
    {
        if(class_exists('WC_Integration')){
            //Incluindo a classe de integraçao
            include_once 'includes/wpmelhorenviointegration.php';
            //Registrando a integração
            add_filter('woocommerce_integrations',array($this, 'me_add_integration'));
            //Criando os links no Menu
            add_action("admin_menu", "me_addMenu");
            function me_addMenu(){
                add_menu_page("Melhor Envio", "Melhor Envio", "administrator", "wpme_melhor-envio","pedidos", plugin_dir_url( __FILE__ )."data/mo.png");
                add_submenu_page("wpme_melhor-envio","Melhor Envio - Pedidos", "Pedidos", "administrator", "wpme_melhor-envio-requests", "pedidos");
                add_submenu_page("wpme_pedidos","Melhor Envio - Pedidos", "Pedidos", "administrator", "wpme_melhor-envio-request", "pedido");
                add_submenu_page("wpme_pedidos","Melhor Envio - Relatório", "Relatório", "administrator", "wpme_melhor-envio-relato", "relatorio");
                add_submenu_page("wpme_melhor-envio","Melhor Envio - Configurações do Plugin", "Configurações", "administrator", "wpme_melhor-envio-config", "wpme_config");
                add_submenu_page("wpme_melhor-envio","Melhor Envio - Configurações da Conta", "Sua Conta Melhor Envio", "administrator", "wpme_melhor-envio-subscription", "wpme_cadastro");
            }

            function wpme_cadastro(){
                include_once 'class/config.php';
                include_once 'views/apikey.php';
            }

            function wpme_config(){
                include_once 'class/config.php';
                include_once 'views/address.php';
            }
        }
    }


    /**
     * Adiciona uma nova integração ao WooCommerce
     */
    public function me_add_integration(){

        $integrations[] = 'WPMelhorEnvioIntegration';
        return $integrations;
    }
}


$WPMelhorEnvioIntegration = new WPMelhorEnvio(__FILE__);

endif;