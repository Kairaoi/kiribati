@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <h1 class="text-2xl font-bold mb-6">Create new role</h1>

    <form action="{{ route('registry.roles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

          <!-- Role Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
            <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('first_name')}}">
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
          </div>

            <!-- Permissions -->
            <div class="mt-4">
                <fieldset>
                    <legend class="block text-sm font-medium text-gray-700">Permissions</legend>
                    <div class="mt-1 space-y-2">
                        @foreach ($permissions as $permission)
                            <div class="flex items-center">
                                <input type="checkbox" name="permissions[{{ $permission->name }}]" id="permission-{{ $permission->name }}"
                                    value="{{ $permission->name }}"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="permission-{{ $permission->name }}" class="ml-2 text-sm text-gray-700">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </fieldset>
                @error('permissions')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Create Role
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

