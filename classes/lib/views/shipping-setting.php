<div class="clear"></div>
<div class="postbox " id="dashboard_right_now" style="width:90%;margin-top:20px;">
    <h3 class="hndle" style="margin: 0px;padding: 10px 15px;"><?php echo __('Shipping Calculator Settings', 'wp_melhor_envioship') ?></h3>
    <div class="inside">
        <div class="main">
            <form method="post" action="" name="<?php echo self::$plugin_slug; ?>">
                <input type="hidden" name="<?php echo self::$plugin_slug; ?>" value="1"/>
                <table class="wp_melhor_envio_table" width="100%;">
                    <tr>
                        <td width="30%"><?php echo __('Enable', 'wp_melhor_envioship') ?></td>
                        <td>
                            <input type="checkbox" name="enable_on_productpage" <?php echo ($this->get_setting("enable_on_productpage")) ? "checked=checked" : ""; ?> value="1" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Button Position', 'wp_melhor_envioship') ?></td>
                        <td>
                            <select name="button_pos">
                                <option value="0" <?php echo ($this->get_setting("button_pos") == 0) ? "selected=selected" : ""; ?>>Below  Title</option>
                                <option value="1" <?php echo ($this->get_setting("button_pos") == 1) ? "selected=selected" : ""; ?>>After Description</option>
                                <option value="2" <?php echo ($this->get_setting("button_pos") == 2) ? "selected=selected" : ""; ?>>After Price</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Shipping Method Input', 'wp_melhor_envioship') ?></td>
                        <td>
                            <select name="shipping_type">
                                <option value="0" <?php echo ($this->get_setting("shipping_type") == 0) ? "selected=selected" : ""; ?>>Select</option>
                                <option value="1" <?php echo ($this->get_setting("shipping_type") == 1) ? "selected=selected" : ""; ?>>Radio</option>
                                <option value="2" <?php echo ($this->get_setting("shipping_type") == 2) ? "selected=selected" : ""; ?>>Display All Shipping With Price</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Display Shipping Message', 'wp_melhor_envioship') ?></td>
                        <td>
                            <select name="display_message">
                                <option value="0" <?php echo ($this->get_setting("display_message") == 0) ? "selected=selected" : ""; ?>>Before Shipping Form</option>
                                <option value="1" <?php echo ($this->get_setting("display_message") == 1) ? "selected=selected" : ""; ?>>After Shipping Form</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="30%"><?php echo __('Default Open Shipping Calculator', 'wp_melhor_envioship') ?></td>
                        <td>
                            <input type="checkbox" name="default_open" <?php echo ($this->get_setting("default_open")) ? "checked=checked" : ""; ?> value="1" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Max Width Of Calculator', 'wp_melhor_envioship') ?></td>
                        <td>
                            <input type="number" class="input_field"  name="max_width" value="<?php echo $this->get_setting("max_width") ?>"/> <label class="px">PX</label>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Shipping Message', 'wp_melhor_envioship') ?></td>
                        <td>
                            <textarea name="shipping_message" cols="35" rows="5"><?php echo $this->get_setting("shipping_message"); ?></textarea>
                            <br>
                            Use <strong>[shipping-method]</strong>, <strong>[shipping-cost]</strong> and <strong>[shipping-country]</strong> <br>for display Shipping Method,Shipping Cost and Shipping Country in Shipping Calculation Message.
                        </td>
                    </tr>
                    <tr>
                        <td><h2><?php echo __('Button customization', 'wp_melhor_envioship') ?></h2></td>
                    </tr>

                    <tr>
                        <td><?php echo __('Button Text', 'wp_melhor_envioship'); ?></td>
                        <td>
                            <?php
                            $button_txt = ($this->get_setting("button_text")) ? $this->get_setting("button_text") : 'Simular frete';
                            ?>

                            <input type="text" class="input_field" name="button_text" value="<?php echo $button_txt; ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;<?php echo __('Align', 'wp_melhor_envioship') ?></td>
                        <td>
                            <select name="button_align">
                                <option value="0" <?php echo ($this->get_setting("button_align") == 0) ? "selected=selected" : ""; ?>><?php echo __('Left', 'wp_melhor_envioship') ?></option>
                                <option value="1" <?php echo ($this->get_setting("button_align") == 1) ? "selected=selected" : ""; ?>><?php echo __('Right', 'wp_melhor_envioship') ?></option>
                                <option value="2" <?php echo ($this->get_setting("button_align") == 2) ? "selected=selected" : ""; ?>><?php echo __('Center', 'wp_melhor_envioship') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;<?php echo __('Size', 'wp_melhor_envioship') ?></td>
                        <td>
                            <input type="number" class="input_field"  name="button_border_size" value="<?php echo $this->get_setting("button_border_size") ?>"/> <label class="px">PX</label>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;<?php echo __('Color', 'wp_melhor_envioship') ?></td>
                        <td>
                            <input type="text" class="color_picker" name="button_border_color" value="<?php echo $this->get_setting("button_border_color") ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;<?php echo __('Border Radius', 'wp_melhor_envioship') ?></td>
                        <td>
                            <input type="number" class="input_field"  name="button_border_radius" value="<?php echo $this->get_setting("button_border_radius") ?>"/> <label class="px">PX</label>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;<?php echo __('Text Color', 'wp_melhor_envioship') ?></td>
                        <td>
                            <input type="text"  class="color_picker" name="button_text_color" value="<?php echo $this->get_setting("button_text_color") ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;<?php echo __('Background Color', 'wp_melhor_envioship') ?></td>
                        <td>
                            <input type="text" class="color_picker" name="button_bg_color" value="<?php echo $this->get_setting("button_bg_color") ?>"/>
                        </td>
                    </tr>
                    <tr><td><br/></td></tr>
                    <tr>
                        <td><?php echo __('Custom Css For Calculator', 'wp_melhor_envioship') ?></td>
                        <td>
                            <textarea name="custom_css"><?php echo $this->get_setting('custom_css') ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <input type="submit" class="button button-primary" name="btn-wp_melhor_envioship-submit" value="<?php echo __("Save Settings", "wp_melhor_envioship") ?>" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<div class="clear"></div>
<div class="postbox " id="dashboard_right_now" style="width:90%;margin-top:20px;">
    <h3 class="hndle" style="margin: 0px;padding: 10px 15px;"><?php echo __("Shortcode", "wp_melhor_envioship") ?></h3>
    <div class="inside">
        <div class="main">
            <table>
                <tr>
                    <td>
                        [shipping-calculator]
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="clear"></div>
