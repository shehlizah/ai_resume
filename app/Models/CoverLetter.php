<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoverLetter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
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
     *
     * @var array
     */
    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /**
     * Get the user that owns the cover letter.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get non-deleted cover letters.
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }
}
