<?php

namespace App\Services;

use App\Models\Draw;
use App\Models\LotteryType;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class XmlParserService
{
    /**
     * Parse XML file and extract lottery draw data.
     *
     * @param string $filePath Path to XML file
     * @param string|null $originalFilename Original filename for logging
     * @return array Parsed draw data
     * @throws \Exception
     */
    public function parseXmlFile(string $filePath, ?string $originalFilename = null): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $xmlContent = file_get_contents($filePath);

        // Suppress libxml errors and use internal error handling
        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($xmlContent);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $filename = $originalFilename ?? basename($filePath);
            throw new \Exception("Failed to parse XML file {$filename}: " . json_encode($errors));
        }

        // Use original filename in error messages if available
        $displayPath = $originalFilename ?? $filePath;
        return $this->extractDrawData($xml, $displayPath);
    }

    /**
     * Extract draw data from parsed XML.
     *
     * @param SimpleXMLElement $xml
     * @param string $filePath
     * @return array
     */
    protected function extractDrawData(SimpleXMLElement $xml, string $filePath): array
    {
        // Extract lottery code from XML content (v2.0 - more robust than filename parsing)
        // Pass the original filename as fallback for code extraction
        $lotteryCode = $this->extractLotteryCode($xml, $filePath);

        if (!$lotteryCode) {
            // Fallback: If filename contains known lottery names
            $filename = basename($filePath);
            $lotteryNames = ['KAPRUKA' => 'KP', 'SASIRI' => 'SR', 'JAYA' => 'JS', 'ADA' => 'AK', 'SHANIDA' => 'SF', 'SUPER' => 'SB', 'LAGNA' => 'LW', 'SUPIRI' => 'DS'];
            foreach ($lotteryNames as $name => $code) {
                if (stripos($filename, $name) !== false) {
                    $lotteryCode = $code;
                    break;
                }
            }
        }

        if (!$lotteryCode) {
            $displayPath = basename($filePath);
            throw new \Exception("Could not extract lottery code from XML content or filename. File: {$displayPath}");
        }

        // Get lottery type from database
        $lotteryType = LotteryType::where('code', $lotteryCode)->first();

        if (!$lotteryType) {
            throw new \Exception("Lottery type not found for code: {$lotteryCode}");
        }

        // Extract basic draw information
        $drawDate = $this->extractDrawDate($xml);
        $drawNumber = $this->extractDrawNumber($xml);
        $numbers = $this->extractNumbers($xml);
        $bonusNumber = $this->extractBonusNumber($xml);
        $prizeBreakdown = $this->extractPrizeBreakdown($xml);
        $totalSales = $this->extractTotalSales($xml);
        $jackpotAmount = $this->extractJackpotAmount($xml);

        // Extract new required fields (v2.0)
        $dayName = $this->extractDayName($xml, $drawDate);
        $color = $this->extractColor($xml);
        $englishLetters = $this->extractEnglishLetters($xml);
        $englishLetters = $this->extractEnglishLetters($xml);
        $superNumber = $this->extractSuperNumber($xml, $lotteryCode);
        $zodiacSign = $this->extractZodiacSign($xml);
        $nextJackpot = $this->extractNextJackpot($xml);
        $specialNumber = $this->extractSpecialNumber($xml);
        $totalWinners = $this->extractTotalWinners($xml);

        // Extract metadata (agents, additional info)
        $metadata = $this->extractMetadata($xml);

        // Add new fields to metadata for PDF generation
        $metadata['day_name'] = $dayName;
        $metadata['color'] = $color;
        $metadata['english_letters'] = $englishLetters;
        $metadata['super_number'] = $superNumber;
        $metadata['zodiac_sign'] = $zodiacSign;
        $metadata['next_jackpot'] = $nextJackpot;
        $metadata['special_number'] = $specialNumber;
        $metadata['total_winners'] = $totalWinners;

        // Calculate Total Prize Value (v2.1)
        $metadata['total_prize_value'] = $this->calculateTotalPrizeValue($xml);

        // Sanitize numbers: verify that letters are not included in numbers
        if (!empty($englishLetters)) {
            $numbers = array_diff($numbers, $englishLetters);
        }

        // Sanitize numbers: if Super Number was extracted from balls (for KP), remove it from numbers
        if ($lotteryCode === 'KP' && $superNumber && in_array($superNumber, $numbers)) {
            // Only remove the last occurrence to be safe, or just the value
            // For KP, super number is usually the last one.
            $lastNum = end($numbers);
            if ($lastNum === $superNumber) {
                array_pop($numbers);
            }
        }

        // Re-index numbers
        $numbers = array_values($numbers);

        return [
            'lottery_type_id' => $lotteryType->id,
            'lottery_code' => $lotteryCode,
            'draw_date' => $drawDate,
            'draw_number' => $drawNumber,
            'numbers' => $numbers,
            'bonus_number' => $bonusNumber,
            'prize_breakdown' => $prizeBreakdown,
            'total_sales' => $totalSales,
            'jackpot_amount' => $jackpotAmount,
            'metadata' => $metadata,
        ];
    }

    /**
     * Extract lottery code from XML content.
     * Tries multiple strategies: <code> tag, <name> mapping, and filename fallback.
     *
     * @param SimpleXMLElement $xml
     * @param string $filePath Original filename for fallback
     * @return string|null
     */
    protected function extractLotteryCode(SimpleXMLElement $xml, string $filePath): ?string
    {
        // Strategy 1: Try to extract from <code> tag (if it's a 2-letter code)
        $codePaths = ['code', 'Code', 'lottery_code', 'LotteryCode', 'LOTTERY_CODE'];

        foreach ($codePaths as $path) {
            if (isset($xml->{$path}) && !empty((string) $xml->{$path})) {
                $code = strtoupper(trim((string) $xml->{$path}));
                // Validate it's a 2-letter code
                if (preg_match('/^[A-Z]{2}$/', $code)) {
                    return $code;
                }
            }
        }

        // Strategy 2: Try to map from lottery name
        $code = $this->mapLotteryNameToCode($xml);
        if ($code) {
            return $code;
        }

        // Strategy 3: Fallback to extracting from original filename
        $filename = basename($filePath);
        if (preg_match('/^([A-Z]{2})_/', $filename, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Map lottery name from XML to lottery code.
     *
     * @param SimpleXMLElement $xml
     * @return string|null
     */
    protected function mapLotteryNameToCode(SimpleXMLElement $xml): ?string
    {
        $namePaths = ['name', 'Name', 'lottery_name', 'LotteryName'];

        foreach ($namePaths as $path) {
            if (isset($xml->{$path}) && !empty((string) $xml->{$path})) {
                $name = strtoupper(trim((string) $xml->{$path}));

                // Map known lottery names to codes
                $nameToCodeMap = [
                    'ADA KOTIPATHI' => 'AK',
                    'SUPIRI DHANA SAMPATHA' => 'DS',
                    'LAGNA WASANAWA' => 'LW',
                    'SUPER BALL' => 'SB',
                    'KAPRUKA' => 'KP',
                    'JAYA SAMPATHA' => 'JS',
                    'SASIRI' => 'SR',
                    'SHANIDA' => 'SF',
                ];

                if (isset($nameToCodeMap[$name])) {
                    return $nameToCodeMap[$name];
                }

                // Try partial matches
                foreach ($nameToCodeMap as $lotteryName => $code) {
                    if (strpos($name, $lotteryName) !== false || strpos($lotteryName, $name) !== false) {
                        return $code;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract draw date from XML.
     */
    protected function extractDrawDate(SimpleXMLElement $xml): ?string
    {
        // Try multiple possible paths for draw date
        $datePaths = [
            'draw_date',
            'DrawDate',
            'date',
            'Date',
            'DRAW_DATE',
        ];

        foreach ($datePaths as $path) {
            if (isset($xml->{$path}) && !empty((string) $xml->{$path})) {
                $dateStr = (string) $xml->{$path};
                // Try to parse and format the date
                try {
                    return date('Y-m-d', strtotime($dateStr));
                } catch (\Exception $e) {
                    Log::warning("Failed to parse date: {$dateStr}");
                }
            }
        }

        return null;
    }

    /**
     * Extract draw number from XML.
     */
    protected function extractDrawNumber(SimpleXMLElement $xml): ?int
    {
        // Try multiple possible paths for draw number
        $numberPaths = [
            'number',               // Common in XML files (e.g., <number>2843</number>)
            'draw_no',
            'draw_number',
            'DrawNo',
            'DrawNumber',
            'DRAW_NO',
            'serial_no',
            'SerialNo',
        ];

        foreach ($numberPaths as $path) {
            if (isset($xml->{$path}) && !empty((string) $xml->{$path})) {
                return (int) $xml->{$path};
            }
        }

        return null;
    }

    /**
     * Extract winning numbers from XML.
     * This method intelligently detects different XML structures.
     */
    protected function extractNumbers(SimpleXMLElement $xml): array
    {
        $numbers = [];

        // Strategy 1: Look for <numbers> or <winning_numbers> tag
        $numberTags = ['numbers', 'winning_numbers', 'Numbers', 'WinningNumbers', 'NUMBERS'];

        foreach ($numberTags as $tag) {
            if (isset($xml->{$tag})) {
                $numbersNode = $xml->{$tag};

                // Check if it's a comma-separated string
                $numbersStr = (string) $numbersNode;
                if (str_contains($numbersStr, ',')) {
                    return array_map('trim', explode(',', $numbersStr));
                }

                // Check for child number elements
                if (isset($numbersNode->number)) {
                    foreach ($numbersNode->number as $num) {
                        $numbers[] = (string) $num;
                    }
                    return $numbers;
                }
            }
        }

        // Strategy 2: Look for individual number tags (number1, number2, etc.)
        for ($i = 1; $i <= 20; $i++) {
            $numTag = "number{$i}";
            if (isset($xml->{$numTag})) {
                $numbers[] = (string) $xml->{$numTag};
            } else {
                break; // Stop when no more sequential numbers found
            }
        }

        if (!empty($numbers)) {
            return $numbers;
        }

        // Strategy 3: Look for <ball>, <balls>, or similar tags
        if (isset($xml->balls)) {
            foreach ($xml->balls->ball as $ball) {
                $numbers[] = (string) $ball;
            }
            return $numbers;
        }

        return $numbers;
    }

    /**
     * Extract bonus number from XML.
     */
    protected function extractBonusNumber(SimpleXMLElement $xml): ?string
    {
        $bonusTags = ['bonus_number', 'bonus', 'BonusNumber', 'Bonus', 'BONUS', 'super_number', 'power_number'];

        foreach ($bonusTags as $tag) {
            if (isset($xml->{$tag}) && !empty((string) $xml->{$tag})) {
                return (string) $xml->{$tag};
            }
        }

        return null;
    }

    /**
     * Extract prize breakdown from XML.
     */
    protected function extractPrizeBreakdown(SimpleXMLElement $xml): array
    {
        $prizeBreakdown = [];

        // Strategy 1: Look for <results><prize> structure
        if (isset($xml->results->prize)) {
            foreach ($xml->results->prize as $prize) {
                $prizeData = [
                    'code' => (string) ($prize->code ?? ''),
                    'index' => (int) ($prize->index ?? 0),
                    'value' => (float) ($prize->value ?? 0),
                    'amount' => (float) ($prize->value ?? 0), // Normalize to amount
                    'count' => (int) ($prize->count ?? 0),
                    'game' => (string) ($prize->game ?? ''),
                ];

                // Include agent info if available
                if (isset($prize->agent)) {
                    $prizeData['agents'] = [];
                    foreach ($prize->agent as $agent) {
                        $prizeData['agents'][] = [
                            'code' => (string) ($agent->code ?? ''),
                            'name_en' => (string) ($agent->nameen ?? ''),
                            'name_si' => (string) ($agent->namesi ?? ''),
                            'name_ta' => (string) ($agent->nametm ?? ''),
                            'location_en' => (string) ($agent->locationen ?? ''),
                            'location_si' => (string) ($agent->locationsi ?? ''),
                            'location_ta' => (string) ($agent->locationtm ?? ''),
                            'district' => (string) ($agent->district ?? ''),
                        ];
                    }
                }

                $prizeBreakdown[] = $prizeData;
            }
            return $prizeBreakdown;
        }

        // Strategy 2: Look for <prizes><prize> structure
        if (isset($xml->prizes)) {
            foreach ($xml->prizes->prize as $prize) {
                $prizeData = [
                    'category' => (string) ($prize->category ?? ''),
                    'winners' => (int) ($prize->winners ?? 0),
                    'amount' => (float) ($prize->amount ?? 0),
                ];

                // Include category names in different languages if available
                if (isset($prize->category_en))
                    $prizeData['category_en'] = (string) $prize->category_en;
                if (isset($prize->category_si))
                    $prizeData['category_si'] = (string) $prize->category_si;
                if (isset($prize->category_ta))
                    $prizeData['category_ta'] = (string) $prize->category_ta;

                $prizeBreakdown[] = $prizeData;
            }
        }

        return $prizeBreakdown;
    }

    /**
     * Extract total sales from XML.
     */
    protected function extractTotalSales(SimpleXMLElement $xml): ?float
    {
        $salesTags = ['total', 'total_sales', 'sales', 'TotalSales', 'Sales', 'TOTAL_SALES'];

        foreach ($salesTags as $tag) {
            if (isset($xml->{$tag}) && !empty((string) $xml->{$tag})) {
                return (float) str_replace(',', '', (string) $xml->{$tag});
            }
        }

        return null;
    }

    /**
     * Extract jackpot amount from XML.
     */
    protected function extractJackpotAmount(SimpleXMLElement $xml): ?float
    {
        $jackpotTags = ['super', 'jackpot', 'jackpot_amount', 'Jackpot', 'JackpotAmount', 'JACKPOT', 'super_prize', 'grand_prize'];

        foreach ($jackpotTags as $tag) {
            if (isset($xml->{$tag}) && !empty((string) $xml->{$tag})) {
                $value = (float) str_replace(',', '', (string) $xml->{$tag});
                // Only return if value is greater than 0
                if ($value > 0) {
                    return $value;
                }
            }
        }

        // Try to extract from prize breakdown (usually the first/top prize)
        if (isset($xml->results->prize[0]->value)) {
            return (float) str_replace(',', '', (string) $xml->results->prize[0]->value);
        }

        if (isset($xml->prizes->prize[0]->amount)) {
            return (float) str_replace(',', '', (string) $xml->prizes->prize[0]->amount);
        }

        return null;
    }

    /**
     * Extract day name from XML or derive from draw date.
     */
    protected function extractDayName(SimpleXMLElement $xml, string $drawDate): ?string
    {
        $dayTags = ['day_name', 'day', 'DayName', 'Day', 'DAY'];

        foreach ($dayTags as $tag) {
            if (isset($xml->{$tag}) && !empty((string) $xml->{$tag})) {
                return (string) $xml->{$tag};
            }
        }

        // If not in XML, derive from draw date
        try {
            $date = new \DateTime($drawDate);
            return $date->format('l'); // Returns full day name (Monday, Tuesday, etc.)
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Extract lottery color/brand color from XML.
     */
    protected function extractColor(SimpleXMLElement $xml): ?string
    {
        $colorTags = ['color', 'Color', 'COLOR', 'ticket_color', 'brand_color'];

        foreach ($colorTags as $tag) {
            if (isset($xml->{$tag}) && !empty((string) $xml->{$tag})) {
                return (string) $xml->{$tag};
            }
        }

        return null;
    }

    /**
     * Extract English letters from XML (if applicable).
     */
    protected function extractEnglishLetters(SimpleXMLElement $xml): ?array
    {
        $letterTags = ['english_letters', 'letters', 'EnglishLetters', 'Letters', 'LETTERS'];

        foreach ($letterTags as $tag) {
            if (isset($xml->{$tag})) {
                $lettersStr = (string) $xml->{$tag};
                if (str_contains($lettersStr, ',')) {
                    return array_map('trim', explode(',', $lettersStr));
                }
                // Single letter
                if (!empty($lettersStr)) {
                    return [$lettersStr];
                }
            }
        }

        // Extract from balls element - look for alphabetic characters
        if (isset($xml->balls)) {
            $letters = [];
            foreach ($xml->balls->ball as $ball) {
                $ballValue = (string) $ball;
                // If it's alphabetic, it's a letter
                if (preg_match('/^[A-Za-z]+$/', $ballValue)) {
                    $letters[] = $ballValue;
                }
            }
            if (!empty($letters)) {
                return $letters;
            }
        }

        return null;
    }

    /**
     * Extract super number from XML (different from bonus number).
     */
    protected function extractSuperNumber(SimpleXMLElement $xml, ?string $lotteryCode = null): ?string
    {
        // Explicit tags
        $superTags = ['super_number', 'SuperNumber', 'SUPER_NUMBER', 'power_ball'];

        foreach ($superTags as $tag) {
            if (isset($xml->{$tag}) && !empty((string) $xml->{$tag})) {
                // Verify it's not a large jackpot amount (heuristic: < 100)
                // Kapruka <super> is jackpot, so we skip it if it's large number
                $val = (string) $xml->{$tag};
                if (is_numeric($val) && (float) $val > 100) {
                    continue;
                }
                return $val;
            }
        }

        // For Kapruka (KP), if no explicit super tag found, try to find it in balls
        if ($lotteryCode === 'KP' && isset($xml->balls)) {
            // Get all balls
            $balls = [];
            foreach ($xml->balls->ball as $ball) {
                $balls[] = (string) $ball;
            }

            // Remove letters
            $numericBalls = array_filter($balls, function ($b) {
                return is_numeric($b);
            });

            // If we have at least 5 numeric balls (usually 4 winners + 1 super), or specifically for KP structure
            // The LAST numeric ball is the Super Number
            if (count($numericBalls) > 0) {
                return end($numericBalls);
            }
        }

        return null;
    }

    /**
     * Calculate Total Prize Value from prize breakdown.
     */
    protected function calculateTotalPrizeValue(SimpleXMLElement $xml): float
    {
        $total = 0.0;

        // Strategy 1: <results><prize>
        if (isset($xml->results->prize)) {
            foreach ($xml->results->prize as $prize) {
                $val = (float) str_replace(',', '', (string) ($prize->value ?? 0));
                $count = (int) str_replace(',', '', (string) ($prize->count ?? 0));
                $total += $val * $count;
            }
        }
        // Strategy 2: <prizes><prize>
        elseif (isset($xml->prizes->prize)) {
            foreach ($xml->prizes->prize as $prize) {
                $val = (float) str_replace(',', '', (string) ($prize->amount ?? 0));
                $winners = (int) str_replace(',', '', (string) ($prize->winners ?? 0));
                $total += $val * $winners;
            }
        }

        return $total;
    }

    /**
     * Extract zodiac sign from XML (if applicable).
     */
    protected function extractZodiacSign(SimpleXMLElement $xml): ?string
    {
        $zodiacTags = ['zodiac_sign', 'zodiac', 'ZodiacSign', 'Zodiac', 'ZODIAC', 'lagna', 'Lagna'];

        foreach ($zodiacTags as $tag) {
            if (isset($xml->{$tag}) && !empty((string) $xml->{$tag})) {
                return (string) $xml->{$tag};
            }
        }

        // Extract from balls element - look for zodiac names
        if (isset($xml->balls)) {
            $zodiacNames = [
                'aries',
                'taurus',
                'gemini',
                'cancer',
                'leo',
                'virgo',
                'libra',
                'scorpio',
                'sagittarius',
                'capricorn',
                'aquarius',
                'pisces'
            ];

            foreach ($xml->balls->ball as $ball) {
                $ballValue = strtolower(trim((string) $ball));
                if (in_array($ballValue, $zodiacNames)) {
                    return ucfirst($ballValue);
                }
            }
        }

        return null;
    }

    /**
     * Extract next super jackpot amount from XML.
     */
    protected function extractNextJackpot(SimpleXMLElement $xml): ?float
    {
        $nextJackpotTags = ['next_jackpot', 'next_super_jackpot', 'NextJackpot', 'NEXT_JACKPOT', 'next_draw_jackpot'];

        foreach ($nextJackpotTags as $tag) {
            if (isset($xml->{$tag}) && !empty((string) $xml->{$tag})) {
                $value = (float) str_replace(',', '', (string) $xml->{$tag});
                if ($value > 0) {
                    return $value;
                }
            }
        }

        // Check next/super element
        if (isset($xml->next->super)) {
            $value = (float) str_replace(',', '', (string) $xml->next->super);
            if ($value > 0) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Extract special number from XML (if applicable).
     */
    protected function extractSpecialNumber(SimpleXMLElement $xml): ?string
    {
        $specialTags = ['special_number', 'SpecialNumber', 'SPECIAL_NUMBER', 'lucky_number'];

        foreach ($specialTags as $tag) {
            if (isset($xml->{$tag}) && !empty((string) $xml->{$tag})) {
                return (string) $xml->{$tag};
            }
        }

        return null;
    }

    /**
     * Extract total winners count from XML.
     */
    protected function extractTotalWinners(SimpleXMLElement $xml): ?int
    {
        // Check count element first (direct winner count)
        if (isset($xml->count) && !empty((string) $xml->count)) {
            return (int) str_replace(',', '', (string) $xml->count);
        }

        $winnerTags = ['total_winners', 'TotalWinners', 'TOTAL_WINNERS', 'winner_count'];

        foreach ($winnerTags as $tag) {
            if (isset($xml->{$tag}) && !empty((string) $xml->{$tag})) {
                return (int) str_replace(',', '', (string) $xml->{$tag});
            }
        }

        // Try to sum from prize breakdown
        if (isset($xml->results->prize)) {
            $total = 0;
            foreach ($xml->results->prize as $prize) {
                if (isset($prize->count)) {
                    $total += (int) $prize->count;
                }
            }
            return $total > 0 ? $total : null;
        }

        if (isset($xml->prizes->prize)) {
            $total = 0;
            foreach ($xml->prizes->prize as $prize) {
                if (isset($prize->winners)) {
                    $total += (int) $prize->winners;
                }
            }
            return $total > 0 ? $total : null;
        }

        return null;
    }

    /**
     * Extract additional metadata from XML.
     * This includes agent information, multi-language data, etc.
     */
    protected function extractMetadata(SimpleXMLElement $xml): array
    {
        $metadata = [];

        // Extract agent information
        if (isset($xml->agents)) {
            $agents = [];
            foreach ($xml->agents->agent as $agent) {
                $agents[] = [
                    'district' => (string) ($agent->district ?? ''),
                    'district_si' => (string) ($agent->district_si ?? ''),
                    'district_ta' => (string) ($agent->district_ta ?? ''),
                    'serial' => (string) ($agent->serial ?? ''),
                    'name' => (string) ($agent->name ?? ''),
                    'name_si' => (string) ($agent->name_si ?? ''),
                    'name_ta' => (string) ($agent->name_ta ?? ''),
                ];
            }
            $metadata['agents'] = $agents;
        }

        // Extract any multi-language descriptions
        if (isset($xml->description_en))
            $metadata['description_en'] = (string) $xml->description_en;
        if (isset($xml->description_si))
            $metadata['description_si'] = (string) $xml->description_si;
        if (isset($xml->description_ta))
            $metadata['description_ta'] = (string) $xml->description_ta;

        // Extract <attributes> section (common in SB, SF)
        if (isset($xml->attributes)) {
            foreach ($xml->attributes->attribute as $attr) {
                $key = (string) ($attr->key ?? '');
                $val = (string) ($attr->valueen ?? ''); // Default to English value

                if ($key && $val) {
                    $metadata[$key] = $val;
                }
            }
        }

        // Extract any additional custom fields
        foreach ($xml as $key => $value) {
            $keyStr = (string) $key;
            // Skip already processed fields
            if (!in_array($keyStr, ['draw_date', 'draw_no', 'numbers', 'prizes', 'results', 'agents', 'total_sales', 'jackpot', 'attributes', 'next'])) {
                // Store as additional metadata
                if (is_object($value) && count($value->children()) > 0) {
                    continue;
                } else {
                    $metadata[$keyStr] = (string) $value;
                }
            }
        }

        return $metadata;
    }

    /**
     * Save parsed draw data to database.
     * Handles duplicate prevention using the unique constraint.
     *
     * @param array $drawData
     * @return Draw
     */
    public function saveDraw(array $drawData): Draw
    {
        try {
            // Use updateOrCreate to handle duplicates gracefully
            $draw = Draw::updateOrCreate(
                [
                    'lottery_type_id' => $drawData['lottery_type_id'],
                    'draw_date' => $drawData['draw_date'],
                    'draw_number' => $drawData['draw_number'],
                ],
                [
                    'numbers' => $drawData['numbers'],
                    'bonus_number' => $drawData['bonus_number'],
                    'prize_breakdown' => $drawData['prize_breakdown'],
                    'total_sales' => $drawData['total_sales'],
                    'jackpot_amount' => $drawData['jackpot_amount'],
                    'metadata' => $drawData['metadata'],
                ]
            );

            Log::info("Draw saved successfully", [
                'lottery_code' => $drawData['lottery_code'],
                'draw_date' => $drawData['draw_date'],
                'draw_number' => $drawData['draw_number'],
            ]);

            return $draw;
        } catch (\Exception $e) {
            Log::error("Failed to save draw", [
                'error' => $e->getMessage(),
                'draw_data' => $drawData,
            ]);
            throw $e;
        }
    }
}
