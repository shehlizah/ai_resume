<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'user_id',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'resume_url',
        'cover_letter',
    ];

    public function job()
    {
        return $this->belongsTo(\App\Models\PostedJob::class, 'job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
