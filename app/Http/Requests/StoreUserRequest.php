<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'=>'required|email|ends_with:go.tz|unique:users,email',
            'password'=>'required|alpha_num|min:8',
            'user_name'=>'min:3|required',
            'phone_number'=>'required|integer|starts_with:255',
            'profile_image'=>'mimes:jpg,png,jpeg,gif',
            'signature_image'=>'mimes:jpg,png,jpeg',
        ];
    }
}
