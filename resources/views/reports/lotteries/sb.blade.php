@php
    /*
    |──────────────────────────────────────────────────────────────────────────
    | SUPERBALL — sb.blade.php
    |──────────────────────────────────────────────────────────────────────────
    | HTML SECTION MAP
    | [A] ROOT WRAPPER      → .lottery-banner-container .sb-banner .lang-{$lang}
    | [B] BACKGROUND IMAGE  → superball_all.png  (same for all 3 languages)
    | [C] DATA OVERLAY      → .sb-data-overlay
    |     ├─ [C-MAIN] LEFT  → .sb-main-content
    |     │   ├─ [C1] ROW 1 → Draw Number + Colour boxes  (.sb-info-label / .sb-info-val)
    |     │   ├─ [C2] ROW 2 → Column labels               (.sb-lbl)
    |     │   ├─ [C3] ROW 3 → Winning balls + Letter ball  (.sb-ball)
    |     │   └─ [C4] ROW 4 → Next Jackpot pill            (.sb-jackpot-pill)
    |     └─ [C-PRIZE] RIGHT → Prize Card               (.sb-prize-card-*)
    |          logo: sc.png (Second Chance — KEEP AS IS, do not change)
    |
    | CSS OVERRIDES PER LANGUAGE  (report-superball.css)
    |   English → .sb-banner.lang-en ...
    |   Sinhala → .sb-banner.lang-si ...
    |   Tamil   → .sb-banner.lang-ta ...
    |──────────────────────────────────────────────────────────────────────────
    */
    $lang = $language ?? 'en';
    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };
@endphp

{{-- [A] ROOT WRAPPER --}}
<div class="lottery-banner-container sb-banner lang-{{ $lang }}">

    {{-- [B] BACKGROUND IMAGE --}}
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/superball_all.png') }}"
         alt="Superball Banner"
         class="lottery-banner-image">

    {{-- [C] DATA OVERLAY
         English → .sb-banner.lang-en .sb-data-overlay { font-size: 1.1cqw }
         Sinhala → .sb-banner.lang-si .sb-data-overlay { font-size: 1.85cqw }
         Tamil   → .sb-banner.lang-ta .sb-data-overlay { font-size: 1.5cqw } --}}
    <div class="sb-data-overlay">

        {{-- [C-MAIN] LEFT — main lottery data --}}
        <div class="sb-main-content-{{ $lang }}">

            {{-- [C1] ROW 1 — Draw Number + Colour boxes
                 English → .sb-banner.lang-en .sb-info-label { font-size: 1.3em; padding: 0.5em 1em }
                           .sb-banner.lang-en .sb-info-val   { font-size: 2.4em }
                 Sinhala → .sb-banner.lang-si .sb-info-label { font-size: 1.1em; padding: 0.4em 0.7em }
                           .sb-banner.lang-si .sb-info-val   { font-size: 1.9em }
                 Tamil   → .sb-banner.lang-ta .sb-info-label { font-size: 0.95em; padding: 0.3em 0.5em }
                           .sb-banner.lang-ta .sb-info-val   { font-size: 1.8em } --}}
            <div class="sb-info-row">

                <div class="sb-info-box">
                    <span class="sb-info-label">{{ $L('Draw Number') }}</span>
                    <span class="sb-info-val">{{ $draw->draw_number }}</span>
                </div>

                @if($draw->color)
                    <div class="sb-info-box">
                        <span class="sb-info-label">{{ $L('Colour') }}</span>
                        <span class="sb-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                    </div>
                @endif

            </div>
            {{-- END [C1] --}}

            {{-- [C2] ROW 2 — Column Labels
                 English → .sb-banner.lang-en .sb-lbl { font-size: 1.1em }
                 Sinhala → .sb-banner.lang-si .sb-lbl { font-size: 0.95em }
                 Tamil   → .sb-banner.lang-ta .sb-lbl { font-size: 0.8em } --}}
            <div class="sb-labels-row">

                <span class="sb-lbl sb-col-win">{{ $L('Winning Numbers') }}</span>

                @if($draw->english_letters)
                    <span class="sb-lbl sb-col-eng">{{ $L('English Letter') }}</span>
                @endif

            </div>
            {{-- END [C2] --}}

            {{-- [C3] ROW 3 — Ball Groups
                 English → .sb-banner.lang-en .sb-ball { font-size: 3.2em }
                 Sinhala → .sb-banner.lang-si .sb-ball { font-size: 2.8em }
                 Tamil   → .sb-banner.lang-ta .sb-ball { font-size: 2.5em } --}}
            <div class="sb-results-row">

                {{-- Winning Numbers --}}
                <div class="sb-ball-group sb-col-win">
                    @foreach($draw->numbers as $number)
                        <div class="sb-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                    @endforeach
                </div>

                {{-- English Letter ball --}}
                @if($draw->english_letters)
                    <div class="sb-ball-group sb-col-eng">
                        @foreach(explode(',', $draw->english_letters) as $letter)
                            <div class="sb-ball circle-white">{{ strtoupper(ltrim(trim($letter), '0')) }}</div>
                        @endforeach
                    </div>
                @endif

            </div>
            {{-- END [C3] --}}

            {{-- [C4] ROW 4 — Next Jackpot Pill
                 English → .sb-banner.lang-en .sb-jackpot-pill { font-size: 2.3em; padding: 0.3em 1em }
                 Sinhala → .sb-banner.lang-si .sb-jackpot-pill { font-size: 1.8em; padding: 0.3em 0.8em }
                 Tamil   → .sb-banner.lang-ta .sb-jackpot-pill { font-size: 1.8em; padding: 0.3em 0.7em } --}}
            @if($draw->next_jackpot)
                <div class="sb-jackpot-row">
                    <div class="sb-jackpot-pill">
                        {{ $L('Next Jackpot') }} : {{ $lang === 'ta' ? 'ரூ.' : 'Rs.' }}
                        {{ number_format($draw->next_jackpot, 2) }}
                    </div>
                </div>
            @endif
            {{-- END [C4] --}}

        </div>
        {{-- END [C-MAIN] --}}

        {{-- [C-PRIZE] RIGHT — Prize Card
             Logo: sc.png (Second Chance) — DO NOT change for Superball.
             Prize card text sizes:
               English → .sb-banner.lang-en .sb-prize-card-title  { font-size: 1.4em }
                         .sb-banner.lang-en .sb-prize-card-label  { font-size: 1.1em }
                         .sb-banner.lang-en .sb-prize-card-number { font-size: 2.6em }
               Sinhala → .sb-banner.lang-si .sb-prize-card-title  { font-size: 1.15em }
                         .sb-banner.lang-si .sb-prize-card-label  { font-size: 0.9em }
                         .sb-banner.lang-si .sb-prize-card-number { font-size: 2.2em }
               Tamil   → .sb-banner.lang-ta .sb-prize-card-title  { font-size: 1.05em }
                         .sb-banner.lang-ta .sb-prize-card-label  { font-size: 0.8em }
                         .sb-banner.lang-ta .sb-prize-card-number { font-size: 2.0em } --}}
        @php
            $prizeRows = [];
            $targetAmounts = [200, 40]; // Rs.200 first, then Rs.40
            if ($draw->prize_breakdown) {
                foreach ($targetAmounts as $target) {
                    foreach ($draw->prize_breakdown as $p) {
                        if ((int) ($p['amount'] ?? $p['value'] ?? 0) == $target) {
                            $prizeRows[] = $p;
                            break;
                        }
                    }
                }
            }
        @endphp

        @if(count($prizeRows) > 0)
            @php
                $specialNos = [];
                foreach ($prizeRows as $p) {
                    $amt = (int) ($p['amount'] ?? $p['value'] ?? 0);
                    $metaKey = "SP_{$amt}_NO";
                    $specialNos[$amt] = $draw->metadata[$metaKey] ?? $p['code'] ?? '-';
                }
            @endphp
            <div class="sb-prize-card">

                {{-- Logo — sc.png (Second Chance). Keep as-is for Superball. --}}
                <div class="sb-prize-card-logo">
                    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/sc.png') }}" alt="Second Chance">
                </div>

                {{-- Prize columns --}}
                <div class="sb-prize-card-body">
                    <div class="sb-prize-card-title">{{ $L('Special No') }}</div>
                    <div class="sb-prize-card-cols">
                        @foreach($prizeRows as $p)
                            @php $amt = (int) ($p['amount'] ?? $p['value'] ?? 0); @endphp
                            <div class="sb-prize-card-col">
                                <div class="sb-prize-card-label">{{ $lang === 'ta' ? 'ரூ.' : 'Rs.' }}{{ number_format($amt) }}/- {{ $L('Prize') }}</div>
                                <div class="sb-prize-card-number">{{ $specialNos[$amt] ?? '-' }}</div>
                            </div>
                            @if(!$loop->last)
                                <div class="sb-prize-card-divider"></div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>
        @endif
        {{-- END [C-PRIZE] --}}

    </div>
    {{-- END [C] --}}

</div>
{{-- END [A] --}}