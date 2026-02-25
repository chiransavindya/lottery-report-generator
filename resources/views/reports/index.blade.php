@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>PDF Reports</h2>
        <a href="{{ route('uploads.index') }}" class="btn btn-primary">Upload Files</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <strong>{{ session('success') }}</strong>
        </div>
    @endif

    @if($uploadBatches->count() > 0)
        <div class="accordion-container">
            @foreach($uploadBatches as $drawDate => $batches)
                @php
                    $isFirst = $loop->first;
                    $dateFormatted = \Carbon\Carbon::parse($drawDate)->format('F d, Y');
                    $dateId = 'date-' . str_replace('-', '', $drawDate);

                    // Get all draws for this date from the batches
                    $draws = \App\Models\Draw::where('draw_date', $drawDate)
                        ->with('lotteryType')
                        ->orderBy('lottery_type_id')
                        ->get();

                    // Check if reports exist for this date
                    $hasReports = isset($existingReports[$drawDate]) && $existingReports[$drawDate]->count() > 0;
                @endphp

                <div class="accordion-item {{ $isFirst ? 'active' : '' }}" style="margin-bottom: 15px;">
                    <!-- Accordion Header -->
                    <div class="accordion-header" onclick="toggleAccordion('{{ $dateId }}')"
                        style="background: white;
                                                                                                                                            color: var(--text-color);
                                                                                                                                            padding: 20px 25px;
                                                                                                                                            border-radius: 10px;
                                                                                                                                            cursor: pointer;
                                                                                                                                            display: flex;
                                                                                                                                            justify-content: space-between;
                                                                                                                                            align-items: center;
                                                                                                                                            box-shadow: var(--shadow);
                                                                                                                                            border-left: 5px solid var(--primary-color);
                                                                                                                                            transition: all 0.3s;">
                        <div>
                            <h3 style="margin: 0; font-size: 20px; font-weight: 700;">
                                Draw Date: {{ $dateFormatted }}
                            </h3>
                            <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px; color: var(--text-light);">
                                {{ $draws->count() }} {{ Str::plural('lottery', $draws->count()) }}
                                @if($hasReports)
                                    • Reports Generated
                                @endif
                            </p>
                        </div>
                        <div style="display: flex; gap: 15px; align-items: center;">
                            @if($draws->count() >= 8)
                                @if(!$hasReports)
                                     <form action="{{ route('reports.consolidated') }}" method="POST" onclick="event.stopPropagation();">
                                        @csrf
                                        <input type="hidden" name="draw_date" value="{{ $drawDate }}">
                                        <button type="submit" class="btn btn-outline"
                                            style="padding: 10px 20px; font-size: 14px; font-weight: 700;">
                                            Generate Report
                                        </button>
                                    </form>
                                @endif
                            @endif
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <button
                                    onclick="event.stopPropagation(); if(confirm('Delete all data for {{ $dateFormatted }}?')) { document.getElementById('delete-date-{{ $dateId }}').submit(); }"
                                    class="btn"
                                    style="background: #ef4444; color: white; padding: 10px 20px; font-size: 14px; font-weight: 700; border: none; border-radius: 6px; cursor: pointer;">
                                    Delete
                                </button>
                                <form id="delete-date-{{ $dateId }}" action="{{ route('uploads.destroy', $batches->first()) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                            <span class="accordion-icon" style="font-size: 24px; transition: transform 0.3s; color: var(--text-light);">▼</span>
                        </div>
                    </div>

                    <!-- Accordion Content -->
                    <div id="{{ $dateId }}" class="accordion-content"
                        style="display: {{ $isFirst ? 'block' : 'none' }};
                                                                                                                                      background: white;
                                                                                                                                      border: 1px solid var(--border-color);
                                                                                                                                      border-top: none;
                                                                                                                                      border-radius: 0 0 10px 10px;
                                                                                                                                      padding: 0;
                                                                                                                                      margin-top: -5px;">

                        @include('reports.partials.draw_list', [
                            'draws' => $draws, 
                            'drawDate' => $drawDate,
                            'hasReports' => $hasReports,
                            'existingReports' => $existingReports,
                            'lotteryOrder' => $lotteryOrder ?? ['KP', 'LW', 'AK', 'SF', 'SB', 'SR', 'JS', 'DS']
                        ])

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px; background: #f9fafb; border-radius: 10px;">
            <h3 style="color: var(--text-light); margin-bottom: 10px;">No Uploads Yet</h3>
            <p style="color: var(--text-light); margin-bottom: 20px;">Upload lottery XML files to generate reports.</p>
            <a href="{{ route('uploads.index') }}" class="btn btn-primary">Upload Files</a>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        .accordion-header:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1) !important;
        }

        .accordion-item.active .accordion-icon {
            transform: rotate(180deg);
        }
    </style>
@endpush

@push('scripts')
    <script>
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            const item = content.closest('.accordion-item');
            const allItems = document.querySelectorAll('.accordion-item');

            // Close all other accordions
            allItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                    const otherContent = otherItem.querySelector('.accordion-content');
                    if (otherContent) {
                        otherContent.style.display = 'none';
                    }
                }
            });

            // Toggle current accordion
            if (content.style.display === 'none') {
                content.style.display = 'block';
                item.classList.add('active');
            } else {
                content.style.display = 'none';
                item.classList.remove('active');
            }
        }
    </script>
@endpush