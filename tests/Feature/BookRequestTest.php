<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookRequestTest extends TestCase
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

    public function test_valid_book_data_passes_validation(): void
    {
        $response = $this->actingAs($this->user)->post(route("books.store"), [
            "title" => "テスト書籍",
            "author" => "テスト著者",
            "isbn" => "1234567890123",
            "published_date" => "2023-01-01",
            "genres" => $this->genres->pluck("id")->toArray(),
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_title_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route("books.store"), [
            "title" => "",
            "author" => "著者",
            "isbn" => "1234567890123",
            "published_date" => "2023-01-01",
            "genres" => $this->genres->pluck("id")->toArray(),
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_isbn_must_be_13_characters(): void
    {
        $response = $this->actingAs($this->user)->post(route("books.store"), [
            "title" => "タイトル",
            "author" => "著者",
            "isbn" => "12345",
            "published_date" => "2023-01-01",
            "genres" => $this->genres->pluck("id")->toArray(),
        ]);

        $response->assertSessionHasErrors('isbn');
    }

    public function test_genres_must_be_array(): void
    {
        $response = $this->actingAs($this->user)->post(route("books.store"), [
            "title" => "タイトル",
            "author" => "著者",
            "isbn" => "1234567890123",
            "published_date" => "2023-01-01",
            "genres" => "1",
        ]);

        $response->assertSessionHasErrors('genres');
    }

    public function test_genres_must_exist_in_database(): void
    {
        $response = $this->actingAs($this->user)->post(route("books.store"), [
            "title" => "タイトル",
            "author" => "著者",
            "isbn" => "1234567890123",
            "published_date" => "2023-01-01",
            "genres" => [9999, 9998],
        ]);

        $response->assertSessionHasErrors('genres.0');
    }
}
