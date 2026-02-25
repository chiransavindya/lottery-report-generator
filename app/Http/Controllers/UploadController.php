<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadXmlFilesRequest;
use App\Models\UploadBatch;
use App\Services\FileUploadService;
use App\Services\BatchValidationService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService,
        protected BatchValidationService $batchValidator,
        protected NotificationService $notificationService
    ) {
    }

    /**
     * Display upload form and batch history.
     */
    public function index()
    {
        $query = UploadBatch::with('user')->latest();

        // Operators can only see their own uploads
        if (auth()->user()->isOperator()) {
            $query->where('user_id', auth()->id());
        }

        $batches = $query->paginate(10);

        return view('uploads.index', compact('batches'));
    }

    /**
     * Process file uploads with new workflow (v2.0).
     * Supports up to 16 files, date grouping, and Smart Proceed logic.
     */
    public function store(UploadXmlFilesRequest $request)
    {
        $files = $request->file('files');

        // Validate lottery files (new logic: allow up to 16, group by date)
        $validation = $this->fileUploadService->validateLotteryFiles($files);

        if (!$validation['valid']) {
            return back()->withErrors($validation['errors'])->withInput();
        }

        $dateBuckets = $validation['date_buckets'];
        $updates = $validation['updates'];

        // Create batch and store files
        $batch = $this->fileUploadService->createBatch($files, $dateBuckets);

        // Send notifications for duplicate uploads
        if (!empty($updates)) {
            foreach ($updates as $update) {
                $this->notificationService->notifySuperAdminOfDuplicateUpload([
                    'lottery_code' => $update['lottery_code'],
                    'lottery_name' => $update['lottery_name'],
                    'draw_date' => $update['draw_date'],
                    'draw_number' => $update['draw_number'],
                    'operator_name' => auth()->user()->name,
                ]);
            }
        }

        // Dispatch processing jobs
        $this->fileUploadService->dispatchProcessingJobs($batch);

        // Prepare success message
        $message = "Successfully uploaded {$batch->total_files} files. Processing has started.";

        // Prepare date buckets for session (remove file objects to avoid serialization errors)
        $sessionDateBuckets = collect($dateBuckets)->map(function ($bucket) {
            unset($bucket['files']); // Remove file objects
            return $bucket;
        })->toArray();

        // Return with warnings if applicable
        if (!empty($validation['warnings'])) {
            return redirect()
                ->route('reports.index')
                ->with('success', $message)
                ->with('warnings', $validation['warnings'])
                ->with('date_buckets', $sessionDateBuckets);
        }

        return redirect()
            ->route('reports.index')
            ->with('success', $message)
            ->with('date_buckets', $sessionDateBuckets);
    }

    /**
     * View batch details and processing status.
     */
    public function show(UploadBatch $batch)
    {
        $batch->load(['files', 'user']);

        // Decode date_buckets JSON for display
        $dateBuckets = $batch->date_buckets ? json_decode($batch->date_buckets, true) : [];

        return view('uploads.show', compact('batch'))->with('date_buckets', $dateBuckets);
    }

    /**
     * Delete a batch (admin only).
     */
    public function destroy(UploadBatch $batch)
    {
        // Check if user is admin or super_admin
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Only administrators can delete batches.');
        }

        // Delete associated files from storage
        foreach ($batch->files as $file) {
            if (file_exists($file->file_path)) {
                unlink($file->file_path);
            }
        }

        // Delete batch and files (cascade will handle upload_files)
        $batch->delete();

        return redirect()
            ->route('uploads.index')
            ->with('success', 'Batch deleted successfully.');
    }
}
