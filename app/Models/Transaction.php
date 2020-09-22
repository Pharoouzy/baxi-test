<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaction
 * @package App\Models
 */
class Transaction extends Model
{
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'amount',
        'user_id',
        'address',
        'address',
        'raw_output',
        'reference',
        'token_code',
        'addon_code',
        'product_code',
        'phone_number',
        'meter_number',
        'token_amount',
        'service_type',
        'response_full',
        'amount_of_power',
        'transaction_code',
        'smartcard_number',
        'transaction_status',
        'outstanding_balance',
        'addon_months_paid_for',
        'product_months_paid_for',
        'transaction_description',
    ];

    /**
     * @var array
     */
    protected $hidden = ['response_full', 'raw_output'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\Models\User');
    }

}
