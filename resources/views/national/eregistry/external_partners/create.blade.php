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

    <form action="{{ route('registry.external-partners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
 
        <div class="bg-white shadow-sm font-poppins rounded-2xl p-6 max-w-2xl mx-auto mt-4 mb-4">

            <h2 class="text-m font-semibold text-gray-800 mb-6">
                <span class="text-cyan-600">Create your Ministry External Partner</span>
            </h2>

            <!-- File Type Name -->
            <div class="mb-5 relative">
                <label class="block text-sm font-medium text-gray-700">
                    External Partner Name <span class="text-red-500">*</span>
                </label>

                <input type="text" name="name" id="external_partner_name"
                    value="{{ old('name') }}"
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm 
                        focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 
                        text-sm px-3 py-2"
                    placeholder="e.g. International Telecommunication Union"
                    autocomplete="off"
                    required>

                <div id="name-suggestions"
                    class="absolute z-10 w-full bg-white border rounded-md mt-1 hidden shadow">
                </div>
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
                    placeholder="Short description of this external partner">
            </div>
            

        {{-- Optional link to Identity Organisation --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">
                    Link to Identity Organisation <span class="text-gray-400">(optional)</span>
                </label>

                <select name="identity_organisation_id" id="identity_organisation_id"
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2">

                    <option value="">-- None --</option>

                    @foreach($identityOrganisations as $org)
                        <option value="{{ $org->id }}"
                            data-type="{{ $org->organisation_type_id }}"
                            {{ old('identity_organisation_id') == $org->id ? 'selected' : '' }}>
                            
                            {{ $org->name }} {{ $org->code ? "({$org->code})" : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">
                    Category <span class="text-gray-400">(required if no identity selected)</span>
                </label>

                <select name="organisation_type_id" id="organisation_type_id"
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2">

                    <option value="">-- None --</option>

                    @foreach($organisationTypes as $type)
                        <option value="{{ $type->id }}"
                            {{ old('organisation_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="border-t border-gray-200 pt-5 flex justify-center gap-3">
                <button type="submit"
                    class="px-5 py-2 text-sm font-medium rounded-lg 
                        bg-cyan-600 text-white hover:bg-cyan-700 
                        focus:ring-2 focus:ring-cyan-300">
                    Create External Partner
                </button>
            </div>
        </div>
    </form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const input = document.getElementById('file_type_name');
        const box = document.getElementById('name-suggestions');
        const suggestionUrl = "{{ route('registry.file-types.name.suggestions') }}";

        if (!input || !box) {
            console.error('Input or suggestion box not found');
            return;
        }

        input.addEventListener('input', function () {
            let query = this.value.trim();

            if (query.length < 2 ) {
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
                header.textContent = "Existing file-types:";
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

<script>
const identitySelect = document.getElementById('identity_organisation_id');
const categorySelect = document.getElementById('organisation_type_id');

identitySelect.addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];

    if (this.value) {
        // auto-fill category
        categorySelect.value = selected.dataset.type || '';

        // lock it
        categorySelect.setAttribute('disabled', true);
    } else {
        // enable manual selection
        categorySelect.removeAttribute('disabled');
    }
});
</script>
@endpush
@endsection


