@extends('layouts.app')

@section('title', 'Assign HOD')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mt-4 mb-4 shadow rounded-lg">

        <form action="{{ route('registry.divisions.update', $division) }}" method="POST">
            @csrf
            @method('PUT')

            <div class=" border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Division Details
                    </h2>
                </div>

                <dl>
                    <div class="grid grid-cols-1 gap-2 px-6 py-4 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">Division Name</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2">
                            {{ $division->name }}
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 px-6 py-4 sm:grid-cols-3">
                        <dt class="text-sm font-medium text-gray-500">Current HOD</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2">
                            {{ $division->hod?->name ?? '-' }}

                            <div class="mt-2">
                                <a href="{{ route('registry.divisions.assign-hod', $division) }}"
                                class="text-xs font-medium text-cyan-600 hover:text-cyan-800">
                                    Assign or edit HOD
                                </a>
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- <div class=" border border-gray-200 bg-white shadow-sm">
                <div class="border-b flex justify-between border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Units
                    </h2>
                    <a href="{{ route('registry.units.create', ['division_id' => $division->id]) }}"
                        class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 text-white text-sm rounded-md hover:bg-cyan-700 transition">
                        <i class="fas fa-plus"></i>
                        Create New Unit
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Name</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Head of Unit</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($units as $unit)
                                <tr>
                                    <td class="px-6 py-3 text-gray-900">
                                        {{ $unit->name }}
                                    </td>
                                  
                                    <td class="px-6 py-3 text-gray-700">
                                        {{ $unit->unit_head?->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-3">
                                        @if($unit->is_active)
                                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                Active
                                            </span>
                                        @else
                                            <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        <a href="{{ route('registry.units.edit', $unit) }}"
                                            class="text-cyan-600 hover:text-cyan-800">
                                            Edit Unit/Assign Head of Unit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No units assigned to this division.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div> --}}

            <div class=" border border-gray-200 bg-white shadow-sm">
                <div class="border-b flex justify-between border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Users
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Name</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Unit</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Designation</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($users as $user)
                                <tr>
                                    <td class="px-6 py-3 text-gray-900">
                                        {{ $user->name }}
                                    </td>
                                  
                                    <td class="px-6 py-3 text-gray-700">
                                        {{ $user->unit?->name ?? '-' }}
                                    </td>

                                    <td class="px-6 py-3 text-gray-700">
                                        {{ $user->designation ?? '-' }}
                                    </td>

                                    <td class="px-6 py-3">
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
                                    <td class="px-6 py-3">
                                        <a href="{{ route('registry.users.edit', ['user' => $user]) }}"
                                            class="text-cyan-600 hover:text-cyan-800">
                                            Edit/Assign Unit
                                        </a>
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
                                <th class="px-6 py-2 text-left font-semibold text-gray-600">Name</th>
                                <th class="px-6 py-2 text-left font-semibold text-gray-600">Email</th>
                                <th class="px-6 py-2 text-left font-semibold text-gray-600">Designation</th>
                                <th class="px-6 py-2 text-left font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($users as $user)
                                <tr>
                                    <td class="px-6 py-2 text-gray-900">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-2 text-gray-700">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-2 text-gray-700">
                                        {{ $user->designation ?? '-' }}
                                    </td>
                                    <td class="px-6 py-2">
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