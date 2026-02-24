<div class="lottery-banner-container js-banner">
    @php
        $L = function ($key) use ($labels) {
            return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
        };
    @endphp
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/jayasampatha_all.png') }}" alt="Jayasampatha Banner"
        class="lottery-banner-image">

    <div class="js-data-overlay">
        <!-- Row 1: Draw Info -->
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

        <!-- Row 2: Labels -->
        <div class="js-labels-row">
            @if($draw->english_letters)
                <span class="js-lbl col-eng">{{ $L('English Letter') }}</span>
            @endif
            <span class="js-lbl col-win">{{ $L('Winning Numbers') }}</span>
        </div>

        <!-- Row 3: Results (English Letter + Winning Numbers) -->
        <div class="js-results-row">
            <!-- English Letter -->
            @if($draw->english_letters)
                <div class="js-ball-group col-eng">
                    @foreach(explode(',', $draw->english_letters) as $letter)
                        <div class="js-ball circle-white letter-ball">{{ strtoupper(ltrim(trim($letter), '0')) }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Winning Numbers -->
            <div class="js-ball-group col-win">
                @foreach($draw->numbers as $number)
                    <div class="js-ball circle-white">{{ (int) $number }}</div>
                @endforeach
            </div>
        </div>

        <!-- Row 4: Total Value / Jackpot -->
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
    </div>
</div>