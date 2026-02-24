<?php

namespace App\Jobs;

use App\Models\UploadFile;
use App\Services\XmlParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessXmlFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public UploadFile $uploadFile
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(XmlParserService $xmlParser): void
    {
        try {
            Log::info("Processing XML file", [
                'file_id' => $this->uploadFile->id,
                'filename' => $this->uploadFile->filename,
            ]);

            // Update status to processing
            $this->uploadFile->update(['status' => 'processing']);

            // Parse the XML file (pass original filename for logging purposes)
            $drawData = $xmlParser->parseXmlFile(
                $this->uploadFile->file_path,
                $this->uploadFile->original_filename
            );

            // Save the draw to database
            $draw = $xmlParser->saveDraw($drawData);

            // Update file status to completed
            $this->uploadFile->update([
                'status' => 'completed',
                'error_message' => null,
            ]);

            // DELETE THE FILE TO SAVE SPACE
            if (file_exists($this->uploadFile->file_path)) {
                unlink($this->uploadFile->file_path);
                Log::info("Deleted XML file to save space", ['file' => $this->uploadFile->file_path]);
            }

            // Update batch counters
            $batch = $this->uploadFile->batch;
            if ($batch) {
                $batch->increment('processed_files');

                // Check if batch is complete
                if ($batch->processed_files + $batch->failed_files >= $batch->total_files) {
                    $batch->update(['status' => 'completed']);
                }
            }

            Log::info("XML file processed successfully", [
                'file_id' => $this->uploadFile->id,
                'draw_id' => $draw->id,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to process XML file", [
                'file_id' => $this->uploadFile->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Update file status to failed
            $this->uploadFile->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            // Update batch counters
            $batch = $this->uploadFile->batch;
            if ($batch) {
                $batch->increment('failed_files');

                // Check if batch is complete
                if ($batch->processed_files + $batch->failed_files >= $batch->total_files) {
                    $batch->update(['status' => 'completed_with_errors']);
                }
            }

            // Re-throw to allow retry logic
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("XML processing job failed permanently", [
            'file_id' => $this->uploadFile->id,
            'error' => $exception->getMessage(),
        ]);

        // Update file status
        $this->uploadFile->update([
            'status' => 'failed',
            'error_message' => 'Job failed after ' . $this->tries . ' attempts: ' . $exception->getMessage(),
        ]);
    }
}
