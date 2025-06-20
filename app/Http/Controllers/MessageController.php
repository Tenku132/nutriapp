<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;

class MessageController extends Controller
{
    // Restrict messaging access for unverified farmers
    protected function restrictUnverifiedFarmer()
    {
        if (Auth::user()->role === 'farmer' && !Auth::user()->is_verified) {
            return redirect()->route('farmer.settings')->with('error', 'Please verify your account to access messaging.');
        }
        return null;
    }

    public function inbox()
    {
        if ($redirect = $this->restrictUnverifiedFarmer()) return $redirect;

        $userId = Auth::id();

        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get()
            ->groupBy(function ($msg) use ($userId) {
                return $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
            });

        $role = Auth::user()->role;
        $view = $role === 'farmer' ? 'farmer.messages.index' : 'buyer.messages.index';

        return view($view, compact('conversations'));
    }

    public function show($userId)
    {
        if ($redirect = $this->restrictUnverifiedFarmer()) return $redirect;

        $authId = Auth::id();

        $messages = Message::where(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $authId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $authId);
        })->get();

        $conversations = Message::where('sender_id', $authId)
            ->orWhere('receiver_id', $authId)
            ->get()
            ->groupBy(function ($msg) use ($authId) {
                return $msg->sender_id === $authId ? $msg->receiver_id : $msg->sender_id;
            });

        $role = Auth::user()->role;
        $view = $role === 'farmer' ? 'farmer.messages.show' : 'buyer.messages.show';

        return view($view, [
            'messages' => $messages,
            'conversations' => $conversations,
            'userId' => $userId
        ]);
    }

    public function reply(Request $request, $userId)
    {
        if ($redirect = $this->restrictUnverifiedFarmer()) return $redirect;

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $userId,
            'message' => $request->message,
            'is_read' => false,
        ]);

        $receiver = User::find($userId);
        $senderName = Auth::user()->name;

        $receiver->notify(new NewMessageNotification([
            'sender' => $senderName,
            'sender_id' => Auth::id()
        ]));

        $route = Auth::user()->role === 'farmer' ? 'farmer.messages.show' : 'buyer.messages.show';
        return redirect()->route($route, $userId);
    }

    public function create()
    {
        if ($redirect = $this->restrictUnverifiedFarmer()) return $redirect;

        $role = Auth::user()->role;
        $users = $role === 'farmer'
            ? User::where('role', 'buyer')->get()
            : User::where('role', 'farmer')->get();

        $view = $role === 'farmer' ? 'farmer.messages.create' : 'buyer.messages.create';
        return view($view, ['users' => $users]);
    }

    public function store(Request $request)
    {
        if ($redirect = $this->restrictUnverifiedFarmer()) return $redirect;

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        $receiver = User::find($request->receiver_id);
        $senderName = Auth::user()->name;

        $receiver->notify(new NewMessageNotification([
            'sender' => $senderName
        ]));

        $route = Auth::user()->role === 'farmer' ? 'farmer.messages.show' : 'buyer.messages.show';
        return redirect()->route($route, $request->receiver_id)->with('success', 'Message sent!');
    }
}
