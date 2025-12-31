<?php

use App\Models\User;

test('basic test example', function () {
    expect(true)->toBeTrue();
});

test('can create a user', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);
    
    expect($user->email)->toBe('test@example.com');
});
