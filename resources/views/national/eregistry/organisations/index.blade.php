@extends('layouts.app')

@section('content')
   @if (session('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition.opacity.scale.80
            x-init="setTimeout(() => show = false, 4000)" 
            class="fixed text-whiteright-0 bg-cyan-400 px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3 z-50">
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
<div class="container mx-auto px-4 py-8 max-w-screen-2xl mx-auto px-3 sm:px-6 lg:px-8 mt-3 rounded-md min-h-screen ">
    <h1 class="text-3xl font-bold text-gray-800">Organisations</h1>
    <p class="text-gray-500 text-sm mt-2 mb-2">View all organisations registered in the DMS</p>

    <div class="mb-4 flex justify-between">
        <div>
            <select id="typeFilter" class="filter-input w-full border rounded-lg border-gray-600 bg-white px-4 py-2.5 text-sm text-cyan-900 shadow-sm transition duration-200 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100 hover:border-cyan-400">                
                <option value="">All Organisation</option>
                <option value="ministry">Ministry</option>
                <option value="soe">State Owned Enterprise(SOE)</option>
                <option value="diplomatic">Diplomatic Mission</option>
                <option value="international">International Organisation</option>
                <option value="religion">Religious Organisation</option>
            </select>
        </div>
        @if($isSystemAdmin)
            <div>
                <a href="{{ route('registry.organisations.create') }}"
                    class="inline-flex items-center justify-center gap-2
                            min-h-[60px] border px-5 py-3 rounded-lg
                            bg-white text-cyan-700 text-sm font-semibold
                            shadow-sm hover:bg-cyan-700 transition duration-200">

                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    Create Organisation
                </a>
            </div>
        @endif
    </div>

    <div class="bg-white shadow-sm border rounded-2xl border-gray-100 p-4">        
        <table id="organisationsTable" class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wide">
                <tr>
                    <th>#</th>
                    <th>UUID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Category/Type</th>
                    <th>Status</th>
                    <th class="w-28 no-export">Actions</th>
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
                border-collapse: separate;
                border-spacing: 0;
                border: 0.5px solid #d3d3d8;
                border-radius: 8px;
                overflow: hidden;
            }

            /* Header */
            #organisationsTable thead {
                background-color: #edf0f1;
                color: #5f6163;
                font-size: 12px;
            }

            #organisationsTable thead th {
                padding: 8px 10px;
                font-weight: 600;
                
            }

            /* Cells */
            #organisationsTable td {
                padding: 12px 14px;
                white-space: normal !important;
                border-bottom: 1px solid #cfd4d7;
            }

            /* Row divider */
            #organisationsTable tbody tr {
                border-bottom: 1.5px solid #6b6969;
                border-top: 1.5px solid #6b6969;
                background-color: #ffffff;
            }

            /* Hover effect */
            #organisationsTable tbody tr:hover {
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
    const isSystemAdmin = @json($isSystemAdmin);
    const isMinistryAdmin = @json($isMinistryAdmin);


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
           { data: 'uuid', visible: false}, // Hide UUID column
           { data: 'name' },
           { data: 'code'},
           { data: 'type_name', name: 'organisation_types.name' },
           {
                data: 'is_active',
                render: function (data) {
                    return Number(data) === 1
                        ? '<span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">Active</span>'
                        : '<span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">Inactive</span>';
                }
            },
           { 
                data: null,
                name: 'actions',
                orderable: false,
                searchable: false,
                render(data, type, row) {
                    let buttons = `
                        <a href="/registry/organisations/${row.uuid}" 
                            class="inline-flex items-center justify-center hover:text-gray-600 transition"
                                title="View">
                                <i class="fa fa-eye text-sm"></i>
                        </a>
                        `;
                    if (isSystemAdmin || isMinistryAdmin) {
                        buttons += `<a href="/registry/organisations/${row.uuid}/edit" 
                            class="inline-flex items-center justify-center hover:text-gray-600 transition ms-3"
                            title="Edit">
                            <i class="fa fa-pen text-sm"></i>
                        </a>
                         `;
                        const isActive = Number(row.is_active);
                        if (isActive === 1) {
                            buttons += `
                                <button class="inline-flex items-center justify-center hover:text-red-600 transition ms-3 deactivate-action" 
                                    data-id="${row.id}" 
                                    title="Deactivate">
                                    <i class="fa fa-ban text-sm"></i>
                                </button>
                            `;
                        } else {
                            buttons += `
                                <button class="inline-flex items-center justify-center hover:text-red-600 transition ms-3 activate-action" 
                                    data-id="${row.id}" 
                                    title="Activate">
                                    <i class="fa fa-circle-check text-sm"></i>
                                </button>
                            `;
                        }
                    }
                    return buttons;
                }
            }
           
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
                        title: 'DMS Organisations',
                        exportOptions: {
                            columns: ':visible:not(.no-export)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-download"></i>PDF',
                        className: 'pdf-export-btn',
                        title: 'DMS Organisations',
                        exportOptions: {
                        columns: ':visible:not(.no-export)'
                    }
                    }
                ]
   });

   // Reload table when initial type changes
    $('#typeFilter').on('change', function() {
        table.ajax.reload();
    });


   // Handle delete action
   $(document).on('click', '.delete-action', function(e) {
       e.preventDefault();
       const id = $(this).data('id');
       
       if (confirm('Are you sure you want to delete this organisation?')) {
           $.ajax({
               url : route('registry.organisations.destroy', id),
               type : 'DELETE',
               headers :{
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
               },
               success(response) {
                   $('#organisationsTable').DataTable().ajax.reload();
                   alert('Organisation deleted successfully'); 
               },
               error(xhr) {
                   alert('Error deleting organisation'); 
               } 
           });
       }
       closeAllDropdowns(); 
   });

   function route(name, id) {
      return {
          'registry.organisations.show': "{{ route('registry.organisations.show', ':id') }}".replace(':id', id),
          'registry.organisations.edit': "{{ route('registry.organisations.edit', ':id') }}".replace(':id', id),
          'registry.organisations.destroy': "{{ route('registry.organisations.destroy', ':id') }}".replace(':id', id)
      }[name];
   }
});
</script>
@endpush
