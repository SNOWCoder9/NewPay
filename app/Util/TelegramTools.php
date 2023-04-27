<?php


namespace App\Util;


class TelegramTools
{
    /**
     * Sends a POST request to Telegram Bot API.
     * 伪异步，无结果返回.
     *
     * @param array $params
     *
     * @return string
     */
    public static function SendPost($Method, $Params)
    {
        $URL = 'https://api.telegram.org/bot5673798664:AAEjyyM_QjkkRLaIVykxSaRsUsZUDlDJ9Tc/' . $Method;
        $POSTData = json_encode($Params);
        $C = curl_init();
        curl_setopt($C, CURLOPT_URL, $URL);
        curl_setopt($C, CURLOPT_POST, 1);
        curl_setopt($C, CURLOPT_HTTPHEADER, ['Content-Type:application/json; charset=utf-8']);
        curl_setopt($C, CURLOPT_POSTFIELDS, $POSTData);
        curl_setopt($C, CURLOPT_TIMEOUT, 1);
        curl_exec($C);
        curl_close($C);
    }
}
