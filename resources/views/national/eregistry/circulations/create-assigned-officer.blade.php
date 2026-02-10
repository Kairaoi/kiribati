@extends('layouts.app')

@section('content')
    <div class="container">
        <h3> Confirm sending this file to admin</h3>

        <form action="{{ route('file-circulations.storeAssigned Officer') }}" method="POST">
            @csrf

            <p><strong>File Name:</strong> {{ $file->name }}</p>
            <p><strong>Send Date:</strong> {{ now()->format('Y-m-d H:i') }}</p>
            <p><strong>Circulated By:</strong> {{ auth()->user()->name }}</p>

            <label for="to_review_file">Send To Officer:</label>
            <select name="to_review_file" required>
                @foreach ($toAssignOfficers as $officer)
                    <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->roles->first()->name }})</option>
                @endforeach
            </select>

            <label for="comments">Comments (Optional):</label>
            <textarea name="comments"></textarea>

            <button type="submit">Confirm & Send</button>
        </form>
@endsection
