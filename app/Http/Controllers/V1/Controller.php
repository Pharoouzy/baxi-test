<?php

namespace App\Http\Controllers\V1;

use App\Models\Schedule;
use App\Models\Transaction;
use App\Models\TransactionRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 * @package App\Http\Controllers\V1
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $model
     * @return bool|string
     */
    public function generateId($model){
        $id = substr(hexdec(uniqid()), -6);

        if($model->where('id', $id)->exists()){
            return $this->generateId($model);
        }

        return $id;
    }

    /**
     * @return string
     */
    public function generateAgentReference(){
        $reference = 'ARF-'.substr(hexdec(uniqid()), -12);

        if(Transaction::where('reference', $reference)->exists()){
            return $this->generateAgentReference();
        }

        return $reference;
    }
}
