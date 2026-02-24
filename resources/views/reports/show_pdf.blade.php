<!DOCTYPE html>
<html lang="{{ $language ?? 'en' }}">

<head>
    <meta charset="UTF-8">
    <title>Daily Report - {{ $date }} | LRMS</title>
    <link rel="icon" href="{{ asset('images/logo/logo.png') }}">

    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/report-view.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/report-header.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/report-accordion.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/pdf-buttons.css') }}?v={{ time() }}">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Lottery Specific CSS -->
    <link rel="stylesheet" href="{{ asset('css/report-kapruka.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/report-lagnawasana.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/report-adakotipathi.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/report-shanida.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/report-superball.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/report-sasiri.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/report-jayasampatha.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/report-supiridhana.css') }}?v={{ time() }}">

    <!-- Vite Assets for PDF Generation -->
    @vite(['resources/js/app.js'])

    <style>
        /* PDF Download Button Styles */
        .pdf-download-btn {
            transition: all 0.3s ease;
            position: relative;
        }

        .pdf-download-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .pdf-download-btn.generating {
            background: #6b7280 !important;
        }

        .spinner {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid #ffffff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .page-break {
            page-break-after: always;
            break-after: page;
        }

        .page-break:last-child {
            page-break-after: auto;
            break-after: auto;
        }

        /* For merged PDF - ensure all content is visible */
        .merged-pdf .tab-pane {
            display: block !important;
            margin: 0;
            padding: 0;
        }

        .merged-pdf .a4-container {
            margin: 0 auto;
            box-shadow: none;
        }

        /* Professional Enhancements */
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            background: #fff;
            padding: 0.75rem 1.5rem;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            background: #f3f4f6;
        }

        .back-btn:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .chrome-tab {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            background: #f3f4f6;
            color: #6b7280;
            transition: all 0.2s;
            font-weight: 500;
            border: 1px solid transparent;
        }

        .chrome-tab:hover {
            background: #e5e7eb;
            color: #374151;
        }

        .chrome-tab.active {
            background: #fff;
            color: #2563eb;
            border-color: #e5e7eb;
            border-bottom-color: #fff;
            font-weight: 600;
            box-shadow: 0 -1px 2px rgba(0,0,0,0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            letter-spacing: 0.01em;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        .btn i {
            font-size: 1.1em;
        }

        .tabs-header-bar {
            border-bottom: 1px solid #e5e7eb;
            padding: 0 1rem;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .chrome-tabs {
            display: flex;
            gap: 4px;
        }
    </style>

    <!-- Hot Reload for Live Editing -->
    <!-- <script>
        // Auto-refresh every 2 seconds for live editing
        let lastModified = Date.now();
        let reloadInterval = setInterval(() => {
            fetch(window.location.href, { method: 'HEAD' })
                .then(() => {
                    if (Date.now() - lastModified > 2000) {
                        window.location.reload();
                    }
                })
                .catch(() => { });
        }, 2000);

        document.addEventListener('click', (e) => {
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                clearInterval(reloadInterval);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'r' || e.key === 'R') {
                e.preventDefault();
                window.location.reload();
            }
        });
    </script> -->
</head>

<body>
    <div class="main-wrapper {{ isset($isMerged) && $isMerged ? 'merged-pdf' : '' }}" data-report-date="{{ $date }}" data-current-language="{{ $language ?? 'en' }}">
        @if(isset($report))
            @php
                $languageInfo = [
                    'en' => ['name' => 'English', 'icon' => 'fa-flag-usa', 'color' => '#3b82f6'],
                    'si' => ['name' => 'Sinhala', 'icon' => 'fa-circle-dot', 'color' => '#10b981'],
                    'ta' => ['name' => 'Tamil', 'icon' => 'fa-circle-dot', 'color' => '#f59e0b'],
                ];

                $languageConfigs = [
                    'en' => [
                        'labels' => [
                            'Winning Numbers' => 'WINNING NUMBERS',
                            'English Letter' => 'ENGLISH LETTER',
                            'Super Number' => 'SUPER NUMBER',
                            'Zodiac' => 'ZODIAC (LAGNA)',
                            'Next Jackpot' => 'NEXT SUPER JACKPOT',
                            'Total Value' => 'TOTAL VALUE OF PRIZES',
                            'Winners' => 'TOTAL NO. OF Rs.200,000 WINNERS',
                            'Prize' => 'Prize',
                            'Special No' => 'Special No.',
                            'Draw Number' => 'Draw Number',
                            'Colour' => 'Colour'
                        ],
                    ],
                    'si' => [
                        'labels' => [
                            'Winning Numbers' => 'ජයග්‍රාහී අංක',
                            'English Letter' => 'ඉංග්‍රීසි අක්ෂරය',
                            'Super Number' => 'සුපිරි අංකය',
                            'Zodiac' => 'ලග්නය',
                            'Next Jackpot' => 'මීළඟ සුපිරි ජයමල්ල',
                            'Total Value' => 'දිනා ඇති මුළු මුදල',
                            'Total Prize Value' => 'ත්‍යාගවල මුළු වටිනාකම',
                            'Winners' => 'අද බිහි වු දෙලක්ෂපතියන් ගණන',
                            'Prize' => 'ත්‍යාගය',
                            'Amount' => 'මුදල',
                            'Special No' => 'විශේෂ අංකය',
                            'Draw Number' => 'දිනුම් වාරය',
                            'Colour' => 'වර්ණය',
                            'Green' => 'කොළ',
                            'Yellow' => 'කහ',
                            'Red' => 'රතු',
                            'Blue' => 'නිල්',
                            'Orange' => 'තැඹිලි',
                            'Purple' => 'දම්',
                            'Pink' => 'රෝස',
                            'Light Blue' => 'ලා නිල්',
                            'Light blue' => 'ලා නිල්',
                            'Light Pink' => 'ලා රෝස',
                            'Light pink' => 'ලා රෝස',
                            'Light Green' => 'ලා කොළ',
                            'Light green' => 'ලා කොළ',
                            'Dark Green' => 'තද කොළ',
                            'Dark green' => 'තද කොළ',
                            'Dark Blue' => 'තද නිල්',
                            'Dark blue' => 'තද නිල්',
                            'Brown' => 'දුඹුරු',
                            'Light Brown' => 'ලා දුඹුරු',
                            'Light brown' => 'ලා දුඹුරු',
                            'Dark Brown' => 'තද දුඹුරු',
                            'Dark brown' => 'තද දුඹුරු',
                            'Aries' => 'මේෂ',
                            'Taurus' => 'වෘෂභ',
                            'Gemini' => 'මිථුන',
                            'Cancer' => 'කටක',
                            'Leo' => 'සිංහ',
                            'Virgo' => 'කන්‍යා',
                            'Libra' => 'තුලා',
                            'Scorpio' => 'වෘශ්චික',
                            'Sagittarius' => 'ධනු',
                            'Capricorn' => 'මකර',
                            'Aquarius' => 'කුම්භ',
                            'Pisces' => 'මීන'
                        ],
                    ],
                    'ta' => [
                        'labels' => [
                            'Winning Numbers' => 'வெற்றி இலக்கங்கள்',
                            'English Letter' => 'ஆங்கில எழுத்து',
                            'Super Number' => 'சுப்பர் இலக்கம்',
                            'Zodiac' => 'இராசி',
                            'Next Jackpot' => 'அடுத்த சுப்பர் பரிசுப்பொதி',
                            'Total Value' => 'பரிசுகளின் மொத்த பெறுமதி',
                            'Winners' => 'ரூ. 200,000 வெற்றியாளர்களின் மொத்த எண்ணிக்கை',
                            'Prize' => 'பரிசு',
                            'Special No' => 'விசேட இலக்கம்',
                            'Draw Number' => 'சீட்டிழுப்பு இலக்கம்',
                            'Colour' => 'நிறம்',
                            'Green' => 'பச்சை',
                            'Yellow' => 'மஞ்சள்',
                            'Red' => 'சிவப்பு',
                            'Blue' => 'நீலம்',
                            'Orange' => 'செம்மஞ்சள்',
                            'Purple' => 'ஊதா',
                            'Pink' => 'இளஞ்சிவப்பு',
                            'Light Blue' => 'இள நீலம்',
                            'Light blue' => 'இள நீலம்',
                            'Light Pink' => 'வெளிர் இளஞ்சிவப்பு',
                            'Light pink' => 'வெளிர் இளஞ்சிவப்பு',
                            'Light Green' => 'இளம் பச்சை',
                            'Light green' => 'இளம் பச்சை',
                            'Dark Green' => 'கரும் பச்சை',
                            'Dark green' => 'கரும் பச்சை',
                            'Dark Blue' => 'கரும் நீலம்',
                            'Dark blue' => 'கரும் நீலம்',
                            'Brown' => 'கபிலம்',
                            'Light Brown' => 'இளம் கபிலம்',
                            'Light brown' => 'இளம் கபிலம்',
                            'Dark Brown' => 'கரும் கபிலம்',
                            'Dark brown' => 'கரும் கபிலம்',
                            'Aries' => 'மேடம்',
                            'Taurus' => 'இடபம்',
                            'Gemini' => 'மிதுனம்',
                            'Cancer' => 'கடகம்',
                            'Leo' => 'சிம்மம்',
                            'Virgo' => 'கன்னி',
                            'Libra' => 'துலாம்',
                            'Scorpio' => 'விருச்சிகம்',
                            'Sagittarius' => 'தனுசு',
                            'Capricorn' => 'மகரம்',
                            'Aquarius' => 'கும்பம்',
                            'Pisces' => 'மீனம்'
                        ],
                    ],
                ];
            @endphp

            @if(!isset($isMerged) || !$isMerged)
                <!-- Navigation Bar (Only for web view) -->
                <nav class="navbar">
                    <div class="nav-left">
                        <!-- Brand -->
                        <div
                            style="display: flex; align-items: center; margin-right: 20px; font-weight: 700; color: var(--primary-color);">
                            <img src="{{ asset('images/logo/logo.png') }}" alt="LRMS" style="height: 32px; margin-right: 12px;">
                            <!-- <span style="font-size: 1.25rem;">LRMS</span> -->
                        </div>

                        <a href="{{ route('reports.index') }}" class="back-btn">
                            <i class="fas fa-arrow-left"></i> Back to Reports
                        </a>
                    </div>

                    <div class="nav-right">
                        <!-- <span style="color: #6b7280; font-size: 14px;">Expand sections below to view and download</span> -->
                    </div>
                </nav>

                <!-- Tabbed Interface Container -->
                <div class="tabs-wrapper">

                    <!-- Action Bar: Tabs Left, Downloads Right -->
                    <div class="tabs-header-bar">
                        <!-- Tabs -->
                        <div class="chrome-tabs">
                            @foreach(['en' => 'English', 'si' => 'Sinhala', 'ta' => 'Tamil'] as $code => $name)
                                @php
                                    $isActive = ($language ?? 'en') === $code;
                                    $activeClass = $isActive ? 'active' : '';
                                @endphp
                                <div class="chrome-tab {{ $activeClass }}" onclick="switchTab('{{ $code }}')" id="tab-{{ $code }}">
                                    @if($code === 'en')
                                        <i class="fas fa-globe-americas tab-icon"></i>
                                    @else
                                        <span class="tab-icon" style="font-weight:bold; font-size: 0.9em;">{{ strtoupper($code) }}</span>
                                    @endif
                                    <span class="tab-text">{{ $name }} Report</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Right Side Actions -->
                        <div class="global-actions">
                            <button type="button" class="btn btn-purple btn-sm pdf-download-btn"
                                data-pdf-action="download-merged"
                                title="Download Merged PDF">
                                <i class="fas fa-file-pdf"></i> Download All (Merged)
                            </button>
                        </div>
                    </div>
            @else
                <!-- For PDF: No navigation, just content wrapper -->
                <div class="tabs-wrapper">
            @endif

                <!-- Tab Content Area -->
                <div class="tabs-content-container">
                    @foreach(['en', 'si', 'ta'] as $lang)
                        @php
                            $info = $languageInfo[$lang];
                            // Determine visibility
                            if (isset($isMerged) && $isMerged) {
                                // For merged PDF, show ALL languages
                                $displayStyle = 'display: block;';
                            } else {
                                // For web view, show only selected language
                                $isDefault = ($language ?? 'en') === $lang;
                                $displayStyle = $isDefault ? 'display: block;' : 'display: none;';
                            }
                        @endphp

                        <div id="tab-content-{{ $lang }}"
                            class="tab-pane {{ (isset($isMerged) && $isMerged) ? 'page-break' : '' }}"
                            style="{{ $displayStyle }}">

                            @if(!isset($isMerged) || !$isMerged)
                                <!-- Individual Tab Toolbar (Download for this specific language) - Only for web view -->
                                <div class="tab-toolbar">
                                    <div class="toolbar-left">
                                        <span class="lang-badge badge-{{ $lang }}">{{ $info['name'] }}</span>
                                    </div>
                                    <div class="toolbar-right">
                                        <button type="button" class="btn btn-primary btn-sm pdf-download-btn"
                                            data-pdf-action="download-language" data-pdf-language="{{ $lang }}">
                                            <i class="fas fa-download"></i> Download {{ $info['name'] }} PDF
                                        </button>
                                    </div>
                                </div>

                                <!-- Report Content (A4) -->
                                <div class="a4-wrapper-center">
                            @endif
                                <div class="a4-container {{ $lang === 'si' ? 'width-si' : '' }}" id="report-content-{{ $lang }}">
                                    @php
                                        $headerImageMap = ['en' => 'header_en.png', 'si' => 'header_sin.png', 'ta' => 'header_tm.png'];
                                        $headerImage = $headerImageMap[$lang] ?? 'header_en.png';

                                        $carbonDate = \Carbon\Carbon::parse($date);
                                        $dayName = $carbonDate->format('l');
                                        $formattedDate = $carbonDate->format('Y.m.d');

                                        if ($lang === 'si') {
                                            $sinhalaDays = [
                                                'Sunday' => 'ඉරිදා',
                                                'Monday' => 'සඳුදා',
                                                'Tuesday' => 'අඟහරුවාදා',
                                                'Wednesday' => 'බදාදා',
                                                'Thursday' => 'බ්‍රහස්පතින්දා',
                                                'Friday' => 'සිකුරාදා',
                                                'Saturday' => 'සෙනසුරාදා'
                                            ];
                                            $dayName = $sinhalaDays[$dayName] ?? $dayName;
                                        } elseif ($lang === 'ta') {
                                            $tamilDays = [
                                                'Sunday' => 'ஞாயிற்றுக்கிழமை',
                                                'Monday' => 'திங்கட்கிழமை',
                                                'Tuesday' => 'செவ்வாய்க்கிழமை',
                                                'Wednesday' => 'புதன்கிழமை',
                                                'Thursday' => 'வியாழக்கிழமை',
                                                'Friday' => 'வெள்ளிக்கிழமை',
                                                'Saturday' => 'சனிக்கிழமை'
                                            ];
                                            $dayName = $tamilDays[$dayName] ?? $dayName;
                                        }

                                        $langLabels = $languageConfigs[$lang]['labels'] ?? [];
                                    @endphp

                                    <!-- Header Image -->
                                    <div class="header-image-container">
                                        <img src="{{ asset('images/pdf_static_images/header_images/' . $headerImage) }}"
                                            alt="Header" class="report-header-image">
                                        <div class="header-date-overlay overlay-{{ $lang }}">
                                            {{ $formattedDate }} <span class="day-name">{{ $dayName }}</span>
                                        </div>
                                    </div>

                                    <!-- Report Content -->
                                    <div class="page-content transparent-container no-padding">
                                        @php
                                            if (!function_exists('getHexColor')) {
                                                function getHexColor($colorName)
                                                {
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
                                                        'white' => '#9ca3af',
                                                        'gray' => '#6b7280',
                                                        'cyan' => '#06b6d4',
                                                        'lime' => '#84cc16',
                                                        'indigo' => '#6366f1',
                                                        'violet' => '#8b5cf6',
                                                        'fuchsia' => '#d946ef',
                                                        'rose' => '#f43f5e',
                                                        'sky' => '#0ea5e9',
                                                        'teal' => '#14b8a6',
                                                        'emerald' => '#10b981',
                                                        'amber' => '#f59e0b'
                                                    ];
                                                    return $colorMap[strtolower(trim($colorName ?? ''))] ?? '#667eea';
                                                }
                                            }

                                            $lotteryOrder = ['KP', 'LW', 'AK', 'SF', 'SB', 'SR', 'JS', 'DS'];
                                            $sortedDraws = $draws->sortBy(function ($draw) use ($lotteryOrder) {
                                                $code = $draw->lotteryType->code;
                                                $position = array_search($code, $lotteryOrder);
                                                return $position !== false ? $position : 999;
                                            });
                                        @endphp

                                        @foreach($sortedDraws as $draw)
                                            @php
                                                $code = strtolower($draw->lotteryType->code);
                                                $viewPath = "reports.lotteries.$code";
                                                $view = View::exists($viewPath) ? $viewPath : "reports.lotteries.common";
                                            @endphp
                                            @include($view, ['draw' => $draw, 'language' => $lang, 'labels' => $langLabels])
                                        @endforeach
                                    </div>

                                    <!-- Footer Image -->
                                    @php
                                        $footerImageMap = ['en' => 'footer_en.png', 'si' => 'footer_sin.png', 'ta' => 'footer_tm.png'];
                                        $footerImage = $footerImageMap[$lang] ?? 'footer_en.png';
                                    @endphp
                                    <img src="{{ asset('images/pdf_static_images/footer_images/' . $footerImage) }}"
                                        alt="Footer" class="report-footer-image">
                                </div>
                            @if(!isset($isMerged) || !$isMerged)
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tab Switching Script -->
        <script>
            function switchTab(lang) {
                // Update Tabs
                document.querySelectorAll('.chrome-tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                const activeTab = document.getElementById('tab-' + lang);
                if (activeTab) activeTab.classList.add('active');

                // Update Content
                document.querySelectorAll('.tab-pane').forEach(panel => {
                    panel.style.display = 'none';
                });
                const activeContent = document.getElementById('tab-content-' + lang);
                if (activeContent) activeContent.style.display = 'block';
            }
        </script>
    </div>
</body>

</html>
