<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Tulong Kabataan')</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="@yield('body_class', 'info-page')">
    @include('administrator.partials.loading-screen')
    @include('partials.main-header')

    <main id="main-content" class="info-page-main">
        <section class="info-hero" aria-labelledby="info-page-title">
            <div class="info-hero__inner">
                @hasSection('hero_kicker')
                    <p class="info-hero__kicker">@yield('hero_kicker')</p>
                @endif

                <h1 id="info-page-title">@yield('hero_title')</h1>

                @hasSection('hero_subtitle')
                    <p class="info-hero__subtitle">@yield('hero_subtitle')</p>
                @endif
            </div>

            @include('partials.wave-divider', ['surface' => 'white'])
        </section>

        <section class="info-content-shell" aria-label="@yield('hero_title') content">
            <div class="info-page-container">
                <aside class="info-sidebar" aria-label="@yield('hero_title') section navigation">
                    <div class="info-sidebar__inner">
                        <span class="info-sidebar__label">On this page</span>
                        <nav class="info-sidebar__nav">
                            @yield('section_nav')
                        </nav>
                    </div>
                </aside>

                <article class="info-article">
                    @yield('content')
                </article>
            </div>
        </section>
    </main>

    @include('partials.main-footer')
</body>

</html>
