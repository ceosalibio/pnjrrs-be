<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

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
        'rank_id',
        'name',
        'position',
        'username',
        'password',
        'role',
        'approver',
        'office_role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        // 'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
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

    public function rank()
    {
        return $this->belongsTo(ItemRank::class, 'rank_id');
    }

    public function approvers()
    {
        return $this->hasMany(Approver::class, 'user_id');
    }

    /**
     * Get approvers for a specific report type
     * 
     * @param string $reportType The report type (e.g., 'Personnel', 'Equipment', 'Facility', 'Training')
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approversForReportType($reportType)
    {
        return $this->approvers()->where('report_type', $reportType);
    }
}
