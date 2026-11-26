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

    <div class="container px-4 py-8 max-w-screen-2xl mx-auto px-3 sm:px-6 lg:px-8 bg-white shadow-lg mt-4 mb-4 rounded-md min-h-screen">
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
                <div class="mb-6">
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Subfolders
                    </h3>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach($subfolders as $subfolder)
                            <a href="{{ route('registry.folders.show', $subfolder) }}"
                            class="group flex items-center gap-3 rounded-lg border border-gray-200
                                    bg-white p-4 shadow-sm transition
                                    hover:shadow-md
                                    focus:outline-none focus:ring-2">

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center
                                            rounded-lg text-cyan-600">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-7 w-7"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        aria-hidden="true">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" />
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="break-words text-sm font-semibold text-gray-800
                                            group-hover:text-cyan-800">
                                        {{ $subfolder->name }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        Open folder
                                    </p>
                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 shrink-0 text-gray-400 group-hover:text-cyan-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    aria-hidden="true">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m9 5 7 7-7 7" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="overflow-x-auto mt-4 rounded-md border border-gray-300 p-4">
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
        /* Filing table */
        #folder-files-table {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #dbe3e8;
            border-radius: 10px;
            background: #ffffff;
            font-size: 13px;
            color: #334155;
        }

        /* Column headings */
        #folder-files-table thead th {
            padding: 13px 16px;
            background: #f3f4f6;
            border-bottom: 2px solid #d1d5db;
            color: #4b5563;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            text-align: left;
            vertical-align: middle;
            white-space: nowrap;
        }

        #folder-files-table thead th:first-child {
            border-top-left-radius: 10px;
        }

        #folder-files-table thead th:last-child {
            border-top-right-radius: 10px;
        }

        /* Document rows */
        #folder-files-table tbody td {
            padding: 13px 16px;
            border-bottom: 1px solid #e5ebf0;
            background: #ffffff;
            vertical-align: middle;
            line-height: 1.5;
            white-space: normal !important;
            overflow-wrap: anywhere;
            box-shadow: none !important;
        }

        #folder-files-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        #folder-files-table tbody tr:hover td {
            background: #f3f4f6;
        }

        #folder-files-table tbody tr:last-child td {
            border-bottom: none;
        }

        #folder-files-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }

        #folder-files-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }

        /* Document links */
        #folder-files-table tbody td a:not(.dropdown-item):not(.action-btn) {
            color: #374151;
            font-weight: 600;
            text-decoration: none;
        }

        #folder-files-table tbody td a:not(.dropdown-item):not(.action-btn):hover {
            color: #111827;
            text-decoration: underline;
        }

        /* Optional column classes */
        #folder-files-table .file-reference {
            color: #374151;
            font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
            font-size: 12px;
            font-weight: 600;
        }

        #folder-files-table .file-subject {
            min-width: 220px;
            color: #1e293b;
            font-weight: 500;
        }

        #folder-files-table .file-date {
            color: #64748b;
            font-size: 12px;
            white-space: nowrap !important;
        }

        /* Search and page-size controls */
        #folder-files-table_wrapper .dataTables_filter,
        #folder-files-table_wrapper .dataTables_length {
            margin-bottom: 16px;
            color: #64748b;
            font-size: 12px;
        }

        #folder-files-table_wrapper .dataTables_filter input,
        #folder-files-table_wrapper .dataTables_length select {
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background-color: #ffffff;
            color: #334155;
            font-size: 13px;
            outline: none;
        }

        #folder-files-table_wrapper .dataTables_filter input {
            width: 260px;
            max-width: 100%;
            margin-left: 8px;
            padding: 8px 12px;
        }

        #folder-files-table_wrapper .dataTables_length select {
            margin: 0 6px;
            padding: 6px 30px 6px 10px;
        }

        #folder-files-table_wrapper .dataTables_filter input:focus,
        #folder-files-table_wrapper .dataTables_length select:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.12);
        }

        /* Record count */
        #folder-files-table_wrapper .dataTables_info {
            padding-top: 18px;
            color: #64748b;
            font-size: 12px;
        }

        /* Pagination */
        #folder-files-table_wrapper .dataTables_paginate {
            padding-top: 14px;
        }

        #folder-files-table_wrapper .dataTables_paginate .paginate_button {
            margin: 0 2px;
            padding: 6px 11px;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px;
            background: #ffffff !important;
            color: #475569 !important;
            font-size: 12px;
            box-shadow: none !important;
            cursor: pointer;
        }

        #folder-files-table_wrapper .dataTables_paginate .paginate_button:hover {
            border-color: #d1d5db !important;
            background: #f3f4f6 !important;
            color: #374151 !important;
        }

        #folder-files-table_wrapper .dataTables_paginate .paginate_button.current {
            border-color: #4b5563 !important;
            background: #4b5563 !important;
            color: #ffffff !important;
        }

        #folder-files-table_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.45;
            cursor: default;
        }

        /* Row actions */
        #folder-files-table .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 10px;
            border: 1px solid #dbe3e8;
            border-radius: 6px;
            background: #ffffff;
            color: #475569;
            font-size: 12px;
            cursor: pointer;
        }

        #folder-files-table .action-btn:hover {
            border-color: #9ca3af;
            background: #f3f4f6;
            color: #374151;
        }

        /* Action dropdown */
        #folder-files-table .dropdown-menu {
            position: absolute;
            min-width: 170px;
            padding: 5px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
            z-index: 1000;
        }

        #folder-files-table .dropdown-item {
            display: block;
            padding: 9px 12px;
            border-radius: 5px;
            color: #334155;
            font-size: 12px;
            text-decoration: none;
        }

        #folder-files-table .dropdown-item:hover {
            background: #f3f4f6;
            color: #111827;
        }

        /* Export buttons */
        #folder-files-table_wrapper .excel-export-btn,
        #folder-files-table_wrapper .pdf-export-btn {
            display: inline-flex !important;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
            padding: 7px 12px !important;
            border: 1px solid #dbe3e8 !important;
            border-radius: 6px !important;
            background: #ffffff !important;
            color: #475569 !important;
            font-size: 12px !important;
            font-weight: 500;
            box-shadow: none !important;
            cursor: pointer;
        }

        #folder-files-table_wrapper .excel-export-btn:hover {
            border-color: #86efac !important;
            background: #f0fdf4 !important;
            color: #15803d !important;
        }

        #folder-files-table_wrapper .pdf-export-btn:hover {
            border-color: #fca5a5 !important;
            background: #fef2f2 !important;
            color: #b91c1c !important;
        }

        /* Empty table */
        #folder-files-table td.dataTables_empty {
            padding: 40px 16px;
            color: #94a3b8;
            text-align: center;
        }

        @media (max-width: 640px) {
            #folder-files-table thead th,
            #folder-files-table tbody td {
                padding: 10px 12px;
            }

            #folder-files-table_wrapper .dataTables_filter input {
                width: 180px;
            }
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