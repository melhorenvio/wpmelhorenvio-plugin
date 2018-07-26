
<style>

    .wp_melhor_envio_calc_shipping {
        display:none;
    }
    .wp_melhor_envio_shipping_button {
        display:none;
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
                <p class="form-row form-row-wide">
                    <select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state" rel="calc_shipping_state">
                        <option value=""><?php echo 'Selecione o país'; ?></option>
                        <?php
                        foreach (WC()->countries->get_shipping_countries() as $key => $value)
                            echo '<option value="' . esc_attr($key) . '"' . selected(WC()->customer->get_shipping_country(), esc_attr($key), false) . '>' . esc_html($value) . '</option>';
                        ?>
                    </select>
                </p>

                <p class="form-row form-row-wide shipping_state">
                    <?php
                    $current_cc = WC()->customer->get_shipping_country();
                    $current_r = WC()->customer->get_shipping_state();
                    $states = WC()->countries->get_states($current_cc);
                    if (is_array($states) && empty($states)) {
                        ?>
                        <input type="hidden" name="calc_shipping_state" class="text-state" id="calc_shipping_state" placeholder="<?php echo 'Estado / País' ?>" />
                        <?php
                    } elseif (is_array($states)) {
                        ?>

                        <select name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php echo 'Estado / País' ?>">
                            <option value=""><?php echo 'Selecione o estado'; ?></option>
                            <?php
                            foreach ($states as $ckey => $cvalue)
                                echo '<option value="' . esc_attr($ckey) . '" ' . selected($current_r, $ckey, false) . '>' . __(esc_html($cvalue), 'woocommerce') . '</option>';
                            ?>
                        </select>

                        <?php
                    } else {
                        ?>
                        <input type="text" class="input-text" value="<?php echo esc_attr($current_r); ?>" placeholder="<?php echo 'Estado / País' ?>" name="calc_shipping_state" id="calc_shipping_state" />
                        <?php
                    }
                    ?>
                </p>

                <?php if (apply_filters('woocommerce_shipping_calculator_enable_city', false)) : ?>

                    <p class="form-row form-row-wide">
                        <input type="text" class="input-text" value="<?php echo esc_attr(WC()->customer->get_shipping_city()); ?>" placeholder="<?php echo 'Cidade'; ?>" name="calc_shipping_city" id="calc_shipping_city" />
                    </p>

                <?php endif; ?>

                <?php if (apply_filters('woocommerce_shipping_calculator_enable_postcode', true)) : ?>

                    <p class="form-row form-row-wide shipping_postcode">
                        <input type="text" class="input-text" value="<?php echo esc_attr(WC()->customer->get_shipping_postcode()); ?>" placeholder="<?php echo 'CEP'; ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
                    </p>

                <?php endif; ?>
                <p class="form-row form-row-wide shippingmethod_container">
                    <?php
                    $packages = WC()->cart->get_shipping_packages();
                    $packages = WC()->shipping->calculate_shipping($packages);
                    $available_methods = WC()->shipping->get_packages();
                    if ($this->get_setting('shipping_type') == 1) {
                        if (isset($available_methods[0]["rates"]) && count($available_methods[0]["rates"]) > 0) {
                            foreach ($available_methods[0]["rates"] as $key => $method) {
                                echo '<input name="calc_shipping_method" class="shipping_method" type="radio" ' . checked($key, WC()->session->chosen_shipping_method, false) . ' value="' . esc_attr($key) . '">&nbsp;' . wp_kses_post($method->label) . "<br>";
                            }
                        }
                    } else {
                        ?>
                        <select name="calc_shipping_method" id="calc_shipping_method" class="shipping_method">
                            <option value=""><?php echo 'Selecione o método de envio'; ?></option>
                            <?php
                            if (isset($available_methods[0]["rates"]) && count($available_methods[0]["rates"]) > 0) {
                                foreach ($available_methods[0]["rates"] as $key => $method) {
                                    echo '<option value="' . esc_attr($key) . '" ' . selected($key, WC()->session->chosen_shipping_method, false) . '>' . wp_kses_post($method->label) . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <?php
                    }
                    ?>
                </p>    
                <?php if ($this->get_setting("shipping_type") != 2): ?>
                <p>
                    <button type="submit"  name="wp_melhor_envio_calc_shipping" value="1" class="button wp_melhor_envio_calc_shipping button"><?php 'Simular' ?></button>
                    <span class="loaderimage"><img src="<?php echo self::$plugin_url ?>classes/lib/assets/images/wp_melhor_envio-loader.gif" alt=""></span>
                </p>
                <?php endif; ?>
                <?php if ($this->get_setting("display_message") == 1): ?>
                    <div class="wp_melhor_envio_message"></div>
                <?php endif; ?>
                <?php wp_nonce_field('woocommerce-cart'); ?>
            </section>
        </form>
    </div>

</div>
