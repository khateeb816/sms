<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fine_type',
        'amount',
        'issue_date',
        'due_date',
        'payment_date',
        'status',
        'reason',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2'
    ];

    /**
     * Get the student that owns the fine.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the user who created the fine.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
