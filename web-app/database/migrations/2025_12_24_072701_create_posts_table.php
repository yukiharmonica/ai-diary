<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            // ユーザー削除時に投稿も削除されるように cascade を設定
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('text'); // 日記本文 (バリデーションで400文字制限)
            // 処理状態: pending(処理待ち), processing(生成中), completed(完了), failed(失敗)
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('genre'); // 日常, グルメ, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
