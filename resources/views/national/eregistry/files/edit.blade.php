@extends('layouts.app')

@section('content')

<style>
    /* Tables */
    .ck-content table {
        border-collapse: collapse;
        width: 100%;
    }

    .ck-content table,
    .ck-content th,
    .ck-content td {
        border: 1px solid #000;
    }

    .ck-content th,
    .ck-content td {
        padding: 8px;
    }

    /* Bullet lists */
    .ck-content ul {
        list-style-type: disc;
        padding-left: 2rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    /* Numbered lists */
    .ck-content ol {
        list-style-type: decimal;
        padding-left: 2rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .ck-content li {
        display: list-item;
        margin-bottom: 0.25rem;
    }

    /* Nested lists */
    .ck-content ul ul {
        list-style-type: circle;
    }

    .ck-content ul ul ul {
        list-style-type: square;
    }

    .ck-content ol ol {
        list-style-type: lower-alpha;
    }
</style>


{{-- <div class="container mx-auto font-poppins px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('files.create.withType', $createType) }} </div> --}}
<div class="container bg-white shadow mx-auto px-6 py-10 max-w-4xl mt-4 mb-6 text-gray-700 font-medium rounded-md min-h-screen border border-gray-600">
     <h2 class="text-md uppercase font-bold text-cyan-600 mb-6">
        Edit File
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

    <form action="{{ route('registry.files.update', $file) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
            <div class="text-gray-700 text-sm grid grid-cols-1">
                @php
                    $user = auth()->user();
                    $organisationName = $user->organisation?->name;
                    $isRegistry = $user->hasRole('registry');

                    $savedSourceType = match ($file->source_type) {
                        \App\Models\National\Eregistry\Organisation::class =>
                            (string) $file->source_id === (string) $user->organisation_id
                                ? 'own_ministry'
                                : 'identity_organisation',
                        null => '',
                        default => 'external_partner',
                    };

                    $sourceType = old('source_type', $savedSourceType);
                    $divisionId = old('division', $file->from_division_id);
                    $sourceId = old('source_id', $file->source_id);

                    $showDivision = $sourceType === 'own_ministry';
                    $showOrganisation = $sourceType === 'identity_organisation';
                    $showPartner = $sourceType === 'external_partner';

                    $selectClass = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm';
                @endphp

                @if ($isRegistry)
                    <div>
                        <label for="source_type">
                            Source Type <span class="text-red-600">*</span>
                        </label>

                        <select name="source_type" id="source_type" class="{{ $selectClass }}" required>
                            <option value="" disabled @selected($sourceType === '')>
                                Select a source type
                            </option>
                            <option value="own_ministry" @selected($showDivision)>
                                My Ministry{{ $organisationName ? ' (' . $organisationName . ')' : '' }}
                            </option>
                            <option value="identity_organisation" @selected($showOrganisation)>
                                Other Registered Organisations
                            </option>
                            <option value="external_partner" @selected($showPartner)>
                                External Partners
                            </option>
                        </select>

                        @error('source_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="division-select-container" class="mt-3" @if (!$showDivision) hidden @endif>
                        <label for="division" class="block text-sm text-gray-700">
                            Division <span class="text-red-600">*</span>
                        </label>

                        <select id="division" name="from_division_id" class="{{ $selectClass }}"
                            data-selected="{{ $showDivision ? $divisionId : '' }}"
                            @disabled(!$showDivision)>
                            <option value="">Select Division</option>
                        </select>

                        @error('division')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="org-select-container" class="mt-3" @if (!$showOrganisation) hidden @endif>
                        <label for="organisation" class="block text-sm text-gray-700">
                            Organisation Name <span class="text-red-600">*</span>
                        </label>

                        <select id="organisation" name="source_id" class="{{ $selectClass }}"
                            data-selected="{{ $showOrganisation ? $sourceId : '' }}"
                            @disabled(!$showOrganisation)>
                            <option value="">Select Organisation</option>
                        </select>

                        <a href="{{ route('registry.external-partners.create') }}" target="_blank"
                            class="mt-3 text-xs text-cyan-600 hover:text-cyan-800 hover:underline">
                            Organisation not listed? + Add External Partner
                        </a>
                    </div>

                    <div id="partner-select-container" class="mt-3" @if (!$showPartner) hidden @endif>
                        <label for="partner" class="block text-sm text-gray-700">
                            External Partner <span class="text-red-600">*</span>
                        </label>

                        <select id="partner" name="source_id" class="{{ $selectClass }}"
                            data-selected="{{ $showPartner ? $sourceId : '' }}"
                            @disabled(!$showPartner)>
                            <option value="">Select External Partner</option>
                        </select>

                        <a href="{{ route('registry.external-partners.create') }}" target="_blank"
                            class="mt-3 text-xs text-cyan-600 hover:text-cyan-800 hover:underline">
                            Organisation not listed? + Add External Partner
                        </a>
                    </div>

                    @error('source_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @else
                    <div>
                        <label for="source_type">
                            Source Type <span class="text-red-600">*</span>
                        </label>
                        <select name="source_type" id="source_type" class="{{ $selectClass }}">
                            <option value="own_ministry">{{ $organisationName }}</option>
                        </select>
                    </div>

                    <div class="mt-3">
                        <label for="division">
                            Division <span class="text-red-600">*</span>
                        </label>
                        <select id="division" name="division" class="{{ $selectClass }}">
                            <option value="{{ $user->division_id }}">{{ $user->division?->name }}</option>
                        </select>
                    </div>
                @endif
                
                <div class="mt-3">
                    <label for="subject" class="block">Name or Subject of File <span class="text-red-600">*</span></label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject', $file->subject) }}" class="mt-1 mb-2 text-sm block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" required>
                </div>
            </div>
            <div class="mt-2 text-gray-700 text-sm grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- File Type -->
                <div>
                    <label for="file_type_id" class="block">File Type <span class="text-red-500">*</span></label>
                    <select name="file_type_id"
                            id="file_type_id" 
                            class="select2 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm"
                            required>
                            <option class="" disabled selected>Select File Type</option>
                            @foreach ($file_types as $type)
                                <option value="{{ $type->id }}" {{ old('file_type_id', $file->file_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->is_global ? '🌐' : '🏛' }} {{ $type->name }} ({{ $type->code }})                          
                                </option>
                            @endforeach
                    </select>
                    @if ($isRegistry)
                        <div class="mt-2">
                            <div class="flex items-center justify-between text-xs text-cyan-600">
                                <span>No suitable file type?</span>
                                <a href="{{ route('registry.file-types.create') }}" 
                                    class="inline-flex items-center gap-1 font-medium text-cyan-600 hover:text-cyan-800 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" 
                                        class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add file type
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- File Category -->
                <div>
                    <label for="category_id" class="block">
                        Category
                    </label>
                    <select name="category_id" 
                            id="category_id" 
                            class="select2 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" 
                    >
                        <option value="" disabled selected>Select Document Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $file->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block">Due Date 
                    <input type="date" name="due_date" min="{{ date('Y-m-d') }}"  value="{{ old('due_date', $file->due_date) }}"class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" value="{{ old('due_date') }}">
                </div>
            </div>

            {{-- @if(Auth::user()->hasRole('registry')) --}}
                <div class="pt-6 border-t border-gray-300 mt-6 text-gray-700 space-y-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Document Type <span class="text-red-600">*</span>
                    </label>

                    <div class="flex flex-col md:flex-row gap-4">
                        <label id="upload-option" 
                            class="flex items-center gap-3 border border-gray-300 rounded-xl px-4 py-3 cursor-pointer hover:bg-gray-50">
                            
                            <input type="radio" 
                                name="document_source" 
                                value="upload"
                                class="text-cyan-600 focus:ring-cyan-500" checked>
                            <div>
                                <div class="text-sm font-medium text-gray-800">Upload Document</div>
                                <div class="text-xs text-gray-500">Upload an existing PDF file</div>
                            </div>
                        </label>
                        {{-- <label id="online-option"
                            class="flex items-center gap-3 border border-gray-300 rounded-xl px-4 py-3 cursor-pointer hover:bg-gray-50 transition">
                            
                            <input type="radio" 
                                name="document_source" 
                                value="online"
                                class="text-cyan-600 focus:ring-cyan-500">
                            <div>
                                <div class="text-sm font-medium text-gray-800">Write Online</div>
                                <div class="text-xs text-gray-500">
                                    Create memorandum using editor
                                </div>
                            </div>
                        </label> --}}
                    </div>
                </div>

                {{-- Upload Section --}}
                <div id="upload-section" class="mt-4 grid grid-cols-1 text-sm text-gray-700">
                    <label for="main_file" class="mb-2 block font-medium">
                        {{ $file->main_file_path ? 'Replace Main Document' : 'Upload Main Document' }}
                    </label>

                    <input type="file" name="main_file" id="main_file" accept="application/pdf"
                        class="block w-full text-sm
                            file:mr-4 file:rounded-md file:border-0 file:px-4 file:py-2
                            file:bg-cyan-50 file:text-sm file:font-semibold file:text-cyan-700
                            hover:file:bg-cyan-100"
                        aria-describedby="main-file-help">
                     @if ($file->main_file_path)
                        <p class="mb-2 mt-2">
                            Existing Document:
                            <span class="font-medium">{{ $file->main_file_name }}</span>
                        </p>
                    @endif

                    @error('main_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                 {{-- Template Options --}}
                <div id="template-section" class="hidden mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Choose Template <span class="text-red-600">*</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Memorandum --}}
                        <label class="template-option border border-gray-300 rounded-xl p-4 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="correspondence_type" value="memo" class="template-radio hidden">

                            <div class="text-sm font-semibold text-gray-800">Memorandum</div>

                            <div class="text-xs text-gray-500 mt-1">
                                To government ministries, agencies and organisations registered in the Government Establishment Register (ER)
                            </div>
                        </label>

                        {{-- Official Letter --}}
                        <label class="template-option border border-gray-300 rounded-xl p-4 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="correspondence_type" value="letter" class="template-radio hidden">

                            <div class="text-sm font-semibold text-gray-800">Official Letter</div>

                            <div class="text-xs text-gray-500 mt-1">
                                To external bodies (e.g diplomatic missions, development partners, private sector, communities, churches & SOEs)
                            </div>
                        </label>

                        {{-- Internal Memorandum --}}
                        <label class="template-option border border-gray-300 rounded-xl p-4 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="correspondence_type" value="internal" class="template-radio hidden">
                            <div class="text-sm font-semibold text-gray-800">Internal Memorandum</div>

                            <div class="text-xs text-gray-500 mt-1">
                                Only for official internal ministry communications.
                            </div>
                        </label>
                    </div>

                    <div id="ministries-container" class="hidden mt-6">
                        <div class="border border-gray-200 p-3 bg-gray-50 space-y-5">
                            <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                                Memorandum Metatable Fields
                            </h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Recipient Ministries
                                </label>

                                <div class="max-h-64 overflow-y-auto border border-gray-300 bg-white rounded-md p-3 space-y-2">
                                    <label class="flex items-start gap-2 cursor-pointer border-b border-gray-200 pb-2 mb-2">
                                        <input type="checkbox"
                                            id="select-all-ministries"
                                            class="mt-1 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">

                                        <span class="text-sm font-semibold text-gray-700">
                                            All Ministries
                                        </span>
                                    </label>
                                    @foreach($ministries as $ministry)
                                        @if($ministry->id != Auth::user()->organisation_id)
                                            <label class="flex items-start gap-2 cursor-pointer">
                                                <input type="checkbox"
                                                    name="memo_recipients[]"
                                                    value="{{ $ministry->id }}"
                                                    class="ministry-checkbox mt-1 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"
                                                    @checked(in_array($ministry->id, old('memo_recipients', [])))
                                                >

                                                <span class="text-sm text-gray-700">
                                                    {{ $ministry->reviewer_title }}

                                                    <span class="text-gray-500">
                                                        - {{ $ministry->name }}
                                                    </span>
                                                </span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                                @error('memo_recipients')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label for="memo_from_field" class="block text-sm font-medium text-gray-700 mb-2">
                                        From
                                    </label>
                                    @if($officerInCharge)
                                        <input type="text"
                                            name="memo_from_field"
                                            id="memo_from_field"
                                            value="Officer In Charge"
                                            readonly
                                            class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                            >
                                    @else
                                        <input type="text"
                                            name="memo_from_field"
                                            id="memo_from_field"
                                            value="Secretary"
                                            readonly
                                            class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                            >
                                    @endif

                                </div>
                                <div>
                                    <label for="memo_attention_to" class="block text-sm font-medium text-gray-700 mb-2">
                                        Attention to officer
                                    </label>
                                    <input type="text"
                                        name="memo_attention_to"
                                        id="memo_attention_to"
                                        class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        >
                                </div>
                                <div>
                                    <label for="memo_cc_field" class="block text-sm font-medium text-gray-700 mb-2">
                                        CC officer
                                    </label>
                                    <input type="text"
                                        name="memo_cc_field"
                                        id="memo_cc_field"
                                        class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        >
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Official Letter Recipients --}}
                    <div id="recipient-container" class="hidden mt-6">
                        <div class="border border-gray-200 p-3 space-y-5">
                            <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                                Select Official Letter Recipients
                            </h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Registered organisations
                                </label>

                                <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-md p-3 space-y-2">
                                    @foreach($notMinistriesOrgs as $org)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox"
                                                name="registered_organisations[]"
                                                value="{{ $org->id }}"
                                                class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"
                                                @checked(in_array($org->id, old('registered_organisations', [])))
                                            >

                                            <span class="text-sm text-gray-700">
                                                {{ $org->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <a href="{{ route('registry.external-partners.create') }}"
                                    target="_blank"
                                    class="text-xs mt-3 text-cyan-600 hover:text-cyan-800 hover:text-cyan-800 hover:underline">
                                        Organisation not listed? + Add External Partner
                                </a>

                                @error('registered_organisations')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @if($externalPartners->isNotEmpty())
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Ministry External Partners
                                    </label>
                                    <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-md p-3 space-y-2">
                                        @foreach($externalPartners as $partner)
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox"
                                                    name="external_partners[]"
                                                    value="{{ $partner->id }}"
                                                    class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"
                                                    @checked(in_array($partner->id, old('external_partners', [])))
                                                >
                                                <span class="text-sm text-gray-700">
                                                    {{ $partner->name }} {{ $partner->code ? "- $partner->code" : "" }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- Internal letter values --}}
                    <div id="internal-container" class="hidden mt-6">
                        <div class="bg-gray-50 border border-gray-200 p-3 space-y-5">
                            <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                                Internal Memo Metatable Fields
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label for="internal_from_field" class="block text-sm font-medium text-gray-700 mb-2">
                                        From
                                    </label>
                                        <input type="text"
                                            name="internal_from_field"
                                            id="internal_from_field"
                                            value="{{ auth()->user()->name }} - {{ auth()->user()->designation ?? '' }}"
                                            readonly
                                            class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        >
                                </div>
                                <div>
                                    <label for="internal_to_field" class="block text-sm font-medium text-gray-700 mb-2">
                                        To
                                    </label>
                                    @if($officerInCharge)
                                        <input type="text"
                                            name="internal_to_field"
                                            id="internal_to_field"
                                            value="Officer in Charge"
                                            readonly
                                            class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                            >
                                    @else 
                                        <input type="text"
                                            name="internal_to_field"
                                            id="internal_to_field"
                                            value="Secretary"
                                            readonly
                                            class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                            >
                                    @endif
                                </div>
                                <div>
                                    <label for="internal_ufs_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Select UFS Officer
                                    </label>

                                    <select name="internal_ufs_id"
                                        id="internal_ufs_id"
                                        class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">

                                        <option value="">Select UFS Officer</option>

                                        @foreach($usersWithDivision as $officer)
                                            @if($officer->id !== Auth::user()->id)
                                                <option value="{{ $officer->id }}"
                                                    @selected(old('ufs_id') == $officer->id)>
                                                    {{ $officer->first_name }} {{ $officer->last_name }}
                                                    @if($officer->designation)
                                                        - {{ $officer->designation }}
                                                    @endif
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>

                                    @error('ufs_id')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="internal_cc_field" class="block text-sm font-medium text-gray-700 mb-1">
                                    Cc'd Officer
                                    </label>
                                    <input type="text"
                                        name="internal_cc_field"
                                        id="internal_cc_field"
                                        class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        >

                                    @error('internal_cc_field')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div id="editor-section" class="hidden mt-6">
                        <label class="block text-md font-medium text-gray-700 mb-2">
                            Content
                        </label>
                        <textarea name="content" id="editor">{!! old('content', $file->content ?? '') !!}</textarea>
                        @error('content')
                            <p class="mt-1 text-md text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


        <!-- Additional Files Section -->
        @if (!empty($file->additional_file_paths))
            <div class="mb-4 mt-4 text-sm">
                <p class="font-medium text-gray-700">Current supporting documents</p>

                <ul class="mt-2 space-y-2">
                    @foreach ($file->additional_file_paths as $attachment)
                        @continue(empty($attachment['file_path']))

                        <li class="flex items-center justify-between gap-3 rounded border p-2"
                            data-attachment-row>
                            <span data-attachment-name>
                                {{ $attachment['name'] ?? 'Uploaded attachment' }}
                            </span>

                            <input type="hidden" name="remove_additional_files[]"
                                value="{{ $attachment['file_path'] }}"
                                data-remove-path disabled>

                            <button type="button"
                                    class="text-red-700 underline"
                                    data-remove-attachment
                                    aria-pressed="false">
                                Remove
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="file-upload-container" class="space-y-4">
            <div class="file-upload-item relative text-sm">
                <label for="file_1" class="mt-4 block">
                    Add supporting documents (PDF)
                </label>

                <input
                    type="file"
                    name="additional_files[]"
                    id="file_1"
                    accept="application/pdf"
                    class="block w-full text-sm text-gray-600
                        file:mr-4 file:border-0 file:bg-cyan-50
                        file:px-4 file:py-2 file:text-sm file:font-semibold
                        file:text-cyan-700 hover:file:bg-cyan-100"
                >
            </div>

            <button
                type="button"
                id="add-file-button"
                class="inline-flex w-full justify-start text-sm text-cyan-700 underline"
            >
                + Supporting Document
            </button>
        </div>

        <div class="border-t border-gray-200 pt-4 mt-8 flex justify-left gap-3">
            <button type="submit" class="mt-2 mb-2 justify-left bg-cyan-600 text-white py-3 px-10 rounded-md hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-700 focus:ring-offset-2">
                Edit File
            </button>
        </div>
    </form>
</div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

    <script>
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: [
                    'bold',
                    'italic',
                    'underline',
                    '|',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'insertTable',
                    '|',
                    'undo',
                    'redo'
                ]
            })
           
            .catch(error => {
                console.error(error);
            });
    </script>

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

        document.querySelectorAll('[data-remove-attachment]').forEach((button) => {
            button.addEventListener('click', () => {
                const row = button.closest('[data-attachment-row]');
                const input = row.querySelector('[data-remove-path]');
                const markedForRemoval = input.disabled;

                input.disabled = !markedForRemoval;
                row.querySelector('[data-attachment-name]')
                    .classList.toggle('line-through', markedForRemoval);

                button.textContent = markedForRemoval ? 'Undo remove' : 'Remove';
                button.setAttribute('aria-pressed', String(markedForRemoval));
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

    @if ($isRegistry)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sourceType = document.getElementById('source_type');
                const divisionSelect = document.getElementById('division');
                const orgSelect = document.getElementById('organisation');
                const partnerSelect = document.getElementById('partner');

                const fields = {
                    own_ministry: {
                        container: document.getElementById('division-select-container'),
                        select: divisionSelect,
                    },
                    identity_organisation: {
                        container: document.getElementById('org-select-container'),
                        select: orgSelect,
                    },
                    external_partner: {
                        container: document.getElementById('partner-select-container'),
                        select: partnerSelect,
                    },
                };

                const divisions = @json($divisions);
                const organisations = @json($organisations);
                const partners = @json($externalPartners);

                divisions.forEach(division => {
                    divisionSelect.add(new Option(division.name, division.id));
                });

                const organisationGroups = new Map();

                organisations.forEach(organisation => {
                    const typeName = organisation.type?.name ?? 'Other';

                    if (!organisationGroups.has(typeName)) {
                        const group = document.createElement('optgroup');
                        group.label = typeName;
                        orgSelect.appendChild(group);
                        organisationGroups.set(typeName, group);
                    }

                    organisationGroups.get(typeName).appendChild(
                        new Option(`${organisation.code} - ${organisation.name}`, organisation.id)
                    );
                });

                partners.forEach(partner => {
                    partnerSelect.add(new Option(partner.name, partner.id));
                });

                Object.values(fields).forEach(({ select }) => {
                    const selectedValue = select.dataset.selected ?? '';

                    if (Array.from(select.options).some(option => option.value === selectedValue)) {
                        select.value = selectedValue;
                    }
                });

                function updateSourceFields() {
                    Object.entries(fields).forEach(([type, { container, select }]) => {
                        const isActive = sourceType.value === type;

                        container.hidden = !isActive;
                        select.disabled = !isActive;
                        select.required = isActive;
                    });
                }

                sourceType.addEventListener('change', updateSourceFields);
                updateSourceFields();
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isRegistry = @json(auth()->user()->hasRole('registry'));

            const sourceTypeSelect = document.getElementById('source_type');
            const organisationSelect = document.getElementById('organisation');

            const sourceRadios = document.querySelectorAll(
                'input[name="document_source"]'
            );

            const uploadRadio = document.querySelector(
                'input[name="document_source"][value="upload"]'
            );

            const onlineRadio = document.querySelector(
                'input[name="document_source"][value="online"]'
            );

            const uploadOption = document.getElementById('upload-option');
            const onlineOption = document.getElementById('online-option');

            const uploadSection = document.getElementById('upload-section');
            const templateSection = document.getElementById('template-section');
            const editorSection = document.getElementById('editor-section');

            const templateOptions = document.querySelectorAll('.template-option');
            const ministriesContainer = document.getElementById('ministries-container');
            const recipientContainer = document.getElementById('recipient-container');
            const internalContainer = document.getElementById('internal-container');

            function setOptionDisabled(option, radio, disabled) {
                radio.disabled = disabled;

                if (disabled) {
                    radio.checked = false;
                }

                if (!option) return;

                option.classList.toggle('opacity-50', disabled);
                option.classList.toggle('cursor-not-allowed', disabled);
                option.classList.toggle('bg-gray-100', disabled);

                option.classList.toggle('cursor-pointer', !disabled);
                option.classList.toggle('hover:bg-gray-50', !disabled);
            }

            function updateTemplateFields() {
                const showOnline = onlineRadio.checked && !onlineRadio.disabled;

                const selectedTemplate = document.querySelector(
                    '.template-option input[type="radio"]:checked'
                );

                const template = selectedTemplate?.value;

                templateOptions.forEach(option => {
                    const radio = option.querySelector('input[type="radio"]');
                    const selected = radio?.checked ?? false;

                    option.classList.toggle('border-cyan-500', selected);
                    option.classList.toggle('bg-cyan-50', selected);
                });

                ministriesContainer.classList.toggle(
                    'hidden',
                    !showOnline || template !== 'memo'
                );

                recipientContainer.classList.toggle(
                    'hidden',
                    !showOnline || template !== 'letter'
                );

                internalContainer.classList.toggle(
                    'hidden',
                    !showOnline || template !== 'internal'
                );

                editorSection.classList.toggle(
                    'hidden',
                    !showOnline || !['memo', 'letter', 'internal'].includes(template)
                );
            }

            function updateSections() {
                const showUpload =
                    uploadRadio.checked && !uploadRadio.disabled;

                const showOnline =
                    onlineRadio.checked && !onlineRadio.disabled;

                uploadSection.classList.toggle('hidden', !showUpload);
                templateSection.classList.toggle('hidden', !showOnline);

                uploadSection.querySelectorAll('input, select, textarea, button')
                    .forEach(input => {
                        input.disabled = !showUpload;
                    });

                updateTemplateFields();
            }

            function updateDocumentSourceOptions() {
                const sourceType = sourceTypeSelect?.value;

                const disableUpload = !isRegistry;

                const disableOnline = isRegistry && (
                    sourceType === 'identity_organisation' ||
                    sourceType === 'external_partner'
                );

                setOptionDisabled(uploadOption, uploadRadio, disableUpload);
                setOptionDisabled(onlineOption, onlineRadio, disableOnline);

                // Keep an allowed selection, or choose an available default.
                if (!uploadRadio.checked && !onlineRadio.checked) {
                    if (!onlineRadio.disabled) {
                        onlineRadio.checked = true;
                    } else if (!uploadRadio.disabled) {
                        uploadRadio.checked = true;
                    }
                }

                updateSections();
            }

            sourceRadios.forEach(radio => {
                radio.addEventListener('change', updateSections);
            });

            templateOptions.forEach(option => {
                const radio = option.querySelector('input[type="radio"]');

                option.addEventListener('click', function () {
                    if (!radio || radio.disabled || !onlineRadio.checked) {
                        return;
                    }

                    radio.checked = true;
                    updateTemplateFields();
                });

                radio?.addEventListener('change', updateTemplateFields);
            });

            sourceTypeSelect?.addEventListener(
                'change',
                updateDocumentSourceOptions
            );

            organisationSelect?.addEventListener(
                'change',
                updateDocumentSourceOptions
            );

            updateDocumentSourceOptions();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('select-all-ministries');
            const ministryCheckboxes = document.querySelectorAll('.ministry-checkbox');

            // Select / deselect all
            selectAll.addEventListener('change', function () {
                ministryCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });
            });

            // Keep "All Ministries" in sync
            ministryCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    selectAll.checked =
                        ministryCheckboxes.length > 0 &&
                        [...ministryCheckboxes].every(cb => cb.checked);
                });
            });

            // Useful when old() returns all ministries after validation failure
            selectAll.checked =
                ministryCheckboxes.length > 0 &&
                [...ministryCheckboxes].every(cb => cb.checked);
        });
    </script>

@endsection
