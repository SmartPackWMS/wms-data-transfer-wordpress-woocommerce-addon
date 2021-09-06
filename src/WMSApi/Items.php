<?php

namespace SmartPack\WMS\WMSApi;

class Items extends APIService
{
    function list()
    {
        $data = $this->client->request('GET', 'item/list/');
        echo 'getStatusCode: ' . $data->getStatusCode();

        return $data->getBody();
    }

    function import(array $attr)
    {
        // $data = $this->client->request('POST', 'item/import', [
        //     'body' => json_encode([
        //         $attr
        //     ])
        // ]);

        // return $data->getBody();
    }

    function get(string $sku)
    {
        $data = $this->client->request('GET', 'item/get/' . $sku);
        return $data->getBody();
    }
}
