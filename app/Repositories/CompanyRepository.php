<?php

namespace App\Repositories;

use App\Common\Repository\BaseRepository;
use App\Models\Company;
use App\Models\User;

/**
 * @extends BaseRepository<Company>
 */

class CompanyRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(Company $company)
    {
        $this->model = $company;
    }

    public function myCompany(User $user){
        return $user->company;
    }
}
