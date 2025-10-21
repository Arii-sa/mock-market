<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => ['required', 'string', 'max:20'],
            'img_url'  => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'postcode' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'  => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'      => 'ユーザー名を入力してください',
            'name.max'           => 'ユーザー名は20文字以内で入力してください',
            'img_url.mimes'      => 'プロフィール画像は.jpeg または .png 形式でアップロードしてください',
            'postcode.required'  => '郵便番号を入力してください',
            'postcode.regex'     => '郵便番号は「123-4567」の形式で入力してください',
            'address.required'   => '住所を入力してください',
        ];
    }

}
