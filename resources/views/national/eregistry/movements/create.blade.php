@extends('layouts.app')

@section('title', 'Create Movement')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <h1 class="text-2xl font-bold mb-6">Create New Movement</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('registry.movements.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- File Selection -->
        <div>
            <label for="file_id" class="block text-sm font-medium text-gray-700">File</label>
            <select name="file_id" id="file_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('file_id') is-invalid @enderror">
                <option value="">Select a File</option>
                @foreach ($files as $id => $file)
                    <option value="{{ $id }}" {{ old('file_id') == $id ? 'selected' : '' }}>{{ $file }}</option>
                @endforeach
            </select>
            @error('file_id')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- From Ministry -->
        <div>
            <label for="from_ministry_id" class="block text-sm font-medium text-gray-700">From Ministry</label>
            <select name="from_ministry_id" id="from_ministry_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('from_ministry_id') is-invalid @enderror">
                <option value="">Select a Ministry</option>
                @foreach ($ministries as $id => $ministry)
                    <option value="{{ $id }}" {{ old('from_ministry_id') == $id ? 'selected' : '' }}>{{ $ministry }}</option>
                @endforeach
            </select>
            @error('from_ministry_id')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- To Ministry -->
        <div>
            <label for="to_ministry_id" class="block text-sm font-medium text-gray-700">To Ministry</label>
            <select name="to_ministry_id" id="to_ministry_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('to_ministry_id') is-invalid @enderror">
                <option value="">Select a Ministry</option>
                @foreach ($lists as $id => $ministry)
                    <option value="{{ $id }}" {{ old('to_ministry_id') == $id ? 'selected' : '' }}>{{ $ministry }}</option>
                @endforeach
            </select>
            @error('to_ministry_id')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- From Division -->
        {{--<div>
            <label for="from_division_id" class="block text-sm font-medium text-gray-700">From Division</label>
            <select name="from_division_id" id="from_division_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('from_division_id') is-invalid @enderror">
                <option value="">Select a Division</option>
                @foreach ($divisions as $id => $division)
                    <option value="{{ $id }}" {{ old('from_division_id') == $id ? 'selected' : '' }}>{{ $division }}</option>
                @endforeach
            </select>
            @error('from_division_id')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>--}}

       {{-- <!-- To Division -->
        <div>
            <label for="to_division_id" class="block text-sm font-medium text-gray-700">To Division</label>
            <select name="to_division_id" id="to_division_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('to_division_id') is-invalid @enderror">
                <option value="">Select a Division</option>
                @foreach ($divisions as $id => $division)
                    <option value="{{ $id }}" {{ old('to_division_id') == $id ? 'selected' : '' }}>{{ $division }}</option>
                @endforeach
            </select>
            @error('to_division_id')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>--}}

       {{-- <!-- From User -->
        <div>
            <label for="from_user_id" class="block text-sm font-medium text-gray-700">From User</label>
            <select name="from_user_id" id="from_user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('from_user_id') is-invalid @enderror">
                <option value="">Select a User</option>
                @foreach ($users as $id => $user)
                    <option value="{{ $id }}" {{ old('from_user_id') == $id ? 'selected' : '' }}>{{ $user }}</option>
                @endforeach
            </select>
            @error('from_user_id')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>--}}

        <!-- To User -->
        <div>
            <label for="to_user_id" class="block text-sm font-medium text-gray-700">To User</label>
            <select name="to_user_id" id="to_user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('to_user_id') is-invalid @enderror">
                <option value="">Select a User</option>
                @foreach ($users as $id => $user)
                    <option value="{{ $id }}" {{ old('to_user_id') == $id ? 'selected' : '' }}>{{ $user }}</option>
                @endforeach
            </select>
            @error('to_user_id')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Movement Dates -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Start Date -->
            <div>
                <label for="movement_start_date" class="block text-sm font-medium text-gray-700">Movement Start Date</label>
                <input type="date" name="movement_start_date" id="movement_start_date" value="{{ old('movement_start_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('movement_start_date') is-invalid @enderror">
                @error('movement_start_date')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- End Date -->
            <div>
                <label for="movement_end_date" class="block text-sm font-medium text-gray-700">Movement End Date</label>
                <input type="date" name="movement_end_date" id="movement_end_date" value="{{ old('movement_end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('movement_end_date') is-invalid @enderror">
                @error('movement_end_date')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Comments -->
        <div>
            <label for="comments" class="block text-sm font-medium text-gray-700">Comments</label>
            <textarea name="comments" id="comments" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('comments') is-invalid @enderror">{{ old('comments') }}</textarea>
            @error('comments')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Status -->
<div>
    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('status') is-invalid @enderror">
        <option value="pending_registry" {{ old('status') == 'pending_registry' ? 'selected' : '' }}>Pending Registry</option>
        <option value="pending_secretary_review" {{ old('status') == 'pending_secretary_review' ? 'selected' : '' }}>Pending Secretary Review</option>
        <option value="pending_staff_assignment" {{ old('status') == 'pending_staff_assignment' ? 'selected' : '' }}>Pending Staff Assignment</option>
        <option value="assigned_to_staff" {{ old('status') == 'assigned_to_staff' ? 'selected' : '' }}>Assigned to Staff</option>
        <option value="in_circulation" {{ old('status') == 'in_circulation' ? 'selected' : '' }}>In Circulation</option>
        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
        <option value="returned" {{ old('status') == 'returned' ? 'selected' : '' }}>Returned</option>
    </select>
    @error('status')
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror
</div>


        <!-- Submit Button -->
        <div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Create Movement
            </button>
        </div>
    </form>
</div>
@endsection
