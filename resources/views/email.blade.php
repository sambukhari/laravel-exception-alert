<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Exception Alert</title>
</head>
<body>
  <h2>🚨 Exception Alert — {{ $app }} ({{ $env }})</h2>
  <p><strong>URL:</strong> {{ $url }}</p>
  <p><strong>Type:</strong> {{ get_class($exception) }}</p>
  <p><strong>Message:</strong> {{ $exception->getMessage() }}</p>
  <p><strong>File:</strong> {{ $exception->getFile() }} : {{ $exception->getLine() }}</p>
  <pre style="background:#f4f4f4;padding:10px;border-radius:4px;">{{ $exception->getTraceAsString() }}</pre>
</body>
</html>
