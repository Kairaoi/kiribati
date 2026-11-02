@extends('layouts.app')

@section('content')
<div class="container mt-6 mx-auto shadow rounded-lg px-4 py-6 max-w-5xl">
    <h1 class="flex mt-2 text-xl font-bold mb-6">Edit User</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('registry.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

              <!-- Ministry -->
            <div>
                @if ($isSystemAdmin)
                    <div>
                        <label for="organisation_id" class="mb-1 block text-sm font-medium text-gray-700">
                            Ministry
                        </label>

                        <select
                            name="organisation_id"
                            id="organisation_id"
                            required
                            class="mt-1 text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                        >
                            <option value="">Select ministry</option>

                            @foreach ($organisations as $organisation)
                                <option
                                    value="{{ $organisation->id }}"
                                    @selected(old('organisation_id', $user->organisation_id) == $organisation->id)
                                >
                                    {{ $organisation->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('organisation_id')
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
                            value="{{ $user->organisation->name }}"
                            readonly
                            class="mt-1 block text-sm w-full rounded-md border-gray-300
                                bg-gray-100 text-gray-600 shadow-sm"
                        >

                        <input
                            type="hidden"
                            id="organisation_id"
                            value="{{ $user->organisation_id }}"
                        >
                    </div>
                @endif
            </div>

            <!-- Division -->
            <div>
                <label for="division_id" class="block text-sm font-medium text-gray-700">
                    Select division: <span class="text-red-600">*</span>
                </label>

                <select
                    name="division_id"
                    id="division_id"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm"
                    required
                    disabled
                >
                    <option value="">Select an organisation first</option>
                </select>
            </div>

            <!-- First Name -->
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name: <span class="text-red-600">*</span></label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
            </div>

            <!-- Last Name -->
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name: <span class="text-red-600">*</span></label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
            </div>

            <!-- Designation -->
            <div>
                <label for="designation" class="block text-sm font-medium text-gray-700">Designation: <span class="text-red-600">*</span></label>
                <input type="text" name="designation" id="designation" value="{{ old('designation', $user->designation) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email: <span class="text-red-600">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Select Role: <span class="text-red-600">*</span></label>
                <select name="role" id="role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                    <option value=""> Select a role</option>
                    @foreach ($roles as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('role_id', $currentRole?->id) == $id ? 'selected' : '' }}>
                            {{ ucfirst($name) }}
                        </option>
                    @endforeach
                </select>
            </div>
                          
        </div>
            <button type="submit" class="mt-4 mb-10 justify-left bg-cyan-600 text-white py-3 px-6 rounded-md hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-700 focus:ring-offset-2">
                Edit User
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

        if (passwordInput.value.length < 8) {
            hint.classList.remove('hidden');
        } else {
            hint.classList.add('hidden');
        }
    }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const organisationSelect = document.getElementById('organisation_id');
        const divisionSelect = document.getElementById('division_id');

        const divisions = @json($divisions);
        const selectedDivisionId = @json(old('division_id', $user->division_id));

        function loadDivisions(organisationId, divisionId = null) {
            // Clear the previous options.
            divisionSelect.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';

            if (!organisationId) {
                placeholder.textContent = 'Select an organisation first';
                divisionSelect.appendChild(placeholder);
                divisionSelect.disabled = true;
                return;
            }

            const matchingDivisions = divisions.filter((division) =>
                String(division.organisation_id) === String(organisationId)
            );

            placeholder.textContent = matchingDivisions.length
                ? 'Select a division'
                : 'No divisions available for this organisation';

            divisionSelect.appendChild(placeholder);

            matchingDivisions.forEach((division) => {
                const option = document.createElement('option');

                option.value = division.id;
                option.textContent = division.name;

                option.selected =
                    String(division.id) === String(divisionId);

                divisionSelect.appendChild(option);
            });

            divisionSelect.disabled = matchingDivisions.length === 0;
        }

        organisationSelect?.addEventListener('change', function () {
            // Clear the previous division when the organisation changes.
            loadDivisions(this.value);
        });

        // Restore the existing or previously submitted division on page load.
        loadDivisions(organisationSelect?.value, selectedDivisionId);
    });
</script>
</div>
@endsection
