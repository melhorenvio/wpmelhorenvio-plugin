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
Plugin URI:  http://www.melhorenvio.com.br/integracoes/woocommerce
Description: Plugin que permite a cotação de fretes utilizando a API do Melhor Envio. Ainda é possível disponibilizar as informações da cotação de frete diretamente para o consumidor final.
Version:     2.0.0
Author:      Melhor Envio
Author URI:  https://melhorenvio.com.br/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
Domain Path: /languages
*/

if( !class_exists('woocommerce-melhor-envio-integration')):
    /* Register plugin status hooks */
    register_activation_hook(__FILE__,'wpmelhorenvio_install');

    include_once plugin_dir_path(__FILE__).'includes/wpmeinstaller.php';
    include_once plugin_dir_path(__DIR__). '/woocommerce-extra-checkout-fields-for-brazil/includes/class-extra-checkout-fields-for-brazil-api.php';

    class woocommercemelhorenviointegration
    {
        public function __construct()
        {
            if( in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' )))
                &&  in_array('woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php', apply_filters( 'active_plugins', get_option( 'active_plugins' )))){
                add_action('plugins_loaded',array($this,'init'));
            }
        }
        public function init()
        {
            if(class_exists('WC_Integration')){
                //Criando os links no Menu
                add_action("admin_menu", "wpmelhorenvio_addMenu");

                function plugin_add_settings_link( $links ) {
                    $settings_link = '<a href="admin.php?page=wpmelhorenvio_melhor-envio-config">' . __( 'Configurações' ) . '</a>';
                    array_unshift( $links, $settings_link );
                    return $links;
                }

                $plugin = plugin_basename( __FILE__ );
                add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );

                function wpmelhorenvio_addMenu(){
                   add_menu_page("Melhor Envio", "Melhor Envio", "administrator", "wpmelhorenvio_melhor-envio","wpmelhorenvio_pedidos", plugin_dir_url( __FILE__ )."mo.png");
                    add_submenu_page("wpmelhorenvio_melhor-envio","Melhor Envio - Pedidos", "Pedidos", "administrator", "wpmelhorenvio_melhor-envio-requests", "wpmelhorenvio_pedidos");
                    add_submenu_page("wpmelhorenvio_melhor-envio","Melhor Envio - Configurações do Plugin", "Configurações", "administrator", "wpmelhorenvio_melhor-envio-config", "wpmelhorenvio_config");
                    add_submenu_page("wpmelhorenvio_melhor-envio","Melhor Envio - Configurações da Conta", "Sua Conta Melhor Envio", "administrator", "wpmelhorenvio_melhor-envio-subscription", "wpmelhorenvio_cadastro");
                }

                function wpmelhorenvio_cadastro(){
                    include_once plugin_dir_path(__FILE__).'class/config.php';
                    include_once plugin_dir_path(__FILE__).'views/apikey.php';
                }

                function wpmelhorenvio_config(){
                    if( get_option("wpmelhorenvio_token") == null){
                        wp_redirect(get_admin_url(get_current_blog_id(),"admin.php?page=wpmelhorenvio_melhor-envio-subscription"));
                    }
                    include_once plugin_dir_path(__FILE__).'class/config.php';
                    include_once plugin_dir_path(__FILE__).'views/address.php';
                }

                function wpmelhorenvio_pedidos(){
                    if( get_option("wpmelhorenvio_token") == null){
                        wp_redirect(get_admin_url(get_current_blog_id(),"admin.php?page=wpmelhorenvio_melhor-envio-subscription"));
                    }
                    include_once plugin_dir_path(__FILE__).'class/orders.php';
                    include_once plugin_dir_path(__FILE__).'views/pedidos.php';
                }
                include_once plugin_dir_path(__FILE__).'class/shipping.php';
            }


            include_once plugin_dir_path(__FILE__).'class/orders.php';
            add_action( 'wp_ajax_wpmelhorenvio_ajax_getTracking', 'wpmelhorenvio_ajax_getTracking' );
                function wpmelhorenvio_ajax_getTracking(){
                    echo wpmelhorenvio_getCustomerCotacaoAPI();
                    die();
                }


            add_action( 'wp_ajax_wpmelhorenvio_ajax_getJsonOrders', 'wpmelhorenvio_ajax_getJsonOrders' );
            function wpmelhorenvio_ajax_getJsonOrders(){
                echo wpmelhorenvio_getJsonOrders();
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_ticketAcquirementAPI', 'wpmelhorenvio_ajax_ticketAcquirementAPI' );
            function wpmelhorenvio_ajax_ticketAcquirementAPI(){
                echo wpmelhorenvio_ticketAcquirementAPI();
                die();

            }
            add_action( 'wp_ajax_wpmelhorenvio_ajax_ticketPrintingAPI', 'wpmelhorenvio_ajax_ticketPrintingAPI' );
            function wpmelhorenvio_ajax_ticketPrintingAPI(){
                echo wpmelhorenvio_ticketPrintingAPI();
                die();
            }
            add_action( 'wp_ajax_wpmelhorenvio_getCustomerCotacaoAPI', 'wpmelhorenvio_ajax_getCustomerCotacaoAPI' );
            function wpmelhorenvio_ajax_getCustomerCotacaoAPI(){
                echo wpmelhorenvio_getCustomerCotacaoAPI();
                die();
            }
            add_action( 'wp_ajax_wpmelhorenvio_ajax_getCustomerInfoAPI', 'wpmelhorenvio_ajax_getCustomerInfoAPI' );
            function wpmelhorenvio_ajax_getCustomerInfoAPI(){
                echo wpmelhorenvio_getCustomerInfoAPI();
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_getBalanceAPI', 'wpmelhorenvio_ajax_getBalanceAPI' );
            function wpmelhorenvio_ajax_getBalanceAPI(){
                echo wpmelhorenvio_getBalanceAPI();
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_getLimitsAPI', 'wpmelhorenvio_ajax_getLimitsAPI' );
            function wpmelhorenvio_ajax_getLimitsAPI(){
                echo wpmelhorenvio_getLimitsAPI();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_getTrackingAPI','wpmelhorenvio_ajax_getTrackingAPI');
            function wpmelhorenvio_ajax_getTrackingAPI(){
                echo wpmelhorenvio_getTrackingAPI();
                die();
            }


            add_action('wp_ajax_wpmelhorenvio_ajax_addTrackingAPI','wpmelhorenvio_ajax_addTrackingAPI');
            function wpmelhorenvio_ajax_addTrackingAPI(){
                echo wpmelhorenvio_addTrackingAPI();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_getTrackingsData','wpmelhorenvio_ajax_getTrackingsData');
            function wpmelhorenvio_ajax_getTrackingsData(){
                echo wpmelhorenvio_getTrackingsData();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_payTicketAPI','wpmelhorenvio_ajax_payTicketAPI');
            function wpmelhorenvio_ajax_payTicketAPI(){
                echo wpmelhorenvio_payTicket();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_getAddressAPI','wpmelhorenvio_ajax_getAddressAPI');
            function wpmelhorenvio_ajax_getAddressAPI(){
                echo get_option('wpmelhorenvio_address');
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_getOptionsAPI','wpmelhorenvio_ajax_getOptionsAPI');
            function wpmelhorenvio_ajax_getOptionsAPI(){
                echo get_option('wpmelhorenvio_pluginconfig');
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_updateStatusData','wpmelhorenvio_ajax_updateStatusData');
            function wpmelhorenvio_ajax_updateStatusData(){
                echo wpmelhorenvio_updateTrackingData();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_cancelTicketAPI','wpmelhorenvio_ajax_cancelTicketAPI');
            function wpmelhorenvio_ajax_cancelTicketAPI(){
                echo wpmelhorenvio_cancelTicketAPI();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_cancelTicketData','wpmelhorenvio_ajax_cancelTicketData');
            function wpmelhorenvio_ajax_cancelTicketData(){
                echo wpmelhorenvio_cancelTicketData();
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_getCompanyAPI', 'wpmelhorenvio_ajax_getCompanyAPI' );
            function wpmelhorenvio_ajax_getCompanyAPI(){
                echo get_option('wpmelhorenvio_company');
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_removeTrackingAPI', 'wpmelhorenvio_ajax_removeTrackingAPI' );
            function wpmelhorenvio_ajax_removeTrackingAPI(){
                echo wpmelhorenvio_removeFromCart();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_updateStatusTracking','wpmelhorenvio_ajax_updateStatusTracking');
            function wpmelhorenvio_ajax_updateStatusTracking(){
                echo wpmelhorenvio_updateStatusTracking();
                die();
            }



        }

        /**
         * Adiciona uma nova integração ao WooCommerce
         */


    }
    $WPMelhorEnvioIntegration = new woocommercemelhorenviointegration(__FILE__);
endif;