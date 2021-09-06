<?php

namespace SmartPack\WMS\WMSApi;

use GuzzleHttp\Client;

abstract class APIService
{
    protected $client = null;

    const OPTION_NAME = 'smartpack_wms_plugin_settings';

    public function __construct()
    {
        $setting = get_option(self::OPTION_NAME);

        $this->client = new Client([
            'base_uri' => $setting['endpoint'] ?? 'https://smartpack.dk/api/v1/',
            'timeout'  => 2.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'auth' => [
                $setting['username'],
                $setting['password']
            ]
        ]);
    }
}
