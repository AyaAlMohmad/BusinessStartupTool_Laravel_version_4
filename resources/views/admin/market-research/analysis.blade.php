@extends('layouts.app')

@section('content')
<div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Market Research Table Modification Analysis</h1>

        {{-- Daily Modifications Chart --}}
        <canvas id="auditChart" width="400" height="200" class="mb-12"></canvas>

        {{-- Field-Based Modifications Chart --}}
        <h2 class="text-xl font-semibold mb-4">Modification Distribution by Field</h2>
        <canvas id="fieldChart" width="400" height="200"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // First chart (Daily Modifications)
        var auditCtx = document.getElementById('auditChart').getContext('2d');
        new Chart(auditCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($modificationsPerDay->pluck('date')) !!},
                datasets: [{
                    label: 'Number of Modifications',
                    data: {!! json_encode($modificationsPerDay->pluck('count')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Daily Modifications to Landing Page'
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Date' },
                        type: 'category'
                    },
                    y: {
                        title: { display: true, text: 'Number of Modifications' },
                        beginAtZero: true
                    }
                }
            }
        });

        // Second chart (Field-Based Modifications)
        var fieldCtx = document.getElementById('fieldChart').getContext('2d');
        new Chart(fieldCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_keys($fieldCounts)) !!},
        datasets: [{
            data: {!! json_encode(array_values($fieldCounts)) !!},
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#8B0000', '#00FF00'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Modification Ratio by Field'
            }
        },
        // ↓↓↓ أضف هذا الجزء لتصغير حجم الدائرة ↓↓↓
        layout: {
            padding: 20
        },
        radius: '60%' // مثلاً 60% من حجم الـ container بدل الحجم الافتراضي (الذي هو غالباً 90% أو 100%)
    }
});

    });
</script>
@endsection
