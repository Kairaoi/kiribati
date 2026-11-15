@extends('layouts.app')

@section('content')
{{-- <div class="container mx-auto font-montserrat px-4 py-8 max-w-6xl mt-3 rounded-md min-h-screen"> --}}

{{-- <div class="container mx-auto font-poppins px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('files.create.withType', $createType) }} </div> --}}

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

<div class="bg-white shadow rounded-2xl p-6 max-w-2xl mx-auto mt-4 mb-4">
    <nav aria-label="Folder breadcrumb" class="mb-4">
            <ol class="flex flex-wrap items-center gap-2 text-sm">
                <li>
                    <a href="{{ route('registry.folders.index') }}" class="text-cyan-700 hover:underline">
                        E-Filing
                    </a>
                </li>

                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="flex items-center gap-2">
                        <span class="text-gray-400" aria-hidden="true">/</span>

                        <a
                            href="{{ route('registry.folders.show', $breadcrumb->uuid) }}"
                            class="text-cyan-700 hover:underline"
                        >
                            {{ $breadcrumb->name }}
                        </a>
                    </li>
                @endforeach

                <li class="flex items-center gap-2">
                    <span class="text-gray-400" aria-hidden="true">/</span>

                    <span class="text-gray-800" aria-current="page">
                        Edit {{ $folder->name ?? '' }}
                    </span>
                </li>
            </ol>
        </nav>

    <form action="{{ route('registry.folders.update', $folder->uuid) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="parent_id" value="{{ $parent?->id }}">        
           
            <div class="mb-5 relative">
                @if ($isSystemAdmin)
                    <div>
                        <label
                            for="ministry_id"
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Organisation
                        </label>

                        <select
                            name="ministry_id"
                            id="ministry_id"
                            required
                            class="mt-1 text-sm block w-full rounded-md border-gray-300
                                shadow-sm focus:border-cyan-500
                                focus:ring-cyan-500"
                        >
                            <option value="">Select organisation</option>

                            @foreach ($ministries as $ministry)
                                <option
                                    value="{{ $ministry->id }}"
                                    @selected(
                                        old('ministry_id') == $ministry->id
                                    )
                                >
                                    {{ $ministry->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('ministry_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @else
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Organisation
                        </label>

                        <input
                            type="text"
                            value="{{ $user->organisation->name }}"
                            readonly
                            class="mt-1 block text-sm w-full rounded-md border-gray-300
                                bg-gray-100 text-gray-600 shadow-sm focus:border-cyan-500
                                focus:ring-cyan-500"
                        >

                        <input
                            type="hidden"
                            id="ministry_id"
                            name="ministry_id"
                            value="{{ $user->organisation_id }}"
                        >
                    </div>
                @endif
            </div>
            
            <!-- Folder Name -->
            <div class="mb-5 relative">
                <label class="block text-sm font-medium text-gray-700">
                    Folder Name <span class="text-red-500">*</span>
                </label>

                <input type="text" name="name" id="folder_name"
                    value="{{ old('name', $folder->name ?? '') }}"
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm 
                        focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                        text-sm px-3 py-2"
                    placeholder="e.g. Workshop"
                    autocomplete="off"
                    required>

                <div id="name-suggestions" class="absolute z-10 w-full bg-white border rounded-md mt-1 hidden shadow">
                </div>
                <p id="name-warning" class="text-sm text-red-600 mt-1 hidden">
                    This folder already exists.
                </p>
            </div>

            <!-- Description -->
            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700">
                    Description 
                </label>
                <input type="text" name="description" id="description"
                    value="{{ old('description', $folder->description ?? '') }}"
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm 
                        focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                        text-sm px-3 py-2"
                    placeholder="Short description of this file type">
            </div>
          

            <!-- Divider -->
            <div class="border-t border-gray-200 pt-5 flex gap-3">
                <button type="submit"
                    class="px-5 py-3 text-sm font-medium rounded-lg 
                        bg-cyan-600 text-white hover:bg-cyan-700 
                        focus:ring-2 focus:ring-cyan-300">
                    Update Folder
                </button>
            </div>
        </div>
    </form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const input = document.getElementById('file_type_name');
        const box = document.getElementById('name-suggestions');
        const warning = document.getElementById('name-warning');

        const suggestionUrl = "{{ route('registry.file-types.name.suggestions') }}";

        let currentSuggestions = [];

        if (!input || !box || !warning) return;

        function checkDuplicate(value) {
            const exists = currentSuggestions.some(
                name => name.toLowerCase() === value.toLowerCase()
            );

            if (exists) {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }

        input.addEventListener('input', function () {

            let query = this.value.trim();

            checkDuplicate(query);

            if (query.length < 2) {
                box.classList.add('hidden');
                warning.classList.add('hidden');
                return;
            }

            fetch(`${suggestionUrl}?q=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(data => {

                currentSuggestions = data; // store for duplicate check

                box.innerHTML = '';

                if (!data.length) {
                    box.classList.add('hidden');
                    return;
                }

                box.classList.remove('hidden');

                // Header
                const header = document.createElement('div');
                header.className = "px-2 py-1 text-xs text-gray-500 border-b";
                header.textContent = "Existing file-types:";
                box.appendChild(header);

                data.forEach(name => {

                    const isExact = name.toLowerCase() === query.toLowerCase();

                    const item = document.createElement('div');
                    item.className = `
                        p-2 cursor-pointer text-sm
                        ${isExact ? 'bg-red-50 text-red-600 font-medium' : 'hover:bg-gray-100'}
                    `;
                    item.textContent = name;

                    item.addEventListener('click', () => {
                        input.value = name;
                        box.classList.add('hidden');

                        // show warning immediately
                        warning.classList.remove('hidden');
                    });

                    box.appendChild(item);
                });

                // re-check after fetch
                checkDuplicate(query);
            })
            .catch(() => {
                box.classList.add('hidden');
            });
        });

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const input = document.getElementById('code');
        const box = document.getElementById('code-suggestions');
        const warning = document.getElementById('code-warning');

        const suggestionUrl = "{{ route('registry.file-types.code.suggestions') }}";

        let currentSuggestions = [];

        if (!input || !box || !warning) return;

        function checkDuplicate(value) {
            const exists = currentSuggestions.some(
                name => name.toLowerCase() === value.toLowerCase()
            );

            if (exists) {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }

        input.addEventListener('input', function () {

            let query = this.value.trim();

            checkDuplicate(query);

            if (query.length < 2) {
                box.classList.add('hidden');
                warning.classList.add('hidden');
                return;
            }

            fetch(`${suggestionUrl}?q=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(data => {

                currentSuggestions = data; // store for duplicate check

                box.innerHTML = '';

                if (!data.length) {
                    box.classList.add('hidden');
                    return;
                }

                box.classList.remove('hidden');

                // Header
                const header = document.createElement('div');
                header.className = "px-2 py-1 text-xs text-gray-500 border-b";
                header.textContent = "Existing file-types:";
                box.appendChild(header);

                data.forEach(name => {

                    const isExact = name.toLowerCase() === query.toLowerCase();

                    const item = document.createElement('div');
                    item.className = `
                        p-2 cursor-pointer text-sm
                        ${isExact ? 'bg-red-50 text-red-600 font-medium' : 'hover:bg-gray-100'}
                    `;
                    item.textContent = name;

                    item.addEventListener('click', () => {
                        input.value = name;
                        box.classList.add('hidden');

                        // show warning immediately
                        warning.classList.remove('hidden');
                    });

                    box.appendChild(item);
                });

                // re-check after fetch
                checkDuplicate(query);
            })
            .catch(() => {
                box.classList.add('hidden');
            });
        });

    });
</script>
@endpush
@endsection


