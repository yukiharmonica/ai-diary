<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'bot_name',
        'bot_persona_id',
        'response_text',
    ];

    // 投稿とのリレーション
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
