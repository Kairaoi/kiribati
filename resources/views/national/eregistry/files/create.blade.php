@extends('layouts.app')

@section('content')
{{-- <div class="container mx-auto font-montserrat px-4 py-8 max-w-6xl mt-3 rounded-md min-h-screen"> --}}

{{-- <div class="container mx-auto font-poppins px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('files.create.withType', $createType) }} </div> --}}
<div class="container font-poppins bg-white mx-auto px-6 py-10 max-w-4xl mt-4 mb-4 text-gray-700 font-medium rounded-md min-h-screen border border-gray-600">
     <h2 class="text-lg font-semibold text-gray-800 mb-6">
        Create File
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
            <div class="text-gray-700 text-sm grid grid-cols-1">
                @if(auth()->user()->hasRole('registry'))
                    <div>
                        <label for="source_type">Source Type <span class="text-red-600">*</span></label>
                        <select name="source_type" id="source_type" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                            <option value="">Select Source Type</option>
                            <option value="identity_organisation">Registered Organisation</option>
                            <option value="external_partner">External Partner</option>
                        </select>
                    </div>
                    <div id="org-select-container">
                        <div class="mt-3 flex items-center justify-between mb-1">
                            <label for="organisation" class="mt-1 block text-sm text-gray-700">
                                Organisation Name <span class="text-red-600">*</span>
                            </label>
                        </div>
                        <select 
                            id="organisation" 
                            name="source_id" 
                            class="mt-1 block w-full appearance-none rounded-md border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm text-gray-700 shadow-sm transition duration-200 hover:border-cyan-400 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100"
                        >
                            <option value="">Select Organisation</option>
                            @foreach($identityOrganisations->groupBy(fn($org) => optional($org->type)->name ?? 'Other') as $type => $organisations)
                                <optgroup label="{{ $type }}">
                                    @foreach($organisations as $organisation)
                                        <option value="{{ $organisation->id }}">
                                            {{ $organisation->name }} {{ $organisation->code ?? '' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <a href="{{ route('registry.external-partners.create') }}"
                            target="_blank"
                            class="text-xs mt-3 text-cyan-600 hover:text-cyan-800 hover:text-cyan-800 hover:underline">
                                Organisation not listed? + Add External Partner
                        </a>
                    </div>
                    <div id="partner-select-container" class="mt-3" style="display:none;">
                        <label for="partner">External Partner <span class="text-red-600">*</span></label>
                        <select id="partner" 
                                name="source_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" 
                                data-selected="{{ old('partner_id') }}"
                        >
                            <option value="">Select External Partner</option>
                        </select>
                        <a href="{{ route('registry.external-partners.create') }}"
                            target="_blank"
                            class="text-xs mt-3 text-cyan-600 hover:text-cyan-800 hover:text-cyan-800 hover:underline">
                                Organisation not listed? + Add External Partner
                        </a>
                    </div>
                @elseif(auth()->user()->hasRole(['user', 'sro', 'hod']))
                    <input type="hidden" name="source_type" value="identity_organisation">

                    <input type="hidden" name="source_id" value="{{ auth()->user()->ministry_id }}">
                    
                    <label for="userOrganisation" class="block">
                        Source Organisation <span class="text-red-600">*</span>
                    </label>

                    <input
                        type="text"
                        id="userOrganisation"
                        value="{{ auth()->user()->ministry?->name }}"
                        class="mt-1 mb-3 text-sm block w-full border-gray-300 rounded-md bg-gray-50 text-gray-700 shadow-sm"
                        readonly
                    >
                @endif
                
                <div class="mt-3">
                    <label for="subject" class="block">Name or Subject of File <span class="text-red-600">*</span></label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="mt-1 mb-2 text-sm block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" required>
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
                                <option value="{{ $type->id }}" {{ old('file_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->is_global ? '🌐' : '🏛' }} {{ $type->name }}                           
                                </option>
                            @endforeach
                    </select>
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
                </div>
                <!-- File Category -->
                <div>
                    <label for="category_id" class="block">
                        Category
                        <span class="text-gray-400">(Optional)</span>
                    </label>
                    <select name="category_id" 
                            id="category_id" 
                            class="select2 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" 
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
                    <input type="date" name="due_date" min="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" value="{{ old('due_date') }}">
                </div>
            </div>

            @if(auth()->user()->hasRole('registry'))
                <div class="pt-6 border-t border-gray-300 mt-6 text-gray-700 space-y-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Document Type <span class="text-red-600">*</span>
                    </label>

                    <div class="flex flex-col md:flex-row gap-4">
                        <label class="flex items-center gap-3 border border-gray-300 rounded-xl px-4 py-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="document_source" value="upload"
                                class="text-cyan-600 focus:ring-cyan-500" checked>

                            <div>
                                <div class="text-sm font-medium text-gray-800">Upload Document</div>
                                <div class="text-xs text-gray-500">Upload an existing PDF file</div>
                            </div>
                        </label>

                        <label id="online-option"
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
                        </label>
                    </div>
                </div>

                {{-- Upload Section --}}
                <div id="upload-section" class="mt-4 text-gray-700 text-sm grid grid-cols-1">
                    <label for="main_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Main Document
                    </label>
                    <input type="file" name="main_file" id="main_file" accept="application/pdf"
                        class="block w-full text-sm
                        file:mr-4 file:py-2 file:px-4
                        file:border-0 file:text-sm file:font-semibold
                        file:bg-cyan-50 file:text-cyan-700
                        hover:file:bg-cyan-100">
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
                        <div class="border border-gray-200 p-3 space-y-5">
                            <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                                Metatable Fields
                            </h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Recipient Ministries
                                </label>

                                <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-md p-3 space-y-2">
                                    @foreach($ministries as $ministry)
                                        @if($ministry->id != auth()->user()->ministry_id)
                                            <label class="flex items-start gap-2 cursor-pointer">
                                                <input type="checkbox"
                                                    name="memo_recipients[]"
                                                    value="{{ $ministry->id }}"
                                                    class="mt-1 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"
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
                                    <input type="text"
                                        name="memo_from_field"
                                        id="memo_from_field"
                                        class="mt-1 block w-full border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                </div>
                                <div>
                                    <label for="memo_attention_to" class="block text-sm font-medium text-gray-700 mb-2">
                                        Attention to officer
                                    </label>
                                    <input type="text"
                                        name="memo_attention_to"
                                        id="memo_attention_to"
                                        class="mt-1 block w-full border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                </div>
                                <div>
                                    <label for="memo_cc_field" class="block text-sm font-medium text-gray-700 mb-2">
                                        Cc'd officer
                                    </label>
                                    <input type="text"
                                        name="memo_cc_field"
                                        id="memo_cc_field"
                                        class="mt-1 block w-full border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
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
                                Metatable Fields
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label for="internal_from_field" class="block text-sm font-medium text-gray-700 mb-2">
                                        From
                                    </label>
                                    <input type="text"
                                        name="internal_from_field"
                                        id="internal_from_field"
                                        class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        >
                                </div>
                                <div>
                                    <label for="internal_to_field" class="block text-sm font-medium text-gray-700 mb-2">
                                        To
                                    </label>
                                    <input type="text"
                                        name="internal_to_field"
                                        id="internal_to_field"
                                        class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        >
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
                                            @if($officer->id !== auth()->user()->id)
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
            @else
                <input type="hidden" name="document_source" value="online">
                 {{-- Template Options --}}
                <div id="template-section" class="mt-4">
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
                        <div class="border border-gray-200 p-3 space-y-5">
                            <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                                Metatable Fields
                            </h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Recipient Ministries
                                </label>

                                <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-md p-3 space-y-2">
                                    @foreach($ministries as $ministry)
                                        @if($ministry->id != auth()->user()->ministry_id)
                                            <label class="flex items-start gap-2 cursor-pointer">
                                                <input type="checkbox"
                                                    name="memo_recipients[]"
                                                    value="{{ $ministry->id }}"
                                                    class="mt-1 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"
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
                                    <input type="text"
                                        name="memo_from_field"
                                        id="memo_from_field"
                                        class="mt-1 block w-full border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                </div>
                                <div>
                                    <label for="memo_attention_to" class="block text-sm font-medium text-gray-700 mb-2">
                                        Attention to officer
                                    </label>
                                    <input type="text"
                                        name="memo_attention_to"
                                        id="memo_attention_to"
                                        class="mt-1 block w-full border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                </div>
                                <div>
                                    <label for="memo_cc_field" class="block text-sm font-medium text-gray-700 mb-2">
                                        Cc'd officer
                                    </label>
                                    <input type="text"
                                        name="memo_cc_field"
                                        id="memo_cc_field"
                                        class="mt-1 block w-full border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
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
                                Metatable Fields
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label for="internal_from_field" class="block text-sm font-medium text-gray-700 mb-2">
                                        From
                                    </label>
                                    <input type="text"
                                        name="internal_from_field"
                                        id="internal_from_field"
                                        class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        >
                                </div>
                                <div>
                                    <label for="internal_to_field" class="block text-sm font-medium text-gray-700 mb-2">
                                        To
                                    </label>
                                    <input type="text"
                                        name="internal_to_field"
                                        id="internal_to_field"
                                        class="mt-1 block w-full text-sm border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        >
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
                                            @if($officer->id !== auth()->user()->id)
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
            @endif


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
                    class="w-full inline-flex justify-start text-cyan-700 underline text-sm">
                    + Supporting Document
            </button>
        </div>

        <div class="text-center">
            <button type="submit" class="mt-10 mb-2 w-full bg-cyan-500 text-white py-2 px-8 rounded-lg hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                Create File
            </button>
        </div>
    </form>
</div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

    <script>
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'underline',
                    '|',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'outdent',
                    'indent',
                    '|',
                    'link',
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

            function loadOrganisations() {
                orgContainer.style.display = "block";
                orgSelect.disabled = false;

                reset(orgSelect, "Select Organisation");

                const grouped = {};

                ORGANISATIONS.forEach(o => {
                    const typeName = o.type?.name ?? "Other";

                    if (!grouped[typeName]) {
                        grouped[typeName] = [];
                    }

                    grouped[typeName].push(o);
                });

                Object.keys(grouped).forEach(typeName => {
                    const optgroup = document.createElement("optgroup");
                    optgroup.label = typeName;

                    grouped[typeName].forEach(o => {
                        const opt = document.createElement("option");
                        opt.value = o.id;
                        opt.textContent = o.name;
                        optgroup.appendChild(opt);
                    });

                    orgSelect.appendChild(optgroup);
                });
            }

            function loadPartners() {
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

            sourceType.addEventListener("change", function () {
                hideAll();

                if (this.value === "identity_organisation") {
                    loadOrganisations();
                } else if (this.value === "external_partner") {
                    loadPartners();
                }
            });

            // ✅ Initial state
            hideAll();

            // ✅ Auto-load based on default selected value
            if (sourceType.value === "identity_organisation") {
                loadOrganisations();
            } else if (sourceType.value === "external_partner") {
                loadPartners();
            }

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sourceRadios = document.querySelectorAll('input[name="document_source"]');

            const uploadSection = document.getElementById('upload-section');
            const templateSection = document.getElementById('template-section');
            const editorSection = document.getElementById('editor-section');
            const mainFile = document.getElementById('main_file');

            const templateOptions = document.querySelectorAll('.template-option');
            const ministriesContainer = document.getElementById('ministries-container');
            const recipientContainer = document.getElementById('recipient-container');
            const internalContainer = document.getElementById('internal-container');

            function toggleDocumentSource() {
                const selectedSource = document.querySelector('input[name="document_source"]:checked').value;

                if (selectedSource === 'online') {
                    uploadSection.classList.add('hidden');
                    templateSection.classList.remove('hidden');
                    mainFile.disabled = true;
                } else if (selectedSource === 'upload') {
                    uploadSection.classList.remove('hidden');
                    templateSection.classList.add('hidden');
                    editorSection.classList.add('hidden');
                    ministriesContainer.classList.add('hidden');
                    recipientContainer.classList.add('hidden');
                    internalContainer.classList.add('hidden');
                    mainFile.disabled = false;
                }
            }

            sourceRadios.forEach(radio => {
                radio.addEventListener('change', toggleDocumentSource);
            });

            templateOptions.forEach(option => {
                option.addEventListener('click', function () {
                    templateOptions.forEach(item => {
                        item.classList.remove('border-cyan-500', 'bg-cyan-50');
                    });

                    this.classList.add('border-cyan-500', 'bg-cyan-50');

                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;

                    if (radio.value === 'memo') {
                        ministriesContainer.classList.remove('hidden');
                        editorSection.classList.remove('hidden');
                        internalContainer.classList.add('hidden');
                        recipientContainer.classList.add('hidden');
                    } else if (radio.value === 'letter') {
                        ministriesContainer.classList.add('hidden');
                        internalContainer.classList.add('hidden');
                        recipientContainer.classList.remove('hidden');
                        editorSection.classList.remove('hidden');
                    } else if (radio.value === 'internal') {
                        ministriesContainer.classList.add('hidden');
                        recipientContainer.classList.add('hidden');
                        internalContainer.classList.remove('hidden');
                        editorSection.classList.remove('hidden');
                    } else {
                        ministriesContainer.classList.add('hidden');
                        recipientContainer.classList.add('hidden');
                        internalContainer.classList.add('hidden');
                        editorSection.classList.add('hidden');
                    }
                });
            });

            toggleDocumentSource();
        });
        </script>
        {{-- <script>
            const ufsSelect = document.getElementById('ufs_officer_id');
            const ccSelect = document.getElementById('cc_field');

            function updateCcOptions() {
                const selectedUfs = ufsSelect.value;

                Array.from(ccSelect.options).forEach(option => {
                    option.disabled = false;

                    if (selectedUfs && option.value === selectedUfs) {
                        option.disabled = true;

                        if (ccSelect.value === selectedUfs) {
                            ccSelect.value = '';
                        }
                    }
                });
            }

            ufsSelect.addEventListener('change', updateCcOptions);
            updateCcOptions();
        </script> --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sourceTypeSelect = document.getElementById('source_type');
                const organisationSelect = document.getElementById('organisation');

                const onlineOption = document.getElementById('online-option');
                const onlineRadio = onlineOption.querySelector('input[value="online"]');

                const templateSection = document.getElementById('template-section');

                // logged-in ministry id from backend
                const loggedInMinistryId = @json(auth()->user()->ministry_id);
                function toggleOnlineSection() {
                    const sourceType = sourceTypeSelect.value;
                    const sourceId = organisationSelect.value;

                    const disableOnline =
                        (sourceType === 'identity_organisation' && parseInt(sourceId) !== parseInt(loggedInMinistryId) ) ||
                        (sourceType === 'external_partner');

                    if (disableOnline) {
                        onlineRadio.checked = false;
                        onlineRadio.disabled = true;

                        onlineOption.classList.add(
                            'opacity-50',
                            'pointer-events-none',
                            'bg-gray-100'
                        );

                        // Hide template section
                        templateSection.classList.add('hidden');

                    } else {
                        onlineRadio.disabled = false;

                        onlineOption.classList.remove(
                            'opacity-50',
                            'pointer-events-none',
                            'bg-gray-100'
                        );

                        // Only show template section if Online is selected
                        if (onlineRadio.checked) {
                            templateSection.classList.remove('hidden');
                        }
                    }
                }

                onlineRadio.addEventListener('change', function () {
                    if (this.checked && !this.disabled) {
                        templateSection.classList.remove('hidden');
                    } else {
                        templateSection.classList.add('hidden');
                    }
                });

                sourceTypeSelect.addEventListener('change', toggleOnlineSection);
                organisationSelect.addEventListener('change', toggleOnlineSection);

                toggleOnlineSection();
            });
        </script>
@endsection
