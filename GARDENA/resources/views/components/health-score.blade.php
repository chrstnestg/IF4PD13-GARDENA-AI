@props(['score' => 68, 'label' => 'Sedang'])

@php
    $color = $score >= 80 ? '#2d9a4f' : ($score >= 60 ? '#f59e0b' : '#ef4444');
    $circ  = 2 * M_PI * 30;
    $dash  = $circ * (1 - $score / 100);
@endphp

<div class="flex items-center gap-3">
    <div class="relative w-20 h-20 flex-shrink-0">
        <svg width="80" height="80" viewBox="0 0 80 80" style="transform:rotate(-90deg)">
            <circle cx="40" cy="40" r="30" fill="none" stroke="#e2e8f0" stroke-width="6"/>
            <circle cx="40" cy="40" r="30" fill="none"
                    stroke="{{ $color }}" stroke-width="6"
                    stroke-linecap="round"
                    stroke-dasharray="{{ $circ }}"
                    stroke-dashoffset="{{ $dash }}"/>
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="font-brand font-extrabold text-2xl text-gray-800 leading-none">{{ $score }}</span>
            <span class="text-[10px] text-gray-400 font-medium">/100</span>
        </div>
    </div>
    <div>
        <p class="text-xs text-gray-400 font-medium">Kesehatan Tanaman</p>
        <p class="font-brand font-bold text-lg" style="color:{{ $color }}">{{ $label }}</p>
    </div>
</div>