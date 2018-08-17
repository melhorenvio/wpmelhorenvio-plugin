<style>

    .wp_melhor_envio_calc_shipping {
        /* display:none; */
    }
    .wp_melhor_envio_shipping_button {
        /* display:none; */
    }

    .wp_melhor_envio_shipping_button a {
        line-height: 35px;
    }
    .wp_melhor_envio_shipping_button a img{
        float: left;
        margin-right: 10px;
        width: 20px;
        margin-top: 8px;
    }
    .price {
        font-size:15px!important;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }
        input[type="number"]::-webkit-inner-spin-button, 
        input[type="number"]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
        
</style>

<div id="wp_melhor_envio_shipping_calculator">
    <div class="wp_melhor_envio_shipping_button"><a href="javascript:void(0);" class="button btn_shipping"><img src="<?php echo self::$plugin_url ?>assets/img/truck.png"><?php echo  "Simular frete"; ?></a></div>
    <div class="wp_melhor_envio_shiiping_form">
        <?php if ($this->get_setting("display_message") != 1): ?>
            <div class="wp_melhor_envio_message"></div>
        <?php endif; ?>

        <form class="woocommerce-shipping-calculator" action="" method="post">
            <section class="shipping-calculator-form">
                <?php
                if (is_product()) {
                    global $post;
                    ?>
                    <input type="hidden" name="product_id" value="<?php echo $post->ID; ?>" />
                <?php } ?>
            
                <p class="form-row form-row-wide shipping_postcode">
                    <input type="text" maxlength="9" placeholder="00000-000" class="input-text cep" value="<?php echo esc_attr(WC()->customer->get_shipping_postcode()); ?>" placeholder="<?php echo 'CEP'; ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
                </p>

                <p class="form-row form-row-wide shippingmethod_container">
                    <?php
                        $packages = WC()->cart->get_shipping_packages();
                        $packages = WC()->shipping->calculate_shipping($packages);
                        $available_methods = WC()->shipping->get_packages();
                    ?>
                </p>    
                <p>
                    <button type="submit"  name="wp_melhor_envio_calc_shipping" value="1" class="button wp_melhor_envio_calc_shipping button"><?php echo 'Simular'; ?></button>
                    <span class="loaderimage"><img src="<?php echo self::$plugin_url ?>classes/lib/assets/images/wp_melhor_envio-loader.gif" alt=""></span>
                </p>
                <?php if ($this->get_setting("display_message") == 1): ?>
                    <div class="wp_melhor_envio_message"></div>
                <?php endif; ?>
                <?php wp_nonce_field('woocommerce-cart'); ?>
            </section>
        </form>

        <div id="responseMelhorEnvio">
        </div>
    </div>

</div>
