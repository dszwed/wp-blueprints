<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Blueprint;
use App\Models\User;
use App\Repositories\BlueprintRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BlueprintService
{
    public function __construct(
        private readonly BlueprintRepository $blueprintRepository
    ) {
    }

    /**
     * Get paginated list of blueprints with optional filters.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @param int $page
     * @return array{data: Collection, meta: array<string, mixed>}
     */
    public function getPaginatedList(array $filters = [], int $perPage = 15, int $page = 1): array
    {
        $paginator = $this->blueprintRepository->getPaginatedList($filters, $perPage, $page);

        return [
            'data' => collect($paginator->items())->map(fn (Blueprint $blueprint) => [
                'id' => $blueprint->id,
                'name' => $blueprint->name,
                'description' => $blueprint->description,
                'status' => $blueprint->status,
                'php_version' => $blueprint->php_version,
                'wordpress_version' => $blueprint->wordpress_version,
                'steps' => $blueprint->steps,
                'created_at' => $blueprint->created_at,
                'updated_at' => $blueprint->updated_at,
                'is_anonymous' => $blueprint->is_anonymous,
            ]),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    public function create(array $data, ?User $user = null): Blueprint
    {
        $blueprint = new Blueprint($data);
        
        if ($user) {
            $blueprint->user_id = $user->id;
            $blueprint->is_anonymous = false;
        } else {
            $blueprint->is_anonymous = true;
        }

        $blueprint->php_version = $data['preferredVersions']['php'] ?? null;
        $blueprint->wordpress_version = $data['preferredVersions']['wp'] ?? null;

        $blueprint->save();

        return $blueprint;
    }
} 