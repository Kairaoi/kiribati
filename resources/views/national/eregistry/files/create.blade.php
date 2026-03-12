@extends('layouts.app')

@section('content')
{{-- <div class="container mx-auto font-montserrat px-4 py-8 max-w-6xl mt-3 rounded-md min-h-screen"> --}}

{{-- <div class="container mx-auto font-poppins px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('files.create.withType', $createType) }} </div> --}}

<div class="container bg-white mx-auto font-poppins px-6 py-10 max-w-5xl mt-4 rounded-md min-h-screen border border-gray-600">
    @if($createType === 'dispatch')
        <h1 class="flex items-center justify-center text-m font-semibold text-gray-800 tracking-wide mb-4">
            Create New Dispatch File
        </h1>
    @elseif($createType === 'internal')
        <h1 class="flex items-center justify-center text-m font-semibold text-gray-800 tracking-wide mb-4">
            Create New Internal File
        </h1>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('registry.files.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
 
        <!-- <label for="name" class="block text-sm font-medium text-gray-700">Division</label> -->
            <div class="text-gray-700 text-sm grid grid-cols-1">
                <label for="subject" class="block">File Subject: <span class="text-red-600">*</span></label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="mt-1 mb-4 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            </div>
    
            <div class="text-gray-700 text-sm grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- File Name -->
                

                <!-- Division -->

                @if($createType === 'dispatch')
                    <input type="hidden" name="organisation_id" id="organisation_id" value="{{ Auth::user()->organisation_id}}">
                    <div>
                        @if(Auth::user()->hasRole('user'))
                            <label for="division" class="block">From Division: </label>
                            <input type="text" name="division" id="division" value="{{ Auth::user()->division ? Auth::user()->division->name : '' }}" class="mt-1 block w-full border-gray-300 bg-gray-100 text-gray-700 rounded-md shadow-sm focus:ring-0 focus:border-gray-300 sm:text-sm cursor-not-allowed" required readonly>
                            <input type="hidden" name="division_id" id="division_id" value="{{ Auth::user()->division_id }}">
                        
                        @elseif(Auth::user()->hasRole('admin') || Auth::user()->hasRole('registry'))
                            <label for="division" class="block"> From Division: <span class="text-red-600">*</span></label>
                            <select name="division_id" id="division_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option class="text-gray-500" value="">Select a division </option>
                                @foreach($divisions as $division)
                                    @if($division->organisation_id == Auth::user()->organisation_id)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        @endif
                    </div>
                @else
            
                    {{-- 1) Type --}}
                    <div>
                        <label for="organisation_type">From Organisation Type</label>
                        <select id="organisation_type" 
                                name="organisation_type" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                data-selected="{{ old('organisation_type') }}">
                            <option value="">Select Type</option>
                            @foreach($organisationTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2) Organisation (select) --}}
                    <div id="org-select-container">
                        <label for="organisation">Organisation Name</label>
                        <select id="organisation" 
                                name="organisation_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                data-selected="{{ old('organisation_id') }}"
                        >
                            <option value="">Select Organisation</option>
                        </select>
                    </div>

                    {{-- 2b) Organisation (text input when no orgs available) --}}
                    <div id="org-input-container" style="display:none;">
                        <label for="organisation_name">Organisation Name</label>
                        <input type="text" 
                               id="organisation_name" 
                               name="organisation_name" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                               placeholder="Enter organisation name" 
                               value="{{ old('organisation_name') }}">
                    </div>

                    {{-- 3) Division (depends on organisation) --}}
                    <div id="division-container" style="display:none;">
                        <label for="division_id">From Division</label>
                        <select id="division_id" 
                                name="division_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                data-selected="{{ old('division_id') }}">
                            <option value="">-- Select Division --</option>
                        </select>
                    </div>
                @endif

                <!-- File Type -->
                <div>
                    <label for="file_type_id" class="block">File Type: <span class="text-red-600">*</span></label>
                    <select name="file_type_id"
                            id="file_type_id" 
                            class="select2 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                        <option class="" disabled selected>Select File Type</option>
                        @foreach ($file_types as $type)
                            <option value="{{ $type->id }}" {{ old('file_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- File Category -->
                <div>
                    <label for="category_id" class="block">
                        File Category:
                    </label>
                    <select name="category_id" 
                            id="category_id" 
                            class="select2 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                            required>
                        <option value="" disabled selected>Select File Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        
        <div class="pt-6 border-t border-gray-300 mt-6 text-gray-700 space-y-4"></div>
    
            <!-- Main File Section -->
            <div>
                <label for="file_1" class="block mt-2 text-sm">
                        Main File:  <span class="text-red-600">*</span>               
                </label>
                <input type="file" name="main_file" id="main_file" accept="application/pdf"
                       class="block w-full text-sm 
                            file:mr-4 file:py-2 file:px-4
                            file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100"
                       required>
        
        
                <!-- Additional Files Section -->
                <div id="file-upload-container" class="space-y-4">
                    <div class="file-upload-item text-sm relative">
                        <label for="file_1" class="block mt-4">
                            Additional Files:      
                        </label>
                        <input type="file" name="additional_files[]" id="file_1" accept="application/pdf"
                            class="block w-full text-sm text-gray-600
                                file:mr-4 file:py-2 file:px-4
                                file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100">                
                    </div>
            
                    <button type="button" id="add-file-button"
                        class="w-full inline-flex justify-start text-purple-700 underline text-sm">
                        + Additional File
                    </button>
                </div>
            </div>
        

        <!-- Recipient Ministries Section -->
        @if($createType === 'dispatch')
            <input type="hidden" name="initial_type" value="dispatch">
            <div class="pt-6 border-t border-gray-300 mt-6 text-gray-700 space-y-4">
                <div>
                    <h2 class="pb-2 font-medium text-indigo-700 text-m">Select Recipient Ministries:</h2>
                </div>
                <div>
                    <!-- Ministries checkboxes in a responsive grid layout -->
                    <div class="mt-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($ministries as $id => $name)
                            @if($id != auth()->user()->organisation_id)
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="recipient_organisations[]" 
                                        value="{{ $id }}" 
                                        id="organisation_{{ $id }}" 
                                        class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500 rounded"
                                        @if(old('recipient_organisations') && in_array($id, old('recipient_organisations'))) checked @endif
                                    >
                                    <label for="organisation_{{ $id }}" class="ml-2 text-sm text-gray-700">{{ $name }}</label>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif ($createType === 'internal')
            <input type="hidden" name="initial_type" value="internal">
            <input type="hidden" name="recipient_organisations[]" value="{{ Auth::user()->organisation_id}}">
        @endif 
        
        <!-- Hidden field to indicate initial_type -->
        {{-- <input type="hidden" name="initial_type" value="{{ $createType === 'dispatch' ? 'dispatch' : 'internal' }}"> --}}

        <div class="mt-6 border-t border-gray-300"></div>

        <div class="text-center">
            <button type="submit" class="mt-10 mb-2 w-1/4 bg-green-700 text-white py-2 px-8 rounded-lg hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Create File
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let fileCounter = 1;  // Start with the initial file
            const container = document.getElementById('file-upload-container');
            const addButton = document.getElementById('add-file-button');

            addButton.addEventListener('click', function () {
                if (fileCounter >= 5) {
                    alert('You can only add up to 5 files.');
                    return;
                }

                fileCounter++;
                const newFileInput = document.createElement('div');
                newFileInput.classList.add('file-upload-item', 'mt-4', 'border', 'p-4', 'rounded', 'relative');

                newFileInput.innerHTML = `
                    <label for="file_${fileCounter}" class="block text-sm font-medium text-gray-700">
                        Additional File (PDF only)
                    </label>
                    <input type="file" name="additional_files[]" id="file_${fileCounter}" accept="application/pdf"
                        class="block w-full text-sm text-gray-600
                            file:mr-4 file:py-2 file:px-4
                            file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100">                
                        </div>                    
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
            $('#category_id').select2({
                placeholder: "Select Category Type",
                allowClear: true
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ORGANISATIONS = @json($organisations); // [{id,name,code,organisation_type_id}, ...]
            const DIVISIONS     = @json($allDivisions);     // [{id,name,organisation_id}, ...]

            const OTHER_TYPE_ID = {{ $otherTypeId }};

            const typeSelect         = document.getElementById("organisation_type");
            const orgSelect          = document.getElementById("organisation");
            const orgSelectContainer = document.getElementById("org-select-container");
            const orgInputContainer  = document.getElementById("org-input-container");
            const divisionContainer  = document.getElementById("division-container");
            const divisionSelect     = document.getElementById("division_id");

            function resetOrgSelect() {
                orgSelect.innerHTML = '<option value="">Select Organisation</option>';
            }

            function resetDivisionSelect() {
                divisionSelect.innerHTML = '<option value="">Select Division</optio>';
            }

            function populateOrganisationsByType(typeId) {
                resetOrgSelect();

                // Filter organisations by type
                const filtered = ORGANISATIONS.filter(o =>
                    String(o.organisation_type_id) === String(typeId)
                );

                // Populate the dropdown
                filtered.forEach(o => {
                    const opt = document.createElement('option');
                    opt.value = o.id;
                    opt.textContent = o.name + (o.code ? ` (${o.code})` : '');
                    orgSelect.appendChild(opt);
                });

                // If type = OTHER, add the "Add new" option
                if (String(typeId) === String(OTHER_TYPE_ID)) {
                    const addNew = document.createElement('option');
                    addNew.value = '__add_new__';
                    addNew.textContent = '+ Add new organisation';
                    orgSelect.appendChild(addNew);

                    // Show dropdown AND input
                    orgSelectContainer.style.display = 'block';
                    orgInputContainer.style.display = 'block';
                } else {
                    // Normal type: only show dropdown
                    orgSelectContainer.style.display = 'block';
                    orgInputContainer.style.display = 'none';
                }

                return filtered.length;
            }

            function populateDivisionsByOrganisation(orgId) {
                resetDivisionSelect();
                const filtered = DIVISIONS.filter(d => String(d.organisation_id) === String(orgId));
                filtered.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.name;
                    divisionSelect.appendChild(opt);
                });
                return filtered.length;
            }

            // 1️⃣ Populate organisations when type changes
            typeSelect.addEventListener("change", function () {
                populateOrganisationsByType(this.value);
            });

            // 2️⃣ Handle showing input / divisions when organisation changes
            orgSelect.addEventListener("change", function () {
                const value = this.value;
                if (String(typeSelect.value) === String(OTHER_TYPE_ID) && value === '__add_new__') {
                    orgInputContainer.style.display = 'block';
                    divisionContainer.style.display = 'none';
                    resetDivisionSelect();
                } else {
                    orgInputContainer.style.display = String(typeSelect.value) === String(OTHER_TYPE_ID) ? 'block' : 'none';
                    document.getElementById('organisation_name').value = '';
                    if (!value) {
                        divisionContainer.style.display = 'none';
                        resetDivisionSelect();
                        return;
                    }
                    const count = populateDivisionsByOrganisation(value);
                    divisionContainer.style.display = count > 0 ? 'block' : 'none';
                }
            });

            // When Organisation changes → populate divisions
            orgSelect.addEventListener("change", function () {
                const orgId = this.value;
                if (!orgId) {
                    divisionContainer.style.display = "none";
                    resetDivisionSelect();
                    return;
                }

                const count = populateDivisionsByOrganisation(orgId);
                divisionContainer.style.display = count > 0 ? "block" : "none";
            });

            // --- Optional: restore old() selections on validation error / edit screens ---
            const preType = typeSelect.getAttribute('data-selected');
            const preOrg  = orgSelect.getAttribute('data-selected');
            const preDiv  = divisionSelect.getAttribute('data-selected');

            if (preType) {
                typeSelect.value = preType;
                const orgCount = populateOrganisationsByType(preType);
                if (orgCount > 0) {
                    orgSelectContainer.style.display = "block";
                    orgInputContainer.style.display = "none";
                    if (preOrg) {
                        orgSelect.value = preOrg;
                        const divCount = populateDivisionsByOrganisation(preOrg);
                        divisionContainer.style.display = divCount > 0 ? "block" : "none";
                        if (preDiv) divisionSelect.value = preDiv;
                    }
                } else {
                    orgSelectContainer.style.display = "none";
                    orgInputContainer.style.display = "block";
                    divisionContainer.style.display = "none";
                }
            }
        });
    </script>
</div>
@endsection
