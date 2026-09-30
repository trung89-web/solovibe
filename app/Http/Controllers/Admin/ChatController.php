<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getUsers()
    {
        $userIds = Message::pluck('sender_id')
            ->merge(Message::pluck('receiver_id'))
            ->filter(fn($id) => $id != Auth::id() && $id != null)
            ->unique();

        $users = User::whereIn('id', $userIds)->get(['id', 'name']);

        return response()->json($users);
    }

    public function getMessages($userId)
    {
        $adminId = Auth::id();
        $messages = Message::where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->whereNull('receiver_id');
            })
            ->orWhere(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $userId)->where('receiver_id', $adminId);
            })
            ->orWhere(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $adminId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function showHistory(User $user)
    {
        $adminId = Auth::id();

        $messages = Message::where(function ($q) use ($user, $adminId) {
                $q->where('sender_id', $user->id)
                  ->where(function ($sub) use ($adminId) {
                      $sub->where('receiver_id', $adminId)
                           ->orWhereNull('receiver_id');
                  });
            })
            ->orWhere(function ($q) use ($user, $adminId) {
                $q->where('sender_id', $adminId)
                  ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.chat.history', compact('user', 'messages'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->is_locked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản khách hàng đang bị khóa, không thể gửi tin nhắn.',
            ], 403);
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->user_id,
            'content' => $request->message,
        ]);

        return response()->json(['status' => 'success', 'data' => $message]);
    }

    public function sendMessage(Request $request)
    {
        return $this->send($request);
    }
}