<?php

namespace App\Http\Controllers\V1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\EmailHelper;
use App\Helpers\RequestHelper;
use App\Helpers\VerificationHelper;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\V1\Controller;

/**
 * Class RegisterController
 * @package App\Http\Controllers\V1\Auth
 */
class RegisterController extends Controller
{
    use RequestHelper, EmailHelper, VerificationHelper;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(Request $request){

        $validator = $this->customValidator($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'other_name' => 'sometimes|nullable|string',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|min:11|max:11|unique:users,phone_number',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = $this->createUser($request);

        $url = config('app.main_url').config('app.verify_url').'/'.$this->encrypt($user);
        $this->sendVerificationCode($user, $url);

        return $this->response('Success', $user, 201);
    }

    /**
     * @param $request
     * @return mixed
     */
    private function createUser($request){
        $user = User::create([
            'id' => $this->generateId(User::query()),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_name' => $request->other_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'verification_code' => $this->generateVerificationCode(),
            'password' => Hash::make($request->password),
        ]);

        return $user;
    }

}
