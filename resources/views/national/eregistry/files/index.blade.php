@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-6 px-4 sm:px-6 lg:px-8 mt-4">

    {{-- MAIN ARCHIVE --}}
    <div class="md:col-span-3 bg-blue-50 text-blue-700 border border-blue-200 p-4 rounded-md">
       
        <div class="mb-3 relative">
            <input type="text" id="fileSearch"
                placeholder="Search by keyword..."
                class="w-full pl-10 pr-3 py-2 border border-gray-300 text-m focus:outline-none focus:ring-2 focus:ring-blue-400">

            {{-- Search icon --}}
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/>
            </svg>
        </div>

        <h3 class="text-m tracking-widest font-semibold text-slate-700 mt-10 mb-3">
            ARCHIVED FILES (by month)
        </h3>

        <div id="fileContainer" class="mt-6 space-y-3">
            @foreach ($monthlyArchives as $year => $months)
                {{-- <h3 class="text-lg font-semibold text-blue-700 mt-3">
                   {{ $year }} 
                </h3> --}}
                <div class="list-disc">
                    @foreach ($months as $item)
                        <h3 class="text-lg font-semibold text-blue-700 mt-3">
                            {{ $year }}
                            {{ \Carbon\Carbon::create()->month($item->month)->format('F') }}
                            {{-- ({{ $item->total }}) --}}
                        </h3>
                            @foreach($files as $file)
                                <div class="ml-4 border-b py-2 flex items-center justify-between hover:bg-blue-100 rounded-md transition">
                                    {{-- LEFT: File info --}}
                                    <div>
                                        <div class="font-semibold text-m text-slate-600 truncate max-w-md">
                                            {{ $file->file_subject }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $file->organisation_name }} • 
                                            {{ \Carbon\Carbon::parse($file->archived_date)->format('d M Y') }}
                                        </div>
                                    </div>

                                    {{-- RIGHT: Actions --}}
                                    <div class="flex space-x-2 text-sm">

                                        {{-- VIEW --}}
                                        <a href="{{ route('registry.files.view', $file->file_id) }}"
                                        class="px-2 py-1 text-blue-600 hover:text-blue-800 hover:underline">
                                            View
                                        </a>

                                        {{-- DOWNLOAD --}}
                                        <a href="{{ route('registry.files.download.main', $file->file_id) }}"
                                        class="px-2 py-1 text-green-600 hover:text-green-800 hover:underline">
                                            Download
                                        </a>

                                        {{-- RESTORE --}}
                                        <a href="{{ route('registry.files.view', $file->file_id) }}"
                                        class="px-2 py-1 text-red-600 hover:text-red-800 hover:underline">
                                            Restore
                                        </a>

                                    </div>
                                </div>
                            @endforeach
                
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- SIDEBAR --}}
    <aside class="bg-white border border-blue-200 p-4 rounded-md text-sm text-blue-800">
        <h3 class="text-lg tracking-widest font-semibold text-blue-700 mb-3">
            Organisations
        </h3>

        <ul class="space-y-1">
            <li>
                {{-- <a href="#"
                   class="category-link hover:text-blue-900 hover:underline"
                   data-category="">
                    All Organisations
                </a> --}}
            </li>

              {{-- FILE LIST --}}
            <div id="fileResults" class="mt-6 space-y-3">
                @foreach($organisations as $org)
                    <div class="border-b pb-2">
                        <a href="#"
                           class="org-link hover:text-blue-900 hover:underline"
                           data-org="{{ $org->id }}">
                            {{ $org->name }} ({{ $org->total }})
                        </a>
                    </div>
                @endforeach
            </div>
        </ul>
    </aside>
</div>

    @push('styles')
    <style>
        .active-filter {
            @apply text-blue-900 font-bold underline;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        $('.archive-link').on('click', function(){
        $('.archive-link').removeClass('active-filter');
        $(this).addClass('active-filter');
    });

    $('.org-link').on('click', function(){
        $('.org-link').removeClass('active-filter');
        $(this).addClass('active-filter');
    });

    </script>

    <script>
        document.querySelectorAll('.org-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const orgId = this.dataset.org;

                fetch(`/archives/by-organisation/${orgId}`)
                    .then(res => res.json())
                    .then(files => {

                        let html = '';

                        if (files.length === 0) {
                            html = '<p class="text-gray-500">No files for this organisation.</p>';
                        } else {
                            files.forEach(file => {
                                html += `
                                    <div class="border p-2 rounded mb-2">
                                        <strong>${file.name}</strong><br>
                                        <span class="text-sm text-gray-500">${file.archived_date}</span>
                                    </div>
                                `;
                            });
                        }

                        // 🔥 THIS replaces the default files
                        document.getElementById('filesContainer').innerHTML = html;
                    });
            });
        });
    </script>
    @endpush

@endsection
