@extends('layouts.app')
@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Websites List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">All Websites</h3>
                    <a href="{{ route('admin.websites.analysis') }}" style="color: blue">
                        <i class="fas fa-chart-bar mr-2"></i>
                        View Analysis
                    </a>
                </div>

         
                    <div class="p-6 overflow-x-auto">
                        <table class="table-auto text-sm border w-full">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-2 py-2 whitespace-nowrap">ID</th>
                                    <th class="px-2 py-2 whitespace-nowrap">User</th>
                                    <th class="px-2 py-2 whitespace-nowrap">Business</th>
                                    <th class="px-2 py-2 whitespace-nowrap">Business Name</th>
                                    <th class="px-2 py-2 whitespace-nowrap">Description</th>
                                    <th class="px-2 py-2 whitespace-nowrap">About Us</th>
                                    <th class="px-2 py-2 whitespace-nowrap">Color</th>
                                    <th class="px-2 py-2 whitespace-nowrap">Logo Style</th>
                                    <th class="px-2 py-2 whitespace-nowrap">Social Proof</th>
                                    <th class="px-2 py-2 whitespace-nowrap">Contact Info</th>
                                    <th class="px-2 py-2 whitespace-nowrap">Services</th>
                                    <th class="px-2 py-2 whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($websites as $website)
                                    <tr class="border-b">
                                        <td class="px-2 py-1 whitespace-nowrap">{{ $website->id }}</td>
                                        <td class="px-2 py-1 whitespace-nowrap">{{ $website->user->name ?? '-' }}</td>
                                        <td class="px-2 py-1 whitespace-nowrap">{{ $website->business->businessname ?? '-' }}</td>
                                        <td class="px-2 py-1 truncate max-w-[150px]">{{ $website->business_name }}</td>85
                                        <td class="px-2 py-1 truncate max-w-[200px]">{{ $website->business_description }}</td>
                                        <td class="px-2 py-1 truncate max-w-[200px]">{{ $website->about_us }}</td>
                                        <td class="px-2 py-1 whitespace-nowrap">{{ $website->colour_choice }}</td>
                                        <td class="px-2 py-1 whitespace-nowrap">{{ $website->logo_style_choice }}</td>
                                        <td class="px-2 py-1 truncate max-w-[150px]">{{ $website->social_proof }}</td>
                                        <td class="px-2 py-1">
                                            <ul class="list-disc pl-4 max-w-[180px] truncate">
                                                @foreach ((array) $website->contact_info as $info)
                                                    <li class="truncate">{{ $info }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-2 py-1 max-w-[220px]">
                                            @if ($website->services->count())
                                                <table class="table-auto border text-xs w-full">
                                                    <thead>
                                                        <tr>
                                                            <th class="border px-1 py-1">#</th>
                                                            <th class="border px-1 py-1">Name</th>
                                                            <th class="border px-1 py-1">Desc</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($website->services as $index => $service)
                                                            <tr>
                                                                <td class="border px-1 py-1">{{ $index + 1 }}</td>
                                                                <td class="border px-1 py-1 truncate max-w-[100px]">{{ $service->name }}</td>
                                                                <td class="border px-1 py-1 truncate max-w-[100px]">{{ $service->description ?? '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <span class="text-gray-500 text-xs">No services</span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-1 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.websites.show', $website->id) }}">
                                                    <i class="fas fa-eye text-blue-500"></i>
                                                </a>
                                                <form action="{{ route('admin.websites.destroy', $website->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700">
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
