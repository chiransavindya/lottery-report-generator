@php
    /*
    |──────────────────────────────────────────────────────────────────────────
    | LAGNAWASANA — lw.blade.php
    |──────────────────────────────────────────────────────────────────────────
    | HTML SECTION MAP
    | [A] ROOT WRAPPER     → .lottery-banner-container .lw-banner .lang-{$lang}
    | [B] BACKGROUND IMAGE → per-language image (en/si/ta each have their own)
    | [C] DATA OVERLAY     → .lw-data-overlay
    |     ├─ [C1] ROW 1   → Draw Number box + Colour box  (.lw-info-label / .lw-info-val)
    |     ├─ [C2] ROW 2   → Winning Numbers label         (.lw-lbl)
    |     ├─ [C3] ROW 3   → Winning balls + Zodiac box    (.lw-ball / .lw-zodiac-name)
    |     └─ [C4] ROW 4   → Jackpot pill                  (.lw-jackpot-pill)
    |
    | CSS OVERRIDES PER LANGUAGE  (report-lagnawasana.css)
    |   English → .lw-banner.lang-en ...
    |   Sinhala → .lw-banner.lang-si ...
    |   Tamil   → .lw-banner.lang-ta ...
    |──────────────────────────────────────────────────────────────────────────
    */
    $lang = $language ?? 'en';

    // Per-language background images
    $lwImageMap = [
        'en' => 'lagna_english.png',
        'si' => 'lagna_sinhala.png',
        'ta' => 'lagna_tamil.png',
    ];
    $lwImage = $lwImageMap[$lang] ?? 'lagna_english.png';

    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };
@endphp

{{-- [A] ROOT WRAPPER --}}
<div class="lottery-banner-container lw-banner lang-{{ $lang }}">

    {{-- [B] BACKGROUND IMAGE — unique per language (controlled by $lwImageMap above) --}}
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/' . $lwImage) }}"
         alt="Lagnawasana Banner"
         class="lottery-banner-image">

    {{-- [C] DATA OVERLAY
         English → .lw-banner.lang-en .lw-data-overlay { font-size: 1.1cqw }
         Sinhala → .lw-banner.lang-si .lw-data-overlay { font-size: 1.85cqw }
         Tamil   → .lw-banner.lang-ta .lw-data-overlay { font-size: 1.5cqw } --}}
    <div class="lw-data-overlay">

        {{-- [C1] ROW 1 — Draw Number + Colour boxes
             English → .lw-banner.lang-en .lw-info-label { font-size: 1.3em; padding: 0.5em 1em }
                       .lw-banner.lang-en .lw-info-val   { font-size: 2.4em }
             Sinhala → .lw-banner.lang-si .lw-info-label { font-size: 1.1em; padding: 0.4em 0.7em }
                       .lw-banner.lang-si .lw-info-val   { font-size: 1.9em }
             Tamil   → .lw-banner.lang-ta .lw-info-label { font-size: 1.0em; padding: 0.3em 0.6em }
                       .lw-banner.lang-ta .lw-info-val   { font-size: 1.8em } --}}
        <div class="lw-info-row">

            <div class="lw-info-box">
                <span class="lw-info-label">{{ $L('Draw Number') }}</span>
                <span class="lw-info-val">{{ $draw->draw_number }}</span>
            </div>

            @if($draw->color)
                <div class="lw-info-box">
                    <span class="lw-info-label">{{ $L('Colour') }}</span>
                    <span class="lw-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                </div>
            @endif

        </div>
        {{-- END [C1] --}}

        {{-- [C2] ROW 2 — Winning Numbers label
             English → .lw-banner.lang-en .lw-lbl { font-size: 1.1em }
             Sinhala → .lw-banner.lang-si .lw-lbl { font-size: 0.95em }
             Tamil   → .lw-banner.lang-ta .lw-lbl { font-size: 0.85em } --}}
        <div class="lw-labels-row">
            <span class="lw-lbl lw-col-win">{{ $L('Winning Numbers') }}</span>
        </div>
        {{-- END [C2] --}}

        {{-- [C3] ROW 3 — Winning balls + Zodiac box
             Balls:
               English → .lw-banner.lang-en .lw-ball { font-size: 3.2em }
               Sinhala → .lw-banner.lang-si .lw-ball { font-size: 2.8em }
               Tamil   → .lw-banner.lang-ta .lw-ball { font-size: 2.5em }
             Zodiac name:
               English → .lw-banner.lang-en .lw-zodiac-name { font-size: 1.4em }
               Sinhala → .lw-banner.lang-si .lw-zodiac-name { font-size: 1.1em }
               Tamil   → .lw-banner.lang-ta .lw-zodiac-name { font-size: 1.0em } --}}
        <div class="lw-results-row">

            {{-- Winning Numbers --}}
            <div class="lw-ball-group">
                @foreach($draw->numbers as $number)
                    <div class="lw-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                @endforeach
            </div>

            {{-- Zodiac box (shown only when zodiac_sign data exists) --}}
            @if($draw->zodiac_sign)
                <div class="lw-zodiac-box">
                    @php
                        $zodiacSlug = strtolower(str_replace(' ', '_', $draw->zodiac_sign));
                        $zodiacImagePath = public_path("images/zodiac_images/{$zodiacSlug}.png");
                    @endphp
                    @if(file_exists($zodiacImagePath))
                        <img src="{{ asset("images/zodiac_images/{$zodiacSlug}.png") }}"
                             alt="{{ ucfirst($draw->zodiac_sign) }}"
                             class="lw-zodiac-icon">
                    @endif
                    <div class="lw-zodiac-name">
                        {{ $L(ucfirst(strtolower(trim($draw->zodiac_sign)))) }}
                    </div>
                </div>
            @endif

        </div>
        {{-- END [C3] --}}

        {{-- [C4] ROW 4 — Next Jackpot Pill
             English → .lw-banner.lang-en .lw-jackpot-pill { font-size: 2.0em; padding: 0.3em 1em }
             Sinhala → .lw-banner.lang-si .lw-jackpot-pill { font-size: 1.6em; padding: 0.3em 0.8em }
             Tamil   → .lw-banner.lang-ta .lw-jackpot-pill { font-size: 1.4em; padding: 0.3em 0.7em } --}}
        @if($draw->next_jackpot)
            <div class="lw-jackpot-row">
                <div class="lw-jackpot-pill">
                    {{ $L('Next Jackpot') }} : {{ $lang === 'ta' ? 'ரூ.' : 'Rs.' }} {{ number_format($draw->next_jackpot, 2) }}
                </div>
            </div>
        @endif
        {{-- END [C4] --}}

    </div>
    {{-- END [C] --}}

</div>
{{-- END [A] --}}