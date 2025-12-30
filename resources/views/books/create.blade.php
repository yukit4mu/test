<x-app-layout>
    <x-slot name="header">書籍の登録</x-slot>
    <form action="{{ route('books.store') }}" method="POST">
        @include('books._form')
        <button type="submit">登録する</button>
    </form>
</x-app-layout>