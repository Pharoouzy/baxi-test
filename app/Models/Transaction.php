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
     * @var array
     */
    protected $appends = ['type', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return null|string
     */
    public function getTypeAttribute(){
        if(substr($this->service_type, -7) === 'prepaid')
            return 'Prepaid';
        else if(substr($this->service_type, -8) === 'postpaid')
            return 'Postpaid';
        else
            return null;
    }

    public function getStatusAttribute(){

        return ($this->transaction_status === 'success') ? 'successful' : $this->transaction_status;
    }

}
