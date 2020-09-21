<?php
/**
 * Created by PhpStorm.
 * User: umar-farouq
 * Date: 9/21/20
 * Time: 06:34 PM
 */

namespace App\Helpers;

use App\Mail\EmailVerificationSuccessful;
use Exception;
use App\Mail\EmailVerification;
use App\Mail\PasswordResetRequest;
use App\Mail\PasswordResetSuccess;
use Illuminate\Support\Facades\Mail;
use App\Mail\CreateTransactionRequest;

/**
 * Trait EmailHelper
 * @package App\Helpers
 */
trait EmailHelper {

    /**
     * @param $user
     * @param $data
     */
    public function sendPasswordResetRequest($user, $data, $url) {
        try {
            Mail::to($user->email)->queue(new PasswordResetRequest($data, $url));
        }
        catch (Exception $exception){
            abort(503, 'Unable to send email due to connection error.');
        }
    }

    /**
     * @param $user
     */
    public function sendPasswordResetSuccess($user) {
        try {
            Mail::to($user->email)->queue(new PasswordResetSuccess($user));
        }
        catch (Exception $exception){
            abort(503, 'Unable to send email due to connection error. But everything looks good.');
        }
    }

    public function sendVerificationCode($user, $url, $staff = false){
        try {
            Mail::to($user->email)->queue(new EmailVerification($user, $url, $staff));
        }
        catch (Exception $exception){
            abort(503, 'Unable to send email due to connection error. But everything looks good.');
        }
    }

    public function sendVerificationSuccess($user, $url) {
        try {
            Mail::to($user->email)->queue(new EmailVerificationSuccessful($user, $url));
        }
        catch (Exception $exception) {
            abort(503, 'Unable to send email due to connection error. But everything looks good.');
        }
    }

    public function sendCreateTransactionRequest($admins, $data, $backoffice = false){
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->queue(new CreateTransactionRequest($data, $admin, $backoffice));
            } catch (Exception $exception) {
                abort(503, 'Unable to send email due to connection error.');
            }
        }


    }

    public function sendCustomerCreateTransactionRequest($user, $data, $backoffice = true){

        try {
            Mail::to($user->email)->queue(new CreateTransactionRequest($data, $user, $backoffice));
        } catch (Exception $exception) {
            abort(503, 'Unable to send email due to connection error...');
        }

    }


}