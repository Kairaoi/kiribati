@extends('layouts.app')

@section('content')
<div class="container mx-auto font-montserrat px-4 py-6 max-w-3xl">
    <h1 class="flex mt-4 text-xl font-bold mb-6">Create New Unit</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('registry.units.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-1 gap-3">

              <!-- Ministry -->
            <div>
                <label for="ministry_id" class="block text-sm font-medium text-gray-700">Ministry/Public Body: <span class="text-red-600">*</span></label>
                @role('registry')
                    <input type="text"
                        id="ministry_name"
                        value="{{ $ministryName }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700 sm:text-sm"
                        readonly>
                @endrole
                @hasrole('system-admin')
                        <select name="ministry_id" id="ministry_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm"
                            required>
                            <option value="">-- Select Ministry --</option>

                            @foreach($ministries as $ministry)
                                <option value="{{ $ministry->id }}" {{ old('ministry_id') == $ministry->id ? 'selected' : '' }}>
                                    {{ $ministry->name }}
                                </option>
                            @endforeach
                        </select>
                @endhasrole
            </div>
          
            <div>
                <label for="division_name" class="block text-sm font-medium text-gray-700">Division: <span class="text-red-600">*</span></label>
                    @role('registry')
                        <input type="text"
                            id="division_name"
                            value="{{ $divisionName }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700 sm:text-sm"
                            readonly>
                    @endrole
                    <input type="hidden" name="division_id" value="{{ $divisionId }}">
            </div> 

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name of unit: <span class="text-red-600">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
            </div>         
                          
        </div>
            <button type="submit" class="w-full bg-cyan-600 text-white py-2 px-4 rounded-md hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                Create Unit
            </button>
    </form>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</div>
@endsection
