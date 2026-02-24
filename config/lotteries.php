<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Required Lotteries Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines the 8 mandatory lottery types that must be
    | present for each draw date to generate a complete report.
    |
    */

    'required_lotteries' => [
        'AK' => [
            'code' => 'AK',
            'name_en' => 'Ada Kotipathi',
            'name_si' => 'ඇද කෝටිපති',
            'name_ta' => 'அட கோடிபதி',
        ],
        'DS' => [
            'code' => 'DS',
            'name_en' => 'Supiri Dhana Sampatha',
            'name_si' => 'සුපිරි ධන සම්පත',
            'name_ta' => 'சுபிரி தன சம்பத',
        ],
        'LW' => [
            'code' => 'LW',
            'name_en' => 'Lagna Wasanawa',
            'name_si' => 'ලග්න වාසනාව',
            'name_ta' => 'லக்னா வாசனாவா',
        ],
        'SB' => [
            'code' => 'SB',
            'name_en' => 'Super Ball',
            'name_si' => 'සුපර් බෝල්',
            'name_ta' => 'சூப்பர் பால்',
        ],
        'KP' => [
            'code' => 'KP',
            'name_en' => 'Kapruka',
            'name_si' => 'කප්රුක',
            'name_ta' => 'கப்ருக',
        ],
        'JS' => [
            'code' => 'JS',
            'name_en' => 'Jaya Sampatha',
            'name_si' => 'ජය සම්පත',
            'name_ta' => 'ஜெய சம்பத',
        ],
        'SR' => [
            'code' => 'SR',
            'name_en' => 'Sasiri',
            'name_si' => 'සසිරි',
            'name_ta' => 'சசிரி',
        ],
        'SF' => [
            'code' => 'SF',
            'name_en' => 'Shanida',
            'name_si' => 'ශනිදා',
            'name_ta' => 'ஷானிடா',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Lottery Codes (Quick Access)
    |--------------------------------------------------------------------------
    |
    | Simple array of all required lottery codes for validation
    |
    */
    'required_codes' => ['AK', 'DS', 'LW', 'SB', 'KP', 'JS', 'SR', 'SF'],

    /*
    |--------------------------------------------------------------------------
    | Total Required Count
    |--------------------------------------------------------------------------
    |
    | The exact number of lotteries required for a complete batch
    |
    */
    'required_count' => 8,
];
