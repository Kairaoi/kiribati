@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl mt-3 rounded-md min-h-screen ">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">External Partners</h1>
            <p class="text-gray-500 text-sm mt-2 mb-2">Manage external partners within the ministry:</p>
        </div>
        
        <a href="{{ route('registry.external-partners.create') }}"
                class="inline-flex items-center mt-5 gap-2 px-4 py-2 bg-cyan-600 text-white text-sm rounded-md hover:bg-cyan-700 transition">
                <i class="fas fa-plus"></i>
                Add External Partner
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">        
        <table id="externalPartnersTable" class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wide">
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>LINKED TO</th>
                    <th>Category</th>
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
        border-collapse: collapse;
        /* font-size: 0.85rem; */
        border-left: 0.5px solid #d3d3d8;
        border-right: 0.5px solid #d3d3d8;
    }

    /* Header */
    #externalPartnersTable thead {
        background-color: #f9fafb;
        color: #6b7280;
        text-transform: uppercase;
        font-size: 12px;
    }

    /* Cells */
    #externalPartnersTable th,
    #externalPartnersTable td {
        padding: 12px 14px;
    }

    /* Row divider */
    #externalPartnersTable tbody tr {
        border-bottom: 1px solid #e5e7eb;
    }

    /* Hover effect */
    #externalPartnersTable tbody tr:hover {
        background-color: #f9fafb;
    }

    /* Fix long text */
    #externalPartnersTable td {
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

   var table = $('#externalPartnersTable').DataTable({
       processing: true,
        serverSide: true,  
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
           { data: 'id'},
           { data: 'name' },
           { data: 'identity_organisation_name' },
            { data: 'organisation_type_name' },
           {
                data: null,
                orderable: false,
                name: 'actions',
                searchable: false,
                render: function (data, type, row) {
                    let buttons = `
                        <a href="/registry/external-partners/${row.id}/edit" 
                        class="btn btn-sm btn-outline-info"
                        style="display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fas fa-edit"></i>    Edit
                        </a>

                    `;
                    
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

  
   // Handle action button click
   $('#externalPartnersTable').on('click', '.action-dropdown', function(e) {
       e.stopPropagation();
       const button = $(this);
       const rowId = button.data('id');
       
       // Close any open dropdowns
       closeAllDropdowns();

       // Create and position the dropdown
       const dropdown = $(` 
           <div class="dropdown-menu" style="display:none;">
               <a class="dropdown-item" href="${route('registry.external-partners.show', rowId)}">
                   <i class="fas fa-eye text-cyan-500 mr-2"></i> View
               </a>
               <a class="dropdown-item" href="${route('registry.external-partners.edit', rowId)}">
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
       
       if (confirm('Are you sure you want to delete this external partner?')) {
           $.ajax({
               url : route('registry.external-partners.destroy', id),
               type : 'DELETE',
               headers :{
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
               },
               success(response) {
                   $('#externalPartnersTable').DataTable().ajax.reload();
                   alert('External partner deleted successfully'); 
               },
               error(xhr) {
                   alert('Error deleting external partner'); 
               } 
           });
       }
       closeAllDropdowns(); 
   });

   function route(name, id) {
      return {
          'registry.external-partners.show': "{{ route('registry.external-partners.show', ':id') }}".replace(':id', id),
          'registry.external-partners.edit': "{{ route('registry.external-partners.edit', ':id') }}".replace(':id', id),
          'registry.external-partners.destroy': "{{ route('registry.external-partners.destroy', ':id') }}".replace(':id', id)
      }[name];
   }
});
</script>
@endpush
