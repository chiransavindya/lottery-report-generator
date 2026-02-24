@php
    $lang = $language ?? 'en';
    $srImageMap = [
        'en' => 'sasiri_english.png',
        'si' => 'sasiri_sinhala.png',
        'ta' => 'sasiri_tamil.png'
    ];
    $srImage = $srImageMap[$lang] ?? 'sasiri_english.png';

    $L = function ($key) use ($labels) {
        return mb_convert_case($labels[$key] ?? $key, MB_CASE_TITLE, "UTF-8");
    };

    // Calculate Winners Count for Sasiri (200,000 winners)
    $winnersCount = 0;
    if ($draw->prize_breakdown) {
        foreach ($draw->prize_breakdown as $prize) {
            $val = (int) ($prize['amount'] ?? $prize['value'] ?? 0);
            if ($val == 200000) {
                $winnersCount = $prize['count'] ?? $prize['winners'] ?? 0;
                break;
            }
        }
    }
@endphp

<div class="lottery-banner-container sr-banner">
    <img src="{{ asset('images/pdf_static_images/lottery_bg_images/' . $srImage) }}" alt="Sasiri Banner"
        class="lottery-banner-image">

    <div class="sr-data-overlay">
        <!-- Main Content Area -->
        <div class="sr-main-content">
            <!-- Row 1: Draw Info & Color -->
            <div class="sr-info-row">
                <div class="sr-info-box">
                    <span class="sr-info-label">{{ $L('Draw Number') }}</span>
                    <span class="sr-info-val">{{ $draw->draw_number }}</span>
                </div>
                @if($draw->color)
                    <div class="sr-info-box">
                        <span class="sr-info-label">{{ $L('Colour') }}</span>
                        <span class="sr-info-val">{{ $L(ucfirst(strtolower(trim($draw->color)))) }}</span>
                    </div>
                @endif
            </div>

            <!-- Row 2: Winning Numbers Label & Winners Table (Side by Side potentially) -->
            <div class="sr-middle-row">
                <!-- Winning Numbers Section -->
                <div class="sr-winning-section">
                    <div class="sr-winning-label"> {{ $L('Winning Numbers') }}
                    </div>
                    <div class="sr-ball-group">
                        @foreach($draw->numbers as $number)
                            <div class="sr-ball circle-white">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</div>
                        @endforeach
                    </div>
                </div>

                <!-- Winners Stats Box (Right Side) -->
                @if($winnersCount > 0)
                    <div class="sr-winners-box">
                        <span class="sr-winners-label">
                            {{ $L('Winners') }}
                        </span>
                        <span class="sr-winners-val">{{ str_pad($winnersCount, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                @endif
            </div>

            <!-- Row 3: Total Prize Value Pills -->
            @if($draw->total_prize_value || $draw->total_sales)
                <div class="sr-jackpot-row">
                    <div class="sr-jackpot-pill">
                        {{ $L('Total Value') }} : Rs.
                        {{ number_format($draw->total_prize_value ?? $draw->total_sales, 2) }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>