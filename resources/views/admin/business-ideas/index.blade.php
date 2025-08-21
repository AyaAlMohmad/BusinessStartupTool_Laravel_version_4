@extends('layouts.app')
@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Business Ideas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center p-4 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Business Ideas List</h3>
                    <a href="{{ route('admin.ideas.analysis') }}" 
                       style="color: blue">
                        <i class="fas fa-chart-bar mr-2"></i>
                        View Analysis
                    </a>
                </div>
                
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table-fixed w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 w-20">ID</th>
                                <th class="px-4 py-2">User</th>
                                <th class="px-4 py-2">Business</th>
                                <th class="px-4 py-2">Skills Experience</th>
                                <th class="px-4 py-2">Passions Interests</th>
                                <th class="px-4 py-2">Values Goals</th>
                                <th class="px-4 py-2">Business Ideas</th>
                                <th class="px-4 py-2">Personal Notes</th>
                                <th class="px-4 py-2 w-32">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($businessIdeas as $businessIdea)
                                <tr>
                                    <td class="border px-4 py-2">{{ $businessIdea->id }}</td>
                                    <td class="border px-4 py-2">{{ $businessIdea->user->name ?? 'New Business' }}</td>
                                    <td class="border px-4 py-2">{{ $businessIdea->business->name ?? '' }}</td>

                                    <td class="border px-4 py-2">{!! $businessIdea->formattedPairs('skills_experience') !!}</td>

                                    <td class="border px-4 py-2">{!! $businessIdea->formattedPairs('passions_interests') !!}</td>
                                    <td class="border px-4 py-2">{!! $businessIdea->formattedPairs('values_goals') !!}</td>
                                    <td class="border px-4 py-2">{!! $businessIdea->formattedPairs('business_ideas') !!}</td>
                                    <td class="border px-4 py-2">{!! $businessIdea->formattedPairs('personal_notes') !!}</td>

                                    <td class="border px-4 py-2 w-32">
                                        <a href="{{ route('admin.business-ideas.show', $businessIdea) }}">
                                            <i class="fas fa-eye icon-blue"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.business-ideas.destroy', $businessIdea) }}"
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
