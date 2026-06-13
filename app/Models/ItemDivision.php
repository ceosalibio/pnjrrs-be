<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemDivision extends Model
{
    protected $fillable = ['name', 'description', 'created_by', 'updated_by'];

    public function grades()
    {
        return $this->hasMany(ItemGrade::class, 'division_id');
    }
}
