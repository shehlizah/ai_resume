# Interview Prep File Upload and Data Processing Fix

## Summary
Fixed the file path resolution bug in `JobMatchService::analyzeUploadedResume()` that was preventing uploaded resume files from being found and processed correctly.

## The Bug
When users uploaded resume files via the interview prep page:
1. Files were stored in `storage/app/private/uploads/temp/{user_id}/`
2. But `JobMatchService::analyzeUploadedResume()` was looking in `storage/app/uploads/temp/{user_id}/` (missing `/private`)
3. This caused file-not-found errors and prevented resume text extraction

## The Fix
Updated `JobMatchService::analyzeUploadedResume()` to:
1. First look in the private directory: `storage/app/private/{relative_path}`
2. Fall back to public directory if not found: `storage/app/{relative_path}`
3. This handles both temporary uploaded files and any other resume files

### Code Change
**File:** `app/Services/JobMatchService.php`

```php
public function analyzeUploadedResume(?string $relativePath): array
{
    if (empty($relativePath)) {
        return [];
    }

    // Try private directory first (where temp uploads are stored)
    $fullPath = storage_path('app/private/' . ltrim($relativePath, '/'));

    if (!file_exists($fullPath)) {
        // Fallback to public app directory
        $fullPath = storage_path('app/' . ltrim($relativePath, '/'));
    }

    if (!file_exists($fullPath)) {
        \Log::warning('Resume file not found at: ' . $fullPath);
        return [];
    }
    
    // ... rest of method
}
```

## Complete Interview Prep Workflow

### 1. File Upload Phase
- **Endpoint:** `POST /user/resumes/upload-temp`
- **Controller:** `UserResumeController::uploadTemporary()`
- **Storage Location:** `storage/app/private/uploads/temp/{user_id}/{filename}`
- **Response:** Returns relative path: `uploads/temp/{user_id}/{filename}`

### 2. Generation Request Phase
- **Endpoint:** `POST /user/interview/prep/generate`
- **Controller:** `InterviewPrepController::generatePrep()`
- **Payload:**
  ```json
  {
    "resume_id": null,
    "uploaded_file": "uploads/temp/{user_id}/{filename}",
    "job_title": "Software Engineer",
    "experience_level": "mid"
  }
  ```

### 3. Resume Analysis Phase
- **Method:** `JobMatchService::analyzeUploadedResume($relativePath)`
- **Process:**
  1. Resolves relative path to full file path (now tries private dir first)
  2. Extracts text from PDF/DOCX file
  3. Guesses skills, experience years, job title from content
  4. Returns structured resume profile with `raw_text`

### 4. Interview Prep Generation Phase
- **Method:** `OpenAIService::generateInterviewPrepFromResume()`
- **Process:**
  1. Builds prompt based on plan (free or pro)
  2. Sends to OpenAI with resume text, job title, experience level
  3. Receives structured JSON response
  4. **FREE Plan Response:**
     ```json
     {
       "questions": [
         {
           "question": "...",
           "sample_answer": "...",
           "tips": ["...", "..."]
         }
       ]
     }
     ```
  5. **PRO Plan Response:**
     ```json
     {
       "questions": [...],
       "technical_topics": "...",
       "salary_tips": "..."
     }
     ```

### 5. Response Parsing Phase
- **Method:** `OpenAIService::parseInterviewPrepJson($content)`
- **Process:**
  1. Extracts JSON from OpenAI response
  2. Validates structure
  3. Returns parsed array or fallback data
  4. **Fallback:** Simple default questions (always ensures some data)

### 6. Frontend Display Phase
- **View:** `resources/views/user/interview/prep.blade.php`
- **JavaScript Function:** `displayResults(data)`
- **Display:**
  1. Lists all questions with sample answers and tips
  2. For pro users: Shows technical topics and salary tips sections
  3. Uses `formatText()` to convert newlines to HTML breaks
  4. Uses `escapeHtml()` to prevent XSS

## File Structure Summary

```
Upload → File Storage
   ↓
Interview Prep Form
   ↓
generatePrep() Controller
   ↓
analyzeUploadedResume() [NOW FIXED]
   ↓
generateInterviewPrepFromResume()
   ↓
OpenAI API Call
   ↓
parseInterviewPrepJson()
   ↓
Frontend Response
   ↓
displayResults() JS Function
   ↓
User Sees Questions/Tips
```

## Testing

### Run Tests
```bash
php artisan test tests/Feature/InterviewPrepTest.php
```

### Test Cases Included
1. **File Path Resolution** - Verifies private directory lookup works
2. **Uploaded File Generation** - Tests full flow with uploaded file
3. **Saved Resume Generation** - Tests with existing UserResume
4. **Pro Access Features** - Verifies pro tier gets technical topics
5. **Invalid Resume Handling** - Tests error handling
6. **Validation** - Tests required field validation
7. **Authentication** - Tests that endpoint requires login

## Error Handling

### Common Issues and Solutions

#### Issue: "Could not extract text from resume"
- **Cause:** File not found due to path issue
- **Solution:** Now fixed with private directory lookup

#### Issue: File not found but upload succeeded
- **Cause:** Files uploaded to wrong directory
- **Solution:** Now correctly stored in `storage/app/private/`

#### Issue: Empty response from OpenAI
- **Cause:** API error or timeout
- **Solution:** Fallback to default questions provided

#### Issue: JSON parsing fails
- **Cause:** OpenAI returned non-JSON response
- **Solution:** Regex extraction and fallback data

## Performance Considerations

1. **Caching:** OpenAI responses are NOT cached (user-specific)
2. **File Size:** Limited to 10MB for uploads
3. **Text Extraction:** PDFs/DOCX processed synchronously
4. **Tokens:** PRO plan uses 4000 tokens, FREE uses 2000

## Security

1. **File Storage:** Private directory prevents direct web access
2. **User Verification:** Only authenticated users can upload
3. **Ownership:** Files stored by user ID
4. **Cleanup:** Temporary files should be cleaned up periodically
5. **XSS Prevention:** Frontend uses `escapeHtml()` for all user content

## Future Improvements

1. Implement async file processing for larger files
2. Add file cleanup cron job for old temporary uploads
3. Cache OpenAI responses for identical job titles/experience levels
4. Add support for more file formats (RTF, TXT)
5. Implement progress streaming for large file extractions
6. Add file preview before generation
7. Store generated results in database for history/reuse
