<nav x-data="{ open: false }" class="bg-white border-b py-2 border-gray-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
        <div class="flex justify-between h-12">
            <div class="flex">
                <!-- Logo -->
                {{-- <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div> --}}

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 font-montserrat font-bold sm:flex">
                    <img src="{{ asset('images/flag1.png') }}" 
                        alt=""
                        class="w-12 h-12 mr-2" />
                    
                </div>

                {{-- E-Registry --}}
                @hasanyrole(['registry'])
                    <div class="hidden space-x-8 sm:ms-10 text-2xl font-montserrat font-bold sm:flex items-center">
                        <div x-data="{ open: false }" 
                            class="relative flex items-center h-16"
                            @mouseenter="open = true"
                            @mouseleave="open = false">

                            <x-nav-link href="{{ route('registry.boards.index') }}"
                                :active="request()->routeIs('registry.boards.index', 'registry.dispatches.*', 'registry.file-circulations.*')"
                                class="flex items-center h-full">
                                <svg class="w-5 h-5 inline-block mr-1 text-blue-900" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9l9-7 9 7v11a2 2 0 01-2 2h-4a2 2 0 01-2-2V13H9v7a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                                 <span class="leading-none">{{ __('E-REGISTRY') }}</span>
                            </x-nav-link>
                        </div>
                    </div>


                    {{-- Management --}}
                    <div class="hidden space-x-8 sm:ms-10 text-2xl font-montserrat font-bold sm:flex items-center">                        
                        <div x-data="{ open: false }" 
                            class="relative flex items-center h-16"
                            @mouseenter="open = true"
                            @mouseleave="open = false">
                        
                            <x-nav-link href="{{ route('registry.boards.management') }}" 
                                :active="request()->routeIs('registry.boards.management')"
                                class="flex items-center h-full">
                                <svg class="w-5 h-5 inline-block mr-1 text-blue-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6M4 6h16M4 10h16M4 14h16" />
                                </svg>
                                {{ __('MANAGEMENT') }}
                            </x-nav-link>
                        </div>
                    </div>

                    {{-- Archive --}}
                    <div class="hidden space-x-8 sm:ms-10 text-2xl font-montserrat font-bold sm:flex items-center">                        
                        <div x-data="{ open: false }" 
                            class="relative flex items-center h-16"
                            @mouseenter="open = true"
                            @mouseleave="open = false">
                        
                            <x-nav-link href="{{ route('registry.files.index') }}" 
                                :active="request()->routeIs('registry.files.index')"
                                class="flex items-center h-full">
                                <svg class="w-5 h-5 inline-block mr-1 text-blue-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4a2 2 0 001-1.73zM12 3v5M3.27 8l8.73 5 8.73-5M3.27 16l8.73 5 8.73-5" />
                                </svg>
                                {{ __('ARCHIVE') }}
                            </x-nav-link>
                        </div>
                    </div>
                @endhasanyrole 

                @hasanyrole(['user', 'admin'])
                    <div class="hidden space-x-8 sm:ms-10 text-2xl font-montserrat font-bold sm:flex items-center">                        
                            <div x-data="{ open: false }" 
                                class="relative flex items-center h-16"
                                @mouseenter="open = true"
                                @mouseleave="open = false">

                                <x-nav-link href="{{ route('registry.file-circulations.review.index') }}" 
                                    :active="request()->routeIs('registry.file-circulations.review.index')"
                                    class="flex items-center h-full">
                                    <svg class="w-5 h-5 inline-block mr-1 text-blue-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M5 6h14M5 6v12a2 2 0 002 2h10a2 2 0 002-2V6H5z" />
                                    </svg>
                                    {{ __('TO REVIEW') }}
                                </x-nav-link>
                            </div>
                    </div>

                    <div class="hidden space-x-8 sm:ms-10 text-2xl font-montserrat font-bold sm:flex items-center">                        
                            <div x-data="{ open: false }" 
                                class="relative flex items-center h-16"
                                @mouseenter="open = true"
                                @mouseleave="open = false">

                                <x-nav-link href="{{ route('registry.file-circulations.assigned.index') }}" 
                                    :active="request()->routeIs('registry.file-circulations.assigned.index')"
                                    class="flex items-center h-full">
                                    <svg class="w-5 h-5 inline-block mr-1 text-blue-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M5 6h14M5 6v12a2 2 0 002 2h10a2 2 0 002-2V6H5z" />
                                    </svg>
                                    {{ __('ASSIGNED') }}
                                </x-nav-link>
                            </div>
                    </div>
                @endhasanyrole

                {{-- Profile --}}
                {{-- <div class="hidden space-x-8 sm:ms-10 text-2xl font-montserrat font-bold sm:flex items-center">                        
                        <div x-data="{ open: false }" 
                            class="relative flex items-center h-16"
                            @mouseenter="open = true"
                            @mouseleave="open = false">

                            <x-nav-link href="{{ route('registry.boards.profile') }}" 
                            :active="request()->routeIs('registry.boards.profile')"
                            class="flex items-center h-full">
                                <svg class="w-5 h-5 inline-block mr-1 text-blue-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9.99 9.99 0 0112 15c2.28 0 4.374.755 6.001 2.026M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ __('PROFILE') }}
                            </x-nav-link>
                        </div>
                </div> --}}

            </div>
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ Auth::user()->currentTeam->name }}
                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>
                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Team Switcher -->
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Review officer settings Dropdown -->
                @hasanyrole(['registry'])
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                        <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->first_name }}" />
                                    </button>
                                @else
                                    <span class="inline-flex rounded-md">
                                        <a href="{{ optional(Auth::user()->organisation)->id
                                                ? route('registry.organisations.reviewOfficer.show', Auth::user()->organisation->id)
                                                : '#' }}"
                                                class="inline-flex font-poppins px-3 py-2 text-sm leading-4 font-bold rounded-md
                                                    bg-yellow-100 text-slate-900 
                                                    hover:bg-yellow-200
                                                    transition ease-in-out duration-150">
                                                {{ Auth::user()->organisation->code }} Reviewer:
                                                {{ optional(Auth::user()->organisation->reviewOfficer)->first_name . ' ' .
                                                optional(Auth::user()->organisation->reviewOfficer)->last_name
                                                ?? 'No Officer Assigned' }}
                                        </a>
                                    </span>
                                    
                                @endif
                            </x-slot>

                            <x-slot name="content">
                                <!-- Account Management -->
                                {{-- <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Change Review Officer') }}
                                </div>

                                @foreach ($users as $user)
                                    @if($user->id != Auth::user()->organisation->reviewOfficer->id )
                                        <!-- Do this if $user is NOT the logged-in user -->
                                        <x-dropdown-link href="#">{{ $user->first_name }}</x-dropdown-link>
                                    @endif
                                @endforeach --}}
                                
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endhasanyrole

                <!-- User Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->first_name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex font-roboto items-center px-3 py-2 text-m leading-4 font-bold rounded-md text-gray-800 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ Auth::user()->first_name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <!-- New Dropdown Menu -->
                            <div class="border-t border-gray-200"></div>
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('New Menu') }}
                            </div>

                            <x-dropdown-link href="{{ route('registry.boards.index') }}">
                                {{ __('E-Registry') }}
                            </x-dropdown-link>

                            <x-dropdown-link href="{{ route('registry.boards.index') }}">
                                {{ __('New Option 2') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-200"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->first_name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->first_name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan

                    <!-- Team Switcher -->
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-200"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>

                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team" />
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</nav>
