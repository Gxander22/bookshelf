@extends('layouts.app')

@section('content')
    <h1>Book Collection</h1>
    <p>Browse and manage books in our database</p>
    <a class="button" href="{{ route('books.create') }}">Add New Book</a>
    @session('success')
        <p class="success">{{ session('success') }}</p>
    @endsession

    <div class="book-grid">
        @forelse ($books as $book)
            <article class="card">
                <span class="tag">{{ $book->genre ?? 'General' }}</span>

                @if ($book->cover)
                    <img src="{{ filter_var($book->cover, FILTER_VALIDATE_URL) ? 
                    $book->cover : asset('storage/'.$book->cover) }}" alt="{{ $book->title }}">
                @endif

                <h2>{{ $book->title }}</h2>
                <p>by {{ $book->author }}</p>
                <a class="button" href="{{ route('books.edit', $book) }}">Edit</a>
            </article>
        @empty
            <p>No books available yet.</p>
        @endforelse
    </div>
@endsection