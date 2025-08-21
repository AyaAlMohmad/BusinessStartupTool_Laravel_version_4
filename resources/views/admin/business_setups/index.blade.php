@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Business Setups') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center p-4 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Business Setup List</h3>
                    <a href="{{ route('admin.business-setup.analysis') }}" style="color: blue">
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
                                <th class="px-4 py-2">Type</th>
                                <th class="px-4 py-2">Notes</th>
                                <th class="px-4 py-2">Tasks</th>
                                <th class="px-4 py-2 w-32">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($setups as $setup)
                                <tr>
                                    <td class="border px-4 py-2">{{ $setup->id }}</td>
                                    <td class="border px-4 py-2">{{ $setup->user->name ?? 'N/A' }}</td>
                                    <td class="border px-4 py-2">{{ $setup->business->name ?? 'N/A' }}</td>
                                    <td class="border px-4 py-2">{{ $setup->type }}</td>
                                    <td class="border px-4 py-2">
                                        {{ is_array($setup->notes) ? ($setup->notes['detail'] ?? '') : $setup->notes }}
                                    </td>
                                    
                                    <td class="border px-4 py-2">
                                        @if($setup->tasks->count())
                                        <table class="table-auto w-full mb-6 border">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="px-4 py-2">Task Title</th>
                                                    <th class="px-4 py-2">Status</th>
                                                    <th class="px-4 py-2">Due Date</th>
                                                    <th class="px-4 py-2">Assigned To</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($setup->tasks as $task)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border px-4 py-2">{{ $task->title ?? '-' }}</td>
                                                        <td class="border px-4 py-2">{{ $task->status ?? '-' }}</td>
                                                        <td class="border px-4 py-2">{{ $task->due_date ?? '-' }}</td>
                                                        <td class="border px-4 py-2">{{ $task->assigned_to ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-gray-500 mb-6">No tasks linked to this setup.</p>
                                    @endif
                                     
                                    </td>
                                    
                                    <td class="border px-4 py-2 w-32">
                                        <a href="{{ route('admin.business-setups.show', $setup->id) }}" class="text-blue-500 mr-2">
                                            <i class="fas fa-eye icon-blue"></i>
                                        </a>
                                        <form action="{{ route('admin.business-setups.destroy', $setup->id) }}"
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
