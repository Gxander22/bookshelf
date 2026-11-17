@extends('layouts.app')

@section('content')
    <a class="button" href="{{ route('books.index') }}">Back to books</a>
    <h1>{{ $book->title }}</h1>

    @if ($book->cover)
        <img
            src="{{ filter_var($book->cover, FILTER_VALIDATE_URL) ? $book->cover : asset('storage/'.$book->cover) }}"
            alt="{{ $book->title }}"
        >
    @endif

    <p><strong>Author:</strong> {{ $book->author }}</p>
    <p><strong>Genre:</strong> {{ $book->genre ?? 'General' }}</p>
    <p><strong>Publisher:</strong> {{ $book->publisher->name ?? 'Unknown' }}</p>

    @if ($book->publisher?->address)
        <p><strong>Publisher address:</strong> {{ $book->publisher->address }}</p>
    @endif

    @if ($book->publisher?->website)
        <p><strong>Publisher website:</strong> {{ $book->publisher->website }}</p>
    @endif
@endsection