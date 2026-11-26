<nav x-data="{ open: false }" class="bg-white border-b py-2 border-gray-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <nav x-data="{ mobileOpen: false }"
        class="relative bg-white border-b border-gray-200 shadow-sm">

        {{-- Top Header --}}
        <div class="max-w-screen-2xl mx-auto px-3 sm:px-6 lg:px-8">

            <div class="flex min-h-[72px] items-center justify-between gap-3 py-3">

                {{-- Branding --}}
                <div class="flex min-w-0 items-center gap-3">

                    <img src="{{ asset('images/flag1.png') }}"
                        alt="Kiribati Flag"
                        class="h-9 w-9 sm:h-12 sm:w-12 shrink-0 object-contain">

                    <div class="min-w-0">

                        <h1 class="text-base lg:text-lg font-bold text-gray-800 leading-tight">

                            <span class="hidden md:inline">
                                Document Management System
                            </span>

                            <span class="md:hidden">
                                DMS
                            </span>

                        </h1>

                        <div class="mt-1 flex items-center gap-2">

                            <span class="inline-flex items-center rounded-md
                                        text-xs
                                        font-semibold text-cyan-700">

                                {{ Auth::user()->organisation?->code ?? 'No Organisation Assigned' }}

                            </span>

                        </div>

                    </div>

                </div>

                {{-- Header Actions --}}
                <div class="flex shrink-0 items-center gap-2 sm:gap-4">

                    {{-- Teams Dropdown --}}
                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())

                        <x-dropdown align="right" width="60">

                            <x-slot name="trigger">

                                <button type="button"
                                        class="inline-flex items-center gap-2
                                            rounded-lg border border-gray-200
                                            px-3 py-2 text-sm font-medium
                                            text-gray-600 hover:bg-gray-50 transition">

                                    <i class="fa-solid fa-users" aria-hidden="true"></i>

                                    <span class="hidden lg:block max-w-[120px] truncate">
                                        {{ Auth::user()->currentTeam->name }}
                                    </span>

                                    <i class="fa-solid fa-chevron-down text-xs"
                                    aria-hidden="true"></i>

                                </button>

                            </x-slot>

                            <x-slot name="content">

                                <div class="px-4 py-2 text-xs font-semibold text-gray-400">
                                    Manage Team
                                </div>

                                <x-dropdown-link
                                    href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                    Team Settings
                                </x-dropdown-link>

                                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())

                                    <x-dropdown-link href="{{ route('teams.create') }}">
                                        Create New Team
                                    </x-dropdown-link>

                                @endcan

                                @if (Auth::user()->allTeams()->count() > 1)

                                    <div class="border-t border-gray-100"></div>

                                    <div class="px-4 py-2 text-xs font-semibold text-gray-400">
                                        Switch Teams
                                    </div>

                                    @foreach (Auth::user()->allTeams() as $team)
                                        <x-switchable-team :team="$team" />
                                    @endforeach

                                @endif

                            </x-slot>

                        </x-dropdown>

                    @endif


                    {{-- Account Dropdown --}}
                    <x-dropdown align="right" width="48">

                        <x-slot name="trigger">

                            <button type="button"
                                    class="flex items-center gap-2 sm:gap-3
                                        rounded-lg p-1.5 sm:px-3 sm:py-2
                                        hover:bg-gray-100 transition">

                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())

                                    <img src="{{ Auth::user()->profile_photo_url }}"
                                        alt="{{ Auth::user()->first_name }}"
                                        class="w-9 h-9 rounded-full object-cover">

                                @else

                                    <div class="flex shrink-0 items-center justify-center
                                                w-9 h-9 rounded-full bg-cyan-100
                                                text-cyan-700 text-sm font-bold">

                                        {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}

                                    </div>

                                @endif

                                <div class="hidden sm:block min-w-0 text-left">

                                    <p class="max-w-[140px] truncate text-sm font-semibold text-gray-800">
                                        {{ Auth::user()->first_name }}
                                    </p>

                                    <p class="text-xs text-gray-500 capitalize">
                                        {{ str_replace('-', ' ', Auth::user()->roles->pluck('name')->first() ?? '') }}
                                    </p>

                                </div>

                                <i class="fa-solid fa-chevron-down text-xs text-gray-400"
                                aria-hidden="true"></i>

                            </button>

                        </x-slot>

                        <x-slot name="content">

                            <div class="px-4 py-3 border-b border-gray-100">

                                <p class="text-sm font-semibold text-gray-800">
                                    {{ Auth::user()->name }}
                                </p>

                                <p class="text-xs text-gray-500 break-all">
                                    {{ Auth::user()->email }}
                                </p>

                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">

                                <i class="fa-solid fa-user w-5 text-gray-400"></i>
                                Profile

                            </x-dropdown-link>

                            {{-- <x-dropdown-link href="{{ route('registry.users.signature.edit') }}">

                                <i class="fa-solid fa-signature w-5 text-gray-400"></i>
                                Edit Signature

                            </x-dropdown-link> --}}

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())

                                <x-dropdown-link href="{{ route('api-tokens.index') }}">

                                    <i class="fa-solid fa-key w-5 text-gray-400"></i>
                                    API Tokens

                                </x-dropdown-link>

                            @endif

                            <div class="border-t border-gray-100"></div>

                            {{-- Logout --}}
                            <form method="POST" action="{{ route('logout') }}">

                                @csrf

                                <button type="submit"
                                        class="flex items-center gap-2 w-full
                                            px-4 py-2 text-left text-sm
                                            text-red-600 hover:bg-red-50 transition">

                                    <i class="fa-solid fa-arrow-right-from-bracket w-4"
                                    aria-hidden="true"></i>

                                    Log Out

                                </button>

                            </form>

                        </x-slot>

                    </x-dropdown>


                    {{-- Mobile Menu Button --}}
                    <button type="button"
                            @click="mobileOpen = !mobileOpen"
                            :aria-expanded="mobileOpen.toString()"
                            aria-controls="main-navigation"
                            aria-label="Toggle navigation"
                            class="inline-flex lg:hidden items-center justify-center
                                w-10 h-10 rounded-lg border border-gray-200
                                text-gray-600 hover:bg-gray-100 transition">

                        <i class="fa-solid text-lg"
                        :class="mobileOpen ? 'fa-xmark' : 'fa-bars'">
                        </i>

                    </button>

                </div>

            </div>

        </div>


        {{-- Main Navigation --}}
        <div id="main-navigation"
            :class="mobileOpen ? 'block' : 'hidden'"
            class="lg:block border-t border-gray-100 bg-gray-50/50">

            <div class="max-w-screen-2xl mx-auto px-3 sm:px-6 lg:px-8">

                <div class="flex flex-col lg:flex-row lg:flex-wrap
                            lg:items-center gap-1 lg:gap-2 py-2">

                    {{-- Files --}}
                    <div x-data="{ open: false }"
                        @click.outside="open = false"
                        @keydown.escape.window="open = false"
                        class="relative">

                        <button type="button"
                                @click="open = !open"
                                :aria-expanded="open.toString()"
                                class="flex w-full items-center gap-2
                                    px-4 py-2.5 rounded-lg text-sm font-semibold
                                    transition
                                    {{ request()->routeIs('registry.files.*')
                                            ? 'bg-cyan-100 text-cyan-800'
                                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

                            <i class="fa-regular fa-file-lines w-4"></i>

                            <span>Files</span>

                            <i class="fa-solid fa-chevron-down ml-auto text-[10px]
                                    transition-transform"
                            :class="{ 'rotate-180': open }">
                            </i>

                        </button>

                        {{-- Files Dropdown --}}
                        <div x-show="open"
                            x-transition
                            style="display: none;"
                            class="mt-1 lg:absolute lg:left-0 lg:top-full
                                    lg:w-56 lg:z-50 lg:shadow-lg
                                    bg-white border border-gray-200 rounded-lg p-1">

                            <a href="{{ route('registry.files.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5
                                    rounded-md text-sm text-gray-700 hover:bg-cyan-50
                                    hover:text-cyan-700 transition">

                                <i class="fa-solid fa-list w-4"></i>
                                View Files

                            </a>

                            @role('registry')

                                <a href="{{ route('registry.files.create') }}"
                                class="flex items-center gap-3 px-3 py-2.5
                                        rounded-md text-sm text-gray-700 hover:bg-cyan-50
                                        hover:text-cyan-700 transition">

                                    <i class="fa-solid fa-plus w-4"></i>
                                    Create File

                                </a>

                            @endrole

                        </div>

                    </div>


                    @hasanyrole('registry|system-admin')

                        {{-- E-Filing --}}
                        <a href="{{ route('registry.folders.index') }}"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-lg
                                text-sm font-semibold transition
                                {{ request()->routeIs('registry.folders.*')
                                        ? 'bg-cyan-100 text-cyan-800'
                                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

                            <i class="fa-regular fa-folder-open w-4"></i>

                            E-Filing

                        </a>


                        {{-- File Types --}}
                        <div x-data="{ open: false }"
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            class="relative">

                            <button type="button"
                                    @click="open = !open"
                                    :aria-expanded="open.toString()"
                                    class="flex w-full items-center gap-2 px-4 py-2.5
                                        rounded-lg text-sm font-semibold transition
                                        {{ request()->routeIs('registry.file-types.*')
                                                ? 'bg-cyan-100 text-cyan-800'
                                                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

                                <i class="fa-solid fa-layer-group w-4"></i>

                                <span>File Types</span>

                                <i class="fa-solid fa-chevron-down ml-auto text-[10px]
                                        transition-transform"
                                :class="{ 'rotate-180': open }">
                                </i>

                            </button>

                            <div x-show="open"
                                x-transition
                                style="display: none;"
                                class="mt-1 lg:absolute lg:left-0 lg:top-full
                                        lg:w-56 lg:z-50 lg:shadow-lg
                                        bg-white border border-gray-200 rounded-lg p-1">

                                <a href="{{ route('registry.file-types.index') }}"
                                class="flex items-center gap-3 px-3 py-2.5
                                        rounded-md text-sm text-gray-700
                                        hover:bg-cyan-50 hover:text-cyan-700 transition">

                                    <i class="fa-solid fa-list w-4"></i>
                                    View File Types

                                </a>

                                <a href="{{ route('registry.file-types.create') }}"
                                class="flex items-center gap-3 px-3 py-2.5
                                        rounded-md text-sm text-gray-700
                                        hover:bg-cyan-50 hover:text-cyan-700 transition">

                                    <i class="fa-solid fa-plus w-4"></i>
                                    Add File Type

                                </a>

                            </div>

                        </div>


                        {{-- External Partners --}}
                        <a href="{{ route('registry.external-partners.index') }}"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-lg
                                text-sm font-semibold transition
                                {{ request()->routeIs('registry.external-partners.*')
                                        ? 'bg-cyan-100 text-cyan-800'
                                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

                            <i class="fa-solid fa-handshake w-4"></i>

                            External Partners

                        </a>


                        {{-- Organisations --}}
                        <a href="{{ route('registry.organisations.index') }}"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-lg
                                text-sm font-semibold transition
                                {{ request()->routeIs('registry.organisations.*')
                                        ? 'bg-cyan-100 text-cyan-800'
                                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

                            <i class="fa-regular fa-building w-4"></i>

                            Organisations

                        </a>


                        {{-- Divisions and HoDs --}}
                        <a href="{{ route('registry.divisions.index') }}"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-lg
                                text-sm font-semibold transition
                                {{ request()->routeIs('registry.divisions.*')
                                        ? 'bg-cyan-100 text-cyan-800'
                                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

                            <i class="fa-solid fa-sitemap w-4"></i>

                            Divisions & HoDs

                        </a>

                    @endhasanyrole


                    {{-- Users --}}
                    @hasanyrole('admin|system-admin')

                        <a href="{{ route('registry.users.index') }}"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-lg
                                text-sm font-semibold transition
                                {{ request()->routeIs('registry.users.*')
                                        ? 'bg-cyan-100 text-cyan-800'
                                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

                            <i class="fa-solid fa-users w-4"></i>

                            Users

                        </a>

                    @endhasanyrole

                </div>

            </div>

        </div>

    </nav>

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
