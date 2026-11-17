<?php

use App\Models\Book;
use App\Models\Publisher;
use App\Models\User;
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
        ->assertSee(route('books.show', $book))
        ->assertSee('class="card-cover-placeholder"', false)
        ->assertSee('aria-label="No cover available for The Hobbit"', false)
        ->assertSee(route('books.create'))
        ->assertDontSee(route('books.edit', $book))
        ->assertDontSee('<button type="submit" class="button delete">Delete</button>', false);
});

test('a book can be opened to see its full details', function () {
    $publisher = Publisher::create([
        'name' => 'Acme Publishing',
        'address' => '100 Book Lane',
        'website' => 'https://acme.example',
    ]);

    $book = Book::create([
        'title' => 'The Hobbit',
        'author' => 'J. R. R. Tolkien',
        'genre' => 'Fantasy',
        'cover' => 'https://books.example/hobbit.jpg',
        'publisher_id' => $publisher->id,
    ]);

    $this->get(route('books.show', $book))
        ->assertOk()
        ->assertSeeText('The Hobbit')
        ->assertSeeText('J. R. R. Tolkien')
        ->assertSeeText('Fantasy')
        ->assertSeeText('Acme Publishing')
        ->assertSeeText('100 Book Lane')
        ->assertSeeText('https://acme.example')
        ->assertSee('src="https://books.example/hobbit.jpg"', false)
        ->assertSee(route('books.index'));
});

test('the books page shows six books per page', function () {
    foreach (range(1, 7) as $number) {
        Book::create([
            'title' => "Book {$number}",
            'author' => 'A. Writer',
            'genre' => 'Fiction',
        ]);
    }

    $this->get(route('books.index'))
        ->assertOk()
        ->assertSeeText('Book 1')
        ->assertSeeText('Book 2')
        ->assertSeeText('Book 3')
        ->assertSeeText('Book 6')
        ->assertDontSeeText('Book 7')
        ->assertSee(route('books.index').'?page=2');

    $this->get(route('books.index', ['page' => 2]))
        ->assertOk()
        ->assertSeeText('Book 7')
        ->assertDontSeeText('Book 1')
        ->assertSee(route('books.index'));
});

test('a book can be deleted from the books page', function () {
    $this->actingAs(User::factory()->create());

    $book = Book::create([
        'title' => 'The Hobbit',
        'author' => 'J. R. R. Tolkien',
        'genre' => 'Fantasy',
    ]);

    $this->get(route('books.index'))
        ->assertOk()
        ->assertSee('<button type="submit" class="button delete">Delete</button>', false);

    $this->delete(route('books.destroy', $book))
        ->assertRedirect(route('books.index'))
        ->assertSessionHas('success', 'Book deleted successfully.');

    $this->assertDatabaseMissing('books', ['id' => $book->id]);
});

test('a book can be created with an uploaded cover', function () {
    $this->actingAs(User::factory()->create());
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

test('the create form lists publishers and saves the selected publisher', function () {
    $this->actingAs(User::factory()->create());

    $publisher = Publisher::create([
        'name' => 'Acme Publishing',
    ]);

    $this->get(route('books.create'))
        ->assertOk()
        ->assertSee('value="'.$publisher->id.'"', false)
        ->assertSeeText('Acme Publishing');

    $this->post(route('books.store'), [
        'title' => 'Published Book',
        'author' => 'A. Writer',
        'genre' => 'Fiction',
        'publisher_id' => $publisher->id,
    ])->assertRedirect(route('books.index'));

    $this->assertDatabaseHas('books', [
        'title' => 'Published Book',
        'publisher_id' => $publisher->id,
    ]);
});

test('a book can be edited without replacing its cover', function () {
    $this->actingAs(User::factory()->create());
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
    $this->actingAs(User::factory()->create());
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
