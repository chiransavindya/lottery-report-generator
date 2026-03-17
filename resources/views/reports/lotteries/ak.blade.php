@php
    /*
    |──────────────────────────────────────────────────────────────────────────
    | ADA KOTIPATHI — ak.blade.php
    |──────────────────────────────────────────────────────────────────────────
    | HTML SECTION MAP
    | [A] ROOT WRAPPER     → .lottery-banner-container .ak-banner .lang-{$lang}
    | [B] BACKGROUND IMAGE → adakotipathi_all.png  (same for all 3 languages)
    | [C] DATA OVERLAY     → .ak-data-overlay
    |     ├─ [C1] ROW 1   → Draw Number box + Colour box  (.ak-info-label / .ak-info-val)
    |     ├─ [C2] ROW 2   → Column labels                 (.ak-lbl)
    |     ├─ [C3] ROW 3   → Ball groups                   (.ak-ball)
    |     └─ [C4] ROW 4   → Jackpot pill                  (.ak-jackpot-pill)
    |
    | CSS OVERRIDES PER LANGUAGE  (report-adakotipathi.css)
    |   English → .ak-banner.lang-en ...
    |   Sinhala → .ak-banner.lang-si ...
    |   Tamil   → .ak-banner.lang-ta ...
    |──────────────────────────────────────────────────────────────────────────
    */
    $lang = $language ?? 'en';
    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };
@endphp

{{-- [A] ROOT WRAPPER — lang-* class here drives ALL CSS language isolation --}}
<div class="lottery-banner-container ak-banner lang-{{ $lang }}">

    {{-- [B] BACKGROUND IMAGE — shared for en / si / ta --}}
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/adakotipathi_all.png') }}"
         alt="Ada Kotipathi Banner"
         class="lottery-banner-image">

    {{-- [C] DATA OVERLAY
         CSS font-size controlled per language:
           English → .ak-banner.lang-en .ak-data-overlay { font-size: 1.1cqw }
           Sinhala → .ak-banner.lang-si .ak-data-overlay { font-size: 1.85cqw }
           Tamil   → .ak-banner.lang-ta .ak-data-overlay { font-size: 1.5cqw } --}}
    <div class="ak-data-overlay">

        {{-- [C1] ROW 1 — Draw Number + Colour boxes
             English → .ak-banner.lang-en .ak-info-label { font-size: 1.3em; padding: 0.5em 1em }
                       .ak-banner.lang-en .ak-info-val   { font-size: 2.4em }
             Sinhala → .ak-banner.lang-si .ak-info-label { font-size: 1.1em; padding: 0.4em 0.7em }
                       .ak-banner.lang-si .ak-info-val   { font-size: 1.9em }
             Tamil   → .ak-banner.lang-ta .ak-info-label { font-size: 1.0em; padding: 0.3em 0.5em }
                       .ak-banner.lang-ta .ak-info-val   { font-size: 1.8em } --}}
        <div class="ak-info-row">

            <div class="ak-info-box">
                <span class="ak-info-label">{{ $L('Draw Number') }}</span>
                <span class="ak-info-val">{{ $draw->draw_number }}</span>
            </div>

            @if($draw->color)
                <div class="ak-info-box">
                    <span class="ak-info-label">{{ $L('Colour') }}</span>
                    <span class="ak-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                </div>
            @endif

        </div>
        {{-- END [C1] --}}

        {{-- [C2] ROW 2 — Column Labels
             English → .ak-banner.lang-en .ak-lbl { font-size: 1.1em }
             Sinhala → .ak-banner.lang-si .ak-lbl { font-size: 0.95em }
             Tamil   → .ak-banner.lang-ta .ak-lbl { font-size: 0.85em } --}}
        <div class="ak-labels-row">

            @if($draw->english_letters)
                <span class="ak-lbl ak-col-eng">{{ $L('English Letter') }}</span>
            @endif

            <span class="ak-lbl ak-col-win">{{ $L('Winning Numbers') }}</span>

        </div>
        {{-- END [C2] --}}

        {{-- [C3] ROW 3 — Ball Groups
             English → .ak-banner.lang-en .ak-ball { font-size: 3.2em }
             Sinhala → .ak-banner.lang-si .ak-ball { font-size: 2.8em }
             Tamil   → .ak-banner.lang-ta .ak-ball { font-size: 2.5em } --}}
        <div class="ak-results-row">

            {{-- English Letter ball --}}
            @if($draw->english_letters)
                <div class="ak-ball-group ak-col-eng">
                    @foreach(explode(',', $draw->english_letters) as $letter)
                        <div class="ak-ball circle-white">{{ strtoupper(ltrim(trim($letter), '0')) }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Winning Numbers --}}
            <div class="ak-ball-group ak-col-win">
                @foreach($draw->numbers as $number)
                    <div class="ak-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                @endforeach
            </div>

        </div>
        {{-- END [C3] --}}

        {{-- [C4] ROW 4 — Next Jackpot Pill
             English → .ak-banner.lang-en .ak-jackpot-pill { font-size: 2.3em; padding: 0.3em 1em }
             Sinhala → .ak-banner.lang-si .ak-jackpot-pill { font-size: 1.8em; padding: 0.3em 0.8em }
             Tamil   → .ak-banner.lang-ta .ak-jackpot-pill { font-size: 1.8em; padding: 0.3em 0.7em } --}}
        @if($draw->next_jackpot)
            <div class="ak-jackpot-row">
                <div class="ak-jackpot-pill">
                    {{ $L('Next Jackpot') }} : {{ $lang === 'ta' ? 'ரூ.' : 'Rs.' }} {{ number_format($draw->next_jackpot, 2) }}
                </div>
            </div>
        @endif
        {{-- END [C4] --}}

    </div>
    {{-- END [C] --}}

</div>
{{-- END [A] --}}
