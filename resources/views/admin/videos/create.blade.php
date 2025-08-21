@extends('layouts.app')
@section('content')
    <style>
        .custom-button {
            background-color: #4CAF50; 
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
    
        .custom-button:hover {
            background-color: #45a049; 
            transform: scale(1.05);
        }
    
        .custom-button:active {
            transform: scale(0.95);
        }
    </style>
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Add Video</h1>
        <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            <!-- Title Field (Dropdown) -->
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
                <select name="title" id="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="" disabled selected>Select a title</option>
                    <option value="Business_Idea">Business Idea</option>
                    <option value="Testing_Your_Idea">Testing Your Idea</option>
                    <option value="Market_Research">Market Research</option>
                    <option value="Start_Simple">Start Simple</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Marketing2">Marketing2</option>
                    <option value="Sales_Strategy">Sales Strategy</option>
                    <option value="Sales_Strategy2">Sales Strategy2</option>
                    <option value="Business_Setup">Business Setup</option>
                    <option value="Financial_Planning">Financial_Planning</option>
                    
                </select>
            </div>

            <!-- Video Upload Field -->
            <div class="mb-4">
                <label for="video" class="block text-gray-700 font-medium mb-2">Upload Video</label>
                <input type="file" name="video" id="video" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" accept="video/*" required>
                <p class="text-sm text-gray-500 mt-1">Supported formats: MP4, MOV, AVI, WMV. Max size: 100MB.</p>
            </div>

            <!-- Description Field -->
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea name="description" id="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4"></textarea>
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit" class="w-full custom-button">
                    Save
                </button>
            </div>
        </form>
    </div>
@endsection