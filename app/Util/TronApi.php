<?php


namespace App\Util;


class TronApi
{
    /**
     * 验证波长地址
     *
     * @param $address
     * @return mixed
     */
    public static function validateAddress($address)
    {
        return self::getData('POST', '/wallet/validateaddress', ['address' => $address]);
    }

    public static function getData($method, $url, $body)
    {
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://api.trongrid.io']);

        $response = $client->request($method, $url, [
            'body' => json_encode($body),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'TRON-PRO-API-KEY' => '33494ee3-a3b4-4f12-8ce1-04cdcee8ad40',
            ],
        ]);
        $data = $response->getBody()->getContents();

        return json_decode($data, true);
    }
}
