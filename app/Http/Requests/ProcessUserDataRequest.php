<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProcessUserDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cpf' => 'required|string|size:11|regex:/^[0-9]+$/',
            'cep' => 'required|string|size:8|regex:/^[0-9]+$/',
            'email' => 'required|email:rfc,dns',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], 422));
    }
}