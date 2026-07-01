<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingItem extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'unit_id',
        'sub_unit_id',
        'office_id',
        'sub_office_id',
        'items',
        'year',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'items' => 'json',
            'year' => 'integer',
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
}
