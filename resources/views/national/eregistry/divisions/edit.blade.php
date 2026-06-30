@extends('layouts.app')

@section('title', 'Assign HOD')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mt-4 mb-4 shadow rounded-lg">

        <form action="{{ route('registry.divisions.update', $division) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                       Edit Division
                    </h2>
                </div>

                <dl>
                    <div class="grid grid-cols-1 gap-2 px-6 py-4 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">
                            Division Name
                        </dt>

                        <dd class="sm:col-span-2">
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $division->name) }}"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                required
                            >

                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 px-6 py-4 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">Current HOD</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2">
                            {{ $division->hod?->name ?? 'No HOD assigned' }}

                            <div class="mt-2">
                                <a href="{{ route('registry.divisions.assign-hod', $division) }}"
                                class="text-xs font-medium text-cyan-600 hover:text-cyan-800">
                                    Change HOD
                                </a>
                            </div>
                        </dd>
                    </div>

                    <div class="flex justify-end border-t border-gray-200 px-6 py-4">
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