@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <h1 class="text-2xl font-bold mb-6">Create new user</h1>

    <form action="{{ route('registry.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if(session('error'))
            <div class="text-red-500 px-4 py-3 rounded-md shadow-md mb-6 !important">
                <strong class="font-bold">Error! </strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Ministry -->
        <div>
            <label for="ministry_id" class="block text-sm font-medium text-gray-700">Ministry:</label>

            <!-- Display Ministry as a Read-Only Input -->
            <input type="text" id="ministry_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed sm:text-sm"
                value="{{ auth()->user()->ministry->name }}" readonly>

            <!-- Hidden Input to Submit Ministry ID -->
            <input type="hidden" name="ministry_id" value="{{ auth()->user()->ministry->id }}">

            @error('ministry_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

          <!-- First Name -->
          <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" name="first_name" id="first_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('first_name')}}">
            @error('first_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Last Name -->
        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('last_name')}}">
            @error('last_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Work email address</label>
            <input type="email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('email')}}">
            @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Role -->
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select name="role" id="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                <option value="" disabled selected>Select a role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
            @error('role')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>


        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Create User
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

