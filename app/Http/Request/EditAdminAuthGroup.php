<?php

namespace App\Http\Request;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Yo;

class EditAdminAuthGroup extends FormRequest
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
      'name' => ['required', 'between:1,20'],
      'remark' => ['between:0,100'],
    ];
  }

  public function messages()
  {
    return [
      'name.required' => 100017,
      'name.between' => 100018,
      'remark.between' => 100019,
    ];
  }

  public function failedValidation(Validator $validator)
  {
    Yo::error_echo($validator->errors()->first());
  }
}
