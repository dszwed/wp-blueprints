<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Blueprint;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class BlueprintRepository
{
    /**
     * Get paginated list of blueprints with optional filters.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getPaginatedList(array $filters = [], int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $query = Blueprint::query()
            ->with(['user', 'statistics'])
            ->when(isset($filters['status']), function (Builder $query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(isset($filters['php_version']), function (Builder $query) use ($filters) {
                $query->where('php_version', $filters['php_version']);
            })
            ->when(isset($filters['wordpress_version']), function (Builder $query) use ($filters) {
                $query->where('wordpress_version', $filters['wordpress_version']);
            });

        return $query->paginate(
            perPage: min($perPage, 100),
            page: $page
        );
    }
} 