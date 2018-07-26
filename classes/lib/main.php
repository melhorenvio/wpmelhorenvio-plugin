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

            /* localization */
            add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
            
            /* create admin menu for shipping calculator setting */
            // add_action("admin_menu", array($this, "admin_menu"));

            /* hook for calculate shipping with ajax */
            add_action('wp_ajax_nopriv_ajax_calc_shipping', array($this, 'ajax_calc_shipping'));
            add_action('wp_ajax_ajax_calc_shipping', array($this, 'ajax_calc_shipping'));
            /* hook update shipping method */
            add_action('wp_ajax_nopriv_update_shipping_method', array($this, 'update_shipping_method'));
            add_action('wp_ajax_update_shipping_method', array($this, 'update_shipping_method'));

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


            /* check shipping button display on product page */
            // if ($this->get_setting("enable_on_productpage") == 1) {
                /* hook for display shipping button on product page */

                if ($this->get_setting("button_pos") == 1)
                    add_action('woocommerce_single_product_summary', array(&$this, 'display_shipping_calculator'), 100);
                elseif ($this->get_setting("button_pos") == 2)
                    add_action('woocommerce_single_product_summary', array(&$this, 'display_shipping_calculator'), 20);
                else
                    add_action('woocommerce_single_product_summary', array(&$this, 'display_shipping_calculator'), 8);
            // }

            add_action('woocommerce_product_bulk_edit_end', array($this, 'output_bulk_shipping_fields'));
            add_action('woocommerce_product_bulk_edit_save', array($this, 'save_bulk_shipping_fields'));

            add_action('manage_product_posts_custom_column', array($this, 'output_quick_shipping_values'));
            add_action('woocommerce_product_quick_edit_end', array($this, 'output_quick_shipping_fields'));
            add_action('woocommerce_product_quick_edit_save', array($this, 'save_quick_shipping_fields'));
        }

        
        /**
         * Handle localisation
         */
        public function load_plugin_textdomain() {
                load_plugin_textdomain( 'wp_melhor_envioship', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/' );
        }
        
        public function save_quick_shipping_fields($product)
        {
            $product_id = $product->id;

            if ($product_id > 0) {
                $metavalue = isset($_REQUEST[self::$calculator_metakey]) ? "yes" : "no";
                update_post_meta($product_id, self::$calculator_metakey, $metavalue);
            }
        }

        public function output_quick_shipping_fields()
        {
            include self::$plugin_dir . "view/quick-settings.php";
        }

        public function output_quick_shipping_values($column)
        {
            global $post;

            $product_id = $post->ID;
            if ($column == 'name') {
                $estMeta = get_post_meta($product_id, self::$calculator_metakey, true);
                ?>
                <div class="hidden" id="wp_melhor_enviowoo_shipping_inline_<?php echo $product_id; ?>">
                    <div class="_shipping_enable"><?php echo $estMeta; ?></div>
                </div>
                <?php
            }
        }

        public function output_bulk_shipping_fields()
        {
            include self::$plugin_dir . "view/bulk-settings.php";
        }

        public function save_bulk_shipping_fields($product)
        {
            $product_id = $product->id;
            if ($product_id > 0) {
                $metavalue = isset($_REQUEST[self::$calculator_metakey]) ? "yes" : "no";
                update_post_meta($product_id, self::$calculator_metakey, $metavalue);
            }
        }

        public function custom_woocommerce_process_product_meta($post_id)
        {
            $metavalue = isset($_POST[self::$calculator_metakey]) ? "yes" : "no";
            update_post_meta($post_id, self::$calculator_metakey, $metavalue);
        }

        public function add_custom_price_box()
        {
            $hide_calculator = "yes";
            if (isset($_GET["post"]))
                $hide_calculator = get_post_meta($_GET["post"], self::$calculator_metakey, true);
            woocommerce_wp_checkbox(array('id' => self::$calculator_metakey, 'value' => $hide_calculator, 'label' => __('Hide Shipping Calculator', 'wp_melhor_enviohpa_hide_calculator')));
        }

        public function update_shipping_method()
        {
            include_once plugin_dir_path(__FILE__).'classes/ME/error.php';
            insertLogErrorMelhorEnvioGeneric('#######################################################');
            insertLogErrorMelhorEnvioGeneric('Entrou na funcao de calculo de frete na tela do produto');
            insertLogErrorMelhorEnvioGeneric('PHP: ' . phpversion());

            WC_Shortcode_Cart::calculate_shipping();

            if (isset($_POST["product_id"]) && $this->check_product_incart($_POST["product_id"]) === false) {

                insertLogErrorMelhorEnvioGeneric('IF1 - Tem product_id e não tem o produto no cart');
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

                WC()->cart->remove_cart_item($cart_item_key);
            } else {
                $packages = WC()->cart->get_shipping_packages();
                $packages = WC()->shipping->calculate_shipping($packages);
                $available_methods = WC()->shipping->get_packages();
            }

            insertLogErrorMelhorEnvioGeneric('shipping_type: ' . $this->get_setting('shipping_type'));

            wc_clear_notices();
            if ($this->get_setting('shipping_type') == 2) {
                if (isset($available_methods[0]["rates"]) && count($available_methods[0]["rates"]) > 0) {
                    $count = 0;
                    echo '<ul class="shipping_with_price">';
                    foreach ($available_methods[0]["rates"] as $key => $method) {
                        echo "<li>";
                        echo wp_kses_post($method->label) . "&nbsp;<strong>(" . $method->cost . ")</strong>";
                        echo "</li>";
                    }
                    echo '</ul>';
                }
            } else if ($this->get_setting('shipping_type') == 1) {

                if (isset($available_methods[0]["rates"]) && count($available_methods[0]["rates"]) > 0) {
                    $count = 0;
                    foreach ($available_methods[0]["rates"] as $key => $method) {
                        $checked = ($count == 0) ? "checked=checked" : "";
                        echo '<input name="calc_shipping_method" type="radio" ' . $checked . ' ' . checked($key, WC()->session->chosen_shipping_method, false) . ' value="' . esc_attr($key) . '">&nbsp;' . wp_kses_post($method->label) . "<br>";
                        $count++;
                    }
                }
            } else {

                insertLogErrorMelhorEnvioGeneric('Chegamos até aqui, amigos.');

                ?>
                <select name="calc_shipping_method" id="calc_shipping_method" class="shipping_method">
                    <option value=""><?php echo 'Selecione um método de envio'; ?></option>
                    <?php

                    insertLogErrorMelhorEnvioGeneric('Numeros de metodos de envio: ' .count($available_methods[0]["rates"]));
                    if (isset($available_methods[0]["rates"]) && count($available_methods[0]["rates"]) > 0) {

                        foreach ($available_methods[0]["rates"] as $key => $method) {
                            echo '<option value="' . esc_attr($key) . '" ' . selected($key, WC()->session->chosen_shipping_method, false) . '>' . wp_kses_post($method->label) . '</option>';
                        }
                    }
                    ?>
                </select>
                <?php
            }
            die();
        }

        /* function for display shipping calculator on product page */

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


                /* check shipping method and country selected */
                if ($_POST["calc_shipping_method"] == "") {
                    $returnResponse = array("code" => "error", "message" => 'Por favor, selecione um método de envio');
                } elseif ($_POST["calc_shipping_country"] == "") {
                    $returnResponse = array("code" => "error", "message" => 'Por favor, selecione o país');
                } else {
                    $country = sanitize_text_field($_POST["calc_shipping_country"]);

                    /* calculate shipping */
                    $shippingCharge = $this->get_shipping_text(sanitize_text_field($_POST["calc_shipping_method"]), $country);

                    /* get country full name */
                    $country = WC()->countries->countries[$country];

                    if (isset($shippingCharge['label'])) {

                        if (trim($this->get_setting("shipping_message")) != "") {
                            $message = str_replace(array("[shipping-method]", "[shipping-cost]", "[shipping-country]"), array($shippingCharge["label"], $shippingCharge["cost"], $country), $this->get_setting("shipping_message"));
                        } else {
                            $message  = "<b>Produto :</b> R$" . $shippingCharge["product_price"] . "</br>";
                            $message .= "<b>".$shippingCharge["label"] . "</b> : R$" . $shippingCharge["cost"] . "</br>";
                            $message .= "<b>Total :</b> R$" . $shippingCharge["total"];
                            
                        }

                        $returnResponse = array("code" => "success", "message" => $message);
                    } else if (isset($shippingCharge['code'])) {
                        $returnResponse = array("code" => "error", "message" => __($shippingCharge['message'], "wp_melhor_envioship"));
                    } else {
                        $returnResponse = array("code" => "error", "message" => 'Método de envio não disponível');
                    }
                }
            endif;
            echo json_encode($returnResponse);
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

        /* function for calculate shiiping */
        public function get_shipping_text($shipping_method, $country)
        {
            global $woocommerce, $post;
            $returnResponse = array();
            WC_Shortcode_Cart::calculate_shipping();


            if (isset($_POST["product_id"]) && $this->check_product_incart($_POST["product_id"]) === false) {
                $qty = (isset($_POST['current_qty']) && $_POST['current_qty'] > 0) ? $_POST['current_qty'] : 1;
                if (isset($_POST['variation_id']) && $_POST['variation_id'] != "" && $_POST['variation_id'] > 0) {
                    $cart_item_key = WC()->cart->add_to_cart(sanitize_text_field($_POST["product_id"]), $qty, sanitize_text_field($_POST['variation_id']));
                } else {
                    $cart_item_key = WC()->cart->add_to_cart(sanitize_text_field($_POST["product_id"]), $qty);
                }
                $packages = WC()->cart->get_shipping_packages();
                $packages = WC()->shipping->calculate_shipping($packages);
                $packages = WC()->shipping->get_packages();
                WC()->cart->remove_cart_item($cart_item_key);
            } else {
                $packages = WC()->cart->get_shipping_packages();
                $packages = WC()->shipping->calculate_shipping($packages);
                $packages = WC()->shipping->get_packages();
            }

            wc_clear_notices();

            if (isset($packages[0]["rates"][$shipping_method])) {
                $selectedShiiping = $packages[0]["rates"][$shipping_method];

                $_product = wc_get_product( $_POST['product_id'] );

                $returnResponse = array(
                    "label"         => $selectedShiiping->label, 
                    "cost"          => number_format($selectedShiiping->cost, 2, ',', '.'), 
                    "product_price" => number_format($_product->get_price(), 2, ',', '.'),
                    "total"         => number_format((floatval($selectedShiiping->cost) + $_product->get_price()), 2, ',', '.')
                );
                
            } else {
                $AllMethod = WC()->shipping->load_shipping_methods();
                $selectedMethod = $AllMethod[$shipping_method];
                $flag = 0;
                if ($selectedMethod->availability == "including"):
                    foreach ($selectedMethod->countries as $methodcountry) {
                        if ($country == $methodcountry) {
                            $flag = 1;
                        }
                    }
                    if ($flag == 0):
                        $message = $selectedMethod->method_title . " is not available in selected country.";
                        $returnResponse = array("code" => "error", "message" => $message);
                    endif;
                endif;
            }

            return $returnResponse;
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
            wp_enqueue_script('wc-country-select');
            wp_enqueue_script(self::$plugin_slug, self::$plugin_url . "classes/lib/assets/js/shipping-calculator.js");
        }

        /* register admin menu for shipping calculator setting */

        public function admin_menu()
        {
            $wc_page = 'woocommerce';
            add_submenu_page($wc_page, self::$plugin_title, self::$plugin_title, "install_plugins", self::$plugin_slug, array($this, "calculator_setting_page"));
        }

        /* admin setting page for shipping calculator  */

        /* public function calculator_setting_page()
        {

            
            if (isset($_POST[self::$plugin_slug])) {
                $this->saveSetting();
            }
            
            include_once self::$plugin_dir . "classes/lib/views/shipping-setting.php";
        } */

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