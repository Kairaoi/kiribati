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

{{-- <div class="container mx-auto font-poppins px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('dispatches.show', $file) }} </div> --}}
<div class="mx-w-5xl mx-auto">

     <div class="font-roboto mx-w-5xl px-4 sm:px-6 lg:px-8 mt-4 justify-center items-center">
        <section>
            <div class="metadata-card mx-auto">
                <h3 class="title">Dispatch File Metadata</h3>
                @php
                    $status = $file->status; // 'Dispatched' or 'Pending Dispatch'
                    $isDispatched = strcasecmp($status, 'Dispatched') === 0;

                    // Dark card colors (like your example)
                    $pill = $isDispatched
                        ? 'bg-emerald-600/20 text-emerald-300 ring-1 ring-emerald-400/30'
                        : 'bg-amber-500/20 text-amber-300 ring-1 ring-amber-400/30';
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
                <!-- RECIPIENT MINISTRIES -->
            <div class="metadata-section text-sm">
                <h4>Recipient Ministries | Status</h4>
                <div id="ministries-container">
                    @foreach($file->recipientMinistries as $index => $m)
                        <div class="grid grid-cols-2 border-b border-dashed border-white/30 py-2 
                            {{ $index >= 1 ? 'hidden extra-ministry' : '' }}">
                            <div class="text-sm">
                                {{ $m->name }} -> 
                            </div>
                            <div>
                                {{ ucfirst($m->pivot->status) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($file->recipientMinistries->count() > 1)
                    <button 
                        id="toggle-ministries"
                        class="mt-3 text-sm text-blue-200 hover:text-white flex items-center gap-1"
                    >
                        <span id="toggle-text">Show all</span>
                        <span id="toggle-arrow">▼</span>
                    </button>
                @endif
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

{{-- Stored as a Dispatch Instance when Dispatch button is clicked --}}
<div class="flex justify-center font-roboto text-lg border border-t">

        <form method="POST" action="{{ route('registry.dispatches.store') }}">
            @csrf

            <input type="hidden" name="file_id" value="{{ $file->id }}">
            <input type="hidden" name="from_organisation_id" value="{{ $file->organisation_id }}">
            <input type="hidden" name="from_division_id" value="{{ $file->division_id }}">
            <input type="hidden" name="dispatch_date" value="{{ now()->format('Y-m-d H:i:s') }}">

            @if($file->status === 'Pending Dispatch')
                <button type="submit" 
                    onclick="return confirm('Are you sure you want to dispatch this file?');"
                    class="w-72 mt-4 mb-8 inline-flex items-center justify-center bg-green-700 hover:bg-green-900 text-white font-semibold py-2 px-10 text-base rounded-xl shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <x-heroicon-o-paper-airplane class="w-5 h-5 -rotate-45" />
                    <span>DISPATCH FILE</span>
                </button>
            @endif

        </form>
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
        container.innerHTML = `<embed src="${pdfUrl}" type="application/pdf" width="100%" height="600px" />`;
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