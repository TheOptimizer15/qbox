<?php

namespace App\Repositories;

use App\Common\Repository\BaseRepository;
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

    public function getByPhoneNumber($phoneNumber){
        return $this->model->where('phone_number', $phoneNumber)->first();
    }

    
}
