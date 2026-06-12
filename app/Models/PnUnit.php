<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PnUnit extends Model
{
    use SoftDeletes;
    protected $table = 'pn_units';
    protected $fillable = [
        'category_id',
        'name',
        'abreviation',
        'description',
        'address',
        'icon',
        'created_by',
        'updated_by',
    ];

    public function category()
    {
        return $this->belongsTo(PnCategory::class, 'category_id');
    }
}
