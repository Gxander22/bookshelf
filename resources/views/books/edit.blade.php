@extends('layouts.app')

@section('content')
    <h1>Edit Book</h1>

    <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" maxlength="150" required value="{{ old('title', $book->title) }}">
        @error('title')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="author">Author:</label>
        <input type="text" id="author" name="author" maxlength="100" required value="{{ old('author', $book->author) }}">
        @error('author')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="genre">Genre:</label>
        <input type="text" id="genre" name="genre" maxlength="50" required value="{{ old('genre', $book->genre) }}">
        @error('genre')
            <div class="error">{{ $message }}</div>
        @enderror

        @if ($book->cover)
            <label>Current cover:</label>
            <img class="current-cover" src="{{ filter_var($book->cover, FILTER_VALIDATE_URL) ? $book->cover : asset('storage/'.$book->cover) }}" alt="{{ $book->title }}">
        @endif

        <label for="cover">Replace cover (optional):</label>
        <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp">
        @error('cover')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit">Save Changes</button>
        <a href="{{ route('books.index') }}">Cancel</a>
    </form>
@endsection
