<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::all();

        return view('books.index', ['books' => $books]);
    }

    public function bookDetail(Book $book): View
    {
        return view('book-detail', ['book' => $book]);
    }

    public function create(): View
    {
        return view('books.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'author' => ['required', 'string', 'max:100'],
            'genre' => ['required', 'string', 'max:50'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'public');

            if ($coverPath === false) {
                throw new RuntimeException('Unable to store the uploaded book cover.');
            }

            $validatedData['cover'] = $coverPath;
        }

        Book::create($validatedData);

        return redirect()
            ->route('books.index')
            ->with('success', 'Book added successfully.');
    }
}
