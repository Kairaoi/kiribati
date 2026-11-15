@extends('layouts.app')

@section('content')

    <div class="container mx-auto px-4 py-8 max-w-full rounded-md min-h-screen">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manage all filed documents</h1>
                <form action="{{ route('registry.folders.index') }}" method="GET" class="mt-4 flex items-center gap-2">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Enter folder name or file name/reference number..."
                        class="sm:w-96 rounded-lg border-gray-300 text-sm shadow-sm
                            focus:border-cyan-500 focus:ring-cyan-500"
                    >

                    <button
                        type="submit"
                        class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold
                            text-white hover:bg-cyan-700">
                        Search
                    </button>

                    @if (trim(request('search', '')) !== '')
                        <a href="{{ route('registry.folders.index') }}"
                        class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Clear search
                        </a>
                    @endif
                </form>
            </div>
            <div >
                
            </div>
            <div>
            <a href="{{ route('registry.folders.create') }}"
                    class="mt-4 inline-flex items-center rounded-md gap-2 px-4 py-2 bg-cyan-600 text-white text-sm font-medium shadow-sm hover:bg-cyan-700 transition">
                    <i class="fas fa-plus"></i>
                    Create New Folder
                </a>
            </div>
        </div>

        @if ($fileResults !== null)
            <section class="mb-6">
                <h2 class="mb-3 text-lg font-semibold text-gray-900">
                    Filed Documents
                </h2>

                @forelse ($fileResults as $assignment)
                    <div class="mb-2 rounded-lg border border-gray-200 p-3">
                        <p class="text-xs text-gray-500">
                            {{ $assignment->file->reference_no }}
                        </p>

                        <p class="font-medium text-gray-900">
                            {{ $assignment->file->subject }}
                        </p>

                        <a href="{{ route('registry.files.show', $assignment->file->uuid) }}"
                        class="text-sm text-cyan-700 hover:underline">
                            Open File
                        </a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No matching filed documents.</p>
                @endforelse

                {{ $fileResults->links() }}
            </section>
        @endif

        <div class="space-y-6 p-2"> 
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
                @foreach($folders as $folder)   
                        <div class="folder-card group relative rounded-xl border border-gray-200 bg-white shadow-sm transition hover:border-gray-400 hover:shadow-md"
                            data-edit-url="{{ route('registry.folders.edit', $folder->uuid) }}"
                            data-delete-url="{{ route('registry.folders.destroy', $folder->uuid) }}"
                            data-folder-name="{{ $folder->name }}"
                        >
                            <a href="{{ route('registry.folders.show', $folder->uuid) }}"
                            class="block rounded-xl p-4 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-10 w-10 text-gray-400 group-hover:text-gray-500"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path d="M2 4a2 2 0 012-2h4l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V4z" />
                                </svg>

                                <div class="mt-3">
                                    <p class="truncate text-sm font-medium text-gray-900">
                                        {{ $folder->name }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $folder->file_assignments_count ?? 0 }} files
                                    </p>
                                </div>
                            </a>

                            <button
                                type="button"
                                class="folder-menu-button absolute right-3 top-3 rounded px-2 py-1
                                    text-gray-500 hover:bg-gray-100 hover:text-gray-800
                                    focus:outline-none focus:ring-2 focus:ring-gray-400"
                                aria-label="Actions for {{ $folder->name }}"
                                aria-controls="folder-context-menu"
                                aria-expanded="false"
                            >
                                ⋮
                            </button>
                        </div>
                @endforeach
            </div>
                
            <div
                    id="folder-context-menu"
                    class="fixed z-50 hidden w-40 overflow-hidden rounded-lg border
                        border-gray-200 bg-white py-1 shadow-lg"
                >
                    <a
                        id="folder-edit-link"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100
                            focus:bg-gray-100 focus:outline-none"
                    >
                        Edit folder
                    </a>

                    <form id="folder-delete-form" method="POST">
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="block w-full px-4 py-2 text-left text-sm text-red-600
                                hover:bg-red-50 focus:bg-red-50 focus:outline-none"
                        >
                            Delete folder
                        </button>
                    </form>
                </div>
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

