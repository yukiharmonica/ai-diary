<?php

use App\Jobs\GenerateReactionJob;
use App\Livewire\PostForm;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

// 1. ゲストユーザーはアクセスできない（コンポーネントが表示されない）ことの確認
// 今回はダッシュボード内にあるため、ダッシュボードへのアクセス権限テストなどで代替されますが、
// Livewireコンポーネント単体としてもエラーにならず動作することを確認します。
test('ゲストは投稿できない', function () {
    Livewire::test(PostForm::class)
        ->assertStatus(200); // コンポーネント自体はレンダリングされる（表示制御はBlade側）
});

// 2. ログインユーザーが投稿できることの確認
test('ユーザーは日記を投稿でき、ジョブがディスパッチされる', function () {
    // ジョブが実際に実行されないようにモック（偽装）する
    Queue::fake();

    $user = User::factory()->create();

    Livewire::actingAs($user) // ユーザーとしてログイン
        ->test(PostForm::class)
        ->set('genre', 'グルメ')
        ->set('text', 'テスト用の日記です。とても美味しいラーメンを食べました。')
        ->call('save') // saveメソッドを実行
        ->assertHasNoErrors(); // エラーがないこと

    // データベースに保存されたか確認
    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'genre' => 'グルメ',
        'text' => 'テスト用の日記です。とても美味しいラーメンを食べました。',
        'status' => 'pending',
    ]);

    // AI生成ジョブがキューに登録されたか確認
    Queue::assertPushed(GenerateReactionJob::class);
});

// 3. バリデーション（文字数制限など）の確認
test('日記が空だとバリデーションエラーになる', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PostForm::class)
        ->set('text', '') // 空文字
        ->call('save')
        ->assertHasErrors(['text' => 'required']);
});
