<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('id');

        return [
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'barcode' => ['nullable', 'string', 'max:50', Rule::unique('products', 'barcode')->ignore($productId)],
            'manufacturer' => ['nullable', 'string', 'max:100'],
            'category' => ['sometimes', 'required', 'string', Rule::in(Product::getCategories())],
            'stock_quantity' => ['sometimes', 'required', 'integer', 'min:0'],
            'min_stock_level' => ['nullable', 'integer', 'min:0'],
            'unit' => ['sometimes', 'required', 'string', Rule::in(Product::getUnits())],
            'unit_price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:9999999.99'],
            'suggested_price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do produto é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome não pode exceder 255 caracteres.',
            'sku.unique' => 'Este SKU já está em uso.',
            'sku.max' => 'O SKU não pode exceder 50 caracteres.',
            'barcode.unique' => 'Este código de barras já está em uso.',
            'barcode.max' => 'O código de barras não pode exceder 50 caracteres.',
            'category.required' => 'A categoria é obrigatória.',
            'category.in' => 'Categoria inválida.',
            'stock_quantity.required' => 'A quantidade em estoque é obrigatória.',
            'stock_quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'stock_quantity.min' => 'A quantidade não pode ser negativa.',
            'unit_price.required' => 'O preço de venda é obrigatório.',
            'unit_price.numeric' => 'O preço deve ser um número válido.',
            'unit_price.min' => 'O preço não pode ser negativo.',
            'unit_price.max' => 'O preço não pode exceder 9.999.999,99.',
        ];
    }
}
