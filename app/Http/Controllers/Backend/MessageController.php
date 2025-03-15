<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('messages.inbox');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $admins = User::where('role', 1)->where('status', 'active')->get();
        $teachers = User::where('role', 2)->where('status', 'active')->get();
        $students = User::where('role', 4)->where('status', 'active')->get();
        $parents = User::where('role', 3)->where('status', 'active')->get();

        return view('backend.pages.messages.compose', compact('admins', 'teachers', 'students', 'parents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|string|in:admin,teacher,parent,student,all',
            'recipient_id' => 'required_unless:recipient_type,all',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'message_type' => 'required|string|in:alert,warning,complaint,general',
        ]);

        $user = Auth::user();
        $userType = $this->getUserType($user);
        $isBroadcast = $request->recipient_type === 'all' || $request->has('is_broadcast');

        $message = new Message();
        $message->sender_id = $user->id;
        $message->sender_type = $userType;
        $message->recipient_type = $request->recipient_type;
        $message->subject = $request->subject;
        $message->message = $request->message;
        $message->message_type = $request->message_type;
        $message->is_broadcast = $isBroadcast;

        if (!$isBroadcast) {
            $message->recipient_id = $request->recipient_id;
        }

        $message->save();

        // Get recipient information for activity log
        $recipientType = $request->recipient_type;
        $recipientInfo = '';

        if ($recipientType === 'all') {
            $recipientInfo = 'All Users';
        } elseif ($recipientType === 'role') {
            $roleNames = [
                '1' => 'Students',
                '2' => 'Teachers',
                '3' => 'Parents',
                '4' => 'Admins'
            ];
            $recipientInfo = $roleNames[$request->role_id] ?? 'Unknown Role';
        } elseif ($recipientType === 'individual') {
            $recipientCount = count($request->recipient_ids);
            $recipientInfo = $recipientCount . ' individual ' . ($recipientCount == 1 ? 'user' : 'users');
        }

        ActivityService::logMessageActivity('Sent', $request->subject, $recipientInfo);

        return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = Message::with(['sender', 'recipient'])->findOrFail($id);
        $user = Auth::user();
        $userType = $this->getUserType($user);

        $isSender = $message->sender_id === $user->id && $message->sender_type === $userType;
        $isRecipient = $message->recipient_id === $user->id && $message->recipient_type === $userType;
        $isBroadcastRecipient = $message->is_broadcast && ($message->recipient_type === $userType || $message->recipient_type === 'all');

        // Check if the message has been deleted by this user using the new system
        if ($message->isDeletedByUser($user->id, $userType)) {
            return redirect()->route('messages.index')->with('error', 'Message not found.');
        }

        // For backward compatibility
        if (($isSender && $message->deleted_by_sender) ||
            (($isRecipient || $isBroadcastRecipient) && $message->deleted_by_recipient)
        ) {
            return redirect()->route('messages.index')->with('error', 'Message not found.');
        }

        if (!$isSender && !$isRecipient && !$isBroadcastRecipient) {
            return redirect()->route('messages.index')->with('error', 'You do not have permission to view this message.');
        }

        if (($isRecipient || $isBroadcastRecipient) && !$message->is_read) {
            $message->is_read = true;
            $message->read_at = now();
            $message->save();
        }

        return view('backend.pages.messages.show', compact('message'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);
        $message->update([
            'subject' => $request->subject,
            'message' => $request->message,
            'is_draft' => false,
        ]);

        // Get recipient information for activity log
        $recipients = $message->recipients()->count();
        $recipientInfo = $recipients . ' ' . ($recipients == 1 ? 'recipient' : 'recipients');

        ActivityService::logMessageActivity('Updated and Sent', $request->subject, $recipientInfo);

        return redirect()->route('messages.index')->with('success', 'Message updated and sent successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $subject = $message->subject;
        $message->delete();

        ActivityService::logMessageActivity('Deleted', $subject, '');

        return redirect()->route('messages.index')->with('success', 'Message deleted successfully.');
    }

    public function inbox()
    {
        $user = Auth::user();
        $userType = $this->getUserType($user);
        $userKey = $user->id . '_' . $userType;

        $messages = Message::with('sender')
            ->where(function ($query) use ($user, $userType, $userKey) {
                $query->where(function ($q) use ($user, $userType) {
                    $q->where('recipient_id', $user->id)
                        ->where('recipient_type', $userType)
                        ->where('deleted_by_recipient', false);
                })->orWhere(function ($q) use ($userType, $userKey) {
                    $q->where('is_broadcast', true)
                        ->where(function ($sq) use ($userType) {
                            $sq->where('recipient_type', $userType)
                                ->orWhere('recipient_type', 'all');
                        })
                        ->where(function ($sq) use ($userKey) {
                            // Either the deleted_by_users column doesn't exist yet, or the user is not in the array
                            $sq->whereNull('deleted_by_users')
                                ->orWhereRaw("NOT JSON_CONTAINS(deleted_by_users, '\"$userKey\"')");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unreadCount = Message::where(function ($query) use ($user, $userType, $userKey) {
            $query->where(function ($q) use ($user, $userType) {
                $q->where('recipient_id', $user->id)
                    ->where('recipient_type', $userType)
                    ->where('deleted_by_recipient', false);
            })->orWhere(function ($q) use ($userType, $userKey) {
                $q->where('is_broadcast', true)
                    ->where(function ($sq) use ($userType) {
                        $sq->where('recipient_type', $userType)
                            ->orWhere('recipient_type', 'all');
                    })
                    ->where(function ($sq) use ($userKey) {
                        // Either the deleted_by_users column doesn't exist yet, or the user is not in the array
                        $sq->whereNull('deleted_by_users')
                            ->orWhereRaw("NOT JSON_CONTAINS(deleted_by_users, '\"$userKey\"')");
                    });
            });
        })
            ->where('is_read', false)
            ->count();

        return view('backend.pages.messages.inbox', compact('messages', 'unreadCount'));
    }

    public function sent()
    {
        $user = Auth::user();
        $userType = $this->getUserType($user);
        $userKey = $user->id . '_' . $userType;

        $messages = Message::with('recipient')
            ->where('sender_id', $user->id)
            ->where('sender_type', $userType)
            ->where(function ($query) use ($userKey) {
                // Either the deleted_by_users column doesn't exist yet, or the user is not in the array
                $query->whereNull('deleted_by_users')
                    ->orWhereRaw("NOT JSON_CONTAINS(deleted_by_users, '\"$userKey\"')");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('backend.pages.messages.sent', compact('messages'));
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $userType = $this->getUserType($user);
        $userKey = $user->id . '_' . $userType;

        Message::where(function ($query) use ($user, $userType, $userKey) {
            $query->where(function ($q) use ($user, $userType) {
                $q->where('recipient_id', $user->id)
                    ->where('recipient_type', $userType)
                    ->where('deleted_by_recipient', false);
            })->orWhere(function ($q) use ($userType, $userKey) {
                $q->where('is_broadcast', true)
                    ->where(function ($sq) use ($userType) {
                        $sq->where('recipient_type', $userType)
                            ->orWhere('recipient_type', 'all');
                    })
                    ->where(function ($sq) use ($userKey) {
                        // Either the deleted_by_users column doesn't exist yet, or the user is not in the array
                        $sq->whereNull('deleted_by_users')
                            ->orWhereRaw("NOT JSON_CONTAINS(deleted_by_users, '\"$userKey\"')");
                    });
            });
        })
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return redirect()->route('messages.index')->with('success', 'All messages marked as read.');
    }

    private function getUserType(User $user)
    {
        switch ($user->role) {
            case 1:
                return 'admin';
            case 2:
                return 'teacher';
            case 3:
                return 'parent';
            case 4:
                return 'student';
            default:
                return 'unknown';
        }
    }
}
