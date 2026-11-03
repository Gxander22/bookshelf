<?php

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('the books page displays books', function () {
    $book = Book::create([
        'title' => 'The Hobbit',
        'author' => 'J. R. R. Tolkien',
        'genre' => 'Fantasy',
    ]);

    $response = $this->get('/books');

    $response
        ->assertOk()
        ->assertSeeText('The Hobbit')
        ->assertSeeText('J. R. R. Tolkien')
        ->assertSee(route('books.edit', $book));
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

test('a book can be edited without replacing its cover', function () {
    Storage::fake('public');
    Storage::disk('public')->put('covers/original.png', 'original cover');

    $book = Book::create([
        'title' => 'Old Title',
        'author' => 'Old Author',
        'genre' => 'Fantasy',
        'cover' => 'covers/original.png',
    ]);

    $this->get(route('books.edit', $book))
        ->assertOk()
        ->assertSee('value="Old Title"', false)
        ->assertSee('value="Old Author"', false)
        ->assertSee('value="Fantasy"', false);

    $response = $this->put(route('books.update', $book), [
        'title' => 'Updated Title',
        'author' => 'Updated Author',
        'genre' => 'Adventure',
    ]);

    $response
        ->assertRedirect(route('books.index'))
        ->assertSessionHas('success', 'Book updated successfully.');

    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'title' => 'Updated Title',
        'author' => 'Updated Author',
        'genre' => 'Adventure',
        'cover' => 'covers/original.png',
    ]);

    Storage::disk('public')->assertExists('covers/original.png');
});

test('editing a book replaces its existing uploaded cover', function () {
    Storage::fake('public');
    Storage::disk('public')->put('covers/original.png', 'original cover');

    $book = Book::create([
        'title' => 'Old Title',
        'author' => 'Old Author',
        'genre' => 'Fantasy',
        'cover' => 'covers/original.png',
    ]);

    $response = $this->put(route('books.update', $book), [
        'title' => 'New Title',
        'author' => 'New Author',
        'genre' => 'Mystery',
        'cover' => UploadedFile::fake()->createWithContent(
            'replacement.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lS8AAAAASUVORK5CYII='),
        ),
    ]);

    $response->assertRedirect(route('books.index'));

    $book->refresh();

    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'title' => 'New Title',
        'cover' => $book->cover,
    ]);

    Storage::disk('public')->assertExists($book->cover);
    Storage::disk('public')->assertMissing('covers/original.png');
});
