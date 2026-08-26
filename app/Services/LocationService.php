<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\LocationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocationService extends BaseService
{
    protected LocationRepositoryInterface $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function getAllLocations(int $perPage = 15): LengthAwarePaginator
    {
        return $this->locationRepository->paginate($perPage);
    }

    public function getLocation(int $id): ?Model
    {
        return $this->locationRepository->find($id);
    }

    public function createLocation(array $data): Model
    {
        return $this->locationRepository->create($data);
    }

    public function updateLocation(int $id, array $data): bool
    {
        return $this->locationRepository->update($id, $data);
    }

    public function deleteLocation(int $id): bool
    {
        return $this->locationRepository->delete($id);
    }
}
