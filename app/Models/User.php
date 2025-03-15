<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\ClassRoom;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'image',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the teacher details associated with the user.
     */
    public function teacherDetail()
    {
        return $this->hasOne(TeacherDetail::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the classes that the student belongs to.
     */
    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_student', 'student_id', 'class_id')
            ->withTimestamps();
    }

    /**
     * Get the sent messages for the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the received messages for the user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    /**
     * Get the fees for the student.
     */
    public function fees()
    {
        return $this->hasMany(Fee::class, 'student_id');
    }

    /**
     * Get the fines for the student.
     */
    public function fines()
    {
        return $this->hasMany(Fine::class, 'student_id');
    }
}
