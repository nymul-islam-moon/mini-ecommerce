<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreProductRequest extends FormRequest
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
            'name'              => 'product name',
            'slug'              => 'slug',
            'price'             => 'price',
            'sale_price'        => 'sale price',
            'stock'             => 'stock',
            'is_active'         => 'status',
            'main_image'        => 'main image',
            'sub_category_id'   => 'sub-category',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => $this->slug ?? Str::slug($this->name),
            'is_active' => $this->has('is_active') ? (bool) $this->is_active : true,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255', 'unique:products,name', 'min:3'],
            'slug'            => ['required', 'string', 'max:255', 'unique:products,name', 'min:3'],
            'description'     => ['nullable', 'string'],
            'price'           => ['required', 'numeric', 'min:0'],
            'sale_price'      => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock'           => ['required', 'integer', 'min:0'],
            'is_active'       => ['boolean'],
            'main_image'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sub_category_id' => ['required', 'exists:sub_categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Please enter a :attribute.',
            'name.unique'               => 'The :attribute ":input" is already in use.',
            'name.max'                  => 'The :attribute may not be greater than :max characters',
            'name.min'                  => 'The :attribute must be at least :min characters',
            'slug.required'             => 'Please provide a :attribute.',
            'slug.unique'               => 'The :attribute ":input" is already in use.',
            'slug.max'                  => 'The :attribute may not be greater than :max characters',
            'slug.min'                  => 'The :attribute must be at least :min characters',
            'price.required'            => 'Please enter a :attribute',
            'price.numeric'             => 'The :attribute must be a valid number',
            'price.min'                 => 'The :attribute must be at least :min',
            'sale_price.numeric'        => 'The :attribute must be a valid number',
            'sale_price.min'            => 'The :attribute must be at least :min',
            'sale_price.lt'             => 'The :attribute must be less than the price',
            'stock.required'            => 'Please enter the :attribute',
            'stock.integer'             => 'The :attribute must be an integer',
            'stock.min'                 => 'The :attribute must be at least :min',
            'main_image.image'          => 'The :attribute must be an image file',
            'main_image.mimes'          => 'The :attribute must be a file of type : :values',
            'sub_category_id.required'  => 'Please select a :attribute',
            'sub_category_id.exists'    => 'The selected :attribute is invalid',
        ];
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
