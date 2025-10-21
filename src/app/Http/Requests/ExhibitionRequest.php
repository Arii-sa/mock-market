<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name'        => ['required', 'string', 'max:255'],
            'brand'       => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'img_url'     => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'categories'  => ['required', 'array'],   // 複数カテゴリ
            'condition_id'   => ['required', 'exists:conditions,id'],
            'price'       => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'        => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max'      => '商品説明は255文字以内で入力してください',
            'img_url.required'     => 'アップロード必須',
            'img_url.image'        => '商品画像は画像ファイルでアップロードしてください',
            'img_url.mimes'        => '商品画像は.jpg, .jpeg, .png 形式でアップロードしてください',
            'categories.required'  => '商品のカテゴリーを選択してください',
            'condition.required'   => '商品の状態を選択してください',
            'price.required'       => '商品価格を入力してください',
            'price.integer'        => '商品価格は数値で入力してください',
            'price.min'            => '商品価格は1円以上で入力してください',
        ];
    }
}
