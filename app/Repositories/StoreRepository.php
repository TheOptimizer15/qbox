<?php

namespace App\Repositories;

use App\Common\Repository\BaseRepository;
use App\Models\Store;

class StoreRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(Store $store)
    {
        $this->model = $store;
    }
}
