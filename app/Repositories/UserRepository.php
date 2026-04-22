<?php

namespace App\Repositories;

use App\Common\Repository\BaseRepository;
use App\Enums\UserRole;
use App\Models\User;

class UserRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getByEmail(string $email)
    {
        $query = $this->query();

        return $query->where('email', $email)->first();
    }

    public function getByPhoneNumber($phoneNumber)
    {
        return $this->model->where('phone_number', $phoneNumber)->first();
    }

    public function blockTenant(User $tenant)
    {
        if ($tenant->is_blocked) {
            return $tenant->refresh();
        }
        $tenant->is_blocked = true;
        $tenant->save();

        return $tenant->refresh();
    }

    public function unblockTenant(User $tenant)
    {
        if (! $tenant->is_blocked) {
            return $tenant->refresh();
        }
        $tenant->is_blocked = false;
        $tenant->save();

        return $tenant->refresh();
    }

    public function findTenant(string $tenantId)
    {
        return $this->query()->whereIn('role', UserRole::tenants())
            ->where('id', $tenantId)->first();
    }

    public function findAllTenants(int $perPage, string $storeId)
    {
        $tenants = $this->query()->whereIn('role', UserRole::tenants())
            ->where('store_id', $storeId)
            ->paginate($this->perPage($perPage));

        return $this->formatPagination($tenants);
    }
}
