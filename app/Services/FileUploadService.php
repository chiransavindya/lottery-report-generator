<?php

namespace App\Services;

use App\Models\Draw;
use App\Models\LotteryType;
use App\Models\UploadBatch;
use App\Models\UploadFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    protected BatchValidationService $batchValidator;
    protected XmlParserService $xmlParser;

    public function __construct()
    {
        $this->batchValidator = new BatchValidationService();
        $this->xmlParser = new XmlParserService();
    }

    /**
     * Validate that uploaded files meet requirements.
     * New Logic: Allow up to 16 files, group by date, check completeness per bucket.
     *
     * @param array $files Array of UploadedFile objects
     * @return array ['valid' => bool, 'errors' => array, 'warnings' => array, 'date_buckets' => array, 'updates' => array]
     */
    public function validateLotteryFiles(array $files): array
    {
        $errors = [];
        $warnings = [];
        $updates = [];

        // Check max 16 files
        if (count($files) > 16) {
            $errors[] = 'You can upload a maximum of 16 XML files at once. You uploaded ' . count($files) . ' files.';
            return [
                'valid' => false,
                'errors' => $errors,
                'warnings' => $warnings,
                'date_buckets' => [],
                'updates' => [],
            ];
        }

        // Parse each file to extract lottery code and draw date from XML content
        $filesData = [];
        foreach ($files as $file) {
            $filename = $file->getClientOriginalName();

            // Parse XML to extract lottery code from content (v2.0 - more robust)
            try {
                $tempPath = $file->store('temp');
                $fullPath = Storage::path($tempPath);
                $drawData = $this->xmlParser->parseXmlFile($fullPath, $filename);

                $code = $drawData['lottery_code'];

                // Verify lottery code exists in config
                if (!in_array($code, config('lotteries.required_codes'))) {
                    $errors[] = "Unknown lottery code '{$code}' found in {$filename}. Valid codes: " . implode(', ', config('lotteries.required_codes'));
                    Storage::delete($tempPath);
                    continue;
                }

                // Check if this will update an existing draw
                $existingCheck = $this->batchValidator->checkForExistingDraw($code, $drawData['draw_date']);
                if ($existingCheck['is_update']) {
                    $updates[] = [
                        'lottery_code' => $code,
                        'lottery_name' => $this->batchValidator->getLotteryName($code, 'en'),
                        'draw_date' => $drawData['draw_date'],
                        'draw_number' => $drawData['draw_number'],
                        'filename' => $filename,
                    ];
                }

                $filesData[] = [
                    'file' => $file,
                    'lottery_code' => $code,
                    'draw_date' => $drawData['draw_date'],
                    'draw_number' => $drawData['draw_number'],
                    'filename' => $filename,
                    'is_update' => $existingCheck['is_update'],
                ];

                // Clean up temp file
                Storage::delete($tempPath);
            } catch (\Exception $e) {
                $errors[] = "Failed to parse {$filename}: " . $e->getMessage();
                continue;
            }
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'errors' => $errors,
                'warnings' => $warnings,
                'date_buckets' => [],
                'updates' => [],
            ];
        }

        // Group files by date (Date Bucket System)
        $dateBuckets = $this->batchValidator->groupFilesByDate($filesData);

        // Check for duplicates and generate warnings
        foreach ($dateBuckets as $bucket) {
            if (!empty($bucket['duplicate_lotteries'])) {
                $duplicateCodes = implode(', ', array_unique($bucket['duplicate_lotteries']));
                $errors[] = "Duplicate lottery types found for date {$bucket['date']}: {$duplicateCodes}. Each lottery should appear only once per date.";
            }
        }

        // Generate warnings for incomplete buckets
        foreach ($dateBuckets as $bucket) {
            if (!$bucket['is_complete']) {
                $missingNames = $this->batchValidator->formatMissingLotteries($bucket['missing_lotteries'], 'en');
                $warnings[] = "⚠️ Incomplete batch for {$bucket['date']} ({$bucket['file_count']}/{$bucket['required_count']}). Missing: {$missingNames}";
            }
        }

        // Generate warnings for updates
        if (!empty($updates)) {
            $updateList = collect($updates)->map(function ($update) {
                return "{$update['lottery_name']} ({$update['draw_date']})";
            })->implode(', ');
            $warnings[] = "⚠️ The following files will UPDATE existing draws: {$updateList}";
        }

        $valid = empty($errors);

        return [
            'valid' => $valid,
            'errors' => $errors,
            'warnings' => $warnings,
            'date_buckets' => $dateBuckets,
            'updates' => $updates,
        ];
    }

    /**
     * Check if any of the uploaded files will update existing draws.
     * (This method is deprecated - now handled in validateLotteryFiles)
     *
     * @param array $files
     * @return array ['will_update' => bool, 'existing_draws' => array, 'warnings' => array]
     */
    public function checkExistingDraws(array $files): array
    {
        // This is now handled in validateLotteryFiles
        return [
            'will_update' => false,
            'existing_draws' => [],
            'warnings' => [],
        ];
    }

    /**
     * Create a new upload batch and store files with date bucket information.
     *
     * @param array $files
     * @param array $dateBuckets
     * @return UploadBatch
     */
    public function createBatch(array $files, array $dateBuckets): UploadBatch
    {
        $user = Auth::user();

        // Determine if this is a single-date or multi-date batch
        $dates = array_keys($dateBuckets);
        $primaryDate = count($dates) === 1 ? $dates[0] : null;

        // Check if all buckets are complete
        $allComplete = collect($dateBuckets)->every(fn($bucket) => $bucket['is_complete']);
        $missingLotteries = [];

        if (!$allComplete) {
            foreach ($dateBuckets as $bucket) {
                if (!$bucket['is_complete']) {
                    $missingLotteries[$bucket['date']] = $bucket['missing_lotteries'];
                }
            }
        }

        // Create batch record
        $batch = UploadBatch::create([
            'batch_name' => 'Batch-' . now()->format('YmdHis'),
            'user_id' => $user->id,
            'draw_date' => $primaryDate,
            'status' => 'pending',
            'total_files' => count($files),
            'processed_files' => 0,
            'failed_files' => 0,
            'is_complete' => $allComplete,
            'missing_lotteries' => !empty($missingLotteries) ? json_encode($missingLotteries) : null,
            'date_buckets' => json_encode($dateBuckets),
        ]);

        // Store each file
        foreach ($files as $file) {
            $this->storeFile($file, $batch);
        }

        return $batch;
    }

    /**
     * Store an individual file and create database record.
     * If duplicate checksum exists, update the existing record.
     *
     * @param UploadedFile $file
     * @param UploadBatch $batch
     * @return UploadFile
     */
    public function storeFile(UploadedFile $file, UploadBatch $batch): UploadFile
    {
        $originalName = $file->getClientOriginalName();

        // Generate checksum
        $checksum = hash_file('sha256', $file->getRealPath());

        // Check if file with same checksum already exists
        $existingFile = UploadFile::where('checksum', $checksum)->first();

        // Store file in organized directory structure
        // Format: lottery-uploads/{YYYY-MM-DD}/{batch_id}/filename.xml
        $date = now()->format('Y-m-d');
        $directory = "lottery-uploads/{$date}/{$batch->id}";
        $filePath = $file->storeAs($directory, $originalName);
        $fullPath = Storage::path($filePath);

        if ($existingFile) {
            // Log the duplicate file update for admin review
            \Log::warning('Duplicate file uploaded - updating existing record', [
                'checksum' => $checksum,
                'original_filename' => $originalName,
                'existing_file_id' => $existingFile->id,
                'existing_batch_id' => $existingFile->batch_id,
                'new_batch_id' => $batch->id,
                'uploaded_by' => auth()->user()->name ?? 'Unknown',
                'uploaded_at' => now()->toDateTimeString(),
            ]);

            // Create audit log entry
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'file_duplicate_update',
                'table_name' => 'upload_files',
                'record_id' => $existingFile->id,
                'changes' => json_encode([
                    'old_batch_id' => $existingFile->batch_id,
                    'new_batch_id' => $batch->id,
                    'old_file_path' => $existingFile->file_path,
                    'new_file_path' => $fullPath,
                    'checksum' => $checksum,
                    'original_filename' => $originalName,
                ]),
            ]);

            // Delete old file from storage
            if (file_exists($existingFile->file_path)) {
                @unlink($existingFile->file_path);
            }

            // Update existing record with new file location and batch
            $existingFile->update([
                'batch_id' => $batch->id,
                'original_filename' => $originalName,
                'file_path' => $fullPath,
                'file_size' => $file->getSize(),
                'status' => 'pending',
                'updated_at' => now(),
            ]);

            return $existingFile;
        }

        // Create new database record
        $uploadFile = UploadFile::create([
            'batch_id' => $batch->id,
            'filename' => Str::random(40) . '.xml', // Random name for security
            'original_filename' => $originalName,
            'file_path' => $fullPath,
            'file_size' => $file->getSize(),
            'checksum' => $checksum,
            'status' => 'pending',
        ]);

        return $uploadFile;
    }

    /**
     * Dispatch processing jobs for all files in a batch.
     *
     * @param UploadBatch $batch
     * @return void
     */
    public function dispatchProcessingJobs(UploadBatch $batch): void
    {
        $batch->update(['status' => 'processing']);

        foreach ($batch->files as $file) {
            \App\Jobs\ProcessXmlFile::dispatch($file);
        }
    }
}
