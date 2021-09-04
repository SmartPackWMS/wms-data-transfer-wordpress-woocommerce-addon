<?php

namespace SmartPack\WMS\Controllers;

use DateTime;

class WPInit_Controller
{
    function init()
    {
        add_action('woocommerce_update_product', function ($product_id) {
            update_post_meta($product_id, 'smartpack_wms_state', 'pending');
            update_post_meta($product_id, 'smartpack_wms_changed', new DateTime());
        }, 10, 1);
    }
}
