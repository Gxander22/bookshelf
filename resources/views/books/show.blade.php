@extends('layouts.app')

@section('content')
    <span class="tag">{{ $book->genre ?? 'General' }}</span>
    <img src="{{ $book->cover }}">
    <h1>{{ $book->title }}</h1>
    <p>by {{ $book->author }}</p>
@endsection