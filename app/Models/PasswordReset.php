<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PasswordReset
 * @package App\Models
 */
class PasswordReset extends Model
{
    protected $primaryKey = 'id';

    public $incrementing = false;
    /**
     * @var array
     */
    protected $fillable = ['id', 'email', 'token'];
}
