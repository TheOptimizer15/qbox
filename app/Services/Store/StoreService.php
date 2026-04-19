<?php

namespace App\Services\Store;

use App\Enums\UserRole;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Models\Store;
use App\Models\User;
use App\Repositories\StoreRepository;

/**
 * Handles business logic for store CRUD operations.
 *
 * Owners can manage all stores belonging to their company.
 * Cashiers (tenants) can only view the store they are assigned to.
 */
class StoreService
{
    public function __construct(
        protected StoreRepository $storeRepository
    ) {}

    /**
     * Retrieve all stores accessible to the given user.
     *
     * - Cashiers receive only their assigned store (no pagination).
     * - Owners receive a paginated list of all stores under their company.
     *
     * @param  User  $user  The authenticated user.
     * @param  int  $perPage  Number of results per page (clamped between 10–100).
     * @return array{0: Store|array<Store>, 1: array}  A tuple of [stores, pagination meta].
     *
     * @throws ForbiddenException  If the owner does not have a company.
     */
    public function getAllStores(User $user, int $perPage = 15): array
    {
        // if user is a cashier return the store he has been assigned
        if ($user->role == UserRole::CASHIER) {
            $store = $this->storeRepository->getTenantStore($user);

            return [$store, []];
        }

        $this->ownCompany($user);

        return $this->storeRepository->getAllStores($user, $perPage);
    }

    /**
     * Retrieve a single store by its ID.
     *
     * - Cashiers can only access the store they are assigned to.
     * - Owners can only access stores belonging to their company.
     *
     * @param  User  $user  The authenticated user.
     * @param  string  $id  The UUID of the store to retrieve.
     * @return Store  The requested store.
     *
     * @throws NotFoundException  If no store exists with the given ID.
     * @throws ForbiddenException  If the user is not authorized to view this store.
     */
    public function getStore(User $user, string $id): Store
    {
        $store = $this->storeRepository->findById($id);

        if (! $store) {
            throw new NotFoundException('store not found');
        }

        // if user is a cashier check the store
        if ($user->role == UserRole::CASHIER) {
            if ($user->store->id !== $store->id) {
                throw new ForbiddenException;
            }

            return $store;
        }

        $this->ownCompany($user);

        if ($user->company->id !== $store->company->id) {
            throw new ForbiddenException('you cannot load a store you do not own');
        }

        return $store;
    }

    /**
     * Create a new store under the authenticated owner's company.
     *
     * The company_id is automatically injected from the user's company,
     * so it should not be included in the input data.
     *
     * @param  User  $user  The authenticated owner.
     * @param  array{name: string, location?: string, longitude?: float, latitude?: float, online: bool}  $data  The validated store data.
     * @return Store  The newly created store.
     *
     * @throws ForbiddenException  If the user does not own a company.
     */
    public function createStore(User $user, array $data): Store
    {
        $this->ownCompany($user);

        $storeData = [
            ...$data,
            ...['company_id' => $user->company->id],
        ];

        return $this->storeRepository->create($storeData);
    }

    /**
     * Update an existing store's data.
     *
     * Only the owner of the company that the store belongs to can update it.
     *
     * @param  User  $user  The authenticated owner.
     * @param  string  $storeId  The UUID of the store to update.
     * @param  array  $storeData  The validated fields to update.
     * @return Store  The updated store.
     *
     * @throws NotFoundException  If no store exists with the given ID.
     * @throws ForbiddenException  If the user does not own this store's company.
     */
    public function updateStore(User $user, string $storeId, array $storeData): Store
    {
        $this->ownCompany($user);

        $store = $this->storeRepository->findById($storeId);

        if (! $store) {
            throw new NotFoundException('store not found');
        }

        if ($user->company->id !== $store->company->id) {
            throw new ForbiddenException('you cannot edit store you do not own');
        }

        return $this->storeRepository->update($store, $storeData);
    }

    /**
     * Delete a store by its ID.
     *
     * Only the owner of the company that the store belongs to can delete it.
     *
     * @param  User  $user  The authenticated owner.
     * @param  string  $storeId  The UUID of the store to delete.
     * @return bool|null  True on success, null if already deleted.
     *
     * @throws NotFoundException  If no store exists with the given ID.
     * @throws ForbiddenException  If the user does not own this store's company.
     */
    public function deleteStore(User $user, string $storeId): ?bool
    {
        $this->ownCompany($user);

        $store = $this->storeRepository->findById($storeId);

        if (! $store) {
            throw new NotFoundException('store not found');
        }

        if ($user->company->id !== $store->company->id) {
            throw new ForbiddenException('you cannot delete store you do not own');
        }

        return $this->storeRepository->delete($store);
    }

    /**
     * Assert that the user owns a company.
     *
     * @param  User  $user  The user to check.
     * @return void
     *
     * @throws ForbiddenException  If the user does not own any company.
     */
    private function ownCompany(User $user): void
    {
        if (! $user->company()->exists()) {
            throw new ForbiddenException('you do not own a company');
        }
    }
}
