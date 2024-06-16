<?php

namespace App\Http\Request;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Yo;

class EditIpPool extends FormRequest
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
      'ip' => ['required', 'between:1,20'],
      'region' => ['required', 'between:1,50']
    ];
  }

  public function messages()
  {
    return [
      'ip.required' => 100036,
      'ip.between' => 100037,
      'region.required' => 100038,
      'region.between' => 100039,
    ];
  }

  public function failedValidation(Validator $validator)
  {
    Yo::error_echo($validator->errors()->first());
  }
}
