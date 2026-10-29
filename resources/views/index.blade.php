@extends('')

@section('content')
    <h1>Book Collection</h1>
    <p> Browse and manage books in our database.</p>
    <div>
        @foreach ($books as $book)
            <article class="card">
                <span>
            
        @endforeach
