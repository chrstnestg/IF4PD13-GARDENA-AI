<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between gap-4">

        <p class="text-xs text-gray-400">
            © {{ date('Y') }} By <strong class="text-gray-600">GARDENA-AI</strong>
        </p>

        <div class="flex items-center gap-1">
            @foreach([
                ['mailto:hello@gardena.ai',           'bi-envelope-fill',  'Email'],
                ['https://instagram.com/gardenaai',   'bi-instagram',      'Instagram'],
                ['https://twitter.com/gardenaai',     'bi-twitter-x',      'X'],
                ['https://facebook.com/gardenaai',    'bi-facebook',       'Facebook'],
                ['https://wa.me/62xxxxxxxxxx',        'bi-whatsapp',       'WhatsApp'],
            ] as [$href, $icon, $title])
                <a href="{{ $href }}"
                   target="{{ str_starts_with($href, 'mailto') ? '_self' : '_blank' }}"
                   title="{{ $title }}"
                   class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-green-600 hover:bg-green-50 transition-all duration-150 text-[0.95rem]">
                    <i class="bi {{ $icon }}"></i>
                </a>
            @endforeach
        </div>
    </div>
</footer>