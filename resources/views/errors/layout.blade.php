@php
    $statusCode = trim($__env->yieldContent('code', '500'));
    $statusTitle = trim($__env->yieldContent('title', 'Something went wrong'));
    $statusMessage = trim($__env->yieldContent('message', 'The application could not complete this request.'));
    $statusHint = trim($__env->yieldContent('hint', 'Please try again in a moment or return to the homepage.'));
    $primaryTip = trim($__env->yieldContent('primary_tip', 'Check the address and try again.'));
    $secondaryTip = trim($__env->yieldContent('secondary_tip', 'Return to a known page if the problem continues.'));
    $statusFamily = substr($statusCode, 0, 1);
    $isServerError = $statusFamily === '5';
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $statusCode }} | {{ $statusTitle }}</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Merriweather:wght@700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --error-bg: #111827;
            --error-panel: rgba(255, 255, 255, 0.055);
            --error-panel-strong: rgba(255, 255, 255, 0.09);
            --error-border: rgba(255, 255, 255, 0.16);
            --error-text: #f8fafc;
            --error-muted: #cbd5e1;
            --error-soft: #94a3b8;
            --error-accent: {{ $isServerError ? '#f97316' : '#4f46e5' }};
            --error-accent-soft: {{ $isServerError ? 'rgba(249, 115, 22, 0.18)' : 'rgba(79, 70, 229, 0.22)' }};
            --error-accent-border: {{ $isServerError ? 'rgba(249, 115, 22, 0.42)' : 'rgba(129, 140, 248, 0.48)' }};
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--error-text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 18% 18%, var(--error-accent-soft), transparent 28%),
                radial-gradient(circle at 82% 12%, rgba(59, 130, 246, 0.14), transparent 26%),
                linear-gradient(135deg, #101827 0%, #111827 46%, #0f172a 100%);
        }

        a,
        button {
            font: inherit;
        }

        .error-page {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: clamp(24px, 5vw, 56px);
        }

        .error-shell {
            width: min(100%, 780px);
            text-align: center;
        }

        .error-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: clamp(32px, 6vw, 58px);
        }

        .error-logo img {
            display: block;
            width: min(220px, 58vw);
            height: auto;
        }

        .error-status-line {
            display: inline-grid;
            grid-template-columns: auto 1px auto;
            gap: clamp(14px, 3vw, 24px);
            align-items: center;
            margin-bottom: 20px;
            color: var(--error-text);
            letter-spacing: 0;
        }

        .error-code {
            font-family: Merriweather, Georgia, serif;
            font-size: clamp(3.2rem, 10vw, 7rem);
            font-weight: 900;
            line-height: 0.95;
        }

        .error-divider {
            width: 1px;
            height: clamp(44px, 8vw, 78px);
            background: var(--error-border);
        }

        .error-title {
            max-width: 12ch;
            margin: 0;
            font-family: Merriweather, Georgia, serif;
            font-size: clamp(1.35rem, 3.5vw, 2.25rem);
            font-weight: 700;
            line-height: 1.15;
            text-align: left;
        }

        .error-message {
            max-width: 58ch;
            margin: 0 auto;
            color: var(--error-muted);
            font-size: clamp(1rem, 1.8vw, 1.12rem);
            line-height: 1.8;
        }

        .error-hint {
            max-width: 54ch;
            margin: 14px auto 0;
            color: var(--error-soft);
            font-size: 0.95rem;
            line-height: 1.65;
        }

        .error-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 30px;
        }

        .error-button {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--error-border);
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.94rem;
            font-weight: 700;
            line-height: 1;
            padding: 0 18px;
            text-decoration: none;
            transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
        }

        .error-button--primary {
            border-color: var(--error-accent-border);
            background: var(--error-accent);
            color: #ffffff;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.24);
        }

        .error-button--secondary {
            background: rgba(255, 255, 255, 0.04);
            color: var(--error-text);
        }

        .error-button:hover,
        .error-button:focus-visible {
            transform: translateY(-2px);
            outline: none;
        }

        .error-button--primary:hover,
        .error-button--primary:focus-visible {
            filter: brightness(1.04);
        }

        .error-button--secondary:hover,
        .error-button--secondary:focus-visible {
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.09);
        }

        .error-clues {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin: clamp(34px, 6vw, 52px) auto 0;
            text-align: left;
        }

        .error-clue {
            min-width: 0;
            border: 1px solid var(--error-border);
            border-radius: 8px;
            background: var(--error-panel);
            padding: 18px;
        }

        .error-clue span {
            display: block;
            margin-bottom: 7px;
            color: var(--error-text);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .error-clue p {
            margin: 0;
            color: var(--error-muted);
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .error-footnote {
            margin: 30px 0 0;
            color: rgba(203, 213, 225, 0.72);
            font-size: 0.82rem;
            line-height: 1.55;
        }

        @media (max-width: 640px) {
            .error-page {
                align-items: flex-start;
                padding: 34px 18px;
            }

            .error-status-line {
                grid-template-columns: 1fr;
                gap: 10px;
                text-align: center;
            }

            .error-divider {
                width: min(180px, 54vw);
                height: 1px;
                margin: 0 auto;
            }

            .error-title {
                max-width: none;
                text-align: center;
            }

            .error-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .error-button {
                width: 100%;
            }

            .error-clues {
                grid-template-columns: 1fr;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .error-button {
                transition: none;
            }

            .error-button:hover,
            .error-button:focus-visible {
                transform: none;
            }
        }
    </style>
</head>

<body>
    <main class="error-page" aria-labelledby="error-title">
        <section class="error-shell">
            <a class="error-logo" href="{{ route('landpage') }}" aria-label="Tulong Kabataan homepage">
                <img src="{{ asset('img/log1.png') }}" alt="Tulong Kabataan">
            </a>

            <div class="error-status-line" aria-label="HTTP status {{ $statusCode }}">
                <strong class="error-code">{{ $statusCode }}</strong>
                <span class="error-divider" aria-hidden="true"></span>
                <h1 class="error-title" id="error-title">{{ $statusTitle }}</h1>
            </div>

            <p class="error-message">{{ $statusMessage }}</p>
            <p class="error-hint">{{ $statusHint }}</p>

            <div class="error-actions" aria-label="Error page actions">
                <a class="error-button error-button--primary" href="{{ route('landpage') }}">Go Home</a>
                <button class="error-button error-button--secondary" type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.assign('{{ route('landpage') }}')">
                    Go Back
                </button>
            </div>

            <div class="error-clues" aria-label="What you can do next">
                <div class="error-clue">
                    <span>What happened</span>
                    <p>{{ $primaryTip }}</p>
                </div>
                <div class="error-clue">
                    <span>Next step</span>
                    <p>{{ $secondaryTip }}</p>
                </div>
            </div>

            <p class="error-footnote">HTTP {{ $statusCode }} is a status code returned by the server for this request.</p>
        </section>
    </main>
</body>

</html>
