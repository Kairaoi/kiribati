@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold text-gray-900 tracking-wide">E-Registry Users</h1>
        <a href="{{ route('registry.users.create') }}" class="btn btn-primary transition duration-300 transform hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Add new user
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-500 px-4 py-3 rounded-md shadow-md mb-6 !important">
        <strong class="font-bold">Success! </strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table id="usersTable" class="table w-full">
            <thead class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white">
                <tr>
                    <th class="w-16">ID</th>
                    <th>Name</th>
                    <th>Ministry</th>
                    {{-- <th>Division</th> --}}
                    <th>Email Address</th>
                    {{-- <th>Role</th> --}}
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
<script>
    setTimeout(() => {
        let alertBox = document.querySelector("[role='alert']");
        if (alertBox) {
            alertBox.style.transition = "opacity 0.5s ease-out";
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 500);
        }
    }, 5000); // Message disappears after 5 seconds
</script>
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

   $('#usersTable').DataTable({
       processing: true,
       serverSide: true,
       ajax: {
           url: "{{ route('registry.users.datatables') }}",
           type: 'POST',
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
       },
       columns: [
           { data: 'id' },
           {
                data: 'name', // Define a custom 'name' field
                render: function(data, type, row) {
                    return row.first_name + ' ' + row.last_name; // Combine first name and last name
                }
            },
           { data: 'ministry_name'},
           { data: 'email' },
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
               title: 'Users',
               exportOptions: {
                   columns: ':not(:last-child)' // Exclude the last column (Actions)
               }
           },
           {
               extend: 'pdfHtml5',
               text: '<i class="fas fa-file-pdf"></i> PDF',
               className: 'btn btn-danger',
               title: 'Users Registry',
               exportOptions: {
                   columns: ':not(:last-child)' // Exclude the last column (Actions)
               }
           }
       ]
   });

   // Handle action button click
   $('#usersTable').on('click', '.action-dropdown', function(e) {
       e.stopPropagation();
       const button = $(this);
       const rowId = button.data('id');

       // Close any open dropdowns
       closeAllDropdowns();

       // Create and position the dropdown
       const dropdown = $(`
           <div class="dropdown-menu" style="display:none;">
               <a class="dropdown-item" href="${route('registry.users.show', rowId)}">
                   <i class="fas fa-eye text-blue-500 mr-2"></i> View
               </a>
               <a class="dropdown-item" href="${route('registry.users.edit', rowId)}">
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

       if (confirm('Are you sure you want to delete this user?')) {
           $.ajax({
               url: route('registry.users.destroy', id),
               type: 'DELETE',
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               success(response) {
                   $('#usersTable').DataTable().ajax.reload();
                   alert('User deleted successfully');
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
          'registry.users.show': "{{ route('registry.users.show', ':id') }}".replace(':id', id),
          'registry.users.edit': "{{ route('registry.users.edit', ':id') }}".replace(':id', id),
          'registry.users.destroy': "{{ route('registry.users.destroy', ':id') }}".replace(':id', id)
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

//    function getFolderName(id) {
//        // You can replace this with an actual lookup for the folder name based on the ID
//        const folders = {
//            1: 'Health Reports',
//            2: 'Educational Documents',
//            3: 'Financial Reports'
//        };
//        return folders[id] || 'Unknown';
//    }
});
</script>
@endpush
