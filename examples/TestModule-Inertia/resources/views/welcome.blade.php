<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @inertiaHead
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        .card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    
    <div class="card">
        <p>{{ $content }}</p>
        <p><strong>Note:</strong> This Blade view uses @inertiaHead directive without errors!</p>
    </div>

    <div style="margin-top: 1rem;">
        <a href="/testmodule" style="color: #2563eb; text-decoration: underline;">
            Back to Inertia Example
        </a>
    </div>
</body>
</html>
