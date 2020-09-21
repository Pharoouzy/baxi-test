<?php
/**
 * Created by PhpStorm.
 * User: umar-farouq
 * Date: 9/21/20
 * Time: 06:36 PM
 */

namespace App\Handlers;

use App\Services\Paystack;
use App\Models\Transaction;
use App\Helpers\EmailHelper;
use App\Helpers\RequestHelper;
use App\Helpers\PaymentHelper;

class PaymentHandler {

    use PaymentHelper, EmailHelper, RequestHelper;

    private $paystack;

    public function __construct(){
        $this->paystack = new PayStack();
    }

    public function pay($request, $user) {

        if(!$user){
            abort(401, 'Unauthorized Request');
        }

        $payment_data = $this->getPaymentData($request);

        $transaction = $request->transactions()->create([
            'id' => $this->generateTransactionId(),
            'user_id' => $user->id,
            'reference' => $payment_data['reference'],
            'amount' => $payment_data['amount'],
            'response_description' => 'pending',
            'response_code' => '11',
            'response_full' => '{}'
        ]);

        $response = $this->paystack->initialize([
            'amount' => $payment_data['amount'],
            'email' => $user->email,
            'transaction_ref' => $payment_data['reference'],
        ]);

        if($response['status']){
            $transaction->update([
                'authorization_url' => $response['data']['authorization_url'],
                'access_code' => $response['data']['access_code']
            ]);
        }

        return $response['data']['authorization_url'];
    }

    public function verify($request) {
        $reference = $request->reference;
        $trxref = $request->trxref;
        // $user = auth()->user();
        $response = $this->paystack->verify($reference);
        $transaction = Transaction::where('reference', $reference)->first();

        if(isset($response['status']) && $response['status'] == true) {

            // update transaction table
            $transaction->update([
                'response_code' => '00',
                'response_description' => $response['message'],
                'payment_status' => 'successful', //strtolower($response['data']['gateway_response']),
                'response_full' => $response,
            ]);

            // send notification to admin and user

            return $this->response('Payment Successful');
        }
        else{
            // update transaction table
            $transaction->update([
                'response_code' => '11',
                'response_description' => 'failed',
                'response_full' => $response,
            ]);

           // update transaction request status if necessary

            // send notification to admin and user
            return $this->response('Payment Failed');
        }
    }

    public function verifyTopup($request) {
        $reference = $request->reference;
        $user = auth()->user();
        $response = $this->paystack->verify($reference);

        if(isset($response['status']) && $response['status'] === true) {
            // dd($response);
            $transaction = Transaction::create([
                'id' => $this->generateTransactionId(),
                'user_id' => $user->id,
                'reference' => $reference,
                'amount' => $response['data']['amount']
            ]);

            $transaction->update([
                'response_code' => '00',
                'response_description' => $response['message'],
                'payment_status' => 'successful',// strtolower($response['data']['gateway_response']),
                'response_full' => $response,
            ]);

            // update wallet balance and wallet transaction
            $user->wallet()->update([
                'balance' => $user->wallet->balance + $response['data']['amount'],
            ]);

            $transaction->wallet_transaction()->create([
                'id' => $this->generateWalletTransactionId(),
                'user_id' => $user->id,
                'wallet_id' => $user->wallet->id,
                'new_balance' => $user->wallet->balance + $response['data']['amount'],
                'amount' => $response['data']['amount'],
                'type' => 'credit',
                'description' => 'Wallet Topup'
            ]);


            // send notification to user

            return $this->response('Payment Successful');
        }
        else{
            // update wallet transaction to failed
            $transaction = Transaction::create([
                'id' => $this->generateTransactionId(),
                'user_id' => $user->id,
                'reference' => $reference,
                'amount' => $response['data']['amount'],
            ]);

            $transaction->update([
                'response_code' => '11',
                'response_description' => 'failed',
                'payment_status' => 'failed',//strtolower($response['data']['gateway_response']),
                'response_full' => $response,
            ]);

            // update transaction request status if necessary

            // send notification to admin and user
            return $this->response('Payment Failed');
        }
    }

}