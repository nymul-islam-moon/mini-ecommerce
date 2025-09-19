<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdateSubCategoryRequest extends FormRequest
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
            'name'          => ['required', 'string', 'max:255', 'unique:sub_categories,name,' . $this->sub_category->id],
            'category_id'   => ['required', 'integer', 'exists:categories,id'],
            'is_active'     => ['boolean'],
            'slug'          => ['required', 'string', 'max:255', 'unique:sub_categories,slug,' . $this->sub_category->id],
            'description'   => ['nullable', 'string']
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a :attribute.',
            'name.unique'   => 'The :attribute ":imput" is already in use.',
            'name.max'      => 'The :attribute may not be greater than :max characters.',
        ];
    }

    protected function prepareforValidation()
    {
        $this->merge([
            'name'      => $this->has('name') ? Str::ucfirst(Str::lower($this->input('name'))) : null,
            'slug'      => $this->has('name') ? Str::slug($this->input('name')) : null,
            'is_active' => $this->has('is_active') ? (bool)$this->input('is_active') : false,
        ]);
    }


    protected function failedValidation(Validator $validator)
    {
        // Log all errors
        Log::error('Sub-category Update validation failed', [
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
