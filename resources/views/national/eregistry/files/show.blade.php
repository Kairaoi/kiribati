{{-- @extends('layouts.app')

{{-- @section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-center text-2xl font-bold text-gray-900 text-centre">View File: {{ $file->name }}</h1>
        <a href="{{ route('registry.files.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md transition duration-150 ease-in-out">
            <span>Back to Files</span>
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-base">

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">From Division:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    {{ optional($file->division)->name ?? 'No division specified' }}
                </dd>
            </div>
            
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">File Name:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $file->name }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">File Type:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $file->fileType->name }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">File Date: </dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $file->created_at->format('M d, Y') }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">File Status:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    @if ($file->status === 'Pending Dispatch')
                        <span class="inline-block px-3 py-1 bg-gray-600 text-white text-xs font-semibold rounded-full">
                            {{ $file->status }}
                        </span>
                    @elseif ($file->status === 'Dispatched')
                        <span class="inline-block px-3 py-1 bg-green-600 text-white text-xs font-semibold rounded-full">
                            {{ $file->status }}
                        </span>
                    @else
                        <span class="inline-block px-3 py-1 bg-gray-400 text-white text-xs font-semibold rounded-full">
                            {{ $file->status }}
                        </span>
                    @endif
                </dd>
            </div>
            
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Recipient Ministries:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    @if ($file->recipientMinistries->isNotEmpty())
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($file->recipientMinistries as $organisation)
                                @php
                                    $status = $organisation->pivot->status ?? 'No Status';
                                    $statusClass = match ($status) {
                                        'Dispatched' => 'text-green-900',
                                        'Circulated' => 'text-green-600',
                                        'Assigned To Officer' => 'text-blue-600',
                                        'Completed' => 'text-gray-600',
                                        default => 'text-gray-600 font-bold',
                                    };
                                @endphp
                                <li>
                                    {{ $organisation->name }} ({{ $organisation->code }}) <br>
                                    Status: <span class=" text-base {{ $statusClass }}">
                                         {{ $status }}
                                    </span>
                                </li>
                                <br>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-gray-500">No recipient organisations assigned.</span>
                    @endif
                </dd>
            </div>
            
        </dl>
    </div>

    <div class="pdf-viewer bg-white rounded-lg shadow-md p-2">
        @php
            $extension = strtolower(pathinfo($file->main_file_path, PATHINFO_EXTENSION));
            $mainFileUrl = route('registry.files.view', ['id' => $file->id]);
        @endphp

        @if($extension === 'pdf')
            <embed src="{{ $mainFileUrl }}" type="application/pdf" width="100%" height="600px">
        @else
            <p>Main file cannot be previewed.</p>
        @endif

        <div class="mt-4">
            <h3 class="font-semibold">Download Additional Files:</h3>
            <ul class="list-disc pl-5">
                @for ($i = 1; $i <= 3; $i++)
                    @php
                        $field = 'additional_file' . $i . '_path';
                    @endphp
                    @if (!empty($file->$field))
                        <li>
                            <a href="{{ route('registry.files.download.additional', ['id' => $file->id, 'number' => $i]) }}"
                            class="text-blue-500 hover:underline">
                                Download Additional File {{ $i }}
                            </a>
                        </li>
                    @endif
                @endfor
            </ul>
        </div>
    </div>


    {{-- Stored as a Dispatch Instance when Dispatch button is clicked --}}
    {{-- <div class="flex justify-center mt-8">
        <form method="POST" action="{{ route('registry.dispatches.store') }}">
            @csrf

            <input type="hidden" name="file_id" value="{{ $file->id }}">
            <input type="hidden" name="from_organisation_id" value="{{ $file->organisation_id }}">
            <input type="hidden" name="from_division_id" value="{{ $file->division_id }}">
            <input type="hidden" name="dispatch_date" value="{{ now()->format('Y-m-d H:i:s') }}">

            <button type="submit"
                onclick="return confirm('Are you sure you want to dispatch this file?');"
                class="inline-flex items-center justify-center bg-blue-900 hover:bg-blue-700 text-white font-semibold py-2 px-6 text-base rounded-md shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed"
                {{ $file->status !== 'Pending Dispatch' ? 'disabled' : '' }}>
                
                Dispatch File
            </button>
        </form>
    </div>

@endsection --}}

@section('content')
<div class="mx-w-5xl mx-auto">

     <div class="font-roboto max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <section>
            <!-- Card -->
            <div class="rounded-lg bg-slate-800 text-white shadow-xl ring-1 ring-black/10 p-6 sm:p-8">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-semibold tracking-tight">File Details</h2>
                    <p class="text-slate-300 text-sm mt-1">Metadata and current status</p>
                </div>

                <!-- Status pill -->
                @php
                    $status = $file->status; // 'Dispatched' or 'Pending Dispatch'
                    $isDispatched = strcasecmp($status, 'Dispatched') === 0;

                    // Dark card colors (like your example)
                    $pill = $isDispatched
                        ? 'bg-emerald-600/20 text-emerald-300 ring-1 ring-emerald-400/30'
                        : 'bg-amber-500/20 text-amber-300 ring-1 ring-amber-400/30';
                @endphp

                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $pill }}">
                {{ $status }}
                </span>

                </div>

                <!-- Body -->
                <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                {{-- <div>
                    <dt class="text-slate-300 text-sm">File Name</dt>
                    <dd class="mt-1 font-medium">{{ $file->name }}</dd>
                </div> --}}

                <div>
                    <dt class="text-slate-300 text-sm">From</dt>
                    <dd class="mt-1 font-medium">{{ $file->organisation->name }}</dd>
                </div>

                @isset($file->division)
                    <div>
                        <dt class="text-slate-300 text-sm">Division</dt>
                        <dd class="mt-1 font-medium">{{ $file->division->name }}</dd>
                    </div>
                @endisset
                

                <div>
                    <dt class="text-slate-300 text-sm">Type</dt>
                    <dd class="mt-1 font-medium">{{ $file->fileType->name }}</dd>
                </div>

                <div>
                    <dt class="text-slate-300 text-sm">Date</dt>
                    <dd class="mt-1 font-medium">{{ $file->created_at->format('M d, Y') }}</dd>
                </div>

                <div>
                    <dt class="text-slate-300 text-sm">Recipient Ministries</dt>
                    <dd class="mt-1 font-medium flex flex-wrap gap-2">
                        @forelse ($file->recipientMinistries as $m)
                        <span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 px-2 py-0.5 text-xs font-medium">
                            {{ $m->name }} -> {{ $m->pivot->status }}
                        </span>
                        @empty
                        —
                        @endforelse
                    </dd>
                </div>
            </div>
        </section>
    </div>
    
    <div class="font-roboto max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @php
            $extension = strtolower(pathinfo($file->main_file_path, PATHINFO_EXTENSION));
            $mainFileUrl = route('registry.files.view', ['id' => $file->id]);
        @endphp

        @if($extension === 'pdf')
            <embed src="{{ $mainFileUrl }}" type="application/pdf" width="100%" height="600px">
        @else
            <p>Main file cannot be previewed.</p>
        @endif

        <div class="mt-4">
            <h3 class="font-semibold">Download Additional Files:</h3>
            <ul class="list-disc pl-5">
                @for ($i = 1; $i <= 3; $i++)
                    @php
                        $field = 'additional_file' . $i . '_path';
                    @endphp
                    @if (!empty($file->$field))
                        <li>
                            <a href="{{ route('registry.files.download.additional', ['id' => $file->id, 'number' => $i]) }}"
                            class="text-blue-500 hover:underline">
                                Download Additional File {{ $i }}
                            </a>
                        </li>
                    @endif
                @endfor
            </ul>
        </div>
    </div>

</div>
@endsection --}}