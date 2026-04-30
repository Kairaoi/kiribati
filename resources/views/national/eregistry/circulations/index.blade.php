@extends('layouts.app')

@section('content')

    @if (session('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition.opacity.scale.80
            x-init="setTimeout(() => show = false, 4000)" 
            class="fixed right-0 bg-cyan-400 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3 z-50">
            <!-- Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke-width="2" 
                stroke="currentColor" 
                class="w-6 h-6 text-white">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>

            <!-- Message -->
            <span class="font-medium">{{ session('success') }}</span>

            <!-- Close button -->
            <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                &times;
            </button>
        </div>
    @endif

    {{-- <div class="container mx-auto font-montserrat px-4 max-w-7xl mt-2"> {{ Breadcrumbs::render('circulations.index') }} </div> --}}
    <div class="container mx-auto px-4 py-8 max-w-7xl mt-3 rounded-md min-h-screen ">
        <div class="mb-4 flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Incoming Mails</h1>
                <p class="text-gray-500 text-sm mt-1">
                    View and manage all internal and incoming files within your organisation.
                </p>
            </div>

            <a href="{{ route('registry.files.create.withType',['createType' => 'internal']) }}"
                class="inline-flex items-center mt-5 gap-2 px-4 py-2 bg-cyan-600 text-white text-sm rounded-md hover:bg-cyan-700 transition">
                <i class="fas fa-plus"></i>
                Create internal file
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">        
            <table id="circulations-table" class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wide">
                    <tr>
                        <th>Reference No</th>
                        {{-- <th>From</th> --}}
                        <th>File Date</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Review Officer</th>
                        <th>Assigned Officer(s)</th>
                        <th class="w-29">Actions</th>
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
                border-collapse: collapse;
                /* font-size: 0.85rem; */
                border-left: 0.5px solid #d3d3d8;
                border-right: 0.5px solid #d3d3d8;
            }



            /* Header */
            #circulations-table thead {
                background-color: #f9fafb;
                color: #6b7280;
                text-transform: uppercase;
                font-size: 12px;
            }

            /* Cells */
            #circulations-table th,
            #circulations-table td {
                padding: 14px 16px;
            }

            /* Row divider */
            #circulations-table tbody tr {
                border-bottom: 1px solid #e5e7eb;
            }

            /* Hover effect */
            #circulations-table tbody tr:hover {
                background-color: #f9fafb;
            }

            /* Fix long text */
            #circulations-table td {
                white-space: normal !important;
                /* word-break: break-word; */
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
                        background-color: rgb(32, 180, 39) !important;
                        color: #ffffff;
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
                        background-color: rgb(238, 45, 45) !important;
                        color: #ffffff;
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
                let userRole = "{{ auth()->user()->role }}"; // Get user role 
                let columns = [
                    { data: 'reference_no' },
                    { 
                            data: 'file_date',
                            render: function (data) {
                                if (!data) return '';
                                const date = new Date(data);
                                return date.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',   
                                    day: 'numeric'
                                });
                            }  
                    },
                    { data: 'file_subject'},
                    { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = '';

                                if (data === 'Dispatched') {
                                    badgeClass = 'bg-cyan-300 text-slate-800 text-xs px-2 py-1 rounded-full font-medium';
                                } else if (data === 'Pending Review') {
                                    badgeClass = 'bg-yellow-300 text-slate-800 text-xs px-2 py-1 rounded-full font-medium';
                                } else if (data === 'Reviewed') {
                                    badgeClass = 'bg-emerald-300 text-slate-800 text-xs px-2 py-1 rounded-full font-medium';
                                } else {
                                    badgeClass = 'bg-gray-300 text-slate-800 text-xs px-2 py-1 rounded-full font-medium';
                                }
                                return `<span class="${badgeClass}">${data}</span>`;
                            }
                    },
                    { data: 'reviewer_name'},
                    { data: 'officers' },
                    {
                            data: null, // means we'll render manually
                            name: 'actions',
                            orderable: false,
                            searchable: false,

                            render: function (data, type, row) {

                                let buttons = `<a href="/registry/file-circulations/${row.id}" 
                                                  class="btn btn-sm btn-outline-info" 
                                                  title="View">
                                                  <i class="fas fa-eye"></i>
                                                </a>
                                `;

                                if (row.file_initial_type === 'internal' ) //if file is uploaded from users ministry, then file can be edited
                                {
                                    buttons += `<a href="/registry/files/${row.id}/edit-type/internal" 
                                        class="btn btn-sm btn-outline-primary" 
                                        title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    `;
                                    buttons += `<button class="btn btn-sm btn-outline-danger delete-action" 
                                        data-id="${row.id}" 
                                        title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                `;
                                }

                                if (row.file_recipient_status === 'Reviewed') { //if file is reviewed, allow archiving
                                    buttons += `<button class="btn btn-sm btn-outline-warning archive-action" 
                                                data-file-id="${row.file_id}" 
                                                title="Archive">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    `;
                                }   

                                console.log('Row file id:', row.file_id);

                                return buttons; 
                            }
                        }

                ]

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
                $('#circulations-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('registry.file-circulations.datatables') }}",  // Updated route
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                        }
                    },
                    pageLength: 10,
                    responsive: true,
                    order: [[0, 'desc']],
                    dom: "<'row mb-3'<'col-md-6 d-flex align-items-center'B><'col-12 col-md-6 text-md-end text-start'f>>" +
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
                            title: 'Circulations',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fas fa-download"></i> PDF',
                            className: 'pdf-export-btn',
                            title: 'Circulations',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        }
                    ],
                    columns: columns
                });
            });

                // Handle delete action
                $(document).on('click', '.delete-action', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    
                    if (confirm('Are you sure you want to delete this file?')) {
                        $.ajax({
                            url: "{{ route('registry.file-circulations.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                            },
                            success(response) {
                                $('#circulations-table').DataTable().ajax.reload();
                                alert('File deleted successfully');
                            },
                            error(xhr) {
                                alert('Error deleting file');
                            }
                        });
                    }
                    closeAllDropdowns(); 
                });


                // Handle archive action
                $(document).on('click', '.archive-action', function(e) {
                    e.preventDefault();
                    const fileId = $(this).attr('data-file-id');
                    console.log('File ID:', fileId);
                    // if (!confirm('Are you sure you want to archive this file?')) return;

                    $.ajax({
                        url: route('registry.files.archive'), // POST route
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: { file_id: fileId }, // send ID in POST body
                        success: function(res) {
                            $('#circulations-table').DataTable().ajax.reload(); // refresh table
                            alert('File archived successfully');
                        },
                        error: function(xhr) {
                            alert(xhr.responseJSON?.error || 'Error archiving file');
                        }
                    });
                });


                function route(name, id) {
                    return {
                        'registry.file-circulations.show': "{{ route('registry.file-circulations.show', ':id') }}".replace(':id', id),
                        'registry.file-circulations.edit': "{{ route('registry.files.edit', ':id') }}".replace(':id', id),
                        'registry.file-circulations.destroy': "{{ route('registry.file-circulations.destroy', ':id') }}".replace(':id', id),
                        'registry.files.archive': "{{ route('registry.files.archive') }}"
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
