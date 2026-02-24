@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; margin-bottom: 5px;">Super Admin Dashboard</h2>
            <p style="color: var(--text-soft);">System Health & Analytics</p>
        </div>
        <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-soft); background: #fff; padding: 8px 16px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    <!-- System Health -->
    <div class="card"
        style="background: linear-gradient(135deg, var(--dlb-red) 0%, var(--dlb-orange) 100%); color: white; border: none; padding: 30px;">
        <h3
            style="margin-bottom: 25px; color: white; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 15px; font-weight: 700;">
            System Health status</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px;">
            <div>
                <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">Total Users</div>
                <div style="font-size: 2.5rem; font-weight: 800;">{{ $systemHealth['total_users'] }}</div>
            </div>
            <div>
                <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">Active Users</div>
                <div style="font-size: 2.5rem; font-weight: 800;">{{ $systemHealth['active_users'] }}</div>
            </div>
            <div>
                <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">Total Reports</div>
                <div style="font-size: 2.5rem; font-weight: 800;">{{ $systemHealth['total_reports'] }}</div>
            </div>
            <div>
                <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">Storage Usage</div>
                <div style="font-size: 2.5rem; font-weight: 800;">{{ $systemHealth['storage_usage'] }}</div>
            </div>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="card" style="margin-bottom: 0; border-left: 4px solid var(--dlb-green); padding: 25px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                Total Batches
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--text-main);">
                {{ number_format($stats['total_batches']) }}
            </div>
            <div style="font-size: 0.85rem; color: var(--text-soft); margin-top: 5px;">
                {{ number_format($stats['total_files_processed']) }} files processed
            </div>
        </div>

        <div class="card" style="margin-bottom: 0; border-left: 4px solid #0288d1; padding: 25px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                Success Rate
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--text-main);">{{ $stats['avg_success_rate'] }}%
            </div>
            <div style="font-size: 0.85rem; color: var(--text-soft); margin-top: 5px;">{{ $stats['successful_batches'] }}
                successful batches</div>
        </div>

        <div class="card" style="margin-bottom: 0; border-left: 4px solid var(--dlb-red); padding: 25px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-soft); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                Failed Batches
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--dlb-red);">{{ $stats['failed_batches'] }}
            </div>
            <div style="font-size: 0.85rem; color: var(--text-soft); margin-top: 5px;">Requires attention</div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px; margin-bottom: 30px;">

        <!-- Upload Trends Chart -->
        <div class="card" style="margin-bottom: 0; padding: 25px;">
            <h3 style="font-weight: 700; font-size: 1.2rem; margin-bottom: 20px;">Upload Trends (Last 7 Days)</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="uploadTrendsChart"></canvas>
            </div>
        </div>

        <!-- User Activity Chart -->
        <div class="card" style="margin-bottom: 0; padding: 25px;">
            <h3 style="font-weight: 700; font-size: 1.2rem; margin-bottom: 20px;">User Activity by Role</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="userActivityChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Recent User Logins -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 700; font-size: 1.2rem;">Recent User Logins</h3>
            <a href="{{ route('users.index') }}" class="btn-outline" style="font-size: 0.85rem; padding: 6px 12px; text-decoration: none;">Manage Users</a>
        </div>
        <div class="table-container">
            <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background: var(--bg-gray); text-align: left;">
                        <th style="padding: 12px 15px; border-radius: 8px 0 0 8px; font-weight: 600; color: var(--text-soft);">User</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Role</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Email</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: var(--text-soft);">Last Login</th>
                        <th style="padding: 12px 15px; border-radius: 0 8px 8px 0; font-weight: 600; color: var(--text-soft);">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLogins as $user)
                        <tr>
                            <td style="padding: 15px; border-bottom: 1px solid #eee; font-weight: 600;">{{ $user->name }}</td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                <span style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-soft);">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">{{ $user->email }}</td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee; color: var(--text-soft);">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                            <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                @if($user->is_active)
                                    <span style="background: #e0f2f1; color: var(--dlb-green); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Active</span>
                                @else
                                    <span style="background: #ffebee; color: var(--dlb-red); padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Management Links -->
    <div class="card" style="padding: 30px;">
        <h3 style="font-weight: 700; font-size: 1.2rem; margin-bottom: 20px;">Quick Management</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="{{ route('users.index') }}" class="btn-outline" style="background: var(--dlb-red); color: white; border-color: var(--dlb-red); text-decoration: none;">Manage Users</a>
            <a href="{{ route('reports.index') }}" class="btn-outline" style="text-decoration: none;">View All Reports</a>
            <a href="{{ route('uploads.index') }}" class="btn-outline" style="text-decoration: none;">View All Uploads</a>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Colors
            const colors = {
                red: '#e63946',
                orange: '#fca311',
                yellow: '#ffb703',
                green: '#2a9d8f',
                blue: '#0288d1',
                text: '#2b2d42'
            };

            // User Activity Chart
            const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
            const userActivityData = @json($userActivity);

            const roles = userActivityData.map(item => item.role.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase()));
            const roleCounts = userActivityData.map(item => item.count);

            new Chart(userActivityCtx, {
                type: 'doughnut',
                data: {
                    labels: roles,
                    datasets: [{
                        data: roleCounts,
                        backgroundColor: [
                            colors.red,
                            colors.orange,
                            colors.green,
                            colors.yellow,
                            colors.blue
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: {
                                    family: "'Inter', sans-serif"
                                }
                            }
                        }
                    }
                }
            });

            // Upload Trends Chart
            const trendsCtx = document.getElementById('uploadTrendsChart').getContext('2d');
            const trendsData = @json($uploadTrends);

            const dates = trendsData.map(item => {
                const d = new Date(item.date);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            });
            const counts = trendsData.map(item => item.count);
            const files = trendsData.map(item => item.files);

            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Batches Uploaded',
                            data: counts,
                            borderColor: colors.orange,
                            backgroundColor: 'rgba(252, 163, 17, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Files Processed',
                            data: files,
                            borderColor: colors.green,
                            backgroundColor: 'rgba(42, 157, 143, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: "'Inter', sans-serif"
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            grid: {
                                borderDash: [4, 4],
                                color: '#f0f0f0'
                            },
                            title: {
                                display: true,
                                text: 'Batches',
                                font: {
                                    family: "'Inter', sans-serif"
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false,
                            },
                            title: {
                                display: true,
                                text: 'Files',
                                font: {
                                    family: "'Inter', sans-serif"
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush