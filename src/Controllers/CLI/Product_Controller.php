<?php

namespace SmartPack\WMS\Controllers\CLI;

class CLI_Products
{
    public function hello_world()
    {
        \WP_CLI::line('Hello World!');
    }

    function execute()
    {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 10
        );

        $loop = new \WP_Query($args);

        while ($loop->have_posts()) {
            $loop->the_post();
            global $product;

            if ($product->get_sku()) {
                $item = [
                    'sku' => $product->get_sku(),
                    'description' => get_the_title()
                ];

                print_r($item);
            } else {
                echo 'Product missing SKU number';
            }
        }

        wp_reset_query();
    }
}
