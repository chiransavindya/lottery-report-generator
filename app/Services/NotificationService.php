<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification to Super Admin when a duplicate draw is uploaded.
     *
     * @param array $updateInfo Contains lottery_code, lottery_name, draw_date, draw_number, operator_name
     * @return void
     */
    public function notifySuperAdminOfDuplicateUpload(array $updateInfo): void
    {
        try {
            // Get all super admins
            $superAdmins = User::where('role', 'super_admin')->get();

            if ($superAdmins->isEmpty()) {
                Log::warning('No super admins found to notify about duplicate upload', $updateInfo);
                return;
            }

            // Log the notification
            Log::info('Duplicate draw uploaded - Notifying Super Admins', $updateInfo);

            // Send email to each super admin
            foreach ($superAdmins as $admin) {
                $this->sendDuplicateEmail($admin, $updateInfo);
            }

            // Optionally: Create in-system notification (if you have notifications table)
            // $this->createSystemNotification($updateInfo);

        } catch (\Exception $e) {
            Log::error('Failed to send duplicate upload notification', [
                'error' => $e->getMessage(),
                'update_info' => $updateInfo,
            ]);
        }
    }

    /**
     * Send email notification about duplicate upload.
     *
     * @param User $admin
     * @param array $updateInfo
     * @return void
     */
    protected function sendDuplicateEmail(User $admin, array $updateInfo): void
    {
        try {
            Mail::send('emails.duplicate_upload', $updateInfo, function ($message) use ($admin, $updateInfo) {
                $message->to($admin->email, $admin->name)
                    ->subject("LRMS Alert: Duplicate Draw Updated - {$updateInfo['lottery_name']} ({$updateInfo['draw_date']})");
            });

            Log::info("Duplicate notification email sent to {$admin->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send email to {$admin->email}", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification about incomplete batch upload.
     *
     * @param User $operator
     * @param array $incompleteBuckets
     * @return void
     */
    public function notifyOperatorOfIncompleteBatch(User $operator, array $incompleteBuckets): void
    {
        try {
            Log::info('Incomplete batch uploaded', [
                'operator' => $operator->name,
                'buckets' => $incompleteBuckets,
            ]);

            // You can send an email if needed
            // Mail::send('emails.incomplete_batch', [...], ...);

        } catch (\Exception $e) {
            Log::error('Failed to send incomplete batch notification', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification when Smart Proceed is used.
     *
     * @param User $operator
     * @param array $completeBuckets
     * @param array $droppedBuckets
     * @return void
     */
    public function notifySmartProceed(User $operator, array $completeBuckets, array $droppedBuckets): void
    {
        try {
            Log::info('Smart Proceed executed', [
                'operator' => $operator->name,
                'completed' => array_column($completeBuckets, 'date'),
                'dropped' => array_column($droppedBuckets, 'date'),
            ]);

            // Send notification to admins if needed
            $superAdmins = User::where('role', 'super_admin')->get();

            foreach ($superAdmins as $admin) {
                Mail::send('emails.smart_proceed', [
                    'operator_name' => $operator->name,
                    'operator_email' => $operator->email,
                    'complete_buckets' => $completeBuckets,
                    'dropped_buckets' => $droppedBuckets,
                ], function ($message) use ($admin) {
                    $message->to($admin->email, $admin->name)
                        ->subject('LRMS: Smart Proceed - Incomplete Data Dropped');
                });
            }

        } catch (\Exception $e) {
            Log::error('Failed to send Smart Proceed notification', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
