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

<div class="container mx-auto px-4 py-8 max-w-full mt-3 rounded-md min-h-screen ">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-wide">Divisions</h1>
            <p class="text-base text-gray-500 mt-1">
                View and manage all divisions in your ministry.
            </p>
        </div>

        <a href="{{ route('registry.divisions.create') }}"
            class="inline-flex items-center mt-5 gap-2 px-4 py-2 bg-cyan-600 text-white text-sm rounded-md hover:bg-cyan-700 transition">
            <i class="fas fa-plus"></i>
            Create New Division
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2">
        <table id="divisionsTable" class="bg-gray-50 text-gray-800 text-sm divide-y divide-gray-200 stripe">
            <thead>
                <tr>
                    <th>Ministry</th>
                    <th>Name</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody> <!-- DataTable will populate this -->
        </table>
    </div>
</div>

@endsection

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
        #divisionsTable thead {
            background-color: #e9edee;
            color: #5c5d5f;
            font-size: 12px;
        }

        #divisionsTable thead th {
            padding: 8px 10px;
            font-weight: 600;
            
        }

        /* Cells */
        #divisionsTable td {
            padding: 12px 14px;
            white-space: normal !important;
            border-bottom: 1px solid #cfd4d7;
        }

        /* Row divider */
        #divisionsTable tbody tr {
            border-bottom: 1.5px solid #6b6969;
            border-top: 1.5px solid #6b6969;
            background-color: #ffffff;
        }

        /* Hover effect */
        #divisionsTable tbody tr:hover {
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
    $('#divisionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('registry.divisions.datatables') }}",  // Updated route
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            }
        },
        columns: [
            { data: 'ministry_name' },
            { data: 'division_name' },
            { data: 'created_at', 
                render(data) {
                    return new Date(data).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }
            },
            { data: 'updated_at', 
                render(data) {
                    return new Date(data).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }
            },

            {
                data: null,
                name: 'actions',
                orderable: false,
                searchable: false,
                render(data, type, row) {

                    let actions = `
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
                    `;

                    if (row.can_delete) {
                        actions += `
                            <button class="inline-flex items-center justify-center hover:text-red-600 transition ms-3 delete-action" 
                                    data-id="${row.id}" 
                                    title="Delete">
                                <i class="fa fa-trash text-sm"></i>
                            </button>
                        `;
                    }

                    return actions;
                }
            }
        ],
        pageLength: 10,
        responsive: true,
        pageLength: 10,
        pagingType: "simple_numbers",
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
                text: '<i class="fas fa-file-excel me-1"></i> Excel',
                className: 'excel-export-btn',
                title: 'Divisions',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                className: 'pdf-export-btn',
                title: 'Divisions',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });


    // Handle delete action
    $(document).on('click', '.delete-action', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this division?')) {
            $.ajax({
                url: route('registry.divisions.destroy', id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                success(response) {
                    $('#divisionsTable').DataTable().ajax.reload();
                    alert('Division deleted successfully');
                },
                error(xhr) {
                    alert('Error deleting division');
                }
            });
        }
        closeAllDropdowns(); 
    });

    function route(name, id) {
        return {
            'registry.divisions.show': "{{ route('registry.divisions.show', ':id') }}".replace(':id', id),
            'registry.divisions.edit': "{{ route('registry.divisions.edit', ':id') }}".replace(':id', id),
            'registry.divisions.destroy': "{{ route('registry.divisions.destroy', ':id') }}".replace(':id', id)
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
});

</script>
@endpush
