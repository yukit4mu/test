<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_own_review(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->assertTrue($user->can('update', $review));
    }

    public function test_user_cannot_update_other_users_review(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $otherUser->id,
            'book_id' => $book->id,
        ]);

        $this->assertFalse($user->can('update', $review));
    }

    public function test_user_can_delete_own_review(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->assertTrue($user->can('delete', $review));
    }

    public function test_user_cannot_delete_other_users_review(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $otherUser->id,
            'book_id' => $book->id,
        ]);

        $this->assertFalse($user->can('delete', $review));
    }
}
