@extends('layouts.app')

@section('content')
<div class="container mx-w-5xl mx-auto">
    @php
        // 1. Figure out which ministry is logged in
        $recipientOrganisationId = auth()->user()->organisation_id;

        // 2. Get this ministry's record from the file's recipients relationship
        $recipient = $file->recipientMinistries()
            ->where('organisation_id', $recipientOrganisationId)
            ->first();

        // 3. Use the recipient-specific statusS
        $recipientStatus = $recipient?->pivot->status;
    @endphp

    @php
        // Normalise for comparisons
        $normalizedStatus = strtolower($recipientStatus);

        // 4. Decide pill style + display label based on status
        $status = $recipientStatus; // default

        switch ($normalizedStatus) {
            case 'pending circulation':
                $pill = 'text-slate-500 ring-1 ring-slate-400/30';
            break;

            case 'pending review':
                // $pill = 'bg-sky-500/20 text-sky-300 ring-1 ring-sky-400/30';
                $pill = 'text-amber-500 ring-1 ring-amber-400/30';
                break;

            case 'assigned':
                // Shorter display label, but keep full value in DB
                $status = 'Assigned';
                $pill = 'text-emerald-500 ring-1 ring-emerald-400/30';
                break;

            default:
                // Fallback style
                $pill = 'text-slate-500 ring-1 ring-slate-400/30';
                break;
            }                        
    @endphp

    <div class="mx-auto mt-6 bg-white justify-center border border-gray-200 rounded-xl shadow-sm p-5 max-w-3xl">
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

            {{-- File Type --}}
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">File Type</p>
                <p class="text-gray-900 font-semibold">
                    {{ $file->fileType->name ?? 'N/A' }}
                </p>    
            </div>

            {{-- Assigned Officers --}}

                 {{-- Review Officer --}}
                {{-- @isset($loggedInOrganisation->review)
                    
                @endisset --}}
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">Review Officer</p>
                    @if($fileCirculation?->toReviewFile)
                        <p class="text-gray-900 font-semibold">
                            {{ $fileCirculation->toReviewFile->first_name }} {{ $fileCirculation->toReviewFile->last_name }}
                        </p>
                    @else
                        <p class="text-gray-900 font-semibold">-</p>
                    @endif
                </div>

            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Assigned Officers</p>
                @if($fileCirculation?->assignedOfficers->isNotEmpty())
                    <ul class="list-disc list-inside text-gray-900 font-semibold">
                        @foreach($fileCirculation->assignedOfficers as $officer)
                            <li>{{ $officer->first_name }} {{ $officer->last_name }}: {{ $officer->pivot->status }} </li> 
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-900 font-semibold">-</p>
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
        </div>
    </div>

    <div class="px-6 py-4 flex flex-col items-center gap-4">

        {{-- Main file --}}
        <a href="{{ asset('storage/'.$file->main_file_path) }}"
            target="_blank"
            class="flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white px-5 py-2 rounded-lg shadow">
                📄 View Main File
        </a>

        {{-- Additional files --}}
        <div class="flex flex-col items-center gap-2">
            @if($file->additional_file1_path)
                <a href="{{ asset('storage/'.$file->additional_file1_path) }}" target="_blank"
                class="text-cyan-700 hover:underline flex items-center gap-1">
                    {{ $file->additional_file1_name ?? 'Attachment 1' }}
                </a>
            @endif

            @if($file->additional_file2_path)
                <a href="{{ asset('storage/'.$file->additional_file2_path) }}" target="_blank"
                class="text-cyan-700 hover:underline flex items-center gap-1">
                    {{ $file->additional_file2_name ?? 'Attachment 2' }}
                </a>
            @endif

            @if($file->additional_file3_path)
                <a href="{{ asset('storage/'.$file->additional_file3_path) }}" target="_blank"
                class="text-cyan-700 hover:underline flex items-center gap-1">
                    {{ $file->additional_file3_name ?? 'Attachment 3' }}
                </a>
            @endif
        </div>
    </div>
        
    @if($recipientStatus === 'Pending Circulation')
            <div class="flex justify-center mb-4">
                <form method="POST" action="{{ route('registry.file-circulations.store') }}">
                    @csrf
                    <input type="hidden" name="file_id" value="{{ $file->id }}">
                    <input type="hidden" name="organisation_id" value="{{ auth()->user()->organisation_id }}"> 

                    <button type="submit"
                            class="bg-cyan-600 hover:bg-cyan-700 text-white px-10 py-2 rounded-md font-semibold">
                        Circulate to Review Officer
                    </button>
                </form>
            </div>
            {{-- <div class="mt-4 mb-6 flex justify-center">
                <a href="{{ route('registry.file-circulations.index') }}"
                    class="flex items-center gap-2 text-gray-600 hover:text-cyan-600">
                        ← Back
                </a>
            </div> --}}
    @elseif($recipientStatus === 'Pending Review' && $loggedInOrganisation->review_officer_id === Auth::id())
        <div class="max-w-2xl mx-auto bg-white shadow-sm rounded-lg p-6 border space-y-4">
            <form method="POST" action="{{ route('registry.file-circulations.store.assigned-officers', $fileCirculation) }}">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="file_id" value="{{ $file->id }}">
                                    {{-- <label for="assignedOfficers" class="mr-4 text-m">Assign officer(s)</label> --}}
                                    <select id="assignedOfficers"
                                            name="assignedOfficers[]"
                                            multiple
                                            class="w-full max-w-md px-8 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        @foreach ($usersWithDivision as $user)
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
        {{-- <div class="mt-4 mb-6 flex justify-center">
            <a href="{{ route('registry.file-circulations.review.index') }}"
            class="flex items-center gap-2 text-gray-600 hover:text-cyan-600">
                ← Back
            </a>
        </div> --}}
    @elseif($recipientStatus === 'Assigned' && $fileCirculation->assignedOfficers()->wherePivot('status', 'pending')->where('officer_id', Auth::id())->exists()) 
                    <div class="flex justify-center font-roboto text-lgt">
                        <div class="flex items-center justify-between mb-6">     
                            <form method="POST" action="{{ route('registry.file-circulations.store.complete', $fileCirculation) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="file_id" value="{{ $file->id }}">
                                <button type="submit"
                                    class="bg-green-600 hover:bg-green-900 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition duration-200">
                                    Mark as Received
                                </button>
                            </form>
                        </div>
                    </div>
    @elseif(Auth::user()->hasRole('registry'))
        <div class="mb-6 flex justify-center">
                <a href="{{ route('registry.file-circulations.index') }}"
                class="flex items-center gap-2 text-gray-600 hover:text-cyan-600">
                    ← Back
                </a>
            </div>
    @endif

        {{-- <div class="flex flex-col justify-center items-center gap-2 mt-2 mb-6"> --}}
                
                    {{-- <div class="flex font-roboto text-lgt justify-center items-center mt-6 mb-6">
                        <form method="POST" action="{{ route('registry.file-circulations.store.assigned-officers', $fileCirculation) }}">
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="file_id" value="{{ $file->id }}">
                            {{-- <label for="assignedOfficers" class="mr-4 text-m">Assign officer(s)</label> --}}
                            {{-- <select id="assignedOfficers"
                                    name="assignedOfficers[]"
                                    multiple
                                    class="w-full max-w-md px-8 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                @foreach ($usersWithDivision as $user)
                                    <option value="{{ $user->id }}"
                                        {{ in_array($user->id, old('assignedOfficers', [])) ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }}: 
                                        {{($user->division_name ?? 'No Division') }}
                                    </option>
                                @endforeach
                            </select>
                            <textarea name="review_comment" id="review_comment" rows="2" class="w-full border rounded p-2 focus:outline-none focus:ring" placeholder="Write your comment..."></textarea>
                            <button type="submit"
                                    class="w-full border rounded text-white bg-gradient-to-r from-cyan-400 via-cyan-500 to-cyan-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-base text-sm px-4 py-2.5 text-center leading-5">
                                Submit
                            </button> --}}
                        {{-- </form> --}}
                    {{-- </div>  --}}
               
        {{-- </div> --}}
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
</div>
@endsection