@extends('layouts.app')

@section('title', 'Assign HOD')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white mt-4 mb-4 shadow rounded-lg">

        <div class="px-6 py-4 border-b">
            <h1 class="text-md font-semibold text-gray-500">
                Assign Head of Division
            </h1>
        </div>

        <form action="{{ route('registry.divisions.update-hod', $division) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">

                {{-- Division --}}
                <div>
                    <label class="block text-sm font-medium text-gray-500">
                        Division
                    </label>

                    <p class="mt-1 text-gray-900 font-semibold">
                        {{ $division->name }}
                    </p>
                </div>

                {{-- Current HOD --}}
                <div>
                    <label class="block text-sm font-medium text-gray-500">
                        Current Head of Division
                    </label>

                    <p class="mt-1 text-gray-900">
                        {{ $division->hod?->name ?? 'No HOD assigned' }}
                    </p>
                </div>

                {{-- Select HOD --}}
                <div>
                    <label for="hod_id" class="block text-sm font-medium text-gray-500">
                        Select New Head of Division
                        <span class="text-red-600">*</span>
                    </label>

                    <select
                        id="hod_id"
                        name="hod_id"
                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                        required>

                        <option value="">Select a user</option>

                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                @selected(old('hod_id', $division->hod_id) == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('hod_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                <a href="{{ route('registry.divisions.index') }}"
                    class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                    Cancel
                </a>

                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-cyan-500 text-white hover:bg-cyan-600">
                    Save HOD
                </button>
            </div>

        </form>
    </div>
</div>
@endsection