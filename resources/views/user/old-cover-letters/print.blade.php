<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $coverLetter->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            line-height: 1.6;
            color: #333;
            padding: 40px;
            background: white;
        }

        .cover-letter-content {
            max-width: 8.5in;
            min-height: 11in;
            margin: 0 auto;
            padding: 40px;
            background: white;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 14px;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .cover-letter-content {
                max-width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0.5in;
                page-break-after: avoid;
            }

            * {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @page {
            size: letter;
            margin: 0.5in;
        }
    </style>
</head>
<body>
    <div class="cover-letter-content">{{ $coverLetter->content }}</div>
    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>
