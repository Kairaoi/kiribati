@extends('layouts.app')

@section('content')

<div class="container mx-auto font-poppins px-4 py-8 max-w-6xl mt-3 rounded-md min-h-screen">

    <h1 class="text-2xl font-bold mb-6">Activity Logs</h1>

    <div class="bg-white shadow rounded-lg overflow-hidden">

        <table class="w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">User</th>
                    <th class="p-3 text-left">Action</th>
                    <th class="p-3 text-left">Subject</th>
                    <th class="p-3 text-left">Date</th>
                </tr>
            </thead>

            <tbody>

                @forelse($activities as $activity)

                <tr class="border-b">

                    <td class="p-3">
                        {{ $activity->causer->first_name ?? 'N/A' }} {{ $activity->causer->last_name ?? 'N/A' }}
                    </td>

                    <td class="p-3">
                        {{ $activity->description }}
                    </td>

                    <td class="p-3">
                        {{ class_basename($activity->subject_type) }}
                        (ID: {{ $activity->subject_id }})
                    </td>

                    <td class="p-3">
                        {{ $activity->created_at->format('d M Y H:i') }}
                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="4" class="p-3 text-center text-gray-500">
                        No activity logs found
                    </td>
                </tr>

                @endforelse

            </tbody>
        </table>

    </div>

    <div class="mt-4">
        {{ $activities->links() }}
    </div>

</div>

@endsection