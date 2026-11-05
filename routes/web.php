<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/books');

Route::get('/books', [BookController::class, 'index'])->name('books.index');

Route::get('/books/create', [BookController::class, 'create'])->name('books.create');

Route::post('/books', [BookController::class, 'store'])->name('books.store');

Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');

Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');

Route::get('/books/{book}', [BookController::class, 'bookDetail'])->name('books.show');

Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
