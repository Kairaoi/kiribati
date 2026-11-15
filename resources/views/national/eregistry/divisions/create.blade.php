@extends('layouts.app')

@section('content')
<div class="container mx-auto shadow mt-6 mb-10 rounded-lg px-4 py-6 max-w-3xl">
    <h1 class="flex mt-4 text-xl font-bold mb-6">Create New Division</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('registry.divisions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-1 gap-3">

             <div>
                @if ($isSystemAdmin)
                    <div>
                        <label
                            for="ministry_id"
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
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
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Organisation
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
                            name="organisation_id"
                            value="{{ $user->organisation_id }}"
                        >
                    </div>
                @endif
            </div>


            <!-- Division Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Division Name: <span class="text-red-600">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                <textarea name="description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-gray-700">Location: <span class="text-red-600">*</span></label>
                <input type="text" name="location" id="location" value="{{ old('location') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone:</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm">
                          
            </div>

            <div>
                <button type="submit" class="mt-4 mb-4 bg-cyan-600 text-white py-2 px-4 rounded-md hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                    Create Division
                </button>
            </div>
        </div>
    </form>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  
</div>
@endsection
