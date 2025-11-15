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
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .preview-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .template-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background: rgba(255,255,255,0.2);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge.premium {
            background: rgba(255, 193, 7, 0.3);
            border: 1px solid rgba(255, 193, 7, 0.5);
        }

        .badge.active {
            background: rgba(40, 167, 69, 0.3);
            border: 1px solid rgba(40, 167, 69, 0.5);
        }
        
        .preview-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
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

        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .preview-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .info-bar {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .info-item {
            display: flex;
            align-items: start;
            gap: 12px;
        }

        .info-icon {
            font-size: 24px;
            opacity: 0.7;
        }

        .info-content h4 {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .info-content p {
            font-size: 16px;
            color: #212529;
            font-weight: 500;
        }

        .preview-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            min-height: 600px;
        }

        .pdf-viewer {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .pdf-viewer iframe {
            width: 100%;
            height: 900px;
            border: none;
            border-radius: 8px;
        }

        .no-content {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .no-content-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .no-content h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #495057;
        }

        .no-content p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .preview-header-content {
                flex-direction: column;
                align-items: stretch;
            }

            .preview-actions {
                width: 100%;
                justify-content: stretch;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .pdf-viewer iframe {
                height: 600px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="preview-header">
        <div class="preview-header-content">
            <div>
                <h1 class="preview-title">
                    <span>📄</span>
                    <span>{{ $template->name }}</span>
                </h1>
                <div class="template-meta">
                    <span class="badge">
                        📁 {{ ucfirst($template->category) }}
                    </span>
                    @if($template->is_premium)
                        <span class="badge premium">
                            👑 Premium
                        </span>
                    @endif
                    @if($template->is_active)
                        <span class="badge active">
                            ✅ Active
                        </span>
                    @else
                        <span class="badge">
                            ⏸️ Inactive
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="preview-actions">
                @if($template->pdf_file && Storage::disk('public')->exists($template->pdf_file))
                    <a href="{{ asset('storage/' . $template->pdf_file) }}" 
                       class="btn btn-success" 
                       download>
                        ⬇️ Download PDF
                    </a>
                @endif
                <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-primary">
                    ✏️ Edit
                </a>
                <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">
                    ← Back
                </a>
            </div>
        </div>
    </div>
    
    <!-- Info Bar -->
    <div class="preview-container">
        <div class="info-bar">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">📝</div>
                    <div class="info-content">
                        <h4>Template Name</h4>
                        <p>{{ $template->name }}</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">📁</div>
                    <div class="info-content">
                        <h4>Category</h4>
                        <p>{{ ucfirst($template->category) }}</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">📅</div>
                    <div class="info-content">
                        <h4>Last Updated</h4>
                        <p>{{ $template->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>

                @if($template->pdf_file && Storage::disk('public')->exists($template->pdf_file))
                    <div class="info-item">
                        <div class="info-icon">💾</div>
                        <div class="info-content">
                            <h4>File Size</h4>
                            <p>{{ number_format(Storage::disk('public')->size($template->pdf_file) / 1024, 2) }} KB</p>
                        </div>
                    </div>
                @endif
            </div>

            @if($template->description)
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                    <div class="info-item">
                        <div class="info-icon">📋</div>
                        <div class="info-content">
                            <h4>Description</h4>
                            <p>{{ $template->description }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Preview Content -->
        <div class="preview-content">
            @if($template->template_type === 'pdf')
                @if($pdfUrl)
                    <div class="pdf-viewer">
                        <iframe 
                            src="{{ $pdfUrl }}" 
                            title="PDF Preview">
                        </iframe>
                    </div>
                @else
                    <div class="no-content">
                        <div class="no-content-icon">⚠️</div>
                        <h3>PDF File Not Found</h3>
                        <p>The PDF file could not be loaded. Please check if the file exists.</p>
                        @if($template->pdf_file)
                            <p style="font-size: 14px; color: #6c757d; margin-top: 10px;">
                                Expected path: <code>storage/{{ $template->pdf_file }}</code>
                            </p>
                        @endif
                        <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-primary">
                            ✏️ Upload PDF File
                        </a>
                    </div>
                @endif
            @else
                <div class="no-content">
                    <div class="no-content-icon">📄</div>
                    <h3>No Preview Available</h3>
                    <p>This template type is not supported for preview.</p>
                    <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-primary">
                        ✏️ Edit Template
                    </a>
                </div>
            @endif
        </div>
    </div>
    
    <script>
        // Log template info for debugging
        console.log('Template Preview Loaded');
        console.log('Template:', {
            name: '{{ $template->name }}',
            type: '{{ $template->template_type }}',
            category: '{{ $template->category }}',
            premium: {{ $template->is_premium ? 'true' : 'false' }},
            active: {{ $template->is_active ? 'true' : 'false' }}
        });
        
        // Handle iframe load error
        const iframe = document.querySelector('iframe');
        if (iframe) {
            iframe.onerror = function() {
                console.error('Failed to load PDF');
                document.querySelector('.pdf-viewer').innerHTML = 
                    '<div class="no-content"><div class="no-content-icon">⚠️</div><h3>Failed to Load PDF</h3><p>There was an error loading the PDF file.</p></div>';
            };
        }
    </script>
</body>
</html>