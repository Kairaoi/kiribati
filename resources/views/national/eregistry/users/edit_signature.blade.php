@extends('layouts.app')

@section('content')

{{-- <div class="container mx-auto font-roboto px-8 max-w-5xl mt-1"> --}}
<div class="container mx-w-5xl mx-auto mt-4">
    <div class="max-w-3xl mx-auto bg-white shadow-sm border border-gray-200 rounded-lg p-6">
        <h3 class=" text-gray-500 mb-2 font-semibold">
            Preview Current Signature:
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            Signature images should be tightly cropped with little or no surrounding white space to ensure proper placement and appearance in generated documents and PDF files.
        </p>
        @if(auth()->user()->signature_path)
            <div class="mb-6">
                <div class="inline-block bg-white border rounded-xl p-4 shadow-sm">
                    <img src="{{ Storage::url(auth()->user()->signature_path) }}"
                        alt="Official Signature"
                        class="h-24 object-contain">
                </div>
            </div>
        @endif
    </div>
    <div class="max-w-3xl mx-auto mt-6 mb-4 space-y-4">
        <form method="POST" action="{{ route('registry.users.signature.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input type="file" name="signature" class="w-full border border-gray-300 text-sm rounded-md px-4 py-2 focus:ring focus:ring-cyan-200">

            <button type="submit"
                    class="w-full flex px-4 py-2 justify-center bg-cyan-600 hover:bg-cyan-700 text-white mt-2 rounded-md font-semibold">
                Update Signature
            </button>
        </form>
    </div>
</div>

@endsection