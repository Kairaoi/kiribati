@extends('layouts.app')

@section('content')
{{-- <div class="container mx-auto font-montserrat px-4 py-8 max-w-6xl mt-3 rounded-md min-h-screen"> --}}

{{-- <div class="container mx-auto font-poppins px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('files.create.withType', $createType) }} </div> --}}

<div class="container bg-white mx-auto font-poppins px-6 py-10 max-w-4xl mt-4 rounded-md min-h-screen border border-gray-600">

     <h2 class="text-m font-semibold text-gray-800 mb-6">
        <span class="text-cyan-600">Create File</span>
     </h2>

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
                <label for="subject" class="block">Document Subject <span class="text-red-600">*</span></label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="mt-1 mb-4 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            </div>
    
            <div class="text-gray-700 text-sm grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- @if($createType === 'dispatch')
                    <input type="hidden" name="organisation_id" id="organisation_id" value="{{ Auth::user()->organisation_id}}">
                    <div>
                        @if(Auth::user()->hasRole('user'))
                            <label for="division" class="block">From Division </label>
                            <input type="text" name="division" id="division" value="{{ Auth::user()->division ? Auth::user()->division->name : '' }}" class="mt-1 block w-full border-gray-300 bg-gray-100 text-gray-700 rounded-md shadow-sm focus:ring-0 focus:border-gray-300 sm:text-sm cursor-not-allowed" required readonly>
                            <input type="hidden" name="division_id" id="division_id" value="{{ Auth::user()->division_id }}">
                        
                        @elseif(Auth::user()->hasRole('admin') || Auth::user()->hasRole('registry'))
                            <label for="division" class="block"> From Division <span class="text-red-600">*</span></label>
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
                @elseif($createType === 'internal') --}}
                    {{-- 1) Type --}}
                    <div>
                        <label for="source_type">Select Source Type <span class="text-red-600">*</span></label>
                        {{-- <select name="source_type" 
                                id="source_type"                                 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                data-selected="{{ old('source_type') }}">
                            <option value="">Select Source Type</option>
                            <option value="identified_org">Registered Organisation</option>
                            <option value="partner">External Partner</option>
                        </select> --}}
                        <select name="source_type" id="source_type" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                            <option value="">Select Source Type</option>
                            <option value="identity_organisation">Registered Organisation</option>
                            <option value="external_partner">External Partner</option>
                        </select>
                    </div>


                    {{-- 2) Organisation (select) --}}
                    <div id="org-select-container">
                        <label for="organisation">Organisation Name <span class="text-red-600">*</span></label>
                        <select id="organisation" 
                                name="source_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                data-selected="{{ old('organisation_id') }}"
                        >
                            <option value="">Select Organisation</option>
                        </select>
                    </div>

                    <div id="partner-select-container">
                        <label for="partner">External Partner <span class="text-red-600">*</span></label>
                        <select id="partner" 
                                name="source_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                data-selected="{{ old('partner_id') }}"
                        >
                            <option value="">Select External Partner</option>
                        </select>
                    </div>


                    {{-- 3) Division (depends on organisation) --}}
                    <div id="division-container" style="display:none;">
                        <label for="division_id">From Division
                            <span class="text-gray-400">(Optional)</span></label>
                        <select id="division_id" 
                                name="division_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                data-selected="{{ old('division_id') }}">
                            <option value="">-- Select Division --</option>
                        </select>
                    </div>
                

                <!-- File Type -->
                <div>
                    <label for="file_type_id" class="block">File Type <span class="text-red-500">*</span></label>
                    <select name="file_type_id"
                            id="file_type_id" 
                            class="select2 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                        <option class="" disabled selected>Select File Type</option>
                        @foreach ($file_types as $type)
                            <option value="{{ $type->id }}" {{ old('file_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->is_global ? '🌐' : '🏛 ' }} {{ $type->name }}                           
                            </option>
                        @endforeach
                    </select>
                    <div class="mt-2">
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>No suitable file type?</span>

                            <a href="{{ route('registry.file-types.create') }}" 
                                class="inline-flex items-center gap-1 font-medium text-indigo-600 hover:text-indigo-800 transition">
                                
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 4v16m8-8H4" />
                                </svg>

                                Add file type
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Create new link -->
                

                <!-- File Category -->
                <div>
                    <label for="category_id" class="block">
                        Document Category
                        <span class="text-gray-400">(Optional)</span>
                    </label>
                    <select name="category_id" 
                            id="category_id" 
                            class="select2 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                    >
                        <option value="" disabled selected>Select Document Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block">Due Date <span class="text-gray-400">(Optional)</span></label>
                    <input type="date" name="due_date" min="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('due_date') }}">
                </div>

            </div>
        
        <div class="pt-6 border-t border-gray-300 mt-6 text-gray-700 space-y-4"></div>
    
            <!-- Main File Section -->
            <div>
                <label for="file_1" class="block mt-2 text-sm">
                        Main Document (PDF):  <span class="text-red-600">*</span>               
                </label>
                <input type="file" name="main_file" id="main_file" accept="application/pdf"
                       class="block w-full text-sm 
                            file:mr-4 file:py-2 file:px-4
                            file:border-0
                            file:text-sm file:font-semibold
                            file:bg-cyan-50 file:text-cyan-700
                            hover:file:bg-cyan-100"
                       required>
        
        
                <!-- Additional Files Section -->
                <div id="file-upload-container" class="space-y-4">
                    <div class="file-upload-item text-sm relative">
                        <label for="file_1" class="block mt-4">
                            Supporting Documents (PDF):      
                        </label>
                        <input type="file" name="additional_files[]" id="file_1" accept="application/pdf"
                            class="block w-full text-sm text-gray-600
                                file:mr-4 file:py-2 file:px-4
                                file:border-0
                                file:text-sm file:font-semibold
                                file:bg-cyan-50 file:text-cyan-700
                                hover:file:bg-cyan-100">                
                    </div>
            
                    <button type="button" id="add-file-button"
                        class="w-full inline-flex justify-start text-purple-700 underline text-sm">
                        + Supporting Document
                    </button>
                </div>
            </div>
        

        <!-- Recipient Ministries Section -->
        {{-- @if($createType === 'dispatch')
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
         --}}
        <!-- Hidden field to indicate initial_type -->
        {{-- <input type="hidden" name="initial_type" value="{{ $createType === 'dispatch' ? 'dispatch' : 'internal' }}"> --}}

        <div class="mt-6 border-t border-gray-300"></div>

        <div class="text-center">
            <button type="submit" class="mt-10 mb-2 w-full bg-cyan-500 text-white py-2 px-8 rounded-lg hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
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
                  
                    <input type="file" name="additional_files[]" id="file_${fileCounter}" accept="application/pdf"
                        class="block w-full text-xs text-gray-600
                            file:mr-4 file:py-2 file:px-4
                            file:border-0
                            file:text-xs file:font-semibold
                            file:bg-cyan-50 file:text-cyan-700
                            hover:file:bg-cyan-100">                
                        </div>                    
                        <button type="button" class="remove-file-button mt-2 inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs rounded-md hover:bg-red-700">
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

            const sourceType = document.getElementById("source_type");
            const orgSelect = document.getElementById("organisation");
            const partnerSelect = document.getElementById("partner");

            const orgContainer = document.getElementById("org-select-container");
            const partnerContainer = document.getElementById("partner-select-container");

            const ORGANISATIONS = @json($identityOrganisations);
            const PARTNERS = @json($externalPartners);

            function reset(select, label) {
                select.innerHTML = `<option value="">${label}</option>`;
            }

            function hideAll() {
                orgContainer.style.display = "none";
                partnerContainer.style.display = "none";
                orgSelect.disabled = true;
                partnerSelect.disabled = true;
            }

            sourceType.addEventListener("change", function () {

                hideAll();

                if (this.value === "identity_organisation") {

                    orgContainer.style.display = "block";
                    orgSelect.disabled = false;

                    reset(orgSelect, "Select Organisation");

                    ORGANISATIONS.forEach(o => {
                        const opt = document.createElement("option");
                        opt.value = o.id;
                        opt.textContent = o.name;
                        orgSelect.appendChild(opt);
                    });

                } else if (this.value === "external_partner") {

                    partnerContainer.style.display = "block";
                    partnerSelect.disabled = false;

                    reset(partnerSelect, "Select Partner");

                    PARTNERS.forEach(p => {
                        const opt = document.createElement("option");
                        opt.value = p.id;
                        opt.textContent = p.name;
                        partnerSelect.appendChild(opt);
                    });
                }
            });

            hideAll();
        });
    </script>
    
</div>
@endsection
