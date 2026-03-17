@php
    /*
    |──────────────────────────────────────────────────────────────────────────
    | SUPIRI DHANA SAMPATHA — ds.blade.php
    |──────────────────────────────────────────────────────────────────────────
    | HTML SECTION MAP
    | [A] ROOT WRAPPER     → .lottery-banner-container .ds-banner .lang-{$lang}
    | [B] BACKGROUND IMAGE → supiridhana_all.png  (same for all 3 languages)
    | [C] DATA OVERLAY     → .ds-data-overlay
    |     ├─ [C1] ROW 1   → Draw Number box + Colour box  (.ds-info-label / .ds-info-val)
    |     ├─ [C2] ROW 2   → Column labels                 (.ds-lbl)
    |     ├─ [C3] ROW 3   → Ball groups                   (.ds-ball)
    |     └─ [C4] ROW 4   → Jackpot pill                  (.ds-jackpot-pill)
    |
    | CSS OVERRIDES PER LANGUAGE  (report-supiridhana.css)
    |   English → .ds-banner.lang-en ...
    |   Sinhala → .ds-banner.lang-si ...
    |   Tamil   → .ds-banner.lang-ta ...
    |──────────────────────────────────────────────────────────────────────────
    */
    $lang = $language ?? 'en';
    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };
@endphp

{{-- [A] ROOT WRAPPER --}}
<div class="lottery-banner-container ds-banner lang-{{ $lang }}">

    {{-- [B] BACKGROUND IMAGE --}}
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/supiridhana_all.png') }}"
         alt="Supiri Dhana Sampatha Banner"
         class="lottery-banner-image">

    {{-- [C] DATA OVERLAY
         English → .ds-banner.lang-en .ds-data-overlay { font-size: 1.1cqw }
         Sinhala → .ds-banner.lang-si .ds-data-overlay { font-size: 1.85cqw }
         Tamil   → .ds-banner.lang-ta .ds-data-overlay { font-size: 1.5cqw } --}}
    <div class="ds-data-overlay">

        {{-- [C1] ROW 1 — Draw Number + Colour boxes
             English → .ds-banner.lang-en .ds-info-label { font-size: 1.3em; padding: 0.5em 1em }
                       .ds-banner.lang-en .ds-info-val   { font-size: 2.4em }
             Sinhala → .ds-banner.lang-si .ds-info-label { font-size: 1.1em; padding: 0.4em 0.7em }
                       .ds-banner.lang-si .ds-info-val   { font-size: 1.9em }
             Tamil   → .ds-banner.lang-ta .ds-info-label { font-size: 1.0em; padding: 0.3em 0.5em }
                       .ds-banner.lang-ta .ds-info-val   { font-size: 1.8em } --}}
        <div class="ds-info-row">

            <div class="ds-info-box">
                <span class="ds-info-label">{{ $L('Draw Number') }}</span>
                <span class="ds-info-val">{{ $draw->draw_number }}</span>
            </div>

            @if($draw->color)
                <div class="ds-info-box">
                    <span class="ds-info-label">{{ $L('Colour') }}</span>
                    <span class="ds-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                </div>
            @endif

        </div>
        {{-- END [C1] --}}

        {{-- [C2] ROW 2 — Column Labels
             English → .ds-banner.lang-en .ds-lbl { font-size: 1.1em }
             Sinhala → .ds-banner.lang-si .ds-lbl { font-size: 0.95em }
             Tamil   → .ds-banner.lang-ta .ds-lbl { font-size: 0.85em } --}}
        <div class="ds-labels-row">

            @if($draw->english_letters)
                <span class="ds-lbl ds-col-eng">{{ $L('English Letter') }}</span>
            @endif

            <span class="ds-lbl ds-col-win">{{ $L('Winning Numbers') }}</span>

        </div>
        {{-- END [C2] --}}

        {{-- [C3] ROW 3 — Ball Groups
             English → .ds-banner.lang-en .ds-ball { font-size: 3.2em }
             Sinhala → .ds-banner.lang-si .ds-ball { font-size: 2.8em }
             Tamil   → .ds-banner.lang-ta .ds-ball { font-size: 2.5em } --}}
        <div class="ds-results-row">

            {{-- English Letter ball --}}
            @if($draw->english_letters)
                <div class="ds-ball-group ds-col-eng">
                    @foreach(explode(',', $draw->english_letters) as $letter)
                        <div class="ds-ball circle-white ds-letter-ball">{{ strtoupper(ltrim(trim($letter), '0')) }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Winning Numbers --}}
            <div class="ds-ball-group ds-col-win">
                @foreach($draw->numbers as $number)
                    <div class="ds-ball circle-white">{{ (int) $number }}</div>
                @endforeach
            </div>

        </div>
        {{-- END [C3] --}}

        {{-- [C4] ROW 4 — Next Jackpot Pill
             English → .ds-banner.lang-en .ds-jackpot-pill { font-size: 2.3em; padding: 0.3em 1em }
             Sinhala → .ds-banner.lang-si .ds-jackpot-pill { font-size: 1.8em; padding: 0.3em 0.8em }
             Tamil   → .ds-banner.lang-ta .ds-jackpot-pill { font-size: 1.8em; padding: 0.3em 0.7em } --}}
        @if($draw->next_jackpot)
            <div class="ds-jackpot-row">
                <div class="ds-jackpot-pill">
                    {{ $L('Next Jackpot') }} : {{ $lang === 'ta' ? 'ரூ.' : 'Rs.' }} {{ number_format($draw->next_jackpot, 2) }}
                </div>
            </div>
        @endif
        {{-- END [C4] --}}

    </div>
    {{-- END [C] --}}

</div>
{{-- END [A] --}}