<?php

namespace App\Http\Requests;

use App\Traits\Strings;
use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    use Strings;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'mobile' => ['required', 'size:11', 'regex:/^[0-9]{11}$/'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'amount' => ['required', 'numeric', 'between:1000,1000000000'],
            'gateway' => ['required'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('mobile')) {
            $this->merge([
                'mobile' => $this->convertToEnglishDigits($this->input('mobile')),
            ]);
        }

        if ($this->has('amount')) {
            $this->merge([
                'amount' => $this->convertToEnglishDigits($this->input('amount')),
            ]);
        }
    }

    /**
     * Customize the validation messages.
     */
    public function messages(): array
    {
        return [
            'mobile.regex' => __('validation.custom.mobile.regex'),
            'amount.between' => __('validation.custom.amount.between'),
        ];
    }
}
