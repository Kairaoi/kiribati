@extends('layouts.app')

@section('content')

<style>
    .metadata-card {
        background: #0f4c75;
        color: #fff;
        padding: 20px;
        border-radius: 12px;
        width: 100%;
        max-width: 700px;
    }

    .metadata-card .title {
        text-align: center;
        margin-bottom: 15px;
    }

    .metadata-section {
        margin-bottom: 15px;
    }

    .metadata-section h4 {
        margin-bottom: 8px;
        color: #b8f1ff;
    }

    .metadata-grid {
        display: grid;
        grid-template-columns: 200px 1fr;
        border: 1px dashed rgba(255,255,255,0.3);
    }

    .meta-label {
        padding: 8px;
        border-bottom: 1px dashed rgba(255,255,255,0.2);
        font-weight: bold;
    }

    .meta-value {
        padding: 8px;
        border-bottom: 1px dashed rgba(255,255,255,0.2);
        background: rgba(255,255,255,0.08);
    }
</style>


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

@if (Auth::user()->hasRole('registry') || ($recipientStatus != 'Pending Review' && $recipientStatus != 'Assigned'))
    <div class="container mx-auto font-poppins px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('circulations.show', $file) }} </div>

@endif
<div class="mx-w-5xl mx-auto">

     <div class="font-roboto mx-w-5xl px-4 sm:px-6 lg:px-8 mt-4 justify-center items-center">
        <section>
            <div class="metadata-card mx-auto">
                <h3 class="title">File Circulation Metadata</h3>
                @php
                    // Normalise for comparisons
                    $normalizedStatus = strtolower($recipientStatus);

                    // 4. Decide pill style + display label based on status
                    $status = $recipientStatus; // default

                    switch ($normalizedStatus) {
                        case 'pending circulation':
                            $pill = 'bg-slate-500/20 text-slate-300 ring-1 ring-slate-400/30';
                            break;

                        case 'pending review':
                            // $pill = 'bg-sky-500/20 text-sky-300 ring-1 ring-sky-400/30';
                            $pill = 'bg-amber-500/20 text-amber-300 ring-1 ring-amber-400/30';
                            break;

                        case 'assigned':
                            // Shorter display label, but keep full value in DB
                            $status = 'Assigned';
                            $pill = 'bg-sky-500/20 text-sky-300 ring-1 ring-sky-400/30';
                            break;

                        default:
                            // Fallback style
                            $pill = 'bg-slate-600/20 text-slate-200 ring-1 ring-slate-400/30';
                            break;
                    }
                @endphp

            <!-- FILE DETAILS -->
            <div class="metadata-section text-sm">
                <h4>File Details</h4>
                <div class="metadata-grid">
                    <div class="meta-label">Name:</div>
                    <div class="meta-value">{{ $file->name ?? 'Better Man' }}</div>

                    <div class="meta-label">File Status:</div>
                    <div class="meta-value">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $pill }}">
                            {{ $status }}
                        </span>
                    </div>

                    <div class="meta-label">Organisation:</div> 
                    <div class="meta-value">{{ $file->organisation->name ?? 'N/A' }}</div>

                    @isset($file->division)
                        <div class="meta-label">Division:</div>
                        <div class="meta-value">{{ $file->division->name ?? 'N/A' }}</div>
                    @endisset

                    <div class="meta-label">File Type:</div>
                    <div class="meta-value">{{ $file->fileType->name ?? 'N/A' }}</div>

                    <div class="meta-label">Date:</div>
                    <div class="meta-value">{{ $file->created_at->format('d/m/Y') ?? '1994' }}</div>
                </div>
            </div>
        </section>
    </div>

    <!-- Container for PDF -->
    <div id="pdf-container" class="mt-5 flex font-roboto mx-auto px-4 sm:px-6 lg:px-8 mt-4 justify-center items-center" style="display:none;">
    <!-- PDF will be injected here -->
    </div>

    <div class="flex justify-center mt-6">
        <button id="view-file-btn" class="w-72 bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
            <span>View File</span>
        </button>
    </div>

</div>
    <div class="flex justify-center">
        @if($recipientStatus === 'Pending Circulation')
                {{-- if file is internal, then it needs to be circulated by registry --}}
                @if($file->initial_type === "internal")
                    <div class="flex items-center justify-between mt-6 mb-6">     
                        <form method="POST" action="{{ route('registry.file-circulations.store') }}">
                            @csrf
                            <input type="hidden" name="file_id" value="{{ $file->id }}">
                            <input type="hidden" name="organisation_id" value="{{ auth()->user()->organisation_id }}">
                            
                            <button type="submit"
                                class="bg-blue-600 text-white px-10 py-3 rounded-xl shadow hover:bg-blue-700 transition">
                                Circulate File to {{ $fileCirculation->toReviewFile ? $fileCirculation->toReviewFile->first_name . ' ' . $fileCirculation->toReviewFile->last_name : 'N/A' }}
                            </button>
                        </form>
                    </div>
                @endif
            
        @elseif($recipientStatus === 'Pending Review' && $loggedInOrganisation->review_officer_id === Auth::id())
            <div class="flex justify-left font-roboto text-lgt">
                <div class="flex items-center justify-between mt-6 mb-10">     
                    <form method="POST" action="{{ route('registry.file-circulations.store.assigned-officers', $fileCirculation) }}">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="file_id" value="{{ $file->id }}">
                        <label for="assignedOfficers" class="mr-4 text-lg">Assign officers to deal with file:</label>

                        <select id="assignedOfficers"
                                name="assignedOfficers[]"
                                multiple
                                class="form-control">

                            @foreach ($usersWithDivision as $user)
                                <option value="{{ $user->id }}"
                                    {{ in_array($user->id, old('assignedOfficers', [])) ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                    - {{ $user->division_name ?? 'No Division' }}
                                </option>
                            @endforeach
                        </select>
                        <div id="selectedOfficers" class="mt-2"></div>
                        <button type="submit"
                            class="flex mt-2 ml-4 bg-blue-600 hover:bg-blue-900 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition duration-200">
                            Submit
                        </button>
                    </form>
                </div>
                <br>
            </div>
        @elseif($recipientStatus === 'Assigned' && $fileCirculation->assignedOfficers()->wherePivot('status', 'pending')->where('officer_id', Auth::id())->exists()) 

            <div class="flex justify-center font-roboto text-lgt">
                <div class="flex items-center justify-between mt-6 mb-6">     
                    <form method="POST" action="{{ route('registry.file-circulations.store.complete', $fileCirculation) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="file_id" value="{{ $file->id }}">
                        <button type="submit"
                            class=" bg-green-600 hover:bg-green-900 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition duration-200">
                            Mark as Completed
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

</div>
   
</div>
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
        container.innerHTML = `<embed src="${pdfUrl}" type="application/pdf" width="100%" height="600px">`;
    });
</script>
@endsection