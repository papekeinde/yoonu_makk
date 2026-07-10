<?php

namespace App\Models;

use App\Enums\Langue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageChatbot extends Model
{
    use HasFactory;

    protected $table = 'messages_chatbot';

    protected $fillable = [
        'user_id',
        'session_id',
        'role',
        'message',
        'langue',
    ];

    protected $casts = [
        'langue' => Langue::class,
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
