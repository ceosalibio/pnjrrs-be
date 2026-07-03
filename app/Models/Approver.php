<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Approver extends Model
{
    protected $fillable = [
        'report_id',
        'report_type',
        'sign_type',
        'user_id',
        'approved_status',
        'disapproved_status',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
