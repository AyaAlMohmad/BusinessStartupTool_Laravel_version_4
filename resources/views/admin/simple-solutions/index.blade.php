@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Start Simple') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center p-4 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Start Simple List</h3>
                    <a href="{{ route('admin.start-simple.analysis') }}" class="text-blue-600 hover:text-blue-800"  style="color: blue">
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
                                <th class="px-4 py-2">Solution Details</th>
                                <th class="px-4 py-2">Strategy</th>
                                <th class="px-4 py-2">Implementation</th>
                                <th class="px-4 py-2 w-32">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solutions as $solution)
                                <tr>
                                    <td class="border px-4 py-2">{{ $solution->id }}</td>
                                    <td class="border px-4 py-2">{{ $solution->business->name ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ $solution->user->name ?? '-' }}</td>
                                    
                                    {{-- Solution Details Column --}}
                                    <td class="border px-4 py-2">
                                        @include('partials.display-data', [
                                            'data' => $solution->big_solution,
                                            'title' => 'Big Solution'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $solution->validation_questions,
                                            'title' => 'Validation Questions'
                                        ])
                                    </td>
                                    
                                    {{-- Strategy Column --}}
                                    <td class="border px-4 py-2">
                                        @include('partials.display-data', [
                                            'data' => $solution->entry_strategy,
                                            'title' => 'Entry Strategy'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $solution->future_plan,
                                            'title' => 'Future Plans'
                                        ])
                                    </td>
                                    
                                    {{-- Implementation Column --}}
                                    <td class="border px-4 py-2">
                                        @include('partials.display-data', [
                                            'data' => $solution->things,
                                            'title' => 'Action Items'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $solution->notes,
                                            'title' => 'Notes'
                                        ])
                                    </td>

                                    <td class="border px-4 py-2 w-32">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.start-simple.show', $solution->id) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye icon-blue"></i>
                                            </a>
                                            <form action="{{ route('admin.start-simple.destroy', $solution->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash icon-red"></i>
                                                </button>
                                            </form>
                                        </div>
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