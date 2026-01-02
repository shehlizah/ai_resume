<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $greeting ?? 'Hello' }}</title>
</head>
<body>
    <h2>{{ $greeting ?? 'Hello' }}</h2>
    @if (!empty($lines))
        @foreach ($lines as $line)
            <p>{{ $line }}</p>
        @endforeach
    @endif
    @if (!empty($actionText) && !empty($actionUrl))
        <p><a href="{{ $actionUrl }}" style="background:#007bff;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;">{{ $actionText }}</a></p>
    @endif
    @if (!empty($outroLines))
        @foreach ($outroLines as $line)
            <p>{{ $line }}</p>
        @endforeach
    @endif
    <p>{{ $salutation ?? '' }}</p>
</body>
</html>
