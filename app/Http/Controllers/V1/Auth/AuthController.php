<?php

namespace App\Http\Controllers\V1\Auth;

use App\Models\User;
use App\Helpers\KeyHelper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\RequestHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Notifications\SignupSuccess;

/**
 * Class AuthController
 * @package App\Http\Controllers\V1\Auth
 */
class AuthController extends Controller
{
    use RequestHelper, KeyHelper;


    public function register(Request $request) {

        $validator = $this->customValidator($request, [
            'email' => 'email|required|unique:users,email',
            'password' => 'required|string|confirmed',
            'first_name' => 'required|string|max:25',
            'last_name' => 'required|string|max:25',
            'phone_number' => 'required|max:15',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'api_key' => 'sk_'.strtolower(uniqid(Str::Random(35))),
            'activation_token' => $this->generateAPIKey()
        ]);

        $user->notify(new SignupSuccess($user));

        return $this->customResponse('User signed up successfully!', 201, $user);
    }

    public function login(Request $request)
    {
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

            if ($user->status == 'disabled' && is_null($user->email_verified_at)) {
                return $this->customResponse('Account not activated, please check your inbox to activate your account.', 403);
            }
            else if($user->status == 'disabled' || $user->status == 'suspended') {

                $message = ($user->status == 'disabled') ? 'This account is Disabled.' : 'This account is temporarily suspended.';

                return $this->customResponse($message, 403);
            }

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

            return $this->customResponse('Authenticated.', 200, $data);
        }

        return $this->customResponse('Unauthorized credentials.', 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        $user = Auth::user();
        $userTokens = $user->tokens;

        foreach ($userTokens as $token) {
            $token->revoke();
        }

        return $this->customResponse('User logged out from API successfully.', 200);
    }

}
