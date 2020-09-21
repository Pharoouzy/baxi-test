<?php
/**
 * Created by PhpStorm.
 * User: umar-farouq
 * Date: 9/21/20
 * Time: 07:21 PM
 */

namespace App\Services;

use Session;
use Exception;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Client\ConnectionException;


/**
 * Class Paystack
 * @package App\Services
 */
class Paystack {

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
    private $header, $env, $http, $initialize_url, $verify_url;

    /**
     * Paystack constructor.
     * @param string $env
     */
    public function __construct($env = 'test') {

        $this->env = $env;

        $key = ($env == 'test') ? config('paystack.test.secret_key') : config('settings.paystack_secret_key');

        $this->initialize_url = config('paystack.initialize_url');

        $this->verify_url = config('paystack.verify_url');

        $this->header = [
            'Authorization' => 'Bearer ' . $key,
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache'
        ];

        $this->http = Http::withHeaders($this->header);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\Client\Response|\Illuminate\Http\RedirectResponse|mixed
     */
    public function initialize($data) {
        $transaction_data = [
            'amount' => $data['amount'] * 100,
            'email' => $data['email'],
            'currency' => 'NGN',
            'reference' => $data['transaction_ref'],
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'transaction_ref' => $data['transaction_ref'],
                'mode' => $this->env,
            ],
            'ref' => $data['transaction_ref'],
        ];

        try {
            $response = $this->http->post($this->initialize_url, $transaction_data);
        }
        catch(ConnectionException $e) {
            session()->flash('error', ['Unable to complete transaction.']);
            abort('503', 'Connection error.');
        }
        catch(ClientException $e) {
            session()->flash('error', ['Unable to complete transaction.']);
            abort('503', 'Connection error.');
        }

        $response = json_decode($response->getBody(), true);

        if (!$response['status']) {
            session()->flash('error', ['Unable to complete transaction.']);
            // return redirect()->route('home');
        }

        return $response;
    }


    /**
     * @param $reference
     * @return \Illuminate\Http\Client\Response|\Illuminate\Http\RedirectResponse|mixed
     */
    public function verify($reference) {

        try {
            $response = $this->http->get($this->verify_url . $reference);
        }
        catch(ConnectionException $e) {
            // session()->flash('error', ['Unable to verify transaction.']);
            abort('503', 'Unable to verify transaction.');
        }
        catch(ClientException $e) {
            // session()->flash('error', ['Unable to verify transaction.']);
            abort('503', 'Unable to verify transaction.');
        }

        $response = json_decode($response->getBody(), true);

        if (!$response['status']) {
            // session()->flash('error', ['Unable to complete transaction.']);
            abort(503, 'Unable to complete transaction.');
            return redirect()->route('home');
        }

        return $response;
    }
}