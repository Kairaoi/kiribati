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


<div class="container mx-auto px-4 py-8 max-w-screen-2xl px-3 sm:px-6 lg:px-8 mt-1 rounded-md min-h-screen">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">File Types</h1>
            <p class="text-gray-500 text-sm mt-2 mb-2">Manage file types used in file reference numbers:</p>
            
            {{-- <label class="block text-m text-slate-900"></label> --}}
            <select id="fileTypeFilter" class="filter-input w-full rounded-lg text-sm text-gray-600 border px-3 py-2 focus:ring focus:ring-cyan-200">
                <option value="">All file types</option>
                <option value="ministry">organisation-specific</option>
                <option value="global">government-wide</option>
            </select>
        </div>
        <a href="{{ route('registry.file-types.create') }}"
                class="inline-flex items-center justify-center gap-2
                        min-h-[60px] border px-5 py-3 rounded-lg
                        bg-white text-cyan-700 text-sm font-semibold
                        shadow-sm hover:bg-cyan-700 transition duration-200">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Add file type
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">        
        <table id="fileTypesTable" class="w-full text-sm table-auto">
            <thead class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wide">
                <tr>
                    <th>ID</th>
                    <th>FILE TYPE NAME</th>
                    <th>CODE</th>
                    <th>SCOPE</th>
                    <th>UPDATED AT</th>
                    <th>STATUS</th>
                    <th class="w-28">Actions</th>
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
    #fileTypesTable thead {
        background-color: #edf0f1;
        color: #5f6163;
        font-size: 12px;
    }

    #fileTypesTable thead th {
        padding: 8px 10px;
        font-weight: 600;   
    }

    /* Cells */
    #fileTypesTable td {
        padding: 12px 14px;
        white-space: normal !important;
        border-bottom: 1px solid #cfd4d7;
    }

    /* Row divider */
    #fileTypesTable tbody tr {
        border-bottom: 1.5px solid #6b6969;
        border-top: 1.5px solid #6b6969;
        background-color: #ffffff;
    }

    /* Hover effect */
    #fileTypesTable tbody tr:hover {
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

    /* Dropdown Menu Styles */
    .dropdown-menu {
        position: absolute;
        background-color: white;
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

   var table = $('#fileTypesTable').DataTable({
       processing: true,
        serverSide: true,  
        searching: true,
        paging: true,
        info: true,
        autoWidth: false,
        responsive: true,
        order: [[0, 'desc']],
        ajax: {
           url: "{{ route('registry.file-types.datatables') }}",
           type: 'POST',
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
           },
           data: function (d) {
                d.selected_type = $('#fileTypeFilter').val();
            },
            dataSrc: 'data'
       },
       columns: [
           { data: 'id', visible: false },
           { data: 'file_type_name' },
           { data: 'code'},
           {
                data: 'is_global',
                render: function (data, type, row) {
                    return data
                        ? `
                            <span class="inline-flex items-center rounded-full bg-cyan-50 px-3 py-1 text-xs font-medium text-cyan-700 ring-1 ring-cyan-100">
                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                                government-wide
                            </span>
                        `
                        : `
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-100">
                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                organisation
                            </span>
                        `;
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
                        <a href="/registry/file-types/${row.id}"
                            class="inline-flex items-center justify-center rounded-md p-2 transition"
                            title="View"
                            aria-label="View file type">
                            <i class="fa fa-eye text-sm"></i>
                        </a>
                    `;

                    const isActive = Number(row.is_active);
                    const canManage = !Number(row.is_global) || Boolean(row.can_edit);

                    if (canManage && isActive === 1) {
                        buttons += `
                            <button
                                type="button"
                                data-id="${row.id}"
                                class="deactivate-action inline-flex items-center justify-center rounded-md p-2 transition"
                                title="Deactivate"
                                aria-label="Deactivate file type">
                                <i class="fa fa-ban text-sm"></i>
                            </button>
                        `;
                    } else if (canManage && isActive === 0) {
                        buttons += `
                            <button
                                type="button"
                                data-id="${row.id}"
                                class="activate-action inline-flex items-center justify-center rounded-md p-2 transition"
                                title="Activate"
                                aria-label="Activate file type">
                                <i class="fa fa-check-circle text-sm"></i>
                            </button>
                        `;
                    }

                    if (Number(row.files_count) === 0 && canManage) {
                        buttons += `
                            <button
                                type="button"
                                data-id="${row.id}"
                                class="delete-action inline-flex items-center justify-center rounded-md p-2 transition"
                                title="Delete"
                                aria-label="Delete file type">
                                <i class="fa fa-trash text-sm"></i>
                            </button>
                        `;
                    }

                    return `
                        <div class="flex items-center gap-1">
                            ${buttons}
                        </div>
                    `;
                }
            }
       ],
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
                        text: '<i class="fas fa-download"></i> Excel',
                        className: 'excel-export-btn',
                        title: 'File types',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-download"></i> PDF',
                        className: 'pdf-export-btn',
                        title: 'File types',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ]
   });

   table.columns.adjust();

   // Reload table when initial type changes
    $('#fileTypeFilter').on('change', function() {
        table.ajax.reload();
    });

   // Handle action button click
   $('#fileTypesTable').on('click', '.action-dropdown', function(e) {
       e.stopPropagation();
       const button = $(this);
       const rowId = button.data('id');
       
       // Close any open dropdowns
       closeAllDropdowns();

       // Create and position the dropdown
       const dropdown = $(` 
           <div class="dropdown-menu" style="display:none;">
               <a class="dropdown-item" href="${route('registry.file-types.show', rowId)}">
                   <i class="fas fa-eye text-cyan-500 mr-2"></i> View
               </a>
               <a class="dropdown-item" href="${route('registry.file-types.edit', rowId)}">
                   <i class="fas fa-edit text-cyan-500 mr-2"></i> Edit
               </a>
               <a class="dropdown-item delete-action" href="#" data-id="${rowId}">
                   <i class="fas fa-trash text-red-500 mr-2"></i> Delete
               </a>
           </div>`);

       // Position the dropdown below the button
       const buttonPosition = button.offset();
       const buttonHeight = button.outerHeight();
       
       dropdown.css({
           top : buttonPosition.top + buttonHeight +5,
           left : buttonPosition.left 
       });

       // Add to body and show
       $('body').append(dropdown);
       dropdown.show();
       
       // Mark this button as active
       button.addClass('active');
       activeDropdown = button; 
   });

    // Handle deactivate action
    $(document).on('click', '.deactivate-action', function(e) {
        e.preventDefault();

        const id = $(this).data('id');

        if (confirm('Are you sure you want to deactivate this file type?')) {
            $.ajax({
                url: route('registry.file-types.deactivate', id),
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success(response) {
                    $('#fileTypesTable').DataTable().ajax.reload();
                    alert('File type deactivated successfully.');
                },
                error(xhr) {
                    alert('Unable to deactivate file type.');
                }
            });
        }

    });


    // Handle activate action
    $(document).on('click', '.activate-action', function(e) {
        e.preventDefault();

        const id = $(this).data('id');

        if (confirm('Are you sure you want to activate this file type?')) {
            $.ajax({
                url: route('registry.file-types.activate', id),
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success(response) {
                    $('#fileTypesTable').DataTable().ajax.reload();
                    alert('File type activated successfully.');
                },
                error(xhr) {
                    alert('Unable to activate file type.');
                }
            });
        }

    });

    // Handle delete action
    $(document).on('click', '.delete-action', async function (e) {
        e.preventDefault();

        const id = $(this).data('id');

        const result = await Swal.fire({
            title: 'Delete file type?',
            text: 'Are you sure you want to delete this file type? This action cannot be undone.',
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
            url: route('registry.file-types.destroy', id),
            type: 'DELETE',

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },

            success(response) {
                $('#fileTypesTable').DataTable().ajax.reload(null, false);

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
                    title: 'Unable to delete file type',
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
          'registry.file-types.show': "{{ route('registry.file-types.show', ':id') }}".replace(':id', id),
          'registry.file-types.edit': "{{ route('registry.file-types.edit', ':id') }}".replace(':id', id),
          'registry.file-types.deactivate': "{{ route('registry.file-types.deactivate', ':id') }}".replace(':id', id),
          'registry.file-types.activate': "{{ route('registry.file-types.activate', ':id') }}".replace(':id', id),
          'registry.file-types.destroy': "{{ route('registry.file-types.destroy', ':id') }}".replace(':id', id)

      }[name];
   }
});
</script>
@endpush
