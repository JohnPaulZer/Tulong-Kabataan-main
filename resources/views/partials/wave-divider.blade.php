@php
    $surface = $surface ?? 'grid';

    $surfaceStyles = [
        'grid' => '--tk-wave-bg: var(--tk-diagonal-grid-bg); --tk-wave-bg-image: var(--tk-diagonal-grid-pattern); --tk-wave-bg-size: 40px 40px; --tk-wave-bg-attachment: fixed;',
        'slate' => '--tk-wave-bg: #f8fafc; --tk-wave-bg-image: none; --tk-wave-bg-size: auto; --tk-wave-bg-attachment: scroll;',
        'white' => '--tk-wave-bg: #ffffff; --tk-wave-bg-image: none; --tk-wave-bg-size: auto; --tk-wave-bg-attachment: scroll;',
    ];

    $surfaceStyle = $surfaceStyles[$surface] ?? $surfaceStyles['grid'];
@endphp

<div
    aria-hidden="true"
    class="pointer-events-none absolute bottom-[-2px] left-0 z-30 h-[68px] w-full bg-[var(--tk-wave-bg)] max-[850px]:h-[35px]"
    style="{{ $surfaceStyle }} --tk-wave-mask: url(&quot;data:image/svg+xml,%3Csvg viewBox='0 0 1200 116' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 85L50 80C100 75 200 64 300 43C400 22 500 -9 600 2C700 12 800 64 900 85C1000 106 1100 96 1150 90L1200 85V116H1150C1100 116 1000 116 900 116C800 116 700 116 600 116C500 116 400 116 300 116C200 116 100 116 50 116H0V85Z' fill='black'/%3E%3C/svg%3E&quot;); background-image: var(--tk-wave-bg-image); background-size: var(--tk-wave-bg-size); background-attachment: var(--tk-wave-bg-attachment); -webkit-mask-image: var(--tk-wave-mask); mask-image: var(--tk-wave-mask); -webkit-mask-size: auto; mask-size: auto; -webkit-mask-repeat: repeat-x; mask-repeat: repeat-x; -webkit-mask-position: 20vw bottom; mask-position: 20vw bottom;"
></div>
