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

    @if (session('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition.opacity.scale.80
            x-init="setTimeout(() => show = false, 4000)" 
            class="fixed right-0 text-white bg-cyan-400 px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3 z-50">
            <!-- Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke-width="2" 
                stroke="currentColor" 
                class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>

            <!-- Message -->
            <span class="font-medium">{{ session('success') }}</span>

            <!-- Close button -->
            <button @click="show = false" class="ml-4 hover:text-gray-200">
                &times;
            </button>
        </div>
    @endif



    <form action="{{ route('registry.organisations.update', $organisation) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="bg-white shadow-sm rounded-2xl p-6 max-w-4xl mx-auto mt-4 mb-4">

            <h2 class="text-m font-semibold text-gray-800 mb-6">
                <span class="text-cyan-600">Edit Organisation (info is used in online templates)</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="mb-3 relative">
                    <label class="block text-sm font-medium text-gray-700">
                        Name <span class="text-red-500">*</span>
                    </label>

                    <input type="text" name="name" id="name"
                        value="{{ old('name', $organisation->name) }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                            focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                            text-sm px-3 py-2"
                        @readonly(!auth()->user()->hasRole('system-admin'))
                        required>
                      
                    {{-- <input type="hidden" name="name" value="{{ $organisation->name }}"> --}}

                    {{-- <div id="name-suggestions" class="absolute z-10 w-full bg-white border rounded-md mt-1 hidden shadow">
                    </div>
                    <p id="name-warning" class="text-sm text-red-600 mt-1 hidden">
                        This already exists.
                    </p> --}}
                </div>

                <!-- Code -->
                <div class="mb-3 relative">
                    <label for="code" class="block text-sm font-medium text-gray-700">
                        Code <span class="text-red-500">*</span>
                    </label>

                    <input type="text" name="code" id="code"
                        value="{{ old('code', $organisation->code) }}"
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
                        value="{{ old('reviewer_title', $organisation->reviewer_title) }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                                focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                                text-sm px-3 py-2"
                        placeholder="e.g. Secretary or Chairperson"
                        @readonly(!auth()->user()->hasRole('system-admin'))
                        >

                    
                </div>

                 <div class="mb-3 relative">
                    <label class="block text-sm font-medium text-gray-700">
                    Organisation Type <span class="text-red-600">*</span>
                    </label>

                    @role('system-admin')
                        <select name="organisation_type_id" id="organisation_type_id"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2"
                            >

                            <option value="">-- None --</option>

                            @foreach($organisationTypes as $type)
                                <option value="{{ $type->id }}"
                                    @selected(old('organisation_type_id', $organisation->organisation_type_id) == $type->id)>
                                    {{ $type->name }} 
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" name="organisation_type_name" id="organisation_type_name"
                            value="{{ old('organisation_type_name', $organisation->type->name) }}"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm 
                                focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                                text-sm px-3 py-2"
                            readonly>
                            <input type="hidden" name="organisation_type_id" value="{{ $organisation->organisation_type_id }}">
                    @endrole   
                </div>

                <div class="mb-3 relative">
                    <label for="address" class="block text-sm font-medium text-gray-700">
                        Address
                    </label>
                    <input type="text" name="address" id="address"
                        value="{{ old('address', $organisation->address) }}"
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
                            value="{{ old('po_box', $organisation->po_box) }}"
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
                            value="{{ old('phone', $organisation->phone) }}"
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
                            value="{{ old('email', $organisation->email ?? '') }}"
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
                            value="{{ old('website', $organisation->website ?? '') }}"
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
                        @if($organisation->logo_path)
                            <p> <span class="text-sm text-gray-500"> Logo Preview: </span> </p>
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $organisation->logo_path) }}"
                                    alt="{{ $organisation->name }} Logo"
                                    class="h-20 w-20 object-contain rounded-lg border border-gray-200 bg-gray-50 p-2">
                            </div>
                        @else 
                            <div class="mt-2 text-sm text-gray-500">
                                No Logo Uploaded
                            </div>
                        @endif
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


