<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'qualification',
        'specialization',
        'education_level',
        'university',
        'degree',
        'major',
        'graduation_year',
        'certification',
        'teaching_experience',
        'biography',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'date_of_birth',
        'blood_group',
        'nationality',
        'religion',
        'marital_status',
        'spouse_name',
        'children_count',
        'social_media_facebook',
        'social_media_twitter',
        'social_media_linkedin',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'tax_id',
    ];

    /**
     * Get the user that owns the teacher details.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
