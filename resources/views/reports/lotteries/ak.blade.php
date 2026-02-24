@php
    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };
@endphp

<div class="lottery-banner-container ak-banner">
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/adakotipathi_all.png') }}" alt="Ada Kotipathi Banner"
        class="lottery-banner-image">

    <div class="ak-data-overlay">
        <!-- Row 1: Draw Info -->
        <div class="ak-info-row">
            <div class="ak-info-box">
                <span class="ak-info-label">{{ $L('Draw Number') }}</span>
                <span class="ak-info-val">{{ $draw->draw_number }}</span>
            </div>
            @if($draw->color)
                <div class="ak-info-box">
                    <span class="ak-info-label">{{ $L('Colour') }}</span>
                    <span class="ak-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                </div>
            @endif
        </div>

        <!-- Row 2: Labels -->
        <div class="ak-labels-row">
            @if($draw->english_letters)<span class="ak-lbl col-eng">{{ $L('English Letter') }}</span>@endif
            <span class="ak-lbl col-win">{{ $L('Winning Numbers') }}</span>
        </div>

        <!-- Row 3: Balls -->
        <div class="ak-results-row">
            <!-- English Letter -->
            @if($draw->english_letters)
                <div class="ak-ball-group col-eng">
                    @foreach(explode(',', $draw->english_letters) as $letter)
                        <div class="ak-ball circle-white">{{ strtoupper(ltrim(trim($letter), '0')) }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Winning Numbers -->
            <div class="ak-ball-group col-win">
                @foreach($draw->numbers as $number)
                    <div class="ak-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                @endforeach
            </div>
        </div>

        <!-- Row 4: Jackpot -->
        @if($draw->next_jackpot)
            <div class="ak-jackpot-row">
                <div class="ak-jackpot-pill">
                    {{ $L('Next Jackpot') }} : Rs. {{ number_format($draw->next_jackpot, 2) }}
                </div>
            </div>
        @endif
    </div>
</div>
