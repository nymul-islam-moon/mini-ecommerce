<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdateProductRequest extends FormRequest
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
            'name'            => ['required', 'string', 'max:255', 'min:3', 'unique:products,name,' . $this->product->id],
            'slug'            => ['required', 'string', 'max:255', 'min:3', 'unique:products,slug,' . $this->product->id],
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
            'name.required'      => 'The product name is required.',
            'name.string'        => 'The product name must be a valid string.',
            'name.max'           => 'The product name may not be greater than :max characters.',
            'name.min'           => 'The product name must be at least :min characters.',
            'name.unique'        => 'This product name is already taken.',

            'slug.required'      => 'The product slug is required.',
            'slug.string'        => 'The product slug must be a valid string.',
            'slug.max'           => 'The product slug may not be greater than :max characters.',
            'slug.min'           => 'The product slug must be at least :min characters.',
            'slug.unique'        => 'This slug is already in use.',

            'description.string' => 'The description must be a valid string.',

            'price.required'     => 'The product price is required.',
            'price.numeric'      => 'The product price must be a number.',
            'price.min'          => 'The product price must be at least :min.',

            'sale_price.numeric' => 'The sale price must be a number.',
            'sale_price.min'     => 'The sale price must be at least :min.',
            'sale_price.lt'      => 'The sale price must be less than the regular price.',

            'stock.required'     => 'The stock quantity is required.',
            'stock.integer'      => 'The stock quantity must be an integer.',
            'stock.min'          => 'The stock quantity must be at least :min.',

            'is_active.boolean'  => 'The status field must be true or false.',

            'main_image.image'   => 'The main image must be an image file.',
            'main_image.mimes'   => 'The main image must be a file of type: :values.',
            'main_image.max'     => 'The main image size may not be greater than :max kilobytes.',

            'sub_category_id.required' => 'The subcategory is required.',
            'sub_category_id.exists'   => 'The selected subcategory is invalid.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Log all errors
        Log::error('Product Update validation failed', [
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
