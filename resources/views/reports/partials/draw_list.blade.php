@if($draws->count() > 0)
    <div style="padding: 20px;">
        @php
            // Sort draws according to lottery order
            $currentLotteryOrder = $lotteryOrder ?? ['KP', 'LW', 'AK', 'SF', 'SB', 'SR', 'JS', 'DS'];

            $sortedDraws = $draws->sortBy(function ($draw) use ($currentLotteryOrder) {
                $code = $draw->lotteryType->code;
                $position = array_search($code, $currentLotteryOrder);
                return $position !== false ? $position : 999;
            });

            // Default English labels only
            $labels = [
                'Winning Numbers' => 'WINNING NUMBERS',
                'English Letter' => 'ENGLISH LETTER',
                'Super Number' => 'SUPER NUMBER',
                'Zodiac' => 'ZODIAC (LAGNA)',
                'Next Jackpot' => 'NEXT SUPER JACKPOT',
                'Total Value' => 'TOTAL VALUE OF PRIZES',
                'Winners' => 'TOTAL NO. OF Rs.200,000 WINNERS',
                'Prize' => 'Prize',
                'Special No' => 'Special No.'
            ];
        @endphp

        <!-- Download Buttons (If reports exist) -->
        @if($hasReports)
            @php
                $report = $existingReports[$drawDate]->first();
            @endphp
            <div style="display: flex; gap: 10px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px dashed var(--border-color); flex-wrap: wrap;">
                <a href="{{ route('reports.show', $report) }}" class="btn btn-primary" style="background: #8b5cf6;">View Full Report</a>
                <a href="{{ route('reports.download.zip', $report) }}" class="btn btn-primary" style="background: #667eea;">Download All (ZIP)</a>
                <a href="{{ route('reports.download', [$report, 'en']) }}" class="btn btn-primary" style="background: #3b82f6;" target="_blank">English PDF</a>
                <a href="{{ route('reports.download', [$report, 'si']) }}" class="btn btn-primary" style="background: #10b981;" target="_blank">Sinhala PDF</a>
                <a href="{{ route('reports.download', [$report, 'ta']) }}" class="btn btn-primary" style="background: #f59e0b;" target="_blank">Tamil PDF</a>
            </div>
        @endif

        @php
            $colorMap = [
                'red' => '#dc2626',
                'blue' => '#2563eb',
                'green' => '#16a34a',
                'light green' => '#4ade80',
                'dark green' => '#15803d',
                'yellow' => '#eab308',
                'orange' => '#f97316',
                'purple' => '#9333ea',
                'pink' => '#ec4899',
                'brown' => '#92400e',
                'black' => '#1f2937',
                'white' => '#f3f4f6',
                'gray' => '#6b7280',
                'grey' => '#6b7280',
                'cyan' => '#06b6d4',
                'lime' => '#84cc16',
                'indigo' => '#6366f1',
                'violet' => '#8b5cf6',
                'fuchsia' => '#d946ef',
                'rose' => '#f43f5e',
                'sky' => '#0ea5e9',
                'teal' => '#14b8a6',
                'emerald' => '#10b981',
                'amber' => '#f59e0b',
                'light blue' => '#38bdf8',
            ];
        @endphp

        <!-- Single Language Display (English only) -->
        @foreach($sortedDraws as $draw)
            @php
                $colorKey = strtolower(trim($draw->color ?? ''));
                $hexColor = $colorMap[$colorKey] ?? '#667eea';
                $lotteryCode = $draw->lotteryType->code;

                // Custom Field Ordering Logic
                $layoutOrders = [
                    'KP' => ['eng' => 1, 'super' => 2, 'win' => 3, 'jackpot' => 4],
                    'LW' => ['win' => 1, 'zodiac' => 2, 'jackpot' => 3],
                    'AK' => ['win' => 1, 'eng' => 2, 'jackpot' => 3],
                    'SF' => ['win' => 1, 'eng' => 2, 'jackpot' => 3],
                    'SB' => ['win' => 1, 'eng' => 2, 'jackpot' => 3],
                    'SR' => ['win' => 1, 'total_val' => 2, 'winner_count' => 3],
                    'JS' => ['eng' => 1, 'win' => 2, 'total_val' => 3],
                    'DS' => ['eng' => 1, 'win' => 2, 'jackpot' => 3],
                ];

                $myOrder = $layoutOrders[$lotteryCode] ?? [];

                $getOrder = function ($key) use ($myOrder) {
                    return $myOrder[$key] ?? 10;
                };
            @endphp

            <div class="card" style="margin-bottom: 15px; border-left: 6px solid {{ $hexColor }}; padding: 20px;">
                <!-- Lottery Header -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div>
                        <h4 style="margin: 0; font-size: 18px; font-weight: 700; color: var(--text-color);">
                            {{ ucwords(strtolower($draw->lotteryType->name_en)) }}
                        </h4>
                                <p style="margin: 5px 0 0 0; color: var(--text-light); font-size: 13px;">
                                    Draw #{{ $draw->draw_number }}
                                    @if($draw->color)
                                        • <span style="display: inline-block; width: 12px; height: 12px; background: {{ $hexColor }}; border-radius: 50%; margin-left: 5px;"></span>
                                        {{ ucfirst($draw->color) }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Results -->
                        <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: start;">
                            <!-- Winning Numbers -->
                            <div style="order: {{ $getOrder('win') }};">
                                <label style="font-size: 11px; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">{{ $labels['Winning Numbers'] }}</label>
                                <div style="display: flex; gap: 8px;">
                                    @foreach($draw->numbers as $number)
                                        @if(!preg_match('/^[A-Za-z]+$/', $number))
                                            @php
                                                $displayNumber = str_pad($number, 2, '0', STR_PAD_LEFT);
                                                if (in_array($lotteryCode, ['JS', 'DS'])) {
                                                    $displayNumber = (int) $number;
                                                }
                                            @endphp
                                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, {{ $hexColor }} 0%, {{ $hexColor }}cc 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                                {{ $displayNumber }}
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- English Letter -->
                            @if(in_array($lotteryCode, ['KP', 'AK', 'SF', 'SB', 'JS', 'DS']) && $draw->english_letters)
                                <div style="order: {{ $getOrder('eng') }};">
                                    <label style="font-size: 11px; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">{{ $labels['English Letter'] }}</label>
                                    <div style="display: flex; gap: 6px;">
                                        @foreach(explode(',', $draw->english_letters) as $letter)
                                            @php $cleanLetter = ltrim(trim($letter), '0'); @endphp
                                            <div style="width: 45px; height: 45px; background: #10b981; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 20px; box-shadow: 0 3px 8px rgba(16, 185, 129, 0.4);">
                                                {{ strtoupper($cleanLetter) }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Super Number -->
                            @if($lotteryCode == 'KP' && $draw->super_number)
                                <div style="order: {{ $getOrder('super') }};">
                                    <label style="font-size: 11px; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">{{ $labels['Super Number'] }}</label>
                                    <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 16px; box-shadow: 0 3px 8px rgba(245, 158, 11, 0.4);">
                                        {{ str_pad($draw->super_number, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                            @endif

                            <!-- Zodiac -->
                            @if($lotteryCode == 'LW' && $draw->zodiac_sign)
                                <div style="order: {{ $getOrder('zodiac') }};">
                                    <label style="font-size: 11px; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">{{ $labels['Zodiac'] }}</label>
                                    <div style="display: flex; align-items: center; gap: 10px; background: white; padding: 8px 15px; border-radius: 10px; border: 2px solid {{ $hexColor }};">
                                        @php $zodiacImage = 'images/zodiac_images/' . strtolower(str_replace(' ', '_', $draw->zodiac_sign)) . '.png'; @endphp
                                        @if(file_exists(public_path($zodiacImage)))
                                            <img src="{{ asset($zodiacImage) }}" alt="{{ $draw->zodiac_sign }}" style="width: 25px; height: 25px; object-fit: contain;">
                                        @else
                                            <span style="font-size: 20px;">♈</span>
                                        @endif
                                        <span style="font-weight: 700; color: {{ $hexColor }}; font-size: 14px;">{{ ucfirst(strtolower($draw->zodiac_sign)) }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Next Super Jackpot -->
                            @if(in_array($lotteryCode, ['KP', 'LW', 'AK', 'SF', 'SB', 'DS']) && $draw->next_jackpot)
                                <div style="order: {{ $getOrder('jackpot') }};">
                                    <label style="font-size: 11px; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">{{ $labels['Next Jackpot'] }}</label>
                                    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 4px solid #f59e0b; padding: 10px 15px; border-radius: 8px;">
                                        <div style="font-size: 16px; font-weight: 900; color: #78350f;">{{ $language === 'ta' ? 'ரூ.' : 'Rs.' }} {{ number_format($draw->next_jackpot, 2) }}</div>
                                    </div>
                                </div>
                            @endif

                            <!-- Total value of prizes -->
                            @if(in_array($lotteryCode, ['SR', 'JS']) && ($draw->total_prize_value || $draw->total_sales))
                                <div style="order: {{ $getOrder('total_val') }};">
                                    <label style="font-size: 11px; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">{{ $labels['Total Value'] }}</label>
                                    <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-left: 4px solid #3b82f6; padding: 10px 15px; border-radius: 8px;">
                                        <div style="font-size: 16px; font-weight: 900; color: #1e40af;">{{ $language === 'ta' ? 'ரூ.' : 'Rs.' }} {{ number_format($draw->total_prize_value ?? $draw->total_sales, 2) }}</div>
                                    </div>
                                </div>
                            @endif

                            <!-- Winners Count (SR) -->
                            @if($lotteryCode == 'SR' && $draw->prize_breakdown)
                                @php
                                    $winnersCount = 0;
                                    foreach ($draw->prize_breakdown as $prize) {
                                        $val = (int) ($prize['amount'] ?? $prize['value'] ?? 0);
                                        if ($val == 200000) {
                                            $winnersCount = $prize['count'] ?? $prize['winners'] ?? 0;
                                            break;
                                        }
                                    }
                                @endphp
                                @if($winnersCount > 0)
                                    <div style="order: {{ $getOrder('winner_count') }};">
                                        <label style="font-size: 11px; color: var(--text-light); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">{{ $labels['Winners'] }}</label>
                                        <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-left: 4px solid #10b981; padding: 10px 15px; border-radius: 8px;">
                                            <div style="font-size: 16px; font-weight: 900; color: #064e3b;">{{ str_pad($winnersCount, 2, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Prize Table (Classic B&W Style) -->
                        @if(in_array($lotteryCode, ['SF', 'SB']) && $draw->prize_breakdown && count($draw->prize_breakdown) > 0)
                            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid var(--border-color);">
                                @php
                                    $prizeRows = [];
                                    if ($lotteryCode == 'SF') { $targetAmounts = [40, 200]; } else { $targetAmounts = [200, 40]; }
                                    foreach ($targetAmounts as $targetAmount) {
                                        foreach ($draw->prize_breakdown as $prize) {
                                            $val = (int) ($prize['amount'] ?? $prize['value'] ?? 0);
                                            if ($val == $targetAmount) { $prizeRows[] = $prize; break; }
                                        }
                                    }
                                @endphp
                                @if(count($prizeRows) > 0)
                                    <table style="width: 100%; max-width: 300px; border-collapse: collapse; background: white; border: 2px solid black; margin-top: 10px;">
                                        <thead>
                                            <tr style="border-bottom: 2px solid black;">
                                                <th style="padding: 8px; text-align: left; font-size: 13px; font-weight: 800; color: black; border-right: 2px solid black; background: #f3f4f6;">{{ $labels['Prize'] }}</th>
                                                <th style="padding: 8px; text-align: center; font-size: 13px; font-weight: 800; color: black; background: #f3f4f6;">{{ $labels['Special No'] }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($prizeRows as $prize)
                                                @php
                                                    $amt = (int) ($prize['amount'] ?? $prize['value'] ?? 0);
                                                $metaKey = "SP_{$amt}_NO";
                                                $specialNo = $draw->metadata[$metaKey] ?? $prize['code'] ?? '-';
                                            @endphp
                                            <tr style="border-bottom: 2px solid black;">
                                                <td style="padding: 8px; font-weight: 700; color: black; font-size: 14px; border-right: 2px solid black;">{{ $language === 'ta' ? 'ரூ.' : 'Rs.' }} {{ number_format($amt) }}/-</td>
                                                <td style="padding: 8px; text-align: center; font-weight: 700; color: black; font-size: 14px;">{{ $specialNo }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @endif
                </div>
        @endforeach
    </div>
@else
    <div style="padding: 40px; text-align: center; color: var(--text-light);">
        No lottery data available for this date.
    </div>
@endif
