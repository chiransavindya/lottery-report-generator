<div class="lottery-banner-container">
    <div style="padding: 20px; border: 1px solid #ccc; margin-bottom: 20px; background: #f9f9f9;">
        <h3>{{ $draw->lotteryType->name ?? 'Unknown Lottery' }} ({{ $draw->lotteryType->code }})</h3>
        <p><strong>Draw Number:</strong> {{ $draw->draw_number }}</p>
        <p><strong>Date:</strong> {{ $draw->draw_date }}</p>
        
        @if($draw->numbers)
            <p><strong>Winning Numbers:</strong> 
                @foreach($draw->numbers as $number)
                    <span style="display:inline-block; padding: 5px 10px; background: #eee; border-radius: 50%; margin-right: 5px;">
                        {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}
                    </span>
                @endforeach
            </p>
        @endif

        @if($draw->english_letters)
            <p><strong>English Letter:</strong> {{ $draw->english_letters }}</p>
        @endif
        
        @if($draw->next_jackpot)
            <p><strong>Next Jackpot:</strong> {{ $lang === 'ta' ? 'ரூ.' : 'Rs.' }} {{ number_format($draw->next_jackpot, 2) }}</p>
        @endif

        <p style="color: red; font-size: 0.8em; margin-top: 10px;">
            <em>Specific report template [reports.lotteries.{{ strtolower($draw->lotteryType->code) }}] not found. Using common fallback.</em>
        </p>
    </div>
</div>
