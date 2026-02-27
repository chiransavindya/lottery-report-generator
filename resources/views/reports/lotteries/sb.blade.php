<div class="lottery-banner-container sb-banner">
    @php
        $L = function ($key) use ($labels) {
            return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
        };
    @endphp
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/superball_all.png') }}" alt="Superball Banner"
        class="lottery-banner-image">

    <div class="sb-data-overlay">
        <!-- Main Content Area (Left/Center) -->
        <div class="sb-main-content">
            <!-- Row 1: Draw Info -->
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

            <!-- Row 2: Labels -->
            <div class="sb-labels-row">
                <span class="sb-lbl col-win">{{ $L('Winning Numbers') }}</span>
                @if($draw->english_letters)
                    <span class="sb-lbl col-eng">{{ $L('English Letter') }}</span>
                @endif
            </div>

            <!-- Row 3: Results -->
            <div class="sb-results-row">
                <!-- Winning Numbers -->
                <div class="sb-ball-group col-win">
                    @foreach($draw->numbers as $number)
                        <div class="sb-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                    @endforeach
                </div>

                <!-- English Letter -->
                @if($draw->english_letters)
                    <div class="sb-ball-group col-eng">
                        @foreach(explode(',', $draw->english_letters) as $letter)
                            <div class="sb-ball circle-white">{{ strtoupper(ltrim(trim($letter), '0')) }}</div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Row 4: Jackpot -->
            @if($draw->next_jackpot)
                <div class="sb-jackpot-row">
                    <div class="sb-jackpot-pill">
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
            <div class="sb-prize-card">
                <!-- Logo on the left -->
                <div class="sb-prize-card-logo">
                    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/sc.png') }}" alt="Second Chance">
                </div>
                <!-- Right: title + columns -->
                <div class="sb-prize-card-body">
                    <div class="sb-prize-card-title">{{ $L('Special No') }}</div>
                    <div class="sb-prize-card-cols">
                        @foreach($prizeRows as $p)
                            @php $amt = (int) ($p['amount'] ?? $p['value'] ?? 0); @endphp
                            <div class="sb-prize-card-col">
                                <div class="sb-prize-card-label">Rs.{{ number_format($amt) }}/- {{ $L('Prize') }}</div>
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
    </div>
</div>