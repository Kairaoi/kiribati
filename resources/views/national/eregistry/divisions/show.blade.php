@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-6 max-w-5xl">

    <div class="bg-white rounded-lg shadow-md p-6 mt-2 mb-2">
        <dl class="grid text-base">
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-bold text-cyan-700">Division Details</dt>
            </div>
            
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Name:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">{{ $division->name ?? '-' }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Organisation:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">{{ $division->organisation->name ?? '-' }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Head of Division (HOD):</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">{{ $division->hod->name ?? '-' }}</dd>
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
    @if($isRegistry || $isSystemAdmin)
        <a href="{{ route('registry.divisions.edit', $division) }}"
            class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-6 py-3 text-sm font-medium text-amber-700 shadow-sm transition-all duration-200 hover:bg-amber-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-amber-400">
            Edit Division
        </a>
    @endif
</div>

@endsection