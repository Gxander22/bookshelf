@extends('layouts.app')

@section('content')
    <a class="button" href="{{ route('books.index') }}">Back to books</a>
    <h1>Login</h1>

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="{{ old('email') }}">
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
@endsection