@extends('layouts.app')

@section('content')

<div class="max-w-full mx-auto py-6 px-4">

    {{-- HEADER --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>

    <div class="flex items-center gap-3 flex-wrap">
            {{-- ICON --}}
            <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-cyan-100 text-cyan-700 shadow-sm">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />

                </svg>

            </div>
            {{-- TITLE --}}
            <div>
                <h1 class="text-xl font-semibold text-gray-800 tracking-tight">
                    Audit Trail
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    File activity and change history
                </p>
            </div>

        </div>

    </div>

    {{-- REFERENCE NUMBER --}}
    <div class="inline-flex items-center gap-2 bg-cyan-50 border border-cyan-200 text-cyan-700 px-4 py-2.5 rounded-2xl shadow-sm">
        <span class="text-xs uppercase tracking-wide font-semibold text-cyan-600">
            Reference No
        </span>

        <span class="text-xs font-semibold">
            {{ $file->reference_no }}
        </span>
    </div>
</div>
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Module</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Event</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Old Values</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">New Values</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Changed By</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Time</th>
            </tr>
        </thead>

        {{-- TABLE BODY --}}
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse ($file->audits->sortByDesc('created_at') as $index => $audit)
                <tr class="hover:bg-gray-50 transition align-top">
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-700 uppercase tracking-wide">
                            {{ class_basename($audit->auditable_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-gray-800 capitalize">
                            {{ $audit->event }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if (!empty($audit->old_values))
                            <div class="space-y-2">
                                @foreach ($audit->old_values as $key => $value)
                                    <div class="bg-red-50 border border-red-100 rounded-lg px-3 py-2">
                                        <div class="text-xs font-semibold text-red-700 uppercase tracking-wide">
                                            {{ str_replace('_', ' ', $key) }}
                                        </div>
                                        <div class="text-sm text-gray-700 mt-1 break-words">
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">
                                —
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        @if (!empty($audit->new_values))
                            <div class="space-y-2">
                                @foreach ($audit->new_values as $key => $value)
                                    <div class="bg-green-50 border border-green-100 rounded-lg px-3 py-2">
                                        <div class="text-xs font-semibold text-green-700 uppercase tracking-wide">
                                            {{ str_replace('_', ' ', $key) }}
                                        </div>
                                        <div class="text-sm text-gray-700 mt-1 break-words">
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">
                                —
                            </span>
                        @endif
                    </td>

                    {{-- USER --}}
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-700">
                            {{ optional($audit->user)->first_name ?? 'System' }}
                        </div>
                    </td>
                    {{-- DATE --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600">
                            {{ $audit->created_at->format('d M Y') }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            {{ $audit->created_at->format('h:i A') }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="text-gray-400 text-lg font-medium">
                            No audit trail found
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            No activities have been recorded for this file yet.
                        </p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="overflow-x-auto mt-4">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Module</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Event</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Old Values</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">New Values</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Changed By</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Time</th>
            </tr>
        </thead>

        {{-- TABLE BODY --}}
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse ($dispatch->audits->sortByDesc('created_at') as $index => $audit)
                <tr class="hover:bg-gray-50 transition align-top">
                    <td class="px-4 py-4 text-sm text-gray-500">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-700 uppercase tracking-wide">
                            {{ class_basename($audit->auditable_type) }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-sm font-medium text-gray-800 capitalize">
                            {{ $audit->event }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        @if (!empty($audit->old_values))
                            <div class="space-y-2">
                                @foreach ($audit->old_values as $key => $value)
                                    <div class=" rounded-lg px-3 py-2">
                                        <div class="text-xs font-semibold text-red-700 uppercase tracking-wide">
                                            {{ str_replace('_', ' ', $key) }}
                                        </div>
                                        <div class="text-sm text-gray-700 mt-1 break-words">
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">
                                —
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-4">
                        @if (!empty($audit->new_values))
                            <div class="space-y-2">
                                @foreach ($audit->new_values as $key => $value)
                                    <div class="bg-green-50 border border-green-100 rounded-lg px-3 py-2">
                                        <div class="text-xs font-semibold text-green-700 uppercase tracking-wide">
                                            {{ str_replace('_', ' ', $key) }}
                                        </div>
                                        <div class="text-sm text-gray-700 mt-1 break-words">
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">
                                —
                            </span>
                        @endif
                    </td>

                    {{-- USER --}}
                    <td class="px-4 py-4">
                        <div class="text-sm font-medium text-gray-700">
                            {{ optional($audit->user)->first_name ?? 'System' }}
                        </div>
                    </td>
                    {{-- DATE --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600">
                            {{ $audit->created_at->format('d M Y') }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            {{ $audit->created_at->format('h:i A') }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="text-gray-400 text-lg font-medium">
                            No audit trail found
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            No activities have been recorded for this file yet.
                        </p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- {{ $file->audits->links() }} --}}

@endsection
