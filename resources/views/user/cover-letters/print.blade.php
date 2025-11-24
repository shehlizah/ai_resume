<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $coverLetter->title }} - Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
        }

        .header {
            margin-bottom: 30px;
        }

        .contact-info {
            margin-bottom: 20px;
        }

        .date {
            margin-bottom: 20px;
            color: #666;
        }

        .recipient {
            margin-bottom: 25px;
        }

        .salutation {
            margin-bottom: 20px;
            font-weight: bold;
        }

        .content {
            text-align: justify;
            margin-bottom: 30px;
        }

        .content p {
            margin-bottom: 15px;
        }

        .signature {
            margin-top: 40px;
        }

        .signature p {
            margin-bottom: 5px;
        }

        @media print {
            body {
                margin: 0;
                padding: 40px;
            }
            
            .no-print {
                display: none !important;
            }
        }

        @media screen {
            .print-actions {
                position: fixed;
                top: 20px;
                right: 20px;
                background: white;
                padding: 10px;
                border-radius: 5px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body>
    
    <!-- Print Actions (visible on screen only) -->
    <div class="print-actions no-print">
        <button onclick="window.print()" style="background: #6366f1; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-right: 10px;">
            🖨️ Print
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            ✖️ Close
        </button>
    </div>

    <!-- Cover Letter Content -->
    <div class="cover-letter">
        
        <!-- Your Information -->
        <div class="header">
            <div class="contact-info">
                <strong>{{ auth()->user()->name }}</strong><br>
                {{ auth()->user()->email }}<br>
                @if(auth()->user()->phone)
                    {{ auth()->user()->phone }}<br>
                @endif
            </div>
        </div>

        <!-- Date -->
        <div class="date">
            {{ now()->format('F d, Y') }}
        </div>

        <!-- Recipient Information -->
        <div class="recipient">
            <strong>{{ $coverLetter->recipient_name }}</strong><br>
            {{ $coverLetter->company_name }}<br>
            {{ $coverLetter->company_address }}
        </div>

        <!-- Salutation -->
        <div class="salutation">
            Dear {{ $coverLetter->recipient_name }},
        </div>

        <!-- Content -->
        <div class="content">
            {!! nl2br(e($coverLetter->content)) !!}
        </div>

        <!-- Signature -->
        <div class="signature">
            <p>Sincerely,</p>
            <br>
            <p><strong>{{ auth()->user()->name }}</strong></p>
        </div>

    </div>

    <script>
        // Auto-print dialog on page load (optional)
        // window.onload = function() { window.print(); }
    </script>

</body>
</html>