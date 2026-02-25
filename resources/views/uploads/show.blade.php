@extends('layouts.app')

@section('title', 'Batch #' . $batch->id . ' Details')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Batch #{{ $batch->id }} - Processing Status</h2>
        <a href="{{ route('uploads.index') }}" class="btn btn-secondary">← Back to Uploads</a>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 25px; background: #f9fafb;">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Uploaded By</div>
                    <div class="summary-value">{{ $batch->user->name }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Upload Date</div>
                    <div class="summary-value">{{ $batch->created_at->format('Y-m-d H:i:s') }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Status</div>
                    <div class="summary-value">
                        <span class="badge badge-{{ $batch->status }}">{{ ucfirst($batch->status) }}</span>
                    </div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Files</div>
                    <div class="summary-value">{{ $batch->total_files }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Processed</div>
                    <div class="summary-value" style="color: var(--success-color);">{{ $batch->processed_files }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Failed</div>
                    <div class="summary-value" style="color: var(--primary-color);">{{ $batch->failed_files }}</div>
                </div>
            </div>

            @if($batch->status === 'processing' || $batch->status === 'pending')
                <div class="progress-bar-container">
                    <div class="progress-bar">
                        <div class="progress-fill"
                            style="width: {{ ($batch->processed_files + $batch->failed_files) / $batch->total_files * 100 }}%">
                        </div>
                    </div>
                    <div class="progress-text">
                        {{ $batch->processed_files + $batch->failed_files }} / {{ $batch->total_files }} files processed
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if(!empty($date_buckets))
        <div class="date-buckets-section">
            <h3 style="margin-top: 30px; margin-bottom: 15px;">Date Buckets</h3>
            @foreach($date_buckets as $date => $bucket)
                <div class="card {{ $bucket['is_complete'] ? 'bucket-complete' : 'bucket-incomplete' }}"
                    style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4 style="margin: 0; color: var(--text-color);">
                                {{ $date }}
                                @if($bucket['is_complete'])
                                    <span style="color: var(--success-color);">Complete</span>
                                @else
                                    <span style="color: var(--primary-color);">Incomplete</span>
                                @endif
                            </h4>
                            <p style="margin: 5px 0; color: var(--text-light);">
                                {{ $bucket['file_count'] }}/{{ $bucket['required_count'] }} files
                            </p>
                        </div>
                        <div>
                            @foreach($bucket['lottery_codes'] as $code)
                                <span class="badge badge-info" style="margin: 2px;">{{ $code }}</span>
                            @endforeach
                        </div>
                    </div>
                    @if(!$bucket['is_complete'] && !empty($bucket['missing_lotteries']))
                        <div class="alert alert-error" style="margin-top: 10px; margin-bottom: 0;">
                            <strong>Missing:</strong>
                            @foreach($bucket['missing_lotteries'] as $missing)
                                {{ $missing['name_en'] }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <h3 style="margin-top: 30px; margin-bottom: 15px;">File Processing Details</h3>

    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Filename</th>
                        <th>Size</th>
                        <th>Status</th>
                        <th>Result</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batch->files as $index => $file)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $file->original_filename }}</td>
                            <td>{{ number_format($file->file_size / 1024, 2) }} KB</td>
                            <td>
                                <span class="badge badge-{{ $file->status }}">
                                    @if($file->status === 'pending')
                                        Pending
                                    @elseif($file->status === 'processing')
                                        Processing
                                    @elseif($file->status === 'completed')
                                        Completed
                                    @else
                                        Failed
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if($file->status === 'completed')
                                    <span style="color: var(--success-color);">Draw saved successfully</span>
                                @elseif($file->status === 'processing')
                                    <span style="color: var(--secondary-color);">Processing...</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($file->error_message)
                                    <span
                                        style="color: var(--primary-color); font-size: 12px;">{{ Str::limit($file->error_message, 100) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($batch->status === 'processing' || $batch->status === 'pending')
        <div class="alert alert-info" style="text-align: center; margin-top: 20px;">
            <p>This page auto-refreshes every 3 seconds while processing...</p>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            font-size: 13px;
            color: var(--text-light);
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-color);
        }

        .progress-bar-container {
            margin-top: 20px;
        }

        .progress-bar {
            height: 30px;
            background: #e5e7eb;
            border-radius: 15px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            transition: width 0.5s ease;
        }

        .progress-text {
            text-align: center;
            margin-top: 8px;
            color: var(--text-light);
            font-size: 14px;
        }

        .bucket-complete {
            border: 2px solid var(--success-color);
            background: #f0fdf4;
        }

        .bucket-incomplete {
            border: 2px solid var(--primary-color);
            background: #fef2f2;
        }
    </style>
@endpush

@push('scripts')
    @if($batch->status === 'processing' || $batch->status === 'pending')
        <script>
            // Auto-refresh every 3 seconds while processing
            setTimeout(() => {
                location.reload();
            }, 3000);
        </script>
    @endif
@endpush