@extends('layouts.app')

@section('content')

@if (session('success'))
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 5000)"
            x-show="show"
            x-transition
            class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-400 text-green-900 px-6 py-3 rounded shadow-lg max-w-md w-full text-center z-50">
            {{ session('success') }}
        </div>
@endif
<div class="container mx-auto font-montserrat px-4 max-w-7xl mt-2"> {{ Breadcrumbs::render('dispatches.index') }} </div>
<div class="container mx-auto font-montserrat px-4 py-8 max-w-7xl">
    
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-wide">Dispatches</h1>
            <p class="text-base mt-1">
                View and manage your dispatched files from your organisation.
            </p>
        </div>

        <a href="{{ route('registry.files.create') }}"
           class="btn btn-link text-primary fw-semibold d-inline-flex align-items-center gap-2 mt-3">
            <i class="fas fa-plus"></i>
            Create New Outward File
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table id="UserDispatchesTable" class="table w-full mt-6">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>From Division</th>
                    <th>File Type</th>
                    <th>Created By</th>
                    <th>Dispatch Date</th>
                    <th>Dispatched By</th>
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

    h1 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        color: #000000; 
    }

    p {
        font-family: 'Montserrat', sans-serif;
        color: #525355; 
    }
    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
       
    }

    .table thead th {
        background-color:  #184f8a; 
        border-bottom: 2px solid #dee2e6;
        color: #f8f9f9;
        font-family: 'Montserrat', sans-serif;
        padding: 1rem;
        margin-top: 50px;
    }

    .table td {
        vertical-align: middle;
        padding: 0.75rem;
        color: #23416d;
        font-size: 0.95rem;
    }

    /* Action Button Styles */
    .action-btn {
        cursor: pointer;
        padding: 0.5rem 1rem;
        /* border-radius: 0.375rem; */
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

    /* Modal and PDF Viewer Styles */
    #pdfViewerModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        width: 100%;
        height: 100%;
        /* background-color: rgba(0, 0, 0, 0.5); */
    }

    .pdf-viewer embed {
        width: 100%;
        height: 600px;
    }

    /* Force green Excel button */
    .excel-export-btn {
        background-color: #16a34a !important; /* Bootstrap green */
        color: white !important;
        border: none !important;
    
    }

    .excel-export-btn:hover {
        background-color: #b2e8cf;
    }

    .pdf-export-btn {
    background-color: #dc2626 !important; /* Bootstrap red */
    color: rgb(249, 246, 246) !important;
    padding: 0.5rem 1rem;
    /* border-radius: 0.375rem; */
    font-size: 0.875rem;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;

    }

    .pdf-export-btn i {
        color: white !important; /* Icon color */
    }

    div.dt-buttons {
        margin-bottom: 0.275rem; /* increase space under buttons */
        margin-top: 1.5rem; /* increase space above buttons */
    }

    .dataTables_filter input {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
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

    // Initialize DataTable
    $('#UserDispatchesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('registry.dispatches.user.datatables') }}",  // Updated route
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            }
        },
        columns: [
            { data: 'file_name' },
            // { data: 'owning_organisation_code' },
            { data: 'owning_division_name' }, 
            { data: 'file_type_name' },
            { data: 'file_created_by' },
            { 
                data: 'dispatch_date',
                render: function (data) {
                    if (!data) return '';
                    const date = new Date(data);
                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }  
            },  
            { data: 'dispatched_by_name'},
            { 
                data: 'status',
                render: function(data, type, row) {
                    let badgeClass = '';

                    if (data === 'Pending Dispatched') {
                        badgeClass = 'bg-red-500 text-white text-xs px-2 py-1 rounded-full font-medium';
                    } else if (data === 'Dispatched') {
                        badgeClass = 'bg-green-500 text-white text-xs px-2 py-1 rounded-full font-medium';
                    } else {
                        badgeClass = 'bg-gray-300 text-white text-xs px-2 py-1 rounded-full font-medium';
                    }

                    return `<span class="${badgeClass}">${data}</span>`;
                }
            },
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
                text: '<i class="fas fa-file-excel me-1"></i> Excel',
                className: 'excel-export-btn',
                title: 'Outward Files Registry',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                className: 'pdf-export-btn',
                title: 'Outward Files Registry',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }

        ]
    });

    // Handle action button click
    $('#UserDispatchesTable').on('click', '.action-dropdown', function(e) {
        e.stopPropagation();
        const button = $(this);
        const rowId = button.data('id');

        console.log('Row ID:', rowId);

        
        // Close any open dropdowns
        closeAllDropdowns();


        // Create and position the dropdown
        const dropdown = $(` 
            <div class="dropdown-menu" style="display:none;">
                <a class="dropdown-item" href="${route('registry.dispatches.show', rowId)}">
                    <i class="fas fa-eye text-blue-500 mr-2"></i> View
                </a>
                <a class="dropdown-item" href="${route('registry.files.edit', rowId)}">
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
                url: route('registry.files.destroy', id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                success(response) {
                    $('#outwardFilesTable').DataTable().ajax.reload();
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
            'registry.dispatches.show': "{{ route('registry.dispatches.show', ':id') }}".replace(':id', id),
            'registry.files.edit': "{{ route('registry.files.edit', ':id') }}".replace(':id', id),
            'registry.files.destroy': "{{ route('registry.files.destroy', ':id') }}".replace(':id', id)
        }[name];
    }



    // Open PDF Viewer Modal
    $(document).on('click', '.view-btn', function(e) {
        const filePath = $(this).closest('tr').find('td').eq(8).text().trim(); // Extract file path from table
        
        // Update PDF Embed Source
        $('#pdfEmbed').attr('src', '{{ asset('storage') }}/' + filePath);
        $('#pdfViewerModal').fadeIn();
    });

    // Close PDF Viewer Modal
    $('#closePdfModal').on('click', function() {
        $('#pdfViewerModal').fadeOut();
    });
});

</script>
@endpush
