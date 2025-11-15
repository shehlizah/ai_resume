<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserResume extends Model
{
    use HasFactory;
    
    protected $table = 'user_resumes';
    
    protected $fillable = [
        'user_id',
        'template_id',
        'data',
        'generated_pdf_path',
        'status',
    ];
    
    protected $casts = [
        'data' => 'array',
    ];
    
    /**
     * Get the user who owns this resume
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the template used for this resume
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }
    
    /**
     * Get the full storage path
     */
    public function getFullPathAttribute()
    {
        return storage_path('app/public/' . $this->generated_pdf_path);
    }
    
    /**
     * Get the public URL
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->generated_pdf_path);
    }
}