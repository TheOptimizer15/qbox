<?php

namespace App\Services\Company;

use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Models\Company;
use App\Models\User;
use App\Repositories\CompanyRepository;

/**
 * Handles business logic for company management.
 *
 * Each owner can have at most one company. Only super admins
 * are allowed to delete a company and all of its associated data.
 */
class CompanyService
{
    public function __construct(
        protected CompanyRepository $repository
    ) {}

    /**
     * Retrieve the authenticated user's company.
     *
     * @param  User  $user  The authenticated user.
     * @return Company|null  The user's company, or null if none exists.
     */
    public function getCompany(User $user): ?Company
    {
        return $this->repository->myCompany($user);
    }

    /**
     * Create a new company for the given owner.
     *
     * A user may only own one company. Attempting to create a second
     * company will result in a BadRequestException.
     *
     * @param  User  $user  The owner creating the company.
     * @param  array{name: string}  $data  The validated company data.
     * @return Company  The newly created company.
     *
     * @throws BadRequestException  If the user already owns a company.
     */
    public function createCompany(User $user, array $data): Company
    {
        if ($user->company()->exists()) {
            throw new BadRequestException('you already have a company');
        }

        $companyData = [...$data, ...['owner_id' => $user->id]];

        return $this->repository->create($companyData);
    }

    /**
     * Update the name of the authenticated user's company.
     *
     * @param  User  $user  The owner of the company.
     * @param  string  $name  The new company name.
     * @return Company  The updated company.
     *
     * @throws BadRequestException  If the user does not own a company.
     */
    public function updateName(User $user, string $name): Company
    {
        if (! $user->company()->exists()) {
            throw new BadRequestException('you do not have a company');
        }

        $company = $user->company;

        return $this->repository->update($company, [
            'name' => $name,
        ]);
    }

    /**
     * Delete a company by its ID.
     *
     * This action is restricted to super admins and will cascade-delete
     * all associated stores and data via the database foreign keys.
     *
     * @param  string  $companyId  The UUID of the company to delete.
     * @return bool|null  True on success, null if already deleted.
     *
     * @throws NotFoundException  If no company exists with the given ID.
     */
    public function deleteCompany(string $companyId): ?bool
    {
        $company = $this->repository->findById($companyId);

        if (! $company) {
            throw new NotFoundException('company not found');
        }

        return $this->repository->delete($company);
    }
}
