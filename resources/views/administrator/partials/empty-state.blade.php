@php
    $icon = $icon ?? 'ri-inbox-line';
    $title = $title ?? 'Nothing to Display Yet';
    $message = $message ?? 'There are no records to display at the moment.';
    $class = $class ?? '';
@endphp

<div class="admin-empty-state {{ $class }}">
    <div class="admin-empty-state__icon" aria-hidden="true">
        <i class="{{ $icon }}"></i>
    </div>
    <h3>{{ $title }}</h3>
    <p>{{ $message }}</p>
</div>
