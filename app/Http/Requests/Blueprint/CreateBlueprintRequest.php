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
            'steps.*.step' => ['required', 'string', 'in:installPlugin,installTheme,activatePlugin,activateTheme,writeFile,runPHP,setSiteOptions'],
            
            // Plugin-related steps validation
            'steps.*.pluginData' => ['required_if:steps.*.step,installPlugin,activatePlugin', 'array'],
            'steps.*.pluginData.resource' => ['required_if:steps.*.step,installPlugin,activatePlugin', 'string'],
            'steps.*.pluginData.slug' => ['required_if:steps.*.step,installPlugin,activatePlugin', 'string'],
            
            // Theme-related steps validation
            'steps.*.themeData' => ['required_if:steps.*.step,installTheme,activateTheme', 'array'],
            'steps.*.themeData.resource' => ['required_if:steps.*.step,installTheme,activateTheme', 'string'],
            'steps.*.themeData.slug' => ['required_if:steps.*.step,installTheme,activateTheme', 'string'],
            
            // File-related steps validation
            'steps.*.path' => ['required_if:steps.*.step,writeFile', 'string'],
            'steps.*.contents' => ['required_if:steps.*.step,writeFile', 'string'],
            
            // PHP code execution validation
            'steps.*.code' => ['required_if:steps.*.step,runPHP', 'string'],
            
            // Site options validation
            'steps.*.options' => ['required_if:steps.*.step,setSiteOptions', 'array'],
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
            'steps.*.step.in' => 'The step type must be one of: installPlugin, installTheme, activatePlugin, activateTheme, writeFile, runPHP, setSiteOptions.',
            
            // Plugin-related validation messages
            'steps.*.pluginData.required_if' => 'Plugin data is required for plugin-related steps.',
            'steps.*.pluginData.array' => 'Plugin data must be an array.',
            'steps.*.pluginData.resource.required_if' => 'Plugin resource is required for plugin-related steps.',
            'steps.*.pluginData.resource.string' => 'Plugin resource must be a string.',
            'steps.*.pluginData.slug.required_if' => 'Plugin slug is required for plugin-related steps.',
            'steps.*.pluginData.slug.string' => 'Plugin slug must be a string.',
            
            // Theme-related validation messages
            'steps.*.themeData.required_if' => 'Theme data is required for theme-related steps.',
            'steps.*.themeData.array' => 'Theme data must be an array.',
            'steps.*.themeData.resource.required_if' => 'Theme resource is required for theme-related steps.',
            'steps.*.themeData.resource.string' => 'Theme resource must be a string.',
            'steps.*.themeData.slug.required_if' => 'Theme slug is required for theme-related steps.',
            'steps.*.themeData.slug.string' => 'Theme slug must be a string.',
            
            // File-related validation messages
            'steps.*.path.required_if' => 'File path is required for writeFile steps.',
            'steps.*.path.string' => 'File path must be a string.',
            'steps.*.contents.required_if' => 'File contents are required for writeFile steps.',
            'steps.*.contents.string' => 'File contents must be a string.',
            
            // PHP code validation messages
            'steps.*.code.required_if' => 'PHP code is required for runPHP steps.',
            'steps.*.code.string' => 'PHP code must be a string.',
            
            // Site options validation messages
            'steps.*.options.required_if' => 'Options are required for setSiteOptions steps.',
            'steps.*.options.array' => 'Options must be an array.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', 'public'),
        ]);
    }
} 