@extends('layouts.app')

@section('content')
    {{-- <div class="container mx-auto font-poppins px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('dispatches.show', $file) }} </div> --}}
    <div class="container mx-w-5xl mx-auto">
        @php
                        $status = $file->status; // 'Dispatched' or 'Pending Dispatch'
                        $isDispatched = strcasecmp($status, 'Dispatched') === 0;

                        // Dark card colors (like your example)
                        $pill = $isDispatched
                            ? 'bg-emerald-300 text-white ring-1 ring-emerald-400/30'
                            : 'bg-amber-100 ring-1 ring-amber-500/50';
        @endphp

        <div class="mx-auto mt-2 bg-white justify-center border border-gray-200 rounded-xl shadow-sm p-5 max-w-3xl">
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

            <div class="grid grid-cols-1 mt-4 gap-y-4 gap-x-8">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">Recipient Ministries | Status</p>
                        <div id="ministries-container">
                            @foreach($file->recipientMinistries as $index => $m)
                                <div class="py-3 px-4 rounded-lg bg-white/60 backdrop-blur-sm mb-1
                                    {{ $index >= 1 ? 'hidden extra-ministry' : '' }}">

                                    {{-- Ministry + Status --}}
                                    <div class="flex items-center justify-between">
                                        <p class="text-gray-900 font-semibold">
                                            {{ $m->name }}
                                        </p>
                                        @if($m->pivot->status === 'Pending Review' || $m->pivot->status === 'Reviewed')
                                            <span class="text-xs px-2 py-1 rounded-full 
                                                {{ $m->pivot->status === 'Pending Review' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $m->pivot->status === 'Reviewed' ? 'bg-green-100 text-green-800' : '' }}">
                                                {{ ucfirst($m->pivot->status) }}
                                            </span>
                                        @endif
                                    </div>

                                    @php
                                        $circulation = $fileCirculations
                                            ->where('to_organisation_id', $m->id)
                                            ->first();
                                    @endphp

                                    {{-- Officers --}}
                                    @if($circulation && $circulation->activeAssignments->isNotEmpty())
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-500 mb-1">Assigned officers</p>

                                            <div class="flex flex-wrap gap-2">
                                                @foreach($circulation->activeAssignments as $assignment)
                                                    @if($assignment->officer)
                                                        <span class="text-xs px-2 py-1 rounded-full bg-cyan-100 text-cyan-800">
                                                            {{ $assignment->officer->first_name }} {{ $assignment->officer->last_name }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
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
    
        <div class="mx-auto mt-2 mb-4 max-w-3xl px-6 py-2">
            <form method="POST" action="{{ route('registry.dispatches.store') }}">
                    @csrf
                    <input type="hidden" name="file_id" value="{{ $file->id }}">
                    <input type="hidden" name="from_organisation_id" value="{{ $file->organisation_id }}">
                    <input type="hidden" name="from_division_id" value="{{ $file->division_id }}">
                    <input type="hidden" name="dispatch_date" value="{{ now()->format('Y-m-d H:i:s') }}">

                    @if($file->status === 'Pending Dispatch')
                        <button type="submit" 
                            onclick="return confirm('Are you sure you want to dispatch this file?');"
                            class="flex w-full bg-cyan-600 hover:bg-cyan-700 items-center justify-center text-white px-10 py-2 rounded-md font-semibold">
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