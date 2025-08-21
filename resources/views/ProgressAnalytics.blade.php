@extends('layouts.app')

@section('content')
    <style>
        .chart-container {
            position: relative;
            margin-bottom: 20px;
        }

        .no-data-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #6c757d;
            font-size: 16px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-align: center;
            color: #2c3e50;
        }

        .stat-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            margin-bottom: 24px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin: 30px 0 20px;
            color: #34495e;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 10px;
        }

        /* أحجام محددة لكل مخطط */
        #geoChart, #ageChart {
            width: 100% !important;
            height: 350px !important;
        }

        #languageChart, #experienceChart {
            width: 100% !important;
            height: 300px !important;
        }

        #migrationChart {
            width: 100% !important;
            height: 250px !important;
        }
        #progressChart {
            width: 100% !important;
            height: 400px !important;
        }
    </style>

    <div class="container-fluid py-4">

        {{-- قسم جديد لمخطط التقدم --}}
        <div class="row">
            <div class="col-12">
                <h4 class="section-title">Progress by Sections</h4>
            </div>
            <div class="col-12">
                <div class="card stat-card">
                    <div class="card-body chart-container">
                        <div class="card-title">Completion Percentage by Section</div>
                        <canvas id="progressChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h4 class="section-title">Participants Overview</h4>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card stat-card">
                    <div class="card-body chart-container">
                        <div class="card-title">Geographical Distribution</div>
                        <canvas id="geoChart" width="400" height="350"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card stat-card">
                    <div class="card-body chart-container">
                        <div class="card-title">Age Groups</div>
                        <canvas id="ageChart" width="400" height="350"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- مخططات اللغات والخبرة --}}
        <div class="row">
            <div class="col-12">
                <h4 class="section-title">Language & Experience</h4>
            </div>
            <div class="col-12 col-md-6">
                <div class="card stat-card">
                    <div class="card-body chart-container">
                        <div class="card-title">Languages</div>
                        <canvas id="languageChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card stat-card">
                    <div class="card-body chart-container">
                        <div class="card-title">Years of Experience</div>
                        <canvas id="experienceChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- مخطط نوع الهجرة --}}
        <div class="row">
            <div class="col-12">
                <h4 class="section-title">Migration Details</h4>
            </div>
            <div class="col-12">
                <div class="card stat-card">
                    <div class="card-body chart-container">
                        <div class="card-title">Migration Type</div>
                        <canvas id="migrationChart" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // لوحة ألوان متناسقة
        const palette = [
            '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6',
            '#1abc9c', '#d35400', '#34495e', '#7f8c8d', '#27ae60'
        ];

        // دالة لإنشاء مخطط شريطي
        function createBarChart(id, labels, data, options = {}) {
            const ctx = document.getElementById(id);
            if (!ctx) {
                console.warn(`Element #${id} not found`);
                return;
            }

            const defaultOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            };

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {...defaultOptions, ...options}
            });
        }

        // دالة لإنشاء مخطط دائري
        function createPieChart(id, labels, data) {
            const ctx = document.getElementById(id);
            if (!ctx) {
                console.warn(`Element #${id} not found`);
                return;
            }

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 15
                            }
                        }
                    }
                }
            });
        }

        // دالة لإنشاء مخطط دونات
        function createDoughnutChart(id, labels, data) {
            const ctx = document.getElementById(id);
            if (!ctx) {
                console.warn(`Element #${id} not found`);
                return;
            }

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 15
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }

        function createProgressChart(id, labels, data) {
            const ctx = document.getElementById(id);
            if (!ctx) return;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels.map(label => label.replace('_', ' ')),
                    datasets: [{
                        data: data,
                        backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                        borderColor: '#fff',
                        borderWidth: 1,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw}%`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // بيانات التقدم من PHP
            const progressData = @json($sectionCompletion);
            // بيانات من PHP
            const ageGroupsData = @json($ageGroups);
            const migrantStats = @json($migrantStats);
            createProgressChart('progressChart',
                Object.keys(progressData),
                Object.values(progressData));
            // 1. مخطط التوزيع الجغرافي (شريطي عمودي)
            createBarChart('geoChart',
                Object.keys(migrantStats.cultural_background),
                Object.values(migrantStats.cultural_background),
                { indexAxis: 'x' });

            // 2. مخطط الفئات العمرية (شريطي أفقي)
            createBarChart('ageChart',
                ['Under 18', '18-25', '26-35', '36-45', '46-55', '55+'],
                Object.values(ageGroupsData),
                { indexAxis: 'y' });

            // 3. مخطط اللغات (دونات)
            createDoughnutChart('languageChart',
                Object.keys(migrantStats.languages),
                Object.values(migrantStats.languages));

            // 4. مخطط سنوات الخبرة (باي)
            createPieChart('experienceChart',
                ['<1 yr', '1-3 yrs', '3-5 yrs', '5+ yrs'],
                [25, 40, 20, 15]);

            // 5. مخطط نوع الهجرة (شريطي مكدس)
            createBarChart('migrationChart',
                Object.keys(migrantStats.visa_category),
                Object.values(migrantStats.visa_category),
                { indexAxis: 'x' });
        });
    </script>
@endsection
