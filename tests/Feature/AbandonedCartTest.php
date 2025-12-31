<?php

use App\Models\User;
use App\Models\AbandonedCart;
use App\Services\AbandonedCartService;
use App\Notifications\IncompleteSignupReminder;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

test('it tracks incomplete signup', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    AbandonedCartService::trackIncompleteSignup($user);

    expect(AbandonedCart::where('user_id', $user->id)
        ->where('type', 'signup')
        ->where('status', 'abandoned')
        ->exists())->toBeTrue();

    $cart = AbandonedCart::where('user_id', $user->id)
        ->where('type', 'signup')
        ->first();

    expect($cart)->not->toBeNull();
    expect($cart->type)->toBe('signup');
    expect($cart->status)->toBe('abandoned');
});

test('it does not create duplicate abandoned carts', function () {
    $user = User::factory()->create();

    AbandonedCartService::trackIncompleteSignup($user);
    AbandonedCartService::trackIncompleteSignup($user);

    $count = AbandonedCart::where('user_id', $user->id)
        ->where('type', 'signup')
        ->count();

    expect($count)->toBe(1);
});

test('it tracks incomplete resume', function () {
    $user = User::factory()->create();

    $resumeData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'title' => 'Software Engineer',
    ];

    AbandonedCartService::trackIncompleteResume($user, 1, $resumeData);

    expect(AbandonedCart::where('user_id', $user->id)
        ->where('type', 'resume')
        ->where('status', 'abandoned')
        ->where('resume_id', 1)
        ->exists())->toBeTrue();

    $cart = AbandonedCart::where('user_id', $user->id)
        ->where('type', 'resume')
        ->first();

    $sessionData = json_decode($cart->session_data, true);
    expect($sessionData['name'])->toBe('John Doe');
    expect($sessionData['title'])->toBe('Software Engineer');
});

test('it tracks pdf preview abandonment', function () {
    $user = User::factory()->create();

    AbandonedCartService::trackPdfPreviewAbandon($user, 5, 'My Resume', 85);

    expect(AbandonedCart::where('user_id', $user->id)
        ->where('type', 'pdf_preview')
        ->where('status', 'abandoned')
        ->where('resume_id', 5)
        ->exists())->toBeTrue();

    $cart = AbandonedCart::where('user_id', $user->id)
        ->where('type', 'pdf_preview')
        ->first();

    $sessionData = json_decode($cart->session_data, true);
    expect($sessionData['resume_name'])->toBe('My Resume');
    expect($sessionData['score'])->toBe(85);
});

test('it marks cart as completed', function () {
    $user = User::factory()->create();

    $cart = AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user->email]),
    ]);

    AbandonedCartService::markAsCompleted($user->id, 'signup');

    $cart->refresh();

    expect($cart->status)->toBe('completed');
    expect($cart->completed_at)->not->toBeNull();
});

test('it marks specific resume as completed', function () {
    $user = User::factory()->create();

    $cart1 = AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'resume',
        'status' => 'abandoned',
        'resume_id' => 1,
        'session_data' => json_encode(['name' => 'Resume 1']),
    ]);

    $cart2 = AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'resume',
        'status' => 'abandoned',
        'resume_id' => 2,
        'session_data' => json_encode(['name' => 'Resume 2']),
    ]);

    AbandonedCartService::markAsCompleted($user->id, 'resume', 1);

    $cart1->refresh();
    $cart2->refresh();

    expect($cart1->status)->toBe('completed');
    expect($cart2->status)->toBe('abandoned');
});

test('it checks if cart is abandoned for specific hours', function () {
    $user = User::factory()->create();

    $cart = AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user->email]),
        'created_at' => now()->subHours(2),
    ]);

    expect($cart->isAbandonedFor(1))->toBeTrue();
    expect($cart->isAbandonedFor(2))->toBeTrue();
    expect($cart->isAbandonedFor(3))->toBeFalse();
});

test('it determines if recovery email should be sent', function () {
    $user = User::factory()->create();

    $cart = AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user->email]),
        'created_at' => now()->subHours(2),
        'recovery_email_sent_count' => 0,
    ]);

    expect($cart->shouldSendRecoveryEmail())->toBeTrue();

    $cart->update(['recovery_email_sent_count' => 2]);
    expect($cart->shouldSendRecoveryEmail())->toBeFalse();
});

test('it gets pending recovery carts', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $pendingCart = AbandonedCart::create([
        'user_id' => $user1->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user1->email]),
        'created_at' => now()->subHours(2),
        'recovery_email_sent_count' => 0,
    ]);

    $recentCart = AbandonedCart::create([
        'user_id' => $user2->id,
        'type' => 'resume',
        'status' => 'abandoned',
        'session_data' => json_encode(['name' => 'Test']),
        'created_at' => now()->subMinutes(30),
        'recovery_email_sent_count' => 0,
    ]);

    $maxEmailCart = AbandonedCart::create([
        'user_id' => $user2->id,
        'type' => 'pdf_preview',
        'status' => 'abandoned',
        'session_data' => json_encode(['resume_name' => 'Test']),
        'created_at' => now()->subHours(3),
        'recovery_email_sent_count' => 2,
    ]);

    $pending = AbandonedCart::getPendingRecovery();

    expect($pending->count())->toBe(1);
    expect($pending->contains($pendingCart))->toBeTrue();
    expect($pending->contains($recentCart))->toBeFalse();
    expect($pending->contains($maxEmailCart))->toBeFalse();
});

test('it marks recovery email as sent', function () {
    $user = User::factory()->create();

    $cart = AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user->email]),
        'recovery_email_sent_count' => 0,
    ]);

    $cart->markRecoveryEmailSent();

    expect($cart->recovery_email_sent_count)->toBe(1);
    expect($cart->first_recovery_email_at)->not->toBeNull();

    $cart->markRecoveryEmailSent();
    expect($cart->recovery_email_sent_count)->toBe(2);
});

test('it gets statistics', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    AbandonedCart::create([
        'user_id' => $user1->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user1->email]),
    ]);

    AbandonedCart::create([
        'user_id' => $user2->id,
        'type' => 'resume',
        'status' => 'completed',
        'session_data' => json_encode(['name' => 'Test']),
        'completed_at' => now(),
    ]);

    AbandonedCart::create([
        'user_id' => $user2->id,
        'type' => 'pdf_preview',
        'status' => 'abandoned',
        'session_data' => json_encode(['resume_name' => 'Test']),
        'created_at' => now()->subHours(2),
    ]);

    $stats = AbandonedCartService::getStats();

    expect($stats['total_abandoned'])->toBe(3);
    expect($stats['total_recovered'])->toBe(1);
    expect($stats['by_type'])->toHaveKey('signup');
    expect($stats['by_type'])->toHaveKey('resume');
    expect($stats['by_type'])->toHaveKey('pdf_preview');
});

test('admin can view abandoned carts index', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('admin.abandoned-carts.index'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.abandoned-carts.index');
    $response->assertViewHas('carts');
    $response->assertViewHas('stats');
});

test('admin can view specific abandoned cart', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    $cart = AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user->email]),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.abandoned-carts.show', $cart->id));

    $response->assertStatus(200);
    $response->assertViewIs('admin.abandoned-carts.show');
    $response->assertViewHas('cart');
});

test('admin can delete abandoned cart', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    $cart = AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user->email]),
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.abandoned-carts.destroy', $cart->id));

    $response->assertRedirect(route('admin.abandoned-carts.index'));

    expect(AbandonedCart::find($cart->id))->toBeNull();
});

test('admin can mark cart as completed', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    $cart = AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user->email]),
    ]);

    $response = $this->actingAs($admin)
        ->patch(route('admin.abandoned-carts.mark-completed', $cart->id));

    $response->assertRedirect();

    $cart->refresh();
    expect($cart->status)->toBe('completed');
});

test('admin can send reminder email', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    $cart = AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user->email]),
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.abandoned-carts.send-reminder', $cart->id));

    $response->assertRedirect();

    Notification::assertSentTo($user, IncompleteSignupReminder::class);

    $cart->refresh();
    expect($cart->recovery_email_sent_count)->toBe(1);
});

test('non admin cannot access abandoned carts', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->get(route('admin.abandoned-carts.index'));

    $response->assertStatus(403);
});

test('it filters abandoned carts by status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user->email]),
    ]);

    AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'resume',
        'status' => 'completed',
        'session_data' => json_encode(['name' => 'Test']),
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.abandoned-carts.index', ['status' => 'abandoned']));

    $response->assertStatus(200);

    $carts = $response->viewData('carts');
    expect($carts->count())->toBe(1);
    expect($carts->first()->status)->toBe('abandoned');
});

test('it filters abandoned carts by type', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user->email]),
    ]);

    AbandonedCart::create([
        'user_id' => $user->id,
        'type' => 'resume',
        'status' => 'abandoned',
        'session_data' => json_encode(['name' => 'Test']),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.abandoned-carts.index', ['type' => 'signup']));

    $response->assertStatus(200);

    $carts = $response->viewData('carts');
    expect($carts->count())->toBe(1);
    expect($carts->first()->type)->toBe('signup');
});

test('it searches abandoned carts by email', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user1 = User::factory()->create(['email' => 'john@example.com']);
    $user2 = User::factory()->create(['email' => 'jane@example.com']);

    AbandonedCart::create([
        'user_id' => $user1->id,
        'type' => 'signup',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user1->email]),
    ]);

    AbandonedCart::create([
        'user_id' => $user2->id,
        'type' => 'resume',
        'status' => 'abandoned',
        'session_data' => json_encode(['email' => $user2->email]),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.abandoned-carts.index', ['search' => 'john']));

    $response->assertStatus(200);

    $carts = $response->viewData('carts');
    expect($carts->count())->toBe(1);
    expect($carts->first()->user->email)->toBe('john@example.com');
});
