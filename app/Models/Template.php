<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'html_content',
        'css_content',
        'html_file_path',
        'css_file_path',
        'preview_image',
        'is_premium',
        'is_active',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the full HTML template with CSS embedded
     */
    public function getFullTemplate()
    {
        return "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$this->name}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; }
        {$this->css_content}
    </style>
</head>
<body>
    {$this->html_content}
</body>
</html>";
    }

    /**
     * Replace placeholders with actual user data
     * 
     * @param array $data Array of placeholder => value pairs
     * @return string Complete HTML with data filled in
     */
    public function renderWithData(array $data)
    {
        $html = $this->html_content;
        
        // Replace all placeholders
        foreach ($data as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value, $html);
        }
        
        return "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$this->name}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; }
        {$this->css_content}
    </style>
</head>
<body>
    {$html}
</body>
</html>";
    }

    /**
     * Get all active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get templates by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get premium templates only
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Get free templates only
     */
    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }
}