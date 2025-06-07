<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListBlueprintsRequest;
use App\Http\Requests\Blueprint\CreateBlueprintRequest;
use App\Http\Resources\BlueprintResource;
use App\Services\BlueprintService;
use Illuminate\Http\JsonResponse;

class BlueprintController extends Controller
{
    public function __construct(
        private readonly BlueprintService $blueprintService
    ) {
    }

    /**
     * Get paginated list of blueprints.
     */
    public function index(ListBlueprintsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->blueprintService->getPaginatedList(
            filters: array_filter([
                'status' => $validated['status'] ?? null,
                'php_version' => $validated['php_version'] ?? null,
                'wordpress_version' => $validated['wordpress_version'] ?? null,
            ]),
            perPage: (int) ($validated['per_page'] ?? 15),
            page: (int) ($validated['page'] ?? 1)
        );

        return response()->json($result);
    }

    public function store(CreateBlueprintRequest $request): JsonResponse
    {
        $blueprint = $this->blueprintService->create(
            $request->validated(),
            $request->user()
        );

        return new JsonResponse([
            'data' => new BlueprintResource($blueprint)
        ], 201);
    }
} 