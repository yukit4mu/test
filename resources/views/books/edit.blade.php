<x-app-layout>
    <x-slot name="header">書籍の編集</x-slot>
    <form action="{{ route('books.update', $book) }}" method="POST">
        @method('PUT')
        @include('books._form')
        <button type="submit">更新する</button>
    </form>
</x-app-layout>