<?php

namespace App\Http\Controllers\V1\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\RequestHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\V1\Controller;

/**
 * Class LoginController
 * @package App\Http\Controllers\V1\Auth
 */
class LoginController extends Controller {

    use RequestHelper;
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {

        $validator = $this->customValidator($request, [
            'email' => 'email|required',
            'password' => 'required',
            'remember_me' => 'boolean|sometimes'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            if ($request->remember_me) {
                $user->remember_token = Str::random(16);
                $user->update();
            }

            $token_object =  $user->createToken(config('app.name'));

            $data = [
                'token' => $token_object->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $token_object->token->expires_at,
                'user' => $user,
            ];

            return $this->response('Authenticated', $data);
        }

        return $this->customResponse('Unauthorized credentials.', [], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request) {

        $request->user()->token()->revoke();
        $user = Auth::user();
        $userTokens = $user->tokens;

        foreach ($userTokens as $token) {
            $token->revoke();
        }

        return $this->response('User logged out from API successfully.');
    }

}
