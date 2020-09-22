<?php
/**
 * Created by PhpStorm.
 * User: umar-farouq
 * Date: 9/21/20
 * Time: 06:33 PM
 */

namespace App\Helpers;

use App\Models\User;

/**
 * Trait VerificationHelper
 * @package App\Helpers
 */
trait VerificationHelper
{
    /**
     * @return int
     */
    public function generateVerificationCode(){
        $verification_code = mt_rand(100000, 999999);

        if($this->verificationCodeExists($verification_code)){
            return $this->generateVerificationCode();
        }

        return $verification_code;
    }

    /**
     * @param $veification_code
     * @return mixed
     */
    public function verificationCodeExists($veification_code){
        return User::where('verification_code', $veification_code)->exists();
    }

    /**
     * @param $user
     * @return string
     */
    public function encrypt($user){
        $encrypted_data = base64_encode(strtotime($user->created_at).','.$user->email);
        return $encrypted_data;
    }

    /**
     * @param $data
     * @return bool|string
     */
    public function decrypt($data){
        $decrypted_data = base64_decode($data);
        $email = substr($decrypted_data, strpos($decrypted_data, ',') + 1);
        return $email;
    }
}