@extends('layouts.app')

@section('title', 'Operator Dashboard')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; margin-bottom: 5px;">Operator Dashboard</h2>
            <p style="color: var(--text-soft);">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-soft); background: #fff; padding: 8px 16px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    <!-- Quick Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div class="card" style="margin-bottom: 0; padding: 25px; border-left: 4px solid var(--dlb-red);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                Uploads Today
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--text-main);">{{ $todayStats['uploads_today'] }}</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 25px; border-left: 4px solid var(--dlb-green);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                Files Processed
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--dlb-green);">
                {{ $todayStats['files_processed_today'] }}
            </div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 25px; border-left: 4px solid var(--dlb-orange);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                Pending Batches
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--dlb-orange);">
                {{ $todayStats['pending_batches'] }}
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card" style="padding: 30px;">
        <h3 style="font-weight: 700; font-size: 1.2rem; margin-bottom: 20px;">Quick Actions</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="{{ route('uploads.index') }}" class="btn-outline" style="background: var(--dlb-red); color: white; border-color: var(--dlb-red); text-decoration: none;">
                Upload XML Files
            </a>
            <a href="{{ route('reports.create') }}" class="btn-outline" style="text-decoration: none;">
                Generate Report
            </a>
        </div>
    </div>

    <!-- Recent Uploads -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 700; font-size: 1.2rem;">Recent Uploads</h3>
            <a href="{{ route('uploads.index') }}" class="btn-outline" style="font-size: 0.85rem; padding: 6px 12px; text-decoration: none;">View All</a>
        </div>

        @if($recentUploads->count() > 0)
            <div class="table-container">
                <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: var(--bg-gray); text-align: left;">
                            <th style="padding: 12px 15px; border-radius: 8px 0 0 8px; font-weight: 600; color: var(--text-soft);">Batch ID</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Lottery</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Files</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Status</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Uploaded</th>
                            <th style="padding: 12px 15px; border-radius: 0 8px 8px 0; font-weight: 600; color: var(--text-soft);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUploads as $upload)
                            <tr>
                                <td style="padding: 15px; border-bottom: 1px solid #eee; font-weight: 600;">#{{ $upload->id }}</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">{{ $upload->lotteryType?->name_en ?? 'N/A' }}</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">{{ $upload->total_files }} ({{ $upload->processed_files }} ✓)</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    @if($upload->status === 'completed')
                                        <span style="background: #e0f2f1; color: var(--dlb-green); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Completed</span>
                                    @elseif($upload->status === 'processing')
                                        <span style="background: #e3f2fd; color: #0288d1; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Processing</span>
                                    @elseif($upload->status === 'failed')
                                        <span style="background: #ffebee; color: var(--dlb-red); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Failed</span>
                                    @else
                                        <span style="background: #fff8e1; color: var(--dlb-orange); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Pending</span>
                                    @endif
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee; color: var(--text-soft); font-size: 0.85rem;">{{ $upload->created_at->diffForHumans() }}</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    <a href="{{ route('uploads.show', $upload) }}" style="color: var(--dlb-green); text-decoration: none; font-weight: 600; font-size: 0.9rem;">View &rarr;</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 40px; text-align: center; color: var(--text-soft); background: var(--bg-gray); border-radius: 12px;">
                <p>No uploads yet. Start by uploading XML files!</p>
            </div>
        @endif
    </div>

    <!-- Recent Reports -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 700; font-size: 1.2rem;">Recent Reports</h3>
            <a href="{{ route('reports.index') }}" class="btn-outline" style="font-size: 0.85rem; padding: 6px 12px; text-decoration: none;">View All</a>
        </div>

        @if($recentReports->count() > 0)
            <div class="table-container">
                <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: var(--bg-gray); text-align: left;">
                            <th style="padding: 12px 15px; border-radius: 8px 0 0 8px; font-weight: 600; color: var(--text-soft);">ID</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Draw Date</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Status</th>
                            <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Generated</th>
                            <th style="padding: 12px 15px; border-radius: 0 8px 8px 0; font-weight: 600; color: var(--text-soft);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentReports as $report)
                            <tr>
                                <td style="padding: 15px; border-bottom: 1px solid #eee; font-weight: 600;">#{{ $report->id }}</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">{{ $report->draw->draw_date ? \Carbon\Carbon::parse($report->draw->draw_date)->format('Y-m-d') : 'N/A' }}</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    @if($report->status === 'published')
                                        <span style="background: #e0f2f1; color: var(--dlb-green); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Published</span>
                                    @else
                                        <span style="background: #fff8e1; color: var(--dlb-orange); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Draft</span>
                                    @endif
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee; color: var(--text-soft); font-size: 0.85rem;">{{ $report->created_at->diffForHumans() }}</td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    <a href="{{ route('reports.show', $report) }}" style="color: var(--dlb-green); text-decoration: none; font-weight: 600; font-size: 0.9rem;">View &rarr;</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 40px; text-align: center; color: var(--text-soft); background: var(--bg-gray); border-radius: 12px;">
                <p>No reports generated yet.</p>
            </div>
        @endif
    </div>
@endsection
