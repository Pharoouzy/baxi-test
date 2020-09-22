<?php
/**
 * Created by PhpStorm.
 * User: umar-farouq
 * Date: 9/21/20
 * Time: 06:34 PM
 */

namespace App\Helpers;

use App\Mail\EmailVerificationSuccessful;
use App\Mail\TransactionReceipt;
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

    /**
     * @param $user
     * @param $url
     */
    public function sendVerificationCode($user, $url){
        try {
            Mail::to($user->email)->queue(new EmailVerification($user, $url));
        }
        catch (Exception $exception){
            abort(503, 'Unable to send email due to connection error. But everything looks good.');
        }
    }

    /**
     * @param $user
     * @param $url
     */
    public function sendVerificationSuccess($user, $url) {
        try {
            Mail::to($user->email)->queue(new EmailVerificationSuccessful($user, $url));
        }
        catch (Exception $exception) {
            abort(503, 'Unable to send email due to connection error. But everything looks good.');
        }
    }

    /**
     * @param $user
     * @param $data
     * @param string $type
     */
    public function sendTransactionReceipt($user, $data){
        try {
            Mail::to($user->email)->queue(new TransactionReceipt($user, $data));
        } catch (Exception $exception) {
            abort(503, 'Unable to send email due to connection error.');
        }

    }

}