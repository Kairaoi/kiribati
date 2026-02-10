@extends('layouts.app')

@section('content')
<div class="container mx-auto font-montserrat px-4 py-6 max-w-5xl">
    <h1 class="flex justify-center text-2xl font-bold mb-6">Edit User: {{($user->first_name)}} {{($user->last_name)}}</h1>

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

        <!-- Owning Organisation (Logged-in user's organisation)-->
        <div>
            <!-- <label for="name" class="block text-sm font-medium text-gray-700">Division</label> -->
            <input type="hidden" name="organisation_id" id="organisation_id" value="{{ Auth::user()->organisation_id}}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>
       

        <!-- First Name -->
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name: <span class="text-red-600">*</span></label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>


        <!-- Last Name -->
        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name: <span class="text-red-600">*</span></label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>


        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email: <span class="text-red-600">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>


        {{-- <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700">
                Password: <span class="text-red-600">*</span>
            </label>
            <div class="relative">
                <input type="password" name="password" id="password"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required oninput="checkPasswordLength()">
                <p id="password-hint" class="mt-2 text-sm text-red-600 hidden">Password must be at least 8 characters.</p>
            </div>
        </div> --}}


        <!-- Role -->
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700">Select Role: <span class="text-red-600">*</span></label>
            <select name="role" id="role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value=""> Select a role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}"
                        {{ old('role', $user->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>


        <!-- Division -->
        <div>
                <label for="division" class="block text-sm font-medium text-gray-700"> Select division: <span class="text-red-600">*</span></label>
                <select name="division_id" id="division_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    <option value="">Select a division </option>
                    @foreach($divisions as $division)
                        @if($division->organisation_id == Auth::user()->organisation_id)
                            <option value="{{ $division->id }}" 
                                {{ old('division_id', $user) }}>
                                {{ $division->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
        </div>               

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Edit User
        </button>
    </form>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</div>
@endsection
