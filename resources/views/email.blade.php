<h2>🚨 Exception Alert from {{ config('app.name') }}</h2>
<p><strong>Message:</strong> {{ $messageText }}</p>
<p><strong>File:</strong> {{ $file }} (line {{ $line }})</p>
<p><strong>URL:</strong> {{ $url }}</p>
<pre style="background:#f7f7f7;padding:10px;border-radius:5px;">{{ $trace }}</pre>
