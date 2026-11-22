@extends('layouts.app')

@section('content')
<div class="container mx-auto shadow mt-6 mb-10 rounded-lg px-4 py-6 max-w-3xl">
    <h1 class="flex mt-4 text-xl font-bold mb-6">Assign Temporary HOD</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="mb-6 rounded-lg border border-gray-200 bg-white p-4">
        <h2 class="text-lg font-semibold text-gray-900">Current Temporary HOD</h2>

        <div class="mt-3 space-y-3">
            @forelse ($currentAppointments as $appointment)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-md border border-gray-200 p-3">
                    <div>
                        <p class="font-medium text-gray-900">
                            {{ $appointment->officer->name }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ $appointment->division->name }}
                            · {{ $appointment->starts_at->format('d M Y') }}
                            to {{ $appointment->ends_at->format('d M Y') }}
                        </p>
                    </div>

                    <form method="POST"
                        action="{{ route('registry.hod-appointments.revoke', $appointment) }}"
                        onsubmit="return confirm('Revoke this temporary HOD appointment?');">
                        @csrf
                        @method('PATCH')

                        <button type="submit"
                                class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                            Revoke
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-gray-500">There are no active temporary HOD appointments.</p>
            @endforelse
        </div>
    </section>
    <form action="{{ route('registry.hod-appointments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-1 gap-3">
             <div>
                @if ($isRegistry)
                    <div>
                        <label for="division_id" class="mb-1 block text-sm font-medium text-gray-700">
                            Divisions
                        </label>

                        <select name="division_id" id="division_id" required class="mt-1 text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                            <option value="">Select division</option>
                            @foreach ($divisions as $div)
                                <option value="{{ $div->id }}"
                                        @selected(old('division_id') == $div->id)>
                                        {{ $div->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('division_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @else
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Division
                        </label>
                        <input type="text" value="{{ $user->division->name }}" readonly class="mt-1 block text-sm w-full rounded-md border-gray-300 bg-gray-100 text-gray-600 shadow-sm">
                    </div>
                @endif
            </div>

            <div>
                <label for="user_id" class="mb-1 block text-sm font-medium text-gray-700">
                    Officer
                </label>

                <select name="user_id" id="user_id" required class="mt-1 text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                    <option value="">Select officer</option>
                    @foreach ($users as $officer)
                        <option
                            value="{{ $officer->id }}"
                            data-division-id="{{ $officer->division_id }}"
                            @selected(old('user_id') == $officer->id)
                        >
                            {{ $officer->name }}
                        </option>
                    @endforeach
                </select>

                @error('user_id')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label for="start_date" class="mb-1 block text-sm font-medium text-gray-700">
                        Start date
                    </label>

                    <input
                        type="date"
                        name="start_date"
                        id="start_date"
                        value="{{ old('start_date') }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                    >

                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="mb-1 block text-sm font-medium text-gray-700">
                        End date
                    </label>

                    <input
                        type="date"
                        name="end_date"
                        id="end_date"
                        value="{{ old('end_date') }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                    >

                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            
            <div>
                <button type="submit" class="mt-4 mb-4 bg-cyan-600 text-white py-2 px-4 rounded-md hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                    Submit
                </button>
            </div>
        </div>
    </form>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const divisionSelect = document.getElementById('division_id');
            const officerSelect = document.getElementById('user_id');

            if (!divisionSelect || !officerSelect) {
                return;
            }

            const placeholder = officerSelect.options[0];
            const officerOptions = Array.from(officerSelect.options).slice(1);

            function loadOfficers(selectedOfficerId = '') {
                const divisionId = divisionSelect.value;
                console.log('Selected division:', divisionId);
                console.table(officerOptions.map((option) => ({
                    officer: option.textContent.trim(),
                    divisionId: option.dataset.divisionId,
                })));
                const matchingOfficers = officerOptions.filter(
                    (option) => divisionId && option.dataset.divisionId === divisionId
                );

                officerSelect.replaceChildren(placeholder, ...matchingOfficers);
                officerSelect.value = matchingOfficers.some(
                    (option) => option.value === selectedOfficerId
                ) ? selectedOfficerId : '';

                officerSelect.disabled = !divisionId;
            }

            loadOfficers(officerSelect.value);

            divisionSelect.addEventListener('change', () => {
                loadOfficers();
            });
        });
    </script>
  
</div>
@endsection
