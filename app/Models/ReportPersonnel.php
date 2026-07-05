<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 

class ReportPersonnel extends Model
{
   use SoftDeletes;

    protected $fillable = [
        'category_id',
        'unit_id',
        'sub_unit_id',
        'office_id',
        'sub_office_id',
        'items',
        'result',
        'assessment',
        'grade_points',
        'afpos_points',
        'required',
        'actual',
        'report_month',
        'status',
        'is_final',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'assessment' => 'array',
            'items' => 'array',
            'result' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function subUnit()
    {
        return $this->belongsTo(SubUnit::class, 'sub_unit_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
