<?php

namespace App\Http\Controllers\V1;

use App\Models\Schedule;
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

    public function generateId($model){
        $id = substr(hexdec(uniqid()), -6);

        if($model->where('id', $id)->exists()){
            return $this->generateId($model);
        }

        return $id;
    }
}
