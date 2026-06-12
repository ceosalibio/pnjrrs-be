<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnCategory extends Model
{
    protected $table = 'pn_categories';
    protected $fillable = [
        'name',
        'description',
        'abreviation',
        'address',
        'icon',
        'created_by',
        'updated_by',
    ];

    public function units()
    {
        return $this->hasMany(PnUnit::class, 'category_id');
    }
}
