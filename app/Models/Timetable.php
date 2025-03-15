<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'class_id',
        'day_of_week',
        'period_id',
        'teacher_id',
        'subject',
        'start_time',
        'end_time',
        'notes',
        'is_break',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_break' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the class that owns the timetable entry.
     */
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * Get the period that owns the timetable entry.
     */
    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    /**
     * Get the teacher that owns the timetable entry.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the formatted time range.
     *
     * @return string
     */
    public function getTimeRangeAttribute()
    {
        return $this->start_time->format('H:i') . '-' . $this->end_time->format('H:i');
    }
}
