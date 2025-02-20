@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <h1 class="text-2xl font-bold mb-6">Create File</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('registry.files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Ministry -->
        <div>
            <label for="ministry_id" class="block text-sm font-medium text-gray-700">Ministry</label>
            <select name="ministry_id" id="ministry_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                <option value="" disabled selected>Select Ministry</option>
                @foreach ($ministries as $id => $name)
                    <option value="{{ $id }}" {{ old('ministry_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Folder -->
        <div>
            <label for="folder_id" class="block text-sm font-medium text-gray-700">Folder</label>
            <select name="folder_id" id="folder_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                <option value="" disabled selected>Select Folder</option>
                @foreach ($folders as $id => $name)
                    <option value="{{ $id }}" {{ old('folder_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <!-- File Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">File Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>

        <!-- File Upload -->
        <div>
            <label for="file" class="block text-sm font-medium text-gray-700">File</label>
            <input type="file" name="file" id="file" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>

        <!-- Details -->
        <div>
            <label for="details" class="block text-sm font-medium text-gray-700">Details</label>
            <textarea name="details" id="details" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="4" required>{{ old('details') }}</textarea>
        </div>

        <!-- From Details -->
        <div>
            <label for="from_details_name" class="block text-sm font-medium text-gray-700">From Details</label>
            <input type="text" name="from_details_name" id="from_details_name" value="{{ old('from_details_name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>

        <!-- To Details -->
        <div>
            <label for="to_details_person_name" class="block text-sm font-medium text-gray-700">To Details</label>
            <input type="text" name="to_details_person_name" id="to_details_person_name" value="{{ old('to_details_person_name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>

         <!-- Receive Date -->
         <div>
            <label for="receive_date" class="block text-sm font-medium text-gray-700">Receive Date</label>
            <input type="date" name="receive_date" id="receive_date" value="{{ old('receive_date') ?? date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>

        <!-- Letter Date -->
        <div>
            <label for="letter_date" class="block text-sm font-medium text-gray-700">Letter Date</label>
            <input type="date" name="letter_date" id="letter_date" value="{{ old('letter_date') ?? date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>


        <!-- Security Level -->
        <div>
            <label for="security_level" class="block text-sm font-medium text-gray-700">Security Level</label>
            <select name="security_level" id="security_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                <option value="public" {{ old('security_level') == 'public' ? 'selected' : '' }}>Public</option>
                <option value="internal" {{ old('security_level') == 'internal' ? 'selected' : '' }}>Internal</option>
                <option value="confidential" {{ old('security_level') == 'confidential' ? 'selected' : '' }}>Confidential</option>
                <option value="strictly_confidential" {{ old('security_level') == 'strictly_confidential' ? 'selected' : '' }}>Strictly Confidential</option>
            </select>
        </div>

        <!-- File Type -->
        <div>
            <label for="file_type_id" class="block text-sm font-medium text-gray-700">File Type</label>
            <select name="file_type_id" id="file_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                <option value="" disabled selected>Select File Type</option>
                @foreach ($file_types as $id => $name)
                    <option value="{{ $id }}" {{ old('file_type_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Create File
        </button>
    </form>
</div>
@endsection
