<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportFacility extends Model
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
        'required',
        'actual',
        'report_month',
        'rating',
        'redcon',  
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
        return $this->belongsTo(PnCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(PnUnit::class, 'unit_id');
    }

    public function subUnit()
    {
        return $this->belongsTo(PnSubUnit::class, 'sub_unit_id');
    }

    public function office()
    {
        return $this->belongsTo(PnOffice::class, 'office_id');
    }

    public function subOffice()
    {
        return $this->belongsTo(PnSubOffice::class, 'sub_office_id');
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
