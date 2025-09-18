<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected $stopOnFirstFailure = true;

    public function attributes(): array
    {
        return [
            'name'          => 'category name',
            'is_active'     => 'status',
            'description'   => 'description',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255', 'unique:categories,name'],
            'is_active'        => ['boolean'],
            'slug'          => ['required', 'string', 'max:255', 'unique:categories,slug'],
            'description'   => ['nullable', 'string', 'max:255']
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a :attribute.',
            'name.unique'   => 'The :attribute ":input" is already in use.',
            'name.max'      => 'The :attribute may not be greater than :max characters.',
        ];
    }


    protected function prepareForValidation()
    {
        $this->merge([
            'name'      => Str::ucfirst(Str::lower($this->input('name'))),
            'is_active' => $this->boolean('is_active'),
            'slug'   => Str::slug($this->input('name')),
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        // Log all errors
        Log::error('Category Store validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input'  => $this->all(),
        ]);

        // Optional: throw exception so the usual redirect with errors happens
        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
