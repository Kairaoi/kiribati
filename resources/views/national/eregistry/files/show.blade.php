@extends('layouts.app')

@section('content')
        @php
            $statusClass = match($file->status) {
                'Pending Action' => 'bg-red-100 text-red-700',
                'Received' => 'bg-blue-100 text-blue-700',
                'Dispatched' => 'bg-cyan-100 text-cyan-700',
                'Pending Review' => 'bg-yellow-100 text-yellow-700',
                'Reviewed' => 'bg-green-100 text-green-700',
                default => 'bg-gray-100 text-gray-600',
            };
        @endphp
        @php
            $statusClass2 = match(optional($circulation)->status) {
                'Received' => 'bg-blue-100 text-blue-700',
                'Pending Action' => 'bg-red-100 text-red-700',
                'Dispatched' => 'bg-cyan-100 text-cyan-700',
                'Pending Review' => 'bg-yellow-100 text-yellow-700',
                'Reviewed' => 'bg-green-100 text-green-700',
                default => 'bg-gray-100 text-gray-600',
            };
        @endphp
        <div class="container mx-w-5xl mx-auto">
            {{-- File Info --}}
            <div class="mx-auto  max-w-5xl bg-white justify-center border border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                <div class="mb-4">
                    <p class="text-sm text-gray-500">File Subject</p>
                    <p class="text-md font-medium text-gray-800 mt-1">
                        {{ $file->subject?? 'N/A' }}
                    </p>
                </div>

                <!-- Grid Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">File Status</p>
                        @if($file->ministry_id == auth()->user()->ministry_id && !$circulation)
                            <span class="inline-block mt-1 text-xs font-medium px-2 py-1 rounded-full {{ $statusClass }}">
                                {{ $file->status ?? 'N/A' }}
                            </span>
                        @elseif($circulation?->to_ministry_id == auth()->user()->ministry_id && $file->ministry_id != auth()->user()->ministry_id) 
                            <span class="inline-block mt-1 text-xs font-medium px-2 py-1 rounded-full {{ $statusClass2 }}">
                                {{ $circulation->status ?? 'N/A' }}
                            </span>
                        @elseif($file->ministry_id == auth()->user()->ministry_id && $circulation && $circulation?->to_ministry_id == auth()->user()->ministry_id) 
                            <span class="inline-block mt-1 text-xs font-medium px-2 py-1 rounded-full {{ $statusClass2 }}">
                                {{ $circulation->status ?? 'N/A' }}
                            </span>
                        @endif
                    </div>  
                    <div>
                        <p class="text-sm text-gray-500">Source / Origin</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->source->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Reference No</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->reference_no ?? '-' }}
                        </p>
                    </div>     
                    <div>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
                        <p class="text-sm text-gray-500">Document Source</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->document_source ?? '-' }}
                        </p>
                    </div>  
                    <div>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
                        <p class="text-sm text-gray-500">Correspondence Type</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->correspondence_type ?? '-' }}
                        </p>
                    </div>  
                    @isset($file->ufsOfficer)
                        <div>
                            <p class="text-sm text-gray-500">UFS Officer</p>
                            <p class="text-md font-medium text-gray-800 mt-1">
                                {{ $file->ufsOfficer?->name ?? '-' }}
                            </p>
                        </div>   
                    @endisset                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     

                    @isset($file->division)
                        <div>
                            <p class="text-sm text-gray-500">Division</p>
                            <p class="text-md font-medium text-gray-800 mt-1">
                                {{ $file->division->name ?? 'N/A' }}
                            </p>
                        </div>
                    @endisset
                    <div>
                        <p class="text-sm text-gray-500">Date Created</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->created_at?->format('d/m/Y') ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">File Type</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->fileType->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Due Date</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->due_date ? $file->due_date->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Circulation Review details --}}
            @if($circulation?->to_ministry_id == $ministryId) 
                <div class="mx-auto  max-w-5xl justify-center border border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">Review Officer</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{ optional($reviewOfficer)->name ?? 'N/A' }}
                            </p>
                            @if($circulation?->status == 'Pending Review' && auth()->user()->hasRole('registry'))
                                <a href="{{ route('registry.users.edit-review-officer') }}"
                                    class="inline-flex items-center text-xs font-medium text-cyan-600 hover:text-cyan-800 hover:underline transition">
                                    Change Review Officer
                                </a>
                            @endif
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
                        <p class="text-sm font-medium text-gray-800 mt-1 break-all"">
                            {{  optional($circulation)->review_comment ?? 'N/A'  }}
                        </p>
                    </div>        
                    <div>
                        <p class="text-xs text-gray-500">Assigned Officers</p>
                        @if($circulation?->activeAssignments->isNotEmpty())
                            <ul class="list-disc list-inside text-gray-900 text-sm font-semibold space-y-2">
                                @foreach($circulation->activeAssignments as $assignment)
                                    <div class="flex items-center mt-1 justify-between p-3 rounded-lg border border-gray-200 bg-white hover:shadow-sm transition">
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
                                            <div class="text-right">
                                                @if($assignment->reassigned_from)
                                                    <div class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded-md inline-block">
                                                        Reassigned from:
                                                        <span class="font-medium text-amber-700">
                                                            {{ $assignment->reassignedFrom->name }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400">Original assignment</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-800 font-semibold">N/A</p>
                        @endif
                    </div>
                </div>
            @endif  

            {{-- Dispatch Details  --}}
            @if($file->ministry_id == $ministryId && $fileCirculations->isNotEmpty())
                <div class="mx-auto  max-w-5xl justify-center border border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            Dispatch Details
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @forelse($fileCirculations as $circulation)
                            <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 p-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ optional($circulation?->toMinistry)->name ?? 'Unknown Ministry' }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Dispatched:
                                        {{ optional($circulation?->dispatch)->dispatch_date
                                            ? \Carbon\Carbon::parse($circulation->dispatch->dispatch_date)->format('d M Y')
                                            : 'N/A' 
                                        }}
                                    </p>
                                    @if($circulation?->activeAssignments?->isNotEmpty())
                                        <div class="mt-2 space-y-1">
                                            @foreach($circulation->activeAssignments as $assignment)
                                                <div class="text-xs text-gray-600">
                                                    <span class="font-medium text-gray-700">
                                                        Assigned:
                                                    </span>
                                                    {{ $assignment->officer?->name ?? 'Unknown Officer' }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="mt-2 text-xs text-gray-400 italic">
                                            No active assignments
                                        </p>
                                    @endif
                                </div>
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass2 }}">
                                    {{ $circulation?->status ?? 'N/A' }}
                                </span>
                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
            @endif
            
            {{-- Main Letter / PDF Viewer --}}
            <div id="pdf-container" class="mt-4 mb-4 flex justify-center">
                <embed  src="{{ route('registry.files.view', $file->id) }}"
                        type="application/pdf"
                        class="w-full max-w-5xl rounded-2xl border border-gray-200 shadow-sm bg-white"
                        style="height: 900px;"
                >
            </div>              
                        
            {{-- Actions --}}
            <div class="p-6 mt-6 mb-4 max-w-5xl mx-auto">
                <div class="w-full space-y-6">
                    @can('update', $file)
                        <a href="{{ route('registry.files.edit', $file) }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-6 py-3 text-sm font-medium text-amber-700 shadow-sm transition-all duration-200 hover:bg-amber-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" 
                                class="h-4 w-4" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.586-9.414a2 2 0 112.828 2.828L12 20l-4 1 1-4 10.414-10.414z" />
                            </svg>
                            Edit File
                        </a>
                    @endcan

                    @can('ufsCirculate', $file)
                        <button type="button"
                            onclick="document.getElementById('ufs-selection-box').classList.toggle('hidden')"
                            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-6 py-3 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" 
                                class="h-4 w-4" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor">
                                <path stroke-linecap="round" 
                                    stroke-linejoin="round" 
                                    stroke-width="2" 
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v4.764a1 1 0 01-.447.894L15 14m0 0L5.447 17.276A1 1 0 015 16.618V11a1 1 0 01.553-.894L15 10z" />
                            </svg>
                            Circulate for UFS Approval
                        </button>

                        <div id="ufs-selection-box" class="hidden rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Officer Selected for UFS
                            </label>
                            <div class="rounded-lg bg-white border border-gray-200 px-4 py-3 text-sm text-gray-800">
                                {{ $file->ufsOfficer?->name ?? 'No UFS officer selected' }}
                            </div>
                            <form method="POST" action="{{ route('registry.file-circulations.store') }}" class="space-y-4">
                                @csrf
                                <input type="hidden" name="file_id" value="{{ $fileId }}">
                                <input type="hidden" name="internal_ufs_id" value="{{ $file->internal_ufs_id }}">
                                <button type="submit"
                                    class="w-full rounded-xl mt-4 bg-gray-800 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50">
                                    Submit
                                </button>
                            </form>
                        </div>
                    @endcan
                    @if($isClosed)
                        <div class="mx-auto max-w-full py-6 px-6 rounded-2xl bg-gray-100 border border-gray-300 text-center shadow-sm">
                            <p class="text-3xl font-extrabold text-gray-800 tracking-wide">
                                File Closed
                            </p>
                            <div class="mt-4 space-y-1">
                                <p class="text-sm text-gray-500 mt-3">
                                    Date Closed:
                                </p>
                                <p class="text-lg font-semibold text-gray-700">
                                    {{ \Carbon\Carbon::parse($closedDate)->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    @elseif(auth()->user()->hasRole('registry') && $file->ministry_id == $ministryId && ($file->status == 'Pending Action' || $circulation->status == 'Reviewed'))
                        <div class="flex flex-wrap justify-center gap-4">
                            @if(($file->correspondence_type === 'memo' && $circulation?->status === 'Reviewed') || ($ministrySource && $file->status === 'Pending Action') )
                                @if(!$file->correspondence_type === 'letter')
                                    <button type="button"
                                            id="dispatchBtn"
                                            class="inline-flex items-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-6 py-3 text-sm font-medium text-cyan-700 shadow-sm transition-all duration-200 hover:bg-cyan-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" 
                                                class="h-4 w-4" 
                                                fill="none" 
                                                viewBox="0 0 24 24" 
                                                stroke="currentColor">
                                                <path stroke-linecap="round" 
                                                    stroke-linejoin="round" 
                                                    troke-width="2" 
                                                    d="M3 10l9-7 9 7M4 10v10h16V10" />
                                            </svg>
                                            Dispatch File
                                    </button>
                                @endif
                            @endif
                            @if(!($file->document_source === 'online' && $file->correspondence_type === 'internal') && $circulation?->status !== 'Reviewed' )
                                    <button type="button"
                                        id="circulateBtn"
                                        class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-black hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                            class="h-4 w-4" 
                                            fill="none" 
                                            viewBox="0 0 24 24" 
                                            stroke="currentColor">
                                            <path stroke-linecap="round" 
                                                stroke-linejoin="round" 
                                                stroke-width="2" 
                                                d="M8 7h8m-8 5h8m-8 5h5" />
                                        </svg>
                                        Submit for SRO Review & Approval
                                    </button>
                            @endif
                        </div>
                        <div id="ufs-selection-box" class="hidden rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <label for="ufs_officer_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Officer to Sign Off on this File
                            </label>
                            <form method="POST" action="{{ route('registry.file-circulations.store') }}" class="space-y-4">
                                @csrf
                                <input type="hidden" name="file_id" value="{{ $fileId }}">
                                <select name="ufs_id"
                                    id="ufs_officer_id"
                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring focus:ring-cyan-200">
                                    <option value="">-- Select Officer --</option>
                                    @foreach($usersWithDivision as $officer)
                                        @if (($officer->division_id === auth()->user()->division_id || $officer->hasRole('admin')) && !$officer->hasRole('review-officer'))
                                            <option value="{{ $officer->id }}">
                                                {{ $officer->first_name }} {{ $officer->last_name }} - {{ $officer->division_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="w-full rounded-xl mt-4 bg-gray-800 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    @disabled(!$reviewOfficer)>
                                    Submit for UFS Approval
                                </button>
                            </form>
                        </div>
                        {{-- <div id="ufs-selection-box" class="hidden rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <label for="ufs_officer_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Select UFS Officer
                            </label>
                            <form method="POST" action="{{ route('registry.file-circulations.store') }}" class="space-y-4">
                                @csrf
                                <input type="hidden" name="file_id" value="{{ $fileId }}">
                                <select name="ufs_id"
                                        id="ufs_officer_id"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring focus:ring-cyan-200">
                                        <option value="">-- Select Officer --</option>
                                        @foreach($usersWithDivision as $officer)
                                            @if (($officer->division_id === auth()->user()->division_id || $officer->hasRole('admin')) && !$officer->hasRole('review-officer'))
                                                <option value="{{ $officer->id }}">
                                                    {{ $officer->first_name }} {{ $officer->last_name }} - {{ $officer->division_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                </select>
                                <button type="submit"
                                        class="w-full rounded-xl mt-4 bg-gray-800 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        @disabled(!$reviewOfficer)>
                                        Submit for UFS Approval
                                </button>
                            </form>
                        </div> --}}
                        {{-- Panels --}}
                        <div class="mx-auto w-full max-w-5xl space-y-6">
                            <form action="{{ route('registry.dispatches.store') }}" method="POST">
                                @csrf
                                {{-- Dispatch Selection --}}
                                <div id="dispatchPanel"
                                    class="hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                    <input type="hidden" name="file_id" value="{{ $file->id }}">
                                    <div class="mb-4">
                                        <h3 class="text-sm text-gray-900">
                                            Dispatch to Ministries
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Select one or more ministries to receive this file.
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($ministries as $id => $ministry)
                                            <label class="flex items-start gap-3 rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 hover:bg-gray-100 transition">
                                                <input type="checkbox"
                                                    name="recipient_ministries[]"
                                                    value="{{ $ministry->id }}"
                                                    class="recipient-checkbox mt-1 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                                                    <span class="font-medium">
                                                        {{ $ministry->name }} ({{ $ministry->code }})
                                                    </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @if($ministries->isNotEmpty())
                                        <button type="submit"
                                            id="confirmDispatchBtn"
                                            disabled
                                            onclick="return confirm('Are you sure you want to dispatch this file?');"
                                            class="mt-6 w-full rounded-xl bg-gray-300 px-5 py-3 text-sm font-bold text-gray-500 cursor-not-allowed shadow-sm transition">
                                            Confirm Dispatch
                                        </button>
                                    @else
                                        <button type="button"
                                            disabled
                                            class="mt-6 w-full rounded-xl bg-gray-200 px-5 py-3 text-sm font-bold text-gray-500 cursor-not-allowed">
                                            No Ministries Available
                                        </button>
                                    @endif
                                </div>
                            </form>
                            {{-- Circulation Panel --}}
                            <div id="circulatePanel"
                                class="hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                                <div class="border-b border-gray-100 px-6 py-4">
                                    <h3 class="text-lg font-bold text-gray-800">
                                        Confirm Review Officer
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        This officer will review and acknowledge this file.
                                    </p>
                                </div>
                                <div class="p-6 space-y-5">
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="mt-2 flex items-center justify-between gap-4">
                                            <p class="text-lg font-bold text-gray-800">
                                                {{ $reviewOfficer?->name ?? 'Not assigned yet' }}
                                            </p>
                                            <a href="{{ route('registry.users.edit-review-officer') }}"
                                                class="shrink-0 text-sm font-semibold text-cyan-600 hover:text-cyan-700 hover:underline">
                                                {{ !$reviewOfficer ? 'Select Review Officer' : 'Change Review Officer' }}
                                            </a>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('registry.file-circulations.store') }}" class="space-y-4">
                                        @csrf
                                        @if(!$reviewOfficer)
                                            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                                                ⚠️ No Review Officer assigned. Please assign one before submitting.
                                            </div>
                                        @endif
                                        <input type="hidden" name="file_id" value="{{ $fileId }}">
                                        <input type="hidden" name="review_officer_id" value="{{ optional($reviewOfficer)->id }}">
                                        <button type="submit"
                                            class="w-full rounded-xl bg-gray-800 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
                                            @disabled(!$reviewOfficer)>
                                            Submit for Review
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @elseif(auth()->user()->hasRole('registry') && $file->ministry_id != $ministryId && $circulation && $circulation->status == 'Pending')
                        <div class="flex justify-center">
                            <form action="{{ route('registry.file-circulations.receive', $circulation) }}" method="POST">
                                @csrf
                                @method('patch')
                                <button type="submit"
                                        id="acceptFileBtn"
                                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-emerald-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Mark as Received
                                </button>
                            </form>
                        </div>
                    @elseif(auth()->user()->hasRole('registry') && $file->ministry_id != $ministryId && $circulation && $circulation->status == 'Received')
                        {{-- Circulation Panel --}}
                        <button type="button"
                                id="circulateBtn"
                                class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-black hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    class="h-4 w-4" 
                                    fill="none" 
                                    viewBox="0 0 24 24" 
                                    stroke="currentColor">
                                    <path stroke-linecap="round" 
                                        stroke-linejoin="round" 
                                        stroke-width="2" 
                                        d="M8 7h8m-8 5h8m-8 5h5" />
                                </svg>
                                Submit for SRO Review & Approval
                        </button>
                        <div id="circulatePanel"
                            class="hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                            <div class="border-b border-gray-100 px-6 py-4">
                                <h3 class="text-lg font-bold text-gray-800">
                                    Confirm Review Officer
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    This officer will review and acknowledge this file.
                                </p>
                            </div>
                            <div class="p-6 space-y-5">
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <div class="mt-2 flex items-center justify-between gap-4">
                                        <p class="text-lg font-bold text-gray-800">
                                            {{ $reviewOfficer?->designation ?? 'Not assigned yet' }} -- {{ $reviewOfficer?->name ?? '' }}
                                        </p>
                                        <a href="{{ route('registry.users.edit-review-officer') }}"
                                            class="shrink-0 text-sm font-semibold text-cyan-600 hover:text-cyan-700 hover:underline">
                                            {{ !$reviewOfficer ? 'Select Review Officer' : 'Change Review Officer' }}
                                        </a>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('registry.file-circulations.store') }}" class="space-y-4">
                                    @csrf
                                    @if(!$reviewOfficer)
                                        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                                            ⚠️ No Review Officer assigned. Please assign one before submitting.
                                        </div>
                                    @endif
                                    <input type="hidden" name="file_id" value="{{ $fileId }}">
                                    <input type="hidden" name="review_officer_id" value="{{ optional($reviewOfficer)->id }}">
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-black hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        @disabled(!$reviewOfficer)
                                        Submit for Review
                                    </button>
                                </form>
                            </div>
                        </div>
                    @elseif (auth()->user()->hasRole('registry') && $file->ministry_id == $ministryId && $file->status == 'Dispatched')
                        <form action="{{ route('registry.files.close', $file) }}" method="POST">
                            @csrf
                            <button type="submit"
                                id="closeFileBtn"
                                class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-400">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Close File
                            </button>
                        </form>
                    @elseif (auth()->user()->hasRole('registry') && ($file->ministry_id == $ministryId || $circulation?->to_ministry_id === auth()->user()->ministry_id ) && (!$fileAssignment || $fileAssignment?->status === 'accepted'))
                        <form action="{{ route('registry.files.close', $file) }}" method="POST">
                            @csrf
                            <button type="submit"
                                id="closeFileBtn"
                                class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-400">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Close File
                            </button>
                        </form>
                    @elseif(auth()->user()->hasRole('registry') && $file->ministry_id != $ministryId &&$fileAssignment && $fileAssignment->status == 'pending')
                        <form method="POST" action="{{ route('registry.file.reassign', $circulation->id) }}">
                            @csrf
                            @if($fileAssignment->reassigned_from !== null)
                                <div class="mb-4 p-4 rounded-lg border border-amber-200 bg-amber-50">
                                    <div class="flex items-start justify-between">
                                        <div class="text-sm text-amber-700">
                                            <span class="font-semibold">Reassigned File</span><br>
                                            This file was reassigned to you from 
                                            <span class="font-medium text-amber-800">
                                                {{ $fileAssignment->reassignedFrom->name }} 
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-4 space-y-3">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="action" value="accepted" class="action-radio">
                                            <span class="text-gray-700 font-medium">
                                                Accept and mark as received
                                            </span>
                                        </label>
                                    </div>
                                </div>
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
                            <div id="officer-select" class="mb-4 hidden">
                                <label class="block font-semibold text-gray-700 mb-1">Select Officer:</label>
                                <select name="new_officer_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-cyan-200">
                                    <option value="">-- Choose officer in your division --</option>
                                    @foreach($usersWithDivision as $officer) 
                                        @if ($officer->division_id === auth()->user()->division_id && 
                                            !$circulation->activeAssignments->contains('officer_id', $officer->id) && 
                                            !$officer->hasRole('review-officer')) 
                                            <option value="{{ $officer->id }}">{{ $officer->first_name }} {{ $officer->last_name }} - {{ $officer->division_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                class="bg-cyan-600 w-full hover:bg-cyan-800 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition duration-200">
                                Submit
                            </button>
                        </form>
                    @elseif(auth()->user()->hasAnyRole('review-officer') && $circulation && $circulation->to_ministry_id == $ministryId && $circulation->status == 'Pending Review')  
                        <div class="flex flex-wrap justify-center gap-3">
                            <div class="flex gap-3">
                                <button type="submit"
                                    id="showReviewSection"
                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-blue-700 hover:shadow focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                    Review and Assign officers
                                </button>
                                <button type="submit"
                                    id="showApproveSection"
                                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-emerald-700 hover:shadow focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve and Assign officers
                                </button>
                                <button type="submit"
                                    id="showRejectSection"
                                    class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-rose-700 hover:shadow focus:outline-none focus:ring-2 focus:ring-rose-400">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Reject
                                </button>
                            </div>
                            {{-- REVIEW SECTION --}}
                            <div id="reviewSection" class="hidden w-full mt-4">
                                <form method="POST" action="{{ route('registry.file.assign', $circulation) }}" class="space-y-5 rounded-xl border border-gray-200 bg-gray-50 p-5 shadow-sm">
                                    @csrf
                                    <input type="hidden" name="file_id" value="{{ $file->id }}">
                                    <input type="hidden" name="status" value="Reviewed">
                                    {{-- Review Comment --}}
                                    <div> 
                                        <p class="font-semibold text-gray-800">Note: This action will set the status of file to 
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">REVIEWED</span>
                                        </p> 
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                                            Comment
                                        </label>
                                        <textarea name="comment"
                                            rows="3"
                                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring focus:ring-cyan-200"
                                            placeholder="Write your review comment...">{{ old('comment') }}</textarea>
                                    </div>
                                    @php
                                        $requiresSignature = ($file->correspondence_type === 'memo' || $file->correspondence_type === 'letter') &&
                                                            $file->document_source === 'online' &&
                                                            $file->status === 'Pending Review';
                                                            $hasSignature = !empty(auth()->user()->signature_path);
                                    @endphp
                                    @if($requiresSignature)
                                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                            <div class="flex-1">
                                                <label class="block text-sm font-semibold text-gray-800">
                                                    Electronic Signature
                                                </label>
                                                <p class="mt-1 text-xs text-gray-600">
                                                    Your stored system signature will automatically be added to the approved memo.
                                                </p>
                                                @if($hasSignature)
                                                    <div class="mt-3 rounded-md border bg-white p-3">
                                                        <p class="mb-2 text-xs font-medium text-gray-500">
                                                            Signature Preview
                                                        </p>
                                                        <img src="{{ asset('storage/' . auth()->user()->signature_path) }}"
                                                            alt="User Signature"
                                                            class="max-h-20 object-contain">
                                                    </div>
                                                @else
                                                    <div class="mt-3 rounded-md border border-red-300 bg-red-50 p-3 text-xs text-red-700">
                                                        A signature is required before approving this memo.
                                                        Please upload your signature in your profile settings.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    <div class="flex justify-right">
                                        <button type="submit"
                                           {{ ($requiresSignature && !$hasSignature) ? 'disabled' : '' }}
                                            class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium text-white shadow-sm transition
                                            {{ ($requiresSignature && !$hasSignature)
                                            ? 'bg-gray-400 cursor-not-allowed'
                                            : 'bg-blue-600 hover:bg-blue-700' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Submit Review
                                        </button>
                                    </div>
                                </form>
                            </div>
                            {{-- APPROVE SECTION --}}
                            <div id="approveSection" class="hidden w-full mt-2">
                                <form method="POST" action="{{ route('registry.file.assign', $circulation) }}" class="space-y-5 rounded-xl border border-gray-200 bg-gray-50 p-5 shadow-sm">
                                    @csrf
                                    <input type="hidden" name="file_id" value="{{ $file->id }}">
                                    <input type="hidden" name="status" value="Approved">
                                    {{-- Approval Comment --}}
                                    <div> 
                                        <p class="font-semibold text-gray-800">Note: This action will set the status of file to 
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">APPROVED</span>
                                        </p> 
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                                            Comment 
                                        </label>
                                        <textarea name="comment"
                                            rows="3"
                                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring focus:ring-cyan-200"
                                            placeholder="Write your approval comment...">{{ old('comment') }}</textarea>
                                    </div>
                                    @php
                                        $requiresSignature =
                                        ($file->correspondence_type === 'memo' || $file->correspondence_type === 'letter') &&
                                        $file->document_source === 'online' &&
                                        $file->status === 'Pending Review';
                                        $hasSignature = !empty(auth()->user()->signature_path);
                                    @endphp
                                    @if($requiresSignature)
                                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                            <div class="flex-1">
                                                <label class="block text-sm font-semibold text-gray-800">
                                                    Electronic Signature
                                                </label>
                                                <p class="mt-1 text-xs text-gray-600">
                                                    Your stored system signature will automatically be added to the approved memo.
                                                </p>
                                                @if($hasSignature)
                                                    <div class="mt-3 rounded-md border bg-white p-3">
                                                        <p class="mb-2 text-xs font-medium text-gray-500">
                                                            Signature Preview
                                                        </p>
                                                        <img src="{{ asset('storage/' . auth()->user()->signature_path) }}"
                                                            alt="User Signature"
                                                            class="max-h-20 object-contain">
                                                    </div>
                                                @else
                                                    <div class="mt-3 rounded-md border border-red-300 bg-red-50 p-3 text-xs text-red-700">
                                                        A signature is required before approving this memo.
                                                        Please upload your signature in your profile settings.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    <div class="flex justify-right">
                                        <button type="submit"
                                                {{ ($requiresSignature && !$hasSignature) ? 'disabled' : '' }}
                                                class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium text-white shadow-sm transition
                                                        {{ ($requiresSignature && !$hasSignature)
                                                        ? 'bg-gray-400 cursor-not-allowed'
                                                        : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M5 13l4 4L19 7" /> 
                                                </svg>
                                            Submit Approval
                                        </button>
                                    </div>
                                </form>
                            </div>
                            {{-- REJECT SECTION --}}
                            <div id="rejectSection" class="hidden w-full mt-4">
                                <form method="POST" action="{{ route('registry.files.show', $circulation) }}" class="space-y-4 border rounded-xl p-4 bg-rose-50">
                                    @csrf
                                    <input type="hidden" name="status" value="Rejected">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Rejection Reason
                                        </label>
                                        <textarea name="comment"
                                                rows="3"
                                                required
                                                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-rose-200"
                                                placeholder="Write reason for rejection..."></textarea>
                                    </div>
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">
                                            Submit Rejection
                                    </button>
                                </form>
                            </div>
                        </div>
                    @elseif ($fileAssignment && $fileAssignment->status == 'pending')
                        <form method="POST" action="{{ route('registry.file.reassign', $circulation->id) }}">
                            @csrf
                            @if($fileAssignment->reassigned_from !== null)
                                <div class="mb-4 p-4 rounded-lg border border-amber-200 bg-amber-50">
                                    {{-- Message --}}
                                    <div class="flex items-start justify-between">
                                        <div class="text-sm text-amber-700">
                                            <span class="font-semibold">
                                                Reassigned File
                                            </span><br>
                                            This file was reassigned to you from 
                                            <span class="font-medium text-amber-800">
                                                {{ $fileAssignment->reassignedFrom->name }} 
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
                                            !$officer->hasRole('review-officer')) 
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
                    @elseif($circulation?->status === 'Pending UFS Approval' && $circulation?->ufs_id === auth()->user()->id)
                        <div class="w-full mt-4">
                            <form id="ufsDecisionForm" method="POST" action="{{ route('registry.ufs.approve', $circulation) }}" class="space-y-4 border rounded-xl p-4 bg-gray-50">
                                @csrf
                                <div> <h3 class="text-lg font-semibold text-gray-800">UFS Approval</h3> </div>
                                    <input type="hidden" name="file_id" value="{{ $file->id }}">
                                    {{-- Comment --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Comment
                                        </label>
                                        <textarea name="ufs_comment"
                                            rows="3"
                                            required
                                            class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-cyan-200"
                                            placeholder="Add comment for your decision..."></textarea>
                                    </div>
                                    {{-- Action Buttons --}}
                                    <div class="flex items-center gap-3">
                                        <button onclick="setApprovalRoute('approve', {{ $circulation->id }})">
                                            Approve
                                        </button>
                                        <button onclick="setApprovalRoute('reject', {{ $circulation->id }})">
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @elseif(auth()->user()->hasRole('registry'))
                        <div class="flex justify-center">
                            <form action="{{ route('registry.files.close', $file) }}" method="POST">
                                @csrf
                                <button type="button"
                                    id="closeFileBtn"
                                    class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Close File
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const actionRadios = document.querySelectorAll('.action-radio');
            const officerSelect = document.getElementById('officer-select');
            const rejectBox = document.getElementById('reject-comment-box');

            actionRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.value === 'reassign') {
                        officerSelect.classList.remove('hidden');
                    } else {
                        officerSelect.classList.add('hidden');
                    }

                    if (this.value === 'rejected') {
                        rejectBox?.classList.remove('hidden');
                    } else {
                        rejectBox?.classList.add('hidden');
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.recipient-checkbox');
            const dispatchBtn = document.getElementById('confirmDispatchBtn');
            function toggleDispatchButton() {
                const anyChecked = [...checkboxes].some(cb => cb.checked);

                dispatchBtn.disabled = !anyChecked;

                if (anyChecked) {

                    dispatchBtn.classList.remove(
                        'bg-gray-300',
                        'text-gray-500',
                        'cursor-not-allowed'
                    );

                    dispatchBtn.classList.add(
                        'bg-gray-800',
                        'text-white',
                        'hover:bg-gray-700'
                    );

                } else {

                    dispatchBtn.classList.add(
                        'bg-gray-300',
                        'text-gray-500',
                        'cursor-not-allowed'
                    );

                    dispatchBtn.classList.remove(
                        'bg-gray-800',
                        'text-white',
                        'hover:bg-gray-700'
                    );
                }
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', toggleDispatchButton);
            });

            toggleDispatchButton();
        });
    </script>
    <script>
        const approveBtn = document.getElementById('showApproveSection');
        const reviewBtn = document.getElementById('showReviewSection');
        const rejectBtn = document.getElementById('showRejectSection');

        const approveSection = document.getElementById('approveSection');
        const reviewSection = document.getElementById('reviewSection');
        const rejectSection = document.getElementById('rejectSection');


        approveBtn.addEventListener('click', () => {
            approveSection.classList.remove('hidden');
            rejectSection.classList.add('hidden');
            reviewSection.classList.add('hidden');
        });

        reviewBtn.addEventListener('click', () => {
            reviewSection.classList.remove('hidden');
            approveSection.classList.add('hidden');
            rejectSection.classList.add('hidden');
        });

        rejectBtn.addEventListener('click', () => {
            rejectSection.classList.remove('hidden');
            approveSection.classList.add('hidden');
            reviewSection.classList.add('hidden');
        });

    </script>
    <script>
        function setApprovalRoute(actionType, circulationId) {

            const form = document.getElementById('ufsDecisionForm');

            if (actionType === 'approve') {
                form.action = "{{ route('registry.ufs.approve', ':id') }}"
                    .replace(':id', circulationId);
            } else {
                form.action = "{{ route('registry.ufs.reject', ':id') }}"
                    .replace(':id', circulationId);
            }
        }
    </script>
@endsection