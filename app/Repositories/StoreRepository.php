<?php

namespace App\Repositories;

use App\Common\Repository\BaseRepository;
use App\Models\Store;
use App\Models\User;

/**
 * @extends BaseRepository<Store>
 */

class StoreRepository extends BaseRepository
{
    
    public function __construct(Store $store)
    {
        $this->model = $store;
    }

    public function getAllStores(User $user, int $perPage = 15, ?string $name = null, ?bool $online = null, ?string $location = null)
    {
        $stores = $user->company()->first()->stores()
            ->when($name, function ($query, $name) {
                $query->where('name', 'LIKE', "%$name%");
            })
            ->when($location, function ($query, $location) {
                $query->where('location', $location);
            })
            ->when(! is_null($online), function ($query) use ($online) {
                $query->where('online', $online);
            })
            ->simplePaginate($this->perPage($perPage));

        return $this->formatPagination($stores);
    }

    public function getTenantStore(User $user)
    {
        $store = $user->store;

        return $store;
    }
}
