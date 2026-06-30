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

    <div class="container mx-auto px-4 py-8 max-w-full rounded-md min-h-screen">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Correspondence Files</h1>
            </div>
            <div>
                <a href="{{ route('registry.files.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-cyan-700 transition">
                    <i class="fas fa-plus text-xs"></i>
                    Correspondence File
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class=" border-b border-gray-200">
            <nav class="flex space-x-8">
                <!-- Active -->
                <a href="{{ route('registry.files.index', ['type' => 'active']) }}"
                    class="pb-3 text-sm font-semibold border-b-2 transition
                    {{ request('type', 'active') === 'active'
                        ? 'border-cyan-600 text-cyan-700'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Active Files
                </a>

                @if(auth()->user()->hasRole(['registry', 'admin', 'sro']))
                    <!-- Closed -->
                    <a href="{{ route('registry.files.index', ['type' => 'closed']) }}"
                        class="pb-3 text-sm font-semibold border-b-2 transition
                        {{ request('type') === 'closed'
                            ? 'border-cyan-600 text-cyan-700'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Closed Files
                    </a>

                    <!-- Archived -->
                    <a href="{{ route('registry.files.index', ['type' => 'archived']) }}"
                        class="pb-3 text-sm font-semibold border-b-2 transition
                        {{ request('type') === 'archived'
                            ? 'border-cyan-600 text-cyan-700'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Archived Files
                    </a>
                @endif
            </nav>
        </div>

        @if(auth()->user()->hasRole(['registry', 'admin', 'sro']))
            @if(request('type') === 'closed')
                <div class="bg-gray-50 p-3 rounded-md">
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-2 text-sm">

                        <!-- Year -->
                        <div>
                            <label class="block text-gray-500 mb-1 font-semibold">Type</label>
                            <select id="initialtypeFilter"
                                class="w-full rounded-md border-gray-200 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                                <option value="all">All</option>
                                <option value="dispatch">Dispatched</option>
                                <option value="received">Received</option>
                                <option value="internal">Internal</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-500 mb-1 font-semibold">Organisation</label>
                            <select id="organisationFilter"
                                            name="organisation"
                                            class="w-full rounded-md border-gray-200 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                                        <option value="">All</option>
                                        @foreach ($organisations as $org)
                                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                                        @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-500 mb-1 font-semibold">File Type</label>
                            <select id="fileType"
                                            name="organisation"
                                            class="w-full rounded-md border-gray-200 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                                        <option value="">All</option>
                                        @foreach ($file_types as $fileType)
                                            <option value="{{ $fileType->id }}">{{ $fileType->name }}</option>
                                        @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-500 mb-1 font-semibold">Category</label>
                            <select id="category"
                                            name="organisation"
                                            class="w-full rounded-md border-gray-200 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                                        <option value="">All</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-500 mb-1 font-semibold">From Date</label>
                            <input type="date" id="dateFrom"
                                class="w-full rounded-md border-gray-200 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                        </div>

                        <div>
                            <label class="block text-gray-500 mb-1 font-semibold">To Date</label>
                            <input type="date" id="dateTo"
                                class="w-full rounded-md border-gray-200 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                        </div>

                        <!-- Reset -->
                        <div class="flex items-end">
                            <button id="reset-filters"
                                class="w-full text-xs font-semibold px-2 py-1.5 bg-gray-200 hover:bg-gray-300 rounded-md">
                                Reset
                            </button>
                        </div>

                    </div>
                </div>
            @endif
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2">        
            <table id="filesTable" class="bg-gray-50 text-gray-800 text-sm divide-y divide-gray-200 stripe">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Circulation ID</th>
                        <th class="uppercase">Reference No</th>
                        <th class="uppercase">Name/Subject</th>
                        <th class="uppercase">File Type</th>
                        <th class="uppercase">Due Date</th>
                        <th class="uppercase">Dispatch Date</th>
                        <th class="uppercase">Received Date</th>
                        <th class="uppercase">Status</th>
                        <th class="uppercase">Circulation Status</th>
                        <th class="w-28">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y"></tbody> <!-- DataTable will populate this -->
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
        #filesTable thead {
            background-color: #e9edee;
            color: #5c5d5f;
            font-size: 12px;
        }

        #filesTable thead th {
            padding: 8px 10px;
            font-weight: 600;
            
        }

        /* Cells */
        #filesTable td {
            padding: 12px 14px;
            white-space: normal !important;
            border-bottom: 1px solid #cfd4d7;
        }

        /* Row divider */
        #filesTable tbody tr {
            border-bottom: 1.5px solid #6b6969;
            border-top: 1.5px solid #6b6969;
            background-color: #ffffff;
        }

        /* Hover effect */
        #filesTable tbody tr:hover {
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
            $(document).ready(function() {
                let activeDropdown = null;
                const isReviewOfficer = @json(auth()->user()->hasRole('review-officer'));
                const isAdmin = @json(auth()->user()->hasRole('admin'));
                const isRegistry = @json(auth()->user()->hasRole('registry'));

                // Close dropdown when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.action-dropdown').length) {
                        closeAllDropdowns();
                    }
                });

                function closeAllDropdowns() {
                    $('.dropdown-menu').remove();
                    if (activeDropdown) {
                        activeDropdown.removeClass('active');
                        activeDropdown = null; 
                    }
                }

                // Initialize DataTable
                let table = $('#filesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('registry.files.datatables') }}",  // Updated route
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                        },
                        data: function (d) {
                            d.type = "{{ request('type', 'active') }}";

                            d.selected_type = $('#initialtypeFilter').val() || '';
                            d.organisation_id = $('#organisationFilter').val() || '';
                            d.file_type = $('#fileType').val() || '';
                            d.category = $('#category').val() || '';
                            d.date_from = $('#dateFrom').val() || '';
                            d.date_to = $('#dateTo').val() || '';

                            console.log('DataTable sending:', {
                                type: d.type,
                                selected_type: d.selected_type,
                                organisation_ids: d.organisation_id,
                                date_from: d.date_from,
                                date_to: d.date_to,
                            });
                        }
                    },
                    columns: [
                        { data: 'id', name: 'files.id', visible: false},
                        { data: 'circulation_id', name: 'fc.id', visible: false},
                        { data: 'reference_no' },
                        { data: 'file_subject' },
                        { data: 'file_type'},
                        {  data: 'due_date',
                            render: function (data) {
                                if (!data) return '-';
                                const date = new Date(data);
                                return date.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                });
                            }
                        },
                        {
                            data: 'dispatch_date',
                            render: function (data) {
                                if (!data) return '-';
                                const date = new Date(data);
                                return date.toLocaleString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });
                            }
                        },
                       {
                            data: 'received_at',
                            render: function (data) {
                                if (!data) return '-';
                                const date = new Date(data);
                                return date.toLocaleString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });
                            }
                        },
                        { 
                            data: 'file_status', name: 'files.status',
                            render: function(data, type, row) {
                                let badgeClass = '';

                                if (data === 'Pending Action') {
                                    badgeClass = 'bg-red-300 text-xs text-slate-700 px-2 py-1 rounded-full font-semibold';
                                } else if (data === 'Dispatched') {
                                    badgeClass = 'bg-cyan-300 text-xs text-slate-700 px-2 py-1 rounded-full font-semibold';
                                } else if (data === 'Pending Review') {
                                    badgeClass = 'bg-yellow-200 text-xs text-slate-700 px-2 py-1 rounded-full font-semibold';
                                } else if (data === 'Pending SRO Approval') {
                                    badgeClass = 'bg-yellow-200 text-xs text-slate-700 px-2 py-1 rounded-full font-semibold';
                                } else if (data === 'Reviewed') {
                                    badgeClass = 'bg-blue-300 text-xs text-slate-700 px-2 py-1 rounded-full font-semibold';
                                } else if (data === 'Received') {
                                    badgeClass = 'bg-blue-300 text-xs text-slate-700 px-2 py-1 rounded-full font-semibold';
                                } else if (data === 'Approved') {
                                    badgeClass = 'bg-green-300 text-xs text-slate-700 px-2 py-1 rounded-full font-semibold';
                                } else if (data === 'Rejected') {
                                    badgeClass = 'bg-red-300 text-xs text-slate-700 px-2 py-1 rounded-full font-semibold';
                                }
                                else {
                                    badgeClass = 'bg-gray-300 text-xs text-slate-700 px-2 py-1 rounded-full font-semibold';
                                }

                                return `<span class="${badgeClass}">${data}</span>`;
                            }
                        },
                        { data: 'circulation_status', name: 'fc.status', visible: false},
                        {
                            data: null,
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row) {
                                let buttons = '';

                                // Pending action → View + Edit + Delete
                                if (row.file_status === 'Pending Action' && isRegistry) {
                                    buttons = `
                                        <a href="/registry/files/${row.id}" 
                                            class="inline-flex items-center justify-center hover:text-gray-600 transition"
                                            title="View">
                                            <i class="fa fa-eye text-sm"></i>
                                        </a>

                                        <a href="/registry/files/${row.id}/edit" 
                                            class="inline-flex items-center justify-center hover:text-gray-600 transition ms-3"
                                            title="Edit">
                                            <i class="fa fa-pen text-sm"></i>
                                        </a>

                                        <button class="inline-flex items-center justify-center hover:text-red-600 transition ms-3 delete-action" 
                                                data-id="${row.id}" 
                                                title="Delete">
                                            <i class="fa fa-trash text-sm"></i>
                                        </button>
                                    `;

                                // Other statuses → View only
                                } else if ((row.circulation_status === 'Reviewed' || row.circulation_status === 'Approved' || row.circulation_status === 'Rejected') && isReviewOfficer) {
                                    buttons = `
                                        <a href="/registry/file-circulations/${row.circulation_id}/overlays/edit" 
                                            class="inline-flex items-center justify-center hover:text-gray-600 transition"
                                            title="Edit Overlays">
                                            Edit Overlays
                                        </a>
                                    `;
                                } else if (isReviewOfficer){
                                    buttons = `
                                        <a href="/registry/files/${row.id}" 
                                            class="inline-flex items-center justify-center hover:text-gray-600 transition"
                                            title="View">
                                            Review
                                        </a>
                                    `;
                                
                                } else {
                                    buttons = `
                                        <a href="/registry/files/${row.id}" 
                                            class="inline-flex items-center justify-center hover:text-gray-600 transition"
                                            title="View">
                                            <i class="fa fa-eye text-sm"></i>
                                        </a>
                                    `;
                                }
                                return buttons;
                            }
                        }
                    ],
                    pageLength: 10,
                    pagingType: "simple_numbers",
                    responsive: true,
                    order: [[0, 'desc']],
                    dom:   "<'row mb-3'<'col-md-6 d-flex align-items-center'B><'col-12 col-md-6 text-md-end text-start'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
                    language: {
                        paginate: {
                            previous: "←",
                            next: "→"
                        }
                    },
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-download"></i> EXCEL',
                            className: 'excel-export-btn',
                            title: 'Outward Files Registry',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fas fa-download"></i> PDF',
                            className: 'pdf-export-btn',
                            title: 'Outward Files Registry',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        }
                    ]
                })

                  // Reload table when initial type changes
                $('#initialtypeFilter').on('change', function() {
                    table.ajax.reload();
                }); 

                // Reload table when organisation changes
                $('#organisationFilter').on('change', function() {
                    table.ajax.reload();
                });

                $('#category').on('change', function() {
                    table.ajax.reload();
                });

                $('#fileType').on('change', function() {
                    table.ajax.reload();
                });

                // Reload table when date range changes
                $('#dateFrom, #dateTo').on('change', function() {
                    table.ajax.reload();
                });


            });

                // Handle delete action
                $(document).on('click', '.delete-action', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    
                    if (confirm('Are you sure you want to delete this file?')) {
                        $.ajax({
                            url: "{{ route('registry.files.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                            },
                            success(response) {
                                $('#filesTable').DataTable().ajax.reload();
                                alert('File deleted successfully');
                            },
                            error(xhr) {
                                alert('Error deleting file');
                            }
                        });
                    }
                    closeAllDropdowns(); 
                });

                function route(name, id) {
                    return {
                        'registry.files.show': "{{ route('registry.files.show', ':id') }}".replace(':id', id),
                        'registry.files.edit': "{{ route('registry.files.edit', ':id') }}".replace(':id', id),
                        'registry.file-circulations.edit': "{{ route('registry.file-circulations.edit', ':circulation_id') }}".replace(':circulation_id', circulation_id),
                        'registry.files.destroy': "{{ route('registry.files.destroy', ':id') }}".replace(':id', id),
                    }[name];
                }

                // Open PDF Viewer Modal
                $(document).on('click', '.view-btn', function(e) {
                    const filePath = $(this).closest('tr').find('td').eq(8).text().trim(); // Extract file path from table
                    
                    // Update PDF Embed Source
                    $('#pdfEmbed').attr('src', '{{ asset('storage') }}/' + filePath);
                    $('#pdfViewerModal').fadeIn();
                });

                // Close PDF Viewer Modal
                $('#closePdfModal').on('click', function() {
                    $('#pdfViewerModal').fadeOut();
                });
            
        
        </script>
    @endpush
@endsection