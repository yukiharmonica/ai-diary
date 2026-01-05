<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// タイムラインで使用している 'users.{id}' チャンネルのアクセス許可定義
Broadcast::channel('users.{id}', function ($user, $id) {
    // ログイン中のユーザーIDと、チャンネルのIDが一致すれば許可
    return (int) $user->id === (int) $id;
});
