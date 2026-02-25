@extends('layouts.app')

@section('title', 'About LRMS')

@section('content')
    <style>
        :root {
            --dlb-red: #e63946;
            --dlb-orange: #fca311;
            --dlb-yellow: #ffb703;
            --dlb-green: #2a9d8f;
            --dlb-white: #ffffff;
            --text-main: #2b2d42;
            --text-soft: #8d99ae;
            --bg-gray: #f8f9fa;
        }

        body {
            background-color: var(--bg-gray);
            font-family: 'Inter', sans-serif;
        }

        .about-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        @keyframes gradient-shift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        /* Minimal Hero */
        .hero-section {
            text-align: center;
            margin-bottom: 60px;
            background: var(--dlb-white);
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hero-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--dlb-red), var(--dlb-orange), var(--dlb-yellow), var(--dlb-green));
            background-size: 300% 100%;
            animation: gradient-shift 6s ease infinite;
        }

        .brand-logo {
            width: 80px;
            height: auto;
            margin-bottom: 25px;
            opacity: 0.9;
            animation: float 6s ease-in-out infinite;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
            margin-bottom: 15px;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: var(--text-soft);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Lottery Grid */
        .lottery-section {
            margin-bottom: 60px;
            text-align: center;
        }

        .section-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-soft);
            font-weight: 700;
            margin-bottom: 30px;
            display: block;
            position: relative;
            display: inline-block;
        }

        .section-label::after {
            content: '';
            display: block;
            width: 40px;
            height: 2px;
            background: var(--dlb-orange);
            margin: 8px auto 0;
            border-radius: 2px;
            opacity: 0.5;
        }

        .lottery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .lottery-item {
            background: var(--dlb-white);
            padding: 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
        }

        /* Staggered animation */
        .lottery-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .lottery-item:nth-child(2) {
            animation-delay: 0.15s;
        }

        .lottery-item:nth-child(3) {
            animation-delay: 0.2s;
        }

        .lottery-item:nth-child(4) {
            animation-delay: 0.25s;
        }

        .lottery-item:nth-child(5) {
            animation-delay: 0.3s;
        }

        .lottery-item:nth-child(6) {
            animation-delay: 0.35s;
        }

        .lottery-item:nth-child(7) {
            animation-delay: 0.4s;
        }

        .lottery-item:nth-child(8) {
            animation-delay: 0.45s;
        }

        .lottery-item:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            border-color: transparent;
        }

        .lottery-code {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--dlb-white);
            flex-shrink: 0;
            transition: transform 0.4s ease;
        }

        .lottery-item:hover .lottery-code {
            transform: rotate(10deg) scale(1.1);
        }

        .lottery-name {
            text-align: left;
            font-weight: 600;
            color: var(--text-main);
            font-size: 0.95rem;
        }

        /* Creative Color Assignments */
        .lottery-item:nth-child(4n+1) .lottery-code {
            background: var(--dlb-red);
            box-shadow: 0 4px 10px rgba(230, 57, 70, 0.3);
        }

        .lottery-item:nth-child(4n+2) .lottery-code {
            background: var(--dlb-orange);
            box-shadow: 0 4px 10px rgba(252, 163, 17, 0.3);
        }

        .lottery-item:nth-child(4n+3) .lottery-code {
            background: var(--dlb-green);
            box-shadow: 0 4px 10px rgba(42, 157, 143, 0.3);
        }

        .lottery-item:nth-child(4n+4) .lottery-code {
            background: var(--dlb-yellow);
            color: var(--text-main);
            box-shadow: 0 4px 10px rgba(255, 183, 3, 0.3);
        }

        /* Features */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 60px;
        }

        .feature-simple {
            background: transparent;
            padding: 25px;
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .feature-simple:hover {
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            transform: translateY(-5px);
        }

        .feature-icon-simple {
            font-size: 2rem;
            margin-bottom: 20px;
            display: inline-flex;
            padding: 12px;
            background: var(--dlb-white);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            color: var(--dlb-red);
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .feature-simple:hover .feature-icon-simple {
            transform: scale(1.1);
            color: var(--dlb-orange);
        }

        .feature-simple h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-main);
        }

        .feature-simple p {
            font-size: 0.95rem;
            color: var(--text-soft);
            line-height: 1.5;
        }

        /* Team Section Redesign */
        .team-section {
            background: var(--dlb-white);
            border-radius: 20px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease;
        }

        .team-section:hover {
            transform: translateY(-3px);
        }

        .team-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .team-member {
            background: var(--bg-gray);
            padding: 30px 20px;
            border-radius: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .team-member:hover {
            transform: translateY(-5px);
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .team-member::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--dlb-red), transparent);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .team-member:hover::before {
            transform: scaleX(1);
        }

        .member-avatar {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 50%;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #fff;
            font-size: 1.5rem;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .team-member:hover .member-avatar {
            transform: scale(1.1) rotate(5deg);
        }

        .member-avatar.cs {
            background: linear-gradient(135deg, var(--dlb-red), var(--dlb-orange));
        }

        .member-avatar.ns {
            background: linear-gradient(135deg, var(--dlb-orange), var(--dlb-yellow));
        }

        .member-avatar.nm {
            background: linear-gradient(135deg, var(--dlb-green), var(--dlb-yellow));
        }

        .member-name {
            font-weight: 700;
            color: var(--text-main);
            display: block;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .member-role {
            font-size: 0.85rem;
            color: var(--text-soft);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .member-socials {
            display: flex;
            gap: 12px;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .team-member:hover .member-socials {
            opacity: 1;
        }

        .social-link {
            color: var(--text-main);
            transition: all 0.2s ease;
            padding: 8px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .social-link:hover {
            color: var(--dlb-red);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .social-link svg {
            width: 20px;
            height: 20px;
            stroke-width: 2;
        }

        @media (max-width: 768px) {
            .features-grid {
                grid-template-columns: 1fr;
            }

            .hero-title {
                font-size: 2rem;
            }

            /* Mobile Steps */
            .steps-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .steps-grid::before {
                width: 2px;
                height: 100%;
                left: 50%;
                top: 0;
                transform: translateX(-50%);
            }
        }

        /* Enhanced How to Use */
        .steps-section {
            margin-bottom: 100px;
            text-align: center;
            position: relative;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Connecting Line */
        .steps-grid::before {
            content: '';
            position: absolute;
            top: 40px;
            /* Adjust based on icon size */
            left: 50px;
            right: 50px;
            height: 2px;
            background: #e9ecef;
            z-index: 0;
        }

        .step-card {
            position: relative;
            z-index: 1;
            padding: 20px;
            background: transparent;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .step-icon-wrapper {
            width: 80px;
            height: 80px;
            background: var(--dlb-white);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            position: relative;
            border: 2px solid transparent;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .step-card:hover .step-icon-wrapper {
            transform: scale(1.1) translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Specific Colors */
        .step-card:nth-child(1) .step-icon-wrapper {
            color: var(--dlb-red);
        }

        .step-card:nth-child(1):hover .step-icon-wrapper {
            background: var(--dlb-red);
            color: white;
            border-color: var(--dlb-red);
        }

        .step-card:nth-child(2) .step-icon-wrapper {
            color: var(--dlb-orange);
        }

        .step-card:nth-child(2):hover .step-icon-wrapper {
            background: var(--dlb-orange);
            color: white;
            border-color: var(--dlb-orange);
        }

        .step-card:nth-child(3) .step-icon-wrapper {
            color: var(--dlb-green);
        }

        .step-card:nth-child(3):hover .step-icon-wrapper {
            background: var(--dlb-green);
            color: white;
            border-color: var(--dlb-green);
        }

        .step-card:nth-child(4) .step-icon-wrapper {
            color: var(--dlb-yellow);
        }

        .step-card:nth-child(4):hover .step-icon-wrapper {
            background: var(--dlb-yellow);
            color: var(--text-main);
            border-color: var(--dlb-yellow);
        }


        .step-icon-svg {
            width: 32px;
            height: 32px;
            stroke-width: 2;
        }

        .step-number-tag {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 24px;
            height: 24px;
            background: var(--text-main);
            color: white;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        .step-title {
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: var(--text-main);
        }

        .step-desc {
            font-size: 0.9rem;
            color: var(--text-soft);
            line-height: 1.6;
        }
    </style>

    <div class="about-container">
        <!-- Hero -->
        <div class="hero-section animate-up">
            <img src="{{ asset('images/logo/logo.png') }}" alt="LRMS Logo" class="brand-logo">
            <h1 class="hero-title">Simply Efficient.</h1>
            <p class="hero-subtitle">The Lottery Report Management System (LRMS) streamlines data processing, XML parsing,
                and report generation for the Development Lotteries Board.</p>
        </div>

        <!-- Supported Lotteries -->
        <div class="lottery-section animate-up delay-100">
            <span class="section-label">Supported Lotteries</span>
            <div class="lottery-grid">
                <div class="lottery-item animate-up">
                    <div class="lottery-code">AK</div>
                    <div class="lottery-name">Ada Kotipathi</div>
                </div>
                <div class="lottery-item animate-up">
                    <div class="lottery-code">DS</div>
                    <div class="lottery-name">Supiri Dhana Sampatha</div>
                </div>
                <div class="lottery-item animate-up">
                    <div class="lottery-code">JS</div>
                    <div class="lottery-name">Jaya Sampatha</div>
                </div>
                <div class="lottery-item animate-up">
                    <div class="lottery-code">KP</div>
                    <div class="lottery-name">Kapruka</div>
                </div>
                <div class="lottery-item animate-up">
                    <div class="lottery-code">LW</div>
                    <div class="lottery-name">Lagna Wasanawa</div>
                </div>
                <div class="lottery-item animate-up">
                    <div class="lottery-code">SB</div>
                    <div class="lottery-name">Super Ball</div>
                </div>
                <div class="lottery-item animate-up">
                    <div class="lottery-code">SF</div>
                    <div class="lottery-name">Shanida</div>
                </div>
                <div class="lottery-item animate-up">
                    <div class="lottery-code">SR</div>
                    <div class="lottery-name">Sasiri</div>
                </div>
            </div>
        </div>

        <!-- Core Features -->
        <div class="features-grid animate-up delay-200">
            <div class="feature-simple">
                <div class="feature-icon-simple">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                </div>
                <h3>Intelligent Processing</h3>
                <p>Automated XML parsing validates draw data instantly, ensuring accuracy before reports are generated.</p>
            </div>
            <div class="feature-simple">
                <div class="feature-icon-simple">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                    </svg>
                </div>
                <h3>Real-time Analytics</h3>
                <p>Monitoring dashboards provide live insights into draw statistics, uploads, and system health.</p>
            </div>
            <div class="feature-simple">
                <div class="feature-icon-simple">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                        <line x1="8" y1="21" x2="16" y2="21" />
                        <line x1="12" y1="17" x2="12" y2="21" />
                    </svg>
                </div>
                <h3>Multilingual Output</h3>
                <p>One-click PDF generation creates professional reports in English, Sinhala, and Tamil simultaneously.</p>
            </div>
        </div>

        <!-- How to Use / Process Flow -->
        <div class="steps-section animate-up delay-200">
            <span class="section-label">How It Works</span>

            <div class="steps-grid">

                <!-- Step 1 -->
                <div class="step-card">
                    <div class="step-icon-wrapper">
                        <div class="step-number-tag">1</div>
                        <svg class="step-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                    </div>
                    <div class="step-title">Upload</div>
                    <div class="step-desc">Upload the official winning numbers XML file provided by DLB.</div>
                </div>

                <!-- Step 2 -->
                <div class="step-card">
                    <div class="step-icon-wrapper">
                        <div class="step-number-tag">2</div>
                        <svg class="step-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                    </div>
                    <div class="step-title">Verify</div>
                    <div class="step-desc">System automatically validates XML structure and draw data integrity.</div>
                </div>

                <!-- Step 3 -->
                <div class="step-card">
                    <div class="step-icon-wrapper">
                        <div class="step-number-tag">3</div>
                        <svg class="step-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                    </div>
                    <div class="step-title">Generate</div>
                    <div class="step-desc">One-click generation creates formatted PDF reports for all languages.</div>
                </div>

                <!-- Step 4 -->
                <div class="step-card">
                    <div class="step-icon-wrapper">
                        <div class="step-number-tag">4</div>
                        <svg class="step-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                    </div>
                    <div class="step-title">Distribute</div>
                    <div class="step-desc">Download and distribute the finalized reports via email or print.</div>
                </div>

            </div>
        </div>

        <!-- Team -->
        <div class="team-section animate-up delay-300">
            <span class="section-label" style="margin-bottom: 30px;">Crafted By</span>
            <div class="team-list">

                <!-- CS -->
                <div class="team-member">
                    <div class="member-avatar cs">CS</div>
                    <span class="member-name">Chiran Savindya</span>
                    <span class="member-role">UI/UX & Integration</span>
                    <div class="member-socials">
                        <a href="https://github.com/chiransavindya/" class="social-link" title="GitHub">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22">
                                </path>
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/in/chiran-senarath" target="_blank" class="social-link"
                            title="LinkedIn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z">
                                </path>
                                <rect x="2" y="9" width="4" height="12"></rect>
                                <circle cx="4" cy="4" r="2"></circle>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- NS -->
                <div class="team-member">
                    <div class="member-avatar ns">NS</div>
                    <span class="member-name">Niwantha Sithumal</span>
                    <span class="member-role">Architecture</span>
                    <div class="member-socials">
                        <a href="https://github.com/N1wan7ha" class="social-link" title="GitHub">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22">
                                </path>
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/in/niwanthasithumal" target="_blank" class="social-link"
                            title="LinkedIn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z">
                                </path>
                                <rect x="2" y="9" width="4" height="12"></rect>
                                <circle cx="4" cy="4" r="2"></circle>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- NM -->
                <div class="team-member">
                    <div class="member-avatar nm">NM</div>
                    <span class="member-name">Nimesha Madurangi</span>
                    <span class="member-role">Concept Founder</span>
                    <div class="member-socials">
                        <a href="https://github.com/NimeshaMadurangi" class="social-link" title="GitHub">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22">
                                </path>
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/in/nimesha-madurangi" target="_blank" class="social-link"
                            title="LinkedIn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z">
                                </path>
                                <rect x="2" y="9" width="4" height="12"></rect>
                                <circle cx="4" cy="4" r="2"></circle>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Minimal Footer
                    <div style="text-align: center; margin-top: 50px; color: var(--text-soft); font-size: 0.8rem; opacity: 0;"
                        class="animate-up delay-300">
                        &copy; {{ date('Y') }} LRMS v2. Development Lotteries Board.
                    </div> -->
    </div>
@endsection
