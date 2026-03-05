@php
    /*
    |──────────────────────────────────────────────────────────────────────────
    | JAYASAMPATHA — js.blade.php
    |──────────────────────────────────────────────────────────────────────────
    | HTML SECTION MAP
    | [A] ROOT WRAPPER     → .lottery-banner-container .js-banner .lang-{$lang}
    | [B] BACKGROUND IMAGE → jayasampatha_all.png  (same for all 3 languages)
    | [C] DATA OVERLAY     → .js-data-overlay
    |     ├─ [C1] ROW 1   → Draw Number box + Colour box  (.js-info-label / .js-info-val)
    |     ├─ [C2] ROW 2   → Column labels                 (.js-lbl)
    |     ├─ [C3] ROW 3   → Ball groups                   (.js-ball)
    |     └─ [C4] ROW 4   → Total Prize Value / Jackpot   (.js-jackpot-pill)
    |
    | CSS OVERRIDES PER LANGUAGE  (report-jayasampatha.css)
    |   English → .js-banner.lang-en ...
    |   Sinhala → .js-banner.lang-si ...
    |   Tamil   → .js-banner.lang-ta ...
    |──────────────────────────────────────────────────────────────────────────
    */
    $lang = $language ?? 'en';
    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };
@endphp

{{-- [A] ROOT WRAPPER --}}
<div class="lottery-banner-container js-banner lang-{{ $lang }}">

    {{-- [B] BACKGROUND IMAGE --}}
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/jayasampatha_all.png') }}"
         alt="Jayasampatha Banner"
         class="lottery-banner-image">

    {{-- [C] DATA OVERLAY
         English → .js-banner.lang-en .js-data-overlay { font-size: 1.1cqw }
         Sinhala → .js-banner.lang-si .js-data-overlay { font-size: 1.85cqw }
         Tamil   → .js-banner.lang-ta .js-data-overlay { font-size: 1.5cqw } --}}
    <div class="js-data-overlay">

        {{-- [C1] ROW 1 — Draw Number + Colour boxes
             English → .js-banner.lang-en .js-info-label { font-size: 1.3em; padding: 0.5em 1em }
                       .js-banner.lang-en .js-info-val   { font-size: 2.4em }
             Sinhala → .js-banner.lang-si .js-info-label { font-size: 1.1em; padding: 0.4em 0.7em }
                       .js-banner.lang-si .js-info-val   { font-size: 1.9em }
             Tamil   → .js-banner.lang-ta .js-info-label { font-size: 1.0em; padding: 0.3em 0.5em }
                       .js-banner.lang-ta .js-info-val   { font-size: 1.8em } --}}
        <div class="js-info-row">

            <div class="js-info-box">
                <span class="js-info-label">{{ $L('Draw Number') }}</span>
                <span class="js-info-val">{{ $draw->draw_number }}</span>
            </div>

            @if($draw->color)
                <div class="js-info-box">
                    <span class="js-info-label">{{ $L('Colour') }}</span>
                    <span class="js-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                </div>
            @endif

        </div>
        {{-- END [C1] --}}

        {{-- [C2] ROW 2 — Column Labels
             English → .js-banner.lang-en .js-lbl { font-size: 1.1em }
             Sinhala → .js-banner.lang-si .js-lbl { font-size: 0.95em }
             Tamil   → .js-banner.lang-ta .js-lbl { font-size: 0.85em } --}}
        <div class="js-labels-row">

            @if($draw->english_letters)
                <span class="js-lbl js-col-eng">{{ $L('English Letter') }}</span>
            @endif

            <span class="js-lbl js-col-win">{{ $L('Winning Numbers') }}</span>

        </div>
        {{-- END [C2] --}}

        {{-- [C3] ROW 3 — Ball Groups
             English → .js-banner.lang-en .js-ball { font-size: 3.2em }
             Sinhala → .js-banner.lang-si .js-ball { font-size: 2.8em }
             Tamil   → .js-banner.lang-ta .js-ball { font-size: 2.5em } --}}
        <div class="js-results-row">

            {{-- English Letter ball --}}
            @if($draw->english_letters)
                <div class="js-ball-group js-col-eng">
                    @foreach(explode(',', $draw->english_letters) as $letter)
                        <div class="js-ball circle-white js-letter-ball">{{ strtoupper(ltrim(trim($letter), '0')) }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Winning Numbers --}}
            <div class="js-ball-group js-col-win">
                @foreach($draw->numbers as $number)
                    <div class="js-ball circle-white">{{ (int) $number }}</div>
                @endforeach
            </div>

        </div>
        {{-- END [C3] --}}

        {{-- [C4] ROW 4 — Total Prize Value or Next Jackpot Pill
             English → .js-banner.lang-en .js-jackpot-pill { font-size: 2.3em; padding: 0.3em 1em }
             Sinhala → .js-banner.lang-si .js-jackpot-pill { font-size: 1.8em; padding: 0.3em 0.8em }
             Tamil   → .js-banner.lang-ta .js-jackpot-pill { font-size: 1.8em; padding: 0.3em 0.7em } --}}
        @if($draw->total_prize_value || $draw->total_sales)
            <div class="js-jackpot-row">
                <div class="js-jackpot-pill">
                    {{ $L('Total Prize Value') }} : Rs.
                    {{ number_format($draw->total_prize_value ?? $draw->total_sales, 2) }}
                </div>
            </div>
        @elseif($draw->next_jackpot)
            <div class="js-jackpot-row">
                <div class="js-jackpot-pill">
                    {{ $L('Next Jackpot') }} : Rs. {{ number_format($draw->next_jackpot, 2) }}
                </div>
            </div>
        @endif
        {{-- END [C4] --}}

    </div>
    {{-- END [C] --}}

</div>
{{-- END [A] --}}