<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// // INCLUSSÃO DOS METODOS DO PLUGIN DE SEQUENCIA DE NUMERO DE PEDIDOS
// add_action( 'wp_insert_post', 'set_sequential_order_number' , 10, 2 );
// add_action( 'woocommerce_process_shop_order_meta','set_sequential_order_number', 10, 2 );

// // return our custom order number for display
// add_filter( 'woocommerce_order_number', 'get_order_number', 10, 2 );

// // return our custom order number for display
// add_filter( 'woocommerce_order_number','get_order_number' , 10, 2 );

// // order tracking page search by order number
// add_filter( 'woocommerce_shortcode_order_tracking_order_id', 'find_order_by_order_number'  );

// // WC Subscriptions support
// add_filter( 'wcs_renewal_order_meta_query', 'subscriptions_remove_renewal_order_meta'  );
// add_filter( 'wcs_renewal_order_created',  'subscriptions_set_sequential_order_number', 10, 2 );

// if ( is_admin() ) {
//     add_filter( 'request', 'woocommerce_custom_shop_order_orderby', 20 );
//     add_filter( 'woocommerce_shop_order_search_fields','custom_search_fields' );

//     // sort by underlying _order_number on the Pre-Orders table
//     add_filter( 'wc_pre_orders_edit_pre_orders_request', 'custom_orderby' );
//     add_filter( 'wc_pre_orders_search_fields', 'custom_search_fields'  );
// }

/**
 * Set the _order_number field for the newly created order
 *
 * @param int $post_id post identifier
 * @param \WP_Post $post post object
 */
function set_sequential_order_number( $post_id, $post ) {
    global $wpdb;

    if ( 'shop_order' === $post->post_type && 'auto-draft' !== $post->post_status ) {

        $order        = wc_get_order( $post_id );
        $order_number = get_order_meta( $order, '_order_number' );

        if ( '' === $order_number ) {

            // attempt the query up to 3 times for a much higher success rate if it fails (due to Deadlock)
            $success = false;

            for ( $i = 0; $i < 3 && ! $success; $i++ ) {

                // this seems to me like the safest way to avoid order number clashes
                $query = $wpdb->prepare( "
                    INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
                    SELECT %d, '_order_number', IF( MAX( CAST( meta_value as UNSIGNED ) ) IS NULL, 1, MAX( CAST( meta_value as UNSIGNED ) ) + 1 )
                        FROM {$wpdb->postmeta}
                        WHERE meta_key='_order_number'",
                    $post_id );

                $success = $wpdb->query( $query );
            }
        }
    }
}

/**
 * Helper method to get order meta pre and post WC 3.0.
 *
 * TODO: Remove this when WooCommerce 3.0+ is required and remove helpers {BR 2017-03-08}
 *
 * @param \WC_Order $order the order object
 * @param string $key the meta key
 * @param bool $single whether to get the meta as a single item. Defaults to `true`
 * @param string $context if 'view' then the value will be filtered
 * @return mixed the order property
 */
function get_order_meta( $order, $key = '', $single = true, $context = 'edit' ) {

    if ( is_wc_version_gte_3_0() ) {

        $value = $order->get_meta( $key, $single, $context );

    } else {

        $order_id = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
        $value    = get_post_meta( $order_id, $key, $single );
    }

    return $value;
}


/**
 * Returns true if the installed version of WooCommerce is 3.0 or greater
 *
 * @since 1.8.0
 * @return boolean true if the installed version of WooCommerce is 3.0 or greater
 */
function is_wc_version_gte_3_0() {
    return get_wc_version() && version_compare( get_wc_version(), '3.0', '>=' );
}

/**
 * Helper method to get the version of the currently installed WooCommerce
 *
 * @since 1.3.2
 * @return string woocommerce version number or null
 */
function get_wc_version() {
    return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
}

/**
 * Filter to return our _order_number field rather than the post ID,
 * for display.
 *
 * @param string $order_number the order id with a leading hash
 * @param WC_Order $order the order object
 * @return string custom order number
 */
function get_order_number( $order_number, $order ) {

    if ( get_order_meta( $order, '_order_number' ) ) {
        $order_number = get_order_meta( $order, '_order_number' );
    }

    return $order_number;
}

/**
 * Search for an order with order_number $order_number
 *
 * @param string $order_number order number to search for
 * @return int post_id for the order identified by $order_number, or 0
 */
function find_order_by_order_number( $order_number ) {

    // search for the order by custom order number
    $query_args = array(
        'numberposts' => 1,
        'meta_key'    => '_order_number',
        'meta_value'  => $order_number,
        'post_type'   => 'shop_order',
        'post_status' => 'any',
        'fields'      => 'ids',
    );

    $posts            = get_posts( $query_args );
    list( $order_id ) = ! empty( $posts ) ? $posts : null;

    // order was found
    if ( $order_id !== null ) {
        return $order_id;
    }

    // if we didn't find the order, then it may be that this plugin was disabled and an order was placed in the interim
    $order = wc_get_order( $order_number );

    if ( ! $order ) {
        return 0;
    }

    if ( get_order_meta( $order, '_order_number' ) ) {
        // _order_number was set, so this is not an old order, it's a new one that just happened to have post_id that matched the searched-for order_number
        return 0;
    }

    return get_order_prop( $order, 'id' );
}

/**
 * Helper method to get order properties pre and post WC 3.0.
 *
 * TODO: Remove this when WooCommerce 3.0+ is required and remove helpers {BR 2017-03-08}
 *
 * @param \WC_Order $order the order for which to get data
 * @param string $prop the order property to get
 * @param string $context the context for the property, 'edit' or 'view'
 * @return mixed the order property
 */
function get_order_prop( $order, $prop, $context = 'edit' ) {

    $value = '';

    if ( is_wc_version_gte_3_0() ) {

        if ( is_callable( array( $order, "get_{$prop}" ) ) ) {
            $value = $order->{"get_{$prop}"}( $context );
        }

    } else {

        // if this is the 'view' context and there is an accessor method, use it
        if ( is_callable( array( $order, "get_{$prop}" ) ) && 'view' === $context ) {
            $value = $order->{"get_{$prop}"}();
        } else {
            $value = $order->$prop;
        }
    }

    return $value;
}

/**
 * Don't copy over order number meta when creating a parent or child renewal order
 *
 * Prevents unnecessary order meta from polluting parent renewal orders,
 * and set order number for subscription orders
 *
 * @since 1.3
 * @param array $order_meta_query query for pulling the metadata
 * @return string
 */
function subscriptions_remove_renewal_order_meta( $order_meta_query ) {
    return $order_meta_query . " AND meta_key NOT IN ( '_order_number' )";
}

/**
 * Sets an order number on a subscriptions-created order
 *
 * @since 1.3
 * @param \WC_Order $renewal_order the new renewal order object
 * @param  \WC_Subscription $subscription Post ID of a 'shop_subscription' post, or instance of a WC_Subscription object
 * @return \WC_Order renewal order instance
 */
function subscriptions_set_sequential_order_number( $renewal_order, $subscription ) {

    // sanity check
    if ( ! $renewal_order instanceof WC_Order ) {
        return $renewal_order;
    }

    $order_post = get_post( get_order_prop( $renewal_order, 'id' ) );
    set_sequential_order_number( $order_post->ID, $order_post );

    // after Subs 2.0 this callback needs to return the renewal order
    return $renewal_order;
}

/**
 * Admin order table orderby ID operates on our meta _order_number
 *
 * @param array $vars associative array of orderby parameteres
 * @return array associative array of orderby parameteres
 */
function woocommerce_custom_shop_order_orderby( $vars ) {
    global $typenow;

    if ( 'shop_order' !== $typenow ) {
        return $vars;
    }

    return custom_orderby( $vars );
}

/**
 * Mofifies the given $args argument to sort on our meta integral _order_number
 *
 * @since 1.3
 * @param array $args associative array of orderby parameteres
 * @return array associative array of orderby parameteres
 */
function custom_orderby( $args ) {

    // Sorting
    if ( isset( $args['orderby'] ) && 'ID' == $args['orderby'] ) {

        $args = array_merge( $args, array(
            'meta_key' => '_order_number',  // sort on numerical portion for better results
            'orderby'  => 'meta_value_num',
        ) );
    }

    return $args;
}


/**
 * Add our custom _order_number to the set of search fields so that
 * the admin search functionality is maintained
 *
 * @param array $search_fields array of post meta fields to search by
 * @return array of post meta fields to search by
 */
function custom_search_fields( $search_fields ) {

    array_push( $search_fields, '_order_number' );

    return $search_fields;
}