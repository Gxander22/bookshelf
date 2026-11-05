<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::with('publisher')->get();

        return view('books.index', ['books' => $books]);
    }

    public function bookDetail(Book $book): View
    {
        return view('book-detail', ['book' => $book]);
    }

    public function create(): View
    {
        return view('books.create', [
            'publishers' => Publisher::query()->orderBy('name')->get(),
        ]);
    }

    public function edit(Book $book): View
    {
        return view('books.edit', ['book' => $book]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'author' => ['required', 'string', 'max:100'],
            'genre' => ['required', 'string', 'max:50'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'publisher_id' => ['nullable', 'integer', 'exists:publishers,id'],
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

    public function update(Request $request, Book $book): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'author' => ['required', 'string', 'max:100'],
            'genre' => ['required', 'string', 'max:50'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'publisher_id' => ['nullable', 'integer', 'exists:publishers,id'],
        ]);

        $oldCover = $book->cover;
        $newCover = null;

        if ($request->hasFile('cover')) {
            $newCover = $request->file('cover')->store('covers', 'public');

            if ($newCover === false) {
                throw new RuntimeException('Unable to store the uploaded book cover.');
            }

            $validatedData['cover'] = $newCover;
        }

        if (! $book->update($validatedData)) {
            if ($newCover !== null) {
                Storage::disk('public')->delete($newCover);
            }

            throw new RuntimeException('Unable to update the book.');
        }

        if ($newCover !== null && $oldCover && ! filter_var($oldCover, FILTER_VALIDATE_URL)) {
            if (! Storage::disk('public')->delete($oldCover)) {
                Log::warning('Unable to remove a replaced book cover.', [
                    'book_id' => $book->getKey(),
                    'cover' => $oldCover,
                ]);
            }
        }

        return redirect()
            ->route('books.index')
            ->with('success', 'Book updated successfully.');

    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Book deleted successfully.');

    }
}
