<?php

namespace SmartPack\WMS\Controllers\CLI;

use SmartPack\WMS\WMSApi\Items;

class CLI_Products
{
    function execute()
    {
        \WP_CLI::success('Start product sync');
        $items = new Items();

        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 10
        );

        $loop = new \WP_Query($args);
        while ($loop->have_posts()) {
            $product = $loop->the_post();
            print_r($product);
            //     global $product;
            //     if ($product->get_sku()) {
            //         $item = [
            //             'sku' => $product->get_sku(),
            //             'description' => get_the_title()
            //         ];

            //         print_r($item);
            //     } else {
            //         echo 'Product missing SKU number';
            //     }
        }

        // $items->import($item);
        // wp_reset_query();
    }
}
