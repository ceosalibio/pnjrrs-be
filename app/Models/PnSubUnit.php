<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PnSubUnit extends Model
{
    use SoftDeletes;
    protected $table = 'pn_sub_units';
    protected $fillable = [
        'category_id',
        'unit_id',
        'name',
        'abreviation',
        'address',
        'description',
        'created_by',
        'updated_by',
    ];

    public function category()
    {
        return $this->belongsTo(PnCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(PnUnit::class, 'unit_id');
    }
}
