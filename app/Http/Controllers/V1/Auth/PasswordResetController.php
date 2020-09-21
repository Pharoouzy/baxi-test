<?php

namespace App\Http\Controllers\V1\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\EmailHelper;
use App\Models\PasswordReset;
use App\Helpers\RequestHelper;
use Illuminate\Support\Carbon;
use App\Helpers\VerificationHelper;
use App\Http\Controllers\V1\Controller;

/**
 * Class PasswordResetController
 * @package App\Http\Controllers\V1\Auth
 */
class PasswordResetController extends Controller {

    use RequestHelper, EmailHelper, VerificationHelper;
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {

        $validator = $this->customValidator($request, [
            'email' => 'required|string|email|exists:users,email',
        ]);

        if($validator->fails()){
            return $this->validationError($validator);
        }

        $user = User::where('email', $request->email)->first();

        $passwordReset = PasswordReset::updateOrCreate(
            [
                'email' => $user->email
            ],
            [
                'id' => $this->generateId(PasswordReset::query()),
                'email' => $user->email,
                'token' => Str::Random(60)
            ]
        );

        if ($user && $passwordReset) {

            $data = [
                'user' => $user,
                'token' => $passwordReset->token
            ];

            $url = config('app.main_url').config('app.reset_url').'/'.$this->encrypt($user).'/'.$passwordReset->token;
            $this->sendPasswordResetRequest($user, $data, $url);
        }

        return $this->response('We have e-mailed your password reset link!');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request, $user) {

        $request['email'] = $this->decrypt(urldecode($user));

        $validator = $this->customValidator($request, [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);

        if($validator->fails()){
            return $this->validationError($validator);
        }

        $email = $request->email;

        $passwordReset = PasswordReset::where([['token', $request->token], ['email', $email]])->first();

        if (!$passwordReset) {
            return $this->response('Invalid password reset credentials.', [], 422);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if ($passwordReset->created_at->addMinutes(30)->timestamp >= Carbon::now()->timestamp) {
            $user->update([
                'status' => 'active',
                'password' => bcrypt($request->password),
            ]);
            $passwordReset->delete();

             $this->sendPasswordResetSuccess($user);

            return $this->response('Password Reset successfully.');
        }

        return $this->response('Token has expired.', [], 422);
    }
}
