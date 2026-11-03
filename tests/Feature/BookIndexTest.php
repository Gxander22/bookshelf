<?php

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the books page displays books', function () {
    Book::create([
        'title' => 'The Hobbit',
        'author' => 'J. R. R. Tolkien',
    ]);

    $response = $this->get('/books');

    $response
        ->assertOk()
        ->assertSeeText('The Hobbit')
        ->assertSeeText('J. R. R. Tolkien');
});
