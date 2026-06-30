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

    <form action="{{ route('registry.file-types.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
 
        <div class="bg-white shadow-sm font-poppins rounded-2xl p-6 max-w-2xl mx-auto mt-4 mb-4">

            <h2 class="text-m font-semibold text-gray-800 mb-6">
                <span class="text-cyan-600">Create your Ministry File Type</span>
            </h2>

            <!-- File Type Name -->
            <div class="mb-5 relative">
                <label class="block text-sm font-medium text-gray-700">
                    File Type Name <span class="text-red-500">*</span>
                </label>

                <input type="text" name="name" id="file_type_name"
                    value="{{ old('name') }}"
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm 
                        focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                        text-sm px-3 py-2"
                    placeholder="e.g. Workshop"
                    autocomplete="off"
                    required>

                <div id="name-suggestions" class="absolute z-10 w-full bg-white border rounded-md mt-1 hidden shadow">
                </div>
                <p id="name-warning" class="text-sm text-red-600 mt-1 hidden">
                    This file type already exists.
                </p>
            </div>
            

            <!-- Description -->
            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700">
                    Description <span class="text-gray-400">(optional)</span>
                </label>
                <input type="text" name="description" id="description"
                    value="{{ old('description') }}"
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm 
                        focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                        text-sm px-3 py-2"
                    placeholder="Short description of this file type">
            </div>

            <!-- Code -->
            <div class="mb-5 relative">
                <label for="code" class="block text-sm font-medium text-gray-700">
                    Code <span class="text-red-500">*</span>
                </label>

                <!-- Helper text -->
                <p class="text-xs text-gray-500 mt-1">
                    Use a unique 2 or 3-letter code to identify this file type (e.g. <span class="font-medium">WKS</span> for Workshop, 
                    <span class="font-medium">LTR</span> for Letter).
                </p>

                <input type="text" name="code" id="code"
                    value="{{ old('code') }}"
                    maxlength="3"
                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                        focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                        text-sm px-3 py-2"
                    placeholder="e.g. WKS or WK"
                    required>

                <div id="code-suggestions" 
                    class="absolute z-10 w-full bg-white border rounded-md mt-1 hidden shadow">
                </div>
                <p id="code-warning" class="text-sm text-red-600 mt-1 hidden">
                    This code already exists.
                </p>

                <!-- Optional validation error -->
                @error('code')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 pt-5 flex justify-center gap-3">
                <button type="submit"
                    class="px-5 py-2 text-sm font-medium rounded-lg 
                        bg-cyan-600 text-white hover:bg-cyan-700 
                        focus:ring-2 focus:ring-cyan-300">
                    Create File Type
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


