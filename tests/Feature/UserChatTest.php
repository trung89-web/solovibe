<?php

use App\Models\User;

it('allows an authenticated user to send a chat message', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/user/chat/send', [
        'message' => 'Xin chào admin',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('status', 'success');

    $this->assertDatabaseHas('messages', [
        'sender_id' => $user->id,
        'content' => 'Xin chào admin',
    ]);
});
