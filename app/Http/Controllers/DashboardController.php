<?php

namespace App\Http\Controllers;

use App\Models\UploadBatch;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show dashboard based on user role.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        } elseif ($user->isAdmin()) {
            return $this->adminDashboard();
        } else {
            return $this->operatorDashboard();
        }
    }

    /**
     * Operator Dashboard.
     */
    protected function operatorDashboard()
    {
        $userId = auth()->id();
        $today = now()->startOfDay();

        // Today's upload stats
        $todayStats = [
            'uploads_today' => UploadBatch::where('user_id', $userId)
                ->whereDate('created_at', $today)
                ->count(),
            'files_processed_today' => UploadBatch::where('user_id', $userId)
                ->whereDate('created_at', $today)
                ->sum('total_files'),
            'pending_batches' => UploadBatch::where('user_id', $userId)
                ->whereIn('status', ['pending', 'processing'])
                ->count(),
        ];

        // Recent uploads
        $recentUploads = UploadBatch::with('lotteryType')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Recent reports
        $recentReports = Report::with(['draw.lotteryType'])
            ->where('generated_by', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.operator', compact('todayStats', 'recentUploads', 'recentReports'));
    }

    /**
     * Admin Dashboard.
     */
    protected function adminDashboard()
    {
        // Failed/problematic batches
        $failedBatches = UploadBatch::with(['user', 'lotteryType'])
            ->where(function ($query) {
                $query->where('status', 'failed')
                    ->orWhere('failed_files', '>', 0);
            })
            ->latest()
            ->take(10)
            ->get();

        // System overview stats
        $stats = [
            'total_uploads' => UploadBatch::count(),
            'published_reports' => Report::where('status', 'published')->count(),
            'failed_batches' => UploadBatch::where('status', 'failed')->count(),
            'total_files_processed' => UploadBatch::sum('processed_files'),
            'active_operators' => User::where('role', 'operator')->where('is_active', true)->count(),
        ];

        // Recent activity
        $recentActivity = UploadBatch::with(['user', 'lotteryType'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.admin', compact('failedBatches', 'stats', 'recentActivity'));
    }

    /**
     * Super Admin Dashboard.
     */
    protected function superAdminDashboard()
    {
        // System health metrics
        $systemHealth = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'total_uploads' => UploadBatch::count(),
            'total_reports' => Report::count(),
            'published_reports' => Report::where('status', 'published')->count(),
            'storage_usage' => $this->getStorageUsage(),
        ];

        // User activity trends (last 7 days)
        $userActivity = User::select('role', DB::raw('count(*) as count'))
            ->where('is_active', true)
            ->groupBy('role')
            ->get();

        // Recent user logins (show all users, ordered by last login)
        $recentLogins = User::orderByDesc('last_login_at')
            ->take(10)
            ->get();

        // Upload trends (last 7 days)
        $uploadTrends = UploadBatch::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count'),
            DB::raw('sum(total_files) as files')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // System-wide stats
        $stats = [
            'total_batches' => UploadBatch::count(),
            'successful_batches' => UploadBatch::where('status', 'completed')->count(),
            'failed_batches' => UploadBatch::where('status', 'failed')->count(),
            'total_files_processed' => UploadBatch::sum('total_files'),
            'avg_success_rate' => $this->calculateSuccessRate(),
        ];

        return view('dashboard.super_admin', compact(
            'systemHealth',
            'userActivity',
            'recentLogins',
            'uploadTrends',
            'stats'
        ));
    }

    /**
     * Get storage usage (simplified).
     */
    protected function getStorageUsage(): string
    {
        $path = storage_path('app');
        if (function_exists('disk_free_space')) {
            $bytes = disk_free_space($path);
            return $this->formatBytes($bytes);
        }
        return 'N/A';
    }

    /**
     * Calculate average success rate.
     */
    protected function calculateSuccessRate(): float
    {
        $total = UploadBatch::sum('total_files');
        $processed = UploadBatch::sum('processed_files');

        if ($total === 0 || $total === null) {
            return 0;
        }

        return round(($processed / $total) * 100, 2);
    }

    /**
     * Format bytes to human readable.
     */
    protected function formatBytes($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
