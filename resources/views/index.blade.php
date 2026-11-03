@extends('layouts.app')

@section('content')
    <h1>Book Collection</h1>
    <p>Browse and manage books in our database</p>
    <div class="book-grid">
        @foreach ($books as $book)
            
            <article class="card">
            <span class="tag">{{ $book->genre }}</span>
            <Img src="{{ $book->cover }}"></Img>                
            <H2>{{ $book->title }}</H2>
            <p>by {{ $book->athor }}</p>

            </article>
            
        @endforeach

    </div>
@endsection