<?php

namespace App\Services\Store;

use App\Enums\UserRole;
use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Repositories\StoreRepository;

class StoreService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected StoreRepository $storeRepository
    )
    {
        
    }

    public function getAllStores(User $user, int $perPage = 15){
        // if user is a cashier return the store he has been assigned
        if($user->role !== UserRole::OWNER){
            return $this->storeRepository->getTenantStore($user);
        }

        return $this->storeRepository->getAllStores($user, $perPage); 
    }

    public function getStore(string $id){
        $store = $this->storeRepository->findById($id);
        if(!$store){
            throw new NotFoundException('store not found');
        }
        return $store;
    }

    public function createStore(string $userId, array $storeData){
        $store = $this->storeRepository->create($storeData);
        return $store;
    }

    public function updateStore($storeId, array $storeData){
        $store = $this->storeRepository->findById($storeId);

        if(!$store){
            throw new NotFoundException('store not found');
        }

        $updatedStore = $this->storeRepository->update($store, $storeData);
        return $updatedStore;
    }

    public function deleteStore(string $storeId){
        $store = $this->storeRepository->findById($storeId);

        if(!$store){
            throw new NotFoundException('store not found');
        }

        return $store;
    }
}
