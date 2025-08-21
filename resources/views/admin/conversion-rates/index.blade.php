@extends('layouts.app')
@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sales Strategy') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center p-4 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Sales Strategy List</h3>
                    <a href="{{ route('admin.sales-strategies.analysis') }}" 
                       style="color: blue">
                        <i class="fas fa-chart-bar mr-2"></i>
                        View Analysis
                    </a>
                </div>
                
                <div class="p-6 bg-white border-b border-gray-200">
                    <table>
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2">Business</th>
                            <th class="px-4 py-2">User</th>
                            <th class="px-4 py-2">Target Revenue</th>
                            <th class="px-4 py-2">Unit Price</th>
                            <th class="px-4 py-2">Sales Needed</th>
                            <th class="px-4 py-2">Total Interactions</th>
                            <th class="px-4 py-2">Total Reach</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($conversionRates as $rate)
                            <tr>
                                 <td class="border px-4 py-2">{{ $rate->business->name ?? '-' }}</td>
                                 <td class="border px-4 py-2">{{ $rate->user->name ?? '-' }}</td>
                                 <td class="border px-4 py-2">{{ $rate->target_revenue }}</td>
                                 <td class="border px-4 py-2">{{ $rate->unit_price }}</td>
                                 <td class="border px-4 py-2">{{ $rate->sales_needed }}</td>
                                 <td class="border px-4 py-2">{{ $rate->total_interactions }}</td>
                                 <td class="border px-4 py-2">{{ $rate->total_reach }}</td>
                                <td class="border px-4 py-2 w-32">
                                    <a href="{{ route('admin.sales-strategies.show', $rate->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye icon-blue"></i></a>
                                    <form action="{{ route('admin.sales-strategies.destroy', $rate->id) }}" method="POST" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger"><i class="fas fa-trash icon-red"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
@endsection
