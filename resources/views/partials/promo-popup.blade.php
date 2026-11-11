@php
    $theme = match ($popup['theme'] ?? 'light') {
        'dark' => [
            'card' => 'bg-slate-900 text-white',
            'subtitle' => 'text-slate-300',
            'button' => 'bg-blue-500 text-white hover:bg-blue-400',
            'label' => 'text-blue-300',
        ],
        'brand' => [
            'card' => 'bg-gradient-to-br from-blue-700 via-blue-700 to-indigo-800 text-white',
            'subtitle' => 'text-blue-100',
            'button' => 'bg-white text-blue-700 hover:bg-blue-50',
            'label' => 'text-blue-200',
        ],
        default => [
            'card' => 'bg-white text-slate-900',
            'subtitle' => 'text-slate-600',
            'button' => 'bg-blue-600 text-white hover:bg-blue-700',
            'label' => 'text-blue-600',
        ],
    };
@endphp

<div id="promo-popup" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="promo-popup-title" aria-hidden="true">
    <div id="promo-popup-card" class="relative w-full max-w-md scale-95 overflow-hidden rounded-2xl opacity-0 shadow-2xl transition duration-300 {{ $theme['card'] }}">
        <button id="promo-popup-close" type="button" class="absolute right-3 top-3 z-10 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-slate-700 shadow-sm ring-1 ring-black/5 transition hover:bg-white hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Tutup promo">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        @if (! empty($popup['image']))
            <div class="aspect-[16/9] w-full bg-slate-100">
                <img src="{{ $popup['image'] }}" alt="{{ $popup['title'] }}" class="h-full w-full object-cover" loading="lazy">
            </div>
        @endif

        <div class="p-6 {{ empty($popup['image']) ? 'pt-14' : '' }}">
            <p class="text-[10.5px] font-bold uppercase tracking-[0.18em] {{ $theme['label'] }}">Promo Spesial</p>
            <h2 id="promo-popup-title" class="mt-2 text-[22px] font-black leading-tight tracking-tight">{{ $popup['title'] }}</h2>

            @if (! empty($popup['subtitle']))
                <p class="mt-2 text-[13.5px] leading-relaxed {{ $theme['subtitle'] }}">{{ $popup['subtitle'] }}</p>
            @endif

            @if (! empty($popup['button_text']) && ! empty($popup['button_link']))
                <a href="{{ $popup['button_link'] }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 text-[13px] font-bold transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $theme['button'] }}">
                    {{ $popup['button_text'] }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            @endif
        </div>
    </div>
</div>

<script>
    (function () {
        var popup = document.getElementById('promo-popup');
        if (!popup) {
            return;
        }

        var card = document.getElementById('promo-popup-card');
        var closeButton = document.getElementById('promo-popup-close');
        var lastFocused = null;

        function openPopup() {
            lastFocused = document.activeElement;
            popup.classList.remove('hidden');
            popup.classList.add('flex');
            popup.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');

            requestAnimationFrame(function () {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            });

            if (closeButton) {
                closeButton.focus();
            }
        }

        function closePopup() {
            popup.classList.add('hidden');
            popup.classList.remove('flex');
            popup.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');

            if (lastFocused && typeof lastFocused.focus === 'function') {
                lastFocused.focus();
            }
        }

        window.setTimeout(openPopup, 1200);

        closeButton.addEventListener('click', closePopup);

        popup.addEventListener('click', function (event) {
            if (event.target === popup) {
                closePopup();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (popup.getAttribute('aria-hidden') !== 'false') {
                return;
            }

            if (event.key === 'Escape') {
                closePopup();
                return;
            }

            if (event.key !== 'Tab') {
                return;
            }

            var focusable = card.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])');
            if (!focusable.length) {
                return;
            }

            var first = focusable[0];
            var last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });
    })();
</script>
