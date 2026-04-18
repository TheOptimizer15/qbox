<?php

namespace App\Services\Company;

use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Repositories\CompanyRepository;

class CompanyService
{
    public function __construct(
        protected CompanyRepository $repository
    ) {}

    public function getCompany(User $user)
    {
        return $this->repository->myCompany($user);
    }

    public function createCompany(User $user, array $data)
    {
        $companyData = [...$data, ...[
            'owner_id' => $user->id,
        ]];

        $company = $this->repository->create($companyData);

        return $company;
    }

    public function updateName(
        User $user,
        $name
    ) {
        $companyId = $user->company?->id;
        $company = $this->repository->findById($companyId);

        if (! $company) {
            throw new NotFoundException('company not found');
        }

        $updatedCompany = $this->repository->update($company, [
            'name' => $name,
        ]);

        return $updatedCompany;
    }

    /**
     * only the super admin can delete a company and all of its data
    */
    public function deleteCompany($companyId)
    {
        $company = $this->repository->findById($companyId);

        if (! $company) {
            throw new NotFoundException('company not found');
        }

        return $this->repository->delete($company);

    }
}
