<?php

namespace SmartPack\WMS\Controllers\CLI;

use Exception;
use SmartPack\WMS\WMSApi\Items;

class CLI_Stock
{
    function execute()
    {
        \WP_CLI::line('Start stock sync');

        $items = new Items();
        $products = $items->list();

        if ($products->status === 200) {
            foreach ($products->data as $val) {
                // Product lookup, if exists, update stock, if not skip product!
                $product_data = get_posts([
                    'post_type' => 'product',
                    'meta_query' => [[
                        'key' => '_sku',
                        'value' => $val->sku
                    ]]
                ]);

                if (!empty($product_data)) {
                    $product = new \WC_Product($product_data[0]->ID);
                    $product->set_manage_stock(true);
                    $product->save();

                    wc_update_product_stock($product_data[0]->ID, $val->totalCombined);
                } else {
                    // Not found
                }
            }
        } else {
            // Error
        }
    }
}
