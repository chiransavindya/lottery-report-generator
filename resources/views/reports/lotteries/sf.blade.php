@php
    $lang = $language ?? 'en';
    // 'si' and 'en' use shanida_all.png, 'ta' uses shanida_tamil.png
    $sfImage = ($lang === 'ta') ? 'shanida_tamil.png' : 'shanida_all.png';

    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };
@endphp

<div class="lottery-banner-container sf-banner lang-{{ $lang }}">
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/' . $sfImage) }}" alt="Shanida Banner"
        class="lottery-banner-image">

    <div class="sf-data-overlay">
        <!-- Main Content Area (Left/Center) -->
        <div class="sf-main-content">
            <!-- Row 1: Draw Info -->
            <div class="sf-info-row">
                <div class="sf-info-box">
                    <span class="sf-info-label">{{ $L('Draw Number') }}</span>
                    <span class="sf-info-val">{{ $draw->draw_number }}</span>
                </div>
                @if($draw->color)
                    <div class="sf-info-box">
                        <span class="sf-info-label">{{ $L('Colour') }}</span>
                        <span class="sf-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                    </div>
                @endif
            </div>

            <!-- Row 2: Labels -->
            <div class="sf-labels-row">
                <span class="sf-lbl sf-col-win">{{ $L('Winning Numbers') }}</span>
                @if($draw->english_letters)
                    <span class="sf-lbl sf-col-eng">{{ $L('English Letter') }}</span>
                @endif
            </div>

            <!-- Row 3: Results -->
            <div class="sf-results-row">
                <!-- Winning Numbers -->
                <div class="sf-ball-group sf-col-win">
                    @foreach($draw->numbers as $number)
                        <div class="sf-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                    @endforeach
                </div>

                <!-- English Letter -->
                @if($draw->english_letters)
                    <div class="sf-ball-group sf-col-eng">
                        @foreach(explode(',', $draw->english_letters) as $letter)
                            <div class="sf-ball circle-white">{{ strtoupper(ltrim(trim($letter), '0')) }}</div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Row 4: Jackpot -->
            @if($draw->next_jackpot)
                <div class="sf-jackpot-row">
                    <div class="sf-jackpot-pill">
                        {{ $L('Next Jackpot') }} : Rs.
                        {{ number_format($draw->next_jackpot, 2) }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Side: Prize Card (horizontal layout matching screenshot) -->
        @php
            /*
             * Build $prizeRows from METADATA keys only.
             *
             * XML attribute format used by the DLB system:
             *   SP_200_NO     → special number for the Rs.200   prize
             *   SP_40_NO      → special number for the Rs.40    prize
             *   SP_50,000_NO  → special number for the Rs.50,000 prize  ← comma in key!
             *
             * IMPORTANT: The parser stores attribute keys verbatim from the XML,
             * so "SP_50,000_NO" contains a literal comma — do NOT use SP_50000_NO.
             *
             * $sfPrizeMap maps: display amount (int) → exact XML metadata key
             * A prize column is ONLY rendered when its metadata key is present
             * and non-empty. No fallback to prize_breakdown codes.
             */
            $sfPrizeMap = [
                50000 => 'SP_50,000_NO',   // Rs.50,000 — note the comma in the key
                200   => 'SP_200_NO',       // Rs.200
                40    => 'SP_40_NO',        // Rs.40
            ];

            $prizeRows = [];
            $sfMeta    = $draw->metadata ?? [];

            foreach ($sfPrizeMap as $sfAmt => $sfMetaKey) {
                if (!empty($sfMeta[$sfMetaKey])) {
                    $prizeRows[] = [
                        'amount'     => $sfAmt,
                        'special_no' => $sfMeta[$sfMetaKey],
                    ];
                }
            }
        @endphp

        @if(count($prizeRows) > 0)
            <div class="sf-prize-card">

                {{-- Logo (Lucky Chance) --}}
                <div class="sf-prize-card-logo">
                    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/lc.png') }}" alt="Lucky Chance">
                </div>

                {{-- One column per SP_{amt}_NO key found in XML metadata --}}
                <div class="sf-prize-card-body">
                    <div class="sf-prize-card-title">{{ $L('Special No') }}</div>
                    <div class="sf-prize-card-cols">
                        @foreach($prizeRows as $row)
                            <div class="sf-prize-card-col">
                                <div class="sf-prize-card-label">
                                    Rs.{{ number_format($row['amount']) }}/- {{ $L('Prize') }}
                                </div>
                                <div class="sf-prize-card-number">{{ $row['special_no'] }}</div>
                            </div>
                            @if(!$loop->last)
                                <div class="sf-prize-card-divider"></div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>
        @endif
    </div>
</div>