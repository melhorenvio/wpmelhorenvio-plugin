<?php
/* Exit if accessed directly */
if (!defined('ABSPATH'))
    exit;

/**
 * Install table.
 *
 * @access public
 * @return void
 */
function wpme_install_table()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $sql = "
        CREATE TABLE IF NOT EXISTS {$wpdb->prefix}tracking_codes_wpme (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `order_id` varchar(255) NOT NULL,
            `tracking_id` varchar(255) DEFAULT NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";
    $wpdb->query($sql);
//    dbDelta($sql);
}

/**
 * Uninstall table.
 *
 * @access public
 * @return void
 */
function wpme_uninstall_table()
{
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $time = time();
    $sql = "DROP TABLE IF EXISTS ".$wpdb->prefix."tracking_codes";
    $wpdb->query($sql);
}

function wpme_install()
{

    wpme_install_table();

    global $wp_version;
    if (!is_plugin_active('woocommerce/woocommerce.php'))
    {
        deactivate_plugins(plugin_basename(__FILE__)); /* Deactivate plugin */
        wp_die(__('You must run WooCommerce 2.x to install Melhor Envio  plugin', 'wpme_melhorenvio'), __('WC not activated', 'wpme_melhorenvio'), array('back_link' => true));
        return;
    }
    if (!is_plugin_active('woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php'))
    {
        deactivate_plugins(plugin_basename(__FILE__)); /* Deactivate plugin */
        wp_die(__('You must run WooCommerce Extra Checkout Fields for Brazil 3.6.x to install Melhor Envio plugin', 'wpme_melhorenvio'), __('WC Extra Checkout Fields not activated', 'wpme_melhorenvio'), array('back_link' => true));
        return;
    }
    if ((float)$wp_version < 3.5)
    {
        deactivate_plugins(plugin_basename(__FILE__)); /* Deactivate plugin */
        wp_die(__('You must run at least WordPress version 3.5 to install Melhor Envio plugin', 'wpme_melhorenvio'), __('WP not compatible', 'wpme_melhorenvio'), array('back_link' => true));
        return;
    }

}
