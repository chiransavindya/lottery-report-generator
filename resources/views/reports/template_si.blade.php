<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $lottery->name_si }} - ඇදීම {{ $draw->draw_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'FreeSerif', 'Noto Sans Sinhala', sans-serif;
            width: 9.3cm;
            margin: 0 auto;
        }

        .ticket-container {
            width: 9.3cm;
            height: auto;
            max-height: 2.4cm;
        }

        .ticket-header {
            position: relative;
        }

        .ticket-logo {
            width: 9.3cm;
            height: 2.4cm;
        }

        .draw-info-overlay {
            position: absolute;
            top: 0.05cm;
            right: 0.1cm;
            left: 4.1cm;
        }

        .draw-label {
            background-color: #b4b4b4;
            padding: 0.04cm;
            display: inline-block;
            color: #000;
            font-weight: 550;
            font-size: 0.26cm;
            font-family: 'Times New Roman', serif;
        }

        .draw-value {
            background-color: #eceef1;
            padding: 0.04cm 0.2cm;
            display: inline-block;
            color: #000;
            font-weight: 550;
            font-size: 0.26cm;
            font-family: 'Times New Roman', serif;
            border-left: 0.03cm solid #000;
        }

        .color-label {
            background-color: #b4b4b4;
            padding: 0.04cm;
            display: inline-block;
            color: #000;
            font-weight: 550;
            font-size: 0.26cm;
            font-family: 'Times New Roman', serif;
            margin-left: 0.2cm;
        }

        .color-value {
            background-color: #eceef1;
            padding: 0.04cm 0.2cm;
            display: inline-block;
            color: #000;
            font-weight: 550;
            font-size: 0.26cm;
            font-family: 'Times New Roman', serif;
            border-left: 0.03cm solid #000;
        }

        .winning-section {
            margin-top: 0.03cm;
            text-align: center;
        }

        .winning-title {
            color: #fff;
            font-size: 0.23cm;
            font-weight: 550;
            margin-bottom: 0.03cm;
        }

        .balls-container {
            text-align: center;
            margin-top: 0.1cm;
        }

        .ball {
            width: 0.65cm;
            height: 0.65cm;
            background-color: #fff;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            line-height: 0.65cm;
            margin: 0 0.05cm;
            font-size: 0.47cm;
            font-weight: 550;
            color: #000;
        }

        .ball:nth-child(5) {
            margin-left: 0.4cm;
        }

        .jackpot-bar {
            margin-top: 0.2cm;
            margin-left: 0cm;
            width: 100%;
            height: 0.55cm;
            background-color: #3b4453;
            border-radius: 0.1cm;
            padding: 0.05cm;
            color: #fff;
            font-size: 0.30cm;
            font-weight: 550;
            font-family: 'Times New Roman', serif;
            text-align: center;
            line-height: 0.45cm;
        }
    </style>
</head>

<body>
    @php
        $lotteryCode = strtolower($lottery->code);
        $logoFile = "{$lotteryCode}.png";

        // Handle language-specific logos
        $langSpecificLogos = ['ds', 'lw', 'sr'];
        if (in_array($lotteryCode, $langSpecificLogos)) {
            $logoFile = "{$lotteryCode}_si.png";
        }

        // Color translation to Sinhala
        $colorTranslations = [
            'Green' => 'කොළ',
            'Red' => 'රතු',
            'Blue' => 'නිල්',
            'Yellow' => 'කහ',
            'Purple' => 'දම්',
            'Orange' => 'තැඹිලි',
            'Pink' => 'රෝස',
            'White' => 'සුදු',
            'Black' => 'කළු',
            'Brown' => 'දුඹුරු',
            'Light Blue' => 'ලා නිල්',
            'Light Pink' => 'ලා රෝස',
            'Light Green' => 'ලා කොළ',
            'Dark Green' => 'තද කොළ',
            'Dark Blue' => 'තද නිල්',
            'Light Brown' => 'ලා දුඹුරු',
            'Dark Brown' => 'තද දුඹුරු'
        ];

        $colorSi = isset($draw->metadata['color']) ? ($colorTranslations[$draw->metadata['color']] ?? $draw->metadata['color']) : null;
    @endphp

    <div class="ticket-container">
        <div class="ticket-header">
            <img src="{{ public_path("images/{$logoFile}") }}" alt="{{ $lottery->name_si }}" class="ticket-logo">

            <div class="draw-info-overlay">
                <span class="draw-label">ඇදීමේ අංකය</span>
                <span class="draw-value">{{ $draw->draw_number }}</span>

                @if($colorSi)
                    <span class="color-label">වර්ණය</span>
                    <span class="color-value">{{ $colorSi }}</span>
                @endif

                <div class="winning-section">
                    <div class="winning-title">
                        ----ජයග්‍රාහී අංක----&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ඉංග්‍රීසි අකුර
                    </div>
                    <div class="balls-container">
                        @foreach($draw->numbers as $number)
                            <div class="ball">{{ $number }}</div>
                        @endforeach
                    </div>
                </div>

                @if(isset($draw->metadata['next_jackpot']))
                    <div class="jackpot-bar">
                        මීළඟ සුපිරි ජැක්පොට් : රු. {{ number_format($draw->metadata['next_jackpot'], 2) }}
                    </div>
                @elseif(isset($draw->jackpot_amount) && $draw->jackpot_amount > 0)
                    <div class="jackpot-bar">
                        ජැක්පොට් : රු. {{ number_format($draw->jackpot_amount, 2) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>

</html>