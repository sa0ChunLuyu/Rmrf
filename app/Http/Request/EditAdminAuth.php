<?php

namespace App\Http\Request;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Yo;

class EditAdminAuth extends FormRequest
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
      'name' => ['required', 'between:1,200'],
      'title' => ['required', 'between:1,20'],
      'icon' => ['between:0,100'],
      'message' => ['between:0,50'],
    ];
  }

  public function messages()
  {
    return [
      'name.required' => 100025,
      'name.between' => 100026,
      'title.required' => 100017,
      'title.between' => 100018,
      'icon.between' => 100027,
      'message.between' => 100028,
    ];
  }

  public function failedValidation(Validator $validator)
  {
    Yo::error_echo($validator->errors()->first());
  }
}
