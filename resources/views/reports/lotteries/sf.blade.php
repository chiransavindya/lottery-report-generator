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

            <!-- Row 4: Jackpot & Second Chance Logo -->
            <div class="sf-jackpot-row">
                @if($draw->next_jackpot)
                    <div class="sf-jackpot-pill">
                        {{ $L('Next Jackpot') }} : Rs.
                        {{ number_format($draw->next_jackpot, 2) }}
                    </div>
                @else
                    <div></div>
                @endif
                <img src="{{ asset('images/pdf_static_images/lottery_bg_images/sc.png') }}" alt="Second Chance" class="sf-second-chance-logo">
            </div>
        </div>

        <!-- Right Side: Prize Table -->
        @php
            $prizeRows = [];
            $targetAmounts = [40, 200]; // Shanida specific targets
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
            <div class="sf-table-content">
                <table class="sf-prize-table">
                    <thead>
                        <tr>
                            <th>{{ $L('Amount') }}</th>
                            <th>{{ $L('Special No') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prizeRows as $prize)
                            @php
                                $amt = (int) ($prize['amount'] ?? $prize['value'] ?? 0);
                                $metaKey = "SP_{$amt}_NO";
                                $specialNo = $draw->metadata[$metaKey] ?? $prize['code'] ?? '-';
                            @endphp
                            <tr>
                                <td class="sf-prize-amt">Rs. {{ number_format($amt) }}/-</td>
                                <td class="sf-special-no">{{ $specialNo }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>