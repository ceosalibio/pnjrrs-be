<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRank extends Model
{
    protected $fillable = [
        'division_id',
        'grade_id',
        'name',
        'description',
        'created_by',
        'updated_by'
        ];

        public function division()
        {
            return $this->belongsTo(ItemDivision::class, 'division_id');
        }

        public function grade()
        {
            return $this->belongsTo(ItemGrade::class, 'grade_id');
        }

}
