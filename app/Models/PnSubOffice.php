<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PnSubOffice extends Model
{
    use SoftDeletes;
    protected $table = 'pn_sub_offices';
    protected $fillable = [
        'category_id',
        'unit_id',
        'sub_unit_id',
        'office_id',
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

    public function subUnit()
    {
        return $this->belongsTo(PnSubUnit::class, 'sub_unit_id');
    }

    public function office()
    {
        return $this->belongsTo(PnOffice::class, 'office_id');
    }

}
