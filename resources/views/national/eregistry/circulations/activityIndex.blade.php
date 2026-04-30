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
<div class="container mx-auto font-poppins px-4 py-8 max-w-6xl mt-3 rounded-md min-h-screen ">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-wide">File Activity</h1>
            <p class="text-base text-gray-500 mt-1">
    
            </p>
        </div>

        {{-- <a href="{{ route('registry.files.create.withType',['createType' => 'internal']) }}"
            class="inline-flex items-center mt-5 gap-2 px-4 py-2 bg-cyan-600 text-white text-sm rounded-md hover:bg-cyan-700 transition">
            <i class="fas fa-plus"></i>
            Create file circulation
        </a> --}}
    </div>

    <div class="p-4 bg-white rounded-lg shadow-lg overflow-hidden">
        <table id="activityTable" class="table table-striped w-full mt-6">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Activity</th>
                    <th>Date</th>
                    <th>Subject</th>
                    {{-- <th>Activity</th> --}}
                    <th class="w-30">Actions</th>
                </tr>
            </thead>
            <tbody></tbody> <!-- DataTable will populate this -->
        </table>
    </div>
</div>

@endsection

@push('styles')
<style>

    h1 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #0a44e3; 
    }

    p {
        font-family: 'Poppins', sans-serif;
        color: #3175c2; 
    }
    /* Table Styles */
   table.dataTable {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        border-left: 0.5px solid #d3d3d8;
        border-right: 0.5px solid #d3d3d8;
    }

    .table.dataTable thead th {
        background-color: #ffffff; 
        border-bottom: 3px solid #d3d3d8;
        border-top: 0.5px solid #d3d3d8;
        color: #000000;
        font-family: 'Poppins', sans-serif;
        padding: 1.5rem;
        padding-top: 0.5rem; 
        padding-bottom: 1rem; 
        font-size: 0.9rem;
        text-align: left;
    }

    .table.dataTable td {
        vertical-align: middle;
        padding: 1.5rem;
        color: #4c4c53;
        font-size: 0.9rem;
        
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
        font-size: 0.9rem !important;
        font-family: 'Poppins', sans-serif !important;
        color: #4b5563 !important;
    }

    .dataTables_paginate.paginate_button {
        padding: 0.25rem 0.5rem;
        margin: 0 2px;
        border-radius: 4px;
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
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script> --}}
<script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.html5.min.js"></script>

<script>

$(document).ready(function() {

    // Initialize DataTable
    $('#activityTable').DataTable({
        processing: true,
        serverSide: true,
        language: {
            paginate: {
                previous: 'Previous ',
                next: ' Next'
            }
        },
        ajax: {
            url: "{{ route('registry.file-circulations.activity.datatables') }}",  // Updated route
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            }
        },
        columns: [
            { data: 'id', visible: false},
            { data: 'activity_type'},
            // {
            //     data: 'activity_type',
            //     render: function(data) {
            //         if (data === 'review') {
            //         return '<span class="badge bg-yellow-500">Review</span>';
            //         }
            //         if (data === 'assigned') {
            //         return '<span class="badge bg-cyan-500">Assigned</span>';
            //         }
            //         return data;
            //     }
            // },
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
            { data: 'file_subject' },
            {
                data: null, // means we'll render manually
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <a href="/registry/file-circulations/${row.id}" class="btn btn-sm btn-info text-slate-900">View</a>
                    `;
                }
            }
        ],
        pageLength: 10,
        responsive: true,
        order: [[0, 'desc']],
        dom: "<'row mb-3'<'col-md-6 d-flex align-items-center'B><'col-12 col-md-6 text-md-end text-start'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
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
                    ]
      
    });


    // Handle delete action
    $(document).on('click', '.delete-action', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this file?')) {
            $.ajax({
                url: route('registry.files.destroy', id),
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

    function route(name, id) {
        return {
            'registry.file-circulations.show': "{{ route('registry.file-circulations.show', ':id') }}".replace(':id', id),
            // 'registry.file-circulations.edit': "{{ route('registry.files.edit', ':id') }}".replace(':id', id),
            // 'registry.file-circulations.destroy': "{{ route('registry.files.destroy', ':id') }}".replace(':id', id)
        }[name];
    }


    // // Open PDF Viewer Modal
    // $(document).on('click', '.view-btn', function(e) {
    //     const filePath = $(this).closest('tr').find('td').eq(8).text().trim(); // Extract file path from table
        
    //     // Update PDF Embed Source
    //     $('#pdfEmbed').attr('src', '{{ asset('storage') }}/' + filePath);
    //     $('#pdfViewerModal').fadeIn();
    // });

    // // Close PDF Viewer Modal
    // $('#closePdfModal').on('click', function() {
    //     $('#pdfViewerModal').fadeOut();
    // });
});


</script>
@endpush
