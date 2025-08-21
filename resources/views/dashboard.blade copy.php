{{-- <x-app-layout> --}}
    @extends('layouts.app')
    @section('content')
        <head>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        </head>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
    
            .dashboard {
                width: 80%;
                margin: 20px auto;
                background-color: #fff;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
    
            .summary {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
            }
    
            .summary-item {
                width: 22%;
                padding: 15px;
                background-color: #f9f9f9;
                text-align: center;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
    
            .details {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
            }
    
            .details>div {
                width: 48%;
            }
    
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
    
            table,
            th,
            td {
                border: 1px solid #ddd;
            }
    
            th,
            td {
                padding: 10px;
                text-align: left;
            }
    
            h3 {
                margin-top: 0;
            }
    
            .status-item {
                width: 30%;
                text-align: center;
                background-color: #f9f9f9;
                padding: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
    
            .funding-status {
                display: flex;
                justify-content: space-between;
            }
    
            .icon {
                margin-right: 10px;
            }
    
            .oval {
                display: inline-block;
                padding: 10px 20px;
                border-radius: 25px;
                background-color: #ddd;
                font-size: 1em;
                text-align: center;
    
            }
    
            .oval-green {
                display: inline-block;
                padding: 10px 20px;
                border-radius: 25px;
                background-color: rgb(46, 125, 50);
                font-size: 1em;
                color: white;
                text-align: center;
            }
    
            .oval-orange {
                display: inline-block;
                padding: 10px 20px;
                border-radius: 25px;
                background-color: rgb(237, 108, 2);
                font-size: 1em;
                color: white;
                text-align: center;
            }
        </style>
        <div class="dashboard">
            <div class="summary">
                <div class="summary-item">
                    <h3><i class="fas fa-dollar-sign icon"></i>Total Startup Costs</h3>
                    <p>${{ $startupCosts->sum('amount') ?? 0 }}</p>
                    <span>Across all users</span>
                </div>
                <div class="summary-item">
                    <h3><i class="fas fa-chart-line icon"></i>Average Startup Cost</h3>
                    <p>${{ $startupCosts->avg('amount') ?? 0 }}</p>
                    <span>Per business</span>
                </div>
                <div class="summary-item">
                    <h3><i class="fas fa-hand-holding-usd icon"></i>Total Funding</h3>
                    <p>${{ $fundingSources->sum('amount') ?? 0 }}</p>
                    <span>All sources</span>
                </div>
                <div class="summary-item">
                    <h3><i class="fas fa-calendar-alt icon"></i>Avg. Breakeven</h3>
                    <p>{{ $averageBreakeven ?? 0 }} months</p>
                    <span>Expected timeline</span>
                </div>
            </div>
            <div class="details">
                <div class="funding-distribution">
                    <h3><i class="fas fa-piggy-bank icon"></i>Funding Distribution</h3>
                    <table>
                        <tr>
                            <th>Source</th>
                            <th>Amount</th>
                            <th>%</th>
                        </tr>
                        @forelse ($fundingSources ?? [] as $funding)
                        <tr>
                            <td>{{ $funding->source ?? 'N/A' }}</td>
                            <td>${{ $funding->amount ?? 0 }}</td>
                            <td>{{ ($funding->amount && $fundingSources->sum('amount')) ? ($funding->amount / $fundingSources->sum('amount')) * 100 : 0 }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">No funding sources available</td>
                        </tr>
                        @endforelse
                    </table>
                </div>
                <div class="cost-categories">
                    <h3><i class="fas fa-coins icon"></i>Cost Categories</h3>
                    <table>
                        <tr>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>%</th>
                        </tr>
                        @forelse ($startupCosts ?? [] as $cost)
                        <tr>
                            <td>{{ $cost->category ?? 'N/A' }}</td>
                            <td>${{ $cost->amount ?? 0 }}</td>
                            <td>{{ ($cost->amount && $startupCosts->sum('amount')) ? ($cost->amount / $startupCosts->sum('amount')) * 100 : 0 }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">No cost categories available</td>
                        </tr>
                        @endforelse
                    </table>
                </div>
            </div>
            <div class="funding-status">
                <h3><i class="fas fa-chart-pie icon"></i>Funding Status Overview</h3>
                <div class="status-item">
                    <h4><i class="fas fa-tasks icon"></i>Planned</h4>
                    <p>${{ $plannedFunding ?? 0 }}</p>
                    <span class="oval">{{ $plannedFundingPercentage ?? 0 }}%</span>
                </div>
                <div class="status-item">
                    <h4><i class="fas fa-check-circle icon"></i>Secured</h4>
                    <p>${{ $fundingSources->sum('amount') ?? 0 }}</p>
                    <span class="oval-green">{{ $securedFundingPercentage ?? 100 }}%</span>
                </div>
                <div class="status-item">
                    <h4><i class="fas fa-hourglass-half icon"></i>Pending</h4>
                    <p>${{ $pendingFunding ?? 0 }}</p>
                    <span class="oval-orange">{{ $pendingFundingPercentage ?? 0 }}%</span>
                </div>
            </div>
        </div>
    {{-- </x-app-layout> --}}
    @endsection
    