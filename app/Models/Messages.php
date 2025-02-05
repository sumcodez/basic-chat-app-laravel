<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory;

        /**
     * The table associated with the model.
     *
     * @var string
    */
    protected $table = 'chat_messages'; // Updated table name

    protected $fillable = [
        'sender_id', 
        'receiver_id', 
        'message', 
        'media_type', 
        'media_url'
    ];

    // Define relationships if needed
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
