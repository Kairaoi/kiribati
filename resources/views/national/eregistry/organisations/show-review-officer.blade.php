@extends('layouts.app')

@section('content')

{{-- <div class="container mx-auto font-roboto px-8 max-w-5xl mt-1"> {{ Breadcrumbs::render('dispatches.show', $file) }} </div> --}}
<div class="mx-w-5xl mx-auto">
    <!-- Back Button -->
    <div class="flex justify-end mb-3">
        <a href="{{ route('registry.dispatches.index') }}"
            class="inline-flex border border-green-400 mt-2 items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-green-800 font-semibold rounded-lg shadow-sm transition duration-150 ease-in-out">
            <!-- Heroicon or Lucide arrow -->
            <svg xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke-width="2" 
                stroke="currentColor" 
                class="w-5 h-5 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
    </div>  

     <div class="font-roboto max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <section>
            <!-- Card -->
            <div class="rounded-lg bg-slate-800 text-white shadow-xl ring-1 ring-black/10 p-6 sm:p-8">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-semibold tracking-tight">File Reviewer Details</h2>
                    </div>
                </div>
                <!-- Body -->
                <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">

                <div>
                    <dt class="text-slate-300 text-sm">Name</dt>
                    <dd class="mt-1 font-medium">{{ $organisation->reviewOfficer ? $organisation->reviewOfficer->first_name . ' ' . 
                                                                                     $organisation->reviewOfficer->last_name : 'N/A' 
                                                 }}
                    </dd>
                </div>

                <div>
                    <dt class="text-slate-300 text-sm">Division</dt>
                    <dd class="mt-1 font-medium">{{ $organisation->reviewOfficer ? $organisation->reviewOfficer->division->name : 'N/A'}}</dd>
                </div>
                
            </div>
        </section>
          <div class="flex justify-left font-roboto text-lgt">
            <div class="flex items-center justify-between mt-6 mb-6">     
                <form method="POST" action="{{ route('registry.organisations.reviewOfficer.update', $organisation->id) }}">
                    @csrf
                    @method('PATCH')

                    <select name="review_officer_id" class="form-control">
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
                        class="flex mt-2 ml-4 bg-blue-600 hover:bg-blue-900 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition duration-200">
                        Update Review Officer
                    </button>
                </form>
            </div>
          </div>
    </div>

  
</div>

@endsection