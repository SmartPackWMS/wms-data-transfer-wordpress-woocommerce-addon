<?php

namespace SmartPack\WMS\WMSApi;

use SmartPack\WMS\WMSApi\APIService;

class Webhook extends APIService
{
    function push($attr)
    {
        $data = $this->client->request('POST', '/wordpress/webhook', [
            'body' => json_encode($attr)
        ]);

        return [
            'statusCode' => $data->getStatusCode(),
            'body' => $data->getBody()
        ];
    }
}
