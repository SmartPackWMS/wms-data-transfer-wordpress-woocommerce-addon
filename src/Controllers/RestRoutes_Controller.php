<?php

namespace SmartPack\WMS\Controllers;

use Exception;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

use SmartPack\WMS\Helpers;

class RestRoutes_Controller extends WP_REST_Controller
{
    const PLUGIN_PREFIX = 'smartpack-wms';
    const API_VERSION = 'v1';
    const OPTION_NAME = 'smartpack_wms_plugin_settings';

    const ROUTES = [
        'stockChanged'      => '/stock-changed',
        'orderChanged'      => '/order-changed',
        'exportProducts'      => '/export/products',
        'exportOrders'      => '/export/orders',
    ];

    public static function get_route_namespace(): string
    {
        return self::PLUGIN_PREFIX . '/' . self::API_VERSION;
    }

    public function register_routes()
    {
        // register_rest_route(
        //     self::get_route_namespace(),
        //     self::ROUTES['stockChanged'],
        //     [
        //         'methods'             => WP_REST_Server::CREATABLE,
        //         'callback'            => [$this, 'stockChanged']
        //     ]
        // );

        // register_rest_route(
        //     self::get_route_namespace(),
        //     self::ROUTES['orderChanged'],
        //     [
        //         'methods'             => WP_REST_Server::CREATABLE,
        //         'callback'            => [$this, 'orderChanged']
        //     ]
        // );


        register_rest_route(
            self::get_route_namespace(),
            self::ROUTES['exportProducts'],
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'exportProducts']
            ]
        );

        register_rest_route(
            self::get_route_namespace(),
            self::ROUTES['exportOrders'],
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'exportOrders']
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

    public function orderChanged(WP_REST_Request $request)
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

        $order_updated = [];
        foreach ($data as $key => $val) {
            try {
                $order = new \WC_Order($val->id);

                switch ($val->state) {
                    case 0:
                        // None
                        update_post_meta($val->id, 'smartpack_wms_order_state', 'none');
                        update_post_meta($val->id, 'smartpack_wms_changed', new \DateTime());
                        break;
                    case 1:
                        // Ready For Packing
                        update_post_meta($val->id, 'smartpack_wms_order_state', 'ready-for-packing');
                        update_post_meta($val->id, 'smartpack_wms_changed', new \DateTime());
                        break;
                    case 2:
                        // Items Missing
                        update_post_meta($val->id, 'smartpack_wms_order_state', 'items-missing');
                        update_post_meta($val->id, 'smartpack_wms_changed', new \DateTime());
                        break;
                    case 3:
                        // Error
                        update_post_meta($val->id, 'smartpack_wms_order_state', 'error');
                        update_post_meta($val->id, 'smartpack_wms_changed', new \DateTime());
                        break;
                    case 4:
                        // Packing
                        update_post_meta($val->id, 'smartpack_wms_order_state', 'packing');
                        update_post_meta($val->id, 'smartpack_wms_changed', new \DateTime());
                        break;
                    case 5:
                        // Packed
                        $order->update_status('wc-completed');
                        update_post_meta($val->id, 'smartpack_wc_order_state', 'completed');
                        update_post_meta($val->id, 'smartpack_wms_order_state', 'packed');
                        update_post_meta($val->id, 'smartpack_wms_changed', new \DateTime());
                        break;
                    case 6:
                        // Canceled
                        update_post_meta($val->id, 'smartpack_wms_order_state', 'canceled');
                        update_post_meta($val->id, 'smartpack_wms_changed', new \DateTime());
                        break;
                }

                $order_updated[$val->id] = $val->state;
            } catch (Exception $e) {
                $order_updated[$val->id] = null;
            }
        }

        return new WP_REST_Response([
            'content' => $order_updated
        ]);
    }

    public function exportProducts(WP_REST_Request $request) {
        $limit = (isset($_GET['limit']) ? (int) $_GET['limit'] : 100);
        $offset = (isset($_GET['offset']) ? (int) $_GET['offset'] : 0);

        $products = Helpers::getAllproducts($limit, $offset);

        $product_data = [];

        foreach ($products as $product) {
            $product_sku = \get_post_meta($product->ID, '_sku', true);
            $woo_product = wc_get_product( $product->ID );

            $product_data[] = Helpers::getProductData($product->ID);
        }

        return new WP_REST_Response([
            'content' => $product_data,
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'found' => count($product_data)
            ]
        ]);
    }

    public function exportOrders() {
        $limit = (isset($_GET['limit']) ? (int) $_GET['limit'] : 100);
        $offset = (isset($_GET['offset']) ? (int) $_GET['offset'] : 0);

        $order_data = [];
        foreach (Helpers::getAllOrders($limit, $offset) as $order) {
            $order_data[] = Helpers::getOrderData($order->ID, $order);
        }

        return $order_data;
    }

    public function init()
    {
        add_action('rest_api_init', function () {
            $this->register_routes();
        });
    }
}
