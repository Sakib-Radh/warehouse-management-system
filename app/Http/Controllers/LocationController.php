<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Http\Resources\LocationResource;
use App\Services\LocationService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $locations = $this->locationService->getAllLocations($perPage);
        return LocationResource::collection($locations);
    }

    public function store(LocationRequest $request): LocationResource
    {
        $location = $this->locationService->createLocation($request->validated());
        return new LocationResource($location);
    }

    public function show(int $id): LocationResource|JsonResponse
    {
        $location = $this->locationService->getLocation($id);

        if (! $location) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        return new LocationResource($location);
    }

    public function update(LocationRequest $request, int $id): LocationResource|JsonResponse
    {
        $updated = $this->locationService->updateLocation($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        $location = $this->locationService->getLocation($id);
        return new LocationResource($location);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->locationService->deleteLocation($id);

        if (! $deleted) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        return response()->json(null, 204);
    }
}
