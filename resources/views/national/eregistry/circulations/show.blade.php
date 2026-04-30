@extends('layouts.app')

@section('content')
<div class="container mx-w-5xl mx-auto">
    @php
        $pill = '';

        switch ($status) {
            case 'Pending Circulation':
                $pill = 'text-slate-500 ring-1 ring-slate-400/30';
                break;

            case 'Pending Review':
                $pill = 'text-amber-500 ring-1 ring-amber-400/30';
                break;

            case 'Reviewed':
                $pill = 'text-emerald-500 ring-1 ring-emerald-400/30';
                break;

            default:
                $pill = 'text-slate-500 ring-1 ring-slate-400/30';
                break;
        }
    @endphp

    <div class="mx-auto mt-2 mb-4 bg-white border border-gray-200 rounded-xl shadow-sm p-5 max-w-3xl">
        
        {{-- Subject --}}
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-500">File Subject</p>
            <p class="mb-3 text-gray-900 font-semibold">
                {{ $file->subject ?? 'N/A' }}
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
            {{-- File Status --}}
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Reference No</p>
                <p class="text-gray-900 font-semibold">
                    {{ $file->reference_no ?? 'N/A' }}
                </p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">File Status</p>
                <p> <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $pill }}">
                    {{ $status }}
                </span></p>
            </div>

            {{-- Organisation --}}
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Organisation</p>
                <p class="text-gray-900 font-semibold">
                    {{ $file->organisation->name ?? 'N/A' }}
                </p>
            </div>

            @isset($file->division)
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">Division</p>
                    <p class="text-gray-900 font-semibold">
                        {{ $file->division->name ?? 'N/A' }}
                    </p>
                </div>
            @endisset
            
            {{-- Created At --}}
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Date</p>
                <p class="text-gray-900 font-semibold">
                    {{ $file->created_at->format('d/m/Y') ?? 'N/A' }}
                </p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">File Type</p>
                <p class="text-gray-900 font-semibold">
                    {{ $file->fileType->name ?? 'N/A' }}
                </p>
            </div>
        </div>

        <div class="mt-4 flex flex-col md:flex-row gap-2">
             <!-- Main File -->
            <a href="{{ asset('storage/'.$file->main_file_path) }}"
            target="_blank"
            class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition">
                📄 View Main File
            </a>

            <!-- Supporting Documents -->
            @if(!empty($file->additional_file_paths))
                <div class="flex flex-wrap gap-2 mt-2">

                    @foreach($file->additional_file_paths as $index => $path)
                        <a href="{{ asset('storage/'.$path) }}"
                            target="_blank"
                            class="inline-flex items-center gap-2 bg-cyan-100 text-cyan-700 hover:bg-cyan-200 text-sm font-medium px-3 py-2 rounded-lg transition">
                            Support Doc {{ $index + 1 }}
                        </a>
                    @endforeach

                </div>
            @endif
        </div>
        <div class="mt-2 pt-4 border-t border-gray-400 grid grid-cols-1 md:grid-cols-3 gap-y-4 gap-x-8">             
            <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">Review Officer</p>
                    @if($fileCirculation?->toReviewFile)
                        <p class="text-gray-900 font-semibold">
                            {{ $fileCirculation->toReviewFile->first_name }} {{ $fileCirculation->toReviewFile->last_name }}
                        </p>
                    @else
                        <p class="text-gray-900 font-semibold">No reviewer assigned for this organisation</p>
                    @endif
            </div>

             {{-- Review Comment --}}
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Review Comment</p>
                @if($fileCirculation?->review_comment)
                    <p class="text-gray-900 font-semibold">
                        {{ $fileCirculation->review_comment }}
                    </p>
                @else
                    <p class="text-gray-900 font-semibold">-</p>
                @endif
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Date reviewed</p>
                @if($fileCirculation?->date_reviewed)
                    <p class="text-gray-900 font-semibold">
                        {{ $fileCirculation->date_reviewed ? \Carbon\Carbon::parse($fileCirculation->date_reviewed)->format('d M Y') : 'N/A' }}
                    </p>
                @else
                    <p class="text-gray-900 font-semibold">-</p>
                @endif
            </div>
        </div>

        <div class="mt-2 pt-4">
                <p class="text-xs uppercase tracking-wide text-gray-500">Assigned Officers</p>
                @if($fileCirculation?->activeAssignments->isNotEmpty())
                    {{-- @dd($fileCirculation->assignedOfficers) --}}
                    <ul class="list-disc list-inside text-gray-900 font-semibold space-y-2">
                        @foreach($fileCirculation->activeAssignments as $assignment)
                            <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-white hover:shadow-sm transition">
                                {{-- Left: Officer Info --}}
                                <div class="flex items-center gap-4">
                                    <div>
                                        <div class="font-semibold text-gray-900 flex items-center gap-2">
                                            {{ $assignment->officer->first_name }} {{ $assignment->officer->last_name }}

                                            @if($assignment->status === 'accepted')
                                                <span class="text-xs bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded-full">
                                                    Received
                                                </span>
                                            @elseif($assignment->status === 'pending')
                                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                                                    Pending
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $assignment->officer->division->name ?? 'No Division' }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: Reassignment Info --}}
                                <div class="text-right">

                                    @if($assignment->reassigned_from)
                                        <div class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded-md inline-block">
                                            Reassigned from:
                                            <span class="font-medium text-amber-700">
                                                {{ $assignment->reassignedFrom->first_name }}
                                                {{ $assignment->reassignedFrom->last_name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">Original assignment</span>
                                    @endif

                                </div>
                            </div>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-900 font-semibold">No assigned officers</p>
                @endif
        </div>
    </div>
        
    @if($status === 'Pending Circulation')
            <div class="max-w-2xl mt-4 mb-4 mx-auto space-y-4">
                <form method="POST" action="{{ route('registry.file-circulations.store') }}">
                    @csrf
                    <input type="hidden" name="file_id" value="{{ $file->id }}">
                    <input type="hidden" name="organisation_id" value="{{ auth()->user()->organisation_id }}"> 

                    <button type="submit"
                            class="w-full bg-cyan-600 hover:bg-cyan-700 text-white px-10 py-2 rounded-md font-semibold">
                        Circulate to Review Officer
                    </button>
                </form>
            </div>
    {{-- if user is the review officer and status of file is pending review --}}
    @elseif($status === 'Pending Review' && $loggedInOrganisation->review_officer_id === Auth::id())
        <div class="max-w-2xl mt-4 mb-4 mx-auto bg-white shadow-sm rounded-lg p-6 border space-y-4">
            <form method="POST" action="{{ route('registry.file.assign', $fileCirculation) }}">
                @csrf

                <input type="hidden" name="file_id" value="{{ $file->id }}">
                {{-- <label for="assignedOfficers" class="mr-4 text-m">Assign officer(s)</label> --}}
                    <select id="assignedOfficers"
                            name="officers[]"
                            multiple
                            class="w-full max-w-md px-8 py-2 rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring focus:ring-cyan-200 focus:ring-opacity-50">
                            @foreach ($usersWithDivision as $user)
                                @if($user->id === $fileCirculation->to_review_file || $user->hasRole('systemAdmin')) 
                                    @continue
                                @endif
                                <option value="{{ $user->id }}"
                                        {{ in_array($user->id, old('assignedOfficers', [])) ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }}: 
                                        {{($user->division_name ?? 'No Division') }}
                                </option>
                            @endforeach
                    </select>
                    <textarea name="review_comment" id="review_comment" rows="3" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" placeholder="Write your comment..."></textarea>
                        <button type="submit"
                                class="w-full bg-cyan-600 hover:bg-cyan-700 text-white py-2 rounded-md font-semibold">
                                Submit
                        </button>
            </form> 
        </div>
    @elseif($status === 'Reviewed' && $fileCirculation->activeAssignments->contains('officer_id', Auth::id()) && $fileAssignment->status === 'pending')
        <div class="mx-auto mt-2 mb-4 max-w-3xl px-6 py-4 bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('registry.file.reassign', $fileCirculation->id) }}">
                @csrf
                @if($fileAssignment->reassigned_from !== null)
                    <div class="mb-4 p-4 rounded-lg border border-amber-200 bg-amber-50">
                        {{-- Message --}}
                        <div class="flex items-start justify-between">
                            <div class="text-sm text-amber-700">
                                <span class="font-semibold">Reassigned File</span><br>
                                This file was reassigned to you from 
                                <span class="font-medium text-amber-800">
                                    {{ $fileAssignment->reassignedFrom->first_name }} 
                                    {{ $fileAssignment->reassignedFrom->last_name }}
                                </span>
                            </div>
                        </div>
                        {{-- Actions --}}
                        <div class="mt-4 space-y-3">

                            {{-- Accept --}}
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="action" value="accepted" class="action-radio">
                                <span class="text-gray-700 font-medium">
                                    Accept and mark as received
                                </span>
                            </label>

                            {{-- Reject --}}
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="action" value="rejected" class="action-radio" id="reject-radio">
                                <span class="text-red-600 font-medium">
                                    Reject and send back
                                </span>
                            </label>

                            {{-- Reject Comment --}}
                            <div id="reject-comment-box" class="hidden">
                                <textarea 
                                    name="reject_comment"
                                    rows="3"
                                    placeholder="Add reason for rejection..."
                                    class="w-full mt-2 p-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-red-300 focus:outline-none"
                                ></textarea>
                            </div>

                        </div>
                    </div>
                {{-- Action Selection --}}
                @elseif ($fileAssignment->reassigned_from === null)
                    <div class="mb-4 font-semibold border-b pb-4">
                            <label class="text-cyan-700">
                                This file has been assigned to you. Please select an action:
                            </label>
                            <div class="mt-2 space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="action" value="accepted" class="action-radio">
                                    <span class="text-gray-700">Accept and Mark as Received</span>
                                </label>

                                <label class="flex items-center gap-2">
                                    <input type="radio" name="action" value="reassign" class="action-radio">
                                    <span class="text-gray-700">Re-assign Officer</span>
                                </label>
            
                            </div>
                    </div>
                @endif
                {{-- Officer Dropdown (Hidden by default) --}}
                <div id="officer-select" class="mb-4 hidden">
                    <label class="block font-semibold text-gray-700 mb-1">Select Officer:</label>
                    <select name="new_officer_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-cyan-200">
                        <option value="">-- Choose officer in your division --</option>
                        @foreach($usersWithDivision as $officer) 
                            {{-- select only officers that are not assigned yet to this file & also officer that is in the same division  --}}
                            @if ($officer->division_id === auth()->user()->division_id && 
                                !$fileCirculation->activeAssignments->contains('officer_id', $officer->id) && 
                                $fileCirculation->to_review_file !== $officer->id) 
                                <option value="{{ $officer->id }}">{{ $officer->first_name }} {{ $officer->last_name }} - {{ $officer->division_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="bg-cyan-600 w-full hover:bg-cyan-800 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition duration-200">
                    Submit
                </button>
            </form>
        </div>
    @endif
    {{-- @elseif(Auth::user()->hasRole('registry'))
        <div class="mb-6 flex justify-center">
                <a href="{{ route('registry.file-circulations.index') }}"
                class="flex items-center gap-2 text-gray-600 hover:text-cyan-600">
                    ← Back
                </a>
            </div>  --}}
    <script>
        document.getElementById('view-file-btn').addEventListener('click', function() {
            const container = document.getElementById('pdf-container');

            // Toggle PDF visibility
            if (container.style.display !== 'none') {
                container.style.display = 'none';
                container.innerHTML = '';
                return;
            }

            // Show container
            container.style.display = '';

            // Embed PDF inline
            const pdfUrl = "{{ route('registry.files.view', $file->id) }}";
            container.innerHTML = `<embed src="${pdfUrl}" type="application/pdf" width="80%" height="800px" />`;
        });
    </script>

    <script>
        const choices = new Choices('#assignedOfficers', {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Select officers to assign',
            shouldSort: false
        });
    </script>

    {{-- Script --}}
    <script>
        const radios = document.querySelectorAll('.action-radio');
        const officerSelect = document.getElementById('officer-select');

        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.value === 'reassign') {
                    officerSelect.classList.remove('hidden');
                } else {
                    officerSelect.classList.add('hidden');
                }
            });
        });
    </script>
</div>
@endsection