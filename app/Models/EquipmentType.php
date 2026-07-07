<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentType extends Model
{
    use SoftDeletes;

     protected $fillable = [
        'category_id',
        'division_id',
        'name'
    ];

    public function category()
    {
        return $this->belongsTo(EquipmentCategory::class, 'category_id');
    }
    public function division()
    {
        return $this->belongsTo(EquipmentDivision::class, 'division_id');
    }
}
