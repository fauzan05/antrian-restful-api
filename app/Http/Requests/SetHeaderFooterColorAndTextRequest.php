<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SetHeaderFooterColorAndTextRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'header_color' => ['required','string',"max:7","min:7"],
            'footer_color' => ['required','string',"max:7","min:7"],
            'text_header_color' => ['required','string',"max:7","min:7"],
            'text_footer_color' => ['required','string',"max:7","min:7"]

        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response([
            'status' => 'Validation Error',
            'data' => null,
            'error' => [
                'error_message' => $validator->getMessageBag()
            ]      
        ], 400));
    }
}
