@extends('layouts.app')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        color: #333;
    }

    /* General Styles */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px;
    }

    /* Tabs Styling */
    .tabs {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 30px;
    }

    .tab {
        padding: 12px 25px;
        background-color: #f1f1f1;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        color: #007bff;
        transition: background-color 0.3s, color 0.3s;
    }

    .tab.active {
        background-color: #007bff;
        color: white;
    }

    /* Content Section */
    .tab-content {
        display: none;
        background-color: #f9f9f9;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .tab-content.active {
        display: block;
    }

    .card {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .card-title {
        font-size: 1.6rem;
        color: #007bff;
        margin-bottom: 15px;
    }

    .card-text {
        color: #6c757d;
        font-size: 1rem;
        margin-bottom: 20px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        border-radius: 30px;
        padding: 10px 20px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    /* Responsiveness */
    @media (max-width: 768px) {
        .tabs {
            flex-direction: column;
            align-items: center;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Tabs Navigation -->
    <div class="tabs" id="tabs">
        @foreach ([
            ['id' => 'filetypes', 'title' => 'File Types', 'icon' => 'bi bi-folder', 'route' => 'registry.file-types.create'],
            // ['id' => 'folders', 'title' => 'Folders', 'icon' => 'bi bi-folder', 'route' => 'registry.folders.index'],
            ['id' => 'files', 'title' => 'Files', 'icon' => 'bi bi-file-earmark', 'route' => 'registry.files.index'],
            ['id' => 'movements', 'title' => 'Movements', 'icon' => 'bi bi-arrow-right-circle', 'route' => 'registry.movements.index'],
            ['id' => 'ministries', 'title' => 'Ministries', 'icon' => 'bi bi-house-door', 'route' => 'registry.ministries.index'],
            // ['id' => 'divisions', 'title' => 'Divisions', 'icon' => 'bi bi-building', 'route' => 'registry.divisions.index'],
            ['id' => 'outwardfiles', 'title' => 'Outward Files', 'icon' => 'bi bi-folder', 'route' => 'registry.outward-files.index'],
            ['id' => 'inwardfiles', 'title' => 'Inward Files', 'icon' => 'bi bi-folder', 'route' => 'registry.inward-files.index'],

        ] as $tab)
            <div class="tab {{ $loop->first ? 'active' : '' }}" data-tab="{{ $tab['id'] }}">
                <i class="{{ $tab['icon'] }}"></i> {{ $tab['title'] }}
            </div>
        @endforeach
    </div>

    <!-- Tab Content -->
    @foreach ([
        ['id' => 'filetypes', 'title' => 'File Types', 'description' => 'View and manage the boards in the national registry.', 'route' => 'registry.file-types.create'],
        // ['id' => 'folders', 'title' => 'Folders', 'description' => 'View and manage the boards in the national registry.', 'route' => 'registry.folders.index'],
        ['id' => 'files', 'title' => 'Files', 'description' => 'View and manage the boards in the national registry.', 'route' => 'registry.files.index'],
        ['id' => 'movements', 'title' => 'Movements', 'description' => 'View and manage the boards in the national registry.', 'route' => 'registry.movements.index'],
        ['id' => 'ministries', 'title' => 'Ministries', 'description' => 'View and manage the boards in the national registry.', 'route' => 'registry.ministries.index'],
        // ['id' => 'divisions', 'title' => 'Divisions', 'description' => 'View and manage the boards in the national registry.', 'route' => 'registry.divisions.index'],
        ['id' => 'outwardfiles', 'title' => 'Outward Files', 'description' => 'View and manage the boards in the national registry.', 'route' => 'registry.outward-files.index'],
        ['id' => 'inwardfiles', 'title' => 'Inward Files', 'description' => 'View and manage the boards in the national registry.', 'route' => 'registry.inward-files.index'],

    ] as $tab)
        <div class="tab-content {{ $loop->first ? 'active' : '' }}" id="{{ $tab['id'] }}">
            <div class="card">
                <h3 class="card-title">{{ $tab['title'] }}</h3>
                <p class="card-text">{{ $tab['description'] }}</p>
                <a href="{{ route($tab['route']) }}" class="btn btn-primary">Go to {{ $tab['title'] }}</a>
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
<script>
    // JavaScript for switching between tabs
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs and content
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to the clicked tab and its corresponding content
                tab.classList.add('active');
                const activeTabContent = document.getElementById(tab.getAttribute('data-tab'));
                activeTabContent.classList.add('active');
            });
        });
    });
</script>
@endpush
@endsection
