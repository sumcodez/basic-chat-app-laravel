<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Connections;

class ConnectionController extends Controller
{
    public function send($receiver_id)
    {
        $sender_id = Auth::id();

        // Check if a connection already exists
        $existingConnection = Connections::where(function ($query) use ($sender_id, $receiver_id) {
            $query->where('sender_id', $sender_id)->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($sender_id, $receiver_id) {
            $query->where('sender_id', $receiver_id)->where('receiver_id', $sender_id);
        })->first();

        if ($existingConnection) {
            return response()->json(['message' => 'Connection already exists.'], 400);
        }

        // Create a new connection
        Connections::create([
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Connection request sent.');
    }


    public function accept($connection_id)
    {
        $connection = Connections::findOrFail($connection_id);
        $connection->update(['status' => 'accepted']);

        return redirect()->back()->with('success', 'Connection accepted.');
    }

    public function decline($connection_id)
    {
        $connection = Connections::findOrFail($connection_id);
        $connection->delete();

        return redirect()->back()->with('success', 'Connection declined.');
    }

    public function endChat($connection_id)
    {
        $connection = Connections::findOrFail($connection_id);

        // Ensure only participants can end the chat
        if ($connection->sender_id !== auth()->id() && $connection->receiver_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $connection->delete();

        return redirect()->back()->with('success', 'Chat ended successfully.');
    }
}
