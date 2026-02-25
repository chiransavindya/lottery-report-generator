<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $lottery->name_ta }} - வெற்றி {{ $draw->draw_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'FreeSerif', 'Noto Sans Tamil', sans-serif;
            width: 10.8cm;
            margin: 0 auto;
        }

        .ticket-container {
            width: 10.8cm;
            height: auto;
            max-height: 2.4cm;
        }

        .ticket-header {
            position: relative;
        }

        .ticket-logo {
            width: 10.8cm;
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
            $logoFile = "{$lotteryCode}_ta.png";
        }

        // Color translation to Tamil
        $colorTranslations = [
            'Green' => 'பச்சை',
            'Red' => 'சிவப்பு',
            'Blue' => 'நீலம்',
            'Yellow' => 'மஞ்சள்',
            'Purple' => 'ஊதா',
            'Orange' => 'செம்மஞ்சள்',
            'Pink' => 'இளஞ்சிவப்பு',
            'Light Blue' => 'இள நீலம்',
            'Light Pink' => 'வெளிர் இளஞ்சிவப்பு',
            'Light Green' => 'இளம் பச்சை',
            'Dark Green' => 'கரும் பச்சை',
            'Dark Blue' => 'கரும் நீலம்',
            'Brown' => 'கபிலம்',
            'Light Brown' => 'இளம் கபிலம்',
            'Dark Brown' => 'கரும் கபிலம்',
            'White' => 'வெள்ளை',
            'Black' => 'கருப்பு'
        ];

        $colorTa = isset($draw->metadata['color']) ? ($colorTranslations[$draw->metadata['color']] ?? $draw->metadata['color']) : null;
    @endphp

    <div class="ticket-container">
        <div class="ticket-header">
            <img src="{{ public_path("images/{$logoFile}") }}" alt="{{ $lottery->name_ta }}" class="ticket-logo">

            <div class="draw-info-overlay">
                <span class="draw-label">சீட்டிழுப்பு இலக்கம்</span>
                <span class="draw-value">{{ $draw->draw_number }}</span>

                @if($colorTa)
                    <span class="color-label">நிறம்</span>
                    <span class="color-value">{{ $colorTa }}</span>
                @endif

                <div class="winning-section">
                    <div class="winning-title">
                        ----வெற்றி இலக்கங்கள்----&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ஆங்கில எழுத்து
                    </div>
                    <div class="balls-container">
                        @foreach($draw->numbers as $number)
                            <div class="ball">{{ $number }}</div>
                        @endforeach
                    </div>
                </div>

                @if(isset($draw->metadata['next_jackpot']))
                    <div class="jackpot-bar">
                        அடுத்த சுப்பர் பரிசுப்பொதி : ரூ. {{ number_format($draw->metadata['next_jackpot'], 2) }}
                    </div>
                @elseif(isset($draw->jackpot_amount) && $draw->jackpot_amount > 0)
                    <div class="jackpot-bar">
                        ஜாக்பாட் : ரூ. {{ number_format($draw->jackpot_amount, 2) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>

</html>