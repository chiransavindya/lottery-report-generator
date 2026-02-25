@php
    $lang = $language ?? 'en';
    $lwImageMap = [
        'en' => 'lagna_english.png',
        'si' => 'lagna_sinhala.png',
        'ta' => 'lagna_tamil.png'
    ];
    $lwImage = $lwImageMap[$lang] ?? 'lagna_english.png';

    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };
@endphp

<div class="lottery-banner-container lw-banner">
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/' . $lwImage) }}" alt="Lagnawasana Banner"
        class="lottery-banner-image">

    <div class="lw-data-overlay">
        <!-- Row 1: Draw Info -->
        <div class="lw-info-row">
            <div class="lw-info-box">
                <span class="lw-info-label">{{ $L('Draw Number') }}</span>
                <span class="lw-info-val">{{ $draw->draw_number }}</span>
            </div>
            @if($draw->color)
                <div class="lw-info-box">
                    <span class="lw-info-label">{{ $L('Colour') }}</span>
                    <span class="lw-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                </div>
            @endif
        </div>

        <!-- Row 2: Winning Numbers Label -->
        <div class="lw-labels-row">
            <span class="lw-lbl col-win">{{ $L('Winning Numbers') }}</span>
        </div>

        <!-- Row 3: Winning Numbers & Zodiac -->
        <div class="lw-results-row">
            <!-- Winning Numbers -->
            <div class="lw-ball-group">
                @foreach($draw->numbers as $number)
                    <div class="lw-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                @endforeach
            </div>

            <!-- Zodiac -->
            @if($draw->zodiac_sign)
                <div class="lw-zodiac-box">
                    @php
                        $zodiacSlug = strtolower(str_replace(' ', '_', $draw->zodiac_sign));
                        $zodiacImagePath = public_path("images/zodiac_images/{$zodiacSlug}.png");
                    @endphp
                    @if(file_exists($zodiacImagePath))
                        <img src="{{ asset("images/zodiac_images/{$zodiacSlug}.png") }}" alt="{{ ucfirst($draw->zodiac_sign) }}"
                            class="lw-zodiac-icon">
                    @endif
                    <div class="lw-zodiac-name">
                        {{ $L(ucfirst(strtolower(trim($draw->zodiac_sign)))) }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Row 4: Jackpot -->
        @if($draw->next_jackpot)
            <div class="lw-jackpot-row">
                <div class="lw-jackpot-pill">
                    {{ $L('Next Jackpot') }} : Rs. {{ number_format($draw->next_jackpot, 2) }}
                </div>
            </div>
        @endif
    </div>
</div>