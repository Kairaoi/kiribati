
@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-100 min-h-screen">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700">E-Registry Dashboard</h1>
        {{-- <div class="text-sm text-gray-500">Welcome back, {{ auth()->user()->name ?? 'User' }}</div> --}}
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <!-- Current Review Officer Card -->
        <div class="bg-white p-4 rounded-2xl shadow border-l-4 border-cyan-500">
            <p class="text-gray-500 text-sm">Current Review Officer</p>
            <h2 class="text-lg font-semibold text-gray-700">
                {{ optional($reviewOfficer)->name ?? 'N/A' }}
            </h2>
            {{-- <a href="{{ route('registry.users.edit-review-officer' }}" --}}
            <a href="{{ route('registry.users.edit-review-officer') }}"
                class="inline-block mt-2 text-sm text-cyan-500 hover:underline">
                Change
            </a>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500 text-sm">Total Files</p>
            <h2 class="text-2xl font-bold text-cyan-500">{{ $totalFiles ?? 0 }}</h2>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500 text-sm">Pending Review</p>
            <h2 class="text-2xl font-bold text-yellow-500">{{ $pendingFiles ?? 0 }}</h2>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500 text-sm">Reviewed and Officers</p>
            <h2 class="text-2xl font-bold text-green-500">{{ $approvedFiles ?? 0 }}</h2>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-500 text-sm">Filed</p>
            <h2 class="text-2xl font-bold text-blue-500">{{ $rejectedFiles ?? 0 }}</h2>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <!-- Recent Files Table -->
        <div class="md:col-span-3 bg-white shadow p-4">
            <div class="flex justify-between mb-4">
                {{-- <h2 class="">Recent Files</h2> --}}
                {{-- <a href="{{ route('registry.files.index') }}" class="text-cyan-500 text-sm">View All</a> --}}
                <div class="flex mb-4 bg-gray-200 p-1 rounded-xl w-fit">
    
            <button 
                onclick="switchTable('due')" 
                id="tab-due"
                class="tab-btn px-4 py-2 rounded-lg text-sm font-medium bg-cyan-700 text-white shadow">
                Due Soon
            </button>

            <button 
                onclick="switchTable('pending')" 
                id="tab-pending"
                class="tab-btn px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-cyan-600 hover:text-white transition">
                Pending Actions
            </button>

        </div>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2">File Name</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentFiles ?? [] as $file)
                        <tr class="border-b">
                            <td class="py-2">{{ $file->name }}</td>
                            <td>
                                <span class="
                                    @if($file->status == 'pending') text-yellow-500
                                    @elseif($file->status == 'approved') text-green-500
                                    @else text-red-500 @endif">
                                    {{ ucfirst($file->status) }}
                                </span>
                            </td>
                            <td>{{ $file->created_at->format('M d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-3 text-gray-400">No files found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-2xl text-sm shadow p-4">
            <h2 class="font-semibold text-gray-700 mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('registry.files.create', ['createType' => 'dispatch']) }}" class="block bg-gray-200 text-gray-700 text-center py-2 rounded-xl hover:bg-cyan-500">Create Dispatch</a>
                {{-- <a href="{{ route('registry.files.create', ['createType' => 'internal']) }}" class="block bg-gray-200 text-gray-700 text-center py-2 rounded-xl hover:bg-cyan-500">Create Internal File</a> --}}
                <a href="{{ route('registry.dispatches.index') }}" class="block bg-gray-200 text-gray-700 text-center py-2 rounded-xl hover:bg-cyan-500">Dispatches</a>
                <a href="{{ route('registry.file-circulations.index') }}" class="block bg-gray-200 text-gray-700 text-center py-2 rounded-xl hover:bg-cyan-500">Incoming Files</a>
            </div>
        </div>

    </div>

    <!-- Secondary Tables -->
    {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

        <!-- Pending Assignments -->
        <div class="bg-white rounded-2xl shadow p-4">
            <h2 class="font-semibold text-gray-700 mb-4">Pending Assignments</h2>
            <ul class="text-sm space-y-2">
                @forelse($pendingAssignments ?? [] as $item)
                    <li class="flex justify-between border-b pb-1">
                        <span>{{ $item->file->name ?? 'File' }}</span>
                        <span class="text-yellow-500">Waiting</span>
                    </li>
                @empty
                    <li class="text-gray-400">No pending assignments</li>
                @endforelse
            </ul>
        </div>

        <!-- Recently Approved -->
        <div class="bg-white rounded-2xl shadow p-4">
            <h2 class="font-semibold text-gray-700 mb-4">Recently Approved</h2>
            <ul class="text-sm space-y-2">
                @forelse($recentApproved ?? [] as $file)
                    <li class="flex justify-between border-b pb-1">
                        <span>{{ $file->name }}</span>
                        <span class="text-green-500">Approved</span>
                    </li>
                @empty
                    <li class="text-gray-400">No approved files</li>
                @endforelse
            </ul>
        </div>

    </div> --}}

</div>
@endsection
