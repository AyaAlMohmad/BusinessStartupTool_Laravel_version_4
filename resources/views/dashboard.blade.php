@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="position-relative overflow-hidden">
            <div class="swiper mySwiper mt-4 mb-2">
                <div class="swiper-wrapper">
                    @foreach ($videos as $index => $video)
                        <div class="swiper-slide">
                            <div class="card card-background shadow-none border-radius-xl card-background-after-none align-items-start mb-0">
                                <video controls class="video-preview w-100" style="aspect-ratio: 16 / 9; object-fit: cover; border-radius: 10px;">
                                    <source src="{{ asset($video->video_path) }}" type="video/mp4">
                                    {{ __('dashboard.browser_not_support') }}
                                </video>
                                <div class="card-body text-start px-3 py-0 w-100">
                                    <div class="row mt-3">
                                        <div class="col-sm-3 mt-auto">
                                            <h4 class="text-dark font-weight-bolder">#{{ $index + 1 }}</h4>
                                            <p class="text-dark opacity-6 text-xs font-weight-bolder mb-0">{{ __('dashboard.title') }}</p>
                                            <h5 class="text-dark font-weight-bolder">{{ $video->title }}</h5>
                                        </div>
                                        <div class="col-sm-3 ms-auto mt-auto">
                                            <p class="text-dark opacity-6 text-xs font-weight-bolder mb-0">{{ __('dashboard.description') }}</p>
                                            <h5 class="text-dark font-weight-bolder">
                                                {{ \Illuminate\Support\Str::limit($video->description, 20) }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>

    <div class="row my-4">
        <!-- Funding Statistics -->
        <div class="col-xl-3 col-sm-6 mb-xl-0">
            <div class="card border shadow-xs mb-4">
                <div class="card-body text-start p-3 w-100">
                    <div class="icon icon-shape icon-sm bg-dark text-white text-center border-radius-sm d-flex align-items-center justify-content-center mb-3">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4.5 3.75a3 3 0 00-3 3v.75h21v-.75a3 3 0 00-3-3h-15z" />
                            <path fill-rule="evenodd" d="M22.5 9.75h-21v7.5a3 3 0 003 3h15a3 3 0 003-3v-7.5zm-18 3.75a.75.75 0 01.75-.75h6a.75.75 0 010 1.5h-6a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="w-100">
                                <p class="text-sm text-secondary mb-1">{{ __('dashboard.planned_funding') }}</p>
                                <h4 class="mb-2 font-weight-bold">${{ number_format($plannedFunding, 2) }}</h4>
                                <div class="d-flex align-items-center">
                                    <span class="text-sm text-success font-weight-bolder">
                                        {{ __('dashboard.total_goal') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0">
            <div class="card border shadow-xs mb-4">
                <div class="card-body text-start p-3 w-100">
                    <div class="icon icon-shape icon-sm bg-dark text-white text-center border-radius-sm d-flex align-items-center justify-content-center mb-3">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.5 5.25a3 3 0 013-3h3a3 3 0 013 3v.205c.933.085 1.857.197 2.774.334 1.454.218 2.476 1.483 2.476 2.917v3.033c0 1.211-.734 2.352-1.936 2.752A24.726 24.726 0 0112 15.75c-2.73 0-5.357-.442-7.814-1.259-1.202-.4-1.936-1.541-1.936-2.752V8.706c0-1.434 1.022-2.7 2.476-2.917A48.814 48.814 0 017.5 5.455V5.25zm7.5 0v.09a49.488 49.488 0 00-6 0v-.09a1.5 1.5 0 011.5-1.5h3a1.5 1.5 0 011.5 1.5zm-3 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                            <path d="M3 18.4v-2.796a4.3 4.3 0 00.713.31A26.226 26.226 0 0012 17.25c2.892 0 5.68-.468 8.287-1.335.252-.084.49-.189.713-.311V18.4c0 1.452-1.047 2.728-2.523 2.923-2.12.282-4.282.427-6.477.427a49.19 49.19 0 01-6.477-.427C4.047 21.128 3 19.852 3 18.4z" />
                        </svg>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="w-100">
                                <p class="text-sm text-secondary mb-1">{{ __('dashboard.secured_funding') }}</p>
                                <h4 class="mb-2 font-weight-bold">${{ number_format($securedFunding, 2) }}</h4>
                                <div class="d-flex align-items-center">
                                    <span class="text-sm text-success font-weight-bolder">
                                        {{ $securedFundingPercentage }}% {{ __('dashboard.of_goal') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0">
            <div class="card border shadow-xs mb-4">
                <div class="card-body text-start p-3 w-100">
                    <div class="icon icon-shape icon-sm bg-dark text-white text-center border-radius-sm d-flex align-items-center justify-content-center mb-3">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3H6a3 3 0 01-3-3V6zm4.5 7.5a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0v-2.25a.75.75 0 01.75-.75zm3.75-1.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V12zm2.25-3a.75.75 0 01.75.75v6.75a.75.75 0 01-1.5 0V9.75A.75.75 0 0113.5 9zm3.75-1.5a.75.75 0 00-1.5 0v9a.75.75 0 001.5 0v-9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="w-100">
                                <p class="text-sm text-secondary mb-1">{{ __('dashboard.remaining_funding') }}</p>
                                <h4 class="mb-2 font-weight-bold">${{ number_format($pendingFunding, 2) }}</h4>
                                <div class="d-flex align-items-center">
                                    <span class="text-sm text-danger font-weight-bolder">
                                        {{ $pendingFundingPercentage }}% {{ __('dashboard.of_goal') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border shadow-xs mb-4">
                <div class="card-body text-start p-3 w-100">
                    <div class="icon icon-shape icon-sm bg-dark text-white text-center border-radius-sm d-flex align-items-center justify-content-center mb-3">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.25 2.25a3 3 0 00-3 3v4.318a3 3 0 00.879 2.121l9.58 9.581c.92.92 2.39 1.186 3.548.428a18.849 18.849 0 005.441-5.44c.758-1.16.492-2.629-.428-3.548l-9.58-9.581a3 3 0 00-2.122-.879H5.25zM6.375 7.5a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="w-100">
                                <p class="text-sm text-secondary mb-1">{{ __('dashboard.breakeven') }}</p>
                                <h4 class="mb-2 font-weight-bold">{{ $averageBreakeven }} {{ __('month') }}</h4>
                                <div class="d-flex align-items-center">
                                    <span class="text-sm text-success font-weight-bolder">
                                        {{ __('dashboard.profitability_time') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Project Statistics -->
        <div class="col-lg-6 col-md-6 mb-md-0 mb-4">
            <div class="card shadow-xs border h-100">
                <div class="card-header pb-0">
                    <h6 class="font-weight-semibold text-lg mb-0">{{ __('dashboard.project_stats') }}</h6>
                    <p class="text-sm">{{ __('dashboard.project_overview') }}</p>
                </div>
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border shadow-xs">
                                <div class="card-body p-3">
                                    <p class="text-sm text-secondary mb-1">{{ __('dashboard.business_ideas') }}</p>
                                    <h4 class="mb-0 font-weight-bold">{{ $businessIdeas }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border shadow-xs">
                                <div class="card-body p-3">
                                    <p class="text-sm text-secondary mb-1">{{ __('dashboard.sales_strategies') }}</p>
                                    <h4 class="mb-0 font-weight-bold">{{ $salesStrategiesCount }}</h4>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-md-6 mb-3">
                            <div class="card border shadow-xs">
                                <div class="card-body p-3">
                                    <p class="text-sm text-secondary mb-1">{{ __('dashboard.marketing_activities') }}</p>
                                    <h4 class="mb-0 font-weight-bold">{{ $marketingActivities->count() }}</h4>
                                </div>
                            </div>
                        </div> --}}
                        <div class="col-md-6 mb-3">
                            <div class="card border shadow-xs">
                                <div class="card-body p-3">
                                    <p class="text-sm text-secondary mb-1">{{ __('dashboard.market_research') }}</p>
                                    <h4 class="mb-0 font-weight-bold">{{ $marketResearches }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="card shadow-xs border">
                <div class="card-header border-bottom pb-0">
                    <h6 class="font-weight-semibold text-lg mb-0">{{ __('dashboard.startup_costs') }}</h6>
                    <p class="text-sm mb-2">{{ __('dashboard.cost_details') }}</p>
                </div>
                <div class="card-body px-0 py-0">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center justify-content-center mb-0">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">{{ __('dashboard.item') }}</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">{{ __('dashboard.cost') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($startupCosts as $cost)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-sm">{{ $cost->name }}</h6>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-normal mb-0">${{ number_format($cost->cost, 2) }}</p>
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="bg-gray-100">
                                    <td>
                                        <h6 class="mb-0 text-sm font-weight-bold">{{ __('dashboard.total') }}</h6>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">${{ number_format($startupCosts->sum('cost'), 2) }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Funding Sources -->
        <div class="col-lg-12">
            <div class="card shadow-xs border">
                <div class="card-header border-bottom pb-0">
                    <h6 class="font-weight-semibold text-lg mb-0">{{ __('dashboard.funding_sources') }}</h6>
                    <p class="text-sm mb-2">{{ __('dashboard.available_funding') }}</p>
                </div>
                <div class="card-body px-0 py-0">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center justify-content-center mb-0">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">{{ __('dashboard.source') }}</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">{{ __('dashboard.amount') }}</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">{{ __('dashboard.type') }}</th>
                                    <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">{{ __('dashboard.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fundingSources as $source)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-sm">{{ $source->source_name }}</h6>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-normal mb-0">${{ number_format($source->amount, 2) }}</p>
                                    </td>
                                    <td>
                                        <span class="text-sm font-weight-normal">{{ $source->type }}</span>
                                    </td>
                                    <td>
                                        <span class="text-sm font-weight-normal">{{ $source->expected_date }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-xs border">
                <div class="card-header pb-0">
                    <div class="d-sm-flex align-items-center mb-3">
                        <div>
                            <h6 class="font-weight-semibold text-lg mb-0">{{ __('dashboard.financial_overview') }}</h6>
                            <p class="text-sm mb-sm-0 mb-2">{{ __('dashboard.financial_balance_details') }}</p>
                        </div>
                        <div class="ms-auto d-flex">
                            <button type="button" class="btn btn-sm btn-white mb-0 me-2">
                                {{ __('dashboard.view_report') }}
                            </button>
                        </div>
                    </div>
                    <div class="d-sm-flex align-items-center">
                        <h3 class="mb-0 font-weight-semibold">${{ number_format($securedFunding, 2) }}</h3>
                        <span class="badge badge-sm border border-success text-success bg-success border-radius-sm ms-sm-3 px-2">
                            <svg width="9" height="9" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.46967 4.46967C0.176777 4.76256 0.176777 5.23744 0.46967 5.53033C0.762563 5.82322 1.23744 5.82322 1.53033 5.53033L0.46967 4.46967ZM5.53033 1.53033C5.82322 1.23744 5.82322 0.762563 5.53033 0.46967C5.23744 0.176777 4.76256 0.176777 4.46967 0.46967L5.53033 1.53033ZM5.53033 0.46967C5.23744 0.176777 4.76256 0.176777 4.46967 0.46967C4.17678 0.762563 4.17678 1.23744 4.46967 1.53033L5.53033 0.46967ZM8.46967 5.53033C8.76256 5.82322 9.23744 5.82322 9.53033 5.53033C9.82322 5.23744 9.82322 4.76256 9.53033 4.46967L8.46967 5.53033ZM1.53033 5.53033L5.53033 1.53033L4.46967 0.46967L0.46967 4.46967L1.53033 5.53033ZM4.46967 1.53033L8.46967 5.53033L9.53033 4.46967L5.53033 0.46967L4.46967 1.53033Z" fill="#67C23A"></path>
                            </svg>
                            {{ $securedFundingPercentage }}%
                        </span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="chart mt-n6">
                        <canvas id="financialChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById("financialChart").getContext("2d");

        // إنشاء تدرجات الألوان
        var gradientStroke1 = ctx.createLinearGradient(0, 230, 0, 50);
        gradientStroke1.addColorStop(1, 'rgba(45,168,255,0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(45,168,255,0.0)');
        gradientStroke1.addColorStop(0, 'rgba(45,168,255,0)');

        var gradientStroke2 = ctx.createLinearGradient(0, 230, 0, 50);
        gradientStroke2.addColorStop(1, 'rgba(119,77,211,0.4)');
        gradientStroke2.addColorStop(0.7, 'rgba(119,77,211,0.1)');
        gradientStroke2.addColorStop(0, 'rgba(119,77,211,0)');

        new Chart(ctx, {
            plugins: [{
                beforeInit(chart) {
                    const originalFit = chart.legend.fit;
                    chart.legend.fit = function fit() {
                        originalFit.bind(chart.legend)();
                        this.height += 40;
                    }
                },
            }],
            type: "line",
            data: {
                labels: {!! json_encode($last12Months) !!}, // يجب تمرير هذا المتغير من المتحكم
                datasets: [{
                    label: "{{ __('dashboard.planned_funding') }}",
                    tension: 0,
                    borderWidth: 2,
                    pointRadius: 3,
                    borderColor: "#2ca8ff",
                    pointBorderColor: '#2ca8ff',
                    pointBackgroundColor: '#2ca8ff',
                    backgroundColor: gradientStroke1,
                    fill: true,
                    data: {!! json_encode($plannedFundingData) !!}, // بيانات التمويل المخطط
                    maxBarThickness: 6
                },
                {
                    label: "{{ __('dashboard.secured_funding') }}",
                    tension: 0,
                    borderWidth: 2,
                    pointRadius: 3,
                    borderColor: "#832bf9",
                    pointBorderColor: '#832bf9',
                    pointBackgroundColor: '#832bf9',
                    backgroundColor: gradientStroke2,
                    fill: true,
                    data: {!! json_encode($securedFundingData) !!}, // بيانات التمويل المضمون
                    maxBarThickness: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 6,
                            boxHeight: 6,
                            padding: 20,
                            pointStyle: 'circle',
                            borderRadius: 50,
                            usePointStyle: true,
                            font: {
                                weight: 400,
                            },
                        },
                    },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#1e293b',
                        bodyColor: '#1e293b',
                        borderColor: '#e9ecef',
                        borderWidth: 1,
                        pointRadius: 2,
                        usePointStyle: true,
                        boxWidth: 8,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [4, 4]
                        },
                        ticks: {
                            callback: function(value, index, ticks) {
                                return '$' + parseInt(value).toLocaleString();
                            },
                            display: true,
                            padding: 10,
                            color: '#b2b9bf',
                            font: {
                                size: 12,
                                family: "Noto Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                            color: "#64748B"
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [4, 4]
                        },
                        ticks: {
                            display: true,
                            color: '#b2b9bf',
                            padding: 20,
                            font: {
                                size: 12,
                                family: "Noto Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                            color: "#64748B"
                        }
                    },
                },
            },
        });
    });
    </script>

@endsection
