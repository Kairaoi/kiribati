@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-6 max-w-5xl">
    <div class="mx-auto max-w-2xl grid grid-cols-1 gap-4 sm:grid-cols-2">

        {{-- Total Files --}}
        <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total Files
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-800">
                        {{ $totalFiles }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>


        {{-- Active Files --}}
        <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Active Files
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-800">
                        {{ $activeFiles }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-50 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

    </div>
    <div class="bg-white rounded-lg shadow-md p-6 mt-6 mb-6">
        
        <dl class="grid text-base">
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-bold text-cyan-700">External Partner Details</dt>
            </div>
            
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Name:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">{{ $externalPartner->name }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Status:</dt>
                <dd class="sm:col-span-2">
                    @if($externalPartner->is_active)
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
                <dt class="font-semibold text-sm text-gray-700">Ministry:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">{{ $externalPartner->ministry->name }}</dd>

            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Ministry:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">{{ $fileType->ministry->name ?? '-' }}</dd>
            </div>
           

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Created At:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">
                    {{ $externalPartner->created_at->format('d M Y') }}
                </dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-sm text-gray-700">Updated At:</dt>
                <dd class="sm:col-span-2 text-sm text-gray-900">
                    {{ $externalPartner->updated_at->format('d M Y') }}
                </dd>
            </div>
        </dl>
    </div>



    
</div>

@endsection