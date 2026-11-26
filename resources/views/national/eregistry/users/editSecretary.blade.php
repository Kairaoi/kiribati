@extends('layouts.app')

@section('content')

{{-- <div class="container mx-auto font-roboto px-8 max-w-5xl mt-1"> --}}
<div class="max-w-3xl mx-auto mt-6 mb-10 px-4 space-y-6">

    @php
        $reviewerTitle = $organisation->reviewer_title ?? 'Senior Review Officer';
    @endphp

    {{-- Page Heading --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            {{ $reviewerTitle }}
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Manage the {{ $reviewerTitle }} for your organisation.
        </p>
    </div>

    {{-- Current Officer --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">

            <h2 class="text-sm font-semibold text-gray-700">
                Current {{ $reviewerTitle }}
            </h2>

            <span class="inline-flex items-center gap-1.5 px-3 py-1
                         rounded-full text-xs font-medium
                         {{ $sro
                            ? 'bg-green-50 text-green-700'
                            : 'bg-red-50 text-red-700' }}">

                <span class="w-2 h-2 rounded-full
                             {{ $sro ? 'bg-green-500' : 'bg-red-500' }}">
                </span>

                {{ $sro ? 'Assigned' : 'Not assigned' }}
            </span>

        </div>

        <div class="p-6">
            <div class="flex items-center gap-4">

                <div class="flex shrink-0 items-center justify-center
                            w-14 h-14 rounded-full
                            {{ $sro
                                ? 'bg-cyan-100 text-cyan-700'
                                : 'bg-red-100 text-red-600' }}">

                    <i class="fa-solid {{ $sro
                        ? 'fa-user-tie'
                        : 'fa-user-xmark' }} text-xl"
                        aria-hidden="true"></i>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $sro?->name ?? 'Not assigned yet' }}
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $sro?->division?->name ?? 'No division assigned' }}
                    </p>
                </div>

            </div>
        </div>

    </div>

    {{-- Officer Assignment --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100">

            <h2 class="text-base font-semibold text-gray-800">
                {{ $sro ? 'Change' : 'Assign' }} {{ $reviewerTitle }}
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Select an officer to serve as the {{ $reviewerTitle }}.
            </p>

        </div>

        <form method="POST"
              action="{{ route('registry.users.update-secretary') }}">

            @csrf
            @method('PATCH')

            <div class="p-6 space-y-5">

                {{-- Officer Selection --}}
                <div>
                    <label for="secretary_id"
                           class="block mb-2 text-sm font-medium text-gray-700">

                        Select {{ $reviewerTitle }}
                        <span class="text-red-500">*</span>

                    </label>

                    <select name="secretary_id"
                            id="secretary_id"
                            required
                            class="w-full rounded-lg border-gray-300
                                   px-4 py-2.5 text-sm text-gray-700
                                   shadow-sm focus:border-cyan-500
                                   focus:ring-cyan-500">

                        <option value="">
                            Select an officer
                        </option>

                        @foreach ($usersWithDivision as $user)
                            @if ($user->id !== $sro?->id)
                                <option value="{{ $user->id }}"
                                    @selected(old('secretary_id') == $user->id)>

                                    {{ $user->name }} -
                                    {{ $user->division_name ?? 'No Division' }}

                                </option>
                            @endif
                        @endforeach

                    </select>

                    @error('secretary_id')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="flex flex-wrap justify-end items-center gap-3
                        px-6 py-4 bg-gray-50 border-t border-gray-100">

                <a href="{{ route('registry.files.index') }}"
                   class="inline-flex items-center justify-center
                          px-5 py-2.5 rounded-lg border border-gray-300
                          bg-white text-sm font-medium text-gray-700
                          hover:bg-gray-100 transition">

                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2
                               px-5 py-2.5 rounded-lg bg-cyan-600
                               text-white text-sm font-semibold shadow-sm
                               hover:bg-cyan-700 transition">

                    <i class="fa-solid fa-user-check" aria-hidden="true"></i>

                    {{ $sro ? 'Update' : 'Assign' }} {{ $reviewerTitle }}

                </button>

            </div>

        </form>
    </div>

</div>

@endsection