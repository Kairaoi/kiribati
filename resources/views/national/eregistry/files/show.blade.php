@extends('layouts.app')

@section('content')
        @php
            $statusClass = match($file->status) {
                'Pending Action' => 'bg-red-100 text-red-700',
                'Dispatched' => 'bg-cyan-100 text-cyan-700',
                'Pending Review' => 'bg-yellow-100 text-yellow-700',
                'Reviewed' => 'bg-green-100 text-green-700',
                default => 'bg-gray-100 text-gray-600',
            };
        @endphp
        @php
            $statusClass2 = match(optional($circulation)->status) {
                'Pending Action' => 'bg-red-100 text-red-700',
                'Dispatched' => 'bg-cyan-100 text-cyan-700',
                'Pending Review' => 'bg-yellow-100 text-yellow-700',
                'Reviewed' => 'bg-green-100 text-green-700',
                default => 'bg-gray-100 text-gray-600',
            };
        @endphp
    {{-- <div class="container mx-auto font-poppins px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('dispatches.show', $file) }} </div> --}}
    <div class="container mx-w-5xl mx-auto">
       
        <div class="mx-auto  max-w-3xl bg-white justify-center border border-gray-200 rounded-lg shadow-sm p-4 mt-4">
            <!-- Subject -->
            <div class="mb-4">
                <p class="text-xs text-gray-500">File Subject</p>
                <p class="text-sm font-medium text-gray-800 mt-1">
                    {{ $file->subject?? 'N/A' }}
                </p>
            </div>

            <!-- Grid Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- @dd($circulation->status) --}}
                <div>
                    <p class="text-xs text-gray-500">File Status</p>
                    @if($file->ministry_id == auth()->user()->ministry_id)
                        <span class="inline-block mt-1 text-xs font-medium px-2 py-1 rounded-full {{ $statusClass }}">
                            {{ $file->status ?? 'N/A' }}
                        </span>
                    @elseif(optional($circulation)->to_ministry_id == auth()->user()->ministry_id && $file->ministry_id != auth()->user()->ministry_id) 
                        <span class="inline-block mt-1 text-xs font-medium px-2 py-1 rounded-full {{ $statusClass }}">
                            {{ $circulation->status ?? 'N/A' }}
                        </span>
                    @endif
                </div>  
                
                <!-- Source -->
                <div>
                    <p class="text-xs text-gray-500">Source / Origin</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">
                        {{ $file->source->name ?? 'N/A' }}
                    </p>
                </div>

                <!-- Reference -->
                <div>
                    <p class="text-xs text-gray-500">Reference No</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">
                        {{ $file->reference_no ?? 'N/A' }}
                    </p>
                </div>      

                <!-- Division -->
                @isset($file->division)
                <div>
                    <p class="text-xs text-gray-500">Division</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">
                        {{ $file->division->name ?? 'N/A' }}
                    </p>
                </div>
                @endisset

                <!-- Date -->
                <div>
                    <p class="text-xs text-gray-500">Date</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">
                        {{ $file->created_at?->format('d/m/Y') ?? 'N/A' }}
                    </p>
                </div>

                <!-- File Type -->
                <div>
                    <p class="text-xs text-gray-500">File Type</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">
                        {{ $file->fileType->name ?? 'N/A' }}
                    </p>
                </div>

                <!-- Due Date -->
                <div>
                    <p class="text-xs text-gray-500">Due Date</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">
                        {{ $file->due_date ? $file->due_date->format('d/m/Y') : 'N/A' }}
                    </p>
                </div>
                <div class="text-xs text-gray-500">
                    <button 
                        id="view-file-btn"
                        class="px-4 py-2 bg-cyan-600 text-white rounded-lg shadow hover:bg-cyan-700 transition"
                    >
                        View Main Letter & Supporting Docs
                    </button>
                </div>
            </div>
        </div>
        @if(optional($circulation)->to_ministry_id == auth()->user()->ministry_id && $file->ministry_id != auth()->user()->ministry_id) 
            <div class="mx-auto  max-w-3xl bg-white justify-center border border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">Review Officer</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{  optional($reviewOfficer)->name() ?? 'N/A'  }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Review Date</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                            {{ optional($circulation)->date_reviewed ? \Carbon\Carbon::parse($circulation->date_reviewed)->format('d M Y') : 'N/A' }}
                            </p>
                        </div>
                </div>
                <div class="mb-4">
                            <p class="text-xs text-gray-500">Review Comment</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{  optional($circulation)->review_comment ?? 'N/A'  }}
                            </p>
                </div>
                        
            
                <div>
                            <p class="text-xs text-gray-500">Assigned Officers</p>
                            @if($circulation?->activeAssignments->isNotEmpty())
                                {{-- @dd($fileCirculation->assignedOfficers) --}}
                                <ul class="list-disc list-inside text-gray-900 text-sm font-semibold space-y-2">
                                    @foreach($circulation->activeAssignments as $assignment)
                                        <div class="flex items-center mt-1 justify-between p-3 rounded-lg border border-gray-200 bg-white hover:shadow-sm transition">
                                            {{-- Left: Officer Info --}}
                                            <div class="flex items-center gap-4">
                                                <div>
                                                    <div class="text-gray-900 flex items-center gap-2">
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
            </div>
        @endif
    
  
    
    <div id="pdf-container" class="flex justify-center hidden mt-4 mb-4"></div>
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mt-6 mb-4 max-w-5xl mx-auto">            <!-- Status Message -->
            <div class="mb-6 text-center">
                <p class="text-md font-semibold">
                    Actions Available:
                </p>
            </div>
            <!-- Action Buttons -->
            <div class="flex justify-center gap-3">
                <!-- Dispatch -->
                @if($file->ministry_id == auth()->user()->ministry_id && auth()->user()->hasRole('registry'))
                    <button type="button"
                        id="dispatchBtn"
                        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition">
                        Dispatch File
                    </button>
                @endif

                @if($file->status == 'Pending Action')
                    @if($fileCirculations->pluck('to_ministry_id')->contains(Auth::user()->ministry_id) || 
                    ($file->ministry_id == auth()->user()->ministry_id && auth()->user()->hasRole('registry')) )
                        <button type="button"
                            id="circulateBtn"
                            class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition">
                            Circulate File
                        </button>
                    @endif
                @endif
            </div>
    
            {{-- dispatch file --}}
            @if($file->ministry_id == auth()->user()->ministry_id && auth()->user()->hasRole('registry'))
                <form method="POST" action="{{ route('registry.dispatches.store') }}" class="col-span-2 mt-4">
                    @csrf
                    <div id="dispatchMinistryPanel" class="hidden mt-4 p-5 border border-gray-200 rounded-xl bg-white shadow-sm">
                        <input type="hidden" name="file_id" value="{{ $file->id }}">

                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-800">
                                Recipient Ministries
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @forelse($fileCirculations as $circulation)
                                <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg bg-gray-50">
                                    <div class="text-sm text-gray-800">
                                        {{ optional($circulation)->toMinistry->name ?? 'Unknown Ministry' }}

                                        <div class="text-xs text-gray-500 mt-1">
                                            Dispatched:
                                            {{ optional($circulation->dispatch)->dispatch_date 
                                                ? \Carbon\Carbon::parse($circulation->dispatch->dispatch_date)->format('d M Y')
                                                : 'N/A' }}
                                        </div>
                                    </div>
                                    <span class="inline-block mt-1 text-xs font-medium px-2 py-1 rounded-full {{ $statusClass2 }}">
                                        {{ ucfirst($circulation->status ?? 'N/A') }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 col-span-2">
                                    No dispatched found.
                                </p>
                            @endforelse
                        </div>
                    </div>

                    <div id="dispatchPanel" class="hidden mt-4 p-4 border rounded-lg grid-cols-2 grid bg-gray-50">
                        <input type="hidden" name="file_id" value="{{ $file->id }}">
                        <p class="text-sm text-gray-600 mb-2">
                            Select Ministries to dispatch this file to:
                        </p>
                        @foreach($ministries as $id => $ministry)
                            <label class="flex items-center gap-3 text-sm text-gray-700">
                                <input type="checkbox"
                                    name="recipient_ministries[]" 
                                    value="{{ $ministry->id }}"
                                    class="text-cyan-600 border-gray-300 focus:ring-cyan-500">
                                <span>
                                    {{ $ministry->name }} ({{ $ministry->code }})
                                </span>
                            </label>
                        @endforeach
                            <button type="submit"
                                onclick="return confirm('Are you sure you want to dispatch this file?');"
                                class="mt-4 col-span-2 inline-flex items-center text-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition">
                                Confirm Dispatch
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 12l18-9-4 18-5-5-6 4 2-6z"/>
                                </svg>
                            </button>
                    </div>
                </form>
            @endif
            
            {{-- incoming file view --}}
            @if($fileCirculations->pluck('to_ministry_id')->contains(auth()->user()->ministry_id) || ($file->ministry_id == auth()->user()->ministry_id && auth()->user()->hasRole('registry')) )
                <div id="circulatePanel"
                    class="hidden mt-5 p-6 border border-gray-200 rounded-xl bg-white shadow-md space-y-5">

                    <!-- Header -->
                    <div>
                        <h3 class="text-sm font-seibold text-gray-800">
                            Confirm Review Officer
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            This officer will review and acknowledge this file.
                        </p>
                    </div>

                    <!-- Current Officer Card -->
                    <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ optional($reviewOfficer)->name() ?? 'Not assigned yet' }}                        </p>
                            </p>
                            <p class="text-xs text-gray-500">
                                Currently assigned review officer
                            </p>
                        </div>

                        @if(!$reviewOfficer)
                            <a href="{{ route('registry.users.edit-review-officer') }}"
                                id="changeOfficerBtn"
                                class="text-cyan-600 text-sm font-medium hover:text-cyan-700 hover:underline transition">
                                Select Review Officer
                            </a>
                        @else
                            <a href="{{ route('registry.users.edit-review-officer') }}"
                                id="changeOfficerBtn"
                                class="text-cyan-600 text-sm font-medium hover:text-cyan-700 hover:underline transition">
                                Select Review Officer
                            </a>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('registry.file-circulations.store') }}"
                        class="space-y-4">
                        @csrf
    
                        @if(!$reviewOfficer)
                            <div class="mb-3 p-3 rounded bg-red-50 border border-red-200 text-red-700 text-sm">
                                ⚠️ No Review Officer assigned. Please assign one before submitting.
                            </div>
                        @endif
                        <input type="hidden" name="file_id" value="{{ $fileId }}">
                        <input type="hidden" name="review_officer_id" value="{{ optional($reviewOfficer)->id }}">

                        <button type="submit"
                            class="w-full px-4 py-2 bg-gray-600 text-white rounded disabled:opacity-50"
                            @disabled(!$reviewOfficer)>
                            Submit
                        </button>
                    </form>

             @elseif(optional($circulation)->pluck('to_ministry_id')->contains(auth()->user()->ministry_id) && auth()->user()->hasRole('review-officer'))
                    <form method="POST" action="{{ route('registry.file.assign', $circulation) }}">
                        @csrf
                        <input type="hidden" name="file_id" value="{{ $file->id }}">
                        {{-- <label for="assignedOfficers" class="mr-4 text-m">Assign officer(s)</label> --}}
                            <select id="assignedOfficers"
                                    name="officers[]"
                                    multiple
                                    class="w-1/2 px-8 py-2 rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring focus:ring-cyan-200 focus:ring-opacity-50">
                                    @foreach ($usersWithDivision as $user)
                                        @if($user->hasRole('review-officer')) 
                                            @continue
                                        @endif
                                        <option value="{{ $user->id }}"
                                                {{ in_array($user->id, old('assignedOfficers', [])) ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{ $user->last_name }}: 
                                                {{($user->division_name ?? 'No Division') }}
                                        </option>
                                    @endforeach
                            </select>
                            <textarea name="review_comment" id="review_comment" rows="2" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" placeholder="Write your comment..."></textarea>
                                <button type="submit"
                                        class="w-full bg-cyan-600 hover:bg-cyan-700 text-white py-2 rounded-md font-semibold">
                                        Submit
                                </button>
                    </form> 
             @elseif($circulation->activeAssignments->contains('officer_id', auth()->user()->id) )
                    <form method="POST" action="{{ route('registry.file.reassign', $circulation->id) }}">
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
                                        !$circulation->activeAssignments->contains('officer_id', $officer->id) && 
                                        $circulation->to_review_file !== $officer->id) 
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
             @endif

         </div>
            
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const btn = document.getElementById('view-file-btn');
            const container = document.getElementById('pdf-container');

            if (!btn || !container) return;

            const pdfUrl = "{{ route('registry.files.view', $file->id) }}";

            let isOpen = false;

            btn.addEventListener('click', function () {

                if (isOpen) {
                    container.innerHTML = '';
                    container.classList.add('hidden');
                    isOpen = false;
                    return;
                }

                container.innerHTML = `
                    <embed src="${pdfUrl}" type="application/pdf" width="70%" height="700px" />
                `;

                container.classList.remove('hidden');
                isOpen = true;
            });

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const dispatchBtn = document.getElementById('dispatchBtn');
            const circulateBtn = document.getElementById('circulateBtn');

            const dispatchPanel = document.getElementById('dispatchPanel');
            const dispatchMinistriesPanel = document.getElementById('dispatchMinistryPanel');
            const circulatePanel = document.getElementById('circulatePanel');

            function hideAll() {
                if (dispatchPanel) dispatchPanel.classList.add('hidden');
                if (dispatchMinistriesPanel) dispatchMinistriesPanel.classList.add('hidden');
                if (circulatePanel) circulatePanel.classList.add('hidden');
            }

            function togglePanel(panel) {
                hideAll();
                if (panel) panel.classList.remove('hidden');
            }

            if (dispatchBtn) {
                dispatchBtn.addEventListener('click', function () {
                    togglePanel(dispatchPanel);
                    if (dispatchMinistriesPanel) {
                        dispatchMinistriesPanel.classList.remove('hidden');
                    }
                });
            }

            if (circulateBtn) {
                circulateBtn.addEventListener('click', function () {
                    togglePanel(circulatePanel);
                });
            }

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

@endsection