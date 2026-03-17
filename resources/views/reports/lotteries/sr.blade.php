@php
    /*
    |──────────────────────────────────────────────────────────────────────────
    | SASIRI — sr.blade.php
    |──────────────────────────────────────────────────────────────────────────
    | HTML SECTION MAP
    | [A] ROOT WRAPPER     → .lottery-banner-container .sr-banner .lang-{$lang}
    | [B] BACKGROUND IMAGE → per-language image (en/si/ta each have their own)
    | [C] DATA OVERLAY     → .sr-data-overlay
    |     └─ [C-MAIN]     → .sr-main-content
    |         ├─ [C1] ROW 1 → Draw Number + Colour boxes (.sr-info-label / .sr-info-val)
    |         ├─ [C2] ROW 2 → Winning Numbers section + Winners stats box side-by-side
    |         │               (.sr-winning-label / .sr-ball / .sr-winners-label)
    |         └─ [C3] ROW 3 → Total Prize Value pill      (.sr-jackpot-pill)
    |
    | CSS OVERRIDES PER LANGUAGE  (report-sasiri.css)
    |   English → .sr-banner.lang-en ...
    |   Sinhala → .sr-banner.lang-si ...
    |   Tamil   → .sr-banner.lang-ta ...
    |──────────────────────────────────────────────────────────────────────────
    */
    $lang = $language ?? 'en';

    // Per-language background images
    $srImageMap = [
        'en' => 'sasiri_english.png',
        'si' => 'sasiri_sinhala.png',
        'ta' => 'sasiri_tamil.png',
    ];
    $srImage = $srImageMap[$lang] ?? 'sasiri_english.png';

    $L = function ($key) use ($labels, $lang) {
        $value = $labels[$key] ?? $key;
        // Only apply title-case for English; Tamil/Sinhala must not be altered
        return $lang === 'en' ? mb_convert_case($value, MB_CASE_TITLE, 'UTF-8') : $value;
    };

    // Winners count from prize breakdown (looks for the Rs.200,000 prize)
    $winnersCount = 0;
    if ($draw->prize_breakdown) {
        foreach ($draw->prize_breakdown as $prize) {
            $val = (int) ($prize['amount'] ?? $prize['value'] ?? 0);
            if ($val == 200000) {
                $winnersCount = $prize['count'] ?? $prize['winners'] ?? 0;
                break;
            }
        }
    }
@endphp

{{-- [A] ROOT WRAPPER --}}
<div class="lottery-banner-container sr-banner lang-{{ $lang }}">

    {{-- [B] BACKGROUND IMAGE — unique per language (controlled by $srImageMap above) --}}
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/' . $srImage) }}"
         alt="Sasiri Banner"
         class="lottery-banner-image">

    {{-- [C] DATA OVERLAY
         English → .sr-banner.lang-en .sr-data-overlay { font-size: 1.1cqw }
         Sinhala → .sr-banner.lang-si .sr-data-overlay { font-size: 1.85cqw }
         Tamil   → .sr-banner.lang-ta .sr-data-overlay { font-size: 1.5cqw } --}}
    <div class="sr-data-overlay">

        {{-- [C-MAIN] Main content column --}}
        <div class="sr-main-content">

            {{-- [C1] ROW 1 — Draw Number + Colour boxes
                 English → .sr-banner.lang-en .sr-info-label { font-size: 1.1em;  padding: 0 0.8em }
                           .sr-banner.lang-en .sr-info-val   { font-size: 2.0em }
                 Sinhala → .sr-banner.lang-si .sr-info-label { font-size: 0.95em; padding: 0 0.6em }
                           .sr-banner.lang-si .sr-info-val   { font-size: 1.6em }
                 Tamil   → .sr-banner.lang-ta .sr-info-label { font-size: 0.85em; padding: 0 0.5em }
                           .sr-banner.lang-ta .sr-info-val   { font-size: 1.4em } --}}
            <div class="sr-info-row">

                <div class="sr-info-box">
                    <span class="sr-info-label">{{ $L('Draw Number') }}</span>
                    <span class="sr-info-val">{{ $draw->draw_number }}</span>
                </div>

                @if($draw->color)
                    <div class="sr-info-box">
                        <span class="sr-info-label">{{ $L('Colour') }}</span>
                        <span class="sr-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                    </div>
                @endif

            </div>
            {{-- END [C1] --}}

            {{-- [C2] ROW 2 — Winning Numbers (left) + Winners Stats (right)
                 Winning label:
                   English → .sr-banner.lang-en .sr-winning-label { font-size: 1em }
                   Sinhala → .sr-banner.lang-si .sr-winning-label { font-size: 0.85em }
                   Tamil   → .sr-banner.lang-ta .sr-winning-label { font-size: 0.75em }
                 Balls:
                   English → .sr-banner.lang-en .sr-ball { font-size: 3.0em }
                   Sinhala → .sr-banner.lang-si .sr-ball { font-size: 2.6em }
                   Tamil   → .sr-banner.lang-ta .sr-ball { font-size: 2.3em }
                 Winners label:
                   English → .sr-banner.lang-en .sr-winners-label { font-size: 1em }
                   Sinhala → .sr-banner.lang-si .sr-winners-label { font-size: 0.85em }
                   Tamil   → .sr-banner.lang-ta .sr-winners-label { font-size: 0.75em } --}}
            <div class="sr-middle-row">

                {{-- Winning Numbers section --}}
                <div class="sr-winning-section">
                    <div class="sr-winning-label">{{ $L('Winning Numbers') }}</div>
                    <div class="sr-ball-group">
                        @foreach($draw->numbers as $number)
                            <div class="sr-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                        @endforeach
                    </div>
                </div>

                {{-- Winners Stats box (shown only when count > 0) --}}
                @if($winnersCount > 0)
                    <div class="sr-winners-box">
                        <span class="sr-winners-label">{{ $L('Winners') }}</span>
                        <span class="sr-winners-val">{{ str_pad($winnersCount, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                @endif

            </div>
            {{-- END [C2] --}}

            {{-- [C3] ROW 3 — Total Prize Value pill
                 English → .sr-banner.lang-en .sr-jackpot-pill { font-size: 2.0em; padding: 0.3em 1em }
                 Sinhala → .sr-banner.lang-si .sr-jackpot-pill { font-size: 1.6em; padding: 0.3em 0.8em }
                 Tamil   → .sr-banner.lang-ta .sr-jackpot-pill { font-size: 1.4em; padding: 0.3em 0.7em } --}}
            @if($draw->total_prize_value || $draw->total_sales)
                <div class="sr-jackpot-row">
                    <div class="sr-jackpot-pill">
                        {{ $L('Total Value') }} : {{ $lang === 'ta' ? 'ரூ.' : 'Rs.' }}
                        {{ number_format($draw->total_prize_value ?? $draw->total_sales, 2) }}
                    </div>
                </div>
            @endif
            {{-- END [C3] --}}

        </div>
        {{-- END [C-MAIN] --}}

    </div>
    {{-- END [C] --}}

</div>
{{-- END [A] --}}