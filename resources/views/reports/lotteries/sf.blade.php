@php
    $lang = $language ?? 'en';
    // 'si' and 'en' use shanida_all.png, 'ta' uses shanida_tamil.png
    $sfImage = ($lang === 'ta') ? 'shanida_tamil.png' : 'shanida_all.png';

    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };
@endphp

<div class="lottery-banner-container sf-banner">
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
                <span class="sf-lbl col-win">{{ $L('Winning Numbers') }}</span>
                @if($draw->english_letters)
                    <span class="sf-lbl col-eng">{{ $L('English Letter') }}</span>
                @endif
            </div>

            <!-- Row 3: Results -->
            <div class="sf-results-row">
                <!-- Winning Numbers -->
                <div class="sf-ball-group col-win">
                    @foreach($draw->numbers as $number)
                        <div class="sf-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                    @endforeach
                </div>

                <!-- English Letter -->
                @if($draw->english_letters)
                    <div class="sf-ball-group col-eng">
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
            <div class="sf-prize-card">
                <!-- Logo on the left -->
                <div class="sf-prize-card-logo">
                    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/sc.png') }}" alt="Second Chance">
                </div>
                <!-- Right: title + columns -->
                <div class="sf-prize-card-body">
                    <div class="sf-prize-card-title">{{ $L('Special No') }}</div>
                    <div class="sf-prize-card-cols">
                        @foreach($prizeRows as $p)
                            @php $amt = (int) ($p['amount'] ?? $p['value'] ?? 0); @endphp
                            <div class="sf-prize-card-col">
                                <div class="sf-prize-card-label">Rs.{{ number_format($amt) }}/- {{ $L('Prize') }}</div>
                                <div class="sf-prize-card-number">{{ $specialNos[$amt] ?? '-' }}</div>
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