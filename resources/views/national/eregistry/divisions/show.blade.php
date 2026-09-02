@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-6 max-w-5xl">

    <div class="bg-white rounded-lg shadow-md p-6 mt-6 mb-6">
        <dl class="grid text-base">
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-bold text-cyan-700">Division Details</dt>
            </div>
            
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Name:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">{{ $division->name ?? '-' }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Ministry:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">{{ $division->ministry->identityOrganisation->name ?? '-' }}</dd>
            </div>
            
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Location:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">{{ $division->location ?? '-' }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Email:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">{{ $division->email ?? '-' }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Phone:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">{{ $division->phone ?? '-' }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Status:</dt>
                <dd class="sm:col-span-2">
                    @if($division->is_active)
                        <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                            Active
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">
                            Inactive
                        </span>
                    @endif
                </dd>
            </div>           

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Created At:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">
                    {{ $division->created_at->format('d M Y') }}
                  
                </dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Updated At:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">
                    {{ $division->updated_at->format('d M Y') }}
                </dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Updated By:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">
                    {{ $division->updatedBy->name ?? '-' }}
                </dd>
            </div>
        </dl>

        
    </div>



    
</div>

@endsection