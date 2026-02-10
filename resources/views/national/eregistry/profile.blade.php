@extends('layouts.app')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    body {
    font-family: 'Montserrat', sans-serif;
    color: #f3e8e8;
    background-color: #eef2f5;
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

{{-- <div class="container">

    @role('registry')
        <!-- Tabs Navigation -->
        <div class="tabs" id="tabs">
            @foreach ([ 
                ['id' => 'files-to-review', 'title' => 'Files For Assignment', 'icon' => 'bi bi-file-earmark', 'route' => 'registry.files.index'],
                ['id' => 'assigned-files', 'title' => 'My Assigned Files', 'icon' => 'bi bi-house-earmark', 'route' => 'registry.files.index'],
        
            ] as $tab)
                <div class="tab {{ $loop->first ? 'active' : '' }}" data-tab="{{ $tab['id'] }}">
                    <i class="{{ $tab['icon'] }}"></i> {{ $tab['title'] }}
                </div>
            @endforeach
        </div>

        <!-- Tab Content -->
        @foreach ([ 
            ['id' => 'files-to-review', 'title' => 'Files To Review', 'btn-title' => 'Manage Assignments', 'description' => 'Review and assign these files to the appropriate officers.', 'route' => 'registry.files.index'],
            ['id' => 'assigned-files', 'title' => 'My Assigned Files', 'btn-title' => 'View My Files', 'description' => 'Files assigned to you for action', 'route' => 'registry.files.index'],
        
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


</div> --}}

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
