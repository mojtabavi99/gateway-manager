<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository extends Repository
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }
}
