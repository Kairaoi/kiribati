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

    <form action="{{ route('registry.organisations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
    
        <div class="bg-white shadow-sm rounded-2xl p-6 max-w-4xl mx-auto mt-4 mb-4">

            <h2 class="text-m font-semibold text-gray-800 mb-6">
                <span class="text-cyan-600">Create organisation</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="mb-3 relative">
                    <label class="block text-sm font-medium text-gray-700">
                        Name <span class="text-red-500">*</span>
                    </label>

                    <input type="text" name="name" id="name"
                        value="{{ old('name') }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                            focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                            text-sm px-3 py-2"
                        required>
                </div>

                <!-- Code -->
                <div class="mb-3 relative">
                    <label for="code" class="block text-sm font-medium text-gray-700">
                        Code <span class="text-red-500">*</span>
                    </label>

                    <input type="text" name="code" id="code"
                        value="{{ old('code') }}"
                        maxlength="5"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                            focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                            text-sm px-3 py-2"
                        @readonly(!auth()->user()->hasRole('system-admin'))
                        required>

                    <!-- Optional validation error -->
                    @error('code')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3 relative">
                    <label for="address" class="block text-sm font-medium text-gray-700">
                        Reviewer Title
                    </label>

                    <input type="text" 
                        name="reviewer_title" 
                        id="reviewer_title"
                        value="{{ old('reviewer_title') }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                                focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                                text-sm px-3 py-2"
                        placeholder="e.g. Secretary or Chairperson"
                        >
                    
                </div>

                 <div class="mb-3 relative">
                    <label class="block text-sm font-medium text-gray-700">
                    Organisation Type <span class="text-red-600">*</span>
                    </label>

                    <select name="organisation_type_id" id="organisation_type_id"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2"
                    >

                        <option value="">-- None --</option>
                        @foreach($organisationTypes as $type)
                            <option value="{{ $type->id }}"
                                @selected(old('organisation_type_id'))>
                                {{ $type->name }} 
                            </option>
                        @endforeach
                    </select> 
                </div>

                <div class="mb-3 relative">
                    <label for="address" class="block text-sm font-medium text-gray-700">
                        Address
                    </label>
                    <input type="text" name="address" id="address"
                        value="{{ old('address') }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                            focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                            text-sm px-3 py-2"
                        >
                </div>

                <div class="mb-3 relative">
                    <label for="po_box" class="block text-sm font-medium text-gray-700">
                        PO Box Number
                    </label>

                    <input type="text" name="po_box" id="po_box"
                            value="{{ old('po_box') }}"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                                focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                                text-sm px-3 py-2"
                            placeholder="e.g. 68"
                            >
                </div>

                <div class="mb-3 relative">
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Phone Number
                        </label>

                        <input type="tel" name="phone" id="phone"
                            value="{{ old('phone') }}"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                                focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                                text-sm px-3 py-2"
                            placeholder="e.g. 74026003"
                            >
                </div>

                <div class="mb-3 relative">
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email
                        </label>

                        <input type="email" name="email" id="email"
                            value="{{ old('email') }}"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                                focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                                text-sm px-3 py-2"
                            >
                </div>

                <div class="mb-3 relative">
                        <label for="website" class="block text-sm font-medium text-gray-700">
                            Website
                        </label>

                        <input type="text" name="website" id="website"
                            value="{{ old('website') }}"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                                focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                                text-sm px-3 py-2"
                            >
                </div>

                <div class="mb-3 relative">
                        <label for="logo" class="block text-sm font-medium text-gray-700">
                            Organisation Logo
                        </label>
                    
                        <input type="file" name="logo" id="logo"
                            accept="image/png,image/jpeg,image/jpg,image/webp"
                            class="mt-2 w-full rounded-lg border border-gray-300 shadow-sm 
                                file:mr-4 file:rounded-md file:border-0 file:bg-cyan-50 
                                file:px-4 file:py-2 file:text-sm file:font-medium 
                                file:text-cyan-700 hover:file:bg-cyan-100
                                text-sm"
                            >
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 pt-3 flex gap-3">
                <button type="submit"
                    class="px-5 py-3 text-sm font-medium rounded-lg 
                        bg-cyan-600 text-white hover:bg-cyan-700 
                        focus:ring-2 focus:ring-cyan-300">
                    Submit
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
        const suggestionUrl = "{{ route('registry.file-types.code.suggestions') }}";

        if (!input || !box) {
            console.error('Input or suggestion box not found');
            return;
        }

        input.addEventListener('input', function () {
            let query = this.value.trim();

            if (query.length < 1 ) {
                box.classList.add('hidden');
                return;
            }

            fetch(`${suggestionUrl}?q=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin' // ensures auth session is sent
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Request failed: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Suggestions:', data); 

                box.innerHTML = '';

                if (!data.length) {
                    box.classList.add('hidden');
                    return;
                }

                box.classList.remove('hidden');

                // ADD HEADER HERE
                const header = document.createElement('div');
                header.className = "px-2 py-1 text-xs text-gray-500 border-b";
                header.textContent = "Existing codes:";
                box.appendChild(header);

                data.forEach(name => {
                    const item = document.createElement('div');
                    item.className = "p-2 cursor-pointer hover:bg-gray-100 text-sm";
                    item.textContent = name;

                    item.addEventListener('click', () => {
                        input.value = name;
                        box.classList.add('hidden');
                    });

                    box.appendChild(item);
                });
            })
            .catch(error => {
                console.error('Fetch error:', error);
                box.classList.add('hidden');
            });
        });

    });
</script>
@endpush
@endsection


