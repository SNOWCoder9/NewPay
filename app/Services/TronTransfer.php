<?php

namespace App\Services;

use IEXBase\TronAPI\Provider\HttpProvider;
use IEXBase\TronAPI\Tron;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TronTransfer
{
    public static function transfer($to, $amount)
    {
        if (!$to){
            return ['code' => 0, 'message' => '请提醒商户配置结算USDT地址'];
        }
        $from = getConfig('settlement_usdt_address');
        if (!$from){
            return ['code' => 0, 'message' => '请先配置结算USDT地址'];
        }
        $key = getConfig('settlement_usdt_key');
        if (!$key){
            return ['code' => 0, 'message' => '请先配置结算USDT私钥'];
        }
        $fullNode = new HttpProvider('https://api.trongrid.io');
        $solidityNode = new HttpProvider('https://api.trongrid.io');
        $eventServer = new HttpProvider('https://api.trongrid.io');
        $signServer = new HttpProvider('https://api.trongrid.io');
        $explorer = new HttpProvider('https://api.trongrid.io');
        try {
            $tron = new Tron($fullNode, $solidityNode, $eventServer, $signServer, $explorer, $key);
            $tron->setAddress($from);
            $contract = $tron->contract('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t');  // Tether USDT https://tronscan.org/#/token20/TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            exit($e->getMessage());
        }
        if ($tron->getBalance($from, true) < 30){
            return ['code' => 0, 'message' => 'TRX能量不足，请先充值'];
        }
        if ($contract->balanceOf() < $amount){
            return ['code' => 0, 'message' => 'USDT数量不足，请先充值'];
        }
        $result = $contract->transfer($to, $amount);
        if (!$result['result']){
            return ['code' => 0, 'data' => "交易失败"];
        }

        return ['code' => 1, 'data' => $result];
    }
}
