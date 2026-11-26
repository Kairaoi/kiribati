@extends('layouts.app')

@section('content')
@if (session()->has('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                titleText: @js(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false
            });
        });
    </script>
@endif
@if (session()->has('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Action failed',
                text: @js(session('error')),
                confirmButtonColor: '#0891b2',
                width: 320
            });
        });
    </script>
@endif

<div class="container mx-auto px-4 py-8 max-w-screen-2xl mx-auto px-3 sm:px-6 lg:px-8 mt-1 rounded-md min-h-screen ">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">External Partners</h1>
            <p class="text-gray-500 text-sm mt-2 mb-2">Manage external partners within the ministry:</p>
        </div>
        
        <a href="{{ route('registry.external-partners.create') }}"
                class="inline-flex items-center justify-center gap-2
                            min-h-[60px] border px-5 py-3 rounded-lg
                            bg-white text-cyan-700 text-sm font-semibold
                            shadow-sm hover:bg-cyan-700 transition duration-200">

                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Add External Partner
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">        
        <table id="externalPartnersTable" class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wide">
                <tr>
                    <th>NAME</th>
                    <th>Type</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th class="w-29">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y"></tbody> <!-- DataTable will populate this -->
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
            #externalPartnersTable thead {
                background-color: #edf0f1;
                color: #5f6163;
                font-size: 12px;
            }

            #externalPartnersTable thead th {
                padding: 8px 10px;
                font-weight: 600;
                
            }

            /* Cells */
            #externalPartnersTable td {
                padding: 12px 14px;
                white-space: normal !important;
                border-bottom: 1px solid #cfd4d7;
            }

            /* Row divider */
            #externalPartnersTable tbody tr {
                border-bottom: 1.5px solid #6b6969;
                border-top: 1.5px solid #6b6969;
                background-color: #ffffff;
            }

            /* Hover effect */
            #externalPartnersTable tbody tr:hover {
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
   .swal2-popup:not(.swal2-toast) {
        width: 320px !important;
        padding: 1.25rem !important;
        border-radius: 12px !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-title {
        font-size: 18px !important;
        font-weight: 600;
        padding: 0.5rem 0 0 !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-html-container {
        font-size: 13px !important;
        line-height: 1.5;
        margin: 0.75rem 0 !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-icon {
        transform: scale(0.65);
        margin: 0 auto !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-actions {
        margin: 0.5rem auto 0 !important;
    }

    .swal2-styled {
        font-size: 13px !important;
        padding: 8px 16px !important;
        border-radius: 6px !important;
    }

    .swal2-toast {
        max-width: 280px !important;
        padding: 10px !important;
    }

    .swal2-toast .swal2-title {
        font-size: 13px !important;
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
   
   var table = $('#externalPartnersTable').DataTable({
        processing: true,
        serverSide: false,  
        searching: true,
        paging: true,
        info: true,
        autoWidth: false,
        responsive: true,
        order: [[0, 'desc']],
        ajax: {
           url: "{{ route('registry.external-partners.datatables') }}",
           type: 'POST',
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
           },
           
       },
       columns: [
            { data: 'name', name:'external_partners.id'},
            { data: 'organisation_type_name', name:'organisation_types.name'},
            { data: 'email', name:'external_partners.email'},
            { data: 'phone', name:'external_partners.phone'},
            {
                data: 'is_active',
                render: function (data, type, row) {
                    return data
                        ? `
                            <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700 ring-1 ring-green-100">
                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                Active
                            </span>
                        `
                        : `
                            <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700 ring-1 ring-red-100">
                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                Inactive
                            </span>
                        `;
                }
            },
            {
                data: null,
                orderable: false,
                name: 'actions',
                searchable: false,
                render: function (data, type, row) {
                    let buttons = `
                        <a href="/registry/external-partners/${row.id}"
                            class="inline-flex items-center justify-center rounded-md p-2 transition focus:outline-none"
                            title="View"
                            aria-label="View file type">
                            <i class="fa fa-eye text-sm"></i>
                        </a>

                        <a href="/registry/external-partners/${row.id}/edit"
                            class="inline-flex items-center justify-center rounded-md p-2 transition focus:outline-none"
                            title="Edit"
                            aria-label="Edit file type">
                            <i class="fa fa-edit text-sm"></i>
                        </a>
                    `;

                    const isActive = Number(row.is_active);

                    if (isActive === 1) {
                        buttons += `
                            <button
                                type="button"
                                data-id="${row.id}"
                                class="deactivate-action inline-flex items-center justify-center rounded-md p-2 transition"
                                title="Deactivate"
                                aria-label="Deactivate">
                                <i class="fa fa-ban text-sm"></i>
                            </button>
                        `;
                    } else if (isActive === 0) {
                        buttons += `
                            <button
                                type="button"
                                data-id="${row.id}"
                                class="activate-action inline-flex items-center justify-center rounded-md p-2 text-green-600 transition hover:bg-green-50 hover:text-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                title="Activate"
                                aria-label="Activate">
                                <i class="fa fa-check-circle text-sm"></i>
                            </button>
                        `;
                    }

                    if (Number(row.files_count) === 0) {
                        buttons += `
                            <button
                                type="button"
                                data-id="${row.id}"
                                class="delete-action inline-flex items-center justify-center rounded-md p-2 transition hover:bg-red-50"
                                title="Delete"
                                aria-label="Delete">
                                <i class="fa fa-trash text-sm"></i>
                            </button>
                        `;
                    }

                    return buttons;
                }
            },
       ],
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
                text: '<i class="fas fa-download"></i>EXCEL',
                className: 'excel-export-btn',
                title: 'External Partners',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-download"></i>PDF',
                className: 'pdf-export-btn',
                title: 'External Partners',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
   });

  
   // Handle deactivate action
    $(document).on('click', '.deactivate-action', function(e) {
        e.preventDefault();

        const id = $(this).data('id');

        if (confirm('Are you sure you want to deactivate this external partner?')) {
            $.ajax({
                url: route('registry.external-partners.deactivate', id),
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success(response) {
                    $('#externalPartnersTable').DataTable().ajax.reload();
                    alert('External Partner deactivated successfully.');
                },
                error(xhr) {
                    alert('Unable to deactivate external partner.');
                }
            });
        }

        closeAllDropdowns();
    });


    // Handle activate action
    $(document).on('click', '.activate-action', function(e) {
        e.preventDefault();

        const id = $(this).data('id');

        if (confirm('Are you sure you want to activate this external partner?')) {
            $.ajax({
                url: route('registry.external-partners.activate', id),
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success(response) {
                    $('#externalPartnersTable').DataTable().ajax.reload();
                    alert('External partner activated successfully.');
                },
                error(xhr) {
                    alert('Unable to activate external partner.');
                }
            });
        }
        closeAllDropdowns();
    });

        // Handle delete action
    $(document).on('click', '.delete-action', async function (e) {
        e.preventDefault();

        const id = $(this).data('id');

        console.log('External Partner ID:', id);
        console.log('Delete URL:', route('registry.external-partners.destroy', id));

        const result = await Swal.fire({
            title: 'Delete external partner?',
            text: 'Are you sure you want to delete this external partner? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true
        });

        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: route('registry.external-partners.destroy', id),
            type: 'DELETE',

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },

            success(response) {
                $('#externalPartnersTable').DataTable().ajax.reload(null, false);

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: response.message ?? 'File type deleted successfully.',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            },

            error(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Unable to delete external partner',
                    text: xhr.responseJSON?.message ??
                        'An unexpected error occurred. Please try again.',
                    confirmButtonColor: '#0891b2',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

   function route(name, id) {
      return {
          'registry.external-partners.show': "{{ route('registry.external-partners.show', ':id') }}".replace(':id', id),
          'registry.external-partners.edit': "{{ route('registry.external-partners.edit', ':id') }}".replace(':id', id),
          'registry.external-partners.deactivate': "{{ route('registry.external-partners.deactivate', ':id') }}".replace(':id', id),
          'registry.external-partners.activate': "{{ route('registry.external-partners.activate', ':id') }}".replace(':id', id),
          'registry.external-partners.destroy': "{{ route('registry.external-partners.destroy', ':id') }}".replace(':id', id)
      }[name];
   }
});
</script>
@endpush
