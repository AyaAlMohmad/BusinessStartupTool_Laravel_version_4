@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Market Research') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center p-4 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Market Research List</h3>
                    <a href="{{ route('admin.market-research.analysis') }}" style="color: blue">
                        <i class="fas fa-chart-bar mr-2"></i>
                        View Analysis
                    </a>
                </div>

                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table-fixed w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2">#</th>
                                <th class="px-4 py-2">Business</th>
                                <th class="px-4 py-2">User</th>
                                <th class="px-4 py-2">Target Customer</th>
                                <th class="px-4 py-2">Solutions</th>
                                <th class="px-4 py-2">Research Details</th>
                                <th class="px-4 py-2 w-32">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($researches as $research)
                                <tr>
                                    <td class="border px-4 py-2">{{ $research->id }}</td>
                                    <td class="border px-4 py-2">{{ $research->business->name ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ $research->user->name ?? '-' }}</td>
                                    
                                    {{-- Target Customer Column --}}
                                    <td class="border px-4 py-2">
                                        <strong>Name:</strong> {{ $research->target_customer_name }}<br>
                                        <strong>Age:</strong> {{ $research->age }}<br>
                                        <strong>Gender:</strong> {{ $research->gender }}<br>
                                        <strong>Income:</strong> {{ $research->income }}<br>
                                        <strong>Education:</strong> {{ $research->education }}
                                    </td>
                                    
                                    {{-- Solutions Column --}}
                                    <td class="border px-4 py-2">
                                        @if(is_array($research->must_have_solutions))
                                            <strong>Must Have:</strong><br>
                                            {{ implode(', ', $research->must_have_solutions) }}<br>
                                        @endif
                                        
                                        @if(is_array($research->should_have_solutions))
                                            <strong>Should Have:</strong><br>
                                            {{ implode(', ', $research->should_have_solutions) }}<br>
                                        @endif
                                        
                                        @if(is_array($research->nice_to_have_solutions))
                                            <strong>Nice to Have:</strong><br>
                                            {{ implode(', ', $research->nice_to_have_solutions) }}
                                        @endif
                                    </td>
                                    
                                    {{-- Research Details Column --}}
                                    <td class="border px-4 py-2">
                                        <strong>Employment:</strong> {{ $research->employment }}<br>
                                        <strong>Other:</strong> {{ $research->other }}<br>
                                        @if(is_array($research->nots))
                                            <strong>Notes:</strong><br>
                                            {{ implode(', ', $research->nots) }}
                                        @endif
                                    </td>

                                    <td class="border px-4 py-2 w-32">
                                        <a href="{{ route('admin.market-research.show', $research->id) }}">
                                            <i class="fas fa-eye icon-blue"></i>
                                        </a>

                                        <form action="{{ route('admin.market-research.destroy', $research->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash icon-red"></i>
                                            </button>
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