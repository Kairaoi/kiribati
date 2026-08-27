@extends('layouts.app')

@section('content')

{{-- <div class="container mx-auto font-roboto px-8 max-w-5xl mt-1"> --}}
<div class="container mx-w-5xl mx-auto mt-4">
    <div class="max-w-3xl mx-auto bg-white shadow-sm border border-gray-200 rounded-lg p-6">
        <h2 class="text-sm font-semibold text-gray-500 tracking-wide uppercase mb-4">
            {{ $ministry->reviewer_title ?? ''}} Details
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Name</p>
                <p class="text-md font-medium text-slate-700">
                    {{ optional($sro)->name ?? 'Not assigned yet' }}                  
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Division</p>
                <p class="text-md font-medium text-slate-700">
                    {{ optional($sro)->division->name ?? 'N/A' }}
                </p>
            </div>

        </div>
        
    </div>
    <div class="max-w-3xl mx-auto mt-6 mb-4 space-y-4">
        <form method="POST" action="{{ route('registry.users.update-secretary') }}">
            @csrf
            @method('PATCH')
            <select name="secretary_id" class="border border-gray-300 text-sm px-4 py-2 focus:ring focus:ring-cyan-200">
                <option value="">Select New {{ $ministry->reviewer_title ?? ''}}</option>
                    @foreach ($usersWithDivision as $user)
                        @if ($user->id !== optional($sro)->id)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} - {{ $user->division_name ?? 'No Division' }}
                            </option>
                        @endif
                    @endforeach
            </select>

            <button type="submit"
                    class="px-4 py-2 justify-center bg-cyan-600 hover:bg-cyan-700 text-white mt-2 font-semibold">
                Update
            </button>
        </form>
    </div>
</div>

@endsection