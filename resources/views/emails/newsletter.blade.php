<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->subject }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .preview-banner {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 12px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #92400e;
            font-weight: 500;
        }
        .article {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid #e5e7eb;
        }
        .article:last-child {
            border-bottom: none;
        }
        .article h2 {
            color: #111;
            margin-top: 0;
            margin-bottom: 16px;
            font-size: 24px;
        }
        .article-image {
            max-width: 100%;
            height: auto;
            margin: 16px 0;
            border-radius: 4px;
        }
        .article-text {
            font-size: 16px;
            color: #374151;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
        .footer a {
            color: #6b7280;
        }
    </style>
</head>
<body>
    @if($isPreview)
        <div class="preview-banner">
            Dies ist eine Vorschau des Newsletters
        </div>
    @endif

    @foreach($newsletter->articles as $article)
        <div class="article">
            <h2>{{ $article->title }}</h2>

            @foreach($article->images as $image)
                <img
                    src="{{ asset('storage/' . $image->file_path) }}"
                    alt="{{ $image->caption ?? $article->title }}"
                    class="article-image"
                >
                @if($image->caption)
                    <p style="font-size: 14px; color: #6b7280; margin-top: 4px;">{{ $image->caption }}</p>
                @endif
            @endforeach

            <div class="article-text">
                {!! nl2br(e($article->text)) !!}
            </div>
        </div>
    @endforeach

    <div class="footer">
        @if($unsubscribeToken)
            <p>
                <a href="{{ route('newsletter.unsubscribe', $unsubscribeToken) }}">
                    Newsletter abbestellen
                </a>
            </p>
        @endif
    </div>
</body>
</html>
