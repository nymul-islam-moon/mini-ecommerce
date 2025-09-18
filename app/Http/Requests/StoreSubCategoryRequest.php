<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class StoreSubCategoryRequest extends FormRequest
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
            'name'          => 'sub-category name',
            'category_id'   => 'category',
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
            'name'          => ['required', 'string', 'max:255', 'unique:sub_categories,name'],
            'category_id'   => ['required', 'integer', 'exists:categories,id'],
            'is_active'     => ['boolean'],
            'slug'          => ['required', 'string', 'max:255', 'unique:sub_categories,slug']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Please enter a :attribute.',
            'name.unique'               => 'The :attribute ":input" is already in use.',
            'name.max'                  => 'The :attribute may not be greater than :max characters.',
            'category_id.required'      => 'Please select a :attribute',
            'category_id.exists'        => 'The selected :attribute is invalid.'
        ];
    }


    protected function prepareForValidation()
    {
        $this->merge([
            'name'          => Str::ucfirst(Str::lower($this->input('name'))),
            'category_id'   => (int) $this->input('category_id'),
            'is_active'     => $this->boolean('is_active'),
            'slug'          => Str::slug($this->input('name')),
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
