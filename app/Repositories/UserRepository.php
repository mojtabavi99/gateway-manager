<?php

namespace App\Repositories;

use App\Models\User;
use App\Traits\ExceptionTrait;

class UserRepository extends Repository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findOrCreate(array $data): User
    {
        return User::query()->where('mobile', $data['mobile'])->first()
            ?: User::query()->create($data);
    }
}
