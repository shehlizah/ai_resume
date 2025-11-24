<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoverLetterTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'preview_image',
        'html_content',
        'structure',
        'category',
        'is_active',
        'is_premium',
        'usage_count',
        'price',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
        'structure' => 'array',
        'price' => 'decimal:2',
    ];

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for free templates
     */
    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    /**
     * Scope for premium templates
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Get category badge color
     */
    public function getCategoryColor()
    {
        return match($this->category) {
            'professional' => 'primary',
            'creative' => 'warning',
            'academic' => 'info',
            'technical' => 'success',
            'executive' => 'danger',
            default => 'secondary'
        };
    }
        /**
     * Get cover letters using this template
     */
    public function coverLetters()
    {
        return $this->hasMany(CoverLetter::class, 'template_id');
    }

}