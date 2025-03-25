<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Datesheet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'class_id',
        'term',
        'start_date',
        'end_date',
        'status',
        'description',
        'instructions'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    /**
     * Get the class for which the datesheet is created.
     */
    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * Get the exams in this datesheet.
     */
    public function exams()
    {
        return $this->belongsToMany(Exam::class)
            ->withPivot('day_number')
            ->withTimestamps()
            ->orderBy('day_number');
    }

    /**
     * Scope a query to only include published datesheets.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include active datesheets.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['published', 'completed'])
            ->where('end_date', '>=', now());
    }

    /**
     * Check if the datesheet is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if the datesheet is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}