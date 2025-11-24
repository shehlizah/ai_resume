<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CoverLetterTemplate;

class CoverLetterTemplateSeeder extends Seeder
{
    public function run(): void
    {
        CoverLetterTemplate::truncate();

        $templates = [
            [
                'name' => 'Professional',
                'description' => '3-paragraph formal',
                'content' => "[Your Full Name]
[Your Email] · [Your Phone]
[Date]

[Recipient Name]
[Company Name]
[Company Address]

Dear [Recipient Name],

I am writing to express my interest in the [Position Title] role at [Company Name]. With [X years] of experience in [relevant field/skill], I have successfully delivered [brief accomplishment or responsibility], and I am confident my background aligns well with the needs of your team.

At my current/previous role, I [describe 1–2 specific achievements—metrics if possible]. These experiences taught me [skill or value], which I will bring to [Company Name] to help [specific company goal or problem you can solve].

Thank you for considering my application. I welcome the opportunity to discuss how my background and enthusiasm can contribute to your team. I can be reached at [Your Phone] or [Your Email].

Sincerely,
[Your Full Name]",
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Concise',
                'description' => 'Short and impactful',
                'content' => "[Date]

Dear [Recipient Name],

I'm excited to apply for the [Position Title] at [Company Name]. I bring [X years] of experience in [skill area] and a track record of [1-line achievement]. I'm confident I can help [Company Name] achieve [specific outcome].

Thank you for your time – I look forward to speaking with you.

Best regards,
[Your Full Name] – [Your Phone] · [Your Email]",
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Conversational',
                'description' => 'Engaging and friendly',
                'content' => "[Date]

Hi [Recipient Name],

I've been following [Company Name]'s work on [product/initiative], and I'm impressed by [specific point]. As a [your role] who loves building [what you build], I'd be thrilled to join as [Position Title].

In my recent work, I [short story-style achievement that shows impact]. I enjoy collaborating with teams to turn ideas into measurable results, and I'd love to bring that energy to [Company Name].

If you're open to a quick call, I'd be happy to share more about how I can contribute. Thanks for considering my application.

Warmly,
[Your Full Name]
[Your LinkedIn or portfolio link] · [Your Email]",
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($templates as $template) {
            CoverLetterTemplate::create($template);
        }
    }
}