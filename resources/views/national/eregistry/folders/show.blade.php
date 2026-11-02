@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition.opacity.scale.80
            x-init="setTimeout(() => show = false, 4000)" 
            class="fixed right-0 bg-cyan-400 px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3 z-50">
            <!-- Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke-width="2" 
                stroke="currentColor" 
                class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>

            <!-- Message -->
            <span class="font-medium">{{ session('success') }}</span>

            <!-- Close button -->
            <button @click="show = false" class="ml-4 hover:text-gray-200">
                &times;
            </button>
        </div>
    @endif

    <div class="container mx-auto px-4 py-8 max-w-6xl bg-white shadow-lg mt-4 mb-4 rounded-md min-h-screen">
        <div class="mb-1 flex items-center justify-between">
            <div>
                <nav aria-label="Folder breadcrumb" class="mb-4">
                    <ol class="flex flex-wrap items-center gap-2 text-md">
                        <li>
                            <a href="{{ route('registry.folders.index') }}" class="text-cyan-700 hover:underline">
                                E-Filing
                            </a>
                        </li>
                        @foreach ($breadcrumbs as $breadcrumb)
                            <li class="flex items-center gap-2">
                                <span class="text-gray-400" aria-hidden="true">/</span>
                                @if ($loop->last)
                                    <span class="font-medium text-gray-800"
                                        aria-current="page"
                                    >
                                        {{ $breadcrumb->name }}
                                    </span>
                                @else
                                    <a
                                        href="{{ route('registry.folders.show', [
                                            'folder' => $breadcrumb->uuid
                                        ]) }}"
                                        class="text-cyan-700 hover:underline"
                                    >
                                        {{ $breadcrumb->name }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('registry.folders.create', ['parent' => $folder->uuid]) }}"
                    class="inline-flex items-center gap-2 px-4 rounded-md py-2 bg-cyan-600 text-white text-sm font-medium shadow-sm hover:bg-cyan-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke-width="2" 
                        stroke="currentColor" 
                        class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Sub-Folder
                </a>
            </div>
        </div>

        <div class="mb-2 flex items-center justify-between">
            @if($subfolders->isNotEmpty())
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
                    @foreach($subfolders as $subfolder)
                        <a href="{{ route('registry.folders.show', $subfolder) }}" class="rounded-lg border bg-white p-3 shadow-sm hover:shadow-md">
                            <div class="text-2xl text-cyan-600">
                                📁
                            </div>

                            <div class="mt-1 text-sm font-medium">
                                {{ $subfolder->name }}
                            </div>

                        </a>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="overflow-x-auto mt-4 rounded-md border border-gray-300 p-4">
            <span class="text-gray-800 text-sm block">Documents in this Folder:</span>
            <table id="folder-files-table" class="w-full text-sm text-left text-gray-500">
                <thead>
                    <tr>
                        <th>Reference No</th>
                        <th>Name/Subject</th>
                        <th>Date</th>
                        <th>Filed At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    @push('styles')
        <style>
            /* Table Styles */
            table.dataTable {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                border: 0.5px solid #d3d3d8;
                border-radius: 8px;
                overflow: hidden;
            }

            /* Header */
            #folder-files-table thead {
                background-color: #e9edee;
                color: #5c5d5f;
                font-size: 12px;
            }

            #folder-files-table thead th {
                padding: 8px 10px;
                font-weight: 600;
                text-align: left;
            }

            /* Cells */
            #folder-files-table td {
                padding: 12px 14px;
                white-space: normal !important;
                border-bottom: 1px solid #cfd4d7;
            }

            /* Row divider */
            #folder-files-table tbody tr {
                border-bottom: 1.5px solid #6b6969;
                border-top: 1.5px solid #6b6969;
                background-color: #ffffff;
            }

            /* Hover effect */
            #folder-files-table tbody tr:hover {
                background-color: #f9fafb;
            }

            .dataTables_filter input {
                border: 1px solid #d1d5db;
                border-radius: 8px;
                padding: 6px 10px;
                outline: none;
            }

            .dataTables_filter input:focus {
                ring: 2px solid #93c5fd;
            }

            .dataTables_length select {
                border-radius: 8px;
                padding: 4px 8px;
                padding-right: 30px; /* space for arrow */
                border: 1px solid #d1d5db;
            }

            /* Action Button Styles */ 
            /* .action-btn {
                cursor: pointer;
                padding: 0.5rem 1rem;
                border-radius: 9999px;
                font-size: 0.875rem;
                transition: background-color 0.3s, transform 0.2s;
            }

            .action-btn:hover {
                background-color: #e5e7eb;
            } */

            /* Dropdown Menu Styles */
            .dropdown-menu {
                position: absolute;
                background-color: white;
                /* border-radius: 9999px; */
                box-shadow: 0 2px 5px rgba(0,0,0,0.15);
                min-width: 160px;
                z-index: 1000;
            }

            .dropdown-item {
                display: block;
                padding: 0.5rem 1rem;
                color: #212529;
            }

            .dropdown-item:hover {
                background-color: #f3f4f6; 
            }


        
            .dataTables_filter input {
                padding: 0.375rem 0.75rem !important;
                border-radius: 0.375rem !important;
                border: 1px solid #ced4da !important;
                font-size: 0.8rem !important;
                margin-top: 1rem !important; 
                font-family: 'Poppins', sans-serif !important;
            }

            .dataTables_info {
                font-size: 0.85rem !important;
                font-family: 'Poppins', sans-serif !important;
                color: #4b5563 !important; /* Tailwind gray-700 */
            }

            .dataTables_paginate {
                display: flex;
                gap: 6px;
                align-items: center;
            }

            .dataTables_paginate .paginate_button {
                padding: 6px 10px;
                border-radius: 6px;
                background: #f3f4f6;
                cursor: pointer;
            }

            .dataTables_paginate .current {
                background: #4f46e5 !important;
                color: white !important;
            }


                    /* Force green Excel button */
                    .excel-export-btn {
                        border: none !important;       /* Ensures no border, overrides any border below */
                        border-radius: 6px !important;
                        background-color: rgba(234, 236, 241, 0.76) !important;
                        color: #000000;
                        padding: 0.25rem 0.75rem !important;   /* smaller padding */
                        box-shadow: none !important;          /* removes inner or outer shadow */
                        background-image: none !important;    /* removes gradient */
                        /* padding: 0.5rem 2rem; */
                        font-size: 0.7rem !important;
                        display: inline-flex;
                        align-items: center;
                    }

                    .excel-export-btn:hover {
                        background-color: #f3f4f6;  
                        transform: translateY(-1px);
                        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
                    }

                    .pdf-export-btn {
                        border: none !important;       /* Ensures no border, overrides any border below */
                        border-radius: 6px !important;
                        background-color:  rgba(234, 236, 241, 0.76) !important;
                        color: #000000;
                        padding: 0.25rem 0.75rem !important;   /* smaller padding */
                        box-shadow: none !important;          /* removes inner or outer shadow */
                        background-image: none !important;    /* removes gradient */
                        /* padding: 0.5rem 2rem; */
                        font-size: 0.7rem !important;
                        display: inline-flex !important;
                        align-items: center;
                    }

                    .pdf-export-btn:hover {
                        background-color: #f3f4f6;  
                        transform: translateY(-1px);
                        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
                    }


        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.5/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.0/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.html5.min.js"></script>

        <script>
        $(document).ready(function () {
            const textRenderer = $.fn.dataTable.render.text();

            $('#folder-files-table').DataTable({
                processing: true,
                serverSide: true,

                ajax: {
                    url: @json(
                        route('registry.folders.files', [
                            'folder' => $folder->uuid,
                        
                        ])
                    ),
                    type: 'GET'
                },

                columns: [
                    {
                        data: 'reference_no',
                        name: 'files.reference_no',
                        render: textRenderer
                    },
                    {
                        data: 'subject',
                        name: 'files.subject',
                        render: textRenderer
                    },
                    {
                        data: 'letter_date',
                        name: 'files.letter_date',
                        render: textRenderer
                    },
                    {
                        data: 'filed_at',
                        name: 'ffa.filed_at',
                        render: textRenderer
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            if (type !== 'display') {
                                return '';
                            }

                            return `
                                <a href="/registry/files/${row.uuid}"
                                    class="inline-flex items-center border justify-center gap-2 rounded-md px-4 py-2 text-xs font-medium shadow-sm transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2"
                                >
                                    View
                                </a>

                                <button
                                    type="button"
                                    class="move-document-btn mt-1 inline-flex items-center shadow-sm gap-2 rounded-md border px-3 py-2 text-xs font-medium"
                                    data-file-id="${row.id}"
                                    data-file-uuid="${row.uuid}"
                                    data-file-reference="${row.reference_no ?? ''}"
                                    data-file-subject="${row.subject ?? ''}"
                                    title="Move document">
                                    Move
                                </button>
                            `;
                        }
                    }

                ],

                pageLength: 10,
                responsive: true,
                pagingType: 'simple_numbers',
                order: [[0, 'desc']],

                dom:
                    "<'row mb-3'<'col-md-6 d-flex align-items-center'B>" +
                    "<'col-12 col-md-6 text-md-end text-start'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",

                language: {
                    emptyTable: 'No documents have been filed in this folder.',
                    paginate: {
                        previous: '←',
                        next: '→'
                    }
                },

                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel me-1"></i> Excel',
                        className: 'excel-export-btn',
                        title: 'Folder Files',
                        exportOptions: {
                            columns: [0]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                        className: 'pdf-export-btn',
                        title: 'Folder Files',
                        exportOptions: {
                            columns: [0]
                        }
                    }
                ]
            });
        });
        
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('folderDestinationModal');
            const closeButton = document.getElementById('close-folder-modal');
            const cancelButton = document.getElementById('cancel-folder-modal');
            const submitButton = document.getElementById('file-to-folder-button');
            const referenceText = document.getElementById('modal-file-reference');
            const subjectText = document.getElementById('modal-file-subject');
            const selectedFolderContainer = document.getElementById('selected-folder-container');
            const selectedFolderName = document.getElementById('selected-folder-name');
            const folderChildrenRoute = @json(route('registry.folders.children', 
                [
                    'folder' => '__FOLDER__'
                ])
            );

            let selectedFileId = null;
            let selectedFileUuid = null;
            let selectedFolderId = null;

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.move-document-btn');
                if (!button) {
                    return;
                }
                selectedFileId = button.dataset.fileId;
                selectedFileUuid = button.dataset.fileUuid;
                selectedFolderId = null;

                referenceText.textContent = button.dataset.fileReference;
                subjectText.textContent = button.dataset.fileSubject;
                selectedFolderContainer.classList.add('hidden');
                submitButton.disabled = true;
                document.querySelectorAll('.folder-option')
                    .forEach(folder => {
                        folder.classList.remove('bg-cyan-50');
                    });

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });


            /*
            |--------------------------------------------------------------------------
            | Select folder and load its subfolders
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function (event) {
                const folder = event.target.closest('.folder-option');

                // Only handle folders inside this modal.
                if (!folder || !modal.contains(folder)) {
                    return;
                }

                document.querySelectorAll('#folderDestinationModal .folder-option')
                    .forEach(item => {
                        item.classList.remove('bg-cyan-50');
                    });

                folder.classList.add('bg-cyan-50');

                selectedFolderId = folder.dataset.folderId;
                selectedFolderName.textContent = folder.dataset.folderName;

                selectedFolderContainer.classList.remove('hidden');
                submitButton.disabled = false;

                const folderUuid = folder.dataset.folderUuid;

                if (!folderUuid) {
                    console.error('Missing data-folder-uuid on folder:', folder);
                        return;
                }

                loadFolder(folderUuid, folder.dataset.folderName);
            });

            //load subfolders
            async function loadFolder(folderUuid, folderName) {
                const folderList = document.getElementById('folder-list');

                folderList.innerHTML = `
                    <div class="p-5 text-center text-sm text-gray-500">
                        Loading...
                    </div>
                `;

                const url = folderChildrenRoute.replace(
                    '__FOLDER__',
                    folderUuid
                );

                try {
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                                'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Unable to load subfolders.');
                    }

                    const result = await response.json();

                    renderFolders(result.folders);

                } catch (error) {
                    folderList.innerHTML = `
                        <div class="p-5 text-center text-red-500">
                            ${error.message}
                        </div>
                    `;
                }
            }

            function renderFolders(folders) {
                const folderList = document.getElementById('folder-list');
                    folderList.innerHTML = '';
                    if (!folders.length) {
                        folderList.innerHTML = `
                            <div class="p-5 text-center text-sm text-gray-500">
                                No subfolders.
                            </div>
                        `;

                        return;
                    }
                    folders.forEach(folder => {
                            folderList.insertAdjacentHTML('beforeend', `
                                <button
                                    type="button"
                                    class="folder-option flex w-full items-center
                                        justify-between border-b px-4 py-3
                                        text-left hover:bg-gray-50"

                                    data-folder-id="${folder.id}"
                                    data-folder-uuid="${folder.uuid}"
                                    data-folder-name="${folder.name}"
                                >

                                    <div class="flex items-center gap-3">
                                        <span>📁</span>

                                        <span class="text-sm font-medium">
                                            ${folder.name}
                                        </span>
                                    </div>

                                    <span class="text-gray-400">
                                        &rsaquo;
                                    </span>

                                </button>
                            `);
                        });
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | File document
                    |--------------------------------------------------------------------------
                    */

                   submitButton.addEventListener('click', async function () {
                        if (!selectedFileUuid ||
                            selectedFileUuid === 'null' ||
                            selectedFileUuid === 'undefined' ||
                            !selectedFolderId) {
                            alert('Select a valid document and destination folder.');
                            return;
                        }

                        submitButton.disabled = true;
                        submitButton.textContent = 'Moving...';

                        try {
                            const routeTemplate = @json(
                                route('registry.folders.files.move', [
                                    'folder' => $folder->uuid,
                                    'file' => '__FILE__'
                                ])
                            );

                            const url = routeTemplate.replace(
                                '__FILE__',
                                encodeURIComponent(selectedFileUuid)
                            );

                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document
                                        .querySelector('meta[name="csrf-token"]')
                                        .content
                                },
                                body: JSON.stringify({
                                    destination_folder_id: selectedFolderId
                                })
                            });

                            const responseText = await response.text();

                            let result;

                            try {
                                result = JSON.parse(responseText);
                            } catch {
                                console.error('Filing response:', {
                                    status: response.status,
                                    url: response.url,
                                    redirected: response.redirected,
                                    body: responseText
                                });

                                throw new Error(
                                    `Server returned HTML instead of JSON (status ${response.status}). Check the browser Console.`
                                );
                            }

                            if (!response.ok) {
                                throw new Error(
                                    result.message ?? 'Unable to move document.'
                                );
                            }

                            closeModal();

                            $('#folder-files-table')
                                .DataTable()
                                .ajax
                                .reload(null, false);

                            alert(result.message ?? 'Document moved successfully.');

                        } catch (error) {
                            alert(error.message);

                        } finally {
                            submitButton.disabled = !selectedFileUuid || !selectedFolderId;
                            submitButton.textContent = 'Move Here';
                        }
                    });

                    
                    /*
                    |--------------------------------------------------------------------------
                    | Close
                    |--------------------------------------------------------------------------
                    */

                    function closeModal() {

                        modal.classList.add('hidden');
                        modal.classList.remove('flex');

                        selectedFileId = null;
                        selectedFolderId = null;
                    }

                    closeButton.addEventListener('click', closeModal);
                    cancelButton.addEventListener('click', closeModal);
            });

            submitButton.addEventListener('click', async function () {
                if (!selectedFileUuid ||
                    selectedFileUuid === 'null' ||
                    selectedFileUuid === 'undefined' ||
                    !selectedFolderId) {
                    alert('Select a valid document and destination folder.');
                    return;
                }

                submitButton.disabled = true;
                submitButton.textContent = 'Moving...';

                try {
                    const routeTemplate = @json(
                        route('registry.folders.files.move', [
                            'file' => '__FILE__',
                            'folder' => $folder->uuid
                        ])
                    );

                    const url = routeTemplate.replace(
                        '__FILE__',
                        encodeURIComponent(selectedFileUuid)
                    );

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            folder_id: selectedFolderId
                        })
                    });

                    const responseText = await response.text();

                    let result;

                    try {
                        result = JSON.parse(responseText);
                    } catch {
                        console.error('Filing response:', {
                            status: response.status,
                            url: response.url,
                            redirected: response.redirected,
                            body: responseText
                        });

                        throw new Error(
                            `Server returned HTML instead of JSON (status ${response.status}). Check the browser Console.`
                        );
                    }

                    if (!response.ok) {
                        throw new Error(
                            result.message ?? 'Unable to move document.'
                        );
                    }

                    closeModal();

                    $('#folder-files-table')
                        .DataTable()
                        .ajax
                        .reload(null, false);

                    alert(result.message ?? 'File moved successfully.');

                } catch (error) {
                    alert(error.message);

                } finally {
                    submitButton.disabled = !selectedFileUuid || !selectedFolderId;
                    submitButton.textContent = 'Move Here';
                }
            });
            
        </script>
    @endpush

    <div id="folderDestinationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
         role="dialog" aria-modal="true" aria-labelledby="folder-modal-title">
        <div class="flex min-h-0 w-full max-w-xl flex-col overflow-hidden rounded-lg bg-white shadow-xl" style="max-height: calc(100dvh - 2rem);">
            {{-- Header --}}
            <div class="flex shrink-0 items-start justify-between gap-3
                        border-b px-4 py-4 sm:px-6">
                <div class="min-w-0 flex-1">
                    <h2 id="folder-modal-title" class="text-lg font-semibold">
                        Move Document
                    </h2>

                    <p
                        id="modal-file-reference"
                        class="mt-1 break-words text-sm font-medium text-gray-700"
                    ></p>

                    <p
                        id="modal-file-subject"
                        class="break-words text-sm text-gray-500"
                    ></p>
                </div>

                <button
                    type="button"
                    id="close-folder-modal"
                    class="shrink-0 text-2xl leading-none text-gray-400
                        hover:text-gray-600"
                    aria-label="Close"
                >
                    &times;
                </button>
            </div>

            {{-- Scrollable body --}}
            <div class="min-h-0 flex-1 overflow-y-auto px-4 py-5 sm:px-6">
                <p class="mb-2 text-sm font-medium">
                    Select Destination Folder
                </p>

                <div id="folder-list" class="min-w-0 rounded-md border">
                    @foreach ($folders as $folder)
                        <button
                            type="button"
                            class="folder-option flex w-full min-w-0 items-center
                                justify-between gap-3 border-b px-4 py-3
                                text-left last:border-b-0 hover:bg-gray-50"
                            data-folder-id="{{ $folder->id }}"
                            data-folder-uuid="{{ $folder->uuid }}"
                            data-folder-name="{{ $folder->name }}"
                        >
                            <span class="flex min-w-0 flex-1 items-start gap-3">
                                <span class="shrink-0" aria-hidden="true">📁</span>

                                <span
                                    class="min-w-0 whitespace-normal text-sm
                                        font-medium [overflow-wrap:anywhere]"
                                >
                                    {{ $folder->name }}
                                </span>
                            </span>

                            <span
                                class="shrink-0 text-gray-400"
                                aria-hidden="true"
                            >
                                &rsaquo;
                            </span>
                        </button>
                    @endforeach
                </div>

                <div
                    id="selected-folder-container"
                    class="mt-4 hidden rounded-md bg-gray-50 p-3"
                >
                    <span class="text-xs text-gray-500">
                        Destination
                    </span>

                    <div
                        id="selected-folder-name"
                        class="text-sm font-semibold [overflow-wrap:anywhere]"
                    ></div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex shrink-0 flex-wrap justify-end gap-3
                        border-t px-4 py-4 sm:px-6">
                <button
                    type="button"
                    id="cancel-folder-modal"
                    class="rounded-md border px-4 py-2 text-sm"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    id="file-to-folder-button"
                    disabled
                    class="rounded-md bg-cyan-600 px-4 py-2 text-sm
                        font-medium text-white disabled:cursor-not-allowed
                        disabled:opacity-50"
                >
                    Move Here
                </button>
            </div>
        </div>
    </div>
@endsection