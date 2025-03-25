<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_id',
        'student_id',
        'marks_obtained',
        'percentage',
        'grade',
        'remarks',
        'is_passed'
    ];

    protected $casts = [
        'marks_obtained' => 'integer',
        'percentage' => 'decimal:2',
        'is_passed' => 'boolean'
    ];

    /**
     * Get the exam that owns the result.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the student who took the exam.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Calculate grade based on percentage
     */
    public function calculateGrade()
    {
        if ($this->percentage >= 90) {
            return 'A+';
        } elseif ($this->percentage >= 80) {
            return 'A';
        } elseif ($this->percentage >= 75) {
            return 'B+';
        } elseif ($this->percentage >= 70) {
            return 'B';
        } elseif ($this->percentage >= 65) {
            return 'C+';
        } elseif ($this->percentage >= 60) {
            return 'C';
        } elseif ($this->percentage >= 50) {
            return 'D';
        } else {
            return 'F';
        }
    }

    /**
     * Calculate if student passed based on exam's passing marks
     */
    public function calculatePassStatus()
    {
        return $this->marks_obtained >= $this->exam->passing_marks;
    }
}
