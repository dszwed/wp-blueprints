<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Blueprint\CreateBlueprintRequest;
use App\Http\Requests\Blueprint\UpdateBlueprintRequest;
use App\Http\Resources\BlueprintResource;
use App\Models\Blueprint;
use App\Services\BlueprintService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class BlueprintController extends Controller
{
    public function __construct(
        private readonly BlueprintService $blueprintService
    ) {
    }

    public function store(CreateBlueprintRequest $request): RedirectResponse
    {
        $blueprint = $this->blueprintService->create(
            $request->validated(),
            $request->user()
        );

        $blueprintData = new BlueprintResource($blueprint);

        return redirect()->route('generator')->with('data', $blueprintData);
    }

    public function update(UpdateBlueprintRequest $request, Blueprint $blueprint): RedirectResponse
    {
        $updatedBlueprint = $this->blueprintService->update(
            $blueprint,
            $request->validated(),
            $request->user()
        );

        $blueprintData = new BlueprintResource($updatedBlueprint);

        return redirect()->route('generator')->with('data', $blueprintData);
    }
} 