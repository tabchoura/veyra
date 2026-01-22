<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductInitializationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Add your authorization logic here
    }

    /**
     * Get the validation rules
     */
    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            // Required fields
            'product_name' => 'required|string|max:255',
            'product_image' => $isUpdate 
                ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120' 
                : 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'weight' => 'required|numeric|min:0.01',
            'declaring_organization' => 'required|string|max:255',
            'organization_country_id' => 'required|exists:countries,id',
            'item_description' => 'required|string|max:3000',
            
            // Optional fields
            'batch_serial_number' => 'nullable|string|max:255',
            'prodcom_code' => 'nullable|string|max:255',
            'organization_address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'product_name.required' => 'Product name is required',
            'product_name.max' => 'Product name cannot exceed 255 characters',
            
            'product_image.required' => 'Product image is required',
            'product_image.image' => 'The file must be an image',
            'product_image.mimes' => 'Image must be in JPG, PNG, or WEBP format',
            'product_image.max' => 'Image size cannot exceed 5MB',
            
            'weight.required' => 'Weight is required',
            'weight.numeric' => 'Weight must be a number',
            'weight.min' => 'Weight must be a positive value',
            
            'declaring_organization.required' => 'Declaring organization is required',
            
            'organization_country_id.required' => 'Organization country is required',
            'organization_country_id.exists' => 'Selected country is invalid',
            
            'item_description.required' => 'Item description is required',
            'item_description.max' => 'Description cannot exceed 3000 characters',
            
            'postal_code.max' => 'Postal code cannot exceed 20 characters',
        ];
    }
}