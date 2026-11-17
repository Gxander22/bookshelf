@extends('layouts.app')

@section('content')
    <h1>Book Collection</h1>
    <p>Browse and manage books in our database</p>
    <div class="account-actions">
        <a class="button" href="{{ route('books.create') }}">Add New Book</a>
        @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">Logout</button>
            </form>
        @endauth
    </div>
    @session('success')
        <p class="success">{{ session('success') }}</p>
    @endsession

    <div class="book-grid">
        @forelse ($books as $book)
            <article class="card">
                <a class="book-card-link" href="{{ route('books.show', $book) }}">
                    <span class="tag">{{ $book->genre ?? 'General' }}</span>

                    @if ($book->cover)
                        <img src="{{ filter_var($book->cover, FILTER_VALIDATE_URL) ? $book->cover : asset('storage/'.$book->cover) }}" alt="{{ $book->title }}">
                    @else
                        <div class="card-cover-placeholder" role="img" aria-label="No cover available for {{ $book->title }}">
                            <span aria-hidden="true">{{ $book->title }}</span>
                        </div>
                    @endif

                    <h2>{{ $book->title }}</h2>
                    <p>by {{ $book->author }}</p>
                    <p>Publisher: {{ $book->publisher->name ?? 'Unknown' }}</p>
                </a>
                @auth
                    <a class="button" href="{{ route('books.edit', $book) }}">Edit Books</a>
                    <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this book?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button delete">Delete</button>
                    </form>
                @endauth
            </article>
        @empty
            <p>No books available yet.</p>
        @endforelse
    </div>
    @if ($books->hasPages())
        <nav class="pagination">
            @if($books->onFirstPage())
                <span class="disabled">Previous</span>
            @else
                <a class="button" href="{{ $books->previousPageUrl() }}">Previous</a>
            @endif

            @if($books->hasMorePages())
                <a class="button" href="{{ $books->nextPageUrl() }}">Next</a>
            @else
                <span class="disabled">Next</span>
            @endif
        </nav>
    @endif
@endsection