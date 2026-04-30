@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-3xl font-bold text-gray-900 tracking-wide">Review file and assign officer(s)</h3>
        <a href="{{ route('registry.file-circulations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md transition duration-150 ease-in-out">
            <span>Back to Files</span>
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-base">
            
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">File Name:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $file->name }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">From Organisation:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    {{ optional($file->organisation)->name ?? 'No Organisation specified' }}
                </dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">From Division:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    {{ optional($file->division)->name ?? 'No division specified' }}
                </dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">File Type:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $file->fileType->name }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">File Date: </dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $file->created_at->format('M d, Y') }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Circulation Status:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    @php $loggedInOrganisationId = auth()->user()->organisation_id; @endphp

                    @foreach ($file->recipientMinistries as $organisation)
                        @if ($organisation->id == $loggedInOrganisationId)
                            @php
                                switch ($organisation->pivot->status) {
                                    case 'Dispatched':
                                        $badgeClass = 'bg-cyan-500 text-white px-2 py-1 rounded';
                                        break;
                                    case 'Circulated':
                                        $badgeClass = 'bg-yellow-500 text-white px-2 py-1 rounded';
                                        break;
                                    case 'Assigned To Officer':
                                        $badgeClass = 'bg-cyan-300 text-gray-700 px-2 py-1 rounded';
                                        break;
                                    default:
                                        $badgeClass = 'bg-gray-400 text-white px-2 py-1 rounded';
                                }
                            @endphp

                            <div class="mb-2">
                                {{-- <span class="font-semibold">{{ $organisation->name }}:</span> --}}
                                <span class="{{ $badgeClass }}">{{ $organisation->pivot->status }}</span>
                            </div>
                        @endif
                    @endforeach

                </dd>
            </div>
        </dl>
    </div>

    <div class="pdf-viewer bg-white rounded-lg shadow-md p-2">
        @php
            $extension = strtolower(pathinfo($file->main_file_path, PATHINFO_EXTENSION));
            $mainFileUrl = route('registry.files.view', ['id' => $file->id]);
        @endphp

        @if($extension === 'pdf')
            <embed src="{{ $mainFileUrl }}" type="application/pdf" width="100%" height="600px">
        @else
            <p>Main file cannot be previewed.</p>
        @endif

        <div class="mt-4">
            <h3 class="font-semibold">Download Additional Files:</h3>
            <ul class="list-disc pl-5">
                @for ($i = 1; $i <= 3; $i++)
                    @php
                        $field = 'additional_file' . $i . '_path';
                    @endphp
                    @if (!empty($file->$field))
                        <li>
                            <a href="{{ route('registry.files.download.additional', ['id' => $file->id, 'number' => $i]) }}"
                            class="text-cyan-500 hover:underline">
                                Download Additional File {{ $i }}
                            </a>
                        </li>
                    @endif
                @endfor
            </ul>
        </div>
    </div>

    {{-- Stored as a Dispatch Instance when Dispatch button is clicked --}}
    <div class="flex items-center justify-between mt-6">     
        <form method="POST" action="{{ route('registry.file-circulations.store.assigned-officers', $fileCirculation) }}" class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            @csrf 

            <input type="hidden" name="file_id" value="{{ $file->id }}">
            <input type="hidden" name="to_organisation_id" value="{{ auth()->user()->organisation_id }}">

            @php
            $isCirculated = $file->recipientMinistries
                ->where('id', auth()->user()->organisation_id)
                ->pluck('pivot.status')
                ->contains('Circulated');
            @endphp

            
            <label for="assignedOfficers" class="block text-xl font-medium text-cyan-600 mb-2">Select Responsible Officers:</label>
            
            <select name="assignedOfficers[]" id="assignedOfficers" multiple class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-cyan-200">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" data-name="{{ $user->first_name }} {{ $user->last_name }}">
                        {{ $user->division_name }} - {{ $user->first_name }} {{ $user->last_name }} </option>
                @endforeach
            </select>

            <!-- Display selected names -->
            <div id="selected-officers-display" class="mt-2 text-gray-700 font-semibold"></div>

            <br class="my-4 border-t border-gray-300">
            <button type="submit"
                title="{{ $isCirculated ? '' : 'Forwarding is only allowed when status is Circulated' }}"
                class="w-full {{ $isCirculated ? 'bg-cyan-600 hover:bg-cyan-900' : 'bg-gray-400 cursor-not-allowed' }} text-white font-semibold py-2 px-4 rounded-md shadow-sm transition duration-200"
                {{ $isCirculated ? '' : 'disabled' }}>
                Forward
            </button>
        </form>

        <script>
            const selectElement = document.getElementById('assignedOfficers');
            const displayElement = document.getElementById('selected-officers-display');

            function updateSelectedOfficersDisplay() {
                const selectedOptions = Array.from(selectElement.selectedOptions);
                const names = selectedOptions.map(option => option.getAttribute('data-name'));
                displayElement.textContent = names.join(', ');
            }

            // Initial update
            updateSelectedOfficersDisplay();

            // Update whenever selection changes
            selectElement.addEventListener('change', updateSelectedOfficersDisplay);
        </script>
    </div>



@endsection

