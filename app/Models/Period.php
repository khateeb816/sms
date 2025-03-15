<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'duration',
        'type',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'duration' => 'integer',
    ];

    /**
     * Get the formatted duration.
     *
     * @return string
     */
    public function getFormattedDurationAttribute()
    {
        return $this->duration . ' minutes';
    }

    /**
     * Get the status badge.
     *
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->type === 'break') {
            return '<span class="badge badge-info">Break</span>';
        }

        return $this->is_active
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-danger">Inactive</span>';
    }
}
