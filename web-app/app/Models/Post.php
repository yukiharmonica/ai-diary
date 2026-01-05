<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    public const GENRES = [
        '日常' => '日常 - Daily Life',
        'グルメ' => 'グルメ - Gourmet',
        '運動' => '運動・健康 - Fitness',
        '読書' => '読書 - Reading',
        '学習' => '学習・自己啓発 - Learning',
        '仕事' => '仕事・キャリア - Work',
        '旅行' => '旅行・お出かけ - Travel',
        '趣味' => '趣味・エンタメ - Hobby',
        'その他' => 'その他 - Others',
    ];

    protected $fillable = [
        'user_id',
        'text',
        'status',
        'genre',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }
}