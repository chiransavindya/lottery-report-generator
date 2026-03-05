@php
    /*
    |──────────────────────────────────────────────────────────────────────────
    | KAPRUKA LOTTERY — kp.blade.php
    |──────────────────────────────────────────────────────────────────────────
    | $lang resolves to: 'en' | 'si' | 'ta'
    |
    | The root div carries   .lang-en / .lang-si / .lang-ta
    | so every CSS override in report-kapruka.css is cleanly scoped.
    |
    | HTML SECTION MAP
    | ─────────────────────────────────────────────────────────────────────────
    | [A] ROOT WRAPPER      — .lottery-banner-container .kp-banner .lang-{$lang}
    | [B] BACKGROUND IMAGE  — kapruka_all.png
    | [C] DATA OVERLAY      — .kp-data-overlay
    |      ├─ [C1] ROW 1 — Draw info boxes (Draw Number / Colour)
    |      ├─ [C2] ROW 2 — Column labels   (English Letter / Super Number / Winning Numbers)
    |      ├─ [C3] ROW 3 — Ball groups     (letter ball | super ball | winning balls)
    |      └─ [C4] ROW 4 — Jackpot pill    (Next Jackpot : Rs. ...)
    |──────────────────────────────────────────────────────────────────────────
    */
    $lang = $language ?? 'en';
    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };
@endphp

{{-- ═══════════════════════════════════════════════════════════════════════
     [A] ROOT WRAPPER
     CSS scope: .kp-banner.lang-en | .kp-banner.lang-si | .kp-banner.lang-ta
     The lang-* class on this div is the ONLY switch between language styles.
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="lottery-banner-container kp-banner lang-{{ $lang }}">

    {{-- ─────────────────────────────────────────────────────────────────
         [B] BACKGROUND IMAGE
         One image for all 3 languages (en / si / ta all use kapruka_all.png)
         ───────────────────────────────────────────────────────────────── --}}
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/kapruka_all.png') }}"
         alt="Kapruka Banner"
         class="lottery-banner-image">

    {{-- ─────────────────────────────────────────────────────────────────
         [C] DATA OVERLAY
         CSS: .kp-data-overlay
         Font-size (cqw) is overridden per language in report-kapruka.css:
           English → .kp-banner.lang-en .kp-data-overlay { font-size: 1.1cqw }
           Sinhala → .kp-banner.lang-si .kp-data-overlay { font-size: 1.75cqw }
           Tamil   → .kp-banner.lang-ta .kp-data-overlay { font-size: 1.5cqw }
         ───────────────────────────────────────────────────────────────── --}}
    <div class="kp-data-overlay">

        {{-- ───────────────────────────────────────────────────────────
             [C1] ROW 1 — Draw Info (Draw Number box + Colour box)
             CSS (common):  .kp-info-row  /  .kp-info-box
             CSS (labels):  .kp-info-label
               English → .kp-banner.lang-en .kp-info-label { font-size: 1.3em; padding: 0.5em 1em }
               Sinhala → .kp-banner.lang-si .kp-info-label { font-size: 1.1em; padding: 0.4em 0.7em }
               Tamil   → .kp-banner.lang-ta .kp-info-label { font-size: 1.0em; padding: 0.3em 0.5em }
             CSS (values): .kp-info-val
               English → .kp-banner.lang-en .kp-info-val { font-size: 2.4em }
               Sinhala → .kp-banner.lang-si .kp-info-val { font-size: 1.9em }
               Tamil   → .kp-banner.lang-ta .kp-info-val { font-size: 1.8em }
             ─────────────────────────────────────────────────────────── --}}
        <div class="kp-info-row">

            {{-- Draw Number box (all languages) --}}
            <div class="kp-info-box">
                <span class="kp-info-label">{{ $L('Draw Number') }}</span>
                <span class="kp-info-val">{{ $draw->draw_number }}</span>
            </div>

            {{-- Colour box (shown only when colour data is present) --}}
            @if($draw->color)
                <div class="kp-info-box">
                    <span class="kp-info-label">{{ $L('Colour') }}</span>
                    <span class="kp-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                </div>
            @endif

        </div>
        {{-- END [C1] ROW 1 --}}

        {{-- ───────────────────────────────────────────────────────────
             [C2] ROW 2 — Column Labels
             CSS (common): .kp-labels-row  /  .kp-lbl  /  .kp-col-eng  /  .kp-col-sup  /  .kp-col-win
             CSS (font):   .kp-lbl
               English → .kp-banner.lang-en .kp-lbl { font-size: 1.1em }
               Sinhala → .kp-banner.lang-si .kp-lbl { font-size: 0.95em }
               Tamil   → .kp-banner.lang-ta .kp-lbl { font-size: 0.85em }
             ─────────────────────────────────────────────────────────── --}}
        <div class="kp-labels-row">

            {{-- English Letter label (shown only when english_letters data exists) --}}
            @if($draw->english_letters)
                <span class="kp-lbl kp-col-eng">{{ $L('English Letter') }}</span>
            @endif

            {{-- Super Number label (shown only when super_number data exists) --}}
            @if($draw->super_number)
                <span class="kp-lbl kp-col-sup">{{ $L('Super Number') }}</span>
            @endif

            {{-- Winning Numbers label (always shown) --}}
            <span class="kp-lbl kp-col-win">{{ $L('Winning Numbers') }}</span>

        </div>
        {{-- END [C2] ROW 2 --}}

        {{-- ───────────────────────────────────────────────────────────
             [C3] ROW 3 — Ball Groups
             CSS (common): .kp-balls-row  /  .kp-ball-group  /  .kp-ball
             CSS (sizes):  .kp-ball
               English → .kp-banner.lang-en .kp-ball { font-size: 3.2em }
               Sinhala → .kp-banner.lang-si .kp-ball { font-size: 2.8em }
               Tamil   → .kp-banner.lang-ta .kp-ball { font-size: 2.5em }
             ─────────────────────────────────────────────────────────── --}}
        <div class="kp-balls-row">

            {{-- English Letter ball (shown only when data exists) --}}
            @if($draw->english_letters)
                <div class="kp-ball-group kp-col-eng">
                    @foreach(explode(',', $draw->english_letters) as $letter)
                        <div class="kp-ball circle-white">{{ strtoupper(ltrim(trim($letter), '0')) }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Super Number ball (shown only when data exists) --}}
            @if($draw->super_number)
                <div class="kp-ball-group kp-col-sup">
                    <div class="kp-ball circle-white">{{ str_pad($draw->super_number, 2, '0', STR_PAD_LEFT) }}</div>
                </div>
            @endif

            {{-- Winning Numbers balls (always shown) --}}
            <div class="kp-ball-group kp-col-win">
                @foreach($draw->numbers as $number)
                    <div class="kp-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                @endforeach
            </div>

        </div>
        {{-- END [C3] ROW 3 --}}

        {{-- ───────────────────────────────────────────────────────────
             [C4] ROW 4 — Next Jackpot Pill
             CSS (common): .kp-jackpot-row  /  .kp-jackpot-pill
             CSS (sizes):  .kp-jackpot-pill
               English → .kp-banner.lang-en .kp-jackpot-pill { font-size: 2.3em; padding: 0.3em 1em }
               Sinhala → .kp-banner.lang-si .kp-jackpot-pill { font-size: 1.8em; padding: 0.3em 0.8em }
               Tamil   → .kp-banner.lang-ta .kp-jackpot-pill { font-size: 1.8em; padding: 0.3em 0.7em }
             ─────────────────────────────────────────────────────────── --}}
        @if($draw->next_jackpot)
            <div class="kp-jackpot-row">
                <div class="kp-jackpot-pill">
                    {{ $L('Next Jackpot') }} : Rs. {{ number_format($draw->next_jackpot, 2) }}
                </div>
            </div>
        @endif
        {{-- END [C4] ROW 4 --}}

    </div>
    {{-- END [C] DATA OVERLAY --}}

</div>
{{-- END [A] ROOT WRAPPER --}}