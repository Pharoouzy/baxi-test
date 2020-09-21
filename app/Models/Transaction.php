<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = ['id', 'reference', 'amount', 'user_id', 'transaction_request_id', 'authorization_url', 'access_code', 'response_code', 'response_description', 'payment_status', 'response_full'];

    protected $hidden = [];

    public function transaction_request(){
        return $this->belongsTo('App\Models\TransactionRequest');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

}
