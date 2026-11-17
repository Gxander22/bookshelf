@extends('layouts.app')

@section('content')
    <a class="button" href="{{ route('books.index') }}">Back to books</a>
    <h1>Register</h1>

    <form method="POST" action="{{ route('register.submit') }}">
        @csrf

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required value="{{ old('name') }}">
        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror

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

        <label for="password_confirmation">Confirm password:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
@endsection
