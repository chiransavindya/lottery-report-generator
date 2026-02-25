@extends('layouts.app')

@section('title', 'Generate New Report')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Generate New Report</h2>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">← Back to Reports</a>
    </div>

    @if($uploadBatches->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 15px; color: var(--text-color);">Select Date Bucket to Generate Report</h3>
            <p style="color: var(--text-light); margin-bottom: 20px;">
                Choose a complete date bucket (all 8 lotteries) to generate consolidated PDF reports in 3 languages.
            </p>

            @foreach($uploadBatches as $batch)
                @php
                    $dateBuckets = json_decode($batch->date_buckets, true) ?? [];
                @endphp

                @foreach($dateBuckets as $date => $bucket)
                    @php
                        $isComplete = $bucket['is_complete'] ?? false;
                        $fileCount = $bucket['file_count'] ?? 0;
                        $requiredCount = $bucket['required_count'] ?? 8;
                        $lotteryCodes = $bucket['lottery_codes'] ?? [];
                        $missingLotteries = $bucket['missing_lotteries'] ?? [];
                    @endphp

                    <div class="card {{ $isComplete ? 'bucket-complete' : 'bucket-incomplete' }}"
                        style="margin-bottom: 20px; border-top-width: 4px; border-top-style: solid; border-top-color: {{ $isComplete ? 'var(--success-color)' : 'var(--primary-color)' }};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <h4 style="margin: 0; color: var(--text-color); font-size: 18px;">
                                    {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                                    @if($isComplete)
                                        <span style="color: var(--success-color); margin-left: 10px; font-weight: 600;">Complete</span>
                                    @else
                                        <span style="color: var(--primary-color); margin-left: 10px; font-weight: 600;">Incomplete</span>
                                    @endif
                                </h4>
                                <p style="margin: 5px 0 0 0; color: var(--text-light); font-size: 14px;">
                                    {{ $fileCount }}/{{ $requiredCount }} lotteries • Batch #{{ $batch->id }}
                                </p>
                            </div>

                            @if($isComplete)
                                <form action="{{ route('reports.consolidated') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="draw_date" value="{{ $date }}">
                                    <button type="submit" class="btn btn-primary"
                                        style="padding: 10px 20px; font-weight: 600;">
                                        Generate Reports
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Lottery Codes Display -->
                        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 15px;">
                            @foreach($lotteryCodes as $code)
                                @php
                                    $lotteryConfig = config("lotteries.required_lotteries.{$code}");
                                    $lotteryName = is_array($lotteryConfig) ? $lotteryConfig['name_en'] : $code;
                                @endphp
                                <span class="badge badge-info">
                                    {{ $code }} - {{ $lotteryName }}
                                </span>
                            @endforeach
                        </div>

                        @if(!$isComplete && !empty($missingLotteries))
                            <div class="alert alert-error" style="margin-bottom: 0;">
                                <strong>Missing:</strong>
                                <span style="margin-left: 5px;">
                                    @foreach($missingLotteries as $code)
                                        @php
                                            $lotteryConfig = config("lotteries.required_lotteries.{$code}");
                                            $name = is_array($lotteryConfig) ? $lotteryConfig['name_en'] : $code;
                                        @endphp
                                        {{ $name }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </span>
                            </div>
                        @endif

                        @if($isComplete)
                            <div class="alert alert-success" style="margin-bottom: 0;">
                                <strong>Ready to Generate</strong>
                                <p style="margin: 5px 0 0 0; font-size: 13px;">
                                    This batch contains all 8 required lotteries. Click "Generate Reports" to create PDF files in English, Sinhala, and Tamil.
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px; background: #f9fafb; border-radius: 10px;">
            <h3 style="color: var(--text-light); margin-bottom: 10px;">No Upload Batches Available</h3>
            <p style="color: var(--text-light); margin-bottom: 20px;">Upload lottery XML files first to generate reports.</p>
            <a href="{{ route('uploads.index') }}" class="btn btn-primary">Go to Upload Files</a>
        </div>
    @endif
@endsection
