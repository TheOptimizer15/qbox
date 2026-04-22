<?php

namespace App\Services\Store;

use App\Enums\UserRole;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Repositories\InvitationRepository;
use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;
use App\Services\Sms\SmsService;
use Illuminate\Database\Eloquent\Collection;

/**
 * This service allows a company owner to manage the tenants of a given store
 * The service uses the sms service provider interface to send sms to invite a user
 * The user has now the possibility to join a store or decline the inivation
 */
class StoreTenantService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected StoreRepository $storeRepository
    ) {}

    /**
     * This method returns all the tenants from a store belonging to the company
     * this routes is accessible to store manager who has access to a store ressource
     * the store manager can only see its current store tenant but cannot block or unblock
     * the owner has access to all the stores
     * 
     * @param User $user can be a store manager or owner
     * @param string|null $storeId not needed when user is manager
     * @throws NotFoundException
     * @return Collection<int, User>
    */
    public function tenants(User $user, string|null $storeId){
        // if user is an owner get the store from the id
        if($user->role == UserRole::OWNER){
            $store = $this->storeRepository->findById($storeId);
            if(!$store){
                throw new NotFoundException('store not found');
            }

            return $store->tenants;
        }

        return $user->store->tenants;
    }

    /**
     * This method blocks a tenant from managing a store
     * If the tenant is blocked he cannot interact with the store no longer
     * @param User $owner
     * @param string $tenantId
     * @throws NotFoundException
     * @return User
    */
    public function block(User $owner, string $tenantId){
        $tenant = $this->userRepository->findTenant($tenantId);
        if(!$tenant){
            throw new NotFoundException('tenant not found');
        }
        
        $this->isTenantFromCurrentCompany($owner, $tenant);
        return $this->userRepository->blockTenant($tenant);
    }

    /**
     * This method unblocks a tenant from managing a store
     * If the tenant is unblocked he can interact with the store 
     * @param User $owner
     * @param string $tenantId
     * @throws NotFoundException
     * @return User
    */
    public function unblock(User $owner, string $tenantId){
        $tenant = $this->userRepository->findTenant($tenantId);
        if(!$tenant){
            throw new NotFoundException('tenant not found');
        }
        
        $this->isTenantFromCurrentCompany($owner, $tenant);
        return $this->userRepository->unblockTenant($tenant);
    }

    /**
     * This private memthod asserts a tenant is from the owner's store
     * @param User $owner
     * @param User $tenant
     * @throws ForbiddenException
     * @return void
     * 
    */
    private function isTenantFromCurrentCompany(User $owner, User $tenant){
        // check if the owner has a business
        if(!$owner->company()->exists()){
            throw new ForbiddenException('you do not have a business');
        }

        $ownerCompanyId = $owner->company->id;
        $tenantCompanyId = $tenant->store->company->id;
        if($tenantCompanyId !== $ownerCompanyId){
            throw new ForbiddenException('this action is not allowed on a tenant you do not work with');
        }
    }
}
