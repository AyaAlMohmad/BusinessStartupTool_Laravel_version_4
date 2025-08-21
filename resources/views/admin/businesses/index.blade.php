@extends('layouts.app')
@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Landing Pagr') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center p-4 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Landing Page List</h3>
                    <a href="{{ route('admin.landing-page.analysis') }}" 
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
                                
                                <th class="px-4 py-2 w-32">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($businesses as $business)
                                <tr>
                                    <td class="border px-4 py-2">{{ $business->id }}</td>
                                    <td class="border px-4 py-2">{{ $business->user->name ?? 'New Business' }}</td>
                                    <td class="border px-4 py-2">{{ $business->name ?? '' }}</td>

                               
                                    <td class="border px-4 py-2 w-32">
                                        <a href="{{ route('admin.landing-page.show', $business->id) }}">
                                            <i class="fas fa-eye icon-blue"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.landing-page.destroy', $business->id) }}"
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
