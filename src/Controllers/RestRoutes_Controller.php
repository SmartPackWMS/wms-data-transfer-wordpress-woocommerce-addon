<?php

namespace SmartPack\WMS\Controllers;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use GuzzleHttp\Exception\RequestException;

class RestRoutes_Controller extends WP_REST_Controller
{
    const PLUGIN_PREFIX = 'smartpack-wms';
    const API_VERSION = 'v1';
    const OPTION_NAME = 'smartpack_wms_plugin_settings';

    const ROUTES = [
        'stockChanged'      => '/stock-changed',
    ];

    public static function get_route_namespace(): string
    {
        return self::PLUGIN_PREFIX . '/' . self::API_VERSION;
    }

    public function register_routes()
    {
        register_rest_route(
            self::get_route_namespace(),
            self::ROUTES['stockChanged'],
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'stockChanged']
            ]
        );
    }

    public function stockChanged(WP_REST_Request $request)
    {
        $data = json_decode($request->get_body());
        $beartoken = $request->get_header('Authorization');

        # Token access check
        if (!$beartoken) {
            return new WP_REST_Response([
                'msg' => 'Beartoken access key is not valid'
            ], 403);
        } else {
            $setting = get_option(self::OPTION_NAME);
            $beartoken = str_replace('Bearer ', '', $beartoken);
            if ($setting['webhook_key'] !== $beartoken) {
                return new WP_REST_Response([
                    'msg' => 'Beartoken access key is not valid'
                ], 403);
            }
        }


        $product_stock_updated = [];
        foreach ($data as $key => $val) {
            $product_id = wc_get_product_id_by_sku($val->sku);

            $product_stock_updated[$val->sku] = [
                'id' => $product_id,
                'stock' => $val->totalCombined
            ];

            $product = new \WC_Product($product_id);
            $product->set_manage_stock(true);
            $product->save();

            wc_update_product_stock($product_id, $val->totalCombined);
        }

        return new WP_REST_Response([
            'content' => $product_stock_updated
        ]);
    }

    public function init()
    {
        add_action('rest_api_init', function () {
            $this->register_routes();
        });
    }
}
