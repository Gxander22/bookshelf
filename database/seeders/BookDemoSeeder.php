<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class BookDemoSeeder extends Seeder
{
    public function run(): void
    {
        $publisher = Publisher::firstOrCreate([
            'name' => 'Campus Demo Press',
        ]);

        $books = [
            [
                'title' => 'Learning Web Layout',
                'author' => 'Alex Rivera',
                'genre' => 'Education',
            ],
            [
                'title' => 'A Practical Guide to Databases',
                'author' => 'Sam Reyes',
                'genre' => 'Technology',
            ],
        ];

        foreach ($books as $book) {
            Book::firstOrCreate(
                [
                    'title' => $book['title'],
                    'author' => $book['author'],
                ],
                [
                    'genre' => $book['genre'],
                    'publisher_id' => $publisher->id,
                    'cover' => null,
                ]
            );
        }
    }
}
