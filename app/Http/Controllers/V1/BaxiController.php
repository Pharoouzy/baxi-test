<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Services\Baxi;
use App\Helpers\EmailHelper;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\RequestHelper;

/**
 * Class BaxiController
 * @package App\Http\Controllers\V1
 */
class BaxiController extends Controller
{
    use RequestHelper, EmailHelper;

    /**
     * @var Baxi
     */
    private $baxi;

    /**
     * Paystack constructor.
     * @param string $env
     */
    public function __construct() {
        $this->baxi = new Baxi();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getElectricityBillers(){
        $billers = $this->baxi->getElectricityBillers();

        return $this->response('Success', $billers);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProviderBouquets(){
        $bouquets = $this->baxi->getProviderBouquets();

        return $this->response('Success', $bouquets);
    }

    /**
     * Requery Pending transaction
     * @param Request $request
     * @param $reference
     * @return \Illuminate\Http\JsonResponse
     */
    public function requeryTransaction(Request $request, $reference){

        $request['reference'] = $reference;

        $validator = $this->customValidator($request, [
            'reference' => 'required|string|exists:transactions,reference',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $transaction = Transaction::where([
            'reference' => $request->reference,
            'transaction_status' => 'pending'
        ])->first();

        if(!$transaction) {
            return $this->response('Transaction has already been handled.', [], 422);
        }

        $user = User::find(auth()->user()->id);

        $response = $this->baxi->requeryTransaction($request->reference);

        if(isset($response['statusCode']) && $response['statusCode'] === '0') {
            $transaction->update([
                'baxi_reference' => $response['baxiReference'],
                'transaction_status' => $response['transactionStatus'],
                'token_code' => $response['tokenCode'] ?? null,
                'transaction_description' => $response['transactionMessage'],
                'transaction_code' => $response['statusCode'],
                'amount_of_power' => $response['amountOfPower'] ?? null,
                'raw_output' => $response['rawOutput'] ?? '{}',
                'response_full' => $response,
            ]);

            $this->sendTransactionReceipt($user, $transaction);

            return $this->response('Success', $response);
        }

        return $this->response('error', $response, 422);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBouquetAddons(Request $request){

        $validator = $this->customValidator($request, [
            'product_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $addons = $this->baxi->getBouquetAddons($request->product_code);

        return $this->response('Success', $addons);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request){

        $validator = $this->customValidator($request, [
            'type' => 'required|string|in:prepaid,postpaid',
            'meter_number' => 'required|string|min:11|max:12'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        // $request['service_type'] = ($request->type === 'postpaid') ? 'enugu_electric_postpaid' : 'enugu_electric_prepaid';

        $request['service_type'] = ($request->type === 'postpaid') ? 'eko_electric_postpaid' : 'eko_electric_prepaid';

        $response = $this->baxi->verifyMeterNumber($request->all());
        $user = User::find(auth()->user()->id);
        if($response['status'] === 'success'){
            $response = $response['data'];
            $transaction = $user->transactions()->create([
                'id' => $this->generateId(Transaction::query()),
                'reference' => $this->generateAgentReference(),
                'name' => $response['name'],
                'service_type' => $request->service_type,
                'address' => $response['address'],
                'meter_number' => $response['accountNumber'],
                'outstanding_balance' => $response['outstandingBalance'],
            ]);

            $data = array_merge([
                'reference' => $transaction->reference,
            ], $response);

            return $this->response('verification successful', $data);
        }

        return $this->response('error', $response, 422);
    }

    /**
     * @param Request $request
     * @param $reference
     * @return \Illuminate\Http\JsonResponse
     */
    public function rechargeElectricityBill(Request $request, $reference){

        $request['reference'] = $reference;

        $validator = $this->customValidator($request, [
            'reference' => 'required|string|exists:transactions,reference',
            'amount' => 'required|numeric|gt:0',
            'phone_number' => 'required|string|min:11|max:11'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $transaction = Transaction::where([
            'reference' => $request->reference,
            'transaction_status' => 'pending'
        ])->first();

        if(!$transaction) {
            return $this->response('Transaction has already been handled.', [], 422);
        }

        $response = $this->baxi->rechargeElectricityBill($request->all(), $transaction);
        $user = User::find(auth()->user()->id);

        if($response['status'] === 'success'){
            $response = $response['data'];
            $transaction->update([
                'transaction_status' => $response['transactionStatus'],
                'token_code' => $response['tokenCode'] ?? null,
                'token_amount' => $response['tokenAmount'] ?? $request->amount,
                'amount' => $response['tokenAmount'] ?? $request->amount,
                'phone_number' => $request->phone_number,
                'transaction_description' => $response['transactionMessage'],
                'transaction_code' => $response['statusCode'],
                'amount_of_power' => $response['amountOfPower'] ?? null,
                'raw_output' => $response['rawOutput'] ?? '{}',
                'response_full' => $response,
            ]);
            $this->sendTransactionReceipt($user, $transaction);
            return $this->response('recharge successful', $transaction);
        }
        else if($response['status'] === 'pending'){
            $transaction->update([
                'token_amount' => $request->amount,
                'amount' => $response['tokenAmount'] ?? $request->amount,
                'transaction_description' => $response['message'],
                'response_full' => $response,
            ]);
            $this->sendTransactionReceipt($user, $transaction);
            return $this->response($response['status'].' transaction', $transaction);
        }

        return $this->response('error', $response, 422);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request){

        $validator = $this->customValidator($request, [
            'addon_months_paid_for' => 'sometimes|nullable|string',
            'amount' => 'required|numeric|gt:0',
            'addon_code' => 'sometimes|nullable|string',
            'product_months_paid_for' => 'required|string',
            'product_code' => 'required|string',
            'smartcard_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = User::find(auth()->user()->id);

        $transaction = $user->transactions()->create([
            'id' => $this->generateId(Transaction::query()),
            'reference' => $this->generateAgentReference(),
            'service_type' => 'dstv'
        ]);

        $response = $this->baxi->subscribe($request->all(), $transaction);

        if($response['status'] === 'success'){
            $response = $response['data'];
            $transaction->update([
                'transaction_status' => $response['transactionStatus'],
                'amount' => $request->amount,
                'smartcard_number' => $request->smartcard_number,
                'transaction_description' => $response['transactionMessage'],
                'transaction_code' => $response['statusCode'],
                'addon_months_paid_for' => $request->addon_months_paid_for,
                'product_months_paid_for' => $request->product_months_paid_for,
                'addon_code' => $request->addon_code,
                'product_code' => $request->product_code,
                'response_full' => $response,
            ]);

            $this->sendTransactionReceipt($user, $transaction);

            return $this->response($response['transactionMessage'], $transaction);
        }

        return $this->response('error', $response, 422);
    }
}
