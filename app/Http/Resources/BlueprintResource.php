<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlueprintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Blueprint $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'php_version' => $this->php_version,
            'wordpress_version' => $this->wordpress_version,
            'steps' => $this->steps,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_anonymous' => $this->is_anonymous,
        ];
    }
} 