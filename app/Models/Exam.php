<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    // Exam Terms
    const TERM_FIRST = 'first_term';
    const TERM_SECOND = 'second_term';
    const TERM_THIRD = 'third_term';
    const TERM_FINAL = 'final_term';

    // Exam Types
    const TYPE_FIRST_TERM = 'first_term';
    const TYPE_SECOND_TERM = 'second_term';
    const TYPE_THIRD_TERM = 'third_term';
    const TYPE_FINAL_TERM = 'final_term';

    // Test Types
    const TYPE_NORMAL = 'normal';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_MONTHLY = 'monthly';
    const TYPE_YEARLY = 'yearly';

    // Exam Status
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'title',
        'teacher_id',
        'class_id',
        'subject',
        'exam_date',
        'start_time',
        'end_time',
        'total_marks',
        'passing_marks',
        'term',
        'type',
        'description',
        'instructions',
        'status'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_marks' => 'integer',
        'passing_marks' => 'integer'
    ];

    /**
     * Get all available exam terms
     */
    public static function getTerms()
    {
        return [
            self::TERM_FIRST => 'First Term',
            self::TERM_SECOND => 'Second Term',
            self::TERM_THIRD => 'Third Term',
            self::TERM_FINAL => 'Final Term',
        ];
    }

    /**
     * Get all available exam types
     */
    public static function getExamTypes()
    {
        return [
            self::TYPE_FIRST_TERM => 'First Term',
            self::TYPE_SECOND_TERM => 'Second Term',
            self::TYPE_THIRD_TERM => 'Third Term',
            self::TYPE_FINAL_TERM => 'Final Term',
        ];
    }

    /**
     * Get all available test types
     */
    public static function getTestTypes()
    {
        return [
            self::TYPE_NORMAL => 'Normal',
            self::TYPE_WEEKLY => 'Weekly',
            self::TYPE_MONTHLY => 'Monthly',
            self::TYPE_YEARLY => 'Yearly',
        ];
    }

    /**
     * Get all available exam statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Get the teacher who created the exam.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the class for which the exam is scheduled.
     */
    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * Get the exam results for this exam.
     */
    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * Get the datesheets this exam belongs to.
     */
    public function datesheets()
    {
        return $this->belongsToMany(Datesheet::class)
            ->withPivot('day_number')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include upcoming exams.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('exam_date', 'asc');
    }

    /**
     * Scope a query to only include completed exams.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
            ->orderBy('exam_date', 'desc');
    }

    /**
     * Check if the exam is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->exam_date >= now() && $this->status === 'scheduled';
    }

    /**
     * Check if the exam is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the exam is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the exam is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
