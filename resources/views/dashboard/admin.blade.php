@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; margin-bottom: 5px;">Admin Dashboard</h2>
            <p style="color: var(--text-soft);">System Overview & Management</p>
        </div>
        <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-soft); background: #fff; padding: 8px 16px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    <!-- System Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div class="card" style="margin-bottom: 0; padding: 25px; border-left: 4px solid var(--dlb-red);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                Total Uploads
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--text-main);">{{ $stats['total_uploads'] }}</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 25px; border-left: 4px solid var(--dlb-green);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                Published Reports
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--dlb-green);">{{ $stats['published_reports'] }}</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 25px; border-left: 4px solid var(--dlb-orange);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                Failed Batches
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--dlb-red);">{{ $stats['failed_batches'] }}</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 25px; border-left: 4px solid var(--dlb-yellow);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                Files Processed
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--dlb-orange);">
                {{ number_format($stats['total_files_processed']) }}
            </div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 25px; border-left: 4px solid #06b6d4;">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                Active Operators
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #06b6d4;">{{ $stats['active_operators'] }}</div>
        </div>
    </div>

    <!-- Failed Batches -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 700; font-size: 1.2rem;">Failed/Problematic Batches</h3>
            <a href="{{ route('uploads.index') }}" class="btn-outline" style="font-size: 0.85rem; padding: 6px 12px; text-decoration: none;">View All</a>
        </div>

        @if($failedBatches->count() > 0)
            <div class="table-container">
                <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: var(--bg-gray); text-align: left;">
                            <th style="padding: 12px 15px; border-radius: 8px 0 0 8px; font-weight: 600; color: var(--text-soft);">Batch ID</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Lottery</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Uploaded By</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Total Files</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Failed</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Status</th>
                            <th style="padding: 12px 15px; border-radius: 0 8px 8px 0; font-weight: 600; color: var(--text-soft);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($failedBatches as $batch)
                            <tr>
                                <td style="padding: 15px; border-bottom: 1px solid #eee; font-weight: 600;">#{{ $batch->id }}</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">{{ $batch->lotteryType?->name_en ?? 'N/A' }}</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">{{ $batch->user?->name ?? 'Unknown' }}</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">{{ $batch->total_files }}</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee; color: var(--dlb-red); font-weight: 800;">{{ $batch->failed_files }}</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    <span style="background: #ffebee; color: var(--dlb-red); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                        {{ ucfirst($batch->status) }}
                                    </span>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    <a href="{{ route('uploads.show', $batch) }}" style="color: var(--dlb-red); text-decoration: none; font-weight: 600; font-size: 0.9rem;">Investigate &rarr;</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 40px; text-align: center; color: var(--text-soft); background: var(--bg-gray); border-radius: 12px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 10px; opacity: 0.5;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <p>No failed batches found. System is healthy!</p>
            </div>
        @endif
    </div>

    <!-- Analytics Section -->
    <div class="card" style="margin-bottom: 30px;">
        <h3 style="font-weight: 700; font-size: 1.2rem; margin-bottom: 20px;">Analytics Overview</h3>
        <div style="position: relative; height: 350px; width: 100%;">
            <canvas id="systemOverviewChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <h3 style="font-weight: 700; font-size: 1.2rem; margin-bottom: 20px;">Recent System Activity</h3>
        <div class="table-container">
            <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background: var(--bg-gray); text-align: left;">
                        <th style="padding: 12px 15px; border-radius: 8px 0 0 8px; font-weight: 600; color: var(--text-soft);">Batch ID</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Draw Date</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Uploaded By</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Files</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Status</th>
                        <th style="padding: 12px 15px; border-radius: 0 8px 8px 0; font-weight: 600; color: var(--text-soft);">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentActivity as $activity)
                        <tr>
                            <td style="padding: 15px; border-bottom: 1px solid #eee; font-weight: 600; color: var(--text-soft);">#{{ $activity->id }}</td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">{{ $activity->draw_date ? \Carbon\Carbon::parse($activity->draw_date)->format('Y-m-d') : 'N/A' }}</td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">{{ $activity->user?->name ?? 'Unknown' }}</td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">{{ $activity->total_files }} ({{ $activity->processed_files }} ✓)</td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                @if($activity->status === 'completed')
                                    <span style="background: #e0f2f1; color: var(--dlb-green); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Completed</span>
                                @elseif($activity->status === 'processing')
                                    <span style="background: #e3f2fd; color: #0288d1; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Processing</span>
                                @elseif($activity->status === 'failed')
                                    <span style="background: #ffebee; color: var(--dlb-red); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Failed</span>
                                @else
                                    <span style="background: #fff8e1; color: var(--dlb-orange); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Pending</span>
                                @endif
                            </td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee; color: var(--text-soft); font-size: 0.85rem;">{{ $activity->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('systemOverviewChart').getContext('2d');

            const stats = {
                published: {{ $stats['published_reports'] }},
                failed: {{ $stats['failed_batches'] }}
            };

            // Using DLB Brand Colors
            const colors = {
                green: '#2a9d8f',
                red: '#e63946',
                orange: '#fca311',
                text: '#2b2d42'
            };

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Published Reports', 'Failed Batches'],
                    datasets: [{
                        label: 'System Metrics',
                        data: [stats.published, stats.failed],
                        backgroundColor: [
                            colors.green, 
                            colors.red
                        ],
                        borderColor: [
                            colors.green,
                            colors.red
                        ],
                        borderWidth: 0,
                        borderRadius: 8,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Current System Status',
                            font: {
                                size: 16,
                                family: "'Inter', sans-serif",
                                weight: 600
                            },
                            color: colors.text
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f0f0f0',
                                borderDash: [5, 5]
                            },
                            ticks: {
                                stepSize: 1,
                                font: {
                                    family: "'Inter', sans-serif"
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    weight: 500
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                }
            });
        });
    </script>
@endpush
