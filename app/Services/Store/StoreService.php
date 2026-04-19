<?php

namespace App\Services\Store;

use App\Enums\UserRole;
use App\Exceptions\ForbiddenException;
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
    ) {}

    public function getAllStores(User $user, int $perPage = 15)
    {
        // if user is a cashier return the store he has been assigned
        if ($user->role == UserRole::CASHIER) {
            $store = $this->storeRepository->getTenantStore($user);

            return [
                $store,
                [],
            ];
        }

        $this->ownCompany($user);

        return $this->storeRepository->getAllStores($user, $perPage);
    }

    public function getStore(User $user, string $id)
    {
        if ($user->role == UserRole::CASHIER) {
            $store = $this->storeRepository->getTenantStore($user);

            return $store;
        }

        $this->ownCompany($user);

        $store = $this->storeRepository->findById($id);

        if (! $store) {
            throw new NotFoundException('store not found');
        }

        if ($user->company->id !== $store->company->id) {
            throw new ForbiddenException('you cannot load a store you do not own');
        }

        return $store;
    }

    public function createStore(User $user, array $data)
    {
        $this->ownCompany($user);

        $storeData = [
            ...$data,
            ...['company_id' => $user->company->id],
        ];
        $store = $this->storeRepository->create($storeData);

        return $store;
    }

    public function updateStore(User $user, $storeId, array $storeData)
    {
        $this->ownCompany($user);

        $store = $this->storeRepository->findById($storeId);

        if (! $store) {
            throw new NotFoundException('store not found');
        }

        if ($user->company->id !== $store->company->id) {
            throw new ForbiddenException('you cannot edit store you do not own');
        }

        $updatedStore = $this->storeRepository->update($store, $storeData);

        return $updatedStore;
    }

    public function deleteStore(User $user, string $storeId)
    {
        $this->ownCompany($user);

        $store = $this->storeRepository->findById($storeId);

        if (! $store) {
            throw new NotFoundException('store not found');
        }

        if ($user->company->id !== $store->company->id) {
            throw new ForbiddenException('you cannot delete store you do not own');
        }

        return $store;
    }

    private function ownCompany(User $user)
    {
        if (! $user->company()->exists()) {
            throw new ForbiddenException('you do not own a company');
        }
    }
}
