<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Exception Alert</title>
</head>
<body>
    <h2>🚨 Exception Alert from {{ $app }}</h2>
    <p><strong>URL:</strong> {{ $url }}</p>
    <p><strong>Type:</strong> {{ get_class($exception) }}</p>
    <p><strong>Message:</strong> {{ $exception->getMessage() }}</p>
    <p><strong>File:</strong> {{ $exception->getFile() }} (Line {{ $exception->getLine() }})</p>
    <pre>{{ $exception->getTraceAsString() }}</pre>
</body>
</html>
