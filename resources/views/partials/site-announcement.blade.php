@php
    $s = $siteSettings ?? [];
    $show = !empty($s['site.announcement.enabled']);
    $variant = $s['site.announcement.variant'] ?? 'info';
    $title = $s['site.announcement.title'] ?? '';
    $message = $s['site.announcement.message'] ?? '';
    $maintEnabled = !empty($s['site.maintenance.enabled']);
    $maintMessage = $s['site.maintenance.message'] ?? '';

    $styles = [
        'info'    => ['bg' => '#eff6ff', 'fg' => '#1e3a8a', 'bd' => '#bfdbfe', 'icon' => 'ri-information-line'],
        'success' => ['bg' => '#ecfdf5', 'fg' => '#065f46', 'bd' => '#a7f3d0', 'icon' => 'ri-checkbox-circle-line'],
        'warning' => ['bg' => '#fffbeb', 'fg' => '#92400e', 'bd' => '#fde68a', 'icon' => 'ri-alert-line'],
        'danger'  => ['bg' => '#fef2f2', 'fg' => '#991b1b', 'bd' => '#fecaca', 'icon' => 'ri-error-warning-line'],
    ];
    $style = $styles[$variant] ?? $styles['info'];
@endphp

@if ($maintEnabled)
    <div role="status"
        style="background:#fef2f2;color:#991b1b;border-bottom:1px solid #fecaca;padding:10px 16px;
               display:flex;gap:10px;align-items:center;justify-content:center;font-weight:600;">
        <i class="ri-tools-line" aria-hidden="true"></i>
        <span>{{ $maintMessage ?: 'Scheduled maintenance in progress.' }}</span>
    </div>
@endif

@if ($show && ($title || $message))
    <div role="status"
        style="background: {{ $style['bg'] }};
               color: {{ $style['fg'] }};
               border-bottom: 1px solid {{ $style['bd'] }};
               padding: 12px 16px;">
        <div style="max-width:1200px;margin:0 auto;display:flex;gap:12px;align-items:flex-start;">
            <i class="{{ $style['icon'] }}" style="font-size:20px;line-height:1.4;" aria-hidden="true"></i>
            <div style="flex:1;">
                @if ($title)
                    <div style="font-weight:700;line-height:1.3;">{{ $title }}</div>
                @endif
                @if ($message)
                    <div style="font-size:14px;line-height:1.5;margin-top:{{ $title ? '2px' : '0' }};">{{ $message }}</div>
                @endif
            </div>
        </div>
    </div>
@endif
