@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-6 max-w-5xl">
    {{-- <div class="flex items-center justify-between mt-6 mb-2">
        <h1 class="text-center text-xl font-bold text-gray-600 text-centre">View User</h1>
    </div> --}}
    
    <div class="bg-white rounded-lg text-sm shadow-md p-6 mb-6">
        <dl class="grid grid-cols-1 text-sm md:grid-cols-2 gap-x-4 gap-y-1 text-base">
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-bold text-cyan-700">Organisation Details</dt>
            </div>
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Name:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $organisation->name }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Code:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $organisation->code }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Type:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $organisation->type->name }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Description:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $organisation->description ?? '-' }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Address:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $organisation->address }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Status:</dt>
                <dd class="sm:col-span-2">
                    @if($organisation->is_active)
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
                <dt class="font-semibold text-gray-700">Created At:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    {{ $organisation->created_at?->format('d M Y') }}
                   
                </dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Updated At:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    {{ $organisation->updated_at?->format('d M Y') ?? '-'}}
                    
                </dd>
            </div>

            {{-- <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Updated By:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    {{ $organisation->updatedBy?->name ?? '-' }}
                </dd>
            </div> --}}
        </dl>
    </div>
    @if($isSystemAdmin)
        <div class="bg-white rounded-lg text-sm shadow-md p-6 mb-6">
            <dl class="grid grid-cols-1 text-sm md:grid-cols-2 gap-x-4 gap-y-1 text-base">
                <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                    <dt class="font-bold text-cyan-700">More details</dt>
                </div>

                <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                    <dt class="font-semibold text-gray-700">Logo:</dt>

                    <dd class="sm:col-span-2 text-gray-900">
                        @if ($ministryLogoPath)
                            <img
                                src="{{ Storage::url($ministryLogoPath) }}"
                                alt="{{ $organisation->name }} logo"
                                class="h-20 w-auto object-contain rounded border border-gray-200 p-1"
                            >
                        @else
                            <span class="text-gray-500">No logo available</span>
                        @endif
                    </dd>
                </div>

                <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                    <dt class="font-semibold text-gray-700">Email:</dt>
                    <dd class="sm:col-span-2 text-gray-900">{{ $ministryEmail }}</dd>
                </div>

                <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                    <dt class="font-semibold text-gray-700">Phone:</dt>
                    <dd class="sm:col-span-2 text-gray-900">{{ $ministryPhone }}</dd>
                </div>

                <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                    <dt class="font-semibold text-gray-700">Number of users:</dt>
                    <dd class="sm:col-span-2 text-gray-900">{{ $totalUsers }}</dd>
                </div>

                <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                    <dt class="font-semibold text-gray-700">Number of created files:</dt>
                    <dd class="sm:col-span-2 text-gray-900">{{ $totalFiles }}</dd>
                </div>

            </dl>
        </div>
   
        <a href="{{ route('registry.organisations.edit', $organisation) }}"
            class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-6 py-3 text-sm font-medium text-amber-700 shadow-sm transition-all duration-200 hover:bg-amber-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-amber-400">
            Edit Organisation
        </a>
    @endif
</div>

@endsection