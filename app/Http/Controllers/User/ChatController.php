<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getMessages()
    {
        $userId = Auth::id();

        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = Auth::user();

        if ($user && $user->is_locked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản của bạn đã bị khóa, vui lòng liên hệ quản trị viên.',
            ], 403);
        }

        $admin = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'Admin', 'Super Admin', 'Content Staff']);
        })->first();

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $admin ? $admin->id : null,
            'content' => $request->message,
        ]);

        return response()->json(['status' => 'success', 'data' => $message]);
    }

    public function sendMessage(Request $request)
    {
        return $this->send($request);
    }
}