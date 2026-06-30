@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-6 max-w-5xl">
    {{-- <div class="flex items-center justify-between mt-6 mb-2">
        <h1 class="text-center text-xl font-bold text-gray-600 text-centre">View User</h1>
    </div> --}}
    
    <div class="bg-white rounded-lg text-md shadow-md p-6 mb-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-base">

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">User ID:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $user->id }}</dd>
            </div>
            
            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">First Name:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $user->first_name }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Last Name:</dt>
                <dd class="sm:col-span-2 text-gray-900">{{ $user->last_name }}</dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Status:</dt>
                <dd class="sm:col-span-2">
                    @if($user->is_active)
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
                <dt class="font-semibold text-gray-700">Roles:</dt>
                <dd class="sm:col-span-2">
                    @forelse($user->getRoleNames() as $role)
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-sm font-medium mr-1 mb-1">
                            {{ $role }}
                        </span>
                    @empty
                        <span class="text-gray-500">No roles assigned</span>
                    @endforelse
                </dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Permissions:</dt>
                <dd class="sm:col-span-2">
                    @forelse($user->getAllPermissions() as $permission)
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-sm font-medium mr-1 mb-1">
                            {{ $permission->name }}
                        </span>
                    @empty
                        <span class="text-gray-500">No permissions assigned</span>
                    @endforelse
                </dd>
            </div>      

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Created At:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    {{ $user->created_at->format('d M Y, g:i A') }}
                    <span class="text-sm text-gray-500">
                        ({{ $user->created_at->diffForHumans() }})
                    </span>
                </dd>
            </div>

            <div class="col-span-2 sm:grid sm:grid-cols-3 sm:gap-4 py-2 border-b">
                <dt class="font-semibold text-gray-700">Updated At:</dt>
                <dd class="sm:col-span-2 text-gray-900">
                    {{ $user->updated_at->format('d M Y, g:i A') }}
                    <span class="text-sm text-gray-500">
                        ({{ $user->updated_at->diffForHumans() }})
                    </span>
                </dd>
            </div>
        </dl>
    </div>
    <a href="{{ route('registry.users.edit', $user) }}"
        class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-6 py-3 text-sm font-medium text-amber-700 shadow-sm transition-all duration-200 hover:bg-amber-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-amber-400">
        <svg xmlns="http://www.w3.org/2000/svg" 
                class="h-4 w-4" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.586-9.414a2 2 0 112.828 2.828L12 20l-4 1 1-4 10.414-10.414z" />
        </svg>
        Edit User
    </a>
</div>

@endsection