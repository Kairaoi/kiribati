@extends('layouts.app')

@section('content')

{{-- <div class="container mx-auto font-roboto px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('dispatches.show', $file) }} </div> --}}
    <div class="max-w-3xl mx-auto bg-white shadow-sm border border-gray-200 rounded-lg p-6 mt-4">
        <h2 class="text-sm font-semibold text-gray-500 tracking-wide uppercase mb-4">
            Review Officer Details
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-gray-500">Name</p>
                <p class="text-lg font-medium text-cyan-600">
                    {{ $organisation->reviewOfficer ? $organisation->reviewOfficer->first_name . ' ' . 
                                                                                        $organisation->reviewOfficer->last_name : 'N/A' 
                    }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Division</p>
                <p class="text-lg font-medium text-cyan-600">
                    {{ $organisation->reviewOfficer ? $organisation->reviewOfficer->division->name : 'N/A'}}
                </p>
            </div>
        </div>
    </div>
    <div class="max-w-3xl mx-auto mt-6 space-y-4">
        <form method="POST" action="{{ route('registry.organisations.reviewOfficer.update', $organisation->id) }}">
            @csrf
            @method('PATCH')

            <select name="review_officer_id" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-cyan-200">

                <option value="">Select review officer</option>
                    @foreach ($usersWithDivision as $user)
                        @if ($user->id !== $organisation->review_officer_id)
                            <option value="{{ $user->id }} ">
                                {{ $user->first_name }} {{ $user->last_name }} - {{ $user->division_name ?? 'No Division' }}
                            </option>
                        @endif
                    @endforeach
            </select>

            <button type="submit"
                    class="flex px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white mt-2 rounded-md font-semibold">
                Update Review Officer
            </button>
        </form>
    </div>

@endsection