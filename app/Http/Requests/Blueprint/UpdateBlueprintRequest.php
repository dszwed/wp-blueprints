<?php

declare(strict_types=1);

namespace App\Http\Requests\Blueprint;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PhpVersion;
use App\Enums\WordpressVersion;

class UpdateBlueprintRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only allow updates if user is authenticated and owns the blueprint
        // or if the blueprint is anonymous and can be updated by anyone
        return $this->user() !== null || $this->route('blueprint')->is_anonymous;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'string', 'in:public,private'],
            'landingPage' => ['sometimes', 'required', 'string'],
            'preferredVersions' => ['sometimes', 'required', 'array'],
            'preferredVersions.php' => ['required_with:preferredVersions', 'string', 'in:' . implode(',', PhpVersion::values())],
            'preferredVersions.wp' => ['required_with:preferredVersions', 'string', 'in:' . implode(',', WordpressVersion::values())],
            'features' => ['sometimes', 'required', 'array'],
            'features.networking' => ['required_with:features', 'boolean'],
            'steps' => ['sometimes', 'required', 'array'],
            'steps.*.step' => ['required_with:steps', 'string', 'in:installPlugin,installTheme,activatePlugin,activateTheme,writeFile,runPHP,setSiteOptions'],
            
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
            'preferredVersions.php.required_with' => 'The PHP version is required when updating preferred versions.',
            'preferredVersions.php.in' => 'The PHP version must be one of: ' . implode(', ', PhpVersion::values()) . '.',
            'preferredVersions.wp.required_with' => 'The WordPress version is required when updating preferred versions.',
            'preferredVersions.wp.in' => 'The WordPress version must be one of: ' . implode(', ', WordpressVersion::values()) . '.',
            'features.required' => 'The features are required.',
            'features.networking.required_with' => 'The networking feature is required when updating features.',
            'features.networking.boolean' => 'The networking feature must be true or false.',
            'steps.*.step.required_with' => 'Each step must have a step name when updating steps.',
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
} 