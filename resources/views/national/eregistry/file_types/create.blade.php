@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Create File</h1>
    </div>

    <!-- File Type Dropdown -->
    <div class="mb-4">
        <label for="fileType" class="form-label text-lg font-medium">File Type</label>
        <select 
            class="form-select block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" 
            name="file_type_id" 
            id="fileType" 
            required>
            <option value="" disabled selected>Choose a file type</option>
            @foreach($fileTypes as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Dynamic Form Container -->
    <div id="dynamic_form" class="p-4 border border-gray-200 rounded-lg bg-white shadow-sm">
        <p class="text-gray-500">Please select a file type to load the corresponding form.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileTypeDropdown = document.getElementById('fileType');
    const dynamicFormContainer = document.getElementById('dynamic_form');

    fileTypeDropdown.addEventListener('change', function () {
        const selectedFileTypeId = this.value;

        if (!selectedFileTypeId) {
            dynamicFormContainer.innerHTML = '<p class="text-gray-500">Please select a file type to load the corresponding form.</p>';
            return;
        }

        // Show loading indicator
        dynamicFormContainer.innerHTML = '<p class="text-blue-500">Loading form...</p>';

        // Get CSRF token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/registry/file-types/${selectedFileTypeId}/dynamic-form`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(html => {
            dynamicFormContainer.innerHTML = html;

            // Add file type ID to the loaded form
            const form = dynamicFormContainer.querySelector('form');
            if (form) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'file_type_id';
                hiddenInput.value = selectedFileTypeId;
                form.appendChild(hiddenInput);
            }
        })
        .catch(error => {
            dynamicFormContainer.innerHTML = `<p class="text-red-500">Error loading form: ${error.message}</p>`;
            console.error('Error:', error);
        });
    });
});
</script>
@endpush