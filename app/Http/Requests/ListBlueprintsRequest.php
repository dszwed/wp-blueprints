<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListBlueprintsRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'status' => ['sometimes', 'string', 'in:public,private'],
            'php_version' => ['sometimes', 'string', 'in:8.2,8.1,8.0,7.4,7.3,7.2,7.1,7.0'],
            'wordpress_version' => ['sometimes', 'string', 'in:6.8,6.7,6.6,6.5,6.4,6.3,6.2,6.1,6.0,5.9,5.8,5.7,5.6,5.5,5.4,5.3,5.2,5.1,5.0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'per_page.max' => 'The number of items per page cannot exceed 100.',
            'status.in' => 'The status must be either public or private.',
            'php_version.in' => 'The PHP version is not supported.',
            'wordpress_version.in' => 'The WordPress version is not supported.',
        ];
    }
} 