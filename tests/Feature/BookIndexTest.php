<?php

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('the books page displays books', function () {
    Book::create([
        'title' => 'The Hobbit',
        'author' => 'J. R. R. Tolkien',
        'genre' => 'Fantasy',
    ]);

    $response = $this->get('/books');

    $response
        ->assertOk()
        ->assertSeeText('The Hobbit')
        ->assertSeeText('J. R. R. Tolkien');
});

test('a book can be created with an uploaded cover', function () {
    Storage::fake('public');

    $this->get(route('books.create'))
        ->assertOk()
        ->assertSee('method="POST"', false)
        ->assertSee('enctype="multipart/form-data"', false);

    $response = $this->post(route('books.store'), [
        'title' => 'Dune',
        'author' => 'Frank Herbert',
        'genre' => 'Science Fiction',
        'cover' => UploadedFile::fake()->createWithContent(
            'dune.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lS8AAAAASUVORK5CYII='),
        ),
    ]);

    $response
        ->assertRedirect(route('books.index'))
        ->assertSessionHas('success', 'Book added successfully.');

    $this->assertDatabaseHas('books', [
        'title' => 'Dune',
        'author' => 'Frank Herbert',
        'genre' => 'Science Fiction',
    ]);

    $book = Book::where('title', 'Dune')->firstOrFail();
    Storage::disk('public')->assertExists($book->cover);

    $this->get(route('books.index'))
        ->assertOk()
        ->assertSeeText('Dune');
});
