<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $template->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
        }
        
        .preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .preview-header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .preview-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .preview-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .btn-primary {
            background: white;
            color: #667eea;
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .preview-content {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            min-height: calc(100vh - 80px);
        }
        
        /* Template Styles - Injected Below */
        {!! $css !!}
    </style>
</head>
<body>
    <div class="preview-header">
        <div class="preview-header-content">
            <h1 class="preview-title">
                <span>📄</span>
                <span>{{ $template->name }}</span>
            </h1>
            <div class="preview-actions">
                <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-primary">
                    ✏️ Edit Template
                </a>
                <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">
                    ← Back
                </a>
            </div>
        </div>
    </div>
    
    <div class="preview-content">
        {!! $html !!}
    </div>
    
    <script>
        console.log('Template Preview Loaded');
        console.log('Template Name: {{ $template->name }}');
        console.log('HTML Length: {{ strlen($html) }} characters');
        console.log('CSS Length: {{ strlen($css) }} characters');
    </script>
</body>
</html>