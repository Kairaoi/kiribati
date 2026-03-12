@extends('layouts.app')

@section('content')
    <style>
            .metadata-card {
                background: #0f4c75;
                color: #fff;
                padding: 20px;
                border-radius: 10px;
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

    {{-- <div class="container mx-auto font-poppins px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('dispatches.show', $file) }} </div> --}}
    <div class="mx-w-5xl mx-auto">
                    @php
                        $status = $file->status; // 'Dispatched' or 'Pending Dispatch'
                        $isDispatched = strcasecmp($status, 'Dispatched') === 0;

                        // Dark card colors (like your example)
                        $pill = $isDispatched
                            ? 'bg-emerald-300 text-white ring-1 ring-emerald-400/30'
                            : 'bg-amber-100 ring-1 ring-amber-500/50';
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
            </div>

            <div class="grid grid-cols-1 mt-4 gap-y-4 gap-x-8">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">Recipient Ministries | Status</p>
                        <div id="ministries-container">
                            @foreach($file->recipientMinistries as $index => $m)
                                <div class="border-b border-dashed border-white/30 py-2 
                                    {{ $index >= 1 ? 'hidden extra-ministry' : '' }}">
                                    <p class="text-gray-900 font-semibold">
                                        {{ $m->name }} : {{ ucfirst($m->pivot->status) }}
                                    </p>
                                    @php
                                        $circulation = $fileCirculations
                                            ->where('to_organisation_id', $m->id)
                                            ->first();
                                    @endphp

                                    @if($circulation && $circulation->assignedOfficers->isNotEmpty())
                                        <p class="text-sm text-gray-600 mt-1">Officers assigned:</p>
                                        @foreach($circulation->assignedOfficers as $officer)
                                           <span class="inline-block mr-2 text-sm text-gray-700"> {{ $officer->first_name }} {{ $officer->last_name }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                            @if($file->recipientMinistries->count() > 1)
                                <button 
                                    id="toggle-ministries"
                                    class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1"
                                >
                                    <span id="toggle-text">Show all</span>
                                    <span id="toggle-arrow">▼</span>
                                </button>
                            @endif
                        </div>
                </div>
            </div>
        </div>

    {{-- <div class="px-6 py-4 flex justify-center"> --}}
        {{-- <a href="#" id="view-file-btn" class="flex justify-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-md shadow">
            📄 View File
        </a> --}}

        {{-- Additional file 1 --}}
        {{-- @if($file->additional_file1_path)
            <a href="{{ asset('storage/'.$file->additional_file1_path) }}" target="_blank" class="text-cyan-700 hover:underline">
                Additional File 1
            </a>
        @endif --}}

        {{-- Additional file 2 --}}
        {{-- @if($file->additional_file2_path)
            <a href="{{ asset('storage/'.$file->additional_file2_path) }}" target="_blank" class="text-cyan-700 hover:underline">
                Additional File 2
            </a>
        @endif --}}

        {{-- Additional file 3 --}}
        {{-- @if($file->additional_file3_path)
            <a href="{{ asset('storage/'.$file->additional_file3_path) }}" target="_blank" class="text-cyan-700 hover:underline">
                Additional File 3
            </a>
        @endif --}}
    {{-- </div> --}}
   
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

         {{-- Dispatch Button / Form --}}
        <div class="px-6 py-2 flex justify-center">    
            <form method="POST" action="{{ route('registry.dispatches.store') }}">
                @csrf
                <input type="hidden" name="file_id" value="{{ $file->id }}">
                <input type="hidden" name="from_organisation_id" value="{{ $file->organisation_id }}">
                <input type="hidden" name="from_division_id" value="{{ $file->division_id }}">
                <input type="hidden" name="dispatch_date" value="{{ now()->format('Y-m-d H:i:s') }}">

                @if($file->status === 'Pending Dispatch')
                    <button type="submit" 
                        onclick="return confirm('Are you sure you want to dispatch this file?');"
                        class="flex bg-cyan-600 hover:bg-cyan-700 text-white px-10 py-2 rounded-md font-semibold">
                        Dispatch File
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                            viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 12l18-9-4 18-5-5-6 4 2-6z"/>
                        </svg>
                    </button>
                @endif
            </form>
    </div>
    </div>


    {{-- Stored as a Dispatch Instance when Dispatch button is clicked --}}
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
            container.innerHTML = `<embed src="${pdfUrl}" type="application/pdf" width="70%" height="700px" />`;
        });
    </script>

    <script>
        document.getElementById('toggle-ministries')?.addEventListener('click', function () {
            const extras = document.querySelectorAll('.extra-ministry');
            const text = document.getElementById('toggle-text');
            const arrow = document.getElementById('toggle-arrow');

            extras.forEach(el => el.classList.toggle('hidden'));

            if (text.innerText === 'Show all') {
                text.innerText = 'Show less';
                arrow.innerText = '▲';
            } else {
                text.innerText = 'Show all';
                arrow.innerText = '▼';
            }
        });
    </script>
@endsection