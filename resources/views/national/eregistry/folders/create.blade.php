@extends('layouts.app')

@section('title', 'Create Folder')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <h1 class="text-2xl font-bold mb-6">Create New Folder</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('registry.folders.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Ministry Selection -->
        <div>
            <label for="ministry_id" class="block text-sm font-medium text-gray-700">Ministry</label>
            <select id="ministry_id" name="ministry_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                <option value="">Select Ministry</option>
                @foreach ($ministries as $id => $ministry)
                    <option value="{{ $id }}">{{ $ministry }}</option>
                @endforeach
            </select>
        </div>

        <!-- Folder Number -->
        <div>
            <label for="folder_number" class="block text-sm font-medium text-gray-700">Folder Number</label>
            <input type="number" id="folder_number" name="folder_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>

        <!-- Folder Name -->
        <div>
            <label for="folder_name" class="block text-sm font-medium text-gray-700">Folder Name</label>
            <input type="text" id="folder_name" name="folder_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>

        <!-- Category (Optional) -->
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <input type="text" id="category" name="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <!-- Folder Description (Optional) -->
        <div>
            <label for="folder_description" class="block text-sm font-medium text-gray-700">Folder Description</label>
            <textarea id="folder_description" name="folder_description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
        </div>

        <!-- Active Status -->
        <div class="flex items-center">
            <input type="checkbox" id="is_active" name="is_active" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
            <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Create Folder
            </button>
            <a href="{{ route('registry.folders.index') }}" class="w-full block text-center bg-gray-600 text-white py-2 px-4 rounded-md mt-2 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection