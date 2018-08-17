<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
if (!class_exists('wp_melhor_envio_shipping_calculator')) {

    class wp_melhor_envio_shipping_calculator {

        private static $plugin_url;
        private static $plugin_dir;
        private static $plugin_title = "Shipping Calculator";
        private static $plugin_slug = "wp_melhor_envioship-calculator-setting";
        private static $wp_melhor_envioship_option_key = "wp_melhor_envioship-calculator-setting";
        private $wp_melhor_envioship_settings;
        public static $calculator_metakey = "__calculator_hide";

        public function __construct()
        {
            global $wp_melhor_envioship_plugin_dir, $wp_melhor_envioship_plugin_url;

            /* plugin url and directory variable */
            self::$plugin_dir = $wp_melhor_envioship_plugin_dir;
            self::$plugin_url = $wp_melhor_envioship_plugin_url;

            /* load shipping calculator setting */
            $this->wp_melhor_envioship_settings = get_option(self::$wp_melhor_envioship_option_key);

            /* hook for calculate shipping with ajax */
            add_action('wp_ajax_nopriv_ajax_calc_shipping', array($this, 'ajax_calc_shipping'));
            add_action('wp_ajax_ajax_calc_shipping', array($this, 'ajax_calc_shipping'));

            /* wp_footer hook */
            add_action("wp_footer", array($this, "wp_footer"));

            /* wp_header hook used for include css */
            add_action("wp_head", array($this, "wp_head"));

            /* register admin css and js for shipping calculator */
            add_action('admin_enqueue_scripts', array($this, 'admin_script'));

            /* shipping calculato shortcode */
            add_shortcode("shipping-calculator", array($this, "srt_shipping_calculator"));

            add_action('woocommerce_product_options_general_product_data', array($this, 'add_custom_price_box'));
            add_action('woocommerce_process_product_meta', array($this, 'custom_woocommerce_process_product_meta'), 2);


            if ($this->get_setting("button_pos") == 1)
                add_action('woocommerce_single_product_summary', array(&$this, 'display_shipping_calculator'), 100);
            elseif ($this->get_setting("button_pos") == 2)
                add_action('woocommerce_single_product_summary', array(&$this, 'display_shipping_calculator'), 20);
            else
                add_action('woocommerce_single_product_summary', array(&$this, 'display_shipping_calculator'), 8);
        }

        
        public function save_quick_shipping_fields($product)
        {
            $product_id = $product->id;

            if ($product_id > 0) {
                $metavalue = isset($_REQUEST[self::$calculator_metakey]) ? "yes" : "no";
                update_post_meta($product_id, self::$calculator_metakey, $metavalue);
            }
        }

        public function update_shipping_method()
        {
            include_once plugin_dir_path(__FILE__).'classes/ME/error.php';
            include_once plugin_dir_path(__FILE__).'classes/services/config.php';
            insertLogErrorMelhorEnvioGeneric('#######################################################');
            insertLogErrorMelhorEnvioGeneric('Entrou na funcao de calculo de frete na tela do produto');
            insertLogErrorMelhorEnvioGeneric('PHP: ' . phpversion());

            WC_Shortcode_Cart::calculate_shipping();

            $qty = (isset($_POST['current_qty']) && $_POST['current_qty'] > 0) ? $_POST['current_qty'] : 1;
            insertLogErrorMelhorEnvioGeneric('#' . $_POST["product_id"] . ' Quantidade: ' . $qty);

            if (isset($_POST['variation_id']) && $_POST['variation_id'] != "" && $_POST['variation_id'] > 0) {
                $cart_item_key = WC()->cart->add_to_cart(sanitize_text_field($_POST["product_id"]), $qty, sanitize_text_field($_POST['variation_id']));
            } else {
                $cart_item_key = WC()->cart->add_to_cart(sanitize_text_field($_POST["product_id"]), $qty);
            }

            $packages = WC()->cart->get_shipping_packages();
            $packages = WC()->shipping->calculate_shipping($packages);
            $available_methods = WC()->shipping->get_packages();

            $product = $packages[0]['contents'][$cart_item_key]['data'];
            WC()->cart->remove_cart_item($cart_item_key);

            wc_clear_notices();

            insertLogErrorMelhorEnvioGeneric('Chegamos até aqui, amigos.');

            $token = get_option('wpmelhorenvio_token');
            $client = new WP_Http();

            $address = str_replace("\\" ,"", get_option('wpmelhorenvio_address'));

            $methodsUseds = $this->getMethodsUseds();

            $body = [
                "from" => [
                    "postal_code" => json_decode($address)->postal_code
                ],
                'to' => [
                    'postal_code' => str_replace('-', '',$_POST['calc_shipping_postcode'])
                ],
                'package' => [
                    "weight" => $product->get_weight(),
                    "width"  => $product->get_width(),
                    "height" => $product->get_height(),
                    "length" => $product->get_length()
                ],
                "services" => $methodsUseds
            ];

            $params = array(
                'headers'           =>  [
                    'Content-Type'  => 'application/json',
                    'Accept'        =>  'application/json',
                    'Authorization' =>  'Bearer '.$token
                ],
                'body'  => json_encode($body),
                'timeout'=>100000);

            insertLogErrorMelhorEnvioGeneric(json_encode($body));

            try {
                $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/calculate',$params);

                if($response->errors) {
                    foreach ($response->errors as $code => $error) {
                        $count = count($error) - 1;
                        for ($x=0; $x<= $count; $x++) {
                            insertLogErrorMelhorEnvioGeneric($error[$x]);
                        }
                    }
                    return '<p>Ops! Ocorreu um erro ao conectar com os servidores, tente novamente</p>';
                }
                
                if ($response['body']) {
                    $resposta = json_decode($response['body']);
                    $stringResponse = $this->setLabelShipping($resposta);
                    return $stringResponse;
                    die;
                }

                if (is_wp_error($response)) {

                    foreach ($response->errors as $code => $error) {
                        $count = count($error) - 1;
                        for ($x=0; $x<= $count; $x++) {
                            insertLogErrorMelhorEnvioGeneric($error[$x]);
                        }
                    }
                    return '<p>Ocorreu um erro ao conectar com os servidores, tente novamente</p>';
                    die;
                }

                if (!is_wp_error($response)) {

                    if ($response['response']['code'] == 500) {
                        insertLogErrorMelhorEnvioGeneric('Erro 500');
                        return  '<p>Ocorreu um erro ao conectar com os servidores. (Error 500)</p>';
                        die;
                    }
                    
                    if ($response['response']['code'] == 404) {
                        insertLogErrorMelhorEnvioGeneric('Erro 404');
                        return  '<p>Ocorreu um erro ao conectar com os servidores. (Error 404)</p>';
                        die;
                    }

                    else {
                        insertLogErrorMelhorEnvioGeneric('#2 time out');
                        return  '<p>Ocorreu um erro ao conectar com os servidores.</p>';
                        die;
                    }
                }
            }
            catch(Exception $e) {
                insertLogErrorMelhorEnvioGeneric($e->getMessage());
                return '<p>Ocorreu um erro ao conectar com os servidores. (error 3)</p>';
            }
            die();
        }

        public function getMethodsUseds()
        {
            include_once plugin_dir_path(__FILE__).'classes/services/config.php';
            $response = '';
            $delivery_zones = WC_Shipping_Zones::get_zones();
            foreach (end($delivery_zones)['shipping_methods'] as $method) {
                $code = getCodeServiceByMethodId($method->id);
                if (!$code) {
                    continue;
                }
                $response = $response . $code . ',';
            }
            return rtrim($response,",");
        }

        public function setLabelShipping($resposta) 
        {
            include_once plugin_dir_path(__FILE__).'classes/services/config.php';

            $stringResponse = '';
            foreach ($resposta as $item) {

                $name = getCustomName($item->id);

                if ($item->error) {
                    $stringResponse = $stringResponse . '<p><b>'. $name.'</b>: ' . $item->error . '</p>';
                    continue;
                }

                if (!$item->price) {
                    continue;
                }

                $time = null;
                if ($item->delivery_range->min == 1 && $item->delivery_range->max == 1) {
                    $time = ' (1 dia útil)';   
                }

                if (($item->delivery_range->min == $item->delivery_range->max) && $item->delivery_range->min > 1 ) {
                    $time = ' ('. $item->delivery_range->min .' dia útil)';   
                }

                if ($item->delivery_range->min > 1 &&  $item->delivery_range->max > $item->delivery_range->min) {
                    $time = ' ('. $item->delivery_range->min .' à ' . $item->delivery_range->max . ' dias úteis)';   
                }

                $stringResponse = $stringResponse . '<p><b>'. $name.'</b>: R$' .number_format($item->price, 2, ',', '.') . $time . '</p>';
            }

            return $stringResponse;
        }

        public function display_shipping_calculator()
        {

            global $product;
            
            $id = (WC()->version < '2.7.0' ) ? $product->id : $product->get_id();
            if (get_post_meta($id, self::$calculator_metakey, true) != "yes")
                include_once self::$plugin_dir . 'classes/lib/views/shipping-calculator.php';
        }

        function srt_shipping_calculator()
        {

            ob_start();
            include_once self::$plugin_dir . 'classes/lib/views/shipping-calculator.php';
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        /* calculate shiiping */

        public function ajax_calc_shipping()
        {
            $returnResponse = array();
            if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "ajax_calc_shipping"):
                echo $this->update_shipping_method();
            endif;
            die();
        }

        public function check_product_incart($product_id)
        {
            foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                $_product = $values['data'];

                if ($product_id == $_product->get_id()) {
                    return true;
                }
            }
            return false;
        }

        public function admin_script()
        {
            if (is_admin()) {

                // Add the color picker css file       
                wp_enqueue_style('wp-color-picker');

                wp_enqueue_script('wp_melhor_envioship-admin', self::$plugin_url . "classes/lib/assets/js/admin.js", array('wp-color-picker'), false, true);
                wp_enqueue_style('wp_melhor_envioship-admin', self::$plugin_url . "classes/lib/assets/css/admin.css");
            }
        }

        public function wp_head()
        {
            /* register jquery */
            wp_enqueue_script('jquery');

            $buttonAlign = "left";
            if ($this->get_setting('button_align') == 0)
                $buttonAlign = "left";
            else if ($this->get_setting('button_align') == 1)
                $buttonAlign = "right";
            else if ($this->get_setting('button_align') == 2)
                $buttonAlign = "center";

            $buttonBorder = $this->get_setting('button_border_size');
            $buttonSize = ($buttonBorder != "") ? $buttonBorder . "px" : "0px";
            $buttonColor = $this->get_setting('button_border_color');
            $defaultOpen = ($this->get_setting('default_open') == 1) ? "block" : "none";
            ?>
            <script type="text/javascript">
                var wp_melhor_envio_ajax_url = "<?php echo admin_url("admin-ajax.php") ?>";
            </script>
            <style type="text/css">
                #wp_melhor_envio_shipping_calculator{margin-top:10px;max-width: <?php echo $this->get_setting('max_width') ? $this->get_setting('max_width') : 400 ?>px;}
                .wp_melhor_envio_shipping_button{
                    margin-bottom:10px;
                    text-align: <?php echo $buttonAlign; ?>
                }
                .wp_melhor_envio_shiiping_form{
                    display:<?php echo $defaultOpen; ?>;
                }
                .loaderimage{
                    display:none;
                    margin-left:5px;
                }
                .wp_melhor_envio_message{margin-bottom:10px;}
                .wp_melhor_envio_error{color:red;}
                .wp_melhor_envio_success{color:green;}
                .wp_melhor_envio_shipping_button .btn_shipping{
                    padding:8px 10px;
                    text-align: center;
                    display:inline-block;
                    border:<?php echo $buttonSize ?> <?php echo $buttonColor ?> solid;
                    border-radius: <?php echo $this->get_setting('button_border_radius'); ?>px;
                    color:<?php echo $this->get_setting('button_text_color'); ?>;
                    background-color: <?php echo $this->get_setting('button_bg_color'); ?>;
                    cursor:pointer;
                }
                <?php
                if ($this->get_setting('custom_css') != ""):
                    echo $this->get_setting('custom_css');
                endif;
                ?>
            </style>
            <?php
        }

        public function wp_footer()
        {
            wp_enqueue_script(self::$plugin_slug, self::$plugin_url . "classes/lib/assets/js/shipping-calculator.js");
        }

        /* register admin menu for shipping calculator setting */

        public function admin_menu()
        {
            $wc_page = 'woocommerce';
            add_submenu_page($wc_page, self::$plugin_title, self::$plugin_title, "install_plugins", self::$plugin_slug, array($this, "calculator_setting_page"));
        }

        /* function for save setting */
        public function saveSetting()
        {
            $arrayRemove = array(self::$plugin_slug, "btn-wp_melhor_envioship-submit");
            $saveData = array();
            foreach ($_POST as $key => $value):
                if (in_array($key, $arrayRemove))
                    continue;
                $saveData[$key] = $value;
            endforeach;
            $this->wp_melhor_envioship_settings = $saveData;
            update_option(self::$wp_melhor_envioship_option_key, $saveData);
        }

        /* function for get setting */

        public function get_setting($key)
        {

            if (!$key || $key == "")
                return;

            if (!isset($this->wp_melhor_envioship_settings[$key]))
                return;

            return $this->wp_melhor_envioship_settings[$key];
        }

    }

}
new wp_melhor_envio_shipping_calculator();