<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Organization extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'category_id',
        'unit_id',
        'sub_unit_id',
        'office_id',
        'sub_office_id',
        'items',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'items' => 'json',
    ];
}
