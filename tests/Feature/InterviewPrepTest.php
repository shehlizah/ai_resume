<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserResume;
use App\Services\JobMatchService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class InterviewPrepTest extends TestCase
{
    private User $user;
    private JobMatchService $jobMatchService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user directly without factory
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        
        $this->jobMatchService = app(JobMatchService::class);
    }

    /**
     * Test file upload resolution with private directory
     */
    public function test_uploaded_resume_file_path_resolution()
    {
        // Create a temporary test PDF file
        Storage::disk('local')->makeDirectory('private/uploads/temp/' . $this->user->id);
        
        $testContent = '%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>
endobj
4 0 obj
<< /Length 44 >>
stream
BT
/F1 12 Tf
100 700 Td
(Test Resume) Tj
ET
endstream
endobj
5 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
endobj
xref
0 6
0000000000 65535 f
0000000009 00000 n
0000000058 00000 n
0000000115 00000 n
0000000262 00000 n
0000000354 00000 n
trailer
<< /Size 6 /Root 1 0 R >>
startxref
433
%%EOF';

        $filePath = 'private/uploads/temp/' . $this->user->id . '/test_resume.pdf';
        Storage::disk('local')->put($filePath, $testContent);

        // Verify file exists
        $this->assertTrue(Storage::disk('local')->exists($filePath));
        
        // Test analyzeUploadedResume with relative path
        $result = $this->jobMatchService->analyzeUploadedResume('uploads/temp/' . $this->user->id . '/test_resume.pdf');
        
        // Should return array with raw_text (even if empty from test PDF)
        $this->assertIsArray($result);
        $this->assertArrayHasKey('raw_text', $result);
        
        // Cleanup
        Storage::disk('local')->delete($filePath);
    }

    /**
     * Test interview prep generation endpoint
     */
    public function test_generate_interview_prep_with_uploaded_file()
    {
        $this->actingAs($this->user);

        // Create a temporary test file
        Storage::disk('local')->makeDirectory('private/uploads/temp/' . $this->user->id);
        
        $filePath = 'private/uploads/temp/' . $this->user->id . '/test_resume.pdf';
        Storage::disk('local')->put($filePath, 'Test resume content with skills like PHP, Laravel, JavaScript');

        // Call generatePrep endpoint
        $response = $this->postJson(route('user.interview.generate-prep'), [
            'resume_id' => null,
            'uploaded_file' => 'uploads/temp/' . $this->user->id . '/test_resume.pdf',
            'job_title' => 'Software Engineer',
            'experience_level' => 'mid'
        ]);

        // Should succeed for free users (with fallback data)
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'questions' => [
                    '*' => ['question', 'sample_answer', 'tips']
                ]
            ]
        ]);

        // Cleanup
        Storage::disk('local')->delete($filePath);
    }

    /**
     * Test interview prep with saved resume
     */
    public function test_generate_interview_prep_with_saved_resume()
    {
        $this->actingAs($this->user);

        // Create a saved resume directly
        $resume = UserResume::create([
            'user_id' => $this->user->id,
            'data' => [
                'fullname' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '555-1234',
                'summary' => 'Experienced software engineer with 5 years of PHP and Laravel experience',
                'experience' => [
                    [
                        'position' => 'Senior Developer',
                        'company' => 'Tech Corp',
                        'description' => 'Developed web applications using Laravel'
                    ]
                ],
                'skills' => ['PHP', 'Laravel', 'JavaScript', 'React']
            ]
        ]);

        // Call generatePrep endpoint
        $response = $this->postJson(route('user.interview.generate-prep'), [
            'resume_id' => $resume->id,
            'uploaded_file' => null,
            'job_title' => 'Software Engineer',
            'experience_level' => 'senior'
        ]);

        // Should succeed
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'questions' => [
                    '*' => ['question', 'sample_answer', 'tips']
                ]
            ]
        ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data['questions']);
        $this->assertGreaterThan(0, count($data['questions']));
    }

    /**
     * Test interview prep with pro access (should include technical topics and salary tips)
     */
    public function test_generate_interview_prep_with_pro_access()
    {
        $this->user->has_lifetime_access = true;
        $this->user->save();

        $this->actingAs($this->user);

        // Create a saved resume directly
        $resume = UserResume::create([
            'user_id' => $this->user->id,
            'data' => [
                'fullname' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '555-5678',
                'summary' => 'Senior full-stack developer',
                'skills' => ['PHP', 'Laravel', 'JavaScript', 'React', 'Docker']
            ]
        ]);

        // Call generatePrep endpoint
        $response = $this->postJson(route('user.interview.generate-prep'), [
            'resume_id' => $resume->id,
            'uploaded_file' => null,
            'job_title' => 'Senior Software Engineer',
            'experience_level' => 'senior'
        ]);

        // Should succeed
        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Should have questions
        $this->assertArrayHasKey('questions', $data);
        $this->assertNotEmpty($data['questions']);
        
        // For PRO users, might also have technical_topics and salary_tips (if OpenAI provides them)
        // But these are optional depending on OpenAI response
    }

    /**
     * Test interview prep with invalid resume
     */
    public function test_generate_interview_prep_with_invalid_resume()
    {
        $this->actingAs($this->user);

        // Call with non-existent file
        $response = $this->postJson(route('user.interview.generate-prep'), [
            'resume_id' => null,
            'uploaded_file' => 'uploads/temp/' . $this->user->id . '/nonexistent.pdf',
            'job_title' => 'Software Engineer',
            'experience_level' => 'mid'
        ]);

        // Should fail with proper error message
        $response->assertStatus(400);
        $response->assertJsonStructure(['success', 'message']);
    }

    /**
     * Test interview prep with missing required fields
     */
    public function test_generate_interview_prep_validation()
    {
        $this->actingAs($this->user);

        // Missing job_title
        $response = $this->postJson(route('user.interview.generate-prep'), [
            'resume_id' => null,
            'uploaded_file' => null,
            'experience_level' => 'mid'
        ]);

        $response->assertStatus(422);

        // Missing experience_level
        $response = $this->postJson(route('user.interview.generate-prep'), [
            'resume_id' => null,
            'uploaded_file' => null,
            'job_title' => 'Software Engineer'
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test that unauthenticated users cannot access interview prep
     */
    public function test_interview_prep_requires_authentication()
    {
        $response = $this->postJson(route('user.interview.generate-prep'), [
            'resume_id' => null,
            'uploaded_file' => null,
            'job_title' => 'Software Engineer',
            'experience_level' => 'mid'
        ]);

        $response->assertStatus(401);
    }
}
