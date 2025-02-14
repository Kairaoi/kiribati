@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold text-gray-900 tracking-wide">Inward Files</h1>
        {{-- <a href="{{ route('registry.outward-files.create') }}" class="btn btn-primary transition duration-300 transform hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Add New File
        </a> --}}
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table id="inwardFilesTable" class="table w-full">
            <thead class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white">
                <tr>
                    <th class="w-16">ID</th>
                    <th>File Name</th>
                    <th>Received From</th>
                    {{-- <th>Folder</th> --}}
                    <th>Letter Ref No</th>
                    <th>Send Date</th>
                    <th>Security Level</th>
                    {{-- <th>Status</th> --}}
                    <th class="w-28">Actions</th>
                </tr>
            </thead>
            <tbody></tbody> <!-- DataTable will populate this -->
        </table>
    </div>
</div>

<!-- Action Dropdown Template (Hidden) -->
<div id="actionDropdownTemplate" class="hidden">
    <div class="dropdown-menu">
        <a class="dropdown-item view-btn" href="#">
            <i class="fas fa-eye text-blue-500 mr-2"></i> View
        </a>
        <a class="dropdown-item edit-btn" href="#">
            <i class="fas fa-edit text-green-500 mr-2"></i> Edit
        </a>
        <a class="dropdown-item delete-btn" href="#">
            <i class="fas fa-trash text-red-500 mr-2"></i> Delete
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        color: #495057;
        font-weight: 600;
        padding: 1rem;
    }

    .table td {
        vertical-align: middle;
        padding: 0.75rem;
        color: #555;
        font-size: 0.95rem;
    }

    /* Action Button Styles */
    .action-btn {
        cursor: pointer;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: background-color 0.3s, transform 0.2s;
    }

    .action-btn:hover {
        background-color: #e5e7eb;
    }

    /* Dropdown Menu Styles */
    .dropdown-menu {
        position: absolute;
        background-color: white;
        border-radius: 0.375rem;
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

   $('#inwardFilesTable').DataTable({
       processing: true,
       serverSide: true,
       ajax: {
           url: "{{ route('registry.inward-files.datatables') }}",
           type: 'POST',
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
       },
       columns: [
           { data: 'id' },
           { data: 'name' },

           { data: 'owning_ministry_name' },

           { data: 'letter_ref_no' },
           { data: 'send_date' },
           { data: 'security_level' },
           {
               data: null,
               orderable: false,
               render(data, type, row) {
                   return `<button class="action-btn action-dropdown" data-id="${row.id}">
                       Actions <i class="fas fa-chevron-down ml-2"></i></button>`;
               }
           }
       ],
       pageLength: 10,
       responsive: true,
       order: [[0, 'desc']],
       dom: 'Bfrtip', // Enable Buttons
       buttons: [
           {
               extend: 'excelHtml5',
               text: '<i class="fas fa-file-excel"></i> Excel',
               className: 'btn btn-success',
               title: 'Inward Files Registry',
               exportOptions: {
                   columns: ':not(:last-child)' // Exclude the last column (Actions)
               }
           },
           {
               extend: 'pdfHtml5',
               text: '<i class="fas fa-file-pdf"></i> PDF',
               className: 'btn btn-danger',
               title: 'Inward Files Registry',
               exportOptions: {
                   columns: ':not(:last-child)' // Exclude the last column (Actions)
               }
           }
       ]
   });

   // Handle action button click
   $('#inwardFilesTable').on('click', '.action-dropdown', function(e) {
       e.stopPropagation();
       const button = $(this);
       const rowId = button.data('id');

       // Close any open dropdowns
       closeAllDropdowns();

       // Create and position the dropdown
       const dropdown = $(`
           <div class="dropdown-menu" style="display:none;">
               <a class="dropdown-item" href="${route('registry.inward-files.show', rowId)}">
                   <i class="fas fa-eye text-blue-500 mr-2"></i> View
               </a>
               <a class="dropdown-item" href="${route('registry.inward-files.edit', rowId)}">
                   <i class="fas fa-edit text-green-500 mr-2"></i> Edit
               </a>
               <a class="dropdown-item delete-action" href="#" data-id="${rowId}">
                   <i class="fas fa-trash text-red-500 mr-2"></i> Delete
               </a>
           </div>`);

       // Position the dropdown below the button
       const buttonPosition = button.offset();
       const buttonHeight = button.outerHeight();

       dropdown.css({
           top: buttonPosition.top + buttonHeight + 5,
           left: buttonPosition.left
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

       if (confirm('Are you sure you want to delete this file?')) {
           $.ajax({
               url: route('registry.inward-files.destroy', id),
               type: 'DELETE',
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               success(response) {
                   $('#inwardFilesTable').DataTable().ajax.reload();
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
          'registry.inward-files.show': "{{ route('registry.inward-files.show', ':id') }}".replace(':id', id),
          'registry.inward-files.edit': "{{ route('registry.inward-files.edit', ':id') }}".replace(':id', id),
          'registry.inward-files.destroy': "{{ route('registry.inward-files.destroy', ':id') }}".replace(':id', id)
      }[name];
   }

   function getMinistryName(id) {
       // You can replace this with an actual lookup for the ministry name based on the ID
       const ministries = {
           1: 'Ministry of Health',
           2: 'Ministry of Education',
           3: 'Ministry of Finance'
       };
       return ministries[id] || 'Unknown';
   }

   function getFolderName(id) {
       // You can replace this with an actual lookup for the folder name based on the ID
       const folders = {
           1: 'Health Reports',
           2: 'Educational Documents',
           3: 'Financial Reports'
       };
       return folders[id] || 'Unknown';
   }
});
</script>
@endpush
