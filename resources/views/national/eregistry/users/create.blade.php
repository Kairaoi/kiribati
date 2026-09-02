@extends('layouts.app')

@section('content')
<div class="container mx-auto font-montserrat shadow px-4 py-6 max-w-5xl">
    <h1 class="flex mt-4 text-xl font-bold mb-6">Create New User</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('registry.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

              <!-- Ministry -->
            <div>
                @if ($isSystemAdmin)
                    <div>
                        <label
                            for="ministry_id"
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Ministry
                        </label>

                        <select
                            name="ministry_id"
                            id="ministry_id"
                            required
                            class="mt-1 text-sm block w-full rounded-md border-gray-300
                                shadow-sm focus:border-cyan-500
                                focus:ring-cyan-500"
                        >
                            <option value="">Select ministry</option>

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
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Ministry
                        </label>

                        <input
                            type="text"
                            value="{{ $user->ministry->identityOrganisation->name }}"
                            readonly
                            class="mt-1 block text-sm w-full rounded-md border-gray-300
                                bg-gray-100 text-gray-600 shadow-sm"
                        >

                        <input
                            type="hidden"
                            id="ministry_id"
                            name="ministry_id"
                            value="{{ $user->ministry_id }}"
                        >
                    </div>
                @endif
            </div>

            <!-- Division -->
            <div>
                <label for="division" class="block text-sm font-medium text-gray-700"> Select division: <span class="text-red-600">*</span></label>
                <select name="division_id" id="division_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
                        <option value="">Select a division </option>
                        @foreach($divisions as $division)
                            @if($division->organisation_id == Auth::user()->organisation_id)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endif
                        @endforeach
                </select>
            </div> 

            <!-- First Name -->
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name: <span class="text-red-600">*</span></label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
            </div>

            <!-- Last Name -->
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name: <span class="text-red-600">*</span></label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
            </div>

            <!-- Designation -->
            <div>
                <label for="designation" class="block text-sm font-medium text-gray-700">Designation: <span class="text-red-600">*</span></label>
                <input type="text" name="designation" id="designation" value="{{ old('designation') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email: <span class="text-red-600">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password: <span class="text-red-600">*</span>
                </label>
                <div class="relative">
                    <input type="password" name="password" id="password"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm"
                        required oninput="checkPasswordLength()">
                    {{-- <p id="password-hint" class="mt-2 text-sm text-red-600 hidden">Password must be at least 8 characters.</p> --}}
                </div>
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Select Role: <span class="text-red-600">*</span></label>
                <select name="role" id="role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                    <option value=""> Roles</option>
                    @foreach ($roles as $id => $name)
                        <option value="{{ $id }}">
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
                          
        </div>
            <button type="submit" class="w-full bg-cyan-600 text-white py-2 px-4 rounded-md hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                Create User
            </button>
    </form>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- JavaScript to toggle all checkboxes -->
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            let fileCounter = 1;  // Start with the initial file
            const container = document.getElementById('file-upload-container');
            const addButton = document.getElementById('add-file-button');

            addButton.addEventListener('click', function () {
                if (fileCounter >= 3) {
                    alert('You can only add up to 3 files.');
                    return;
                }

                fileCounter++;
                const newFileInput = document.createElement('div');
                newFileInput.classList.add('file-upload-item', 'mt-4', 'border', 'p-4', 'rounded', 'relative');

                newFileInput.innerHTML = `
                    <label for="file_${fileCounter}" class="block text-sm font-medium text-gray-700">
                        Upload Additional File (PDF only)
                    </label>
                    <input type="file" name="additional_files[]" id="file_${fileCounter}" accept="application/pdf"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
                    <button type="button" class="remove-file-button mt-2 inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                        Remove File
                    </button>
                `;

                container.appendChild(newFileInput);

                // Add remove functionality
                const removeButton = newFileInput.querySelector('.remove-file-button');
                removeButton.addEventListener('click', function () {
                    container.removeChild(newFileInput);
                    fileCounter--; // Allow adding again when one is removed
                });
            });
        });

        document.getElementById('select_all_organisations').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="organisations[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Initialize Select2 for the file type dropdown
        $(document).ready(function() {
            $('#file_type_id').select2({
                placeholder: "Select File Type",
                allowClear: true
            });
        });

        // Initialize Select2 for the file category dropdown
        $(document).ready(function() {
            $('#file_type_id').select2({
                placeholder: "Select File Type",
                allowClear: true
            });
        });

        function checkPasswordLength() {
            const passwordInput = document.getElementById('password');
            const hint = document.getElementById('password-hint');

            if (passwordInput.value.length < 4) {
                hint.classList.remove('hidden');
            } else {
                hint.classList.add('hidden');
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ministrySelect = document.getElementById('ministry_id');
            const divisionSelect = document.getElementById('division_id');

            const divisions = @json($divisions);
            const oldDivisionId = @json(old('division_id'));

            function loadDivisions(ministryId) {
                divisionSelect.innerHTML =
                    '<option value="">Select division</option>';

                if (!ministryId) {
                    return;
                }

                divisions
                    .filter((division) =>
                        String(division.ministry_id) === String(ministryId)
                    )
                    .forEach((division) => {
                        const option = document.createElement('option');

                        option.value = division.id;
                        option.textContent = division.name;
                        option.selected =
                            String(division.id) === String(oldDivisionId);

                        divisionSelect.appendChild(option);
                    });
            }

            ministrySelect?.addEventListener('change', function () {
                loadDivisions(this.value);
            });

            if (ministrySelect?.value) {
                loadDivisions(ministrySelect.value);
            }
        });
    </script>
</div>
@endsection
