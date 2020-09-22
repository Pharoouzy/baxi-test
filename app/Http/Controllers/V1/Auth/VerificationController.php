<?php

namespace App\Http\Controllers\V1\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Helpers\EmailHelper;
use Illuminate\Http\Request;
use App\Helpers\RequestHelper;
use App\Helpers\VerificationHelper;
use App\Http\Controllers\V1\Controller;

/**
 * Class VerificationController
 * @package App\Http\Controllers\V1\Auth
 */
class VerificationController extends Controller
{
    use RequestHelper, EmailHelper, VerificationHelper;

    /**
     * @param Request $request
     * @param $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $email){
        $validator = $this->customValidator($request, [
            'verification_code' => 'required|min:6|max:6'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $email = $this->decrypt(urldecode($email));

        $user = User::where(['email' => $email, 'verification_code' => $request->verification_code])->first();
        if ($user) {
            /**
             * activate only inactive users
             */
            if ($user->status == 'inactive') {
                /**
                 * activate only verification codes that have not exceeded 30 days
                 */
                if ($user->updated_at->addDays(30 )->timestamp >= Carbon::now()->timestamp) {
                    $user->status      = 'active';
                    $user->email_verified_at = Carbon::now();
                    $user->save();
                    $url = config('app.main_url').'/dashboard';
                    $this->sendVerificationSuccess($user, $url);
                    return $this->response('You account has been successfully activated.');
                }

                return $this->response('Verification code expired.', [], 403);
            }

            return $this->response( 'User is already active or suspended.', [], 401 );
        }

        return $this->response('Invalid verification credentials.', [], 422);
    }

    /**
     * @param Request $request
     * @param $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request, $email){

        $request['email'] = $this->decrypt(urldecode($email));

        $validator = $this->customValidator($request, [
            'email' => 'required|email|exists:users'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = User::where(['email' => $request->email, 'status' => 'inactive'])->first();

        if (!$user) {
            return $this->response('User not authorized.', [], 403);
        }

        $verification_code = $this->generateVerificationCode();

        $user->update([
          'verification_code' => $verification_code
        ]);
        $url = config('app.main_url').config('app.verify_url').'/'.$this->encrypt($user);
        $this->sendVerificationCode($user, $url);

        return $this->response( 'A new verification code has been sent to '.$request->email);
    }
}
