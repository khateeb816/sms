<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'sender_type',
        'recipient_id',
        'recipient_type',
        'subject',
        'message',
        'message_type',
        'is_read',
        'read_at',
        'is_broadcast',
        'deleted_by_sender',
        'deleted_by_recipient',
        'deleted_at',
        'deleted_by_users',
        'class_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_broadcast' => 'boolean',
        'deleted_by_sender' => 'boolean',
        'deleted_by_recipient' => 'boolean',
        'deleted_at' => 'datetime',
        'deleted_by_users' => 'array',
    ];

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient of the message.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Scope a query to only include unread messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include messages for a specific recipient that haven't been deleted by them.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @param  string  $userType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForRecipient($query, $userId, $userType)
    {
        return $query->where(function ($q) use ($userId, $userType) {
            $q->where(function ($q2) use ($userId, $userType) {
                $q2->where('recipient_id', $userId)
                    ->where('recipient_type', $userType)
                    ->where('deleted_by_recipient', false);
            })->orWhere(function ($q2) use ($userType) {
                $q2->where('recipient_type', $userType)
                    ->where('is_broadcast', true)
                    ->where('deleted_by_recipient', false);
            })->orWhere(function ($q3) {
                $q3->where('recipient_type', 'all')
                    ->where('is_broadcast', true)
                    ->where('deleted_by_recipient', false);
            });
        });
    }

    /**
     * Scope a query to only include messages sent by a specific user that haven't been deleted by them.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @param  string  $userType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSender($query, $userId, $userType)
    {
        return $query->where('sender_id', $userId)
            ->where('sender_type', $userType)
            ->where('deleted_by_sender', false);
    }

    /**
     * Mark the message as read.
     *
     * @return bool
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->is_read = true;
            $this->read_at = now();
            return $this->save();
        }
        return true;
    }

    /**
     * Mark the message as deleted by a specific user.
     *
     * @param int $userId
     * @param string $userType
     * @return bool
     */
    public function deleteByUser($userId, $userType)
    {
        $deletedUsers = $this->deleted_by_users ?? [];
        $userKey = $userId . '_' . $userType;

        // Check if this user has already deleted the message
        if (!in_array($userKey, $deletedUsers)) {
            $deletedUsers[] = $userKey;
            $this->deleted_by_users = $deletedUsers;

            // For backward compatibility
            if ($this->sender_id == $userId && $this->sender_type == $userType) {
                $this->deleted_by_sender = true;
            } else {
                $this->deleted_by_recipient = true;
            }

            // If the message has been deleted by the sender and at least one recipient
            // or if it's a broadcast message and has been deleted by the sender
            if (($this->deleted_by_sender && count($deletedUsers) > 1) ||
                ($this->is_broadcast && $this->deleted_by_sender)
            ) {
                $this->deleted_at = now();
            }

            return $this->save();
        }

        return true;
    }

    /**
     * Check if the message has been deleted by a specific user.
     *
     * @param int $userId
     * @param string $userType
     * @return bool
     */
    public function isDeletedByUser($userId, $userType)
    {
        $deletedUsers = $this->deleted_by_users ?? [];
        $userKey = $userId . '_' . $userType;

        return in_array($userKey, $deletedUsers);
    }

    /**
     * Mark the message as deleted by sender.
     * Kept for backward compatibility.
     *
     * @return bool
     */
    public function deleteForSender()
    {
        $this->deleted_by_sender = true;

        // If both sender and recipient have deleted, actually delete the message
        if ($this->deleted_by_recipient) {
            $this->deleted_at = now();
        }

        return $this->save();
    }

    /**
     * Mark the message as deleted by recipient.
     * Kept for backward compatibility.
     *
     * @return bool
     */
    public function deleteForRecipient()
    {
        $this->deleted_by_recipient = true;

        // If both sender and recipient have deleted, actually delete the message
        if ($this->deleted_by_sender) {
            $this->deleted_at = now();
        }

        return $this->save();
    }

    /**
     * Check if the message should be permanently deleted.
     * Kept for backward compatibility.
     *
     * @return bool
     */
    public function shouldBeDeleted()
    {
        return $this->deleted_by_sender && $this->deleted_by_recipient;
    }

    public function class()
    {
        return $this->belongsTo(ClassRoom::class);
    }
}
