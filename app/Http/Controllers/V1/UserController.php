<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\EmailHelper;
use App\Helpers\RequestHelper;
use App\Helpers\VerificationHelper;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserController
 * @package App\Http\Controllers\V1
 */
class UserController extends Controller {

    use RequestHelper, VerificationHelper, EmailHelper;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request) {

        $user = User::where('id', auth()->user()->id)->first();

        return $this->response('Data retrieved successfully', $user);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request) {

        $user = User::find(auth()->user()->id);

        $phone = ($request->phone_number && $user->phone_number == $request->phone_number) ? '' : '|unique:users,phone_number';
        $validator = $this->customValidator($request, [
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'other_name' => 'sometimes|nullable|string',
            'phone_number' => 'sometimes|string|min:11|max:11'.$phone,
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = $this->updateUser($request, $user);

        return $this->response('Profile updated successfully.', $user);
    }

    /**
     * @param $request
     * @param $user
     * @return mixed
     */
    private function updateUser($request, $user){
        $user->update($request->only(['first_name', 'last_name', 'other_name', 'phone_number']));

        return $user;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request){

        $validator = $this->customValidator($request, [
            'old_password' => 'required|string|min:6',
            'password' => 'required|min:6|string|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = User::find(auth()->user()->id);

        if (!Hash::check($request->old_password, $user->password)) {
            return $this->validateCustomError(['old_password' => ['The old password is invalid.']]);
        }

        $user->update(['password' => bcrypt($request->password)]);

        return $this->response('Password changed successfully');

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(Request $request) {
        $users = User::orderBy('first_name', 'desc')->get();

        return $this->response('Success.', $users);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, $id) {

        $request['id'] = $id;

        $validator = $this->customValidator($request, [
            'id' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = User::where('id', $id)->first();

        return $this->response('Success.', $user);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id) {
        $request['id'] = $id;

        $validator = $this->customValidator($request, [
            'id' => 'required|string|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = User::find($id);

        if ($user->id == $request->user()->id) {
            return $this->customResponse('Unable to delete user account.');
        }

        $user->delete();

        return $this->response('User deleted successfully!');
    }
}
