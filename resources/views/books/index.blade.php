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
                @else
                    <div class="card-cover-placeholder" role="img" aria-label="No cover available for {{ $book->title }}">
                        <span aria-hidden="true">{{ $book->title }}</span>
                    </div>
                @endif

                <h2>{{ $book->title }}</h2>
                <p>by {{ $book->author }}</p>
                <p>Publisher: {{ $book->publisher->name ?? 'Unknown' }}</p>
                <a class="button" href="{{ route('books.edit', $book) }}">Edit Books</a>
                <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this book?');">
                    @csrf
                    @method('DELETE')
                    <a type="submit" class="button delete">Delete</a>
                </form>
            </article>
        @empty
            <p>No books available yet.</p>
        @endforelse
    </div>
@endsection