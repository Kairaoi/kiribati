@extends('layouts.app')

@section('content')

<div class="w-full max-w-screen-2xl mx-auto px-3 sm:px-6 lg:px-8 px-3 sm:px-6 lg:px-8 py-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center
                sm:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-800">
                E-Filing
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Manage and organise all filed documents.
            </p>
        </div>

        <a href="{{ route('registry.folders.create') }}"
           class="inline-flex items-center justify-center gap-2
                  w-full sm:w-auto shrink-0 rounded-lg
                  bg-cyan-600 px-5 py-2.5 text-sm font-semibold
                  text-white shadow-sm hover:bg-cyan-700 transition">

            <i class="fa-solid fa-folder-plus" aria-hidden="true"></i>
            Create New Folder

        </a>

    </div>


    {{-- Search Bar --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">

        <form action="{{ route('registry.folders.index') }}"
              method="GET"
              class="flex flex-col sm:flex-row gap-3">

            <div class="relative flex-1 min-w-0">

                <i class="fa-solid fa-magnifying-glass
                          absolute left-3 top-1/2 -translate-y-1/2
                          text-gray-400 pointer-events-none"
                   aria-hidden="true"></i>

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by folder, file name or reference number..."
                       aria-label="Search filed documents and folders"
                       class="w-full rounded-lg border-gray-300
                              py-2.5 pl-10 pr-4 text-sm shadow-sm
                              focus:border-cyan-500 focus:ring-cyan-500">

            </div>

            <div class="flex flex-wrap items-center gap-2">

                <button type="submit"
                        class="flex-1 sm:flex-none inline-flex items-center
                               justify-center gap-2 rounded-lg bg-cyan-600
                               px-5 py-2.5 text-sm font-semibold text-white
                               hover:bg-cyan-700 transition">

                    Search

                </button>

                @if (trim(request('search', '')) !== '')

                    <a href="{{ route('registry.folders.index') }}"
                       class="flex-1 sm:flex-none inline-flex items-center
                              justify-center gap-2 rounded-lg border
                              border-gray-300 bg-white px-4 py-2.5
                              text-sm font-medium text-gray-600
                              hover:bg-gray-50 transition">

                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        Clear

                    </a>

                @endif

            </div>

        </form>

    </div>


    {{-- Filed Document Search Results --}}
    @if ($fileResults !== null)

        <section class="mb-6 rounded-xl border border-gray-200
                        bg-white shadow-sm overflow-hidden">

            <div class="flex items-center justify-between gap-3
                        border-b border-gray-100 px-5 py-4">

                <h2 class="flex items-center gap-2 text-base
                           font-semibold text-gray-800">

                    <i class="fa-regular fa-file-lines text-cyan-600"
                       aria-hidden="true"></i>

                    Filed Documents

                </h2>

                <span class="rounded-full bg-cyan-50 px-3 py-1
                             text-xs font-semibold text-cyan-700">

                    {{ $fileResults->total() }} results

                </span>

            </div>

            <div class="divide-y divide-gray-100">

                @forelse ($fileResults as $assignment)

                    <div class="flex flex-col sm:flex-row sm:items-center
                                sm:justify-between gap-3 px-5 py-4
                                hover:bg-gray-50 transition">

                        <div class="flex items-start gap-3 min-w-0">

                            <div class="flex shrink-0 items-center justify-center
                                        w-11 h-11 rounded-lg
                                        bg-cyan-50 text-cyan-700">

                                <i class="fa-regular fa-file-lines text-xl"
                                   aria-hidden="true"></i>

                            </div>

                            <div class="min-w-0">

                                <p class="text-xs font-semibold text-cyan-700 break-words">

                                    {{ $assignment->file->reference_no }}

                                </p>

                                <p class="mt-1 text-sm font-medium
                                          text-gray-800 break-words">

                                    {{ $assignment->file->subject }}

                                </p>

                            </div>

                        </div>

                        <a href="{{ route('registry.files.show', $assignment->file->uuid) }}"
                           class="inline-flex shrink-0 items-center justify-center
                                  gap-2 rounded-lg border border-gray-200
                                  px-4 py-2 text-sm font-medium text-gray-700
                                  hover:border-cyan-300 hover:bg-cyan-50
                                  hover:text-cyan-700 transition">

                            Open File

                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"
                               aria-hidden="true"></i>

                        </a>

                    </div>

                @empty

                    <div class="flex flex-col items-center justify-center
                                py-10 px-4 text-center">

                        <i class="fa-regular fa-file-lines text-3xl text-gray-300"
                           aria-hidden="true"></i>

                        <p class="mt-3 text-sm font-medium text-gray-700">
                            No matching filed documents
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            Try a different file name or reference number.
                        </p>

                    </div>

                @endforelse

            </div>

            @if ($fileResults->hasPages())

                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $fileResults->withQueryString()->links() }}
                </div>

            @endif

        </section>

    @endif


    {{-- Folder Section --}}
    <section>

        <div class="flex items-center justify-between mb-4">

            <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-800">

                <i class="fa-regular fa-folder text-amber-500"
                   aria-hidden="true"></i>

                Folders

            </h2>

        </div>


        <div class="grid grid-cols-1 min-[400px]:grid-cols-2 
                    md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 
                    2xl:grid-cols-6 gap-3">

            @forelse ($folders as $folder)

                <div class="folder-card group relative min-w-0
                            rounded-lg border border-gray-200 bg-white
                            shadow-sm hover:border-cyan-300 hover:shadow-md
                            transition duration-200"

                    data-edit-url="{{ route('registry.folders.edit', $folder->uuid) }}"
                    data-delete-url="{{ route('registry.folders.destroy', $folder->uuid) }}"
                    data-folder-name="{{ $folder->name }}">

                    <a href="{{ route('registry.folders.show', $folder->uuid) }}"
                    class="flex items-center gap-3 rounded-lg p-3 pr-10
                            focus:outline-none focus:ring-2 focus:ring-cyan-400">

                        {{-- Folder Icon --}}
                        <div class="flex shrink-0 items-center justify-center
                                    w-9 h-9 rounded-lg bg-amber-50
                                    group-hover:bg-amber-100 transition">

                            <i class="fa-solid fa-folder text-xl text-amber-400"
                            aria-hidden="true"></i>

                        </div>

                        {{-- Folder Details --}}
                        <div class="min-w-0">

                            <p class="truncate text-sm font-semibold text-gray-800
                                    group-hover:text-cyan-700 transition"
                            title="{{ $folder->name }}">

                                {{ $folder->name }}

                            </p>

                            <p class="mt-0.5 text-xs text-gray-500">

                                {{ $folder->file_assignments_count ?? 0 }}
                                {{ ($folder->file_assignments_count ?? 0) == 1 ? 'file' : 'files' }}

                            </p>

                        </div>

                    </a>

                    {{-- Folder Actions --}}
                    <button type="button"
                            class="folder-menu-button absolute right-2 top-1/2
                                -translate-y-1/2 flex items-center justify-center
                                w-8 h-8 rounded-md text-gray-400
                                hover:bg-gray-100 hover:text-gray-700
                                focus:ring-2 focus:ring-cyan-400 transition"
                            aria-label="Actions for {{ $folder->name }}"
                            aria-controls="folder-context-menu"
                            aria-expanded="false">

                        <i class="fa-solid fa-ellipsis-vertical"
                        aria-hidden="true"></i>

                    </button>

                </div>

            @empty

                <div class="col-span-full rounded-lg border border-dashed
                            border-gray-300 bg-white py-10 text-center">

                    <i class="fa-regular fa-folder-open text-3xl text-amber-400"></i>

                    <p class="mt-2 text-sm text-gray-500">
                        No folders found.
                    </p>

                </div>

            @endforelse

        </div>

    </section>


    {{-- Folder Context Menu --}}
    <div id="folder-context-menu"
         class="fixed z-50 hidden w-48 overflow-hidden
                rounded-xl border border-gray-200
                bg-white p-1.5 shadow-xl">

        <a id="folder-edit-link"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5
                  text-sm font-medium text-gray-700
                  hover:bg-gray-100 focus:bg-gray-100
                  focus:outline-none cursor-pointer">

            <i class="fa-solid fa-pen-to-square w-4 text-gray-400"
               aria-hidden="true"></i>

            Edit Folder

        </a>

        <div class="my-1 border-t border-gray-100"></div>

        <form id="folder-delete-form" method="POST">

            @csrf
            @method('DELETE')

            <button type="submit"
                    class="flex w-full items-center gap-3 rounded-lg
                           px-3 py-2.5 text-left text-sm font-medium
                           text-red-600 hover:bg-red-50
                           focus:bg-red-50 focus:outline-none">

                <i class="fa-regular fa-trash-can w-4"
                   aria-hidden="true"></i>

                Delete Folder

            </button>

        </form>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const menu = document.getElementById('folder-context-menu');
    const editLink = document.getElementById('folder-edit-link');
    const deleteForm = document.getElementById('folder-delete-form');

    let selectedFolder = null;
    let activeButton = null;

    function closeMenu(restoreFocus = false) {
        menu.classList.add('hidden');

        if (activeButton) {
            activeButton.setAttribute('aria-expanded', 'false');

            if (restoreFocus) {
                activeButton.focus();
            }
        }

        activeButton = null;
        selectedFolder = null;
    }

    function openMenu(card, x, y) {
        closeMenu();

        selectedFolder = card;
        activeButton = card.querySelector('.folder-menu-button');

        editLink.href = card.dataset.editUrl;
        deleteForm.action = card.dataset.deleteUrl;
        activeButton.setAttribute('aria-expanded', 'true');

        menu.classList.remove('hidden');

        // Keep the menu inside the visible window.
        const margin = 8;
        const left = Math.max(
            margin,
            Math.min(x, window.innerWidth - menu.offsetWidth - margin)
        );
        const top = Math.max(
            margin,
            Math.min(y, window.innerHeight - menu.offsetHeight - margin)
        );

        menu.style.left = `${left}px`;
        menu.style.top = `${top}px`;

        editLink.focus();
    }

    document.querySelectorAll('.folder-card').forEach(function (card) {
        const button = card.querySelector('.folder-menu-button');

        card.addEventListener('contextmenu', function (event) {
            event.preventDefault();

            openMenu(card, event.clientX, event.clientY);
        });

        button.addEventListener('click', function (event) {
            event.stopPropagation();

            if (selectedFolder === card && !menu.classList.contains('hidden')) {
                closeMenu();
                return;
            }

            const rect = button.getBoundingClientRect();

            openMenu(card, rect.left, rect.bottom + 4);
        });
    });

    deleteForm.addEventListener('submit', function (event) {
        if (!selectedFolder) {
            event.preventDefault();
            return;
        }

        const name = selectedFolder.dataset.folderName;

        if (!window.confirm(`Delete the folder "${name}"?`)) {
            event.preventDefault();
        }
    });

    document.addEventListener('click', function (event) {
        if (!menu.contains(event.target)) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMenu(true);
        }
    });

    window.addEventListener('resize', () => closeMenu());

    // Ignore scrolling inside the menu itself.
    window.addEventListener('scroll', function (event) {
        if (!(event.target instanceof Node) || !menu.contains(event.target)) {
            closeMenu();
        }
    }, true);
});
</script>

@endsection

