@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <h1 class="text-2xl font-bold mb-6">Create Outward File</h1>

    <!-- Display validation errors, if any -->
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('registry.outward-files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Folder -->
        {{-- <div>
            <label for="folder_id" class="block text-sm font-medium text-gray-700">Folder</label>
            <select name="folder_id" id="folder_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                <option value="" disabled selected>Select Folder</option>
                @foreach ($folders as $id => $name)
                    <option value="{{ old('id') }}">{{ $name }}</option>
                @endforeach
            </select>
        </div> --}}

        <!-- Ministry that owned the outward file -->
        <input type="hidden" name="ministry_id" value=" {{ Auth::user()->ministry_id }}">


        <!-- Ministries to send file to  -->
        <div>
            <label for="recipient_ministries" class="block text-sm font-medium text-gray-700">Select Ministry to send file to</label>
            <select name="recipient_ministries[]" id="recipient_ministries" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" multiple required>
                <option value="all" id="allOption">All Ministries</option> <!-- Option to select all -->
                @foreach ($ministries as $id => $name)
                    @if ($id != auth()->user()->ministry_id)
                        <option value="{{ $id }}" {{ in_array($id, old('recipient_ministries', [])) ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endif
                @endforeach
            </select>
            @error('ministries_sent_to') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <script>
            document.getElementById('recipient_ministries').addEventListener('change', function() {
                var select = document.getElementById('recipient_ministries');
                var allOption = document.getElementById('allOption');

                // If "All Ministries" is selected, select all options
                if (allOption.selected) {
                    // Select all ministries when 'All Ministries' is selected
                    for (var option of select.options) {
                        option.selected = true;
                    }
                } else {
                    // Deselect "All Ministries" if it's not selected
                    allOption.selected = false;
                }
            });
        </script>

        <!-- Division -->
        <div>
            <label for="division_id" class="block text-sm font-medium text-gray-700">Division</label>
            <select name="division_id" id="division_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                <option value="" disabled selected>Select Division</option>
                @foreach ($divisions as $id => $name)
                    <option value="{{ $id }}" {{ old('division_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @error('divisions') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <!-- File Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">File Name</label>
            <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('name')}}">
        </div>

        <!-- File Path -->
        <div>
            <label for="path" class="block text-sm font-medium text-gray-700">File Path</label>
            <input type="file" name="path" id="path" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <!-- Send Date -->
        <div>
            <label for="send_date" class="block text-sm font-medium text-gray-700">Send Date</label>
            <input type="date" name="send_date" id="send_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('send_date')}}" required>
        </div>

        <!-- Letter Date -->
        <div>
            <label for="letter_date" class="block text-sm font-medium text-gray-700">Letter Date</label>
            <input type="date" name="letter_date" id="letter_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('letter_date')}}" required>
        </div>

        <!-- Letter Ref No -->
        <div>
            <label for="letter_ref_no" class="block text-sm font-medium text-gray-700">Letter Reference Number</label>
            <input type="text" name="letter_ref_no" id="letter_ref_no" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('letter_ref_no')}}" required>
        </div>

        <!-- Details -->
        <div>
            <label for="details" class="block text-sm font-medium text-gray-700">Details</label>
            <textarea name="details" id="details" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="4" value="{{ old('details')}}" required></textarea>
        </div>

        <!-- From Details -->
        <div>
            <label for="from_details_name" class="block text-sm font-medium text-gray-700">From Details</label>
            <input type="text" name="from_details_name" id="from_details_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('from_details_name')}} " required>
        </div>

        <!-- To Details -->
        <div>
            <label for="to_details_name" class="block text-sm font-medium text-gray-700">To Details</label>
            <input type="text" name="to_details_name" id="to_details_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('to_details_name')}} " required>
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
            <select name="file_type_id" id="file_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" {{ old('is_active', 0) == 1 ? 'checked' : '' }}>
                <option value="" disabled selected>Select File Type</option>
                @foreach ($fileTypes as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Create File
        </button>
    </form>
</div>
@endsection

@push('styles')
<style>
    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        color: #495057;
        font-weight: 600;
        padding: 1rem;
    }

    .table td {
        vertical-align: middle;
        padding: 0.75rem;
        color: #555;
        font-size: 0.95rem;
    }

    /* Action Button Styles */
    .action-btn {
        cursor: pointer;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: background-color 0.3s, transform 0.2s;
    }

    .action-btn:hover {
        background-color: #e5e7eb;
    }

    /* Dropdown Menu Styles */
    .dropdown-menu {
        position: absolute;
        background-color: white;
        border-radius: 0.375rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        min-width: 160px;
        z-index: 1000;
    }

    .dropdown-item {
        display: block;
        padding: 0.5rem 1rem;
        color: #212529;
    }

    .dropdown-item:hover {
        background-color: #f3f4f6;
    }
</style>
@endpush

