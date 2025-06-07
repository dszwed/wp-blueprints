<?php

declare(strict_types=1);

namespace App\Http\Requests\Blueprint;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PhpVersion;
use App\Enums\WordpressVersion;

class CreateBlueprintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Both authenticated and anonymous users can create blueprints
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:public,private'],
            'landingPage' => ['required', 'string'],
            'preferredVersions' => ['required', 'array'],
            'preferredVersions.php' => ['required', 'string', 'in:' . implode(',', PhpVersion::values())],
            'preferredVersions.wp' => ['required', 'string', 'in:' . implode(',', WordpressVersion::values())],
            'features' => ['required', 'array'],
            'features.networking' => ['required', 'boolean'],
            'steps' => ['required', 'array'],
            'steps.*.step' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The blueprint name is required.',
            'name.max' => 'The blueprint name cannot exceed 255 characters.',
            'status.required' => 'The blueprint status is required.',
            'status.in' => 'The blueprint status must be either public or private.',
            'steps.required' => 'The steps object is required.',
            'steps.array' => 'The steps must be an array.',
            'landingPage.required' => 'The landing page is required.',
            'landingPage.string' => 'The landing page must be a string.',
            'preferredVersions.required' => 'The preferred versions are required.',
            'preferredVersions.php.required' => 'The PHP version is required.',
            'preferredVersions.php.in' => 'The PHP version must be one of: ' . implode(', ', PhpVersion::values()) . '.',
            'preferredVersions.wp.required' => 'The WordPress version is required.',
            'preferredVersions.wp.in' => 'The WordPress version must be one of: ' . implode(', ', WordpressVersion::values()) . '.',
            'features.required' => 'The features are required.',
            'features.networking.required' => 'The networking feature is required.',
            'features.networking.boolean' => 'The networking feature must be true or false.',
            'steps.*.step.required' => 'Each step must have a step name.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', 'public'),
        ]);
    }
} 