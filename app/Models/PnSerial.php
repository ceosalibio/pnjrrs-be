<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PnSerial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pn_serials';

    protected $fillable = [
        'personnel_report_id',
        'category_id',
        'unit_id',
        'sub_unit_id',
        'office_id',
        'sub_office_id',
        'rank_id',
        'serial',
        'name',
        'report_month',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the personnel report associated with this serial.
     */
    public function personnelReport()
    {
        return $this->belongsTo(ReportPersonnel::class, 'personnel_report_id');
    }

    /**
     * Get the category associated with this serial.
     */
    public function category()
    {
        return $this->belongsTo(PnCategory::class, 'category_id');
    }

    /**
     * Get the unit associated with this serial.
     */
    public function unit()
    {
        return $this->belongsTo(PnUnit::class, 'unit_id');
    }

    /**
     * Get the sub unit associated with this serial.
     */
    public function subUnit()
    {
        return $this->belongsTo(PnSubUnit::class, 'sub_unit_id');
    }

    /**
     * Get the office associated with this serial.
     */
    public function office()
    {
        return $this->belongsTo(PnOffice::class, 'office_id');
    }

    /**
     * Get the sub office associated with this serial.
     */
    public function subOffice()
    {
        return $this->belongsTo(PnSubOffice::class, 'sub_office_id');
    }

    /**
     * Get the rank associated with this serial.
     */
    public function rank()
    {
        return $this->belongsTo(ItemRank::class, 'rank_id');
    }
}
