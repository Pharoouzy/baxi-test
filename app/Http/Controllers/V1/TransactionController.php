<?php

namespace App\Http\Controllers\V1;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\RequestHelper;

/**
 * Class TransactionController
 * @package App\Http\Controllers\V1
 */
class TransactionController extends Controller
{

    use RequestHelper;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $transactions = auth()->user()->transactions;

        return $this->response('success', $transactions);
    }

    /**
     * @param Request $request
     * @param $reference
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, $reference){

        $request['reference'] = $reference;

        $validator = $this->customValidator($request, [
            'reference' => 'required|string|exists:transactions,reference',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $transaction = Transaction::where('reference', $request->reference)->first();

        return $this->response('success', $transaction);
    }
}