<?php

namespace Yangze\ModulesHelper\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use \Illuminate\Validation\ValidationException;
use Yangze\ModulesHelper\Exceptions\CommonUnauthorizedException;
use Yangze\ModulesHelper\Exceptions\CommonValidationException;
use Yangze\ModulesHelper\Lib\HttpCode;

class CommonFormRequest extends FormRequest
{
    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws Yangze\ModulesHelper\Exceptions\CommonUnauthorizedException
     */
    public function failedAuthorization()
    {
        throw new CommonUnauthorizedException(HttpCode::message('unauthorized'), HttpCode::code('unauthorized'));
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new CommonValidationException($validator->errors()->first(), HttpCode::code('fail validate'));
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
