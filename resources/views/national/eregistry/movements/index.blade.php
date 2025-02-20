@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold text-gray-900 tracking-wide">File Movements Registry</h1>
        <a href="{{ route('registry.movements.create') }}" class="btn btn-primary transition duration-300 transform hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Add New Movement
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table id="movementsTable" class="table w-full">
            <thead class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white">
                <tr>
                    <th class="w-16">ID</th>
                    <th>File Name</th>
                    <th>From Ministry</th>
                    <th>To Ministry</th>
                    <th>Movement Dates</th>
                    <th>Status</th>
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

   $('#movementsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('registry.movements.datatables') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            dataSrc: function (json) {
                return json.data || [];
            }
        },
        columns: [
            { data: 'id' },
            { data: 'file_name' },
            { data: 'from_ministry_name' },  // Directly using ministry name
            { data: 'to_ministry_name' },
            { 
                data: 'movement_start_date',
                render: function(data, type, row) {
                    return `${formatDate(data)} - ${formatDate(row.movement_end_date)}`;
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    let colorClass = {
                        'completed': 'text-green-500',
                        'in_progress': 'text-yellow-500',
                        'pending': 'text-red-500'
                    }[data] || 'text-gray-500';
                    return `<span class="${colorClass}">${data}</span>`;
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `<button class="action-btn action-dropdown" data-id="${row.id}">
                        Actions <i class="fas fa-chevron-down ml-2"></i></button>`;
                }
            }
        ],
        pageLength: 10,
        responsive: true,
        order: [[0, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success',
                title: 'Movement Registry',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger',
                title: 'Movement Registry',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ]
    });

    // Handle action button click
    $('#movementsTable').on('click', '.action-dropdown', function(e) {
        e.stopPropagation();
        const button = $(this);
        const rowId = button.data('id');

        closeAllDropdowns();

        const dropdown = $(`
            <div class="dropdown-menu" style="display:none;">
                <a class="dropdown-item" href="${route('registry.movements.show', rowId)}">
                    <i class="fas fa-eye text-blue-500 mr-2"></i> View
                </a>
                <a class="dropdown-item" href="${route('registry.movements.edit', rowId)}">
                    <i class="fas fa-edit text-green-500 mr-2"></i> Edit
                </a>
                <a class="dropdown-item delete-action" href="#" data-id="${rowId}">
                    <i class="fas fa-trash text-red-500 mr-2"></i> Delete
                </a>
            </div>
        `);

        const buttonPosition = button.offset();
        const buttonHeight = button.outerHeight();

        dropdown.css({
            top: buttonPosition.top + buttonHeight + 5,
            left: buttonPosition.left
        });

        $('body').append(dropdown);
        dropdown.show();

        button.addClass('active');
        activeDropdown = button;
    });

    // Handle delete action
    $(document).on('click', '.delete-action', function(e) {
        e.preventDefault();
        const id = $(this).data('id');

        if (confirm('Are you sure you want to delete this movement?')) {
            $.ajax({
                url: route('registry.movements.destroy', id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                success(response) {
                    $('#movementsTable').DataTable().ajax.reload();
                    alert('Movement deleted successfully'); 
                },
                error(xhr) {
                    alert('Error deleting movement');
                }
            });
        }
        closeAllDropdowns();
    });

    function formatDate(dateString) {
        if (!dateString) return "N/A";
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function route(name, id) {
        return {
            'registry.movements.show': "{{ route('registry.movements.show', ':id') }}".replace(':id', id),
            'registry.movements.edit': "{{ route('registry.movements.edit', ':id') }}".replace(':id', id),
            'registry.movements.destroy': "{{ route('registry.movements.destroy', ':id') }}".replace(':id', id)
        }[name];
    }
});
</script>
@endpush