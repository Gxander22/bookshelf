@extends('layouts.app')

@section('content')
    <h1>Add New Book</h1>
    <a class="button" href="{{ route('books.index') }}">Back to Books</a>

    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" maxlength="150" required value="{{ old('title') }}"><br>
        @error('title')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="author">Author:</label>
        <input type="text" id="author" name="author" maxlength="100" required value="{{ old('author') }}"><br>
        @error('author')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="genre">Genre:</label>
        <input type="text" id="genre" name="genre" maxlength="50" required value="{{ old('genre') }}"><br>
        @error('genre')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="cover">Cover Image:</label>
        <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp"><br>
        @error('cover')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit">Add Book</button>
    </form>
@endsection
