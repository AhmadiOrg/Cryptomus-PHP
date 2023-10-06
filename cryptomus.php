<?php

/**
 * Cryptomus PHP
 * lib Version: V1.0
 * 
 * @method balance()
 * @method payment_create($array = [])
 * @method payment_info($array = [])
 * @method payment_refund($array = [])
 * @method payment_resend($array = [])
 * @method payment_whtest($array = [])
 * @method payment_services($array = [])
 * @method payment_list($array = [])
 * @method wallet_create($array = [])
 * @method wallet_GenQR($array = [])
 * @method wallet_blockAddress($array = [])
 * @method wallet_blockedRefund($array = [])
 * @method payout_create($array = [])
 * @method payout_info($array = [])
 * @method payout_list($array = [])
 * @method payout_services($array = [])
 * @method transfer_toPersonal($array = [])
 * @method transfer_toBusiness($array = [])
 * @method recurrence_create($array = [])
 * @method recurrence_info($array = [])
 * @method recurrence_list($array = [])
 * @method recurrence_cancel($array = [])
 */

class Cryptomus 
{
    const API_URL =     'https://api.cryptomus.com/';
    const API_VERSION = 'v1';
    const TIME_OUT =    30;

    protected $merchentID;

    protected $paymentKey;

    protected $payoutKey;

    /**
     * construct
     *
     * @param string $merchentID
     * @param string|null $paymentKey
     * @param string|null $payoutKey
     */
    public function __construct(
        string $merchentID, 
        string $paymentKey = null, 
        string $payoutKey = null
        )
    {
        $this->merchentID = $merchentID;
        $this->paymentKey = $paymentKey;
        $this->payoutKey = $payoutKey;
    }

    /**
     * call methods
     *
     * @param [type] $action
     * @param [type] $data
     * @return array
     */
    public function __call(
        $action, 
        $data
        )
    {
        if (isset($data[0])) 
        {
            $data =         $data[0];
        }
        $methods =          $this->methodsList();
        if (array_key_exists($action, $methods))
        {
            $path =         $methods[$action];
            $result =       $this->sendRequest($path, $data);
            return $result;
        }
        else 
            throw new Exception('404: Method not found!');
    }

    /**
     * method list
     *
     * @return array
     */
    public function methodsList()
    {
        $result = [
            'balance' =>                'balance',
            'payment_create' =>         'payment',
            'payment_info' =>           'payment/info',
            'payment_refund' =>         'payment/refund',
            'payment_resend' =>         'payment/resend',
            'payment_whtest' =>         'test-webhook/payment',
            'payment_services' =>       'payment/services',
            'payment_list' =>           'payment/list',
            'wallet_create' =>          'wallet',
            'wallet_GenQR' =>           'wallet/qr',
            'wallet_blockAddress' =>    'wallet/block-address',
            'wallet_blockedRefund' =>   'wallet/blocked-address-refund',
            'payout_create' =>          'payout',
            'payout_info' =>            'payout/info',
            'payout_list' =>            'payout/list',
            'payout_services' =>        'payout/services',
            'transfer_toPersonal' =>    'transfer/to-personal',
            'transfer_toBusiness' =>    'transfer/to-business',
            'recurrence_create' =>      'recurrence/create',
            'recurrence_info' =>        'recurrence/info',
            'recurrence_list' =>        'recurrence/list',
            'recurrence_cancel' =>      'recurrence/cancel',
        ];
        return $result;
    }

    /**
     * send Request to cryptomus
     *
     * @param string $path
     * @param array $params
     * @return array
     */
    private function sendRequest(
        string $path, 
        array $params = []
        )
    {
        $paramters =        json_encode($params);
        $url =              self::API_URL . self::API_VERSION . '/' . $path;
        $API_KEY =          ($this->startsWith($path, 'transfer') or $this->startsWith($path, 'payout')) ? $this->payoutKey : $this->paymentKey;

        $headers =          array(
            'merchant: ' .  $this->merchentID,
            'sign: ' .      md5(base64_encode($paramters) . $API_KEY),
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIME_OUT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramters);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $response = json_decode($response, true);
        curl_close($ch);

        return $response;
    }

    /**
     * check start string with
     *
     * @param [type] $string
     * @param [typ'e] $startString
     */
    private function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return substr($string, 0, $len) === $startString;
    }
}