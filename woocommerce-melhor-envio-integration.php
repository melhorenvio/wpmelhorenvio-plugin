<?php

/*
 *
Plugin Name: Melhor Envio
Plugin URI:  http://www.melhorenvio.com.br/integracoes/woocommerce
Description: Plugin que permite a cotação de fretes utilizando a API do Melhor Envio. Ainda é possível disponibilizar as informações da cotação de frete diretamente para o consumidor final.
Version:     1.3.15
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
    include_once plugin_dir_path(__FILE__).'classes/ME/packageQuotation.php';
    include_once plugin_dir_path(__FILE__).'classes/ME/products.php';
    include_once plugin_dir_path(__FILE__).'classes/ME/error.php';
    include_once plugin_dir_path(__FILE__).'classes/ME/docs.php';
    include_once plugin_dir_path(__FILE__).'classes/services/JadlogPackage.php';
    include_once plugin_dir_path(__FILE__).'classes/services/JadlogCom.php';
    include_once plugin_dir_path(__FILE__).'classes/services/Jamef.php';
    include_once plugin_dir_path(__FILE__).'classes/services/pac.php';
    include_once plugin_dir_path(__FILE__).'classes/services/sedex.php';
    include_once plugin_dir_path(__FILE__).'classes/services/config.php';


    $saved_optionals = json_decode(get_option('wpmelhorenvio_pluginconfig'));
    if ( $saved_optionals->SDP === true) {
        global $wp_melhor_envioship_plugin_url, $wp_melhor_envioship_plugin_dir;
        $wp_melhor_envioship_plugin_dir = dirname(__FILE__) . "/";
        $wp_melhor_envioship_plugin_url = plugins_url() . "/" . basename($wp_melhor_envioship_plugin_dir) . "/";
        include_once $wp_melhor_envioship_plugin_dir . 'classes/lib/main.php';
    }

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

            if(class_exists('WC_Integration') && class_exists('Extra_Checkout_Fields_For_Brazil')){

                //Arquivo necessário para o funcionamento do plugin
                include_once plugin_dir_path(__DIR__). '/woocommerce-extra-checkout-fields-for-brazil/includes/class-extra-checkout-fields-for-brazil-api.php';
                //Criando os links no Menu
                add_action("admin_menu", "wpmelhorenvio_addMenu");

                function plugin_add_settings_link( $links ) {
                    $settings_link = '<a href="admin.php?page=wpmelhorenvio_melhor-envio-config">' . __( 'Configurações' ) . '</a>';
                    array_unshift( $links, $settings_link );
                    return $links;
                }

                $plugin = plugin_basename( __FILE__ );
                add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );

                //Criação de menus laterias
                function wpmelhorenvio_addMenu(){
                    add_menu_page("Melhor Envio", "Melhor Envio", "administrator", "wpmelhorenvio_melhor-envio",null, plugin_dir_url( __FILE__ )."mo.png");
                    add_submenu_page("wpmelhorenvio_melhor-envio","Melhor Envio - Meus dados", "Meus dados", "administrator", "wpmelhorenvio_melhor-envio", "wpmelhorenvio_pedidos");
                    add_submenu_page("wpmelhorenvio_melhor-envio","Melhor Envio - Pedidos", "Pedidos", "administrator", "wpmelhorenvio_melhor-envio-dados", "wpmelhorenvio_dados");
                    add_submenu_page("wpmelhorenvio_melhor-envio","Melhor Envio - Configurações do Plugin", "Configurações", "administrator", "wpmelhorenvio_melhor-envio-config", "wpmelhorenvio_config");
                    add_submenu_page("wpmelhorenvio_melhor-envio","Melhor Envio - Configurações da Conta", "Sua Conta Melhor Envio", "administrator", "wpmelhorenvio_melhor-envio-subscription", "wpmelhorenvio_cadastro");
                }

                function wpmelhorenvio_cadastro(){
                    wp_enqueue_style('style', plugin_dir_url(__FILE__)."/assets/css/style.css");
                    include_once plugin_dir_path(__FILE__).'classes/ME/config.php';
                    include_once plugin_dir_path(__FILE__).'views/apikey.php';
                }
                
                function wpmelhorenvio_config(){
                    if( get_option("wpmelhorenvio_token") == null){
                        wp_redirect(get_admin_url(get_current_blog_id(),"admin.php?page=wpmelhorenvio_melhor-envio-subscription"));
                    }
                    wp_enqueue_style('style', plugin_dir_url(__FILE__)."/assets/css/style.css");
                    include_once plugin_dir_path(__FILE__).'classes/ME/config.php';
                    include_once plugin_dir_path(__FILE__).'views/address.php';
                }
                
                function wpmelhorenvio_pedidos(){
                    if( get_option("wpmelhorenvio_token") == null){
                        wp_redirect(get_admin_url(get_current_blog_id(),"admin.php?page=wpmelhorenvio_melhor-envio-subscription"));
                    }
                    wp_enqueue_style('style', plugin_dir_url(__FILE__)."/assets/css/style.css");
                    include_once plugin_dir_path(__FILE__).'classes/ME/orders.php';
                    include_once plugin_dir_path(__FILE__).'views/pedidos.php';
                }

                function wpmelhorenvio_dados(){
                    if( get_option("wpmelhorenvio_token") == null){
                        wp_redirect(get_admin_url(get_current_blog_id(),"admin.php?page=wpmelhorenvio_melhor-envio-subscription"));
                    }
                    wp_enqueue_style('style', plugin_dir_url(__FILE__)."/assets/css/style.css");
                    include_once plugin_dir_path(__FILE__).'classes/ME/orders.php';
                    // include_once plugin_dir_path(__FILE__).'views/dados.php';
                    include_once plugin_dir_path(__FILE__).'views/dados2.php';
                }
                include_once plugin_dir_path(__FILE__).'classes/ME/shipping.php';
            }

            //Ajax Calls
            include_once plugin_dir_path(__FILE__).'classes/ME/orders.php';

            add_action( 'wp_ajax_wpmelhorenvio_ajax_getTracking', 'wpmelhorenvio_ajax_getTracking' );
            function wpmelhorenvio_ajax_getTracking(){
                check_ajax_referer('wpmelhorenvio_read','security');
                echo wpmelhorenvio_getCustomerCotacaoAPI();
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_getJsonOrders', 'wpmelhorenvio_ajax_getJsonOrders' );
            function wpmelhorenvio_ajax_getJsonOrders(){
                check_ajax_referer('wpmelhorenvio_read','security');
                echo wpmelhorenvio_getJsonOrders();
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_getJsonOrders2', 'wpmelhorenvio_ajax_getJsonOrders2' );
            function wpmelhorenvio_ajax_getJsonOrders2(){
                echo wpmelhorenvio_getJsonOrders2();
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_ticketAcquirementAPI', 'wpmelhorenvio_ajax_ticketAcquirementAPI' );
            function wpmelhorenvio_ajax_ticketAcquirementAPI(){
                check_ajax_referer('wpmelhorenvio_action','security');
                echo wpmelhorenvio_ticketAcquirementAPI();
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_ticketPrintingAPI', 'wpmelhorenvio_ajax_ticketPrintingAPI' );
            function wpmelhorenvio_ajax_ticketPrintingAPI(){
                check_ajax_referer('wpmelhorenvio_action','security');
                echo wpmelhorenvio_ticketPrintingAPI();
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_getCustomerCotacaoAPI', 'wpmelhorenvio_ajax_getCustomerCotacaoAPI' );
            function wpmelhorenvio_ajax_getCustomerCotacaoAPI(){
                check_ajax_referer('wpmelhorenvio_read','security');
                echo wpmelhorenvio_getCustomerCotacaoAPI();
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_getCustomerInfoAPI', 'wpmelhorenvio_ajax_getCustomerInfoAPI' );
            function wpmelhorenvio_ajax_getCustomerInfoAPI(){
                check_ajax_referer('wpmelhorenvio_read','security');
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
                check_ajax_referer('wpmelhorenvio_read','security');
                echo wpmelhorenvio_getTrackingAPI();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_getTrackingApiMR','wpmelhorenvio_ajax_getTrackingApiMR');
            function wpmelhorenvio_ajax_getTrackingApiMR(){
                echo wpmelhorenvio_getTrackingApiMR();
                die();
            }


            add_action('wp_ajax_wpmelhorenvio_ajax_addTrackingAPI','wpmelhorenvio_ajax_addTrackingAPI');
            function wpmelhorenvio_ajax_addTrackingAPI(){
                check_ajax_referer('wpmelhorenvio_action','security');
                echo wpmelhorenvio_addTrackingAPI();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_getTrackingsData','wpmelhorenvio_ajax_getTrackingsData');
            function wpmelhorenvio_ajax_getTrackingsData(){
                check_ajax_referer('wpmelhorenvio_read','security');
                echo wpmelhorenvio_getTrackingsData();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_payTicketAPI','wpmelhorenvio_ajax_payTicketAPI');
            function wpmelhorenvio_ajax_payTicketAPI(){
                check_ajax_referer('wpmelhorenvio_action','security');
                echo wpmelhorenvio_payTicket();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_getAddressAPI','wpmelhorenvio_ajax_getAddressAPI');
            function wpmelhorenvio_ajax_getAddressAPI(){
                check_ajax_referer('wpmelhorenvio_read','security');
                echo str_replace("\\" ,"", get_option('wpmelhorenvio_address'));
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_getOptionsAPI','wpmelhorenvio_ajax_getOptionsAPI');
            function wpmelhorenvio_ajax_getOptionsAPI(){
                check_ajax_referer('wpmelhorenvio_read','security');
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
                check_ajax_referer('wpmelhorenvio_action','security');
                echo wpmelhorenvio_cancelTicketAPI();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_cancelTicketData','wpmelhorenvio_ajax_cancelTicketData');
            function wpmelhorenvio_ajax_cancelTicketData(){
                check_ajax_referer('wpmelhorenvio_action','security');
                echo wpmelhorenvio_cancelTicketData();
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_getCompanyAPI', 'wpmelhorenvio_ajax_getCompanyAPI' );
            function wpmelhorenvio_ajax_getCompanyAPI(){
                check_ajax_referer('wpmelhorenvio_read','security');
                echo str_replace("\\" ,"", get_option('wpmelhorenvio_company'));
                die();
            }

            add_action( 'wp_ajax_wpmelhorenvio_ajax_removeTrackingAPI', 'wpmelhorenvio_ajax_removeTrackingAPI' );
            function wpmelhorenvio_ajax_removeTrackingAPI(){
                check_ajax_referer('wpmelhorenvio_action','security');
                echo wpmelhorenvio_removeFromCart();
                die();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_updateStatusTracking','wpmelhorenvio_ajax_updateStatusTracking');
            function wpmelhorenvio_ajax_updateStatusTracking(){
                check_ajax_referer('wpmelhorenvio_action','security');
                echo wpmelhorenvio_updateStatusTracking();
                die();
            }

            add_action( 'woocommerce_order_status_changed', 'wpmelhorenvio_ajax_checkout_cart', 20, 0);
            function wpmelhorenvio_ajax_checkout_cart() {
                addOrUpdateQuotation();
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_update_info_order','wpmelhorenvio_ajax_update_info_order');
            function wpmelhorenvio_ajax_update_info_order() {
                check_ajax_referer('wpmelhorenvio_action','security');
                header('Content-Type: application/json');
                echo wpmelhorenvio_updateInfoOrder($_POST);
                die;
            }

            add_action('wp_ajax_wpmelhorenvio_ajax_update_quotation_order','wpmelhorenvio_ajax_update_quotation_order');
            function wpmelhorenvio_ajax_update_quotation_order() {
                check_ajax_referer('wpmelhorenvio_action','security');
                echo wpmelhorenvio_updateQuotationOrder($_POST);
                die;
            }

            function woocommerce_get_order_statuses() {
                $order_statuses = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );
                $statuses = array();
                foreach ( $order_statuses as $status ) {
                    $statuses[ $status->slug ] = $status->name;
                }
                return $statuses;
            }

            $wpmelhorenvio_pluginconfig = json_decode(get_option('wpmelhorenvio_pluginconfig'));
            if (isset($wpmelhorenvio_pluginconfig->AA) && $wpmelhorenvio_pluginconfig->AA == true) {

                add_action( 'wp_enqueue_scripts', 'add_js_watch_outside_cep' ); 
                function add_js_watch_outside_cep() {

                    $pluginInfoPath = plugin_basename( __FILE__ );
                    $pluginInfoPath = explode('/', $pluginInfoPath);
                    $pluginInfoPath = $pluginInfoPath[0];

                    wp_enqueue_script( 'autocomplete-address-script', '/wp-content/plugins/' . $pluginInfoPath . '/assets/js/autocomplete-address-script.js', array( ), '', true );
                }
            }

            /**
             * Adds a new column to the "My Orders" table in the account.
             *
             * @param string[] $columns the columns in the orders table
             * @return string[] updated columns
             */
            function sv_wc_add_my_account_orders_column( $columns ) {

                $new_columns = array();
                foreach ( $columns as $key => $name ) {

                    $new_columns[ $key ] = $name;
                    if ( 'order-status' === $key ) {
                        $new_columns['tracking-melhor-rastreio'] = 'Rastreio Melhor Envio';
                    }
                }
                return $new_columns;
            }
            add_filter( 'woocommerce_my_account_my_orders_columns', 'sv_wc_add_my_account_orders_column' );

            /**
             * Adds data to the custom "Tracking Melhor Rastreio" column in "My Account > Orders".
             *
             * @param \WC_Order $order the order object for the row
             */
            function sv_wc_my_orders_melhor_rastreio( $order ) {

                $mr = get_post_meta($order->get_id(), 'wpmelhorenvio_tracking_melhor_rastreio', true);
                if (!$mr || empty($mr)) {

                    $track_id = get_post_meta($order->get_id(), 'wpmelhor_envio_pedido_id', true);
                    if (!$track_id) {
                        echo 'Aguardando código de rastreio';
                    } else {
                        $mr = wpmelhorenvio_getTrackingApiMRClient($order->get_id(), $track_id);
                        if ($mr == 'not found') {
                            echo 'Aguardando código de rastreio';
                        } else {
                            echo '<a href="https://www.melhorrastreio.com.br/rastreio/'. $mr.'" target="_blank">' . $mr . '</a>';
                        }
                    }
                } else {
                    echo '<a href="https://www.melhorrastreio.com.br/rastreio/'. $mr.'" target="_blank">' . $mr . '</a>';
                }
            }
            add_action( 'woocommerce_my_account_my_orders_column_tracking-melhor-rastreio', 'sv_wc_my_orders_melhor_rastreio' );


        }
    }

    $WPMelhorEnvioIntegration = new woocommercemelhorenviointegration(__FILE__);
endif;
