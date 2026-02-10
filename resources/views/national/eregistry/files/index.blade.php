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

    <div class="flex min-h-screen bg-gray-50">
        <div class="w-64 bg-gray-100 p-4">
        <h3 class="font-bold mb-3 mt-4">Quick Filters</h3>
        @foreach($archives as $year => $months)
            <div class="mb-3">
                <h4 class="font-semibold">Monthly Archives</h4>
                <ul class="ml-3">
                    @foreach($months as $item)
                        <li>
                            <a href="#"
                            class="archive-link text-blue-600 hover:underline"
                            data-year="{{ $item->year }}"
                            data-month="{{ $item->month }}">
                                {{ \Carbon\Carbon::create()->month($item->month)->format('F') }} {{ $year }}
                                ({{ $item->total }})
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        <h4 class="font-semibold mb-2">Organisations</h4>

        <ul class="ml-3">
            <li>
                <a href="#"
                class="org-filter text-blue-600 hover:underline"
                data-org="">
                All Organisations
                </a>
            </li>
            @foreach($orgFilters as $org)
                <li>
                    <a href="#"
                    class="org-filter text-blue-600 hover:underline"
                    data-org="{{ $org->id }}">
                        {{ $org->code }} ({{ $org->total }})
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Main Content --}}
    <main class="flex-1 mx-auto max-w-5xl px-10 py-8">
        <h1 class="text-xl font-semibold text-gray-800">
                    Advanced File Search
        </h1>

        {{-- Advanced Filters --}}
        <div class="mx-auto p-6 mb-8 max-w-5xl">

            {{-- <div class="flex items-center justify-between mb-4">
                <button id="toggleFilters"
                        class="text-sm text-blue-600 hover:underline">
                    Show / Hide
                </button>
            </div> --}}

            <div class="bg-blue-100 p-4 rounded-lg border border-blue-200 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- initial type --}}
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Dispatch or Received
                    </label>
                    <select id="initialtypeFilter"
                            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-200">
                        <option value="all">All files</option>
                        <option value="dispatch">Dispatched</option>
                        <option value="received">Incoming Files</option>
                    </select>
                </div>


                {{-- Organisation --}}
                <div>
                    <label class="block text-sm text-gray-600 mb-1" id="organisationLabel">
                        From Organisation
                    </label>
                    <select id="organisationFilter"
                            name="organisations[]"
                            multiple
                            class="w-full">
                        <option value="all">All</option>
                        @foreach ($organisations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </select>

                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">From date</label>
                    <input type="date" id="dateFrom"
                        class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">To date</label>
                    <input type="date" id="dateTo"
                        class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
            </div>
        </div>

        {{-- Table --}}
        <h1 class="text-xl mx-auto font-semibold text-gray-800 max-w-5xl mb-4">
            Files
        </h1>

        <div class="bg-white rounded-lg overflow-hidden shadow-sm">
            <table id="filesTable" class="display icon-table w-full">
                {{-- <thead class="bg-blue-100"> --}}
                    {{-- <tr> --}}
                        {{-- <th class="te text-sm font-medium">File ID</th> --}}
                        {{-- <th class="px-4 py-3 text-sm font-medium">Name</th> --}}
                        {{-- <th class="px-4 py-3 text-sm font-medium">From</th> --}}
                        {{-- <th class="px-4 py-3 text-sm font-medium">To</th> --}}
                        {{-- <th class="px-4 py-3 text-sm font-medium">Date</th> --}}
                    {{-- </tr> --}}
                {{-- </thead> --}}
                <tbody></tbody>
            </table>
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
        color: #3175c2; 
    }

    /* turn rows into grid items */
    #filesTable tbody {
        display: flex;
        flex-wrap: wrap;
    }

    #filesTable tbody tr {
        display: block;
        width: 140px;
        margin: 10px;
    }

    #filesTable tbody td {
        display: block;
        border: none;
    }

    /* icon card */
    .file-icon {
        text-align: center;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
    }

    .file-icon i {
        font-size: 42px;
        color: #5687f0;
    }

    .file-name {
        margin-top: 6px;
        font-size: 13px;
        word-break: break-word;
    }


    /* Table Styles */
    table.dataTable {
        width: 100%;
        border-collapse: collapse;
        border-radius: 0.5rem;
        border-left: 0.5px solid #d3d3d8; 
        border-right: 0.5px solid #d3d3d8;
        align-items: center;
    }

    .table.dataTable thead th {
        background-color: #ffffff; 
        border-bottom: 3px solid #d3d3d8;
        border-top: 0.5px solid #d3d3d8;
        color: #2c2a2a;
        font-family: 'Poppins', sans-serif;
        padding: 0.5rem;
        padding-top: 1rem; 
        padding-bottom: 1rem; 
        font-size: 0.85em;
    }


     .table.dataTable td {
        background-color: #ffffff; 
        color: #2c2a2a;
        font-family: 'Poppins', sans-serif;
        padding: 0.5rem;
        padding-top: 1rem; 
        padding-bottom: 1rem; 
        font-size: 0.85em;
    }
    

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
        background-color: #105bf1; 
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
            paging: false,
            scrollY: '60vh',
            info: false,
            scrollCollapse: true,
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
                // { data: 'id', name: 'files.id', visible: false },
                {
                    data: 'file_name',
                    name: 'files.name',
                    render: function (data, type, row) {
                        return `
                            <div>
                                <a href="/registry/files/${row.id}" 
                                class="text-blue-600 underline font-semibold">
                                    ${data}
                                </a>
                                <div class="text-sm text-gray-500">
                                    From: ${row.organisation_code}
                                </div>
                                <div class="text-xs text-gray-400">
                                    🗓️ ${row.letter_date}
                                </div>

                                </div>
                            </div>
                        `;
                    }
                }
            ],
            responsive: true,
            order: false,
            dom: "<'row mb-3'<'col-md-6'f><'col-md-6'>>" +  // search box left, right side empty
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
            
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
        shouldSort: false
    });
</script>


@endpush
