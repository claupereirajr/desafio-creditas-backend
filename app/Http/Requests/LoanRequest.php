<?php

namespace App\Http\Requests;

use App\Rules\ValidateCpf;
use App\Rules\ValidateState;
use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
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
            'customer.name' => 'required|string',
            'customer.cpf' => ['required', 'string', new ValidateCpf()],
            'customer.age' => 'required|integer|min:18|max:100',
            'customer.location' => ['required', 'string', 'size:2', 'uppercase', new ValidateState()], // State from Brazil
            'customer.income' => 'required|numeric',
        ];
    }
}
