@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl mt-3 rounded-md min-h-screen ">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Organisations</h1>
            <p class="text-gray-500 text-sm mt-2 mb-2">View all organsiations registered in the E-Registry System</p>

            <select id="typeFilter" class="filter-input w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm transition duration-200 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100 hover:border-cyan-400">                
                <option value="">All Organisation</option>
                <option value="ministry">Ministry</option>
                <option value="soe">State Owned Enterprise(SOE)</option>
                <option value="diplomatic">Diplomatic Mission</option>
                <option value="international">International Organisation</option>
                <option value="religion">Religious Organisation</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">        
        <table id="organisationsTable" class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wide">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Category/Type</th>
                    {{-- <th class="w-28">Actions</th> --}}
                </tr>
            </thead>
            <tbody class="divide-y stripe"></tbody> <!-- DataTable will populate this -->
        </table>
    </div>
</div>
@endsection

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
    #organisationsTable thead {
        background-color: #f9fafb;
        color: #6b7280;
        text-transform: uppercase;
        font-size: 12px;
    }

    /* Cells */
    #organisationsTable th,
    #organisationsTable td {
        padding: 14px 16px;
    }

    /* Row divider */
    #organisationsTable tbody tr {
        border-bottom: 1px solid #e5e7eb;
    }

    /* Hover effect */
    #organisationsTable tbody tr:hover {
        background-color: #f9fafb;
    }

    /* Fix long text */
    #organisationsTable td {
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

   var table = $('#organisationsTable').DataTable({
       processing: true,
        serverSide: true,  
        searching: true,
        paging: true,
        info: true,
        autoWidth: false,
        responsive: true,
        order: [[0, 'desc']],
        ajax: {
           url: "{{ route('registry.organisations.datatables') }}",
           type: 'POST',
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
           },
           data: function (d) {
                d.selected_type = $('#typeFilter').val();
            },
            dataSrc: 'data'
       },
       columns: [
           { data: 'id'},
           { data: 'name' },
           { data: 'code'},
           { data: 'type_name'},
          
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
                            title: 'File types',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fas fa-download"></i>PDF',
                            className: 'pdf-export-btn',
                            title: 'File types',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        }
                    ]
   });

   // Reload table when initial type changes
    $('#typeFilter').on('change', function() {
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

   // Handle delete action
   $(document).on('click', '.delete-action', function(e) {
       e.preventDefault();
       const id = $(this).data('id');
       
       if (confirm('Are you sure you want to delete this file type?')) {
           $.ajax({
               url : route('registry.file-types.destroy', id),
               type : 'DELETE',
               headers :{
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
               },
               success(response) {
                   $('#fileTypesTable').DataTable().ajax.reload();
                   alert('File type deleted successfully'); 
               },
               error(xhr) {
                   alert('Error deleting file type'); 
               } 
           });
       }
       closeAllDropdowns(); 
   });

   function route(name, id) {
      return {
          'registry.file-types.show': "{{ route('registry.file-types.show', ':id') }}".replace(':id', id),
          'registry.file-types.edit': "{{ route('registry.file-types.edit', ':id') }}".replace(':id', id),
          'registry.file-types.destroy': "{{ route('registry.file-types.destroy', ':id') }}".replace(':id', id)
      }[name];
   }
});
</script>
@endpush
