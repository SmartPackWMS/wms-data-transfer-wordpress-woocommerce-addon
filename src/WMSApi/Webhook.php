<?php
namespace SmartPack\WMS\WMSApi;

class Webhook
{
    const OPTION_NAME = 'smartpack_wms_plugin_settings';

    function push($attr)
    {
        $setting = get_option(self::OPTION_NAME);
        

        $data = wp_remote_post($setting['endpoint'], [
            'timeout' => 10,
            'body' => json_encode($attr),
            'method'      => 'POST',
            'data_format' => 'body',
        ]);

        $data = json_decode(json_encode($data));
        
        return [
            'statusCode' => $data->response->code,
            'body' => $data->body
        ];
    }
}
