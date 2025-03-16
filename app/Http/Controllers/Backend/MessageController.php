<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        $user = auth()->user();

        if ($user->role == 2) { // Teacher
            // Get classes where the teacher has timetable entries
            $teacher_classes = DB::table('timetables')
                ->where('timetables.teacher_id', $user->id)
                ->join('class_rooms', 'timetables.class_id', '=', 'class_rooms.id')
                ->select('class_rooms.id', 'class_rooms.name')
                ->distinct()
                ->get();

            return view('backend.pages.messages.compose', compact('teacher_classes'));
        }

        // For non-teacher users, load all users as before
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
        $user = auth()->user();
        $userType = $this->getUserType($user);

        if ($user->role == 2) { // Teacher
            $request->validate([
                'recipient_type' => 'required|string|in:class_students,single_student,class_parents',
                'class_id' => 'required_if:recipient_type,class_students,class_parents,single_student|exists:class_rooms,id',
                'recipient_id' => 'required_if:recipient_type,single_student|nullable|exists:users,id',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'message_type' => 'required|string|in:alert,warning,complaint,general',
            ]);

            // Verify teacher has access to the class
            $hasAccess = DB::table('timetables')
                ->where('timetables.teacher_id', $user->id)
                ->where('timetables.class_id', $request->class_id)
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'You do not have access to this class.');
            }

            // Handle different recipient types
            $isBroadcast = in_array($request->recipient_type, ['class_students', 'class_parents']);
            $recipientType = '';
            $recipientId = null;

            switch ($request->recipient_type) {
                case 'class_students':
                    $recipientType = 'student';
                    break;
                case 'class_parents':
                    $recipientType = 'parent';
                    break;
                case 'single_student':
                    $recipientType = 'student';
                    $isBroadcast = false;
                    $recipientId = $request->recipient_id;

                    // Verify student belongs to teacher's class
                    $studentInClass = DB::table('class_student')
                        ->where('student_id', $recipientId)
                        ->where('class_id', $request->class_id)
                        ->exists();

                    if (!$studentInClass) {
                        return redirect()->back()->with('error', 'Selected student is not in your class.');
                    }
                    break;
            }

            try {
                DB::beginTransaction();

                if ($isBroadcast) {
                    // For broadcast messages to a class, create individual messages for each recipient
                    $recipients = [];
                    if ($recipientType === 'student') {
                        // Get all students in the class
                        $recipients = DB::table('users')
                            ->join('class_student', 'users.id', '=', 'class_student.student_id')
                            ->where('class_student.class_id', $request->class_id)
                            ->where('users.role', 4) // Students
                            ->where('users.status', 'active')
                            ->select('users.id')
                            ->get();
                    } elseif ($recipientType === 'parent') {
                        // Get all parents of students in the class
                        $recipients = DB::table('users AS parents')
                            ->join('users AS students', 'students.parent_id', '=', 'parents.id')
                            ->join('class_student', 'students.id', '=', 'class_student.student_id')
                            ->where('class_student.class_id', $request->class_id)
                            ->where('parents.role', 3) // Parents
                            ->where('parents.status', 'active')
                            ->select('parents.id')
                            ->distinct()
                            ->get();
                    }

                    foreach ($recipients as $recipient) {
                        $message = new Message();
                        $message->sender_id = $user->id;
                        $message->sender_type = $userType;
                        $message->recipient_type = $recipientType;
                        $message->recipient_id = $recipient->id;
                        $message->subject = $request->subject;
                        $message->message = $request->message;
                        $message->message_type = $request->message_type;
                        $message->is_broadcast = false;
                        $message->class_id = $request->class_id;
                        $message->save();
                    }
                } else {
                    // Single recipient message
                    $message = new Message();
                    $message->sender_id = $user->id;
                    $message->sender_type = $userType;
                    $message->recipient_type = $recipientType;
                    $message->recipient_id = $recipientId;
                    $message->subject = $request->subject;
                    $message->message = $request->message;
                    $message->message_type = $request->message_type;
                    $message->is_broadcast = false;
                    $message->class_id = $request->class_id;
                    $message->save();
                }

                DB::commit();

                // Get recipient information for activity log
                $className = DB::table('class_rooms')->where('id', $request->class_id)->value('name');
                $recipientInfo = $isBroadcast ?
                    "All " . ucfirst($recipientType) . "s in class " . $className :
                    "Single student in class " . $className;

                ActivityService::logMessageActivity('Sent', $request->subject, $recipientInfo);

                return redirect()->route('messages.sent')->with('success', 'Message sent successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                $message = new Message();
                $message->sender_id = $user->id;
                $message->sender_type = $userType;
                $message->recipient_type = $recipientType;
                $message->subject = $request->subject;
                $message->message = $request->message;
                $message->message_type = $request->message_type;
                $message->is_broadcast = $isBroadcast;
                $message->class_id = $request->class_id;

                if (!$isBroadcast) {
                    $message->recipient_id = $recipientId;
                }

                $message->save();

                // Get recipient information for activity log
                $className = DB::table('class_rooms')->where('id', $request->class_id)->value('name');
                $recipientInfo = $isBroadcast ?
                    "All " . ucfirst($recipientType) . "s in class " . $className :
                    "Single student in class " . $className;

                ActivityService::logMessageActivity('Sent', $request->subject, $recipientInfo);

                return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
            }
        }

        // For non-teacher users, validate as before
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

        $messagesQuery = Message::with('sender')
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
                            $sq->whereNull('deleted_by_users')
                                ->orWhereRaw("NOT JSON_CONTAINS(deleted_by_users, '\"$userKey\"')");
                        });
                });

                // For teachers, also get messages sent to their classes, but exclude their own messages
                if ($userType === 'teacher') {
                    $query->orWhere(function ($q) use ($user) {
                        $q->whereIn('class_id', function ($subquery) use ($user) {
                            $subquery->select('timetables.class_id')
                                ->from('timetables')
                                ->where('timetables.teacher_id', $user->id)
                                ->distinct();
                        })
                            ->where('sender_id', '!=', $user->id); // Exclude messages sent by the teacher
                    });
                }
            })
            ->orderBy('created_at', 'desc');

        $messages = $messagesQuery->paginate(10);

        $unreadCount = (clone $messagesQuery)->where('is_read', false)->count();

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
