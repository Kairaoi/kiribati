@extends('layouts.app')

@section('content')
<div class="container mt-6 mb-6 mx-auto shadow rounded-lg px-4 py-6 max-w-5xl">
    <h1 class="flex mt-2 text-xl font-bold mb-6">Create User</h1>

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
                        <label for="ministry_id" class="mb-1 block text-sm font-medium text-gray-700">
                            Organisation
                        </label>

                        <select
                            name="organisation_id"
                            id="organisation_id"
                            required
                            class="mt-1 text-sm block w-full rounded-md border-gray-300
                                shadow-sm focus:border-cyan-500
                                focus:ring-cyan-500"
                        >
                            <option value="">Select organisation</option>

                            @foreach ($organisations as $organisation)
                                <option
                                    value="{{ $organisation->id }}"
                                    @selected(
                                        old('organisation_id') == $organisation->id
                                    )
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
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Organisation
                        </label>

                        <input type="text"
                            value="{{ $user->organisation->name }}"
                            readonly
                            class="mt-1 block text-sm w-full rounded-md border-gray-300
                                bg-gray-100 text-gray-600 shadow-sm"
                        >

                        <input
                            type="hidden"
                            id="organisation_id"
                            name="organisation_id"
                            value="{{ $user->organisation_id }}"
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
                <label for="role" class="block text-sm font-medium text-gray-700">Select Role: <span class="text-red-700 text-sm"> (NOTE: "admin" role refers to IT admin) </span> <span class="text-red-600">*</span></label> 
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
        <button type="submit" class="mt-4 mb-10 justify-left bg-cyan-600 text-white py-3 px-6 rounded-md hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-700 focus:ring-offset-2">
            Create User
        </button>
    </form>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ministrySelect = document.getElementById('organisation_id');
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
                        String(division.organisation_id) === String(ministryId)
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
