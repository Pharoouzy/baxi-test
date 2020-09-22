<?php
/**
 * Created by PhpStorm.
 * User: umar-farouq
 * Date: 9/21/20
 * Time: 10:21 PM
 */


namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Client\ConnectionException;

/**
 * Class Baxi
 * @package App\Helpers
 */
Class Baxi {

    /**
     * @var array
     */
    /**
     * @var array|string
     */
    /**
     * @var array|\Illuminate\Http\Client\PendingRequest|string
     */
    /**
     * @var array|\Illuminate\Config\Repository|\Illuminate\Http\Client\PendingRequest|mixed|string
     */
    /**
     * @var array|\Illuminate\Config\Repository|\Illuminate\Http\Client\PendingRequest|mixed|string
     */
    /**
     * @var array|\Illuminate\Config\Repository|\Illuminate\Http\Client\PendingRequest|mixed|string
     */
    /**
     * @var array|\Illuminate\Config\Repository|\Illuminate\Http\Client\PendingRequest|mixed|string
     */
    private $header, $env, $http, $endpoint, $api_key, $user_secret, $username;

    /**
     * baxi constructor.
     * @param string $mod\e
     */
    public function __construct($env = 'test') {

        $this->env = $env;

        $this->endpoint = config('baxi.api_url');

        $this->api_key = ($env === 'live') ? config('baxi.live.api_key') : config('baxi.test.api_key');

        $this->user_secret = ($env === 'live') ? config('baxi.live.user_secret') : config('baxi.test.user_secret');

        $this->username = ($env === 'live') ? config('baxi.live.username') : config('baxi.test.username');

        $this->header = [
            'Authorization' => 'Api-key ' . $this->api_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $this->http = Http::withHeaders($this->header);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\Client\Response|mixed
     */
    public function verifyMeterNumber($data){

        try {

            $response = $this->http->post($this->endpoint . '/services/electricity/verify', [
                'service_type' => $data['service_type'],
                'account_number' => $data['meter_number'],
            ]);
        }
        catch(ClientException $e) {
            abort('503', 'Connection error.');
        }

        $response = json_decode($response->getBody(), true);

        if($response['status'] === 'success'){
            $response = $response['data'];
        }

        return $response;

    }

    /**
     * @param $data
     * @param $transaction
     * @return \Illuminate\Http\Client\Response|mixed
     */
    public function rechargeElectricityBill($data, $transaction){
        try {

            $response = $this->http->post($this->endpoint . '/services/electricity/request', [
                'phone' => $data['phone_number'],
                'amount' => $data['amount'],
                'account_number' => $transaction->meter_number,
                'service_type' => $transaction->service_type,
                'agentReference' => $transaction->reference,
                'agentId' => $transaction->id,
            ]);
        }
        catch(ClientException $e) {
            abort('503', 'Connection error.');
        }

        $response = json_decode($response->getBody(), true);

        if($response['status'] === 'success'){
            $response = $response['data'];
        }

        return $response;
    }

    /**
     * @param $data
     * @param $transaction
     * @return \Illuminate\Http\Client\Response|mixed
     */
    public function subscribe($data, $transaction){
        try {

            $response = $this->http->post($this->endpoint . '/services/multichoice/request', [
                'total_amount' => $data['amount'],
                'addon_monthsPaidFor' => $data['addon_months_paid_for'],
                'addon_code' => $data['addon_code'],
                'product_monthsPaidFor' => $data['product_months_paid_for'],
                'product_code' => $data['product_code'],
                'smartcard_number' => $data['smartcard_number'],
                'service_type' => 'dstv',
                'agentReference' => $transaction->reference,
                'agentId' => $transaction->id,
            ]);
        }
        catch(ClientException $e) {
            abort('503', 'Connection error.');
        }

        $response = json_decode($response->getBody(), true);

        if($response['status'] === 'success'){
            $response = $response['data'];
        }

        return $response;
    }

    /**
     * @param $reference
     * @return \Illuminate\Http\Client\Response|mixed
     */
    public function requeryTransaction($reference){
        try {

            $response = $this->http->get($this->endpoint . '/superagent/transaction/requery', [
                'agentReference' => $reference,
            ]);
        }
        catch(ClientException $e) {
            abort('503', 'Connection error.');
        }

        $response = json_decode($response->getBody(), true);

        if($response['status'] === 'success'){
            $response = $response['data'];
        }

        return $response;
    }

    /**
     * @return \Illuminate\Http\Client\Response|mixed
     */
    public function getElectricityBillers(){
        try {
            $response = $this->http->get($this->endpoint . '/services/electricity/billers');
        }
        catch(ClientException $e) {
            abort('503', 'Connection error.');
        }

        $response = json_decode($response->getBody(), true);

        if($response['status'] === 'success'){
            $response = $response['data']['providers'];
        }

        return $response;
    }

    /**
     * @return \Illuminate\Http\Client\Response|mixed
     */
    public function getProviderBouquets(){
        try {

            $response = $this->http->post($this->endpoint . '/services/multichoice/list', [
                'service_type' => 'dstv'
            ]);
        }
        catch(ClientException $e) {
            abort('503', 'Connection error.');
        }

        $response = json_decode($response->getBody(), true);

        if($response['status'] === 'success'){
            $response = $response['data'];
        }

        return $response;
    }

    /**
     * @param $productCode
     * @return \Illuminate\Http\Client\Response|mixed
     */
    public function getBouquetAddons($productCode){
        try {

            $response = $this->http->post($this->endpoint . '/services/multichoice/addons', [
                'product_code' => $productCode,
                'service_type' => 'dstv',
            ]);
        }
        catch(ClientException $e) {
            abort('503', 'Connection error.');
        }

        $response = json_decode($response->getBody(), true);

        if($response['status'] === 'success'){
            $response = $response['data'];
        }

        return $response;
    }

}