<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private \Illuminate\Database\Eloquent\Collection $genres;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->genres = Genre::factory()->count(2)->create();
    }

    public function test_index_displays_books(): void
    {
        Book::factory()->count(3)->create();

        $response = $this->get(route("books.index"));

        $response->assertStatus(200);
        $response->assertViewIs("books.index");
        $response->assertSee(Book::first()->title);
    }

    public function test_guest_cannot_access_create_page(): void
    {
        $response = $this->get(route("books.create"));
        $response->assertRedirect(route("login"));
    }

    public function test_user_can_access_create_page(): void
    {
        $response = $this->actingAs($this->user)->get(route("books.create"));
        $response->assertStatus(200);
    }

    public function test_user_can_store_a_new_book(): void
    {
        $bookData = [
            "title" => "新しいテスト書籍",
            "author" => "テスト著者",
            "isbn" => "1234567890123",
            "published_date" => "2025-01-01",
            "description" => "テスト説明",
            "image_url" => "https://example.com/test.jpg",
            "genres" => $this->genres->pluck("id")->toArray(),
        ];

        $response = $this->actingAs($this->user)->post(route("books.store"), $bookData);

        $response->assertRedirect();
        $this->assertDatabaseHas("books", ["title" => "新しいテスト書籍"]);
        $book = Book::where('title', '新しいテスト書籍')->first();
        $this->assertCount(2, $book->genres);
    }

    public function test_user_can_update_own_book(): void
    {
        $book = Book::factory()->create(['user_id' => $this->user->id]);
        $book->genres()->attach($this->genres->pluck('id'));

        $newGenres = Genre::factory()->count(2)->create();
        $updatedData = [
            "title" => "更新された書籍タイトル",
            "author" => $book->author,
            "isbn" => $book->isbn,
            "published_date" => $book->published_date->format('Y-m-d'),
            "description" => $book->description,
            "genres" => $newGenres->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($this->user)->put(route("books.update", $book), $updatedData);

        $response->assertRedirect(route("books.show", $book));
        $this->assertDatabaseHas("books", ["title" => "更新された書籍タイトル"]);
    }

    public function test_user_cannot_update_other_users_book(): void
    {
        $otherUser = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $otherUser->id]);
        $book->genres()->attach($this->genres->pluck('id'));

        $updatedData = [
            "title" => "不正な更新",
            "author" => $book->author,
            "isbn" => $book->isbn,
            "published_date" => $book->published_date->format('Y-m-d'),
            "description" => $book->description,
            "genres" => $this->genres->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($this->user)->put(route("books.update", $book), $updatedData);

        $response->assertForbidden();
    }

    public function test_user_can_delete_own_book(): void
    {
        $book = Book::factory()->create(["user_id" => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route("books.destroy", $book));

        $response->assertRedirect(route("books.index"));
        $this->assertDatabaseMissing("books", ["id" => $book->id]);
    }

    public function test_user_cannot_delete_other_users_book(): void
    {
        $otherUser = User::factory()->create();
        $book = Book::factory()->create(["user_id" => $otherUser->id]);

        $response = $this->actingAs($this->user)->delete(route("books.destroy", $book));

        $response->assertForbidden();
        $this->assertDatabaseHas("books", ["id" => $book->id]);
    }

    public function test_search_returns_matching_books(): void
    {
        Book::factory()->create(['title' => 'Laravel入門']);
        Book::factory()->create(['title' => 'PHP入門']);
        Book::factory()->create(['title' => 'JavaScript入門']);

        $response = $this->get(route("books.search", ['query' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertSee('Laravel入門');
        $response->assertDontSee('JavaScript入門');
    }
}
