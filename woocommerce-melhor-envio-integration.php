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
Author:      Melhor Envio
Author URI:  https://melhorenvio.com.br/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
Domain Path: /languages
*/

if( !class_exists('woocommerce-melhor-envio-integration')):
    include_once 'includes/wpmeinstaller.php';
    /* Register plugin status hooks */
    register_activation_hook(__FILE__,'wpme_install');

    class woocommercemelhorenviointegration
    {
        public function __construct()
        {

            include_once(ABSPATH.'wp-admin/includes/plugin.php');
            if( is_plugin_active('woocommerce/woocommerce.php')){
                add_action('plugins_loaded',array($this,'init'));
            }
        }

        public function init()
        {
            if(class_exists('WC_Integration')){
                //Criando os links no Menu
                add_action("admin_menu", "wpme_addMenu");

                function plugin_add_settings_link( $links ) {
                    $settings_link = '<a href="admin.php?page=wpme_melhor-envio-config">' . __( 'Configurações' ) . '</a>';
                    array_unshift( $links, $settings_link );
                    return $links;
                }
                $plugin = plugin_basename( __FILE__ );
                add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );




                function wpme_addMenu(){
                   add_menu_page("Melhor Envio", "Melhor Envio", "administrator", "wpme_melhor-envio","wpme_pedidos", plugin_dir_url( __FILE__ )."mo.png");
                    add_submenu_page("wpme_melhor-envio","Melhor Envio - Pedidos", "Pedidos", "administrator", "wpme_melhor-envio-requests", "wpme_pedidos");
                    add_submenu_page("wpme_melhor-envio","Melhor Envio - Configurações do Plugin", "Configurações", "administrator", "wpme_melhor-envio-config", "wpme_config");
                    add_submenu_page("wpme_melhor-envio","Melhor Envio - Configurações da Conta", "Sua Conta Melhor Envio", "administrator", "wpme_melhor-envio-subscription", "wpme_cadastro");
                }

                function wpme_cadastro(){
                    include_once 'class/config.php';
                    include_once 'views/apikey.php';
                }
                function wpme_config(){
                    if( get_option("wpme_token") == null){
                        wp_redirect(get_admin_url(get_current_blog_id(),"admin.php?page=wpme_melhor-envio-subscription"));
                    }
                    include_once 'class/config.php';
                    include_once 'views/address.php';
                }
                function wpme_pedidos(){
                    if( get_option("wpme_token") == null){
                        wp_redirect(get_admin_url(get_current_blog_id(),"admin.php?page=wpme_melhor-envio-subscription"));
                    }
                    include_once 'class/orders.php';
                    include_once 'views/pedidos.php';
                }
                include_once 'class/shipping.php';
            }


            include_once 'class/orders.php';
            add_action( 'wp_ajax_wpme_ajax_getTracking', 'wpme_ajax_getTracking' );
                function wpme_ajax_getTracking(){
                    echo wpme_getCustomerCotacaoAPI();
                    die();
                }


            add_action( 'wp_ajax_wpme_ajax_getJsonOrders', 'wpme_ajax_getJsonOrders' );
            function wpme_ajax_getJsonOrders(){
                echo wpme_getJsonOrders();
                die();
            }
            add_action( 'wp_ajax_wpme_ajax_ticketAcquirementAPI', 'wpme_ajax_ticketAcquirementAPI' );
            function wpme_ajax_ticketAcquirementAPI(){
                echo wpme_ticketAcquirementAPI();
                die();

            }
            add_action( 'wp_ajax_wpme_ajax_ticketPrintingAPI', 'wpme_ajax_ticketPrintingAPI' );
            function wpme_ajax_ticketPrintingAPI(){
                echo wpme_ticketPrintingAPI();
                die();
            }
            add_action( 'wp_ajax_wpme_getCustomerCotacaoAPI', 'wpme_ajax_getCustomerCotacaoAPI' );
            function wpme_ajax_getCustomerCotacaoAPI(){
                echo wpme_getCustomerCotacaoAPI();
                die();
            }
            add_action( 'wp_ajax_wpme_ajax_getCustomerInfoAPI', 'wpme_ajax_getCustomerInfoAPI' );
            function wpme_ajax_getCustomerInfoAPI(){
                echo wpme_getCustomerInfoAPI();
                die();
            }

            add_action( 'wp_ajax_wpme_ajax_getBalanceAPI', 'wpme_ajax_getBalanceAPI' );
            function wpme_ajax_getBalanceAPI(){
                echo wpme_getBalanceAPI();
                die();
            }

            add_action( 'wp_ajax_wpme_ajax_getLimitsAPI', 'wpme_ajax_getLimitsAPI' );
            function wpme_ajax_getLimitsAPI(){
                echo wpme_getLimitsAPI();
                die();
            }

            add_action('wp_ajax_wpme_ajax_getTrackingAPI','wpme_ajax_getTrackingAPI');
            function wpme_ajax_getTrackingAPI(){
                echo wpme_getTrackingAPI();
                die();
            }


            add_action('wp_ajax_wpme_ajax_addTrackingAPI','wpme_ajax_addTrackingAPI');
            function wpme_ajax_addTrackingAPI(){
                echo wpme_addTrackingAPI();
                die();
            }

            add_action('wp_ajax_wpme_ajax_getTrackingsData','wpme_ajax_getTrackingsData');
            function wpme_ajax_getTrackingsData(){
                echo wpme_getTrackingsData();
                die();
            }

            add_action('wp_ajax_wpme_ajax_payTicketAPI','wpme_ajax_payTicketAPI');
            function wpme_ajax_payTicketAPI(){
                echo wpme_payTicket();
                die();
            }

            add_action('wp_ajax_wpme_ajax_getAddressAPI','wpme_ajax_getAddressAPI');
            function wpme_ajax_getAddressAPI(){
                echo get_option('wpme_address');
                die();
            }

            add_action('wp_ajax_wpme_ajax_getOptionsAPI','wpme_ajax_getOptionsAPI');
            function wpme_ajax_getOptionsAPI(){
                echo get_option('wpme_pluginconfig');
                die();
            }

            add_action('wp_ajax_wpme_ajax_updateStatusData','wpme_ajax_updateStatusData');
            function wpme_ajax_updateStatusData(){
                echo wpme_updateTrackingData();
                die();
            }

            add_action('wp_ajax_wpme_ajax_cancelTicketAPI','wpme_ajax_cancelTicketAPI');
            function wpme_ajax_cancelTicketAPI(){
                echo wpme_cancelTicketAPI();
                die();
            }

            add_action('wp_ajax_wpme_ajax_cancelTicketData','wpme_ajax_cancelTicketData');
            function wpme_ajax_cancelTicketData(){
                echo wpme_cancelTicketData();
                die();
            }

            add_action( 'wp_ajax_wpme_ajax_getCompanyAPI', 'wpme_ajax_getCompanyAPI' );
            function wpme_ajax_getCompanyAPI(){
                echo get_option('wpme_company');
                die();
            }

            add_action( 'wp_ajax_wpme_ajax_removeTrackingAPI', 'wpme_ajax_removeTrackingAPI' );
            function wpme_ajax_removeTrackingAPI(){
                echo wpme_removeFromCart();
                die();
            }

            add_action('wp_ajax_wpme_ajax_updateStatusTracking','wpme_ajax_updateStatusTracking');
            function wpme_ajax_updateStatusTracking(){
                echo wpme_updateStatusTracking();
                die();
            }



        }

        /**
         * Adiciona uma nova integração ao WooCommerce
         */


    }
    $WPMelhorEnvioIntegration = new woocommercemelhorenviointegration(__FILE__);
endif;