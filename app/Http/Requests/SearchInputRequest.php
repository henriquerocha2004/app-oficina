<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchInputRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'required|integer|min:1',
            'limit' => 'required|integer|min:1|max:100',
            'sort' => 'required|string|in:asc,desc',
            'sortField' => 'required|string',
            'search' => 'nullable|string',
            'columnSearch' => 'nullable|array',
        ];
    }
}
