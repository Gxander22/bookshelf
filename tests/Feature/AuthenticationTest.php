<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('shows the add book link on the books page for guests', function () {
    $this->get(route('books.index'))
        ->assertOk()
        ->assertSee(route('books.create'))
        ->assertDontSee(route('logout'));
});

it('renders the login form', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSeeText('Login')
        ->assertSee('action="'.route('login.submit').'"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false)
        ->assertSee('<a class="button" href="'.route('books.index').'">Back to books</a>', false)
        ->assertSee(route('register'));
});

it('renders the registration form', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSeeText('Register')
        ->assertSee('action="'.route('register.submit').'"', false)
        ->assertSee('name="name"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false)
        ->assertSee('name="password_confirmation"', false)
        ->assertSee('<a class="button" href="'.route('books.index').'">Back to books</a>', false);
});

it('registers and signs in a new user', function () {
    $this->post(route('register.submit'), [
        'name' => 'New Reader',
        'email' => 'reader@example.com',
        'password' => 'a-strong-password',
        'password_confirmation' => 'a-strong-password',
    ])
        ->assertRedirect(route('books.index'))
        ->assertSessionHasNoErrors();

    $user = User::where('email', 'reader@example.com')->firstOrFail();

    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('users', [
        'name' => 'New Reader',
        'email' => 'reader@example.com',
    ]);
    expect(Hash::check('a-strong-password', $user->password))->toBeTrue();
});

it('rejects duplicate email addresses during registration', function () {
    $user = User::factory()->create();

    $this->post(route('register.submit'), [
        'name' => 'Another Reader',
        'email' => $user->email,
        'password' => 'a-strong-password',
        'password_confirmation' => 'a-strong-password',
    ])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
    $this->assertDatabaseCount('users', 1);
});

it('rejects registration when passwords do not match', function () {
    $this->from(route('register'))
        ->post(route('register.submit'), [
            'name' => 'New Reader',
            'email' => 'reader@example.com',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-different-password',
        ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors('password');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'reader@example.com']);
});

it('logs in a user with valid credentials', function () {
    $user = User::factory()->create();

    $this->post(route('login.submit'), [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('books.index'))
        ->assertSessionHasNoErrors();

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid login credentials', function () {
    $user = User::factory()->create();

    $this->post(route('login.submit'), [
        'email' => $user->email,
        'password' => 'incorrect-password',
    ])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('redirects guests to the login page for protected book actions', function () {
    $this->get(route('books.create'))
        ->assertRedirect(route('login'));
});

it('redirects guests to login when they try to edit a book', function () {
    $book = Book::create([
        'title' => 'The Hobbit',
        'author' => 'J. R. R. Tolkien',
        'genre' => 'Fantasy',
    ]);

    $this->get(route('books.edit', $book))
        ->assertRedirect(route('login'));
});

it('does not allow guests to update a book', function () {
    $book = Book::create([
        'title' => 'The Hobbit',
        'author' => 'J. R. R. Tolkien',
        'genre' => 'Fantasy',
    ]);

    $this->put(route('books.update', $book), [
        'title' => 'Changed Title',
        'author' => 'J. R. R. Tolkien',
        'genre' => 'Fantasy',
    ])->assertRedirect(route('login'));

    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'title' => 'The Hobbit',
    ]);
});

it('redirects guests to login when they try to delete a book', function () {
    $book = Book::create([
        'title' => 'The Hobbit',
        'author' => 'J. R. R. Tolkien',
        'genre' => 'Fantasy',
    ]);

    $this->delete(route('books.destroy', $book))
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'title' => 'The Hobbit',
    ]);
});

it('does not allow guests to submit a new book', function () {
    $this->post(route('books.store'), [
        'title' => 'The Hobbit',
        'author' => 'J. R. R. Tolkien',
        'genre' => 'Fantasy',
    ])->assertRedirect(route('login'));

    $this->assertDatabaseMissing('books', ['title' => 'The Hobbit']);
});

it('returns users to the add book form after logging in', function () {
    $user = User::factory()->create();

    $this->get(route('books.create'))
        ->assertRedirect(route('login'));

    $this->post(route('login.submit'), [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('books.create'));
});

it('logs out an authenticated user', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('books.index'))
        ->assertOk()
        ->assertSee('action="'.route('logout').'" method="POST"', false)
        ->assertSee('>Logout</button>', false);

    $this->post(route('logout'))
        ->assertRedirect(route('books.index'));

    $this->assertGuest();
});
