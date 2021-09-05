<?php

namespace SmartPack\WMS\Controllers;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use GuzzleHttp\Exception\RequestException;

class REST extends WP_REST_Controller
{
    const PLUGIN_PREFIX = 'smartpack-wms';
    const API_VERSION = 'v1';

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
        return new WP_REST_Response([
            'test' => 'demo'
        ]);
    }
}
