<div class="lottery-banner-container ds-banner">
    @php
        $L = function ($key) use ($labels) {
            return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
        };
    @endphp
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/supiridhana_all.png') }}"
        alt="Supiri Dhana Sampatha Banner" class="lottery-banner-image">

    <div class="ds-data-overlay">
        <!-- Row 1: Draw Info -->
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

        <!-- Row 2: Labels -->
        <div class="ds-labels-row">
            @if($draw->english_letters)
                <span class="ds-lbl col-eng">{{ $L('English Letter') }}</span>
            @endif
            <span class="ds-lbl col-win">{{ $L('Winning Numbers') }}</span>
        </div>

        <!-- Row 3: Results -->
        <div class="ds-results-row">
            <!-- English Letter -->
            @if($draw->english_letters)
                <div class="ds-ball-group col-eng">
                    @foreach(explode(',', $draw->english_letters) as $letter)
                        <div class="ds-ball circle-white letter-ball">{{ strtoupper(ltrim(trim($letter), '0')) }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Winning Numbers -->
            <div class="ds-ball-group col-win">
                @foreach($draw->numbers as $number)
                    <div class="ds-ball circle-white">{{ (int) $number }}</div>
                @endforeach
            </div>
        </div>

        <!-- Row 4: Jackpot -->
        @if($draw->next_jackpot)
            <div class="ds-jackpot-row">
                <div class="ds-jackpot-pill">
                    {{ $L('Next Jackpot') }} : Rs. {{ number_format($draw->next_jackpot, 2) }}
                </div>
            </div>
        @endif
    </div>
</div>