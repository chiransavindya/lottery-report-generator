<?php

namespace App\Services;

use App\Models\LotteryType;
use Illuminate\Support\Facades\Log;

class BatchValidationService
{
    /**
     * Group uploaded files by their draw date (Date Bucket System).
     *
     * @param array $files Array of file data with parsed draw_date and lottery_code
     * @return array Grouped by date with completeness info
     */
    public function groupFilesByDate(array $filesData): array
    {
        $dateBuckets = [];
        $requiredCodes = config('lotteries.required_codes');

        // Group files by date
        foreach ($filesData as $fileData) {
            $date = $fileData['draw_date'];
            $code = $fileData['lottery_code'];

            if (!isset($dateBuckets[$date])) {
                $dateBuckets[$date] = [
                    'date' => $date,
                    'files' => [],
                    'lottery_codes' => [],
                    'is_complete' => false,
                    'missing_lotteries' => [],
                    'duplicate_lotteries' => [],
                ];
            }

            // Check for duplicates within the same date
            if (in_array($code, $dateBuckets[$date]['lottery_codes'])) {
                $dateBuckets[$date]['duplicate_lotteries'][] = $code;
            }

            $dateBuckets[$date]['files'][] = $fileData;
            $dateBuckets[$date]['lottery_codes'][] = $code;
        }

        // Check completeness for each bucket
        foreach ($dateBuckets as $date => &$bucket) {
            $bucket['missing_lotteries'] = $this->findMissingLotteries($bucket['lottery_codes']);
            $bucket['is_complete'] = empty($bucket['missing_lotteries']);
            $bucket['file_count'] = count($bucket['lottery_codes']);
            $bucket['required_count'] = count($requiredCodes);
        }

        return $dateBuckets;
    }

    /**
     * Find which required lotteries are missing from the provided codes.
     *
     * @param array $uploadedCodes
     * @return array Array of missing lottery info
     */
    public function findMissingLotteries(array $uploadedCodes): array
    {
        $requiredLotteries = config('lotteries.required_lotteries');
        $missing = [];

        foreach ($requiredLotteries as $code => $info) {
            if (!in_array($code, $uploadedCodes)) {
                $missing[] = [
                    'code' => $code,
                    'name_en' => $info['name_en'],
                    'name_si' => $info['name_si'],
                    'name_ta' => $info['name_ta'],
                ];
            }
        }

        return $missing;
    }

    /**
     * Validate if a batch can proceed with generation.
     * Returns validation result with Smart Proceed options if applicable.
     *
     * @param array $dateBuckets
     * @return array ['can_proceed' => bool, 'action' => string, 'complete_buckets' => array, 'incomplete_buckets' => array]
     */
    public function validateBatchForGeneration(array $dateBuckets): array
    {
        $completeBuckets = [];
        $incompleteBuckets = [];

        foreach ($dateBuckets as $bucket) {
            if ($bucket['is_complete']) {
                $completeBuckets[] = $bucket;
            } else {
                $incompleteBuckets[] = $bucket;
            }
        }

        // Determine the action
        if (empty($completeBuckets) && !empty($incompleteBuckets)) {
            // No complete buckets - cannot proceed
            return [
                'can_proceed' => false,
                'action' => 'block',
                'message' => 'All uploaded batches are incomplete. Please upload missing files.',
                'complete_buckets' => [],
                'incomplete_buckets' => $incompleteBuckets,
            ];
        } elseif (!empty($completeBuckets) && empty($incompleteBuckets)) {
            // All buckets complete - proceed normally
            return [
                'can_proceed' => true,
                'action' => 'proceed',
                'message' => 'All batches are complete. Ready to generate reports.',
                'complete_buckets' => $completeBuckets,
                'incomplete_buckets' => [],
            ];
        } else {
            // Mixed: some complete, some incomplete - trigger Smart Proceed
            return [
                'can_proceed' => true,
                'action' => 'smart_proceed',
                'message' => 'Some batches are incomplete. You can proceed with complete batches only.',
                'complete_buckets' => $completeBuckets,
                'incomplete_buckets' => $incompleteBuckets,
            ];
        }
    }

    /**
     * Get lottery name by code and language.
     *
     * @param string $code
     * @param string $language 'en', 'si', or 'ta'
     * @return string
     */
    public function getLotteryName(string $code, string $language = 'en'): string
    {
        $lotteries = config('lotteries.required_lotteries');

        if (isset($lotteries[$code])) {
            $key = "name_{$language}";
            return $lotteries[$code][$key] ?? $lotteries[$code]['name_en'];
        }

        return $code;
    }

    /**
     * Check if a file will update an existing draw.
     *
     * @param string $lotteryCode
     * @param string $drawDate
     * @return array ['is_update' => bool, 'existing_draw' => Draw|null]
     */
    public function checkForExistingDraw(string $lotteryCode, string $drawDate): array
    {
        $lotteryType = LotteryType::where('code', $lotteryCode)->first();

        if (!$lotteryType) {
            return ['is_update' => false, 'existing_draw' => null];
        }

        $existingDraw = \App\Models\Draw::where('lottery_type_id', $lotteryType->id)
            ->where('draw_date', $drawDate)
            ->first();

        return [
            'is_update' => $existingDraw !== null,
            'existing_draw' => $existingDraw,
        ];
    }

    /**
     * Format missing lotteries for display.
     *
     * @param array $missingLotteries
     * @param string $language
     * @return string
     */
    public function formatMissingLotteries(array $missingLotteries, string $language = 'en'): string
    {
        if (empty($missingLotteries)) {
            return '';
        }

        $names = array_map(function ($lottery) use ($language) {
            $key = "name_{$language}";
            return $lottery[$key] ?? $lottery['name_en'];
        }, $missingLotteries);

        return implode(', ', $names);
    }
}
