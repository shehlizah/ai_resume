<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AbandonedCart;
use App\Services\AbandonedCartService;
use App\Notifications\IncompleteSignupReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class AbandonedCartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_it_tracks_incomplete_signup()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        AbandonedCartService::trackIncompleteSignup($user);

        $this->assertDatabaseHas('abandoned_carts', [
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
        ]);

        $cart = AbandonedCart::where('user_id', $user->id)
            ->where('type', 'signup')
            ->first();

        $this->assertNotNull($cart);
        $this->assertEquals('signup', $cart->type);
        $this->assertEquals('abandoned', $cart->status);
    }

    public function test_it_does_not_create_duplicate_abandoned_carts()
    {
        $user = User::factory()->create();

        AbandonedCartService::trackIncompleteSignup($user);
        AbandonedCartService::trackIncompleteSignup($user);

        $count = AbandonedCart::where('user_id', $user->id)
            ->where('type', 'signup')
            ->count();

        $this->assertEquals(1, $count);
    }

    public function test_it_tracks_incomplete_resume()
    {
        $user = User::factory()->create();

        $resumeData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'title' => 'Software Engineer',
        ];

        AbandonedCartService::trackIncompleteResume($user, 1, $resumeData);

        $this->assertDatabaseHas('abandoned_carts', [
            'user_id' => $user->id,
            'type' => 'resume',
            'status' => 'abandoned',
            'resume_id' => 1,
        ]);

        $cart = AbandonedCart::where('user_id', $user->id)
            ->where('type', 'resume')
            ->first();

        $sessionData = is_string($cart->session_data) ? json_decode($cart->session_data, true) : $cart->session_data;
        $this->assertEquals('John Doe', $sessionData['name']);
        $this->assertEquals('Software Engineer', $sessionData['title']);
    }

    public function test_it_tracks_pdf_preview_abandonment()
    {
        $user = User::factory()->create();

        AbandonedCartService::trackPdfPreviewAbandon($user, 5, 'My Resume', 85);

        $this->assertDatabaseHas('abandoned_carts', [
            'user_id' => $user->id,
            'type' => 'pdf_preview',
            'status' => 'abandoned',
            'resume_id' => 5,
        ]);

        $cart = AbandonedCart::where('user_id', $user->id)
            ->where('type', 'pdf_preview')
            ->first();

        $sessionData = is_string($cart->session_data) ? json_decode($cart->session_data, true) : $cart->session_data;
        $this->assertEquals('My Resume', $sessionData['resume_name']);
        $this->assertEquals(85, $sessionData['score']);
    }

    public function test_it_marks_cart_as_completed()
    {
        $user = User::factory()->create();

        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user->email],
        ]);

        AbandonedCartService::markAsCompleted($user->id, 'signup');

        $cart->refresh();

        $this->assertEquals('completed', $cart->status);
        $this->assertNotNull($cart->completed_at);
    }

    public function test_it_marks_specific_resume_as_completed()
    {
        $user = User::factory()->create();

        $cart1 = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'resume',
            'status' => 'abandoned',
            'resume_id' => 1,
            'session_data' => ['name' => 'Resume 1'],
        ]);

        $cart2 = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'resume',
            'status' => 'abandoned',
            'resume_id' => 2,
            'session_data' => ['name' => 'Resume 2'],
        ]);

        AbandonedCartService::markAsCompleted($user->id, 'resume', 1);

        $cart1->refresh();
        $cart2->refresh();

        $this->assertEquals('completed', $cart1->status);
        $this->assertEquals('abandoned', $cart2->status);
    }

    public function test_it_checks_if_cart_is_abandoned_for_specific_hours()
    {
        $user = User::factory()->create();

        // Create cart with specific old timestamp
        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user->email],
        ]);
        
        // Manually update created_at to be 2 hours ago
        $cart->update(['created_at' => now()->subHours(2)]);
        $cart->refresh();

        $this->assertTrue($cart->isAbandonedFor(1));
        $this->assertTrue($cart->isAbandonedFor(2));
        $this->assertFalse($cart->isAbandonedFor(3));
    }

    public function test_it_determines_if_recovery_email_should_be_sent()
    {
        $user = User::factory()->create();

        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user->email],
            'recovery_email_sent_count' => 0,
        ]);
        
        // Manually set created_at to 2 hours ago
        $cart->update(['created_at' => now()->subHours(2)]);
        $cart->refresh();

        $this->assertTrue($cart->shouldSendRecoveryEmail());

        $cart->update(['recovery_email_sent_count' => 2]);
        $this->assertFalse($cart->shouldSendRecoveryEmail());
    }

    public function test_it_gets_pending_recovery_carts()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $pendingCart = AbandonedCart::create([
            'user_id' => $user1->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user1->email],
            'recovery_email_sent_count' => 0,
        ]);
        $pendingCart->update(['created_at' => now()->subHours(2)]);
        $pendingCart->refresh();

        $recentCart = AbandonedCart::create([
            'user_id' => $user2->id,
            'type' => 'resume',
            'status' => 'abandoned',
            'session_data' => ['name' => 'Test'],
            'recovery_email_sent_count' => 0,
        ]);

        $maxEmailCart = AbandonedCart::create([
            'user_id' => $user2->id,
            'type' => 'pdf_preview',
            'status' => 'abandoned',
            'session_data' => ['resume_name' => 'Test'],
            'recovery_email_sent_count' => 2,
        ]);
        $maxEmailCart->update(['created_at' => now()->subHours(3)]);
        $maxEmailCart->refresh();

        $pending = AbandonedCart::getPendingRecovery();

        $this->assertEquals(1, $pending->count());
        $this->assertTrue($pending->contains($pendingCart));
        $this->assertFalse($pending->contains($recentCart));
        $this->assertFalse($pending->contains($maxEmailCart));
    }

    public function test_it_marks_recovery_email_as_sent()
    {
        $user = User::factory()->create();

        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user->email],
            'recovery_email_sent_count' => 0,
        ]);

        $cart->markRecoveryEmailSent();

        $this->assertEquals(1, $cart->recovery_email_sent_count);
        $this->assertNotNull($cart->first_recovery_email_at);

        $cart->markRecoveryEmailSent();
        $this->assertEquals(2, $cart->recovery_email_sent_count);
    }

    public function test_it_gets_statistics()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        AbandonedCart::create([
            'user_id' => $user1->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user1->email],
        ]);

        AbandonedCart::create([
            'user_id' => $user2->id,
            'type' => 'resume',
            'status' => 'completed',
            'session_data' => ['name' => 'Test'],
            'completed_at' => now(),
        ]);

        AbandonedCart::create([
            'user_id' => $user2->id,
            'type' => 'pdf_preview',
            'status' => 'abandoned',
            'session_data' => ['resume_name' => 'Test'],
            'created_at' => now()->subHours(2),
        ]);

        $stats = AbandonedCartService::getStats();

        $this->assertEquals(3, $stats['total_abandoned']);
        $this->assertEquals(1, $stats['total_recovered']);
        $this->assertArrayHasKey('signup', $stats['by_type']);
        $this->assertArrayHasKey('resume', $stats['by_type']);
        $this->assertArrayHasKey('pdf_preview', $stats['by_type']);
    }

    public function test_admin_can_view_abandoned_carts_index()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.abandoned-carts.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_specific_abandoned_cart()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user->email],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.abandoned-carts.show', $cart->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_delete_abandoned_cart()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user->email],
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.abandoned-carts.destroy', $cart->id));

        $response->assertRedirect(route('admin.abandoned-carts.index'));
        $this->assertDatabaseMissing('abandoned_carts', ['id' => $cart->id]);
    }

    public function test_admin_can_mark_cart_as_completed()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user->email],
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.abandoned-carts.mark-completed', $cart->id));

        $response->assertRedirect();

        $cart->refresh();
        $this->assertEquals('completed', $cart->status);
    }

    public function test_admin_can_send_reminder_email()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user->email],
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.abandoned-carts.send-reminder', $cart->id));

        $response->assertRedirect();

        Notification::assertSentTo($user, IncompleteSignupReminder::class);

        $cart->refresh();
        $this->assertEquals(1, $cart->recovery_email_sent_count);
    }

    public function test_non_admin_cannot_access_abandoned_carts()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.abandoned-carts.index'));

        $response->assertStatus(403);
    }

    public function test_it_filters_abandoned_carts_by_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user->email],
        ]);

        AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'resume',
            'status' => 'completed',
            'session_data' => ['name' => 'Test'],
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.abandoned-carts.index', ['status' => 'abandoned']));

        $response->assertStatus(200);
    }

    public function test_it_filters_abandoned_carts_by_type()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user->email],
        ]);

        AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'resume',
            'status' => 'abandoned',
            'session_data' => ['name' => 'Test'],
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.abandoned-carts.index', ['type' => 'signup']));

        $response->assertStatus(200);
    }

    public function test_it_searches_abandoned_carts_by_email()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['email' => 'john@example.com']);
        $user2 = User::factory()->create(['email' => 'jane@example.com']);

        AbandonedCart::create([
            'user_id' => $user1->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => ['email' => $user1->email],
        ]);

        AbandonedCart::create([
            'user_id' => $user2->id,
            'type' => 'resume',
            'status' => 'abandoned',
            'session_data' => ['email' => $user2->email],
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.abandoned-carts.index', ['search' => 'john']));

        $response->assertStatus(200);
    }
}
