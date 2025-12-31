<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::with("genres")->latest()->paginate(10);
        return view("books.index", compact("books"));
    }

    public function create(): View
    {
        $genres = Genre::all();
        return view("books.create", compact("genres"));
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $bookData = collect($validated)->except('genres')->toArray();
        $book = $request->user()->books()->create($bookData);
        $book->genres()->attach($validated['genres']);

        return redirect()->route("books.show", $book)->with("success", "書籍を登録しました。");
    }

    public function show(Book $book): View
    {
        $book->load(["reviews.user", "reviews.likedByUsers", "genres"]);
        return view("books.show", compact("book"));
    }

    public function edit(Book $book): View
    {
        $this->authorize("update", $book);
        $genres = Genre::all();
        return view("books.edit", compact("book", "genres"));
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $this->authorize("update", $book);
        $book->update($request->validated());
        $book->genres()->sync($request->genres);

        return redirect()->route("books.show", $book)->with("success", "書籍情報を更新しました。");
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize("delete", $book);
        $book->delete();

        return redirect()->route("books.index")->with("success", "書籍を削除しました。");
    }

    /**
     * 書籍検索
     */
    public function search(Request $request): View
    {
        $query = $request->input('query');
        
        $books = Book::with('genres')
            ->when($query, function ($q, $query) {
                return $q->where('title', 'like', "%{$query}%")
                         ->orWhere('author', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('books.index', compact('books', 'query'));
    }

    /**
     * Google Books APIから書籍情報を取得
     */
    public function fetch(Request $request): JsonResponse
    {
        $isbn = $request->query("isbn");

        if (!$isbn) {
            return response()->json(["error" => "ISBNが必要です"], 400);
        }

        $response = Http::get("https://www.googleapis.com/books/v1/volumes", [
            "q" => "isbn:" . $isbn,
            "key" => config("services.google.books_api_key"),
        ]);

        if ($response->failed() || $response->json("totalItems") === 0) {
            return response()->json(["error" => "書籍が見つかりませんでした"], 404);
        }

        $volumeInfo = $response->json("items.0.volumeInfo");

        return response()->json([
            "title" => $volumeInfo["title"] ?? "",
            "author" => implode(", ", $volumeInfo["authors"] ?? []),
            "published_date" => $volumeInfo["publishedDate"] ?? "",
            "description" => $volumeInfo["description"] ?? "",
            "image_url" => $volumeInfo["imageLinks"]["thumbnail"] ?? "",
        ]);
    }

    /**
     * CSVエクスポート
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = $request->query('query');

        $response = new StreamedResponse(function () use ($query) {
            $stream = fopen('php://output', 'w');
            // BOMを追加（Excel対応）
            fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF));
            // ヘッダー
            fputcsv($stream, ['ID', 'タイトル', '著者', 'ISBN', '出版日']);

            Book::query()
                ->when($query, function ($q, $query) {
                    return $q->where('title', 'like', "%{$query}%")
                             ->orWhere('author', 'like', "%{$query}%");
                })
                ->chunk(100, function ($books) use ($stream) {
                    foreach ($books as $book) {
                        fputcsv($stream, [
                            $book->id,
                            $book->title,
                            $book->author,
                            $book->isbn,
                            $book->published_date,
                        ]);
                    }
                });

            fclose($stream);
        });

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="books.csv"');

        return $response;
    }
}
