<?php

namespace App\Repositories;

use App\Models\Approver;
use Illuminate\Database\Eloquent\Collection;

class ApproverRepository
{
    public function create($data)
    {
        Approver::create($data);
    }
}
