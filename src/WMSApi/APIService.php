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
            'base_uri' => $setting['endpoint'],
            'timeout'  => 2.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Hmac-Signature' => hash_hmac('sha256', 'host=' . $_SERVER['HTTP_HOST'] . '&nonce=' . $setting['nonce'], $setting['access_key'])
            ]
        ]);
    }
}
