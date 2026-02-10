@extends('layouts.app')

@section('content')

<div class="container mx-auto font-montserrat px-4 py-6 max-w-7xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-center text-2xl font-bold text-gray-900 text-centre">View UserID :{{ $user->id }}</h1>
        {{-- <a href="{{ route('registry.files.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md transition duration-150 ease-in-out">
            <span>Back to Files</span>
        </a> --}}
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-base">
            
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">First Name:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $user->first_name }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Last Name:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $user->last_name }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Division:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    {{ optional($user->division)->name ?? 'No division specified' }}
                </dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Email:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $user->email }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Role:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    @if ($user->getRoleNames()->isNotEmpty())
                        {{ $user->getRoleNames()->join(', ') }}
                    @else
                        No role assigned
                    @endif
                </dd>
            </div>           
        </dl>
    </div>
</div>

@endsection