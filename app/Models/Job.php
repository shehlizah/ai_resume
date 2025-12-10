<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'title',
        'company',
        'location',
        'type',
        'description',
        'salary',
        'tags',
        'posted_at',
        'source',
        'url',
        'is_featured',
        'is_active'
    ];

    protected $casts = [
        'tags' => 'array',
        'posted_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function getTagsArrayAttribute()
    {
        return is_string($this->tags) ? json_decode($this->tags, true) : $this->tags;
    }

    public function getTimeAgoAttribute()
    {
        return $this->posted_at->diffForHumans();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('posted_at', 'desc');
    }
}
