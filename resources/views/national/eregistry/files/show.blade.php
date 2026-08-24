@extends('layouts.app')

@section('content')
        @if (session('error'))
            <div class="mb-4 border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif
        @php
            $statusClass = match($file->status) {
                'Pending Action' => 'bg-red-100 text-red-700',
                'Rejected' => 'bg-red-100 text-red-700',
                'Received' => 'bg-blue-100 text-blue-700',
                'Dispatched' => 'bg-cyan-100 text-cyan-700',
                'Pending Review' => 'bg-yellow-100 text-yellow-700',
                'Pending SRO Approval' => 'bg-yellow-100 text-yellow-700',
                'Pending UFS' => 'bg-yellow-100 text-yellow-700',
                'Approved' => 'bg-emerald-100 text-emerald-700',
                'UFS Approved' => 'bg-emerald-100 text-emerald-700',
                'Reviewed' => 'bg-green-100 text-green-700',
                'Pending Signature' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-600',
            };
        @endphp
        @php
            $statusClass2 = match(optional($circulation)->status) {
                'Received' => 'bg-blue-100 text-blue-700',
                'Pending Action' => 'bg-red-100 text-red-700',
                'Rejected' => 'bg-red-100 text-red-700',
                'Dispatched' => 'bg-cyan-100 text-cyan-700',
                'Pending Review' => 'bg-yellow-100 text-yellow-700',
                'Pending Receipt' => 'bg-yellow-100 text-yellow-700',
                'Pending SRO Approval' => 'bg-yellow-100 text-yellow-700',
                'Pending UFS' => 'bg-yellow-100 text-yellow-700',
                'Approved' => 'bg-emerald-100 text-emerald-700',
                'UFS Approved' => 'bg-emerald-100 text-emerald-700',
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
                        @if($file->ministry_id == Auth::user()->ministry_id && !$circulation)
                            <span class="inline-block mt-1 text-xs font-medium px-2 py-1 rounded-full {{ $statusClass }}">
                                {{ $file->status ?? '-' }}
                            </span>
                        @elseif($circulation?->to_ministry_id == Auth::user()->ministry_id && $file->ministry_id != Auth::user()->ministry_id) 
                            <span class="inline-block mt-1 text-xs font-medium px-2 py-1 rounded-full {{ $statusClass2 }}">
                                {{ $circulation->status ?? '-' }}
                            </span>
                        @elseif($file->ministry_id == Auth::user()->ministry_id && $circulation && $circulation?->to_ministry_id == Auth::user()->ministry_id) 
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
            @if($file->ufsOfficer && ( $file->status === 'Pending UFS' || $file->status === 'UFS Approved' || $file->status === 'UFS Rejected'))  
                <div class="mx-auto max-w-5xl justify-center border bg-white border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                    <h3 class="font-bold text-cyan-600 mb-2">UFS Details</h3>
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


            {{-- HOD Review Details --}}
            @if($file->created_by === Auth::user()->id && $circulation && $circulation->colleague_id !== null) 
                <div class="mx-auto max-w-5xl justify-center border bg-white border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                    <h3 class="font-bold text-cyan-600 mb-2">HOD Review Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Name</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{ $circulation->colleague->name ?? '-'}}
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Feedback</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                {{ $circulation->colleague_comment ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif  
            
            @if($circulation)
                @can('viewFinalReview', $circulation)
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
                                    @if($circulation->reviewed_by)
                                        <p class="text-sm text-gray-500">Reviewed By</p>
                                        <p class="text-sm font-medium text-gray-800 mt-1">
                                            {{ $circulation->reviewedBy->name ?? '-'}}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-500">To review</p>
                                        <p class="text-sm font-medium text-gray-800 mt-1">
                                        {{ $circulation->reviewOfficer->name ?? '-'}} 
                                        </p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Review Date</p>
                                    <p class="text-sm font-medium text-gray-800 mt-1">
                                        {{ $circulation->date_reviewed ? \Carbon\Carbon::parse($circulation->date_reviewed)->format('d M Y, h:i A') : '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Comment</p>
                                <p class="text-sm font-medium text-gray-800 mt-1 break-all"">
                                    {{ $circulation->review_comment ?? '-'  }}
                                </p>
                            </div>  
                            
                            @can('viewAssignments', $circulation)
                                    <p class="text-sm text-gray-500">Assigned Officers</p>

                                    @if($circulation && $circulation->activeAssignments->isNotEmpty())
                                        @php
                                            $assignments = $circulation->activeAssignments;
                                        @endphp

                                        <div class="list-disc list-inside text-gray-900 text-sm font-semibold space-y-2">
                                            @foreach($assignments as $index => $assignment)
                                                @if(!$assignment->reassigned_from)
                                                    <div class="mt-1 flex justify-between items-start rounded-lg border border-gray-200 bg-white p-3 hover:shadow-sm transition">
                                                        <div>
                                                            <div class="text-gray-900 font-medium">
                                                                {{ $assignment->officer->name }}
                                                            </div>

                                                            <div class="text-xs text-gray-500 mt-1">
                                                                {{ $assignment->officer->division->name ?? 'No Division' }}
                                                            </div>
                                                        </div>

                                                        <div class="flex flex-col items-end gap-2 text-right">
                                                            @if($assignment->status === 'accepted')
                                                                <span class="text-xs bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded-full">
                                                                    Received
                                                                </span>
                                                            @elseif($assignment->status === 'pending')
                                                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                                                                    Pending
                                                                </span>
                                                            @elseif($assignment->status === 'reassigned')
                                                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">
                                                                    Reassigned
                                                                </span>
                                                            @elseif($assignment->status === 'in_progress')
                                                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                                                                    In progress
                                                                </span>
                                                            @elseif($assignment->status === 'complete')
                                                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                                                    Complete
                                                                </span>
                                                                <div class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded-md">
                                                                    Comments: 
                                                                    <span class="font-medium text-gray-700">
                                                                        {{ $assignment->assigned_officer_comment }}
                                                                    </span>
                                                                </div>
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
                @endcan
            @endif

            @can('viewHodAssignments', $fileAssignment)
                <div class="mx-auto max-w-5xl justify-center border bg-white border-gray-200 rounded-lg shadow-sm p-4 mt-4">
                    <h3 class="font-bold text-cyan-600 uppercase mb-2">Assigned Officers </h3>
                  
                    @if($circulation && $circulation->activeAssignments->isNotEmpty())
                            @php
                                $assignments = $circulation->activeAssignments;
                            @endphp

                            <div class="overflow-x-auto border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                                Officer
                                            </th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                                Assigned By
                                            </th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                                Task
                                            </th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                                Status
                                            </th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                                Assigned Officer Remarks
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($assignments as $assignment)
                                            @if($assignment->reassigned_from)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 font-medium text-gray-900">
                                                        {{ $assignment->officer->name }}
                                                    </td>
                                                     <td class="px-4 py-3 text-gray-600">
                                                        {{ $assignment->assignedBy->name }}
                                                    </td>

                                                    <td class="px-4 py-3 text-gray-700">
                                                        {{ $assignment->task ?? '-' }}
                                                    </td>

                                                    <td class="px-4 py-3">
                                                        @if($assignment->status === 'accepted')
                                                            <span class="rounded-full bg-cyan-100 px-2 py-1 text-xs font-medium text-cyan-700">
                                                                Received
                                                            </span>
                                                        @elseif($assignment->status === 'pending')
                                                            <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-700">
                                                                Pending
                                                            </span>
                                                        @elseif($assignment->status === 'completed')
                                                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                                Completed
                                                            </span>
                                                        @elseif($assignment->status === 'rejected')
                                                            <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">
                                                                Rejected
                                                            </span>
                                                        @else
                                                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">
                                                                {{ ucfirst($assignment->status) }}
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td class="px-4 py-3 text-gray-600">
                                                        {{ $assignment->assigned_officer_comment ?? '-' }}
                                                    </td>

                                                   
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                    @else
                        <p class="text-sm text-gray-800 font-semibold">-</p>
                    @endif
                    
                </div>
            @endcan

            {{-- Dispatch Details  --}}
            @if($file->ministry_id == $ministryId && $fileCirculations->isNotEmpty())
                <div class="mx-auto max-w-5xl justify-center border border-gray-200 rounded-lg bg-white shadow-sm p-4 mt-4">
                    <div class="mb-4">
                        <h3 class="uppercase font-bold text-cyan-600">
                            Dispatch Details
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @forelse($fileCirculations as $circulation)
                            <div class="flex items-center justify-between border border-gray-100 bg-white p-3">
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
                $onlineViewUrl = route('registry.files.view.online', ['file' => $file]);
                $extension = strtolower(pathinfo($file->main_file_path, PATHINFO_EXTENSION));
                $downloadUrl = route('registry.files.download', $file);
            @endphp

            @if ($file->document_source === 'upload' )
                <div class="mt-4 mb-2 flex justify-center">
                    <label class="text-sm text-gray-500 block uppercase justify-center">File Upload Preview</label>
                </div>
                <div class="mb-6 flex justify-center">
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
            @elseif ($file->final_pdf_path)
                <div class="mt-4 mb-2 flex justify-center">
                    <label class="text-sm text-gray-500 block uppercase justify-center">Final PDF Preview</label>
                </div>
                <div class="mb-6 flex justify-center">
                    <embed
                        src="{{ asset('storage/' . $file->final_pdf_path) }}"
                        type="application/pdf"
                        class="w-full max-w-5xl rounded-2xl border border-gray-200 shadow-sm bg-white"
                        style="height: 900px;"
                    >
                </div>
            @else
                <div class="mt-4 mb-2 flex justify-center">
                    <label class="text-sm text-gray-500 block uppercase justify-center">Online Preview</label>
                </div>
                <div class="mb-6 flex justify-center">
                    <iframe
                        src="{{ $onlineViewUrl }}"
                        class="w-full max-w-5xl rounded-2xl border border-gray-200 shadow-sm bg-white"
                        style="height: 900px;"
                    ></iframe>
                </div>
            @endif

            {{-- download main file --}}
            <div class="mb-2 max-w-5xl mx-auto flex">
                <a href="{{ $downloadUrl }}"
                            class="inline-flex items-center justify-center gap-2 border border-gray-300 bg-white px-3 py-3 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:border-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 16V4m0 12l-4-4m4 4l4-4M4 20h16" />
                            </svg>
                            Download Main File
                </a>
            </div>

            {{-- Supporting Documents --}}
            @if(!empty($file->additional_file_paths))
                <div class="mx-auto max-w-5xl mb-6 justify-center border border-gray-200 rounded-lg bg-white shadow-sm p-4 mt-2">
                    <h3 class="text-lg text-gray-900 mb-3">
                        Supporting Documents
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($file->additional_file_paths as $number => $document)
                            @php
                                $filePath = $document['file_path'] ?? null;

                                $docDownloadUrl = route('registry.files.download.additional', [
                                    'file' => $file,
                                    'number' => $number,
                                ]);
                            @endphp

                            <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-white p-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $document['name'] ?? ($filePath ? basename($filePath) : 'Unnamed Document') }}
                                    </p>

                                    @if(!empty($document['created_at']))
                                        <p class="mt-1 text-xs text-gray-500">
                                            Uploaded:
                                            {{ \Carbon\Carbon::parse($document['created_at'])->format('d M Y g:i A') }}
                                        </p>
                                    @endif
                                </div>

                                <a href="{{ $docDownloadUrl }}"
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-600 bg-gray-600 px-3 py-2 text-sm font-medium text-slate-500 shadow-sm transition hover:bg-gray-700 text-white">
                                    <i class="fa fa-download"></i>
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="mb-6 max-w-5xl mx-auto flex justify-center">
                <div class="w-full space-y-6">
                    
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
                                    {{ \Carbon\Carbon::parse($closedDate)->format('d M Y \a\t g:i A') }}                                
                                </p>
                            </div>
                        </div>
                    @else 
                        @can('update', $file)
                            <a href="{{ route('registry.files.edit', $file) }}"
                                class="inline-flex items-center justify-center gap-2 border border-cyan-600 bg-cyan-600 px-3 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-cyan-700">
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

                        @can('sign', $file)
                            <button type="button"
                                    onclick="toggleActionPanel('sign-memo-box', this)"
                                    class="action-button inline-flex items-center justify-center gap-2
                                            border border-cyan-600 bg-cyan-600 p-6 py-3
                                            text-sm font-medium text-white shadow-sm transition
                                            hover:bg-cyan-700">                                    
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16.862 3.487a2.1 2.1 0 113 2.97L8.25 18.07 4 19l.93-4.25L16.862 3.487z" />
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13.5 6.5l4 4" />
                                    </svg>
                                    Sign File
                            </button>
                        @endcan

                        @can('reject', $file)
                            <button type="button"
                                    onclick="toggleActionPanel('reject-memo-box', this)"
                                    class="action-button inline-flex items-center justify-center gap-2
                                            border border-red-600 bg-red-600 p-6 py-3
                                            text-sm font-medium text-white shadow-sm transition
                                            hover:bg-red-700">      
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
                                    
                                    Reject File
                            </button>
                        @endcan

                        @can('circulateToReviewOfficer', $file)
                            <button type="button"
                                    onclick="toggleActionPanel('circulatePanel', this)"
                                    class="action-button inline-flex items-center justify-center gap-2
                                                                                border border-cyan-600 bg-cyan-600 px-3 py-3
                                                                                text-sm font-medium text-white shadow-sm transition
                                                                                hover:bg-cyan-700">                                     
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
                        @endcan 

                        @can('dispatch', $file)
                            <button type="button"
                                    onclick="toggleActionPanel('dispatchPanel', this)"
                                    class="action-button inline-flex items-center justify-center gap-2
                                                border border-cyan-600 bg-cyan-600 px-8 py-3
                                                text-sm font-medium text-white shadow-sm transition
                                                hover:bg-cyan-700">                                 
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
                        @endcan

                        @can('ufsCirculate', $file)
                            @if(Auth::user()->signature_path)
                                <button
                                    type="button"
                                    onclick="toggleActionPanel('ufs-selection-box', this)"
                                    class="action-button inline-flex items-center justify-center gap-2
                                                border border-cyan-600 bg-cyan-600 px-3 py-3
                                                text-sm font-medium text-white shadow-sm transition
                                                hover:bg-cyan-700">   
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
                            @else
                                <div class="border border-red-300 justify-center bg-red-50 p-4">
                                    <p class="text-sm font-medium text-red-700">
                                        You must upload your signature before this document can be circulated for UFS approval.
                                    </p>

                                    <a href="{{ route('registry.users.signature.edit') }}"
                                        class="mt-3 inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                                        Upload Signature
                                    </a>
                                </div>
                            @endif
                        @endcan
                       
                        @can('circulateToHod', $file)
                            <button
                                type="button"
                                onclick="toggleActionPanel('colleague-selection-box', this)"
                                class="action-button inline-flex items-center justify-center gap-2
                                        border border-cyan-600 bg-cyan-600 px-3 py-3
                                        text-sm font-medium text-white shadow-sm transition
                                        hover:bg-cyan-700">                                
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
                                Submit to HOD
                            </button>
                        @endcan
                         
                        @if($circulation)
                            @can('ufs', $circulation)
                                <div class="w-full mt-4">
                                    <form id="ufsDecisionForm" method="POST" action="{{ route('registry.ufs.approve', $circulation) }}" class="space-y-4 border rounded-xl p-4 bg-gray-50">
                                        @csrf
                                        <div class="bg-gray-50"> 
                                            <h3 class="text-lg font-semibold text-gray-800">UFS Review</h3> </div>
                                            <input type="hidden" name="file_id" value="{{ $file->id }}">
                                            
                                            <div class="flex items-center gap-3">
                                                <button onclick="setApprovalRoute('approve', {{ $circulation->id }})" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium bg-emerald-400 shadow-sm transition">
                                                    Approve
                                                </button>
                                                <button onclick="setApprovalRoute('reject', {{ $circulation->id }})" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium bg-red-400 shadow-sm transition">
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endcan
                           
                            {{-- sro, hod review buttons --}}
                            @can('review', $circulation)
                                    <div id="approveSection" class="w-full">
                                        <form method="POST" action="{{ route('registry.file.review', $circulation) }}" class="border border-gray-200 bg-white p-4 shadow-sm">
                                            @csrf
                                            <input type="hidden" name="file_id" value="{{ $file->id }}">
                                            <div>
                                                <label class="mb-2 block text-md font-semibold text-gray-700">
                                                    Select Status <span class="text-red-600">*</span>
                                                </label>

                                                <div class="space-y-2">

                                                    {{-- Approve --}}
                                                    <label class="flex items-center gap-3 cursor-pointer">
                                                        <input type="radio"
                                                            name="status"
                                                            value="Approved"
                                                            class="h-4 w-4 border-gray-300 text-green-600 focus:ring-green-500"
                                                            {{ old('status') === 'Approved' ? 'checked' : '' }}
                                                            required>

                                                        <span class="text-md font-medium text-gray-700">
                                                            Approve
                                                        </span>
                                                    </label>

                                                    {{-- Reject --}}
                                                    <label class="flex items-center gap-3 cursor-pointer">
                                                        <input type="radio"
                                                            name="status"
                                                            value="Rejected"
                                                            class="h-4 w-4 border-gray-300 text-red-600 focus:ring-red-500"
                                                            {{ old('status') === 'Rejected' ? 'checked' : '' }}>

                                                        <span class="text-md font-medium text-gray-700">
                                                            Reject
                                                        </span>
                                                    </label>

                                                </div>
                                            </div>

                                            <div>
                                                <label class="mb-2 mt-4 block text-md font-semibold text-gray-700">
                                                    Comment
                                                </label>
                                                <textarea name="comment"
                                                    rows="3"
                                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring focus:ring-cyan-200"
                                                    placeholder="Write your review comment...">{{ old('comment') }}</textarea>
                                            </div>
                                            <div class="mt-6">
                                                <label for="assignedOfficers" class="block text-md font-medium text-gray-700 mb-2">
                                                    Assign Responsible Officers
                                                    <button
                                                    type="button"
                                                    id="selectAllBtn"
                                                    class="border-cyan-200 bg-cyan-50 px-2 py-2 text-xs text-cyan-700 hover:bg-cyan-100">
                                                    Select All
                                                </button>
                                                </label>
                                               
                                                <select
                                                    id="assignedOfficers"
                                                    name="officers[]"
                                                    multiple
                                                    class="w-full">
                                                    @foreach($notAssignedOfficers as $user)
                                                        @if(!($user->id === $circulation->review_officer) || $user->id != Auth::user()->id)
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
                                            <div class="flex mt-6 justify-right">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 bg-cyan-400 rounded-lg px-5 py-2.5 text-sm font-medium shadow-sm transition
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
                                                    Submit
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endcan

                            @can('markReceive', $circulation)
                                    <form action="{{ route('registry.file-circulations.receive', $circulation) }}" method="POST">
                                        @csrf
                                        @method('patch')
                                        <button type="submit"
                                                id="acceptFileBtn"
                                                class="inline-flex gap-2 bg-cyan-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                            @endcan

                            @can('circulateToReviewOfficer', $circulation)
                                <button type="button"
                                    onclick="toggleActionPanel('circulation-review-officer', this)"
                                    class="inline-flex items-center gap-2 bg-cyan-700 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-800 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                            @endcan

                            @can('hodReview', $circulation)
                                <button type="button"
                                    onclick="toggleActionPanel('colleague-review', this)"
                                    class="inline-flex items-center gap-2 bg-cyan-700 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-800 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                    Provide Feedback
                                </button>
                            @endcan
                        @endif

                        @if($fileAssignment)
                            @can('reassign', $fileAssignment)
                                <form method="POST" action="{{ route('registry.file.reassign', $circulation->id) }}" class="mb-6 mt-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                    @csrf
                                    @if($fileAssignment->reassigned_from === null)
                                        <div class="mb-4 font-semibold border-b pb-4">
                                            <label class="text-cyan-700">
                                                This file has been assigned to you. Select from the available actions:
                                            </label>
                                            <div class="mt-2 space-y-2">
                                                <label class="flex items-center gap-2">
                                                    <input type="radio" name="action" value="accepted" class="action-radio">
                                                    <span class="text-gray-700">Accept file task </span>
                                                </label>
                                                @if(!$notAssignedOfficers->isEmpty())
                                                    <label class="flex items-center gap-2">
                                                        <input type="radio" name="action" value="reassign" class="action-radio">
                                                        <span class="text-gray-700">Assign other officers in your division</span>
                                                    </label>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                
                                    <div id="officer-select" class="mb-4 hidden">
                                            <label class="block font-semibold text-gray-700 mb-2">
                                                Assign Tasks
                                            </label>

                                            <div id="task-assignments" class="space-y-3">
                                                <div class="task-row rounded-lg border border-gray-200 bg-gray-50 p-4">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                                        Responsible Officer <span class="text-red-600">*</span>
                                                    </label>

                                                    <select
                                                        name="tasks[0][officer_id]"
                                                        required
                                                        class="mt-1 mb-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                                    >
                                                        <option value="">-- Select an officer --</option>

                                                        @foreach($notAssignedOfficers as $user)
                                                            @if($user->division_id === Auth::user()->division_id && $user->id !== Auth::user()->id)
                                                                <option value="{{ $user->id }}">
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>

                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                                        Task <span class="text-red-600">*</span>
                                                    </label>

                                                    <textarea
                                                        name="tasks[0][task]"
                                                        rows="2"
                                                        required
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                                        placeholder="Describe the task for this officer"
                                                    ></textarea>
                                                </div>
                                            </div>

                                            <button
                                                type="button"
                                                onclick="addTaskRow()"
                                                class="mt-3 rounded-md bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200"
                                            >
                                                + Add another officer task
                                            </button>

                                            @error('tasks')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                    </div>
                                    

                                    <button type="submit"
                                        class="bg-cyan-600 w-full hover:bg-cyan-800 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition duration-200">
                                        Submit
                                    </button>
                                </form>
                            @endcan

                            @can('accept', $fileAssignment)
                                <form method="POST" action="{{ route('registry.file.accept', $circulation->id) }}" class="mb-6 mt-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                    @csrf
                                    <span class="font-semibold text-lg">
                                        Assigned File 
                                    </span><br>
                                    <div class="mt-4 mb-2 p-4 rounded-lg border border-gray-200 bg-gray-50">
                                            <div class="flex items-start justify-between">
                                                <div class="text-md text-gray-700">
                                                    This file was assigned to you by
                                                    <span class="font-medium text-gray-800">
                                                        {{ $fileAssignment->reassignedFrom->name }}
                                                    </span><br>
                                                    <div class="mt-2">
                                                        Task:
                                                        <span class="font-medium text-gray-800">
                                                            {{ $fileAssignment->task}}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            {{-- <div class="mt-4 space-y-3">
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" name="action" value="accepted" class="action-checkbox">
                                                    <span class="text-gray-700 font-medium">
                                                        Accept and mark as Received
                                                    </span>
                                                </label>
                                            </div> --}}
                                        </div>
                                    <button type="submit"
                                            name="action" value="accepted"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 shadow-sm transition duration-200">
                                        Accept and Mark as Received
                                    </button>
                                </form>
                            @endcan

                            @can('complete', $fileAssignment)
                                <div>
                                    <form method="POST" action="{{ route('registry.file.complete', $circulation->id) }}" class="mb-6 mt-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                        @csrf
                                        <input type="hidden" name="file_id" value="{{ $file->id }}">
                                        <div class="mb-4">
                                            <label class="block font-semibold text-gray-700 mb-1">Final Remarks:</ label>
                                            <textarea
                                                name="assignee_comment"
                                                id="assignee_comment"                   
                                                rows="2"
                                                class="mt-1 block w-full border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                            >{{ old('assignee_comment') }}</textarea>
                                            @error('assignee_comment')
                                                <p class="mt-2 text-sm text-red-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                        <button type="submit"
                                            class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 px-4 shadow-sm transition duration-200">
                                            Mark as Complete
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        @endcan

                        @can('close', $file)
                            <form action="{{ route('registry.files.close', $file) }}" 
                                method="POST"
                                onsubmit="return confirmCloseFile();">
                                @csrf
                                <button type="submit"
                                    id="closeFileBtn"
                                    class="inline-flex items-center gap-2 bg-red-500 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-400">

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

                        {{-- ****************************----Panels-----******************************************************************************************************************************************************************************************* --}}

                        @can('dispatch', $file)
                            <div>
                                @if($file->document_source === 'upload') 
                                    <form action="{{ route('registry.dispatches.store') }}" method="POST">
                                        @csrf
                                        <div id="dispatchPanel" class="action-panel hidden rounded-2xl mt-2 border border-gray-200 bg-white p-6 shadow-sm">
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
                                                    <label class="flex items-start gap-3 border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 hover:bg-gray-100 transition">
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
                                @elseif ($file->document_source === 'online')
                                     <form action="{{ route('registry.dispatches.store') }}" method="POST">
                                        @csrf
                                        <div id="dispatchPanel" class="action-panel hidden rounded-md mt-2 border border-gray-200 bg-white p-6 shadow-sm">
                                            <input type="hidden" name="file_id" value="{{ $file->id }}">
                                            <div class="mb-4">
                                                <h3 class="text-sm text-gray-900">
                                                    Dispatch to Ministries
                                                </h3>
                                    
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                @foreach($memoRecipients as $id => $ministry)
                                                    <label class="items-start gap-3 border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 hover:bg-gray-100 transition">
                                                        <input type="hidden"
                                                            name="recipient_ministries[]"
                                                            value="{{ $ministry->id }}">
                                                        <span class="font-medium">
                                                            {{ $ministry->name }} ({{ $ministry->code }})
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            @if($ministries->isNotEmpty())
                                                <button type="submit"
                                                    onclick="return confirm('Are you sure you want to dispatch this file?');"
                                                    class="mt-6 bg-cyan-700 px-5 py-3 rounded-md text-white text-sm font-bold cursor-not-allowed shadow-sm transition">
                                                    Confirm Dispatch
                                                </button>
                                            @else
                                                <button type="button"
                            
                                                    class="mt-6 w-full rounded-xl bg-gray-200 px-5 py-3 text-sm font-bold text-gray-500 cursor-not-allowed">
                                                    No Ministries Available
                                                </button>
                                            @endif
                                        </div>
                                    </form>

                                @endif
                            </div>
                        @endcan

                        @can('sign', $file)
                            <div id="sign-memo-box" class="action-panel hidden">
                                <form method="POST" action="{{ route('registry.files.sign', $file) }}" class="space-y-2 border border-gray-200 bg-gray-50 p-5 shadow-sm">
                                    @csrf
                                    @php
                                        $hasSignature = !empty(Auth::user()->signature_path);
                                    @endphp
                                    <div class="flex-1">
                                        <label class="block text-sm font-semibold text-gray-800">
                                            Electronic Signature
                                        </label>
                                        <p class="mt-1 text-xs text-gray-600">
                                            Your stored system signature will automatically be added to the approved memo.
                                        </p>
                                        @if($hasSignature)
                                            <div class="mt-3 border bg-white p-3">
                                                <p class="mb-2 text-xs font-medium text-gray-500">
                                                    Signature Preview
                                                </p>
                                                <img src="{{ asset('storage/' . Auth::user()->signature_path) }}"
                                                    alt="User Signature"
                                                    class="max-h-20 object-contain">
                                            </div>
                                            <div class="flex justify-right">
                                                <button type="submit"
                                                    {{ (!$hasSignature) ? 'disabled' : '' }}
                                                    class="mt-4 inline-flex items-center gap-2 px-5 py-3 text-sm font-medium text-white shadow-sm transition
                                                        {{ (!$hasSignature)
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
                                                            d="M16.862 3.487a2.1 2.1 0 113 2.97L8.25 18.07 4 19l.93-4.25L16.862 3.487z" />
                                                        <path stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13.5 6.5l4 4" />
                                                    </svg>
                                                    Sign File
                                                </button>
                                            </div>
                                        @else
                                            <div class="mt-3 rounded-md border border-red-300 bg-red-50 p-3 text-xs text-red-700">
                                                A signature is required before signing this memo.
                                                Please upload your signature in your profile settings.
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        @endcan

                        @can('reject', $file)
                            <div id="reject-memo-box" class="action-panel hidden">
                                <form method="POST" action="{{ route('registry.files.sign', $file) }}" class="space-y-2 border border-gray-200 bg-gray-50 p-5 shadow-sm">
                                    @csrf
                                    @php
                                        $hasSignature = !empty(Auth::user()->signature_path);
                                    @endphp
                                            <div class="flex-1">
                                                <label class="block text-sm font-semibold text-gray-800">
                                                    Electronic Signature
                                                </label>
                                                <p class="mt-1 text-xs text-gray-600">
                                                    Your stored system signature will automatically be added to the approved memo.
                                                </p>
                                                @if($hasSignature)
                                                    <div class="mt-3 border bg-white p-3">
                                                        <p class="mb-2 text-xs font-medium text-gray-500">
                                                            Signature Preview
                                                        </p>
                                                        <img src="{{ asset('storage/' . Auth::user()->signature_path) }}"
                                                            alt="User Signature"
                                                            class="max-h-20 object-contain">
                                                    </div>
                                                    <div class="flex justify-right">
                                                        <button type="submit"
                                                            {{ (!$hasSignature) ? 'disabled' : '' }}
                                                                class="mt-4 inline-flex items-center gap-2 px-5 py-3 text-sm font-medium text-white shadow-sm transition
                                                                {{ (!$hasSignature)
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
                                                                    d="M16.862 3.487a2.1 2.1 0 113 2.97L8.25 18.07 4 19l.93-4.25L16.862 3.487z" />
                                                                <path stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M13.5 6.5l4 4" />
                                                            </svg>
                                                            Sign File
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="mt-3 rounded-md border border-red-300 bg-red-50 p-3 text-xs text-red-700">
                                                        A signature is required before signing this memo.
                                                        Please upload your signature in your profile settings.
                                                    </div>
                                                @endif
                                            </div>
                                
                                </form>
                            </div>
                        @endcan

                        @can('circulateToReviewOfficer', $file)
                            <div id="circulatePanel" class="action-panel hidden">
                                <form method="POST" action="{{ route('registry.file-circulations.store') }}">
                                        @csrf
                                        <input type="hidden" name="file_id" value="{{ $fileId }}">
                                        @if(!$reviewOfficer)
                                            <div class="mb-6 border border-red-200 bg-red-50 px-6 py-4">
                                                <a href="{{ route('registry.users.edit-review-officer') }}"
                                                    class="text-sm font-medium text-red-700 hover:text-red-800 hover:underline">
                                                    ⚠️ No Review Officer assigned. Please assign one before submitting.
                                                </a>
                                            </div>
                                        @else
                                            <div class="mb-6 border border-gray-200 bg-white shadow-sm">
                                                <div class="border-b border-gray-100 px-6 py-4">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                                                            Review Officer
                                                        </h3>

                                                        <a href="{{ route('registry.users.edit-review-officer') }}"
                                                            class="inline-flex items-center border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-xs font-medium text-cyan-700 transition hover:bg-cyan-100">
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
                                                
                                                <div class="px-6 py-3">
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-2 bg-cyan-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                                                        <i class="fas fa-paper-plane"></i>
                                                        Submit
                                                    </button>
                                                </div>
                                            </div>

                                        @endif
                                </form>
                            </div>
                        @endcan

                        @can('ufsCirculate', $file)
                            <div id="ufs-selection-box" class="action-panel hidden border border-gray-200 bg-gray-50 p-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Officer Selected for UFS
                                    </label>

                                    <div class="border border-gray-200 bg-white px-4 py-3 text-sm text-gray-800">
                                        {{ $file->ufsOfficer?->name ?? 'No UFS officer selected' }}
                                    </div>

                                    <form method="POST" action="{{ route('registry.files.ufsCirculate', $file) }}" class="space-y-4">
                                        @csrf

                                        <input type="hidden" name="internal_ufs_id" value="{{ $file->internal_ufs_id }}">

                                        <button
                                            type="submit"
                                            class="mt-4 bg-cyan-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-cyan-800">
                                            Submit
                                        </button>
                                    </form>
                            </div>
                            @error('internal_ufs_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        @endcan

                        @can('circulateToHod', $file)
                            <div id="colleague-selection-box" class="action-panel hidden border border-gray-200 bg-gray-50 p-4">
                                <form method="POST" action="{{ route('registry.file-circulations.colleague.store') }}">
                                    @csrf
                                    <input type="hidden" name="file_id" value="{{ $file->id }}">
                                    <input type="hidden" name="colleague" value="{{ $hod?->id }}">
                                     <div class="mb-6 border border-gray-200 bg-white shadow-sm">
                                                <div class="border-b border-gray-100 px-6 py-4">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                                                            Head of Division
                                                        </h3>
                                                    </div>
                                                </div>

                                                <div class="px-6 py-3">
                                                    @if(!$hod)
                                                        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-6 py-4">
                                                            @if(Auth::user()->hasRole('registry'))
                                                            <a href="{{ route('registry.divisions.assign-hod', Auth::user()->division) }}"
                                                                class="text-sm font-medium text-red-700 hover:text-red-800 hover:underline">
                                                                ⚠️ No HOD assigned for your division.
                                                            </a>

                                                            @else
                                                                <p class="text-sm font-medium text-red-700 hover:text-red-800 hover:underline">
                                                                    ⚠️ No HOD assigned for your division.
                                                                </p>
                                                            @endif
                                                        </div>
                                                    @elseif($hod)
                                                        <p class="text-lg font-semibold text-gray-900">
                                                            {{ $hod->name }}
                                                        </p>
                                                        <p class="mt-1 text-sm text-gray-600">
                                                            {{ $hod->designation }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex justify-left">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 bg-cyan-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                                                    <i class="fas fa-paper-plane"></i>
                                                    Submit for Review
                                                </button>
                                            </div>
                                   
                                </form>
                            </div>
                        @endcan

                        @can('circulateToReviewOfficer', $circulation)
                            <div id="circulation-review-officer" class="action-panel hidden border border-gray-200 bg-gray-50 p-4">
                                    <form method="POST" action="{{ route('registry.file-circulations.update', $circulation) }}">
                                        @csrf
                                        @method('put')

                                        @if(!$reviewOfficer)
                                            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-6 py-4">
                                                <a href="{{ route('registry.users.edit-review-officer') }}"
                                                    class="text-sm font-medium text-red-700 hover:text-red-800 hover:underline">
                                                    ⚠️ No Review Officer assigned. Please assign one before submitting.
                                                </a>
                                            </div>
                                        @else
                                            <div class="mb-6 border border-gray-200 bg-white shadow-sm">
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

                                            <div class="flex justify-left">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 bg-cyan-600 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-cyan-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                                                    <i class="fas fa-paper-plane"></i>
                                                    Submit for Review
                                                </button>
                                            </div>
                                        @endif
                                    </form>
                            </div>
                        @endcan

                        @can('hodReview', $circulation)
                            <div id="colleague-review" class="action-panel hidden border border-gray-200 bg-gray-50 p-4">
                                <form method="POST" action="{{ route('registry.file-circulations.colleague.update', $circulation)}}">
                                    @csrf
                                    
                                    <input type="hidden" name="circulation" value="{{ $circulation->id }}">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                                            Comment
                                        </label>
                                        <textarea name="hod_comment"
                                            rows="2"
                                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring focus:ring-cyan-200"
                                            placeholder="Write your review comment...">{{ old('hod_comment') }}</textarea>
                                    </div>
                                    <div class="mt-6 flex items-center gap-3">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center bg-cyan-500 px-6 py-3 text-sm font-medium text-white hover:bg-cyan-600"
                                        >
                                        Return feedback
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endcan
                    @endif

                </div>
                 
            </div>
        </div>
    <script>
        function toggleActionPanel(panelId, button) {
            const selectedPanel = document.getElementById(panelId);

            if (!selectedPanel) return;

            const wasHidden = selectedPanel.classList.contains('hidden');

            // Hide all panels
            document.querySelectorAll('.action-panel').forEach(panel => {
                panel.classList.add('hidden');
            });

            // Reset all buttons
            document.querySelectorAll('.action-button').forEach(btn => {
                btn.classList.remove(
                    'bg-cyan-800',
                    'border-cyan-800',
                    'ring-2',
                    'ring-cyan-300'
                );

                btn.classList.add(
                    'bg-cyan-600',
                    'border-cyan-600'
                );
            });

            // Open selected panel
            if (wasHidden) {

                selectedPanel.classList.remove('hidden');

                // Make clicked button look active
                button.classList.remove(
                    'bg-cyan-600',
                    'border-cyan-600'
                );

                button.classList.add(
                    'bg-cyan-800',
                    'border-cyan-800',
                    'ring-2',
                    'ring-cyan-300'
                );
            }
        }
    </script>
   
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const actionRadios = document.querySelectorAll('.action-radio');
            const officerSelect = document.getElementById('officer-select');

            function updateActionFields() {
                const selectedAction = document.querySelector(
                    '.action-radio:checked'
                )?.value;

                const taskFields = officerSelect.querySelectorAll(
                    'select, textarea, input'
                );

                if (selectedAction === 'reassign') {
                    officerSelect.classList.remove('hidden');

                    taskFields.forEach(field => {
                        field.disabled = false;
                        field.required = true;
                    });

                } else {
                    officerSelect.classList.add('hidden');

                    taskFields.forEach(field => {
                        field.disabled = true;
                        field.required = false;
                    });
                }
            }

            actionRadios.forEach(radio => {
                radio.addEventListener('change', updateActionFields);
            });

            updateActionFields();
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
    <script>
                                        let taskIndex = 1;

                                        function addTaskRow() {
                                            const wrapper = document.getElementById('task-assignments');

                                            const row = document.createElement('div');
                                            row.className = 'task-row rounded-lg border border-gray-200 bg-gray-50 p-4';

                                            row.innerHTML = `
                                                <div class="flex justify-between items-center mb-2">
                                                    <p class="text-sm font-semibold text-gray-700">Officer Task</p>
                                                    <button type="button" onclick="this.closest('.task-row').remove()" class="text-sm text-red-600 hover:underline">
                                                        Remove
                                                    </button>
                                                </div>

                                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                                    Responsible Officer <span class="text-red-600">*</span>
                                                </label>

                                                <select
                                                    name="tasks[${taskIndex}][officer_id]"
                                                    required
                                                    class="mt-1 mb-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                                >
                                                    <option value="">-- Select an officer --</option>
                                                    @foreach(($divisionUsers ?? []) as $user)
                                                        @if($user->id !== auth()->id() && $user->id !== optional($reviewOfficer)->id)
                                                            <option value="{{ $user->id }}">
                                                                {{ $user->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>

                                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                                    Task <span class="text-red-600">*</span>
                                                </label>

                                                <textarea
                                                    name="tasks[${taskIndex}][task]"
                                                    rows="2"
                                                    required
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                                    placeholder="Describe the task for this officer"
                                                ></textarea>
                                            `;

                                            wrapper.appendChild(row);
                                            taskIndex++;
                                        }
    </script>
    <script>
        function confirmCloseFile() {
            return confirm(
                'Are you sure you want to close this file?\n\n' +
                'WARNING: Once this file is closed, it cannot be circulated or dispatched in your organisation.'
            );
        }
    </script>
@endsection