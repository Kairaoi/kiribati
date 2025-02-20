@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-gray-900 tracking-wide">View File: {{ $file->name }}</h1>


        <a href="{{ route('registry.files.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md transition duration-150 ease-in-out">
            <span>Back to Files</span>
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-medium text-gray-700">File Name</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $file->name }}</dd>
            </div>
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-medium text-gray-700">Details</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $file->details }}</dd>
            </div>
            <div class="sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-medium text-gray-700">Upload Date</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $file->created_at->format('M d, Y') }}</dd>
            </div>
            <div class="sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-medium text-gray-700">Security Level</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $file->security_level === 'public' ? 'bg-green-100 text-green-800' : 
                           ($file->security_level === 'internal' ? 'bg-blue-100 text-blue-800' : 
                           ($file->security_level === 'confidential' ? 'bg-yellow-100 text-yellow-800' : 
                           'bg-red-100 text-red-800')) }}">
                        {{ ucfirst($file->security_level) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <div class="pdf-viewer bg-white rounded-lg shadow-md p-2">
        <!-- Display appropriate viewer based on file type -->
        @php
            $extension = pathinfo($file->path, PATHINFO_EXTENSION);
        @endphp
        
        @if(in_array(strtolower($extension), ['pdf', 'doc', 'docx']))
            @if(strtolower($extension) === 'pdf')
                <embed src="{{ asset('storage/' . $file->path) }}" type="application/pdf" width="100%" height="600px">
            @else
                <div class="flex items-center justify-center py-6">
                    <a href="{{ route('registry.files.download', $file->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        <span>Download {{ strtoupper($extension) }} File</span>
                    </a>
                </div>
            @endif
        @else
            <div class="flex flex-col items-center justify-center py-10">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-2 text-gray-600">This file type cannot be previewed</p>
                <a href="{{ route('registry.files.download', $file->id) }}" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md transition duration-150 ease-in-out">
                    <span>Download File</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection