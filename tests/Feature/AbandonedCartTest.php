<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AbandonedCart;
use App\Models\UserResume;
use App\Services\AbandonedCartService;
use App\Notifications\IncompleteSignupReminder;
use App\Notifications\IncompleteResumeReminder;
use App\Notifications\PdfPreviewUpgradeReminder;
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

    /** @test */
    public function it_tracks_incomplete_signup()
    {
        // Create a user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Track incomplete signup
        AbandonedCartService::trackIncompleteSignup($user);

        // Assert cart was created
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

    /** @test */
    public function it_prevents_duplicate_signup_tracking_within_5_minutes()
    {
        $user = User::factory()->create();

        // Track first time
        AbandonedCartService::trackIncompleteSignup($user);

        // Try to track again immediately
        AbandonedCartService::trackIncompleteSignup($user);

        // Should only have one record
        $count = AbandonedCart::where('user_id', $user->id)
            ->where('type', 'signup')
            ->count();

        $this->assertEquals(1, $count);
    }

    /** @test */
    public function it_tracks_incomplete_resume()
    {
        $user = User::factory()->create();

        $resumeData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'title' => 'Software Engineer',
        ];

        // Track incomplete resume
        AbandonedCartService::trackIncompleteResume($user, 1, $resumeData);

        // Assert cart was created
        $this->assertDatabaseHas('abandoned_carts', [
            'user_id' => $user->id,
            'type' => 'resume',
            'status' => 'abandoned',
            'resume_id' => 1,
        ]);

        $cart = AbandonedCart::where('user_id', $user->id)
            ->where('type', 'resume')
            ->first();

        $sessionData = json_decode($cart->session_data, true);
        $this->assertEquals('John Doe', $sessionData['name']);
        $this->assertEquals('Software Engineer', $sessionData['title']);
    }

    /** @test */
    public function it_tracks_pdf_preview_abandonment()
    {
        $user = User::factory()->create();

        // Track PDF preview abandonment
        AbandonedCartService::trackPdfPreviewAbandon($user, 5, 'My Resume', 85);

        // Assert cart was created
        $this->assertDatabaseHas('abandoned_carts', [
            'user_id' => $user->id,
            'type' => 'pdf_preview',
            'status' => 'abandoned',
            'resume_id' => 5,
        ]);

        $cart = AbandonedCart::where('user_id', $user->id)
            ->where('type', 'pdf_preview')
            ->first();

        $sessionData = json_decode($cart->session_data, true);
        $this->assertEquals('My Resume', $sessionData['resume_name']);
        $this->assertEquals(85, $sessionData['score']);
    }

    /** @test */
    public function it_marks_cart_as_completed()
    {
        $user = User::factory()->create();

        // Create abandoned cart
        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => json_encode(['email' => $user->email]),
        ]);

        // Mark as completed
        AbandonedCartService::markAsCompleted($user->id, 'signup');

        // Refresh cart
        $cart->refresh();

        $this->assertEquals('completed', $cart->status);
        $this->assertNotNull($cart->completed_at);
    }

    /** @test */
    public function it_marks_specific_resume_as_completed()
    {
        $user = User::factory()->create();

        // Create two abandoned resume carts
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

        // Mark only resume 1 as completed
        AbandonedCartService::markAsCompleted($user->id, 'resume', 1);

        $cart1->refresh();
        $cart2->refresh();

        $this->assertEquals('completed', $cart1->status);
        $this->assertEquals('abandoned', $cart2->status);
    }

    /** @test */
    public function it_checks_if_cart_is_abandoned_for_specific_hours()
    {
        $user = User::factory()->create();

        // Create cart 2 hours ago
        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => json_encode(['email' => $user->email]),
            'created_at' => now()->subHours(2),
        ]);

        $this->assertTrue($cart->isAbandonedFor(1)); // Abandoned for more than 1 hour
        $this->assertTrue($cart->isAbandonedFor(2)); // Abandoned for at least 2 hours
        $this->assertFalse($cart->isAbandonedFor(3)); // Not abandoned for 3 hours
    }

    /** @test */
    public function it_determines_if_recovery_email_should_be_sent()
    {
        $user = User::factory()->create();

        // Create cart abandoned for 2 hours with no emails sent
        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => json_encode(['email' => $user->email]),
            'created_at' => now()->subHours(2),
            'recovery_email_sent_count' => 0,
        ]);

        $this->assertTrue($cart->shouldSendRecoveryEmail());

        // After 2 emails sent
        $cart->update(['recovery_email_sent_count' => 2]);
        $this->assertFalse($cart->shouldSendRecoveryEmail()); // Max 2 emails
    }

    /** @test */
    public function it_gets_pending_recovery_carts()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Cart abandoned for 2 hours - should be pending
        $pendingCart = AbandonedCart::create([
            'user_id' => $user1->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => json_encode(['email' => $user1->email]),
            'created_at' => now()->subHours(2),
            'recovery_email_sent_count' => 0,
        ]);

        // Cart abandoned for 30 minutes - too soon
        $recentCart = AbandonedCart::create([
            'user_id' => $user2->id,
            'type' => 'resume',
            'status' => 'abandoned',
            'session_data' => json_encode(['name' => 'Test']),
            'created_at' => now()->subMinutes(30),
            'recovery_email_sent_count' => 0,
        ]);

        // Cart with 2 emails already sent - max reached
        $maxEmailCart = AbandonedCart::create([
            'user_id' => $user2->id,
            'type' => 'pdf_preview',
            'status' => 'abandoned',
            'session_data' => json_encode(['resume_name' => 'Test']),
            'created_at' => now()->subHours(3),
            'recovery_email_sent_count' => 2,
        ]);

        $pending = AbandonedCart::getPendingRecovery();

        $this->assertEquals(1, $pending->count());
        $this->assertTrue($pending->contains($pendingCart));
        $this->assertFalse($pending->contains($recentCart));
        $this->assertFalse($pending->contains($maxEmailCart));
    }

    /** @test */
    public function it_marks_recovery_email_as_sent()
    {
        $user = User::factory()->create();

        $cart = AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => json_encode(['email' => $user->email]),
            'recovery_email_sent_count' => 0,
        ]);

        $cart->markRecoveryEmailSent();

        $this->assertEquals(1, $cart->recovery_email_sent_count);
        $this->assertNotNull($cart->first_recovery_email_at);

        // Send another email
        $cart->markRecoveryEmailSent();
        $this->assertEquals(2, $cart->recovery_email_sent_count);
    }

    /** @test */
    public function it_gets_statistics()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create various abandoned carts
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

        $this->assertEquals(3, $stats['total_abandoned']);
        $this->assertEquals(1, $stats['total_recovered']);
        $this->assertArrayHasKey('signup', $stats['by_type']);
        $this->assertArrayHasKey('resume', $stats['by_type']);
        $this->assertArrayHasKey('pdf_preview', $stats['by_type']);
    }

    /** @test */
    public function admin_can_view_abandoned_carts_index()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.abandoned-carts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.abandoned-carts.index');
        $response->assertViewHas('carts');
        $response->assertViewHas('stats');
    }

    /** @test */
    public function admin_can_view_specific_abandoned_cart()
    {
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
    }

    /** @test */
    public function admin_can_delete_abandoned_cart()
    {
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
        $this->assertDatabaseMissing('abandoned_carts', ['id' => $cart->id]);
    }

    /** @test */
    public function admin_can_mark_cart_as_completed()
    {
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
        $this->assertEquals('completed', $cart->status);
    }

    /** @test */
    public function admin_can_send_reminder_email()
    {
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
        $this->assertEquals(1, $cart->recovery_email_sent_count);
    }

    /** @test */
    public function non_admin_cannot_access_abandoned_carts()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.abandoned-carts.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_filters_abandoned_carts_by_status()
    {
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
        $this->assertEquals(1, $carts->count());
        $this->assertEquals('abandoned', $carts->first()->status);
    }

    /** @test */
    public function it_filters_abandoned_carts_by_type()
    {
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
        $this->assertEquals(1, $carts->count());
        $this->assertEquals('signup', $carts->first()->type);
    }

    /** @test */
    public function it_searches_abandoned_carts_by_email()
    {
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
        $this->assertEquals(1, $carts->count());
        $this->assertEquals('john@example.com', $carts->first()->user->email);
    }
}
