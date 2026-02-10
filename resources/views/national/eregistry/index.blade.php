@extends('layouts.app')

@push('styles')
<style>
    body {
        font-family: 'Montserrat', sans-serif;
        color: #f3e8e8;
        background-color: #154970;
        line-height: 1.6;
    }

    /* General Container */
    .container {
        max-width: 1200px;
        margin: 60px auto 0 auto; /* Top margin increased to 60px */
        padding: 40px 20px;
        
    }


    /* Tabs Styling */
    .tabs {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
        margin-bottom: 30px;
    }

    .tab {
        padding: 12px 25px;
        background-color: #ffffff;
        border-radius: 30px;
        cursor: pointer;
        font-weight: 600;
        color: #060e0e;
        transition: all 0.3s ease;
        font-family: 'Montserrat', sans-serif;
        
    }

    .tab:hover {
        background-color: #d3d6db;
    }

    .tab.active {
        background-color: #0f58a7;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    /* Tab Content */
    .tab-content {
        display: none;
        background-color: #ffffff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        font-family: 'Montserrat', sans-serif;
        width: 80%;
        margin: 0 auto;
    }

    .tab-content.active {
        display: block;
    }

    /* Card Styling */
    .card {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
    }

    .card-title {
        font-size: 1.8rem;
        font-weight: 600;
        font-family: 'Montserrat', sans-serif;
        color: #184f8a;
        margin-bottom: 15px;
    }

    .card-text {
        color: #6c757d;
        font-size: 1rem;
        margin-bottom: 20px;
    }

    .btn-primary {
        background-color: #43b988;
        border: none;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: bold;
        font-size: 1rem;
        font-family: 'Montserrat', sans-serif;
        color: #ffffff;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        display: inline-block;
        text-decoration: none;
        width: 30%;
        margin: 0 auto;
    }

    .btn-primary:hover {
        background-color: #1c916e;
        transform: scale(1.05);
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
{{-- <x-app-layout> --}}

<div class="container">

    @role('registry')
        <!-- Tabs Navigation -->
        <div class="tabs" id="tabs">
            @foreach ([ 
                ['id' => 'dispatches', 'title' => 'Dispatches', 'icon' => '', 'route' => 'registry.dispatches.index'],
                ['id' => 'circulations', 'title' => 'Circulations', 'icon' => '', 'route' => 'registry.file-circulations.index'],
                // ['id' => 'files', 'title' => 'Archived Files', 'icon' => '', 'route' => 'registry.files.index'],
        
            ] as $tab)
                <div class="tab {{ $loop->first ? 'active' : '' }}" data-tab="{{ $tab['id'] }}">
                    <i class="{{ $tab['icon'] }}"></i> {{ $tab['title'] }}
                </div>
            @endforeach
        </div>

        <!-- Tab Content -->
        @foreach ([ 
            ['id' => 'dispatches', 'title' => 'Dispatches', 'btn-title' => 'Manage Dispatches', 'description' => 'Manage all dispatches in your organisation', 'route' => 'registry.dispatches.index'],
            ['id' => 'circulations', 'title' => 'Circulations', 'btn-title' => 'Manage Circulations', 'description' => 'Manage Circulations in your organisation', 'route' => 'registry.file-circulations.index'],
            // ['id' => 'files', 'title' => 'Files', 'btn-title' => 'Manage All Files', 'description' => 'Manage All Files in your organisation', 'route' => 'registry.files.index'],
        ] as $tab)
            <div class="tab-content {{ $loop->first ? 'active' : '' }}" id="{{ $tab['id'] }}">
                <div class="card">
                    <h3 class="card-title">{{ $tab['title'] }}</h3>
                    <p class="card-text">{{ $tab['description'] }}</p>
                    <a href="{{ route($tab['route']) }}" class="btn btn-primary">{{ $tab['btn-title'] }}</a>
                </div>
            </div>
        @endforeach
    @endrole

    @role('user')
        <!-- Tabs Navigation -->
        <div class="tabs" id="tabs">
            @foreach ([ 
                // ['id' => 'filetypes', 'title' => 'File Types', 'icon' => 'bi bi-folder', 'route' => 'registry.file-types.create'],
                ['id' => 'user-dispatches', 'title' => 'Dispatches', 'icon' => '', 'route' => 'registry.dispatches.user.index'],
                ['id' => 'files-review', 'title' => 'Files For Review', 'icon' => 'bi bi-file-earmark', 'route' => 'registry.files.index'],
                // ['id' => 'files-assigned', 'title' => 'Assigned Files', 'icon' => 'bi bi-file-earmark', 'route' => 'registry.files.index'],
                // ['id' => 'organisations', 'title' => 'Ministries', 'icon' => 'bi bi-house-door', 'route' => 'registry.organisations.index'],
                // ['id' => 'divisions', 'title' => 'Divisions', 'icon' => 'bi bi-building', 'route' => 'registry.divisions.index'],
        
            ] as $tab)
                <div class="tab {{ $loop->first ? 'active' : '' }}" data-tab="{{ $tab['id'] }}">
                    <i class="{{ $tab['icon'] }}"></i> {{ $tab['title'] }}
                </div>
            @endforeach
        </div>

        <!-- Tab Content -->
        @foreach ([ 
            // ['id' => 'filetypes', 'title' => 'File Types', 'description' => 'View and manage the boards in the national registry.', 'route' => 'registry.file-types.create'], 
            ['id' => 'dispatches', 'title' => 'Dispatches', 'description' => 'View and manage your file dispatches in the national registry', 'route' => 'registry.dispatches.user.index'],
            ['id' => 'organisations', 'title' => 'Ministries', 'description' => 'View and manage the boards in the national registry.', 'route' => 'registry.organisations.index'],
            // ['id' => 'divisions', 'title' => 'Divisions', 'description' => 'View and manage the boards in the national registry.', 'route' => 'registry.divisions.index'],
        
        ] as $tab)
            <div class="tab-content {{ $loop->first ? 'active' : '' }}" id="{{ $tab['id'] }}">
                <div class="card">
                    <h3 class="card-title">{{ $tab['title'] }}</h3>
                    <p class="card-text">{{ $tab['description'] }}</p>
                    <a href="{{ route($tab['route']) }}" class="btn btn-primary">Go to {{ $tab['title'] }}</a>
                </div>
            </div>
        @endforeach
    @endrole

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
