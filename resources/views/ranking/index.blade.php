<x-app-layout>
    <x-slot name="header">評価ランキング TOP 10</x-slot>
    <ol>
        @foreach($rankedBooks as $book)
            <li>
                <a href="{{ route('books.show', $book) }}">{{ $book->title }}</a>
                {{ number_format($book->average_rating, 2) }} ★
            </li>
        @endforeach
    </ol>
</x-app-layout>