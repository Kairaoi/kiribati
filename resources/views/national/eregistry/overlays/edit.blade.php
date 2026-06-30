@extends('layouts.app')

@section('content')

<div class="flex gap-4 mb-4 justify-center mt-4">
    <div id="pdf-wrapper" class="relative border">
        <canvas id="pdf-canvas"></canvas>
        <div id="konva-container" class="absolute top-0 left-0"></div>
    </div>
</div>

<div class ="flex justify-center gap-4 mb-4">
    <form method="POST" action="{{ route('registry.overlays.save', $fileCirculation) }}">
        @csrf
        <button
            id="save-overlays"
            type="button"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Save Overlays
        </button>
    </form>
    <form method="POST" action="{{ route('registry.overlays.finalize', $fileCirculation) }}">
        @csrf
        <button class="px-4 py-2 bg-green-600 text-white rounded">
            Finalize PDF 
        </button>
    </form>
</div>

<script>
    window.overlayConfig = {
        pdfUrl: @json($pdfUrl),
        overlays: @json($overlays),
        saveUrl: @json(route('registry.overlays.save', $fileCirculation)),
        csrf: @json(csrf_token()),
    };
</script>

@vite([
    'resources/js/overlays/editor.js'
])

@endsection