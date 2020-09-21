<?php
/**
 * Created by PhpStorm.
 * User: umar-farouq
 * Date: 9/21/20
 * Time: 06:32 PM
 */

namespace App\Helpers;


use Illuminate\Support\Facades\Hash;

trait AuthHelper
{
    public function checkPassword($request, $password){
        $hashed_password = $request->user()->password;

        if (!Hash::check($password, $hashed_password)){
            abort(403, 'Wrong password combination.');
        }

        return true;
    }

    public function randomPassword(){
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@!#$%&)(+';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i=0; $i<8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }
}