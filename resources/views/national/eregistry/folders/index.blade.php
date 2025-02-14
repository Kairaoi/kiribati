@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex justify-between items-center mb-4">
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach (Breadcrumbs::generate() as $breadcrumb)
            <li class="breadcrumb-item">
                @if ($breadcrumb->url)
                    <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                @else
                    <span>{{ $breadcrumb->title }}</span>
                @endif
            </li>
        @endforeach
    </ol>
    </nav>

        <h1 class="text-3xl font-bold text-gray-900 tracking-wide">Folder Registry</h1>
        <a href="{{ route('registry.folders.create') }}" class="btn btn-primary transition duration-300 transform hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Add New Folder
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table id="foldersTable" class="table w-full">
            <thead class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white">
                <tr>
                    <th class="w-16">ID</th>
                    <th>Folder Index</th>
                    <th>Folder Name</th>
                    <th>Description</th>
                    <th>Active</th>
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

   $('#foldersTable').DataTable({
       processing: true,
       serverSide: true,
       ajax: {
           url: "{{ route('registry.folders.datatables') }}",
           type: 'POST',
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
       },
       columns: [
           { data: 'id' },
           {
        data: 'fileindex',
                render: function(data, type, row) {
                    return `<i class="fas fa-folder mr-2"></i> ${data}`;  // Folder icon + fileindex
                }
          }, // Update column to match data
           { data: 'folder_name' },
           { data: 'folder_description' },
           {
               data: 'is_active',
               render: function(data) {
                   return data ? 'Active' : 'Inactive';
               }
           },
           {
               data: null,
               orderable:false,
               render(data, type, row) {
                   return `<button class="action-btn action-dropdown" data-id="${row.id}">
                       Actions <i class="fas fa-chevron-down ml-2"></i></button>`;
               }
           }
       ],
       pageLength :10,
       responsive :true,
       order:[[0,'desc']],
       dom: 'Bfrtip', // Enable Buttons
       buttons: [
           {
               extend: 'excelHtml5',
               text: '<i class="fas fa-file-excel"></i> Excel',
               className: 'btn btn-success',
               title: 'Folder Registry',
               exportOptions: {
                   columns: ':not(:last-child)' // Exclude the last column (Actions)
               }
           },
           {
               extend: 'pdfHtml5',
               text: '<i class="fas fa-file-pdf"></i> PDF',
               className: 'btn btn-danger',
               title: 'Folder Registry',
               exportOptions: {
                   columns: ':not(:last-child)' // Exclude the last column (Actions)
               }
           }
       ]
   });

   // Handle action button click
   $('#foldersTable').on('click', '.action-dropdown', function(e) {
       e.stopPropagation();
       const button = $(this);
       const rowId = button.data('id');

       // Close any open dropdowns
       closeAllDropdowns();

       // Create and position the dropdown
       const dropdown = $(`
           <div class="dropdown-menu" style="display:none;">
               <a class="dropdown-item" href="${route('registry.folders.show', rowId)}">
                   <i class="fas fa-eye text-blue-500 mr-2"></i> View
               </a>
               <a class="dropdown-item" href="${route('registry.folders.edit', rowId)}">
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

       if (confirm('Are you sure you want to delete this folder?')) {
           $.ajax({
               url : route('registry.folders.destroy', id),
               type : 'DELETE',
               headers :{
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               success(response) {
                   $('#foldersTable').DataTable().ajax.reload();
                   alert('Folder deleted successfully');
               },
               error(xhr) {
                   alert('Error deleting folder');
               }
           });
       }
       closeAllDropdowns();
   });

   function route(name, id) {
      return {
          'registry.folders.show': "{{ route('registry.folders.show', ':id') }}".replace(':id', id),
          'registry.folders.edit': "{{ route('registry.folders.edit', ':id') }}".replace(':id', id),
          'registry.folders.destroy': "{{ route('registry.folders.destroy', ':id') }}".replace(':id', id)
      }[name];
   }
});
</script>
@endpush
