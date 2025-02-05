<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Connections;
use App\Models\Messages;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{


    public function showChat(User $user) {
        $current_user = Auth::user();
    
        if (!$current_user) {
            return redirect()->route('login')->with('error', 'Please log in to access the chat.');
        }

        // Check if the connection still exists
        $connection = Connections::where(function ($query) use ($current_user, $user) {
            $query->where('sender_id', $current_user->id)
                ->where('receiver_id', $user->id);
        })
        ->orWhere(function ($query) use ($current_user, $user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', $current_user->id);
        })
        ->where('status', 'accepted')
        ->first();

        if (!$connection) {
            // If no connection found, redirect to homepage with a message
            return redirect()->route('users.all')->with('error', 'Connection no longer exists.');
        }
    
        // Fetch accepted connections where the current user is either sender or receiver
        $connections = Connections::where(function ($query) use ($current_user) {
                $query->where('sender_id', $current_user->id)
                      ->orWhere('receiver_id', $current_user->id);
            })
            ->where('status', 'accepted')
            ->get();
    
        // Extract connected user IDs (excluding current user)
        $userIds = $connections->map(function ($connection) use ($current_user) {
            return $connection->sender_id == $current_user->id ? $connection->receiver_id : $connection->sender_id;
        });
    
        // Fetch user details
        $connectedUsersList = User::whereIn('id', $userIds)->get();
    
        // Get the last message between the current user and each connected user
        $connectedUsersList = $connectedUsersList->map(function ($user) use ($current_user) {
            $lastMessage = Messages::where(function ($query) use ($current_user, $user) {
                    $query->where('sender_id', $current_user->id)->where('receiver_id', $user->id);
                })
                ->orWhere(function ($query) use ($current_user, $user) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $current_user->id);
                })
                ->latest('created_at') // Order by latest message
                ->first();

            // Count unread messages
            $unreadCount = Messages::where('sender_id', $user->id)
                ->where('receiver_id', $current_user->id)
                ->where('read_status', 0)
                ->count();
    
            // Add the last message to the user data
            $user->last_message = $lastMessage ? $lastMessage->message : null;
            $user->last_message_time = $lastMessage ? $lastMessage->created_at->format('h:i A') : null;
            $user->unreadCount = $unreadCount;
            
            return $user;
        });

        // Sort the users by the last_message_time in descending order (latest first)
        $connectedUsersList = $connectedUsersList->sortByDesc(function ($user) {
            return $user->last_message_time;
        });
    
        return view('users.chat', [
            'current_user' => $current_user,
            'chat_user' => $user,
            'connected_users' => $connectedUsersList, // Include last message
        ]);
    }
    

    // Get messages between two users
    public function getMessages($userId, $chatUserId) {
        $messages = Message::where(function ($query) use ($userId, $chatUserId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $chatUserId);
        })
        ->orWhere(function ($query) use ($userId, $chatUserId) {
            $query->where('sender_id', $chatUserId)
                ->where('receiver_id', $userId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json($messages);
    }



    public function getMessages10($userId, $chatUserId) {
        // Get the current timestamp (you can also use Carbon to modify this to any other timestamp)
        $currentTimestamp = \Carbon\Carbon::now();
    
        // Optionally, you can filter messages within a certain time range, e.g., the last 24 hours
        $timeRange = \Carbon\Carbon::now()->subDays(1); // Last 24 hours
    
        // Fetch the latest 10 messages between the users, ordered by created_at in descending order
        $messages = Messages::where(function ($query) use ($userId, $chatUserId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $chatUserId);
            })
            ->orWhere(function ($query) use ($userId, $chatUserId) {
                $query->where('sender_id', $chatUserId)
                    ->where('receiver_id', $userId);
            })
            // Filter messages created within the last 24 hours (or any other range you prefer)
            // ->where('created_at', '>=', $timeRange) 
            ->orderBy('created_at', 'desc') // Fetch messages in descending order (latest first)
            ->take(10) // Limit to the last 10 messages
            ->get()
            ->sortBy('created_at') // Sort them in ascending order for proper display
            ->values(); // Reset the collection indices
    
        // Check if messages are found
        if ($messages->isEmpty()) {
            return response()->json(['error' => 'No messages found'], 404);
        }

        // Get message IDs where sender is chatUserId (i.e., messages that userId received)
        $unreadMessageIds = $messages->where('sender_id', $chatUserId)->where('read_status', 0)->pluck('id');

        // Update read_status to 1 for those messages
        if ($unreadMessageIds->isNotEmpty()) {
            Messages::whereIn('id', $unreadMessageIds)->update(['read_status' => 1]);
        }
    
        // Return the messages as JSON
        return response()->json($messages);
    }
    

    // Send message function
    public function sendMessage(Request $request) {
        $validated = $request->validate([
            'sender_id' => 'required|exists:chat_users,id',
            'receiver_id' => 'required|exists:chat_users,id',
            'message' => 'nullable|string|max:500',
            'media_type' => 'nullable|in:image,video,gif,text',
            'media_url' => 'nullable|url',
        ]);
    
        // Create the message
        $message = Messages::create([
            'sender_id' => $validated['sender_id'],
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'] ?? null,
            'media_type' => $validated['media_type'] ?? 'text', // Default to 'text'
            'media_url' => $validated['media_url'] ?? null,
        ]);
    
        return response()->json(['status' => 'Message sent successfully!']);
    }
    

    public function destroy($messageId)
    {
        $message = Messages::find($messageId);

        if ($message && $message->sender_id == auth()->id()) {
            $message->delete();
            return response()->json(['message' => 'Message deleted successfully']);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    

    // public function uploadMedia(Request $request)
    // {
    //     try {
    //         // Validate input
    //         $request->validate([
    //             'sender_id' => 'required|integer',
    //             'receiver_id' => 'required|integer',
    //             'message' => 'nullable|string',
    //             'media' => 'nullable|file|mimes:jpeg,png,gif,mp4,mov,avi,pdf|max:3072', 
    //         ],[
    //             'media.max' => 'The uploaded file must not exceed 3MB.',
    //         ]);

    //         $mediaType = null;
    //         $mediaUrl = null;

    //         if ($request->hasFile('media')) {
    //             $file = $request->file('media');
    //             $extension = $file->getClientOriginalExtension();
    //             $filename = time() . '_' . uniqid() . '.' . $extension;

    //             if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
    //                 $mediaType = 'image';
    //                 $folder = 'media/images/';
    //             } elseif (in_array($extension, ['mp4', 'mov', 'avi'])) {
    //                 $mediaType = 'video';
    //                 $folder = 'media/videos/';
    //             } elseif ($extension === 'pdf') {
    //                 $mediaType = 'pdf';
    //                 $folder = 'media/pdf/';
    //             } else {
    //                 return response()->json(['error' => 'Invalid file type'], 400);
    //             }

    //             // Store file in storage/app/public/
    //             $path = $file->storeAs($folder, $filename, 'public');
    //             $mediaUrl = asset('storage/' . $path);
    //         }

    //         // Save message in database
    //         $message = Messages::create([
    //             'sender_id' => $request->sender_id,
    //             'receiver_id' => $request->receiver_id,
    //             'message' => $request->message ?? '',
    //             'media_type' => $mediaType, // Default null
    //             'media_url' => $mediaUrl,
    //             'read_status' => 0
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Message sent successfully!',
    //             'data' => $message
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'Something went wrong: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function uploadMedia(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'sender_id' => 'required|integer',
                'receiver_id' => 'required|integer',
                'message' => 'nullable|string',
                'media' => 'nullable|file|mimes:jpeg,png,gif,mp4,mov,avi,pdf|max:3072',
            ], [
                'media.max' => 'The uploaded file must not exceed 3MB.',
            ]);

            $mediaType = null;
            $mediaUrl = null;

            if ($request->hasFile('media')) {
                $file = $request->file('media');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '.' . $extension;

                // Determine folder based on file type
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $mediaType = 'image';
                    $folder = 'media/images/';
                } elseif (in_array($extension, ['mp4', 'mov', 'avi'])) {
                    $mediaType = 'video';
                    $folder = 'media/videos/';
                } elseif ($extension === 'pdf') {
                    $mediaType = 'pdf';
                    $folder = 'media/pdf/';
                } else {
                    return response()->json(['error' => 'Invalid file type'], 400);
                }

                // Move file to public/media/ directory
                $file->move(public_path($folder), $filename);
                $mediaUrl = asset($folder . $filename);
            }

            // Save message in database
            $message = Messages::create([
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message ?? '',
                'media_type' => $mediaType,
                'media_url' => $mediaUrl,
                'read_status' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully!',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }


    public function fetchMessages_offset(Request $request)
    {
        $userId = auth()->id();
        $contactId = $request->contact_id;
        $offset = $request->offset ?? 0; // Default offset is 0
        $limit = 10; // Fetch 10 messages per request

        $messages = Messages::where(function ($query) use ($userId, $contactId) {
                $query->where('sender_id', $userId)->where('receiver_id', $contactId);
            })
            ->orWhere(function ($query) use ($userId, $contactId) {
                $query->where('sender_id', $contactId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'desc') // Get latest messages first
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->reverse()->values(); // Reverse so older messages appear at the top

        return response()->json($messages->reverse()->values());
    }


    // Delete all chat between two user
    public function deleteChat($chatUserId)
    {
        $userId = auth()->id();

        // Check if user is authenticated
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete all messages between the two users
        Messages::where(function ($query) use ($userId, $chatUserId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $chatUserId);
            })
            ->orWhere(function ($query) use ($userId, $chatUserId) {
                $query->where('sender_id', $chatUserId)
                    ->where('receiver_id', $userId);
            })
            ->delete();

        
        // Find and delete the connection between the users
        $connection = Connections::where(function ($query) use ($userId, $chatUserId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $chatUserId);
        })
        ->orWhere(function ($query) use ($userId, $chatUserId) {
            $query->where('sender_id', $chatUserId)
                ->where('receiver_id', $userId);
        })
        ->first();


        if ($connection) {
            $connection->delete(); // Remove the connection
        }    

        return response()->json(['message' => 'Chat deleted successfully']);
    }
}
