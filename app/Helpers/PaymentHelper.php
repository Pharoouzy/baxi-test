<?php
/**
 * Created by PhpStorm.
 * User: umar-farouq
 * Date: 9/21/20
 * Time: 06:39 PM
 */

namespace App\Helpers;
use App\Models\Transaction;
use App\Models\WalletTransaction;
use Illuminate\Support\Str;

trait PaymentHelper {

    public function getPaymentData($request){

        if($request){

            $data = [
                'subTotal' =>  $request->price,
                'amount' => $request->price,
                'total' => 1,
                'total_item' => 1,
                'delivery_fee' => (int)config('settings.base_fare'),
                'reference' => $this->generateTransactionReference()
            ];

            return $data;
        }

        return null;
    }

    public function generateTransactionReference(){
        $reference = 'SJX-TRF-'.substr(hexdec(uniqid()), -12);

        if(Transaction::where('reference', $reference)->exists()){
            return $this->generateTransactionReference();
        }

        return $reference;
    }

    public function generateTransactionId(){
        $id = substr(md5(substr(hexdec(uniqid()), -6)), -24);

        if(Transaction::query()->where('id', $id)->exists()){
            return $this->generateTransactionId();
        }

        return $id;
    }

    public function generateWalletTransactionId(){
        $id = substr(md5(substr(hexdec(uniqid()), -6)), -24);

        if(WalletTransaction::query()->where('id', $id)->exists()){
            return $this->generateWalletTransactionId();
        }

        return $id;
    }
}