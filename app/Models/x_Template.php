<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'preview_image',
        'pdf_file',
        'template_type',
        'is_premium',
        'is_active',
        'sort_order',
        'version',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];
}
