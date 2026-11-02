@extends('layouts.app')

@section('title', 'Assign HOD')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mt-2 mb-4 shadow rounded-lg">

        <form action="{{ route('registry.divisions.update', $division) }}" method="POST">
            @csrf
            @method('PUT')
        
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-3">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Edit Division
                    </h2>
                </div>

                <dl>
                    {{-- Ministry --}}
                    <div class="grid grid-cols-1 gap-2 px-6 py-4 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">
                            Ministry
                        </dt>

                        <dd class="sm:col-span-2">
                            @if ($isSystemAdmin)
                                <select
                                    name="ministry_id"
                                    id="ministry_id"
                                    required
                                    class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                >
                                    <option value="">Select ministry</option>

                                    @foreach ($ministries as $ministry)
                                        <option
                                            value="{{ $ministry->id }}"
                                            @selected(
                                                (string) old('ministry_id', $division->ministry_id)
                                                === (string) $ministry->id
                                            )
                                        >
                                            {{ $ministry->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    type="text"
                                    value="{{ $user->organisation->name }}"
                                    readonly
                                    class="block w-full rounded-lg border-gray-300 bg-gray-50 text-sm text-gray-600 shadow-sm"
                                >

                                <input
                                    type="hidden"
                                    id="organisation_id"
                                    name="organisation_id"
                                    value="{{ $user->organisation_id }}"
                                >
                            @endif

                            @error('ministry_id')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </dd>
                    </div>

                    {{-- Division name --}}
                    <div class="grid grid-cols-1 gap-2 px-6 py-3 sm:grid-cols-3">

                        <dt class="text-sm font-medium text-gray-500">
                            Division Name
                        </dt>

                        <dd class="sm:col-span-2">
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $division->name) }}"
                                required
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                            >

                            @error('name')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 px-6 py-3 sm:grid-cols-3">

                        <dt class="text-sm font-medium text-gray-500">
                            Location 
                        </dt>

                        <dd class="sm:col-span-2">
                            <input
                                type="text"
                                name="location"
                                id="location"
                                value="{{ old('location', $division->location) }}"
                                required
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                            >

                            @error('location')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 px-6 py-3 sm:grid-cols-3">

                        <dt class="text-sm font-medium text-gray-500">
                            Email
                        </dt>

                        <dd class="sm:col-span-2">
                            <input
                                type="text"
                                name="email"
                                id="email"
                                value="{{ old('email', $division->email) }}"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                            >

                            @error('email')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 px-6 py-3 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">
                            Phone
                        </dt>
                        <dd class="sm:col-span-2">
                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                value="{{ old('phone', $division->phone) }}"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                            >
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </dd>
                    </div>

                    {{-- Current HOD --}}
                    <div class="grid grid-cols-1 gap-2 px-6 py-3 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">
                            Head of Division (HOD)
                        </dt>

                        <dd class="sm:col-span-2">
                            <div class="flex items-center gap-3">
                                <input
                                    type="text"
                                    value="{{ $division->hod?->name ?? 'No HOD assigned' }}"
                                    readonly
                                    class="block w-full rounded-lg border-gray-300 bg-gray-50 text-sm text-gray-600 shadow-sm"
                                >

                                <a
                                    href="{{ route('registry.divisions.assign-hod', $division) }}"
                                    class="inline-flex shrink-0 items-center rounded-lg border border-cyan-600 px-4 py-2 text-sm font-medium text-cyan-600 transition hover:bg-cyan-50 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2"
                                >
                                    Edit
                                </a>
                            </div>
                        </dd>
                    </div>

                    <div class="flex border-t border-gray-200 px-6 py-4">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-cyan-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2"
                        >
                            Save Changes
                        </button>
                    </div>
                </dl>
            </div>

            {{-- <div class="mt-6 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Users in this Division
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Name</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Email</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Designation</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($users as $user)
                                <tr>
                                    <td class="px-6 py-4 text-gray-900">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $user->designation ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->is_active)
                                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                Active
                                            </span>
                                        @else
                                            <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No users assigned to this division.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div> --}}
        </form>
    </div>
</div>
@endsection