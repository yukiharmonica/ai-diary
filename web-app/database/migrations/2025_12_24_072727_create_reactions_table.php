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
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            // 投稿削除時にリアクションも削除
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('bot_name'); // 美食家ボット, etc.
            $table->integer('bot_persona_id'); // 内部管理用ID
            $table->text('response_text'); // AIからの返信
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
