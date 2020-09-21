<?php
/**
 * Created by PhpStorm.
 * User: umar-farouq
 * Date: 9/21/20
 * Time: 06:31 PM
 */

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;

/**
 * Trait RequestHelper
 * @package App\Helpers
 */
trait RequestHelper
{

    /**
     * @param $validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationError($validator) {
        return $this->validateCustomError($validator->errors());
    }

    /**
     * @param $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateCustomError($errors) {
        return $this->customResponse('An error occurred.', ['errors'=> $errors], 422);
    }

    /**
     * @param $request
     * @param array $validation_rules
     * @param array $messages
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function customValidator($request, Array $validation_rules, Array $messages = []) {
        return Validator::make($request->all(), $validation_rules, $messages);
    }

    /**
     * @param $message
     * @param int $status_code
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function response($message, $data = [], $status_code = 200)
    {
        $general_data = !empty($data) ? array_merge(['message' => $message], ['data' => $data]) : ['message' => $message];

        return response()->json($general_data, $status_code);
    }

    /**
     * @param $message
     * @param array $data
     * @param int $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function customResponse($message, $data = [], $status_code = 200)
    {
        $general_data = !empty($data) ? array_merge(['message' => $message], $data) : ['message' => $message];

        return response()->json($general_data, $status_code);
    }


}