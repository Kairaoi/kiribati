@extends('layouts.app')

@section('content')
        @php
            $statusClass = match($file->status) {
                'Pending Action' => 'bg-red-100 text-red-700',
                'Received' => 'bg-blue-100 text-blue-700',
                'Dispatched' => 'bg-cyan-100 text-cyan-700',
                'Pending Review' => 'bg-yellow-100 text-yellow-700',
                'Pending SRO Approval' => 'bg-yellow-100 text-yellow-700',
                'Approved' => 'bg-emerald-100 text-emerald-700',
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
                'Pending SRO Approval' => 'bg-yellow-100 text-yellow-700',
                'Approved' => 'bg-emerald-100 text-emerald-700',
                'Reviewed' => 'bg-green-100 text-green-700',
                default => 'bg-gray-100 text-gray-600',
            };
        @endphp
        <div class="container mx-w-5xl mx-auto">
            {{-- File Info --}}
            <div class="mx-auto  max-w-5xl bg-white justify-center border border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                <p class="font-bold text-cyan-600 uppercase mb-2">File Details</p>
                <div class="mb-4">
                    <p class="text-sm text-gray-500">File Subject</p>
                    <p class="text-md font-medium text-gray-800 mt-1">
                        {{ $file->subject?? '-' }}
                    </p>
                </div>

                <!-- Grid Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 ">File Status</p>
                        @if($file->ministry_id == auth()->user()->ministry_id && !$circulation)
                            <span class="inline-block mt-1 text-xs font-medium px-2 py-1 rounded-full {{ $statusClass }}">
                                {{ $file->status ?? '-' }}
                            </span>
                        @elseif($circulation?->to_ministry_id == auth()->user()->ministry_id && $file->ministry_id != auth()->user()->ministry_id) 
                            <span class="inline-block mt-1 text-xs font-medium px-2 py-1 rounded-full {{ $statusClass2 }}">
                                {{ $circulation->status ?? '-' }}
                            </span>
                        @elseif($file->ministry_id == auth()->user()->ministry_id && $circulation && $circulation?->to_ministry_id == auth()->user()->ministry_id) 
                            <span class="inline-block mt-1 text-xs font-medium px-2 py-1 rounded-full {{ $statusClass2 }}">
                                {{ $circulation->status ?? '-' }}
                            </span>
                        @endif
                    </div>  
                    <div>
                        <p class="text-sm text-gray-500">Source / Origin</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->source->name ?? '-' }}
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
                    @isset($file->correspondence_type)
                        <div>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
                            <p class="text-sm text-gray-500">Correspondence Type</p>
                            <p class="text-md font-medium text-gray-800 mt-1">
                                {{ $file->correspondence_type ?? '-' }}
                            </p>
                        </div> 
                    @endisset                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     

                    @isset($file->division)
                        <div>
                            <p class="text-sm text-gray-500">Division</p>
                            <p class="text-md font-medium text-gray-800 mt-1">
                                {{ $file->division->name ?? '-' }}
                            </p>
                        </div>
                    @endisset
                    <div>
                        <p class="text-sm text-gray-500">Created At</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->created_at?->format('d/m/Y') ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Created By</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->createdBy->name ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">File Type</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->fileType->name ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Due Date</p>
                        <p class="text-md font-medium text-gray-800 mt-1">
                            {{ $file->due_date ? $file->due_date->format('d/m/Y') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- UFS details --}}
            @if($file->ufsOfficer )  
                <div class="mx-auto max-w-5xl justify-center border bg-white border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                    <h3 class="font-bold text-gray-500 mb-2">UFS Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-500">UFS Officer</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{ $circulation?->ufsApprovedBy->name ?? $file->ufsOfficer->name ?? '-'}}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">UFS Status</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{ $circulation?->ufs_status ?? '-'}}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">UFS Date</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{ optional($circulation)->ufs_approved_at ? \Carbon\Carbon::parse($circulation->date_reviewed)->format('d M Y') : '-' }}
                            </p>
                        </div>
                    </div>
                  
                </div>
            @endif  

             {{-- Circulation Review details --}}
            @if($circulation && $file->created_by === $userId && $circulation->to_ministry_id === $ministryId && ($circulation->reviewed_by || $circulation->review_officer)) 
                <div class="mx-auto max-w-5xl justify-center border bg-white border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                    <h3 class="font-bold text-cyan-500 mb-2">Review Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Review Officer</p>
                            @if($circulation->reviewed_by)
                                <p class="text-sm font-medium text-gray-800 mt-1">
                                    {{ $circulation->reviewedBy->name ?? '-'}} 
                                </p>
                            @else
                                <p class="text-sm font-medium text-gray-800 mt-1">
                                    {{ $circulation->reviewOfficer->name ?? '-'}}
                                </p>
                            @endif

                            @if($circulation?->status == 'Pending Review' && auth()->user()->hasRole('registry'))
                                <a href="{{ route('registry.users.edit-review-officer') }}"
                                    class="inline-flex items-center text-sm font-medium text-cyan-600 hover:text-cyan-800 hover:underline transition">
                                    Change Review Officer
                                </a>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Review Status</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{ optional($circulation)->status ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Review Date</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{ optional($circulation)->date_reviewed ? \Carbon\Carbon::parse($circulation->date_reviewed)->format('d M Y') : '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Review Comment</p>
                        <p class="text-sm font-medium text-gray-800 mt-1 break-all"">
                            {{  optional($circulation)->review_comment ?? '-'  }}
                        </p>
                    </div>  
                </div>
            @endif  

            @if($circulation && ($circulation->status === 'Reviewed' || $circulation->status === 'Approved' || $circulation->status === 'Rejected'))
                <div class="mx-auto max-w-5xl justify-center border bg-white border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                    
                    <p class="font-bold text-cyan-600 uppercase mb-2">Final Review Details</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{ optional($circulation)->status ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Reviewed By</p>
                                <p class="text-sm font-medium text-gray-800 mt-1">
                                    {{ $circulation->approvedBy->name ?? '-'}} 
                                </p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Review Date</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{ optional($circulation)->approved_at ? \Carbon\Carbon::parse($circulation->approved_at)->format('d M Y, h:i A') : '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Comment</p>
                        <p class="text-sm font-medium text-gray-800 mt-1 break-all"">
                            {{  optional($circulation)->approval_comment ?? '-'  }}
                        </p>
                    </div>  
                    
                    @can('viewAssignments', $circulation)
                        {{-- <div x-data="{ showAllAssignments: false }"> --}}
                            <p class="text-sm text-gray-500">Assigned Officers</p>

                            @if($circulation && $circulation->activeAssignments->isNotEmpty())
                                @php
                                    $assignments = $circulation->activeAssignments;
                                @endphp

                                <div class="list-disc list-inside text-gray-900 text-sm font-semibold space-y-2">
                                    @foreach($assignments as $index => $assignment)
                                         @if(!$assignment->reassigned_from)
                                           <div class="mt-1 flex justify-between items-start rounded-lg border border-gray-200 bg-white p-3 hover:shadow-sm transition">
                                                {{-- Left --}}
                                                <div>
                                                    <div class="text-gray-900 font-medium">
                                                        {{ $assignment->officer->first_name }}
                                                        {{ $assignment->officer->last_name }}
                                                    </div>

                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $assignment->officer->division->name ?? 'No Division' }}
                                                    </div>
                                                </div>

                                                {{-- Right --}}
                                                <div class="flex flex-col items-end gap-2 text-right">

                                                    @if($assignment->status === 'accepted')
                                                        <span class="text-xs bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded-full">
                                                            Received
                                                        </span>
                                                    @elseif($assignment->status === 'pending')
                                                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                                                            Pending
                                                        </span>
                                                    @endif

                                                    <div class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded-md">
                                                        Assigned By:
                                                        <span class="font-medium text-gray-700">
                                                            {{ $assignment->assignedBy->name }}
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-800 font-semibold">-</p>
                            @endif
                    @endcan
                </div>
            @endif  

            @if($fileAssignment)
                <div class="mx-auto max-w-5xl justify-center border bg-white border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                    <h3 class="font-bold text-cyan-600 uppercase mb-2">Re-assignment Details</h3>
                  
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Comment</p>
                        <p class="text-sm font-medium text-gray-800 mt-1 break-all"">
                            {{  optional($circulation)->approval_comment ?? '-'  }}
                        </p>
                    </div>  
                    @if($circulation && $circulation->activeAssignments->isNotEmpty())
                            @php
                                $assignments = $circulation->activeAssignments;
                            @endphp

                            <div class="list-disc list-inside text-gray-900 text-sm font-semibold space-y-2">
                                @foreach($assignments as $index => $assignment)
                                    @if($assignment->reassigned_from)
                                        <div class="flex mt-1 p-3 rounded-lg border border-gray-200 bg-white hover:shadow-sm transition">
                                            <div class="flex gap-4">
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
                                                <div class="items-right mx-auto">
                                                    <div class="text-xs justify-right mx-auto text-gray-600 bg-gray-50 px-2 py-1 rounded-md inline-block">
                                                        Assigned By:
                                                        <span class="font-medium text-gray-700">
                                                            {{ $assignment->assignedBy->name }}
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                    @else
                        <p class="text-sm text-gray-800 font-semibold">-</p>
                    @endif
                    
                </div>
            @endif

            {{-- Dispatch Details  --}}
            @if($file->ministry_id == $ministryId && $fileCirculations->isNotEmpty())
                <div class="mx-auto max-w-5xl justify-center border border-gray-200 rounded-lg bg-white shadow-sm p-4 mt-4">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            Dispatch Details
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @forelse($fileCirculations as $circulation)
                            <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-white p-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ optional($circulation?->toMinistry)->name ?? 'Unknown Ministry' }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Dispatched:
                                        {{ optional($circulation?->dispatch)->dispatch_date
                                            ? \Carbon\Carbon::parse($circulation->dispatch->dispatch_date)->format('d M Y g:i A')
                                            : '-' 
                                        }}
                                    </p>
                                    {{-- @if($circulation?->activeAssignments?->isNotEmpty())
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
                                    @endif --}}
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
            

            @php
                $fileUrl = route('registry.files.preview', $file);
                $extension = strtolower(pathinfo($file->main_file_path, PATHINFO_EXTENSION));
                $downloadUrl = route('registry.files.download', $file);
            @endphp
            <div class="mt-4 mb-6 flex justify-center">
                @if($extension === 'pdf')
                    <embed
                        src="{{ $fileUrl }}"
                        type="application/pdf"
                        class="w-full max-w-5xl rounded-2xl border border-gray-200 shadow-sm bg-white"
                        style="height: 900px;"
                    >
                @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'tif', 'tiff']))
                    <img
                        src="{{ $fileUrl }}"
                        alt="Uploaded file"
                        class="w-full max-w-4xl rounded-2xl border border-gray-200 shadow-sm bg-white object-contain"
                    >
                @else
                    <p class="text-sm text-gray-500">
                        Preview not available for this file type.
                    </p>
                @endif
            </div>
           
                        
            {{-- Actions --}}
            <div class="mb-6 max-w-4xl mx-auto flex justify-center">
                <div class="w-full space-y-6">
                    <a href="{{ $downloadUrl }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:border-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 16V4m0 12l-4-4m4 4l4-4M4 20h16" />
                        </svg>
                        Download File
                    </a>
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
                                    {{ \Carbon\Carbon::parse($closedDate)->format('d M Y \a\t g:i A') }}                                </p>
                            </div>
                        </div>
                    @else 
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

                        @can('dispatch', $file)
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
                                
                            </div>
                        @endcan

                        @can('circulateToReviewOfficer', $file)
                            <button type="button"
                                    id="circulateBtn"
                                    class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                    Submit to SRO
                            </button>
                            <div id="circulatePanel" class="hidden">
                                <form method="POST" action="{{ route('registry.file-circulations.store') }}">
                                        @csrf
                                        <input type="hidden" name="file_id" value="{{ $fileId }}">
                                        {{-- <input type="hidden" name="review_officer_id" value="{{ optional($reviewOfficer)->id }}"> --}}

                                        @if(!$reviewOfficer)
                                            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-6 py-4">
                                                <a href="{{ route('registry.users.edit-review-officer') }}"
                                                    class="text-sm font-medium text-red-700 hover:text-red-800 hover:underline">
                                                    ⚠️ No Review Officer assigned. Please assign one before submitting.
                                                </a>
                                            </div>
                                        @else
                                            <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
                                                <div class="border-b border-gray-100 px-6 py-4">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                                                            Review Officer
                                                        </h3>

                                                        <a href="{{ route('registry.users.edit-review-officer') }}"
                                                            class="inline-flex items-center rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-xs font-medium text-cyan-700 transition hover:bg-cyan-100">
                                                            Change
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="px-6 py-3">
                                                    <p class="text-lg font-semibold text-gray-900">
                                                        {{ $reviewOfficer->name }}
                                                    </p>

                                                    @if($reviewOfficer->designation)
                                                        <p class="mt-1 text-sm text-gray-600">
                                                            {{ $reviewOfficer->designation }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex justify-end">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                                                    <i class="fas fa-paper-plane"></i>
                                                    Submit
                                                </button>
                                            </div>
                                        @endif
                                </form>
                            </div>
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
                                <form method="POST" action="{{ route('registry.files.ufsCirculate', $file) }}" class="space-y-4">
                                    @csrf
                                    {{-- <input type="hidden" name="file_id" value="{{ $fileId }}"> --}}
                                    <input type="hidden" name="internal_ufs_id" value="{{ $file->internal_ufs_id }}">
                                    <button type="submit"
                                        class="w-full rounded-xl mt-4 bg-gray-800 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50">
                                        Submit
                                    </button>
                                </form>
                            </div>
                        @endcan

                        @can('circulateForReview', $file)
                            <form method="POST" action="{{ route('registry.file-circulations.colleague.store') }}">
                                @csrf
                                <input type="hidden" name="file_id" value="{{ $file->id }}">
                                <div class="mt-4">
                                    <label for="reviewer_id" class="block text-sm font-medium text-gray-700">
                                        Select reviewer or approver (note that only the HOD and SRO can only approve/sign documents)
                                    </label>
                                    <select
                                        name="colleague"
                                        id="colleague"
                                        required
                                        class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                    >
                                        <option value="">Select officer</option>

                                        @foreach ($divisionUsers as $reviewer)
                                            @if($reviewer->id !== auth()->user()->id)
                                                <option value="{{ $reviewer->id }}">
                                                    {{ $reviewer->first_name }} {{ $reviewer->last_name }}
                                                    @if($reviewer->designation)
                                                        - {{ $reviewer->designation }}
                                                    @endif
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <button
                                    type="submit"
                                    class="mt-4 inline-flex items-center rounded-lg bg-cyan-500 px-5 py-2.5 text-sm font-medium hover:bg-cyan-600"
                                >
                                    Submit for Review
                                </button>
                            </form>
                        @endcan

                        @if($circulation)
                            @can('ufs', $circulation)
                                <div class="w-full mt-4">
                                    <form id="ufsDecisionForm" method="POST" action="{{ route('registry.ufs.approve', $circulation) }}" class="space-y-4 border rounded-xl p-4 bg-gray-50">
                                        @csrf
                                        <div class="bg-gray-50"> 
                                            <h3 class="text-lg font-semibold text-gray-800">UFS Review</h3> </div>
                                            <input type="hidden" name="file_id" value="{{ $file->id }}">
                                            {{-- Comment --}}
                                            {{-- <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Comment (Optional)
                                                </label>
                                                <textarea name="ufs_comment"
                                                    rows="2"
                                                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-gray-200"
                                                    placeholder="Add comment for your decision..."></textarea>
                                            </div> --}}
                                            {{-- Action Buttons --}}
                                            <div class="flex items-center gap-3">
                                                <button onclick="setApprovalRoute('approve', {{ $circulation->id }})" class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium bg-emerald-400 shadow-sm transition">
                                                    Approve
                                                </button>
                                                <button onclick="setApprovalRoute('reject', {{ $circulation->id }})" class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium bg-red-400 shadow-sm transition">
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endcan

                            {{-- @can('close', $circulation)
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
                            @endcan --}}

                            {{-- @can('circulateForUFSApproval', $circulation)
                                <button type="button"
                                        id="circulateBtn"
                                        class="inline-flex items-center gap-2 rounded-xl bg-gray-300 px-6 py-3 text-sm font-medium shadow-sm transition-all duration-200 hover:bg-gray-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500">
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
                                    Circulate to SRO (review officer)
                                </button>
                                <div id="circulatePanel" class="hidden">
                                    <form method="POST" action="{{ route('registry.file-circulations.store') }}">
                                        @csrf

                                        <input type="hidden" name="file_id" value="{{ $fileId }}">

                                        @if(!$reviewOfficer)
                                            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-6 py-4">
                                                <a href="{{ route('registry.users.edit-review-officer') }}"
                                                    class="text-sm font-medium text-red-700 hover:text-red-800 hover:underline">
                                                    ⚠️ No Review Officer assigned. Please assign one before submitting.
                                                </a>
                                            </div>
                                        @else
                                            <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
                                                <div class="border-b border-gray-100 px-6 py-4">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                                                            Review Officer
                                                        </h3>

                                                        <a href="{{ route('registry.users.edit-review-officer') }}"
                                                            class="inline-flex items-center rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-xs font-medium text-cyan-700 transition hover:bg-cyan-100">
                                                            Change
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="px-6 py-3">
                                                    <p class="text-lg font-semibold text-gray-900">
                                                        {{ $reviewOfficer->name }}
                                                    </p>

                                                    @if($reviewOfficer->designation)
                                                        <p class="mt-1 text-sm text-gray-600">
                                                            {{ $reviewOfficer->designation }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex justify-end">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                                                    <i class="fas fa-paper-plane"></i>
                                                    Submit for Review
                                                </button>
                                            </div>
                                        @endif
                                    </form>
                                </div>
                            @endcan --}}

                            {{-- sro, hod review buttons --}}
                            @can('review', $circulation)
                                {{-- <div class="flex flex-wrap justify-center gap-3"> --}}
                                    <div class="flex mt-6 mb-2 gap-2">
                                        @if(auth()->user()->hasRole('hod'))
                                            <form method="POST" action="{{ route('registry.file-circulations.store') }}">
                                                @csrf
                                                
                                                <input type="hidden" name="file_id" value="{{ $fileId }}">
                                                <input type="hidden" name="review_officer_id" value="{{ optional($reviewOfficer)->id }}">

                                                <button type="submit"
                                                    id="circulateBtn"
                                                    class="inline-flex items-center gap-2 bg-cyan-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                                    Submit to SRO ({{ $reviewOfficer->name }})
                                                </button>
                                            </form>
                                        @endif
                                        <div class="flex flex-wrap items-center gap-2">
                                            <button type="button"
                                                id="showReviewSection"
                                                class="inline-flex items-center justify-center rounded-lg border border-cyan-600 bg-cyan-600 px-6 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                                Review
                                            </button>

                                            <button type="button"
                                                id="showApproveSection"
                                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-base font-semibold text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5 text-emerald-600"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Approve
                                            </button>

                                            <button type="button"
                                                id="showRejectSection"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-3 text-base font-semibold text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5 text-red-600"
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
                                        
                                    </div>
                                    {{-- REVIEW SECTION --}}
                                    <div id="reviewSection" class="hidden w-full mt-4">
                                        <form method="POST" action="{{ route('registry.file.review', $circulation) }}" class="space-y-5 rounded-xl border border-gray-200 bg-gray-50 p-5 shadow-sm">
                                            @csrf
                                            <input type="hidden" name="file_id" value="{{ $file->id }}">
                                            <input type="hidden" name="status" value="Reviewed">
                                            {{-- Review Comment --}}
                                            <div> 
                                                <p class="font-semibold text-gray-800">This action will set the status of file to 
                                                    <span class="bg-cyan-100 text-cyan-800 px-2 py-1 rounded">REVIEWED</span>
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
                                                    : 'bg-cyan-600 hover:bg-cyan-700' }}">
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
                                        <form method="POST" action="{{ route('registry.file.review', $circulation) }}" class="space-y-5 rounded-xl border border-gray-200 bg-gray-50 p-5 shadow-sm">
                                            @csrf
                                            <input type="hidden" name="file_id" value="{{ $file->id }}">
                                            <input type="hidden" name="status" value="Approved">
                                            {{-- Approval Comment --}}
                                            <div> 
                                                <p class="font-semibold text-gray-800">This action will set the status of file to 
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
                                        <form method="POST" action="{{ route('registry.file.review', $circulation) }}" class="space-y-4 border rounded-xl p-4 bg-rose-50">
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
                                
                            @endcan

                            @can('markReceive', $circulation)
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

                            @endcan

                            @can('circulateToReviewOfficer', $circulation)
                                <button type="button"
                                    id="circulateBtn"
                                    class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                    Submit to SRO
                                </button>
                                <div id="circulatePanel" class="hidden">
                                    <form method="POST" action="{{ route('registry.file-circulations.update', $circulation) }}">
                                        @csrf
                                        @method('put')

                                        <input type="hidden" name="file_id" value="{{ $fileId }}">

                                        @if(!$reviewOfficer)
                                            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-6 py-4">
                                                <a href="{{ route('registry.users.edit-review-officer') }}"
                                                    class="text-sm font-medium text-red-700 hover:text-red-800 hover:underline">
                                                    ⚠️ No Review Officer assigned. Please assign one before submitting.
                                                </a>
                                            </div>
                                        @else
                                            <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
                                                <div class="border-b border-gray-100 px-6 py-4">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                                                            Review Officer
                                                        </h3>

                                                        <a href="{{ route('registry.users.edit-review-officer') }}"
                                                            class="inline-flex items-center rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-xs font-medium text-cyan-700 transition hover:bg-cyan-100">
                                                            Change
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="px-6 py-3">
                                                    <p class="text-lg font-semibold text-gray-900">
                                                        {{ $reviewOfficer->name }}
                                                    </p>

                                                    @if($reviewOfficer->designation)
                                                        <p class="mt-1 text-sm text-gray-600">
                                                            {{ $reviewOfficer->designation }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex justify-end">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                                                    <i class="fas fa-paper-plane"></i>
                                                    Submit for Review
                                                </button>
                                            </div>
                                        @endif
                                    </form>
                                </div>
                            @endcan

                            @can('assign', $circulation)
                                @if($notAssignedOfficers->isNotEmpty())
                                    <form method="POST" action="{{ route('registry.file.assign', $circulation) }}"  class="space-y-5 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                            @csrf
                                            <input type="hidden" name="file_id" value="{{ $file->id }}">
                                            <div class="mb-4">
                                                <label for="assignedOfficers" class="block text-md font-medium text-gray-700 mb-2">
                                                    Assign Officers (from Review Comment)
                                                </label>
                                                <button
                                                    type="button"
                                                    id="selectAllBtn"
                                                    class="mb-3 rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-2 text-sm text-cyan-700 hover:bg-cyan-100">
                                                    Select All Officers
                                                </button>
                                                <select
                                                    id="assignedOfficers"
                                                    name="officers[]"
                                                    multiple
                                                    class="w-full">
                                                    @foreach($notAssignedOfficers as $user)
                                                        @if(!($user->id === $circulation->review_officer) || $user->id != auth()->user()->id)
                                                            <option value="{{ $user->id }}" class="bg-white border rounded-xl">
                                                                {{ $user->name }}
                                                                @if($user->division)
                                                                    • {{ $user->division->name }}
                                                                @endif
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('officers')
                                                    <p class="mt-2 text-sm text-red-600">
                                                        {{ $message }}
                                                    </p>
                                                @enderror
                                            </div>
                                            <div class="flex justify-end">
                                                <button
                                                    id="assignBtn"
                                                    type="submit"
                                                    class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-gray-400">
                                                    <i class="fas fa-user-plus"></i>
                                                    Assign Officers
                                                </button>
                                            </div>
                                    </form>
                                @endif
                            @endcan

                            @can('colleagueReview', $circulation)
                                <div class="mt-6 bg-white rounded-lg border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                        Review Action
                                    </h3>

                                    <form method="POST" action="{{ route('registry.file-circulations.colleague.update', $circulation->id)}}">
                                        @csrf
                                        
                                        <input type="hidden" name="circulation" value="{{ $circulation->id }}">
                                        
                                        <div>
                                            <textarea
                                                name="colleague_comment"
                                                id="colleague_comment"
                                                rows="5"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                                placeholder="Enter review comments, recommendations or required amendments..."
                                            >{{ old('colleague_comment') }}</textarea>

                                            @error('colleague_comment')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mt-6 flex items-center gap-3">
                                            <button
                                                type="submit"
                                                name="action"
                                                value="return"
                                                class="inline-flex items-center rounded-lg bg-red-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-600"
                                            >
                                                Return for Amendment
                                            </button>

                                            <button
                                                type="submit"
                                                name="action"
                                                value="approve"
                                                class="inline-flex items-center rounded-lg bg-cyan-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-cyan-600"
                                            >
                                                Submit for Approval (HOD)
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endcan
                        @endif

                        @if($fileAssignment)
                            @can('view', $fileAssignment)
                                <form method="POST" action="{{ route('registry.file.reassign', $circulation->id) }}" class="mb-6 mt-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                    @csrf

                                    @if($fileAssignment->reassigned_from !== null)
                                        <div class="mb-4 p-4 rounded-lg border border-amber-200 bg-amber-50">
                                            <div class="flex items-start justify-between">
                                                <div class="text-sm text-amber-700">
                                                    <span class="font-semibold">
                                                        Reassigned File
                                                    </span><br>
                                                    This file was assigned to you from 
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
                                    
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" name="action" value="rejected" class="action-radio" id="reject-radio">
                                                    <span class="text-red-600 font-medium">
                                                        Reject and send back
                                                    </span>
                                                </label>
                            
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
                        
                                    @elseif ($fileAssignment->reassigned_from === null)
                                        <div class="mb-4 font-semibold border-b pb-4">
                                            <label class="text-cyan-700">
                                                This file has been assigned to you. Select from the available actions:
                                            </label>
                                            <div class="mt-2 space-y-2">
                                                <label class="flex items-center gap-2">
                                                    <input type="radio" name="action" value="accepted" class="action-radio">
                                                    <span class="text-gray-700">Accept and mark as Received</span>
                                                </label>
                                                @if(!$notAssignedOfficers->isEmpty())
                                                    <label class="flex items-center gap-2">
                                                        <input type="radio" name="action" value="reassign" class="action-radio">
                                                        <span class="text-gray-700">Assign other officers</span>
                                                    </label>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                
                                    <div id="officer-select" class="mb-4 hidden">
                                        <label class="block font-semibold text-gray-700 mb-1">Select:</label>
                                        <select id="assignedOfficers"
                                                    name="officers[]"
                                                    multiple
                                                    class="w-full">
                                                    @foreach($divisionUsers as $user)
                                                        @if($user->id !== auth()->user()->id && $user->id !== $reviewOfficer->id)
                                                            <option value="{{ $user->id }}" class="bg-white border rounded-xl">
                                                                {{ $user->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                        </select>
                                        @error('officers')
                                            <p class="mt-2 text-sm text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                        <label class="block font-semibold text-gray-700 mb-1">Comment:</label>
                                        <textarea
                                                name="reassign_comment"
                                                id="reassign_comment"
                                                rows="2"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                                placeholder="Enter comments for the assigned officers"
                                                required
                                            >{{ old('reassign_comment') }}</textarea>
                                    
                                    </div>

                                    <button type="submit"
                                        class="bg-cyan-600 w-full hover:bg-cyan-800 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition duration-200">
                                        Submit
                                    </button>
                                </form>
                            @endcan
                        @endif

                        @can('close', $file)
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
                        @endcan 

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
    <script>
        const choices = new Choices('#assignedOfficers', {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Select officers to assign',
            shouldSort: false,
            searchEnabled: true
        });

        function clearAssignedOfficers() {
            choices.removeActiveItems();

            Array.from(select.options).forEach(option => {
                option.selected = false;
            });

            select.dispatchEvent(new Event('change', { bubbles: true }));
        }

        document.getElementById('selectAllBtn').addEventListener('click', () => {
            const values = Array.from(
                document.querySelectorAll('#assignedOfficers option')
            ).map(option => option.value);

            choices.removeActiveItems();
            choices.setChoiceByValue(values);
            updateButton();
        });

        // Initial check
        toggleAssignButton();


    </script>
  
@endsection