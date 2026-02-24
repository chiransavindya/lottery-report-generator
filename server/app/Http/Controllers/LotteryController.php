<?php

namespace App\Http\Controllers;

use App\Models\Lottery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LotteryController extends Controller
{
    // Path for storing XML files
    const XML_STORAGE_PATH = 'xml_files';

    // Upload XML files and save lottery data
    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:xml|max:2048',
        ]);

        $lotteriesData = [];

        foreach ($request->file('files') as $file) {
            try {
                $path = $file->store(self::XML_STORAGE_PATH);
                $xml = @simplexml_load_file(Storage::path($path));

                if ($xml === false) {
                    Log::warning("Invalid XML format in file: {$file->getClientOriginalName()}");
                    continue;
                }

                $data = json_decode(json_encode($xml), true);

                // Log the data structure for debugging
                Log::info("Processing file: {$file->getClientOriginalName()}", [
                    'lottery_name' => $data['name'] ?? 'Unknown',
                    'has_number' => isset($data['number']),
                    'has_date' => isset($data['date']),
                    'has_balls' => isset($data['balls']['ball']),
                    'data_structure' => array_keys($data)
                ]);

                // Validate required data and extract lottery information
                if (!isset($data['number'], $data['name'], $data['date'])) {
                    Log::warning("Missing required fields in file: {$file->getClientOriginalName()}", [
                        'missing_fields' => array_diff(['number', 'name', 'date'], array_keys($data))
                    ]);
                    continue;
                }

                // Validate XML <date> is today's date
                // COMMENTED OUT: Accept any date from XML without validation
                // try {
                //     $xmlDate = Carbon::parse($data['date']);
                //     if (!$xmlDate->isSameDay(Carbon::today())) {
                //         Log::warning("XML date is not today for file: {$file->getClientOriginalName()}", [
                //             'xml_date' => $data['date'],
                //             'today' => Carbon::today()->toDateString()
                //         ]);
                //         // Skip processing this file
                //         continue;
                //     }
                // } catch (\Exception $e) {
                //     Log::warning("Unable to parse date in file: {$file->getClientOriginalName()} - " . $e->getMessage());
                //     continue;
                // }

                // Check if balls exist, but don't require them for all lottery types
                if (!isset($data['balls']['ball'])) {
                    Log::info("No balls found in file: {$file->getClientOriginalName()}, proceeding with other data");
                }

                $lotteriesData[] = $this->prepareLotteryData($data, $file->getClientOriginalName());
            } catch (\Exception $e) {
                Log::error("Error processing file {$file->getClientOriginalName()}: " . $e->getMessage());
                continue;
            }
        }

        if (!empty($lotteriesData)) {
            try {
                Lottery::insert($lotteriesData);
                Log::info("Successfully inserted " . count($lotteriesData) . " lottery records");
            } catch (\Exception $e) {
                Log::error("Error inserting lottery data: " . $e->getMessage());
                return redirect()->route('report')->with('error', 'Error saving lottery data: ' . $e->getMessage());
            }
        }

        return redirect()->route('report')->with('success', 'Files uploaded and processed successfully.');
    }

    // Fetch all lotteries for the report page (using actual dates from XML files)
    public function report()
    {
        $lotteries = Lottery::orderBy('date', 'desc')
                             ->get();

        return inertia('Report', ['lotteries' => $lotteries]);
    }

    // Fetch lotteries by name (using actual dates from XML files)
    public function fetchByName(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $lotteries = Lottery::where('name', $request->name)
                             ->orderBy('date', 'desc')
                             ->get();

        return response()->json($lotteries);
    }

    // Prepare lottery data for database insertion
    private function prepareLotteryData(array $data, string $fileName)
    {
        // Handle different ball structures - some lotteries might have different formats
        $balls = [];
        if (isset($data['balls']['ball'])) {
            $balls = is_array($data['balls']['ball']) ? $data['balls']['ball'] : [$data['balls']['ball']];
        } elseif (isset($data['balls'])) {
            // Handle case where balls might be direct children
            $balls = is_array($data['balls']) ? $data['balls'] : [$data['balls']];
        }


        $lotteryName = $data['name'] ?? '';

        // Handle Kapruka FIRST before any other processing
        // Kapruka needs special handling: sort winning numbers only, keep letter and super number in position
        if (strcasecmp(trim($lotteryName), 'Kapruka') === 0 && !empty($balls)) {
            $balls = $this->processKaprukaBalls($balls);
        } else {
            // For all other lotteries, check if they should be processed
            $excludedLotteries = ['Jaya sampatha', 'Supiri dhana sampatha'];
            $shouldProcess = true;

            foreach ($excludedLotteries as $excluded) {
                if (strcasecmp(trim($lotteryName), $excluded) === 0) {
                    $shouldProcess = false;
                    break;
                }
            }

            if ($shouldProcess && !empty($balls)) {
                $balls = $this->processStandardBalls($balls);
            }
        }

        // Extract next super value with better error handling
        $nextSuper = null;
        
        // For Jaya Sampatha and Sasiri, use <total> tag as next super jackpot
        $lotteriesUsingTotal = ['Jaya Sampatha', 'SASIRI', 'Sasiri'];
        $usesTotalForSuper = false;
        
        foreach ($lotteriesUsingTotal as $lotteryCheck) {
            if (strcasecmp(trim($lotteryName), $lotteryCheck) === 0) {
                $usesTotalForSuper = true;
                break;
            }
        }
        
        if ($usesTotalForSuper && isset($data['total'])) {
            // For Jaya Sampatha and Sasiri, get next super from <total> tag
            $nextSuper = is_string($data['total']) ? 
                (float) str_replace(',', '', $data['total']) : 
                (float) $data['total'];
        } elseif (isset($data['next']['super'])) {
            // For other lotteries, get next super from <next><super> tag
            $nextSuper = is_string($data['next']['super']) ? 
                (float) str_replace(',', '', $data['next']['super']) : 
                (float) $data['next']['super'];
        } elseif (isset($data['super'])) {
            // Fallback: Some lotteries might have super directly
            $nextSuper = is_string($data['super']) ? 
                (float) str_replace(',', '', $data['super']) : 
                (float) $data['super'];
        }
        // Extract special attributes with their dynamic labels
        $specialData1 = $this->extractSpecialValueWithLabel($data, 'SP_50,000_NO');
        $specialData2 = $this->extractSpecialValueWithLabel($data, 'SP_40_NO');
        $specialData3 = $this->extractSpecialValueWithLabel($data, 'SP_200_NO');
        $specialData4 = $this->extractSpecialValueWithLabel($data, 'SP_100,000_NO');
        
        // Extract values for backward compatibility
        $special1 = $specialData1['value'];
        $special2 = $specialData2['value'];
        $special3 = $specialData3['value'];
        $special4 = $specialData4['value'];
        // Extract total value with better handling
        $totalValue = null;
        if (isset($data['total'])) {
            $totalValue = is_string($data['total']) ? 
                (float) str_replace(',', '', $data['total']) : 
                (float) $data['total'];
        }
        // Extract prize data
        $prizeData = $this->extractPrizeData($data);
        // Log the processed data for debugging
        Log::info("Prepared lottery data for {$data['name']}", [
            'lottery_name' => $data['name'],
            'balls_count' => count($balls),
            'next_super' => $nextSuper,
            'total_value' => $totalValue,
            'special_values' => [$specialData1['value'], $specialData2['value'], $specialData3['value'], $specialData4['value']]
        ]);
        return [
            'name' => $data['name'] ?? null,
            'number' => $data['number'],
            'date' => isset($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') . ' 00:00:00' : null,
            'color' => isset($data['color']) ? ucwords($data['color']) : null,
            'next_date' => isset($data['next']['date']) ? Carbon::parse($data['next']['date'])->format('Y-m-d') : null,
            'next_super' => $nextSuper !== null ? number_format($nextSuper, 2, '.', '') : number_format(0, 2, '.', ''),
            'ball1' => $balls[0] ?? null,
            'ball2' => $balls[1] ?? null,
            'ball3' => $balls[2] ?? null,
            'ball4' => $balls[3] ?? null,
            'ball5' => $balls[4] ?? null,
            'ball6' => $balls[5] ?? null,
            'ball7' => $balls[6] ?? null,
            'special1' => $specialData1['value'],
            'special2' => $specialData2['value'],
            'special3' => $specialData3['value'],
            'special4' => $specialData4['value'],
            'special1_label' => $specialData1['label'],
            'special2_label' => $specialData2['label'],
            'special3_label' => $specialData3['label'],
            'special4_label' => $specialData4['label'],
            'total' => $totalValue,
            'count' => $prizeData['winner_count'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Process balls for Kapruka lottery
    // XML Input: [17, 52, 51, 74, R, 30]
    // Expected Output: [17, 51, 52, 74, R, 30]
    // Rule: Sort ONLY the winning numbers (before letter), keep R and super number in position
    private function processKaprukaBalls(array $balls)
    {
        Log::info("Kapruka - Input balls:", ['balls' => $balls]);

        $letterIndex = -1;

        // Step 1: Find the English letter position
        foreach ($balls as $index => $ball) {
            if (!is_numeric($ball)) {
                $letterIndex = $index;
                break;
            }
        }

        // If no letter found, just return the original balls
        if ($letterIndex === -1) {
            Log::warning("Kapruka - No letter found, returning original order");
            return $balls;
        }

        Log::info("Kapruka - Letter found at index:", ['index' => $letterIndex]);

        // Step 2: Separate winning numbers (BEFORE letter) from the rest
        $winningNumbers = [];
        $afterLetter = [];

        foreach ($balls as $index => $ball) {
            if ($index < $letterIndex) {
                // Numbers before the letter are winning numbers
                $winningNumbers[] = $ball;
            } elseif ($index >= $letterIndex) {
                // Letter and everything after (including super number) stay as is
                $afterLetter[] = $ball;
            }
        }

        Log::info("Kapruka - Before sorting:", [
            'winning_numbers' => $winningNumbers,
            'after_letter' => $afterLetter
        ]);

        // Step 3: Sort ONLY the winning numbers in ascending order
        sort($winningNumbers, SORT_NUMERIC);

        // Step 4: Merge sorted winning numbers with the letter and super number
        $result = array_merge($winningNumbers, $afterLetter);

        Log::info("Kapruka - Final output:", ['result' => $result]);

        return $result;
    }

    // Process balls for standard lotteries (sort numeric, keep letters at end)
    private function processStandardBalls(array $balls)
    {
        $numericBalls = [];
        $otherBalls = [];

        foreach ($balls as $ball) {
            if (is_numeric($ball)) {
                $numericBalls[] = $ball;
            } else {
                $otherBalls[] = $ball;
            }
        }

        sort($numericBalls, SORT_NUMERIC);

        return array_merge($numericBalls, $otherBalls);
    }

    // Extract specific prize data (total value and winner count) for the 200000.00 prize
    private function extractPrizeData(array $data)
    {
        $totalValue = 0;
        $winnerCount = 0;

        if (isset($data['results']['prize'])) {
            foreach ($data['results']['prize'] as $prize) {
                if ((float) $prize['value'] == 200000.00) {
                    $totalValue = (float) $prize['value'];
                    $winnerCount = (int) $prize['count'];
                    break; // Only process the first matching prize
                }
            }
        }

        return [
            'total_value' => $totalValue,
            'winner_count' => $winnerCount,
        ];
    }

    // Extract a specific special value from the attributes
    private function extractSpecialValue(array $data, string $key)
    {
        // Try multiple possible locations for special values
        $value = null;
        
        // Method 1: Check attributes.attribute structure
        if (isset($data['attributes']['attribute'])) {
            foreach ($data['attributes']['attribute'] as $attribute) {
                if (isset($attribute['key']) && $attribute['key'] === $key) {
                    $value = $attribute['valueen'] ?? $attribute['value'] ?? null;
                    break;
                }
            }
        }
        
        // Method 2: Check direct special fields
        if (!$value && isset($data['special'])) {
            if (is_array($data['special'])) {
                foreach ($data['special'] as $special) {
                    if (isset($special['key']) && $special['key'] === $key) {
                        $value = $special['value'] ?? null;
                        break;
                    }
                }
            }
        }
        
        // Method 3: Check results.prize structure for special values
        if (!$value && isset($data['results']['prize'])) {
            foreach ($data['results']['prize'] as $prize) {
                if (isset($prize['code']) && $prize['code'] === $key) {
                    $value = $prize['count'] ?? null;
                    break;
                }
            }
        }
        
        // Log the extraction attempt for debugging
        Log::debug("Extracting special value for key: {$key}", [
            'found_value' => $value,
            'data_structure' => array_keys($data)
        ]);
        
        return $value;
    }

    // Extract a specific special value with its dynamic label from the attributes
    private function extractSpecialValueWithLabel(array $data, string $key)
    {
        $value = null;
        $label = null;
        
        // Method 1: Check attributes.attribute structure
        if (isset($data['attributes']['attribute'])) {
            foreach ($data['attributes']['attribute'] as $attribute) {
                if (isset($attribute['key']) && $attribute['key'] === $key) {
                    $value = $attribute['valueen'] ?? $attribute['value'] ?? null;
                    // Extract amount from key (e.g., SP_50,000_NO -> 50,000)
                    $label = $this->extractLabelFromKey($key);
                    break;
                }
            }
        }
        
        // Method 2: Check direct special fields
        if (!$value && isset($data['special'])) {
            if (is_array($data['special'])) {
                foreach ($data['special'] as $special) {
                    if (isset($special['key']) && $special['key'] === $key) {
                        $value = $special['value'] ?? null;
                        $label = $this->extractLabelFromKey($key);
                        break;
                    }
                }
            }
        }
        
        // Method 3: Check results.prize structure for special values
        if (!$value && isset($data['results']['prize'])) {
            foreach ($data['results']['prize'] as $prize) {
                if (isset($prize['code']) && $prize['code'] === $key) {
                    $value = $prize['count'] ?? null;
                    $label = $this->extractLabelFromKey($key);
                    break;
                }
            }
        }
        
        // Log the extraction attempt for debugging
        Log::debug("Extracting special value with label for key: {$key}", [
            'found_value' => $value,
            'found_label' => $label,
            'data_structure' => array_keys($data)
        ]);
        
        return [
            'value' => $value,
            'label' => $label
        ];
    }

    // Extract prize label from key (e.g., SP_50,000_NO -> Rs. 50,000/-)
    private function extractLabelFromKey(string $key)
    {
        // Extract amount from key pattern like SP_50,000_NO or SP_40_NO
        if (preg_match('/SP_([0-9,]+)_NO/', $key, $matches)) {
            $amount = $matches[1];
            return "Rs. {$amount}/-";
        }
        
        // Fallback for any unrecognized patterns
        return null;
    }

    // Fetch the latest lottery by name (using actual dates from XML files)
    public function getLottery(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        // Normalize input for case-insensitive match
        $requestedName = trim($request->name);
        
        // Handle specific name mapping for Super Ball
        if (strcasecmp($requestedName, 'Superball') === 0) {
            $requestedName = 'Super Ball';
        }

        Log::info("getLottery called with name: '{$requestedName}'");
        // Get the most recent lottery by name (case-insensitive)
        $lottery = Lottery::whereRaw('LOWER(name) = LOWER(?)', [$requestedName])
                          ->latest()
                          ->first();
                          
        // If not found, log available lotteries for debugging
        if (!$lottery) {
            Log::info("No lottery found for {$requestedName}, searching available lotteries");
            // Log all available lottery names for debugging
            $allLotteries = Lottery::select('name')->distinct()->get();
            Log::info("Available lottery names in database:", $allLotteries->pluck('name')->toArray());
        }
        if (!$lottery) {
            Log::warning("No lottery found for name: {$requestedName}");
            return response()->json(['message' => 'Lottery not found'], 404);
        }
        Log::info("Found lottery for {$requestedName}", [
            'lottery_id' => $lottery->id,
            'date' => $lottery->date,
            'date_type' => gettype($lottery->date)
        ]);
        
        // Convert to array and add computed fields for dynamic labels
        $lotteryArray = $lottery->toArray();
        
        // Use dynamic labels from database if available, otherwise use fallback labels
        $lotteryArray['special1_label'] = $lotteryArray['special1_label'] ?? (!empty($lotteryArray['special1']) ? 'Rs. 50,000/-' : null);
        $lotteryArray['special2_label'] = $lotteryArray['special2_label'] ?? (!empty($lotteryArray['special2']) ? 'Rs. 40/-' : null);
        $lotteryArray['special3_label'] = $lotteryArray['special3_label'] ?? (!empty($lotteryArray['special3']) ? 'Rs. 200/-' : null);
        $lotteryArray['special4_label'] = $lotteryArray['special4_label'] ?? (!empty($lotteryArray['special4']) ? 'Rs. 100,000/-' : null);
        
        // Add computed field to control special section visibility
        $hasAnySpecial = !empty($lotteryArray['special1']) || !empty($lotteryArray['special2']) || 
                        !empty($lotteryArray['special3']) || !empty($lotteryArray['special4']);
        $hasPrizeContext = ((int) ($lotteryArray['count'] ?? 0)) > 0 || 
                          ((float) ($lotteryArray['total'] ?? 0)) > 0;
        $lotteryArray['show_special_section'] = $hasAnySpecial && $hasPrizeContext;
        
        return response()->json($lotteryArray);
    }
}