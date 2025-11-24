<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoverLetter extends Model
{
    use HasFactory;

    protected $table = 'cover_letters';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_phone',
        'user_address',
        'template_id',
        'title',
        'recipient_name',
        'company_name',
        'company_address',
        'content',
        'pdf_url',
        'is_deleted',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'user_id' => 'integer',
        'template_id' => 'integer',
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user that owns the cover letter.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template used for this cover letter.
     */
    public function template()
    {
        return $this->belongsTo(CoverLetterTemplate::class, 'template_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to get non-deleted cover letters.
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    /**
     * Scope to get deleted cover letters.
     */
    public function scopeDeleted($query)
    {
        return $query->where('is_deleted', true);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by template.
     */
    public function scopeByTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    /**
     * Scope to search by title or company name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('recipient_name', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to order by latest.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get the formatted created date.
     */
    public function getCreatedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('M d, Y') : null;
    }

    /**
     * Get the formatted updated date.
     */
    public function getUpdatedDateAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('M d, Y') : null;
    }

    /**
     * Check if cover letter has a PDF.
     */
    public function getHasPdfAttribute()
    {
        return !empty($this->pdf_url);
    }

    /**
     * Get full PDF URL.
     */
    public function getPdfFullUrlAttribute()
    {
        if (!$this->pdf_url) {
            return null;
        }

        // If it's already a full URL, return it
        if (filter_var($this->pdf_url, FILTER_VALIDATE_URL)) {
            return $this->pdf_url;
        }

        // Otherwise, prepend storage URL
        return asset('storage/' . $this->pdf_url);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Soft delete the cover letter.
     */
    public function softDelete()
    {
        $this->update(['is_deleted' => true]);
    }

    /**
     * Restore a soft-deleted cover letter.
     */
    public function restore()
    {
        $this->update(['is_deleted' => false]);
    }

    /**
     * Check if cover letter is deleted.
     */
    public function isDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * Get a summary of the content (first 100 characters).
     */
    public function getContentSummary($length = 100)
    {
        return strlen($this->content) > $length 
            ? substr(strip_tags($this->content), 0, $length) . '...' 
            : strip_tags($this->content);
    }

    /**
     * Boot method for model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-populate user data from authenticated user if not provided
        static::creating(function ($coverLetter) {
            if (auth()->check()) {
                $user = auth()->user();
                
                if (empty($coverLetter->user_id)) {
                    $coverLetter->user_id = $user->id;
                }
                
                if (empty($coverLetter->user_name)) {
                    $coverLetter->user_name = $user->name;
                }
                
                if (empty($coverLetter->user_email)) {
                    $coverLetter->user_email = $user->email;
                }
                
                // Populate phone and address if they exist in user model
                if (empty($coverLetter->user_phone) && isset($user->phone)) {
                    $coverLetter->user_phone = $user->phone;
                }
                
                if (empty($coverLetter->user_address) && isset($user->address)) {
                    $coverLetter->user_address = $user->address;
                }
            }
        });
    }
}