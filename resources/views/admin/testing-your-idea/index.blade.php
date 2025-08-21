@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Testing Your Ideas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center p-4 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Testing Your Ideas List</h3>
                    <a href="{{ route('admin.testing-your-idea.analysis') }}" class="text-blue-600 hover:text-blue-800" style="color: blue">
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
                                <th class="px-4 py-2">Desirability</th>
                                <th class="px-4 py-2">Feasibility</th>
                                <th class="px-4 py-2">Viability</th>
                                <th class="px-4 py-2 w-32">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ideas as $idea)
                                <tr>
                                    <td class="border px-4 py-2">{{ $idea->id }}</td>
                                    <td class="border px-4 py-2">{{ $idea->business->name ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ $idea->user->name ?? '-' }}</td>
                                    
                                    {{-- Desirability Column --}}
                                    <td class="border px-4 py-2">
                                        @include('partials.display-data', [
                                            'data' => $idea->solves_problem,
                                            'title' => 'Solves Problem',
                                            'type' => 'boolean'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $idea->problem_statement,
                                            'title' => 'Problem Statement'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $idea->existing_solutions_used,
                                            'title' => 'Existing Solutions Used',
                                            'type' => 'boolean'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $idea->current_solutions_details,
                                            'title' => 'Current Solutions'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $idea->switch_reason,
                                            'title' => 'Switch Reasons'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $idea->desirability_notes,
                                            'title' => 'Notes'
                                        ])
                                    </td>
                                    
                                    {{-- Feasibility Column --}}
                                    <td class="border px-4 py-2">
                                        @include('partials.display-data', [
                                            'data' => $idea->required_skills,
                                            'title' => 'Required Skills'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $idea->qualifications_permits,
                                            'title' => 'Qualifications'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $idea->feasibility_notes,
                                            'title' => 'Notes'
                                        ])
                                    </td>
                                    
                                    {{-- Viability Column --}}
                                    <td class="border px-4 py-2">
                                        @include('partials.display-data', [
                                            'data' => $idea->payment_possible,
                                            'title' => 'Payment Possible'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $idea->profitability,
                                            'title' => 'Profitability'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $idea->finances_details,
                                            'title' => 'Finances Details'
                                        ])
                                        @include('partials.display-data', [
                                            'data' => $idea->viability_notes,
                                            'title' => 'Notes'
                                        ])
                                    </td>

                                    <td class="border px-4 py-2 w-32">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.testing-your-idea.show', $idea->id) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.testing-your-idea.destroy', $idea->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
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