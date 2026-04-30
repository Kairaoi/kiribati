@extends('layouts.app')
@section('content')

    @if (session('success'))
            <div 
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition
                class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-cyan-400 text-cyan-900 px-6 py-3 rounded shadow-lg max-w-md w-full text-center z-50">
                {{ session('success') }}
            </div>
    @endif

    <div class="container px-4 max-w-6xl rounded-md min-h-screen">
        {{-- Main Content --}}
        <main class="max-w-6xl mx-auto px-4 py-4">
            
                <div class="max-w-4xl mx-auto ">
                    <h1 class="text-xl font-semibold text-cyan-700">
                        Advanced File Search
                    </h1>
                        {{-- initial type --}}
                        <div class="mt-4 mx-auto md:grid-cols-1 lg:grid-cols-1">
                            {{-- Organisation --}}
                            <div>
                                <label class="block text-m text-slate-900" id="organisationLabel">
                                    From Organisation
                                </label>
                                <select id="organisationFilter"
                                        name="organisations[]"
                                        multiple
                                        class="filter-input w-full">
                                    <option value="">All</option>
                                    @foreach ($organisations as $org)
                                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid mx-auto md:grid-cols-3 lg:grid-cols-3 mt-2 gap-2">
                            <div>
                                <label class="block text-m text-slate-900">Dispatch/Received</label>
                                <select id="initialtypeFilter" class="filter-input w-full text-sm text-gray-600 border px-3 py-2 focus:ring focus:ring-cyan-200">
                                    <option value="">All files</option>
                                    <option value="dispatch">Dispatched</option>
                                    <option value="received">Incoming Files</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-m text-slate-900">From date</label>
                                <input type="date" id="dateFrom"
                                    class="filter-input w-full text-sm text-gray-600 border px-3 py-2 focus:ring focus:ring-cyan-200">
                            </div>

                            <div>
                                <label class="block text-m text-slate-900">To date</label>
                                <input type="date" id="dateTo"
                                    class="filter-input w-full text-sm text-gray-600 border px-3 py-2 focus:ring focus:ring-cyan-200">
                            </div>
                        </div>
                        <!-- Reset Button -->
                        <div>
                            <button id="resetFilters" type="button"
                                class="filter-input mt-2 w-full md:w-auto bg-white border border-gray-300 text-sm text-cyan-700 font-medium py-2 px-4 shadow-sm 
                                    hover:bg-cyan-50 hover:text-cyan-800 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-1">
                                Reset Filters
                            </button>
                        </div>
                </div>

                <div class="mx-auto overflow-hidden mt-4">
                    <table id="filesTable" class="table table-striped w-full rounded-lg">
                        <thead> 
                            <tr>
                                <th>File ID</th> 
                                <th>From</th>
                                 <th>Date</th>
                                <th>Subject</th>
                                <th class="w-30">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('styles')
        <style>

            h1 {
                font-family: 'Poppins', sans-serif;
                font-weight: 600;
                color: #0a44e3; 
            }

            p {
                font-family: 'Poppins', sans-serif;
                /* color: #3175c2;  */
            }
            
            /* Table Styles */
            .table.dataTable {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.85rem;
                border-left: 1px solid #d3d3d8;
                border-right: 1px solid #d3d3d8;
                border-top: 1px solid #d3d3d8;
                border-bottom: 1px solid #d3d3d8;
            }

            .table.dataTable thead th {
                background-color: #ffffff; 
                border-bottom: 3px solid #d3d3d8;
                border-top: 0.5px solid #d3d3d8;
                color: #000000;
                font-family: 'Poppins', sans-serif;
                padding: 0.5rem;
                padding-top: 1rem; 
                padding-bottom: 1rem; 
                font-size: 0.9rem;
                text-align: left;
            }

            .table.dataTable td {
                vertical-align: middle;
                padding: 1.5rem;
                color: #4c4c53;
                font-size: 0.9rem;
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
                font-size: 0.9rem !important;
                font-family: 'Poppins', sans-serif !important;
                color: #4b5563 !important;
            }

            .dataTables_paginate.paginate_button {
                padding: 0.25rem 0.5rem;
                margin: 0 2px;
                border-radius: 4px;
            }



        </style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
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
        var table = $('#filesTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: "{{ route('registry.files.archive.datatables') }}",  // Updated route
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                data: function (d) {
                                d.selected_type = $('#initialtypeFilter').val();
                                d.organisation_ids = $('#organisationFilter').val();
                                d.date_from = $('#dateFrom').val();
                                d.date_to = $('#dateTo').val();
                            },
                dataSrc: 'data'
            },
            columns: [
                { data: 'id', name: 'files.id', visible: false },
                { data: 'organisation_code', name: 'organisation_code' },
                { data: 'letter_date', name: 'letter_date',
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
                { data: 'file_subject', name: 'file_subject' },
                {
                            data: null, // means we'll render manually
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row) {
                                let buttons = `
                                    <a href="/registry/files/${row.id}" 
                                    class="text-cyan-700" 
                                    title="View">
                                        View
                                    </a>
                                `;

                                return buttons += `
                                    `;;
                            }
                        }

                
            ],
            pageLength: 10,
            pagingType: "simple_numbers",
            responsive: true,
            order: false,
            dom: "<'row mb-3'<'col-md-6'f><'col-md-6'>>" +  // search box left, right side empty
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-download"></i>EXCEL',
                            className: 'excel-export-btn',
                            title: 'Outward Files Registry',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fas fa-download"></i>PDF',
                            className: 'pdf-export-btn',
                            title: 'Outward Files Registry',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        }
                    ]
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
                        $('#filesTable').DataTable().ajax.reload();
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
                'registry.files.show': "{{ route('registry.files.show', ':id') }}".replace(':id', id),
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


        // Reload table when initial type changes
        $('#initialtypeFilter').on('change', function() {
            table.ajax.reload();
        }); 

        // Reload table when organisation changes
        $('#organisationFilter').on('change', function() {
            table.ajax.reload();
        });

        // Reload table when date range changes
        $('#dateFrom, #dateTo').on('change', function() {
            table.ajax.reload();
        });


        $('.filter-input').on('change', function () {
            table.ajax.reload();
        });


        $('#resetFilters').on('click', function () {
            $('.filter-input').val('').trigger('change');
        });

        // Update organisation label based on initial type
        document.getElementById('initialtypeFilter').addEventListener('change', function () {
            const label = document.getElementById('organisationLabel');

                if (this.value === 'dispatch') {
                    label.textContent = 'To Organisation';
                } else if (this.value === 'received') {
                    label.textContent = 'From Organisation';
                } else {
                    label.textContent = 'Organisation';
                }
        });

        const orgSelect = document.getElementById('organisationFilter');

        orgSelect.addEventListener('change', function () {
            let selected = Array.from(this.selectedOptions).map(o => o.value);

            // If something other than "" is selected → remove "All"
            if (selected.length > 1 && selected.includes("")) {
                this.querySelector('option[value=""]').selected = false;
            }

            // If nothing selected → default back to All
            if (selected.length === 0) {
                this.querySelector('option[value=""]').selected = true;
            }
        });
    });   
</script>

<script>
    const choices = new Choices('#organisationFilter', {
        removeItemButton: true,
        placeholder: true,
        placeholderValue: 'All organisations',
        shouldSort: false,
        classNames: {
    }
    });
</script>

@endpush