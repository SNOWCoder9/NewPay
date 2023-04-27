<?php

namespace App\Payments\AdaPay;

class AdaPayTool
{
    public $rsaPrivateKeyFilePath;
    public $rsaPublicKeyFilePath;
    public $rsaPrivateKey;
    public $rsaPublicKey;

    public function __construct()
    {
    }

    public function generateSignature($url , $params = []):string
    {
        $data = '';
        if (is_array($params)) {
            $data .= $url . json_encode($params);
        } else {
            $data .= $url . $params;
        }
        $sign = $this->SHA1withRSA($data);
        return $sign;
    }

    public function SHA1withRSA($data)
    {
        if ($this->checkEmpty($this->rsaPrivateKeyFilePath)) {
            $privKey = trim($this->rsaPrivateKey);
            $key = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($privKey, 64, "\n", true) . "\n-----END RSA PRIVATE KEY-----";
        } else {
            $privKey = file_get_contents($this->rsaPrivateKeyFilePath);
            $key = openssl_get_privatekey($privKey);
        }
        openssl_sign($data , $signature , $key , OPENSSL_ALGO_SHA1);
        return base64_encode($signature);
    }

    public function verifySign($signature , $data)
    {
        if ($this->checkEmpty($this->rsaPublicKeyFilePath)) {
            $pubKey = trim($this->rsaPublicKey);
            $key = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($pubKey, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
        } else {
            $pubKey = file_get_contents($this->rsaPublicKeyFilePath);
            $key = openssl_get_publickey($pubKey);
        }
        if (openssl_verify($data , base64_decode($signature) , $key , OPENSSL_ALGO_SHA1)) {
            return true;
        }
        return false;
    }

    public function checkEmpty($value)
    {
        if (!isset($value) || ('' === trim($value)) || is_null($value)) {
            return true;
        }
        return false;
    }
}
