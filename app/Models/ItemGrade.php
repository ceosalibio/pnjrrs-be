<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemGrade extends Model
{
    protected $fillable = ['division_id', 'name', 'description', 'created_by', 'updated_by'];

    public function division()
    {
        return $this->belongsTo(ItemDivision::class, 'division_id');
    }
}
