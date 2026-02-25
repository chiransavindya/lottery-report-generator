@php
    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };
@endphp

<div class="lottery-banner-container kp-banner">
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/kapruka_all.png') }}" alt="Kapruka Banner"
        class="lottery-banner-image">

    <div class="kp-data-overlay">
        <!-- Row 1: Draw Info -->
        <div class="kp-info-row">
            <div class="kp-info-box">
                <span class="kp-info-label">{{ $L('Draw Number') }}</span>
                <span class="kp-info-val">{{ $draw->draw_number }}</span>
            </div>
            @if($draw->color)
                <div class="kp-info-box">
                    <span class="kp-info-label">{{ $L('Colour') }}</span>
                    <span class="kp-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                </div>
            @endif
        </div>

        <!-- Row 2: Labels -->
        <div class="kp-labels-row">
            @if($draw->english_letters)<span class="kp-lbl col-eng">{{ $L('English Letter') }}</span>@endif
            @if($draw->super_number)<span class="kp-lbl col-sup">{{ $L('Super Number') }}</span>@endif
            <span class="kp-lbl col-win">{{ $L('Winning Numbers') }}</span>
        </div>

        <!-- Row 3: Balls -->
        <div class="kp-balls-row">
            <!-- English Letter -->
            @if($draw->english_letters)
                <div class="kp-ball-group col-eng">
                    @foreach(explode(',', $draw->english_letters) as $letter)
                        <div class="kp-ball circle-white">{{ strtoupper(ltrim(trim($letter), '0')) }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Super Number -->
            @if($draw->super_number)
                <div class="kp-ball-group col-sup">
                    <div class="kp-ball circle-white">{{ str_pad($draw->super_number, 2, '0', STR_PAD_LEFT) }}</div>
                </div>
            @endif

            <!-- Winning Numbers -->
            <div class="kp-ball-group col-win">
                @foreach($draw->numbers as $number)
                    <div class="kp-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                @endforeach
            </div>
        </div>

        <!-- Row 4: Jackpot -->
        @if($draw->next_jackpot)
            <div class="kp-jackpot-row">
                <div class="kp-jackpot-pill">
                    {{ $L('Next Jackpot') }} : Rs. {{ number_format($draw->next_jackpot, 2) }}
                </div>
            </div>
        @endif
    </div>
</div>