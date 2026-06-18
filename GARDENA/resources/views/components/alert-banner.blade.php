{{--
    Component: alert-banner
    Props:
        $type    - 'critical' | 'warning' | 'success' | 'info'
        $message - teks pesan
        $dismissible - bool, default false
--}}
@props([
    'type'        => 'critical',
    'message'     => '',
    'dismissible' => false,
])

@php
    $iconMap = [
        'critical' => 'bi-exclamation-triangle-fill',
        'warning'  => 'bi-exclamation-circle-fill',
        'success'  => 'bi-check-circle-fill',
        'info'     => 'bi-info-circle-fill',
    ];
    $icon = $iconMap[$type] ?? 'bi-info-circle-fill';
@endphp

<div class="gardena-alert alert-{{ $type }} {{ $dismissible ? 'd-flex justify-content-between' : '' }}"
     @if($dismissible) id="alert-banner" @endif>
    <div class="d-flex align-items-center gap-2">
        <i class="bi {{ $icon }}"></i>
        {{ $message }}
    </div>

    @if($dismissible)
        <button type="button"
                onclick="document.getElementById('alert-banner').remove()"
                style="background:none;border:none;color:inherit;cursor:pointer;padding:0 4px;">
            <i class="bi bi-x-lg"></i>
        </button>
    @endif
</div>
