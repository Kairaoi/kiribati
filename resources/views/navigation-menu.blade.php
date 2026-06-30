<nav x-data="{ open: false }" class="bg-white border-b py-2 border-gray-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-8xl mx-auto text-md px-2 sm:px-4 lg:px-4">
        <div class="flex h-12 w-full items-center">
            <div class="flex w-full items-center">
                    <div class="hidden sm:flex items-center justify-between space-x-3 p-4 font-bold">
                        <img src="{{ asset('images/flag1.png') }}"
                            alt=""
                            class="w-12 h-12 mr-2" />

                        <div class="text-base text-gray-600 flex items-center gap-2">
                            <span>E-Registry |</span>

                            <div x-data="{ open: false }" class="relative">
                                <button type="button"
                                        @click="open = !open"
                                        @click.outside="open = false"
                                        class="inline-flex items-center gap-1 text-cyan-600 hover:text-cyan-700">
                                    <span>
                                        {{ auth()->user()->ministry->code ?? 'No Ministry Assigned' }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
            
                    <div class="hidden space-x-8 font-medium ml-4 sm:flex items-center">
                        {{-- E-Registry --}}
                                <div x-data="{ open: false }"
                                    class="relative flex items-center h-16"
                                    @mouseenter="open = true"
                                    @mouseleave="open = false">

                                    <x-nav-link href="#"
                                        class="flex items-center h-full" :active="request()->routeIs('registry.files.*')">
                                        <span class="leading-none flex items-center">
                                            {{ __('Correspondence Files') }}
                                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </span>
                                    </x-nav-link>

                                    <!-- Dropdown -->
                                    <div x-show="open"
                                        x-transition
                                        class="absolute left-0 top-full w-64 bg-white border border-gray-200 rounded-md shadow-md z-50">

                                        <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wide" >
                                            Files
                                        </div>
                                        <a href="{{ route('registry.files.index') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            View Files
                                        </a>
                                    
                                        <a href="{{ route('registry.files.create') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Add File
                                        </a>
                                    </div>
                                </div>
                    
                        @hasrole(['registry', 'ministry-admin', 'system-admin'])
                                <div x-data="{ open: false }"
                                    class="relative flex items-center h-16"
                                    @mouseenter="open = true"
                                    @mouseleave="open = false">
                                    
                                    <x-nav-link href="#"
                                        class="flex items-center h-full" :active="request()->routeIs('registry.file-types.*')">
                                        <span class="leading-none flex items-center">
                                            {{ __('File Types') }}
                                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </span>
                                    </x-nav-link>

                                    <!-- Dropdown -->
                                    <div x-show="open"
                                        x-transition
                                        class="absolute left-0 top-full w-64 bg-white border border-gray-200 rounded-md shadow-md z-50">

                                        <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wide">
                                            File Types
                                        </div>
                                        <a href="{{ route('registry.file-types.index') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            View File Types
                                        </a>
                                        <a href="{{ route('registry.file-types.create') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Add File Type
                                        </a>
                                    </div>
                                </div>

                                <div x-data="{ open: false }"
                                    class="relative flex items-center h-16"
                                    @mouseenter="open = true"
                                    @mouseleave="open = false">

                                    <x-nav-link href="#"
                                        class="flex items-center h-full"
                                        :active="request()->routeIs('registry.organisations.*') || request()->routeIs('registry.external-partners.*')">

                                        <span class="leading-none flex items-center">
                                            {{ __('Organisations') }}
                                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </span>
                                    </x-nav-link>

                                    <!-- Dropdown -->
                                    <div x-show="open"
                                        x-transition
                                        class="absolute left-0 top-full w-64 bg-white border border-gray-200 rounded-md shadow-md z-50">

                                        <!-- Identity Organisations -->
                                        <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wide">
                                            Identity Organisations
                                        </div>

                                        <a href="{{ route('registry.organisations.index') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            View Organisations
                                        </a>

                                        <div class="border-t my-1"></div>

                                        <!-- External Partners -->
                                        <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wide">
                                            External Partners
                                        </div>

                                        <a href="{{ route('registry.external-partners.index') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            View External Partners
                                        </a>

                                        <a href="{{ route('registry.external-partners.create') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Add External Partner
                                        </a>

                                    </div>
                                </div>

                                {{-- <div x-data="{ open: false }"
                                    class="relative flex items-center h-16"
                                    @mouseenter="open = true"
                                    @mouseleave="open = false">

                                    <x-nav-link href="{{ route('registry.ministries.edit', auth()->user()->ministry_id) }}"
                                        class="flex items-center h-full"
                                        :active="request()->routeIs('registry.ministries.edit', auth()->user()->ministry_id) || request()->routeIs('registry.external-partners.*')">

                                        <span class="leading-none flex items-center">
                                            {{ __('Ministry Profile') }}
                                        </span>
                                    </x-nav-link>
                                </div> --}}

                                <div x-data="{ open: false, templateOpen: false }"
                                    class="relative flex items-center h-16"
                                    @mouseenter="open = true"
                                    @mouseleave="open = false"
                                    class="relative">

                                    <x-nav-link href="#"
                                        class="flex items-center h-full">

                                        <span class="leading-none flex items-center">
                                            {{ __('Ministry') }}
                                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </span>
                                    </x-nav-link>

                                    <!-- Main Dropdown -->
                                    <div x-show="open"
                                        x-transition
                                        class="absolute left-0 top-full w-64 bg-white border border-gray-200 rounded-md shadow-md z-50">

                                        <a href="{{ route('registry.ministries.edit', auth()->user()->ministry_id) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Ministry Profile & Logo
                                        </a>

                                        <a href="{{ route('registry.divisions.index', auth()->user()->ministry_id) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Division Management
                                        </a>

                                        <a href="{{ route('registry.users.index', auth()->user()->ministry_id) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            User & Role Management
                                        </a>

                                        <!-- Nested Templates Dropdown -->
                                        {{-- <div class="relative"
                                            @mouseenter="templateOpen = true"
                                            @mouseleave="templateOpen = false">

                                            <button type="button"
                                                    class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <span> File Templates</span>
                                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                            <!-- Sub Dropdown -->
                                            <div x-show="templateOpen"
                                                x-transition
                                                class="absolute left-full top-0 w-56 bg-white border border-gray-200 rounded-md shadow-md z-50">

                                                <a href="{{ route('registry.file-types.index') }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Memorandum
                                                </a>

                                                <a href="{{ route('registry.file-types.index') }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Official Letter
                                                </a>

                                                <a href="{{ route('registry.file-types.create') }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Internal Memo
                                                </a>
                                            </div>
                                        </div>
                                         --}}
                                    </div>
                                </div>

                                {{-- <div x-data="{ open: false }"
                                    class="relative flex items-center h-16"
                                    @mouseenter="open = true"
                                    @mouseleave="open = false">
                                    
                                    <x-nav-link href="#"
                                        class="flex items-center h-full" :active="request()->routeIs('registry.file-types.*')">
                                        <span class="leading-none flex items-center">
                                            {{ __('Ministry Templates') }}
                                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </span>
                                    </x-nav-link>

                                    <!-- Dropdown -->
                                    <div x-show="open"
                                        x-transition
                                        class="absolute left-0 top-full w-64 bg-white border border-gray-200 rounded-md shadow-md z-50">

                                        <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wide">
                                            {{ __('Templates') }}
                                        </div>
                                        <a href="{{ route('registry.file-types.index') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Memorandum
                                        </a>
                                        <a href="{{ route('registry.file-types.index') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Official Letter
                                        </a>
                                        <a href="{{ route('registry.file-types.create') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Internal Memo
                                        </a>
                                    </div>
                                </div> --}}

                                

                                 
                            </div>
                        @endhasrole 

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

                            <!-- Administration Settings Dropdown -->
                            {{-- @hasanyrole(['sro', 'registry', 'admin'])
                                <div class="ms-3 relative">
                                    <x-dropdown>
                                        <x-slot name="trigger">
                                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->first_name }}" />
                                                </button>
                                            @else
                                                <span class="inline-flex rounded-md">
                                                    <button type="button" class="inline-flex font-roboto items-center px-3 py-2 text-sm leading-4 font-light rounded-md text-gray-800 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                        Administration
                                                    </button>
                                                </span>
                                            @endif
                                        </x-slot>

                                        <x-slot name="content">

                                            <x-dropdown-link href="{{ route('registry.users.index') }}">
                                                {{ __('Manage Users') }}
                                            </x-dropdown-link>

                                            <x-dropdown-link href="{{ route('registry.divisions.index') }}">
                                                {{ __('Manage User Roles') }}
                                            </x-dropdown-link>

                                            <x-dropdown-link href="{{ route('registry.divisions.index') }}">
                                                {{ __('Manage Divisions') }}
                                            </x-dropdown-link>

                                            <x-dropdown-link href="{{ route('registry.divisions.index') }}">
                                                {{ __('Audit Trails') }}
                                            </x-dropdown-link>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            @endhasanyrole --}}
                    </div>
                    <!-- User Settings Dropdown -->
                    <div class="ms-auto justify-right ms-3 relative">
                                <x-dropdown>
                                    <x-slot name="trigger">
                                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                            <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->first_name }}" />
                                            </button>
                                        @else
                                            <span class="flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center whitespace-nowrap font-roboto px-3 py-3 text-sm leading-4 font-light rounded-md text-gray-800 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">

                                                    <span>
                                                        {{ Auth::user()->first_name }}
                                                        |
                                                        {{ Auth::user()->roles->pluck('name')->first() }}
                                                    </span>

                                                    <svg class="ms-2 -me-0.5 size-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
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
                                        {{-- <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('New Menu') }}
                                        </div>

                                        <x-dropdown-link href="{{ route('registry.boards.index') }}">
                                            {{ __('E-Registry') }}
                                        </x-dropdown-link>

                                        <x-dropdown-link href="{{ route('registry.boards.index') }}">
                                            {{ __('New Option 2') }}
                                        </x-dropdown-link> --}}

                                        <x-dropdown-link href="{{ route('registry.users.signature.edit') }}" :active="request()->routeIs('registry.users.signature.edit')">
                                                {{ __('Edit Signature') }}
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

                <x-responsive-nav-link href="{{ route('registry.users.signature.edit') }}" :active="request()->routeIs('registry.users.signature.edit')">
                    {{ __('Edit Signature') }}
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
